<?php defined('SYSPATH') or die('No direct access allowed.');

// Описание класса
class Controller_Users extends Controller
{

	private $user = false;
	
	function action_index()
	{
		$this->content = 'Users page';
	}

	function action_jsearch()
	{
		if ($search = trim((string) $_GET['q']))
		{
			if ($req = DB::query("SELECT * FROM mybb_users WHERE `username` LIKE '".$search."%' ORDER BY username ASC"))
			{
				
				$users = false;
				
				foreach ($req as $user)
				{
					$avatar = $user['avatar']?$user['avatar']:'/images/avatars/hf.jpg';
					$users[] = array(
						'name' => $user['username'],
						'uid'=>$user['uid'],
						'avatar' => $avatar,
					);
				}
				
				$this->content = json_encode($users);
				return false;
				
			}
		}
		
		$this->content = '';
		
	}
}