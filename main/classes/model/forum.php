<?php defined('SYSPATH') or die('No direct script access.');

class Model_Forum extends Model {

    public static $_all_forums;
    public static $_forums_pid;
    
    public static $_forums_permissions = false;
    
    public static $displaygroupfields = array("title", "description", "namestyle", "usertitle", "stars", "starimage", "image");
    
    private $_forum;
    private $_sub_forums = -1;
	
	private $_usergroup_permissions;
    
	public static $_modlist = -1;
    
    public function __construct($fid = false)
	{    
        parent::__construct();
    
        $this->config = loadConfig('forum_config');
		
		$this->user = $this->session->user();
        
        $this->table_name = 'forums';
        $this->table_prefix = $this->config['table_prefix'];
        
		if (is_int($fid) && ($fid>0)){
            $forum = $this->get_forums($fid);
        } elseif (is_object($fid) || is_array($fid)) {
            $forum = $fid;
        } else {
            $forum = false;
        }
        
        if ($forum) {
            $this->_forum = $forum;            
        } else {
            $this->_forum['fid'] = 0;
        }
        
        return $this;
        
    }
    
    public function get($param){
    
        $param = (string) $param;
        
        if (isset($this->_forum[$param])){
            return $this->_forum[$param];
        }
        
        return false;
    
    }
    
    public function isAccess($forum = false)
	{
        
        if ($this->session->isAuth() && $this->session->user()->isAdmin())
		{
            return true;
        }

        if ($forum)
		{
            $fid = $forum['fid'];
			
			$forum_perms = $this->get_permissions($fid);
            
            if ($forum_perms){
                if ($forum_perms['canview']){
                    return true;
                }
            }            
        } elseif (isset($this->_forum['fid']) && $this->_forum['fid'])
		{
			$fid = $this->_forum['fid'];
			$forum_perms = $this->get_permissions($fid);
            
            if ($forum_perms){
                if ($forum_perms['canview']){
                    return true;
                }
            }            
		}
        return false;
    }
    
    
    public function get_forums($fid = false, $pid = -1){
    
        if (!self::$_all_forums)
		{
            if ($this->session->isAuth()){
                $uid = $this->session->user()->get('uid');
                $q = '
                    SELECT f.*, r.dateline as lastread
                    FROM '.$this->table_prefix.$this->table_name.' as f
                    LEFT JOIN (
                            SELECT * FROM '.$this->table_prefix.'forumsread
                            WHERE uid='.$uid.'
                            ORDER BY dateline 
                        ) r ON (r.fid=f.fid AND r.uid='.$uid.')
                    WHERE f.active!=0 
                    ORDER BY pid, disporder ASC';
            } else {
                $q = 'SELECT * FROM '.$this->table_prefix.$this->table_name.' WHERE active!=0 AND open!=0 ORDER BY disporder ASC';
            }
            
            if ($query = DB::query($q)) {
            
                while ($fr = $query->fetch()) {
                    self::$_all_forums[$fr['fid']] = $fr;
                    self::$_forums_pid[$fr['pid']][$fr['fid']] = $fr;                    
                }
            } 
        }   
        
        if ($fid) {
            if (isset(self::$_all_forums[$fid])) {
                return self::$_all_forums[$fid];
            } else return false;
        }
        if ($pid != -1) {
            if (isset(self::$_forums_pid[$pid])) {
                return self::$_forums_pid[$pid];
            } else return false;
        }
        return self::$_all_forums;
    
    }
	
	public function get_parent_list($fid = 0)
	{
		$fid = $fid?$fid:$this->_forum['fid'];
		if (!self::$_all_forums)
		{
			$this->get_forums();
		}
		$prs = false;
		$fr = self::$_all_forums[$fid];
		while ($fr['pid'] != 0)
		{
			$pid = $fr['pid'];
			$fr = self::$_all_forums[$pid];
			$prs[] = $fr;
		}
		return $prs;
	}
	
	public function groupPermissions()
	{
        if (!$this->_usergroup_permissions)
		{
		
			$user = $this->session->user();
			
			$permfields = $this->config['permfields'];
        
            $groups = $user->AllGroupsID();
        
            $res = false;
        
            foreach ($groups as $gid)
			{
                $group = $user->getUserGroupByID($gid);
                foreach ($group as $field=>$value){
                    if (in_array($field, $permfields) && $field) {
                        if (isset($res[$field])){
                            $res[$field] = $value || $res[$field];
                        } else {
                            $res[$field] = $value;
                        }
                    }
                }
            }
            $this->_usergroup_permissions = $res;
        } else {
            $res = $this->_usergroup_permissions;
        }
        return $res;
    }
    
