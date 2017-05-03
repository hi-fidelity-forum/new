<?php defined('SYSPATH') or die('No direct script access.');

class Session 
{

	private static $_initial = false;
	
	private $_user;
	private $_auth;
	private $_admin = false;
	private $_sid;
	
	private $cookie_domain = '.hi-fidelity-forum.com';
	
	private static $_count_guests;
	private static $_online_users;

	function __construct()
	{			
		if (self::$_initial === false)
		{
			$this->init();			
			self::$_initial = $this;
			return $this;
		} 
		else
		{
			return self::$_initial;
		}
	}
	
    private function init()
	{
		//if ($login = Cookie::get('mybbuser'))
		if (isset($_COOKIE['mybbuser']) && ($login = $_COOKIE['mybbuser']))
		{
			//echo '<!-- '.$login.' -->'; exit;
			if ($this->_user = $this->loadUser($login))
			{
				
				$this->_auth = true;
				
				$this->_updateUsers($this->_user);
				
				if ($tz = $this->_user->get('timezone'))
				{
					$ts = ($tz<0?'-':'+').sprintf('%02d',abs(intval($tz))).':'.sprintf('%02d', 60*abs(fmod($tz, 1)));
					DB::query('SET time_zone = "'.$ts.'"');		
				}
			}
		}
		
		if ($this->_auth == false)
		{
			if ($spider = $this->loadSpider())
			{
				$this->_user = $spider;
			}
			else 
			{
				$this->_user = $this->loadGuest();
				$this->_updateGuests();
			}
		}
		
		
		return false;
	}
	
	function user()
	{
		return $this->_user;
	}
	
	private function _updateUsers(User $user)
	{
		if ($uid = $user->get('uid'))
		{
			$sid = md5($uid);
			
			$route = Request::route();
			
			$new_user_list = array();
			
			if ($users_list = MCache::get('users_list'))
			{				
				foreach ($users_list as $key=>$value)
				{
					$last_time = (int) $value['time'];
					if ($last_time > TIME_NOW-900){
						$new_user_list[$key] = $value;
					}
				}
			}
			
			$users_list = $new_user_list;
			$users_list[$sid] = array(
				'time'=>TIME_NOW,
				'route'=>$route,
				'uid'=> $user->get('uid'),
				'username'=> $user->get('username'),
				'usergroup'=>$user->get('usergroup'),
			);
			
			self::$_online_users = $users_list;
			
			SetCookie('sid', $sid, time()+15*24*3600, '/', $this->cookie_domain);
			MCache::set('users_list', $users_list, 1200);
			
		}
		return false;
	}
	
	private static function _updateGuests()
	{
		$sid = false;
		$new_guest_list = null;
		
		if (!isset($_COOKIE['sid']))
		{
			$sid = md5(uniqid(microtime(true)));
			//Cookie::set('sid',$sid, 3600);
			setcookie('sid',$sid, time()+3600);
		}
		else 
		{
			$sid = $_COOKIE['sid'];
		}
		
		if ($guest_list = MCache::get('guest_list'))
		{	
			foreach ($guest_list as $key=>$value)
			{
				$value = (int) $value;
				if ($value > TIME_NOW-900){
					$new_guest_list[$key] = $value;
				}
			}
		}
		
		$guest_list = $new_guest_list;
		$guest_list[$sid] = TIME_NOW;
		
		self::$_count_guests = count($guest_list);
		
		MCache::set('guest_list', $guest_list, 1200);
		
		return $sid;
		
	}
	
	function getCountGuests()
	{
		
		return count(MCache::get('guest_list'));
		//return self::$_count_guests;
	}
	
	function getOnlineUsers()
	{
		if ($us = self::$_online_users)
		{
			return $us;
		}
		else
		{
			return MCache::get('users_list');
		}
	}
	
	function isAuth()
	{
		return $this->_auth;
	}
	
	function loadUser($login)
	{
		$login = (string) $login;
		
		if ($dt = explode('_',$login))
		{
			$user_id = (int) $dt[0];
			$user_hash = (string) $dt[1];
			
			mark_debug_time('start load user');
			
			if ($user_id && $user_hash)
			{
				$user = new User($user_id);
				
				if ($user->get('loginkey') == $user_hash)
				{	
					$time = TIME_NOW;
					
					if ($time - $user->get('lastactive') > 900)
					{
						$last_visit = $user->get('lastactive');
						
						//DB::query('UPDATE LOW_PRIORITY mybb_users SET lastvisit = '.$last_visit.', lastactive='.TIME_NOW.' WHERE `uid`='.$user_id.' LIMIT 1');
						DB::asyncQuery('UPDATE LOW_PRIORITY mybb_users SET lastvisit = '.$last_visit.', lastactive='.TIME_NOW.' WHERE `uid`='.$user_id.' LIMIT 1');

					}
					else
					{
						$timespent = TIME_NOW - $user->get('lastactive');
						$time_online = (int) $user->get('timeonline') + ((int) $timespent);
						
						//DB::query('UPDATE LOW_PRIORITY mybb_users SET lastactive='.TIME_NOW.', timeonline='.$time_online.' WHERE `uid`='.$user_id.' LIMIT 1');
						DB::asyncQuery('UPDATE LOW_PRIORITY mybb_users SET lastactive='.TIME_NOW.', timeonline='.$time_online.' WHERE `uid`='.$user_id.' LIMIT 1');

					}
					
					$str = $user->get('uid')."_".$user->get('loginkey');
					//Cookie::set('mybbuser',$str, 15*24*3600); //15day cookie
					SetCookie('mybbuser',$str, time()+(15*24*3600), '/', $this->cookie_domain); //15day cookie
					
					mark_debug_time('end load user');
					
					return $user;

				} else {
					SetCookie('mybbuser', '', time()-1);
					unset($_COOKIE['mybbuser']);
					return false;
				}			
			}
		}
		return false;
	}
	
