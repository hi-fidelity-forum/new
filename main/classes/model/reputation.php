<?php defined('SYSPATH') or die('No direct script access.');

class Model_Reputation extends Model
{
	
	private $user_id = null;
	
	function __construct($user_id = false)
	{
		
		if ($user_id = (int) $user_id)
		{	
			$this->user_id = $user_id;
			return $this;			
		}
		else 
		{
			return false;
		}
	}
	
	function getAll()
	{
		$uid = $this->user_id;
		
		$paging = new Paging();
		
		$q = 'SELECT r.*, u.username, u.reputation AS adduser_reputation FROM mybb_reputation AS r
			LEFT JOIN mybb_users u ON (r.adduid = u.uid)
			WHERE r.uid='.$uid.' AND r.disabled = 0
			ORDER BY dateline DESC
		';
			
		if ($paging->setQuery($q))
		{
			$paging->execute();
			return $paging;
		}
		return false;
	}
	
	function getByAuthor($adduid)
	{
		$adduid = (int) $adduid;
		
		$uid = $this->user_id;
		
		$q = 'SELECT * FROM mybb_reputation WHERE uid = '.$uid.' AND adduid = '.$adduid.' LIMIT 1';
		
		if ($req = DB::query($q))
		{
			if ($res = $req->fetch())
			{
				return $res;
			}
		}
		
		return false;
	}
	
	function countNegative()
	{
		$uid = $this->user_id;
		
		$q = 'SELECT COUNT(*) as count FROM mybb_reputation WHERE uid = '.$uid.' AND reputation < 0';
		
		if ($req = DB::query($q))
		{
			if ($res = $req->fetch())
			{
				return $res['count'];
			}
		}
		
		return false;
		
	}
	
	function getDisabled()
	{
		$q='SELECT r.*, tu.username as tousername, u.username, u.reputation AS adduser_reputation FROM mybb_reputation AS r
			LEFT JOIN mybb_users u ON (r.adduid = u.uid)
			LEFT JOIN mybb_users tu ON (r.uid = tu.uid)
			WHERE disabled = 1
			ORDER BY dateline ASC';
                		
        if ($paging = new Paging($q))
		{
			$paging->execute();
			return $paging;
		}
		return false;
	}
	
	function enable($rid = false)
	{
		$rid = (int) $rid;
		if ($rep = $this->getByRid($rid))
		{
			if ($rep['disabled'] == 1)
			{
				
				$uid = $rep['uid'];
				
				$data['disabled'] = 0;
				
				if (DB::beginTransaction())
				{
					if (DB::update('mybb_reputation', $data, 'rid = '.$rid))
					{
						$req = DB::query('SELECT SUM(reputation) as sum FROM mybb_reputation WHERE uid='.$uid)->fetch();
						$sum = $req['sum'];
						if (DB::update('mybb_users', array('reputation'=>$sum), 'uid='.$uid))
						{
							DB::commitTransaction();
							return $rid;
						}
					}
				}
			}
		}
	}
	
	function put($author_id, $reputation, $comments, $disabled = true)
	{
		$uid = $this->user_id;
		
		$disabled = (bool) $disabled;
		
		$data = array();
		$data['uid'] = $uid;
		$data['adduid'] = $author_id;
		$data['reputation'] = $reputation;
		$data['dateline'] = TIME_NOW;
		$data['comments'] = $comments;
		$data['disabled'] = $disabled;
		
		if ($rep = $this->getByAuthor($author_id))
		{
			$rid = $rep['rid'];
			
			if (DB::beginTransaction())
			{
				if (DB::update('mybb_reputation', $data, 'rid = '.$rid))
				{
					/*
					$req = DB::query('SELECT SUM(reputation) as sum FROM mybb_reputation WHERE uid='.$uid)->fetch();
					$sum = $req['sum'];
					if (DB::update('mybb_users', array('reputation'=>$sum), 'uid='.$uid))
					{
						DB::commitTransaction();
						return $rid;
					}
					*/
					DB::commitTransaction();
					return $rid;
				}
			}
		}
		else
		{
			if (DB::beginTransaction())
			{
				if ($res = DB::insert('mybb_reputation', $data))
				{
					/*
					$req = DB::query('SELECT SUM(reputation) as sum FROM mybb_reputation WHERE uid='.$uid)->fetch();
					$sum = $req['sum'];
					if (DB::update('mybb_users', array('reputation'=>$sum), 'uid='.$uid))
					{
						DB::commitTransaction();
						return $res;
					}
					*/
					DB::commitTransaction();
					return $res;
				}
			}
		}
		return false;
	}
	
	function getByRid($rid = false)
	{
		$rid = (int) $rid;
		if ($res = DB::query('SELECT * FROM mybb_reputation WHERE rid = '.$rid)->fetch())
		{
			return $res;
		}
		return false;
	}
	
	function remove($rid)
	{
		if (($rid = (int) $rid) && ($this->session->isAuth() && $this->session->user()->isAdmin()))
		{
			$rep = $this->getByRid($rid);
			
			$uid = $rep['uid'];
			
			if (DB::beginTransaction())
			{
				if (DB::query('DELETE FROM mybb_reputation WHERE rid = '.$rid.' LIMIT 1'))
				{
					$req = DB::query('SELECT SUM(reputation) as sum FROM mybb_reputation WHERE uid='.$uid)->fetch();
					$sum = $req['sum'];
					if (DB::update('mybb_users', array('reputation'=>$sum), 'uid='.$uid))
					{
						DB::commitTransaction();
						return true;
					}
				}
			}
		}
	}
    	
}