    public function get_permissions($fid = -1)
	{
		
        $fid = (int) $fid;
        
        if (!self::$_forums_permissions)
		{
            
            $this->load_all_perms();
			
            $permfields = $this->config['permfields'];
            
            $user_groups = $this->session->user()->AllGroupsID();
            $user_perms = $this->groupPermissions();
            
            $res = false;
            
            $all_forums = $this->get_forums();
            
            foreach ($all_forums as $inx=>$frm)
			{
            
                if (isset(self::$_forums_permissions[$frm['fid']]))
                {                    
                    $fperms = self::$_forums_permissions[$frm['fid']];
                    
                    foreach ($fperms as $pr)
                    {
                        if (in_array($pr['gid'],$user_groups))
                        {                            
                            foreach ($pr as $field=>$value)
                            {                                
                                if (in_array($field,$permfields))
                                {
                                    if (isset($res[$frm['fid']][$field])) 
                                    {
                                        $res[$frm['fid']][$field] = $value || ($value>$user_perms[$field]) || $res[$frm['fid']][$field];
                                    }
                                    else {
                                        $res[$frm['fid']][$field] = $value || ($value>$user_perms[$field]);
                                    }
                                }
                            }
                            
                        } elseif (!isset($res[$frm['fid']])) {
                            $res[$frm['fid']] = $pr;
                        }
                    }
                } else {
                    $res[$frm['fid']] = $user_perms;
                }
            }
            self::$_forums_permissions = $res;
        }
        
        if ($fid==-1) {
            return self::$_forums_permissions;
        } else {
            return self::$_forums_permissions[$fid];
        }
    
        return false;
    
    }
	
	public function getUnViwedForums()
	{
		$perms = $this->get_permissions();
		$res = false;
		foreach ($perms as $fid=>$perm)
		{
			if ($perm['canview'] == false)
			{
				$res[] = $fid;
			}
		}
		return $res;
	}
	
	public function load_all_perms()
	{
		if (!self::$_forums_permissions) 
		{	
			//Если группы не загружены
			$q = 'SELECT * FROM '.$this->table_prefix.'forumpermissions ORDER BY pid ASC';

			if ($perm_cache = MCache::get('forumpermissions'))
			{
				
				self::$_forums_permissions = $perm_cache;
					
			} else {
				if ($req = DB::query($q))
				{
					$res = false;
					while ($row = $req->fetch()){
						$res[$row['fid']][$row['gid']] = $row;
					}
					MCache::set('forumpermissions', $res, 3600); 
					self::$_forums_permissions = $res;
				}
			}			
			return self::$_forums_permissions;			
		}
		else
		{
			return self::$_forums_permissions;
		}
	}
	
	public function get_collapsed()
    {
        // set up collapsable items (to automatically show them us expanded)
		if(isset($_COOKIE['collapsed']) && $colcookie = $_COOKIE['collapsed'])
        {			
            $col = explode("|", $colcookie);
            if(!is_array($col))
            {
                $col[0] = $colcookie; // only one item
            }
            unset($collapsed);
            foreach($col as $key => $val)
            {
                $ex = $val."_e";
                $co = $val."_c";
                $collapsed[$co] = "display: show;";
                $collapsed[$ex] = "display: none;";
                $collapsedimg[$val] = "_collapsed";
            }
            return $collapsed;
        }
        return false;
    }
    
    public function subforums($is_array = FALSE, $pid = -1)
	{
    
        $pid = $pid != -1?$pid:$this->_forum['fid'];
        
        $sub = false;
        
        if (!$is_array){
        
            if ($this->_sub_forums != -1){
                return $this->_sub_forums;
            } elseif ($forums = $this->get_forums(false,$pid)) {
                
                foreach ($forums as $forum)
                {
                    if ($this->isAccess($forum)){
                        $sub[$forum['fid']] = new self($forum);
                    }
                }
                
                $this->_sub_forums = $sub;
                return $sub;
            }                
        } else {
            return $this->get_forums(false, $pid);
        }
        return false;
    }
    
