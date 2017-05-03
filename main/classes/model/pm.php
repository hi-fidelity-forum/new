<?php defined('SYSPATH') or die('No direct script access.');

class Model_PM extends Model
{
	
	function __construct()
	{
		$this->user = false;
		if ($this->session->isAuth())
		{
			$this->user = $this->session->user();
			parent::__construct();
		} else 
		{
			return false;
		}
	}
    	
	function getInBox()
	{
		if ($this->user)
		{
			$uid = (int) $this->user->get('uid');
			$q = 'SELECT s.*, d.* FROM 
					(SELECT * FROM dialog_siders WHERE uid='.$uid.') s
					LEFT JOIN dialogs AS d ON (s.dialog_id = d.id AND d.lastsider != '.$uid.')
					ORDER BY `lastmessagetime` DESC';
					
			$q = 'SELECT s.*, d.* FROM 
					(SELECT * FROM dialog_siders WHERE uid='.$uid.') s
					LEFT JOIN dialogs AS d ON (s.dialog_id = d.id)
					WHERE d.lastsider != '.$uid.'
					ORDER BY `lastmessagetime` DESC';
			
			if ($r = new Paging($q))
			{
				$r->execute();
				$dlg = $r->result();
				if ($dlg->rowCount()>0)
				{
					//var_export($dlg->fetchAll());
					$list = false;
					$ids = false;
					
					foreach ($dlg as $dlg_item)
					{
						$ids[] = $dlg_item['id'];
						$list[$dlg_item['id']] = $dlg_item;
						$list[$dlg_item['id']]['author'] = new User($dlg_item['lastsider']);
					}
					/*
					$ids = implode(',', $ids);
					
					$q2 = 'SELECT u.*, s.dialog_id FROM 
						(SELECT * FROM dialog_siders WHERE dialog_id IN ('.$ids.')) s
						LEFT JOIN mybb_users AS u ON (s.uid = u.uid)';
						
					if ($qr = DB::query($q2))
					{
						if ($siders = $qr->fetchAll())
						{
							foreach ($siders as $sider)
							{
								$user = new User($sider['uid']);
								$list[$sider['dialog_id']]['siders'][$sider['uid']] = $user;
							}
						}
					}
					*/
					
					return $list;
				}
				else return false;
			}			
		}
		return false;
	}
	
	function getDialogHeader($id = false)
	{
		if ($id !== false)
		{
			$q = 'SELECT * FROM dialogs
				  WHERE dialogs.id = '.$id.'
				  LIMIT 1';
				  
			if ($req = DB::query($q))
			{
				return $req->fetch();
			}
		}
	}
	
	
	function getDialog($id = false)
	{
		if ($id !== false)
		{
			$uid = (int) $this->user->get('uid');
			
			$q = 'SELECT * FROM
				(SELECT * FROM dialog_messages WHERE dialog_id='.$id.') m
				LEFT JOIN dialogs AS d ON (d.id = '.$id.')
				LEFT JOIN mybb_users u ON (u.uid = m.fromid)
				ORDER BY m.datetime ASC';
			
			if ($r = new Paging($q))
			{
				$r->execute();
				if ($r->result()->rowCount()>0)
				{
					return $r;
				}
				else return false;
			}
			
		}
	}
	
	function createDialog($uid, $toid, $subject, $message)
	{
		
		$create_time = TIME_NOW;
		
		$uid = (int) $uid;
		$toid = (int) $toid;
		
		if (DB::beginTransaction())
		{
			
			if ($dialog_id = DB::insert('dialogs', array('author_id'=>$uid, 'title'=>$subject, 'lastsider'=>$uid, 'lastmessagetime'=>$create_time)))
			{
				$dialog_id = $dialog_id[0];
				DB::insert('dialog_siders', array('dialog_id'=>$dialog_id, 'uid'=> $uid, 'lastread'=>$create_time));
				DB::insert('dialog_siders', array('dialog_id'=>$dialog_id, 'uid'=> $toid, 'lastread'=>0));
				DB::insert('dialog_messages', array('dialog_id'=>$dialog_id, 'fromid'=>$uid, 'message'=>$message, 'datetime'=>$create_time));
				DB::commitTransaction();
				return $dialog_id;
			}
		}
		
		return false;
	}
	
	function addReply($dialog_id, $message)
	{
		$dialog_id = (int) $dialog_id;
		$uid = (int) $this->user->get('uid');
		$time = TIME_NOW;
		$message = (string) $message;
		
		if (DB::beginTransaction())
		{
			
			if ($message_id = DB::insert('dialog_messages', array('dialog_id'=>$dialog_id, 'fromid'=>$uid, 'message'=>$message, 'datetime'=>$time)))
			{
				DB::update('dialogs', array('lastsider'=>$uid, 'lastmessagetime'=>$time), 'id = '.$dialog_id);
				DB::query('UPDATE mybb_users u
							JOIN dialog_siders s ON (s.dialog_id = '.$dialog_id.' AND s.uid = u.uid AND s.uid <> '.$uid.')
							SET u.unreadpms = u.unreadpms+1
						');
				//unreadpms
				DB::commitTransaction();
				return $message_id;
			}
		}
		
	}
	
	function createNotification($uid = false, $message = false, $subject = false, $wait = false)
	{
		if ($uid && $message)
		{
			$subject = $subject?$subject:'Вам пришло новое уведомление';
			
			$user = new User($uid);
			
			$not = array();
			$not['uid'] = $user->get('uid');
			$not['subject'] = $subject;
			$not['message'] = $message;
			$not['dateline'] = TIME_NOW;
			$not['is_read'] = '0';
			DB::insert('notifications', $not);
			
			if ($email = $user->get('email'))
			{
				$mailer = new Mailer();		
				$mailer->setSubject($subject);
				
				$message = '<html><head><meta http-equiv="Content-Type" content="text/html; charset=utf-8" /></head><body>'.$message;
				//$message .= '<br /><br /><a href="http://hi-fidelity-forum.com/profile/'.$uid.'/messaging/notifications">Все уведомления</a></body></html>';
				$message .= '</body></html>';
				
				$mailer->Body = $message;
				$mailer->AddAddress($email);
				$mailer->isHTML(true);
				if(!$mailer->Send())
				{
					//echo 'Не могу отослать письмо!';
				}
				else
				{
					//echo 'Письмо отослано!';
				}
				$mailer->ClearAddresses();
				//$mailer->ClearAttachments();
			}
		}
		return false;
	}
	
	function getNotifications($uid = false, $nt_id = false)
	{
		$uid = (int) $uid;
		$nt_id = (int) $nt_id;
		if ($uid)
		{
			if ($nt_id)
			{
				$q = 'SELECT * FROM notifications WHERE uid='.$uid.' AND id='.$nt_id.' LIMIT 1';
				
				if ($req = DB::query($q))
				{
					if ($res = $req->fetch())
					{
						DB::update('notifications', array('is_read'=>'1'), 'id='.$nt_id);
						return $res;
					}
				}
			}
			else 
			{
				$q = 'SELECT * FROM notifications WHERE uid='.$uid.' ORDER BY dateline DESC';
			
				if ($r = new Paging($q))
				{
					$r->execute();
					return $r;
				}
			}
		}
		return false;
	}
	
}
