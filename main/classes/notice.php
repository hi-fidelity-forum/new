<?php defined('SYSPATH') or die('No direct script access.');

class Notice extends DL
{
	
	private $publish_enable = false;

    public function __construct()
	{    
    
        $this->table_name = 'mybb_privatemessages';
        
        return $this;	
        
    }
	
	public function publish_enable($status = false)
	{
		$this->publish_enable = (boolean) $status;
	}
    
    public function get_all()
	{
		
		$notice = '';
    
        if ($this->session->isAuth())
		{
			
			$user = $this->session->user();
			
			if ($user->get('unreadpms') > 0)
			{        
				//Get PMS unreaded
				$q='SELECT t.pmid, t.fromid, t.subject, u.username
					FROM (
						SELECT *
						FROM '.$this->table_name.' WHERE uid='.$this->session->user()->get('uid').' AND status=0 AND folder=1
					) as t
					LEFT JOIN mybb_users u ON (t.fromid=u.uid) ORDER BY t.dateline DESC';
					
				$res = DB::query($q)->fetchAll();
				
				if (count($res)>0){
					$notice .= View::factory('notice/pms')
							->set('info',$res);
				}
				
			}
            
            if ($this->session->user()->isModer())
			{
                
                $q='SELECT COUNT(*) as count
                    FROM mybb_reportedposts
                    WHERE reportstatus = 0
                    ORDER BY dateline DESC';
                
                $res = DB::query($q)->fetch();
                
                if ($res['count']>0){
                    $notice .= View::factory('notice/reported')
                        ->set('report_count',$res['count']);
                }
				
            }
			
			if ($this->session->user()->isAdmin())
			{
				if ($mod_events = $this->getModEvents())
				{
					$notice .= View::factory('notice/mod_event')->set('events',$mod_events);
				}
			}
			
        }
		
		if ($this->publish_enable === true)
		{
			$notice .= $this->notice->get_top_publish();
		}
		
		if ($notice) return $notice;
		
        return false;
    }
	
	function getModEvents()
	{
		
		$res = false;
		
		$q='SELECT COUNT(*) as count
                    FROM mybb_reputation
                    WHERE disabled = 1
                    ORDER BY dateline DESC';
                
        if ($row = DB::query($q)->fetch())
		{
			if ($count = $row['count'])
			{
				$res['reputation'] = $count;
			}
		}
		
		$q='SELECT COUNT(*) as count
                    FROM shop_ads
                    WHERE status = 2
                    ORDER BY last_ad_date DESC';
                
        if ($row = DB::query($q)->fetch())
		{
			if ($count = $row['count'])
			{
				$res['ads'] = $count;
			}
		}
                
		//return array('reputation'=>2, 'ads'=>'4');
		return $res;
	}
    
    function get_top_publish($lim = 3)
	{
    
		$block = false;
        $limit = (int) $lim;
    
        $qr = "SELECT *
               FROM publish
               WHERE status = 'publish'
               ORDER BY `create` DESC LIMIT ".$limit;
                    
        if ($get_result = MCache::get($qr)) {
			$block = $get_result;
        }
		else 
		{
			$query = DB::query($qr);
			
			if ($items = $query->fetchAll())
			{		
				$block = View::factory('notice/toppublish')
					->set('items',$items);
			
				MCache::set($qr, $block, 120, FALSE);
			}
		}
		
		return $block;
    
    }
    
}