    public function forum_info($fid = 0)
	{
        $fid = $fid?$fid:$this->_forum['fid'];
        
        $forum = $this->get_forums($fid);
        
        $lastpost_data = array(
			"lastpost" => $forum['lastpost'],
			"lastpostsubject" => $forum['lastpostsubject'],
			"lastposter" => $forum['lastposter'],
			"lastposttid" => $forum['lastposttid'],
			"lastposteruid" => $forum['lastposteruid']
		);
        
        
        if ($all = $this->get_forums())
		{
            foreach ($all as $sub)
			{
                if ($sub['pid'] == $fid && $this->isAccess($sub)) {
                    
                    //$subforums[$sub['fid']] = $sub;
                    $sub_info = $this->forum_info($sub['fid']);

                    $forum['threads'] += $sub_info['threads'];
                    $forum['posts'] += $sub_info['posts'];
                    $forum['unapprovedthreads'] += $sub_info['unapprovedthreads'];
                    $forum['unapprovedposts'] += $sub_info['unapprovedposts'];
                
                    if ($sub_info['lastpost'] > $lastpost_data['lastpost']){
                        $lastpost_data = $sub_info;
                    }                                        
                }
            }
        }
        
        $lastpost_data['threads'] = $forum['threads'];
        $lastpost_data['posts'] = $forum['posts'];
        $lastpost_data['unapprovedthreads'] = $forum['unapprovedthreads'];
        $lastpost_data['unapprovedposts'] = $forum['unapprovedposts'];
        
        $threadcut = TIME_NOW - 60*60*24*$this->config['threadreadcut'];
        
        $lastpost_data['is_read'] = false;
        
        if ($this->session->isAuth()){
            $lastpost_data['is_read'] = ($lastpost_data['lastpost']<$forum['lastread']) || ($threadcut>$lastpost_data['lastpost']);            
        } else {
            if (isset($_COOKIE['mybb']['forumread'])) {
                $forumsread = unserialize($_COOKIE['mybb']['forumread']);
                if (empty($frd) && isset($_COOKIE['mybb']['readallforums'])){
                    $forumsread[$fid] = $_COOKIE['mybb']['lastvisit'];
                }
                if(isset($forumsread[$forum['fid']])){
                    $forum['lastread'] = $forumsread[$forum['fid']];
                    $lastpost_data['is_read'] = $lastpost_data['lastpost']<$forum['lastread'];
                }
            }
        }
        
        return $lastpost_data;
        
    }
    
    public function getThreadsPage()
	{    
        //return false;
    
        $fid = $this->_forum['fid'];
        $res = false;
		
		if ($this->session->isAuth())
		{
			$uid = $this->user->get('uid');
			
			$q = 'SELECT t.*, r.tid AS is_reads, r.dateline AS is_read_dateline
			FROM '.$this->table_prefix.'threads t
			LEFT JOIN '.$this->table_prefix.'threadsread r ON (r.uid = '.$uid.' AND r.tid = t.tid)
			WHERE t.fid = '.$fid.' AND t.visible=1
			ORDER BY t.sticky DESC, t.lastpost DESC';
		} else {
			$q = 'SELECT * FROM '.$this->table_prefix.'threads WHERE `fid`='.$fid.' ORDER BY sticky DESC, lastpost DESC';
		}
        
		if ($r = new Paging($q))
		{
			return $r;
		}
        
        return false;

    }
	
	public function getThreadsByUID($uid = null)
	{
        //return false;
		if ($uid !== null)
		{
			$uid = (int) $uid;
			
			if ($this->user->isAdmin())
			{
				$unviewed = false;
			}
			else {
				$unviewed = $this->getUnViwedForums();
			}
			
			if ($this->session->isAuth())
			{	
				$q = 'SELECT t.*, r.tid AS is_reads, r.dateline AS is_read_dateline, f.fid, f.name AS forum_name
				FROM '.$this->table_prefix.'threads t
				LEFT JOIN '.$this->table_prefix.$this->table_name.' f ON (f.fid = t.fid)
				LEFT JOIN '.$this->table_prefix.'threadsread r ON (r.uid = '.$this->user->get('uid').' AND r.tid = t.tid)
				WHERE t.uid = '.$uid.' AND t.visible=1'.($unviewed?(' AND f.fid NOT IN ('.implode(',', $unviewed).') '):'').'
				ORDER BY t.sticky DESC, t.lastpost DESC';
			} else {
				$q = 'SELECT t.*, f.fid, f.name AS forum_name
				FROM '.$this->table_prefix.'threads t
				LEFT JOIN '.$this->table_prefix.$this->table_name.' f ON (f.fid = t.fid)
				WHERE t.uid = '.$uid.' AND t.visible=1'.($unviewed?(' AND f.fid NOT IN ('.implode(',', $unviewed).') '):'').'
				ORDER BY t.sticky DESC, t.lastpost DESC';
			}
			
			if ($r = new Paging($q))
			{
				return $r;
			}
		}
        return false;

    }
	
	
	public function getPostsByUID($uid = null)
	{
        //return false;
		if ($uid !== null)
		{
			$uid = (int) $uid;
			
			if ($this->user->isAdmin())
			{
				$unviewed = false;
			}
			else {
				$unviewed = $this->getUnViwedForums();
			}
			
			if ($this->session->isAuth())
			{	
				$q = 'SELECT p.*, f.fid, f.name AS forum_name
				FROM '.$this->table_prefix.'posts p
				LEFT JOIN '.$this->table_prefix.'threads t ON (t.tid = p.tid)
				LEFT JOIN '.$this->table_prefix.$this->table_name.' f ON (f.fid = t.fid)
				WHERE p.uid = '.$uid.' AND t.visible=1'.($unviewed?(' AND t.fid NOT IN ('.implode(',', $unviewed).') '):'').'
				ORDER BY p.dateline DESC';
			} else {
				$q = 'SELECT p.*, f.fid, f.name AS forum_name
				FROM '.$this->table_prefix.'posts p
				LEFT JOIN '.$this->table_prefix.'threads t ON (t.tid = p.tid)
				LEFT JOIN '.$this->table_prefix.$this->table_name.' f ON (f.fid = t.fid)
				WHERE p.uid = '.$uid.' AND t.visible=1'.($unviewed?(' AND f.fid NOT IN ('.implode(',', $unviewed).') '):'').'
				ORDER BY p.dateline DESC';
			}
			
			if ($r = new Paging($q))
			{
				return $r;
			}
		}
        return false;

    }
	