	static $spiders = 
		array (
			'googlebot' => 'Google',
			'lycos' => 'Lycos',
			'teoma' => 'Ask.com',
			'whatuseek' => 'What You Seek',
			'archive_crawler' => 'Internet Archive',
			'ia_archiver' => 'Alexa Internet',
			'msnbot' => 'Bing',
			'slurp' => 'Yahoo!',
			'twiceler' => 'Cuil',
			'baiduspider' => 'Baidu',
		);
	
	function loadSpider()
	{
		$useragent = Request::$user_agent;
		foreach(self::$spiders as $prefix=>$spider)
		{
			if(mb_strpos(mb_strtolower($useragent), $prefix) !== false)
			{
				$res['uid'] = 0;
				$res['username'] = 'spider';
				$res['usergroup'] = 0;
				$user['usergroup'] = 0;
				
				return new User($user);
			}
		}
		return false;		
	}
	
	
	
	function loadGuest()
	{
		$user['uid'] = '0';
		$user['username'] = 'guest';
		$user['usergroup'] = 1;
		$user['username'] = 'Guest';

		return new User($user);
	}
    
    function login($login, $password)
    {   
        $login = (string) $login;
        $password = (string) $password;
        
        if ($login && $password)
		{
        
            $q = 'SELECT * FROM '.User::$table_users.' WHERE `username`="'.$login.'" LIMIT 1';
            $user = DB::query($q)->fetch();
            
            if ($this->check_password($user, $password))
			{
				
				$str = $user['uid']."_".$user['loginkey'];
				
				//Cookie::set('mybbuser',$str, 15*24*3600); //15day
				SetCookie('mybbuser',$str, time()+15*24*3600, '/', $this->cookie_domain);
				//echo $_COOKIE['mybbuser']; exit;
				$this->init();
				return $this;
            }
			
        }
        
        return false;
        
    }
	
	public function logout()
    {
		/*
        if (Cookie::get('mybbuser')) {
                Cookie::delete('mybbuser');
        }
        if (Cookie::get("sid")) {
                Cookie::delete("sid");
        }
		*/
		
		if (isset($_COOKIE['mybbuser'])) {
            SetCookie('mybbuser', '', time() - 3600, '/', $this->cookie_domain);
			unset($_COOKIE['mybbuser']);
            //SetCookie('mybbuser', '', time() - 3600, '/', $this->cookie_domain);
        }
        if (isset($_COOKIE["sid"])) {
			SetCookie('sid', '', time() - 3600, '/', $this->cookie_domain);
			unset($_COOKIE['sid']);
			//SetCookie('sid', '', time() - 3600, '/', $this->cookie_domain);
			//echo 'logout'; exit;
        }
		
        $this->_user = FALSE;
        $this->_auth = FALSE;
		$this->init();
		return $this;
    }
    
    protected function salt_password($password, $salt)
    {
        return md5(md5($salt).$password);
    }
    
    protected function check_password($user, $password)
    {
        if (!is_string($password))
            return FALSE;
        else {
            
            if($this->salt_password(md5($password), $user['salt']) == $user['password'])
            {
                return TRUE;
            }
            else
            {
                return false;
            }
        }
    }
	
	function randomStr($length="8")
	{
		$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$randstring = '';
		$str_len = strlen($characters)-1;
		for ($i = 0; $i < $length; $i++) {
			$randstring .= $characters[rand(0, $str_len)];
		}
		return $randstring;
	}
	
	public function postKey()
    {
		static $key = false;
        if($this->isAuth())
        {
            return md5($this->_user->get('loginkey').$this->_user->get('salt').$this->_user->get('regdate'));
        }
        else
        {			
			if (!$key) $key = md5($this->randomStr(32));
            return $key;
        }
    }
	
	static function initial()
	{
		
		if (!self::$_initial)
		{
			return new Session();
		}
		
		return self::$_initial;
	}
	
}