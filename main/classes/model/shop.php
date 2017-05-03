<?php defined('SYSPATH') or die('No direct script access.');

class Model_Shop extends Model 
{

	public function __construct()
	{    
        parent::__construct();
		
		$this->config = loadConfig('shop_config');
		
		return $this;
        
    }
	
	public function get_top_category()
	{
		$category = new Model_Shop_Category();
		return $category->get_top_struct();
	}
	
	public function get_category($cid = 0)
	{
		return new Model_Shop_Category($cid);
	}
	
	public function get_subcategory($cid = 0)
	{
		$category = new Model_Shop_Category($cid);
		return $category->getSub();
	}
	
		
	public function get_shop_groups()
	{
		$q = 'SELECT g.*, ug.title FROM `shop_groups` AS g
			LEFT JOIN mybb_usergroups AS ug ON (g.gid = ug.gid)
			ORDER BY g.gid
		';
		
		if ($req = DB::query($q))
		{
			$groups = false;
			while ($res = $req->fetch())
			{
				$groups[$res['gid']] = $res;
			}
			return $groups;
		}
		return false;
	}
	
	public function getUnApprovedAds()
	{
		
		$q='SELECT * FROM shop_ads
            WHERE status = 2
            ORDER BY last_ad_date DESC';
                
        if ($rows = DB::query($q)->fetchAll())
		{
			return $rows;
		}
		
		return false;
	}
	
	public function get_ad($id)
	{
		$ad_id = (int) $id;
		$ad = new Model_Shop_Ad($ad_id);
		if ($ad->info())
		{
			return $ad;
		}
		else 
		{
			return false;
		}
	}
	
	function getUserAds($uid = false, $status = 1)
	{
		
		$status = (int) $status;
		
		if ($uid = (int) $uid)
		{
			if (($this->session->isAuth() && $this->session->user()->get('uid') == $uid) || $this->session->user()->isAdmin())
			{
				$q ='SELECT ad_list.*, ad_list.price as new_price, cv.prefix as currency FROM shop_ads ad_list
									
					LEFT JOIN currency_values cv ON (ad_list.currency_code = cv.code_key AND cv.code_source = 4)
									
					WHERE ad_list.author_id='.$uid.' AND ad_list.status='.$status.'
					ORDER BY ad_list.last_ad_date DESC
				';
					
				if ($r = new Paging($q))
				{
					return $r;
				}
			}
			else 
			{
				$q ='SELECT ad_list.*, ad_list.price as new_price, cv.prefix as currency FROM shop_ads ad_list
									
					LEFT JOIN currency_values cv ON (ad_list.currency_code = cv.code_key  AND cv.code_source = 4)
					
					WHERE ad_list.author_id='.$uid.' AND ad_list.status=1
					ORDER BY ad_list.last_ad_date DESC
				';
					
				if ($r = new Paging($q))
				{
					return $r;
				}
			}
		}
	}
	
	public function getOutDated($id = false)
	{
		if ($id = (int) $id)
		{
			if ($req = DB::query('SELECT * FROM shop_removed_ads WHERE id='.$id.' LIMIT 1'))
			{
				if ($row = $req->fetch())
				{
					return $row;
				}
			}
		}
		else 
		{
			$q = 'SELECT * FROM shop_removed_ads';
			if ($r = new Paging($q))
			{
				$r->execute();
				return $r;
			}
		}
		return false;
	}
	
	public function findOutDatedByAdId($id)
	{
		if ($id = (int) $id)
		{
			if ($req = DB::query('SELECT * FROM shop_removed_ads WHERE ad_id='.$id.' LIMIT 1'))
			{
				if ($row = $req->fetch())
				{
					return $row;
				}
			}
		}
		return false;
	}
	
	public function getAdsCountByUid($uid = false)
	{
		$res = false;
		if ($uid = (int) $uid)
		{
			if ($req = DB::query('SELECT COUNT(*) as count, status FROM shop_ads WHERE author_id='.$uid.' GROUP BY status'))
			{
				$res['all'] = 0;
				foreach ($req->fetchAll() as $row)
				{
					$res['all'] += $row['count'];
					$res['status_'.$row['status']] = $row['count'];
				}
			}
			if ($req = DB::query('SELECT COUNT(*) as is_news FROM shop_ads WHERE author_id='.$uid.' AND is_new = 1 LIMIT 1'))
			{
				if ($row = $req->fetch())
				{
					$res['is_new'] = $row['is_news'];
				}
			}
			
			$client_data = false;
			$user = new User($uid);
			$gid = $user->getGroupID();
			if (isset($this->config['paid_groups'][$gid]))
			{
				$paid_opt = $this->config['paid_groups'][$gid];
				
				$clients = new Model_Shop_Clients();
				$def = $this->config['user_ads_counts'][$gid];
				if ($client = $clients->getClient($uid))
				{
					$client_data = $client;
				}
				if ($client_data['orders'] == false)
				{
					if (isset($this->config['user_ads_counts'][$gid]))
					{
						$res['limit_count'] = $def['count'];
					}
				}
				else 
				{
					$res['limit_count'] = $client['orders'][0]['count_ad'];
				}
				
				if ($paid_opt['can_new'])
				{
					if ($paid_opt['no_limit'])
					{
						$res['limit_new'] = 1024;
					}
					else 
					{
						$activity_group = $user->getGroupByActivity();
						$act_gid = $activity_group['gid'];
						if (isset($this->config['user_ads_counts'][$act_gid]))
						{
							$res['limit_new'] = $this->config['user_ads_counts'][$act_gid]['count'];
						}
					}
				}
				else 
				{
					$res['limit_new'] = 0;
				}
			}
			else 
			{
				if (isset($this->config['user_ads_counts'][$gid]))
				{
					$def = $this->config['user_ads_counts'][$gid];
					$res['limit_count'] = $def['count'];
				}
				else 
				{
					$res['limit_count'] = 0;
				}
				$res['limit_new'] = 0;
			}
		}
		return $res;
	}
	
}