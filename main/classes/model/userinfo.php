<?php defined('SYSPATH') or die('No direct script access.');

class Model_UserInfo extends User
{
	
	function __construct($user_id = false)
	{
		
		if ($user_id)
		{
			
			$this->session = Session::initial();
			
			parent::__construct($user_id);	
			return $this;			
		}
		else 
		{
			return false;
		}
	}
	
	function getUserThreads()
	{
		
		$forum = new Model_Forum();
		
		$uid = $this->get('uid');
		
		$threads = $forum->getThreadsByUID($uid);
		
		return $threads;
	
	}
	
	function getUserPosts()
	{
		
		$forum = new Model_Forum();
		
		$uid = $this->get('uid');
		
		$posts = $forum->getPostsByUID($uid);
		
		return $posts;
	
	}
	
	function getReputations()
	{
		$uid = $this->get('uid');
		
		return $this->reputation = new Model_Reputation($uid);
	}
	
	function changeFields($fields)
	{
		if (gettype($fields) == 'array')
		{
			$def = $this->getFields();
			
			$new = false;
			
			foreach ($def as $key=>$val)
			{
				if (isset($fields[$key]) && !empty($fields[$key]))
				{
					$new[$key] = (string) $fields[$key];
				}
			}
			
			if ($new && ($uid = $this->get('uid')))
			{
				if ($f = DB::update('mybb_userfields', $new, 'ufid = '.$uid))
				{
					return true;
				}
				
			}
		}
		return false;
	}
	
	function changeInfo($data)
	{

		if (gettype($data) == 'array')
		{
			$new = false;
			foreach ($data as $key=>$value)
			{
				if ($this->get($key) !== null)
				{
					$new[$key] = $value;
				}
				else 
				{
					echo 'not '.$key.' ';
				}
			}
			
			if ($new && ($uid = $this->get('uid')))
			{
				if ($f = DB::update('mybb_users', $new, 'uid = '.$uid))
				{
					return true;
				}
			}
		}
	}    	
}
