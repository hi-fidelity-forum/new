<?php defined('SYSPATH') or die('No direct script access.');

Class User 
{

	private $_user;
	private $_admin = null;
	private $_moder = null;
	private $_fields = null;
	private static $_all_user_groups = null;
	
	public static $table_users = 'mybb_users';
	public static $table_groups = 'mybb_usergroups';
	
	public static $admin_groups_id = array('4');
	public static $moder_groups_id = array('3', '6');
	
	function __construct($user_id = null)
	{
	
		if ($user_id !== null)
		{
			if (gettype($user_id) == 'array')
			{
				$this->_user = $user_id;
				return $this;
			}
			else
			{
				$user_id = (int) $user_id;
				$req = DB::query('SELECT * FROM '.self::$table_users.' AS user WHERE user.uid='.$user_id.' LIMIT 1');
				
				if ($user_data = $req->fetch())
				{
					$this->_user = $user_data;
					return $this;

				} else {
					return false;
				}
			}
		}
		else 
		{
			return false;
		}
	}
	
	function get($param = false)
	{
		$param = (string) $param;
		if (!empty($param))
		{
			return isset($this->_user[$param])?$this->_user[$param]:null;
		}
		return false;
	}
	
	function getAvatarSrc()
	{
		if ($avatar = $this->get('avatar'))
		{
			
			if (mb_strpos($avatar, 'http') === false)
			{
				$avatar = str_replace('./', '/', $avatar);
				
				$m = explode('?', $avatar);
				$p = $m[0];
				if (!file_exists(DOCROOT.$p))
				{
					$avatar = '/images/avatars/hf.jpg';
				}
				
				$avatar = ltrim($avatar, '/');
				$avatar = '/'.$avatar;
			}
		}
		else 
		{
			$avatar = '/images/avatars/hf.jpg';
		}
		
		return $avatar;
	}
	
	function getStatus()
	{
		$gid = $this->getDisplayGroupID();
		
		if ($group = $this->getUserGroupByID($gid))
		{
			
			$title = '';
			if (isset($group['usertitle'])&&$group['usertitle'])
			{
				$title = $group['usertitle'];
			}
			else
			{
				$group_activity = $this->getGroupByActivity();
				$title = $group_activity['usertitle'];
			}
			$status['title'] = $title;
			
			$star_image = $group['starimage'];
			$star_count = $group['stars']?$group['stars']:1;
			$stars = '';
			for ($i = 1; $i<=$star_count; $i++)
			{
				$stars .= '<img src="/'.$star_image.'" />';
			}
			$status['stars'] = $stars;
			$status['image'] = isset($group['image'])?$group['image']:false;
			return $status;
		}
		return false;
	}
	
	function getDisplayGroupID()
	{
		$gid = 0;
				
		if ($displaygroup = (int) $this->get('displaygroup'))
		{
			$gid = $displaygroup;
		}
		else 
		{
			$gid = $this->getGroupID();
		}
		
		return $gid;
	}
	
	function getDisplayGroup()
	{
		return $this->getUserGroupByID($this->getDisplayGroupID());
	}
	
	public function getGroupID()
	{
		return $this->get('usergroup')?$this->get('usergroup'):1;
	}
	
	function allGroupsID()
    {    
        if ($this->get('usergroup') || $this->get('additionalgroups')){
            $groups = $this->get('usergroup').($this->get('additionalgroups')?','.$this->get('additionalgroups'):'');
            return explode(',',$groups);
        }
        return array(1);
		
    }
	
	function isAdmin()
	{
		if ($this->_admin === null)
		{
			$groups = $this->allGroupsID();
			$this->_admin = false;
			
			foreach (self::$admin_groups_id as $val)
			{
				if (in_array($val, $groups)) {
					$this->_admin = true;
					break;
				}
			}
			return $this->_admin;
						
		}
		return $this->_admin;
	}
	
	function isModer()
	{
		if ($this->_moder === null)
		{
			$groups = $this->allGroupsID();
			
			$this->_moder = false;
			
			foreach (array_merge(self::$admin_groups_id, self::$moder_groups_id) as $val)
			{
				if (in_array($val, $groups)) {
					$this->_moder = true;
					break;
				}
			}
			return $this->_moder;
						
		}
		return $this->_moder;
	}
	
	function getUserGroupByID($gid = false)
	{
		if ($gid !== false)
		{
			$all_groups = $this->allUserGroups();
			$group = isset($all_groups[$gid])?$all_groups[$gid]:$all_groups[1];
			return $group;
		}
	}
	
	static function allUserGroups()
	{
		if (self::$_all_user_groups === null)
		{
			
			if ($mgroups = MCache::get('usergroups'))
			{
				self::$_all_user_groups = $mgroups;
			}
			else
			{
				if ($req = DB::query('SELECT * FROM '.self::$table_groups))
				{				
					foreach ($req as $row)
					{
						self::$_all_user_groups[$row['gid']] = $row;
					}
					//self::$_all_user_groups = $rows;
					MCache::set('usergroups', self::$_all_user_groups, 3600);

				} else {
					throw die('error select groups table from db');
					return false;
				}
			}
			return self::$_all_user_groups;
		}
		else
		{
			return self::$_all_user_groups;
		}
	}
	
	function getGroupByActivity()
	{
		$settings = loadConfig('groups');
		$activity = $settings['groups_activity'];
		
		$postnum = $this->get('postnum');
		$time = round((TIME_NOW - $this->get('regdate'))/86400);
		
		$gid = false;
		foreach ($activity as $id=>$gr)
		{
			if (($postnum >= $gr['posts']) || ($time >= $gr['time']))
			{
				$gid = $id;
			}
		}
		return $this->getUserGroupByID($gid);
	}
	
	function stylizedUserName()
	{
		$group = $this->getDisplayGroup();
		$username = $this->get('username');
		$namestyle = $group['namestyle'];
		return str_replace('{username}', $username, $namestyle);		
	}
	
	function getFields()
	{
		if ($uid = $this->get('uid'))
		{
			$q = 'SELECT * FROM mybb_userfields WHERE ufid='.$uid;
			
			if ($req = DB::query($q))
			{
				if ($res = $req->fetch())
				{
					$this->_fields = $res;
					return $this->_fields;
				}
			}
			return $this->_fields;
		}
		return false;
	}
	
	public function getUsersByGroupID($group_id = false, $additional = false)
	{
		if ($group_id !== false && $group_id = (int) $group_id)
		{
			//todo: additional search groups
			$q = 'SELECT * FROM mybb_users WHERE `usergroup`='.$group_id;
			if ($req = DB::query($q))
			{
				$res = false;
				while ($item = $req->fetch())
				{
					$res[$item['uid']] = $item;
				}
				return $res;
			}
		}
		return false;
	}
	
}