<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Cli extends Cli 
{

	public function __construct(Request $request)
    {	
		parent::__construct($request);
		$this->auto_render = false;
	}

	public function action_index()
    {
		var_export(date_default_timezone_get());
        echo 'This is CLI index page';
		
    }
	
	public function action_date()
    {
		DB::init();
		
		if (DB::beginTransaction())
		{
			
			$all = DB::query('SELECT id, last_ad_date FROM shop_categories');
			
			foreach ($all as $item)
			{
				$tmp2 = getdate(strtotime($item['last_ad_date']));
				$new_date['last_ad_date_new'] = $tmp2[0]>0?$tmp2[0]:TIME_NOW;
				DB::update('shop_categories',$new_date, 'id='.$item['id']);
			}
			
			if (DB::commitTransaction())
			{
				echo 'Finish';
				return true;
			}
		}
		
		var_export(date_default_timezone_get());
        echo 'This is CLI index page';
		
    }
	
	public function action_get_regions()
    {
    
        $countryId = (int) $this->request->param('id'); 
		
		$lang = 0; // russian
		$headerOptions = array(
			'http' => array(
				'method' => "GET",
				'header' => "Accept-language: en\r\n" . // Вероятно этот параметр ни на что не влияет
				"Cookie: remixlang=$lang\r\n"
			)
		);
		
		$methodUrl = 'http://api.vk.com/method/database.getRegions?v=5.5&need_all=1&offset=0&count=1000&country_id=' . $countryId;
		$streamContext = stream_context_create($headerOptions);
		$json = file_get_contents($methodUrl, false, $streamContext);
		$arr = json_decode($json, true);
		print_r(json_encode($arr['response']['items'])); 
		//print_r($arr['response']); 
		
    }
	
	public function action_get_city()
    {
    
        $countryId = $this->request->param('id'); // Russia
		$lang = 0; // russian
		$headerOptions = array(
			'http' => array(
				'method' => "GET",
				'header' => "Accept-language: en\r\n" . // Вероятно этот параметр ни на что не влияет
				"Cookie: remixlang=$lang\r\n"
			)
		);
		
		
		$methodUrl = 'http://api.vk.com/method/database.getCities?v=5.5&country_id=' . $countryId . '®ion_id=' . $regionId . '&offset=0&need_all=1&count=1000';
		
		$streamContext = stream_context_create($headerOptions);
		$json = file_get_contents($methodUrl, false, $streamContext);
		$arr = json_decode($json, true);
		print_r(json_encode($arr['response']['items'])); 
		
    }
	
	public function action_autoup()
    {
    
		DB::init();
		
        if ($config_file = findFile('config', 'autoup_config'))
		{

			if ($config = @simplexml_load_file($config_file))
			{
				$uptime = isset($config->uptime)?((int) $config->uptime):((int) $config->addChild('uptime',0));
				$users_uid = isset($config->users)?$config->users:false;
				
				$last_uid = isset($config->last_uid)?$config->last_uid:false;
				
				$users_uid = (array) $users_uid;
				
				if ($users_uid)
				{
					foreach ($users_uid as $uid){
						//$users[] = $this->user->get_user($uid);
						$uids[] = $uid;
					}
					
					if (count($uids)>1)
					{
						if (!isset($config->last_uid))
						{
							$config->addChild('last_uid', $last_uid);
							$last_uid = current($uids);
						} else {
							$last = (int) $config->last_uid;
							if ($last == 0 || end($uids) == $last) 
							{
								$last_uid = reset($uids);
							} else {
								foreach ($uids as $key=>$val)
								{
									if ($val == $last) $last_uid = $uids[$key+1];
								}
							}
							$config->last_uid = $last_uid;
						}
					} 
										
					$config->asXML($config_file);
					echo $last_uid;
					
					$user = new User($uid);
					
					$req = DB::query('SELECT `fid`, `name`, `pid`, `parentlist` FROM mybb_forums WHERE fid<>35');
					$fr = $req->fetchAll();
					$forums = array();
					foreach ($fr as $val)
					{
						if ($val['pid'] == 0)
						{
							$forums[$val['fid']] = $val;
						}
						else {
							$forums[$val['pid']]['subforums'][$val['fid']] = $val;
						}
					}
					
					$forum2 = array();
					$ind = array();
					foreach ($forums['7']['subforums'] as $key=>$frm)
					{
						$ind[$key] = $key;
						foreach ($forums as $k=>$v)
						{
							if ($k == $key)
							{
								$forum2[$k] = $v['subforums'];
							}
							
						}
					}
					
					foreach ($forum2 as $i=>$s)
					{
						foreach ($s as $inx=>$item)
						{
							$ind[$inx] = $inx;
						}
					}
					
					//var_export($forum2);
					//var_export($ind);
					
					$items = implode(',',$ind);
					//echo $items;
					
					$q = 'SELECT * FROM mybb_threads 
							WHERE uid='.$last_uid.'
							AND closed=0
							AND fid IN ('.$items.')
							ORDER BY `lastpost`
							LIMIT 1';
					if ($thread = DB::query($q)->fetch())
					{
						//print_r($thread);
						$p = 'SELECT * FROM mybb_posts WHERE tid='.$thread['tid'].' ORDER BY dateline DESC LIMIT 1';
						$last_post = DB::query($p)->fetch();
						$time = getdate(); $time=$time[0];
						print_r($thread);
						$up = 'UPDATE `mybb_posts` SET `dateline`='.$time.' WHERE `pid`='.$last_post['pid'];
						DB::query($up);
						$ut = 'UPDATE `mybb_threads` SET `lastpost`='.$time.' WHERE `tid`='.$thread['tid'];
						DB::query($ut);
						$uf = 'UPDATE `mybb_forums` 
									SET 
										`lastpost`='.$time.',
										`lastposter` = '.DB::quote($thread['lastposter']).',
										`lastposteruid` = '.$thread['lastposteruid'].',
										`lastpostsubject` = '.DB::quote($thread['subject']).',
										`lastposttid` = '.$thread['tid'].'
									WHERE `fid`='.$thread['fid'];		
						DB::query($uf);
					}
				} 
			}
		}
        
    }
	
	function action_brands()
	{
		//action for jquery.autocomplete
		
		DB::init();
		if (isset($_GET['q']) && ($search = trim((string) $_GET['q'])))
		{
			if ($req = DB::query("SELECT name FROM brands WHERE `name` LIKE '".$search."%' ORDER BY name ASC"))
			{
				$res = false;
				foreach ($req as $item)
				{
					$res[]['name'] = $item['name'];
				}
				echo json_encode($res);
			}
		}
	}
	
	function action_exportpm()
	{
		//echo 'action export private message';
		
		DB::init();
		
		$q = 'SELECT COUNT(*) FROM mybb_privatemessages WHERE folder=1 GROUP BY toid';
		
		if ($req = DB::query($q))
		{
			$res = $req->fetchAll();
			
			var_export($res);
			
		}
		
	}

}