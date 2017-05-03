<?php defined('SYSPATH') or die('No direct script access.');

class Model_Shop_Clients extends Model {

	public function __construct()
	{    
        parent::__construct();
    
        $this->config = loadConfig('shop_config');
		
		/*
        
        $this->table_prefix = $this->config['table_prefix'];
		
		$this->table_section = $this->table_prefix.'section';
		$this->table_categories = $this->table_prefix.'categories';
		$this->table_ads = $this->table_prefix.'ads';
		$this->table_ad_items = $this->table_prefix.'ad_items';
		$this->table_forms = $this->table_prefix.'forms';
		*/
		
		return $this;
        
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
	
	public function getClient($uid = false)
	{
		$uid = (int) $uid;
		if ($uid)
		{
			$q = 'SELECT u.username, u.uid, u.usergroup AS gid,
						 cl.client_order_id
				  FROM (
						SELECT * FROM mybb_users
						WHERE uid = '.$uid.'
				  ) as u
				  LEFT JOIN shop_clients AS cl ON (u.uid = cl.uid)
				  ';
			
			
			if ($req = DB::query($q))
			{
				$client = false;
				if ($res = $req->fetch())
				{
					$client = $res;
					$orders = DB::query('SELECT * FROM shop_client_orders AS o WHERE o.uid = '.$uid.' ORDER BY o.id DESC')->fetchAll();
					if (!empty($orders)){
						$client['orders'] = $orders;
					} else {
						$client['orders'] = false;
					}
				}
				return $client;
			}
		}
		return false;
	}
	
	public function get_client($uid = false)
	{
		return $this->getClient($uid);
	}
		
	public function get_clients($group_id = false)
	{
		if ($group_id)
		{
			
			$group_id = (int) $group_id;
			
			$shop_groups = $this->get_shop_groups();
			
			if (isset($shop_groups[$group_id]) && $shop_groups[$group_id]['is_clients_group'] == 1)
			{
				$q = '
					SELECT u.username, u.uid, u.usergroup AS gid,
							o.end AS order_end, o.amount, o.count_ad,
							cl.client_order_id
					FROM (
						SELECT * FROM mybb_users
						WHERE usergroup = '.$group_id.'
					) as u
					LEFT JOIN shop_clients AS cl ON (u.uid = cl.uid)
					LEFT JOIN shop_client_orders AS o ON (cl.client_order_id = o.id OR u.uid=o.uid)
				';
			
			
			if ($req = DB::query($q))
			{
				$clients = false;
				while ($res = $req->fetch())
				{
					$clients[$res['uid']] = $res;
				}
				return $clients;
			}
				
			}
		}
		return false;
	}
	
	public function add_client_order($data = false)
	{
		if ($data && is_array($data) && isset($data['uid']))
		{
			$uid = $order['uid'] = (int) $data['uid'];
			$order['start'] = $data['start'];
			$order['end'] = $data['end'];
			$order['payment_date'] = $data['payment_date'];
			$order['gid'] = (int) $data['gid'];
			$order['amount'] = (int) $data['amount'];
			$order['count_ad'] = (int) $data['count_ad'];
			
			if (DB::beginTransaction())
			{
				
				$res = DB::insert('shop_client_orders', $order);
				$order_id = $res[0];
				
				$nq = 'INSERT INTO shop_clients
						(`uid`, `client_order_id`)
						VALUES ('.$uid.', '.$order_id.')
						ON DUPLICATE KEY UPDATE `client_order_id` = '.$order_id.'
					';
					
				DB::query($nq);
				
				if (DB::commitTransaction())
				{
					return true;
				}
			}
		}
		return false;
	}
	
}