	public function modlist($fid = 0)
	{
		$fid = $fid?$fid:$this->_forum['fid'];
		
		if (self::$_modlist == -1)
		{
			$q = 'SELECT m.id, m.fid, m.isgroup, u.username, u.uid AS uid
					FROM mybb_moderators AS m
					LEFT JOIN mybb_users u ON (m.id = u.uid)
				';
			if ($req = DB::query($q))
			{
				$list = array();
				while ($item = $req->fetch())
				{
					if ($item['isgroup'])
					{
						$users = $this->user->getUsersByGroupID($item['id']);
						$list[$item['fid']] = $users;
					} else {
						$list[$item['fid']][$item['uid']]= $item;
					}
				}
				self::$_modlist = $list;
			}
		}
		
		if (!self::$_all_forums)
		{
			$this->get_forums();
		}
		$prs = false;
		if (isset(self::$_modlist[$fid]))
		{
			return self::$_modlist[$fid];
		} else {
			$fr = self::$_all_forums[$fid];
			while ($fr['pid'] != 0 && !isset(self::$_modlist[$fr['fid']]))
			{
				$pid = $fr['pid'];
				$fr = self::$_all_forums[$pid];
			}
			if (isset(self::$_modlist[$fr['fid']]))
			{
				return self::$_modlist[$fr['fid']];
			}
		}
		
		return false;
	}
	
	public function get_announcements($list = false)
	{
		$res = false;
		
		$list = (string) $list;
		
		if ($list)
		{
			$time = TIME_NOW;
			$q = 'SELECT a.*, u.username
				FROM mybb_announcements a
				LEFT JOIN mybb_users u ON (u.uid=a.uid)
				WHERE a.startdate<='.$time.' AND (a.enddate>='.$time.' OR a.enddate=0) AND (a.fid IN ('.$list.') OR a.fid="-1")
				ORDER BY a.startdate DESC';
				
			if ($req = DB::query($q))
			{
				if ($res = $req->fetchAll())
				{
					return $res;
				}
			}
		}
		
		return $res;
		
	}
	
	public function forum_stats()
    {
        $q = 'SELECT s.numusers, s.numposts, s.numthreads, u.username, u.usergroup, u.displaygroup, u.uid, m.cache AS mostonline FROM (
                SELECT * FROM '.$this->config['table_prefix'].'stats
                ORDER BY dateline DESC
                LIMIT 1
            ) as s
            INNER JOIN (
                SELECT * FROM '.$this->config['table_prefix'].'users
                ORDER BY uid DESC
                LIMIT 1
            ) as u
            INNER JOIN (
                SELECT * FROM '.$this->config['table_prefix'].'datacache
                WHERE title="mostonline"
                LIMIT 1
            ) as m';
              
        if ($query = DB::query($q))
        {
            if ($stats = $query->fetch())
            {
                if (isset($stats['mostonline']))
                {
                    $stats['mostonline'] = unserialize($stats['mostonline']);
                }
                return $stats;
            }
        }
        
        return false;
    }
	
}
