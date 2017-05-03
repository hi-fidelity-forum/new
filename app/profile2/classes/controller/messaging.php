<?php defined('SYSPATH') or die('No direct access allowed.');

// Описание класса
class Controller_Messaging extends Profile
{
	
	function __construct ($r)
	{
		parent::__construct($r);
		if (!$this->session->isAuth()) {
			$this->request->redirect('/profile/'.$this->request->param('id'));
			return false;
		}
		$this->messaging = new Model_PM();
		$this->breadcrumbs->add('Почта', 'profile/'.$this->user->get('uid').'/messaging/');
	}
	
	function action_index()
    {
		
		$messages = $this->messaging->getInBox();
		
		$this->content = View::factory('messaging/index')->set('user', $this->user)->set('messages', $messages);
    }
	
	function action_dialog()
	{
		if ($dialog_id = (int) $this->request->param('inx'))
		{
			if ($_POST && isset($_POST['reply']))
			{
				$message = (string) $_POST['message'];
				$this->messaging->addReply($dialog_id, $message);
				$this->request->redirect('/'.$this->request->uri());
			}
			
			if ($dialog_header = $this->messaging->getDialogHeader($dialog_id))
			{
				$dialog = $this->messaging->getDialog($dialog_id)->result();
				
				$this->breadcrumbs->add($dialog_header['title'], '/dialog/'.$dialog_id);
				
				$this->content = View::factory('messaging/dialog')->set('dialog', $dialog)->set('dialog_header', $dialog_header)->set('user',$this->user);				
			}
			else 
			{
				$this->content = 'Переписка не найдена';
			}
		}
	}
	
	function action_create()
	{
		
		$this->page_title_prefix = $this->page_title_prefix.' - Создание письма';
		$this->breadcrumbs->add('Создание письма',$this->request->controller_uri().'/create');
		
		$uid = (int) $this->user->get('uid');
		
		if ($_POST && isset($_POST['create']) && isset($_POST['toid']))
		{
			$toid = (int) $_POST['toid'];
			if ($toid)
			{
				$subject = (string) $_POST['subject'];
				$message = (string) $_POST['message'];
				
				/*
				$parser = new Parser();
				
				$parser_options = array(
					'allow_html' => 0,
					'allow_mycode' => 1,
					'allow_smilies' => 1,
					'allow_imgcode' => 0,
				);
				
				$post = $parser->parse_message($message, $parser_options);
				
				$post = 'from id:'.$uid.'<br />toid:'.$toid.'<br />Subject:'.$subject.'<br />'.$post;
				
				$this->content = $post;
				*/
				
				if ($pmid = $this->messaging->createDialog($uid, $toid, $subject, $message))
				{
					if ($this->request->isAjax())
					{
						$this->content = '{"success":"Письмо отправленно", "error":0}';
					}
					else 
					{
						$this->content = 'Письмо отправленно';
					}
				}
				else
				{
					if ($this->request->isAjax())
					{
						$this->content = '{"success":0, "error":"Произошла ошибка, письмо не отправленно"}';
					}
					else 
					{
						$this->content = 'Произошла ошибка, письмо не отправленно';
					}
				}
			}
			else {
				$this->content = 'Не указан получатель';
			}
			
		}
		else 
		{
			$editor = new Editor();
			$editor_box = $editor->getEditorBox();
			
			$this->content = View::factory('messaging/create')->set('editor_box', $editor_box);
			
		}
		
	}
	
	function action_notifications()
	{
		if ($id = (int) $this->request->param('inx'))
		{
			if ($notification = $this->messaging->getNotifications($this->user->get('uid'), $id))
			{
				$this->content = View::factory('messaging/notification_view')->set('notification', $notification)->set('user', $this->user);
			}
			else
			{
				$this->content = 'Уведомленние отсутствует';
			}
		}
		else 
		{
			$notifications = $this->messaging->getNotifications($this->user->get('uid'));
			$this->content = View::factory('messaging/notifications')->set('notifications', $notifications)->set('user', $this->user);
		}
	}
}