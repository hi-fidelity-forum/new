<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Admin extends Admin
{

    public function __construct(Request $request)
    {	
		parent::__construct($request);
		$this->shop = new Model_Shop();
	}

	public function action_index()
	{   
		$shop_groups = $this->shop->get_shop_groups();
		$groups = false;
		foreach ($shop_groups as $gr)
		{
			if ($gr['is_clients_group'] == 1)
			{
				$groups[$gr['gid']] = $gr;
			}
		}
		$this->content = View::factory('admin/clients')->set('groups',$groups);
    }
	
	public function action_view()
	{   
        $gid = (int) $this->request->param('id');
        
		$shop_groups = $this->shop->get_shop_groups();
		$groups = false;
		foreach ($shop_groups as $gr)
		{
			if ($gr['is_clients_group'] == 1)
			{
				$groups[$gr['gid']] = $gr;
			}
		}
		
        if ($gid)
        {
			$clients = new Model_Shop_Clients();
			$users = $clients->get_clients($gid);
			
            
			$this->content = View::factory('admin/clients')
                ->set('id',$gid)
                ->set('users',$users)
				->set('groups', $groups);
        }
        else
        {
            $this->content = View::factory('admin/groups');
        }
            
    }
	
	public function action_edit()
	{
		$uid = (int) $this->request->param('id');
		if ($uid)
		{
		
			$clients = new Model_Shop_Clients();
			
			$config = loadConfig('shop_config');
			
			$change = false;
			if ($_POST && isset($_POST['add_order']))
			{
				if (isset($_POST['uid']) && $_POST['uid']==$uid)
				{
					$data = $_POST;
					unset($data['add_order']);
					if (isset($data['end']))
					{
						$data['start'] = date('Y-m-d', strtotime($data['start']));
						$data['end'] = date('Y-m-d', strtotime($data['end']));
						$data['payment_date'] = date('Y-m-d', strtotime($data['payment_date']));
					}
					
					if ($res = $clients->add_client_order($data))
					{
						$change = true;
						$this->redirect('/'.Request::$base_url.'/clients/edit/'.$uid);
					}
				}
			}
		
			
			if ($user = $clients->get_client($uid))
			{
				$groups = $this->shop->get_shop_groups();
				//$this->debug($user);
				$this->content = View::factory('admin/client_edit')
									->set('user',$user)
									->set('change', $change)
									->set('groups',$groups)
									->set('config', $config);
			}
			
		}
		return false;
	}

	public function action_send_pm()
	{
		$gid = (int) $this->request->param('id');
        
		$shop_groups = $this->shop->get_shop_groups();
		$groups = false;
		foreach ($shop_groups as $gr)
		{
			if ($gr['is_clients_group'] == 1)
			{
				$groups[$gr['gid']] = $gr;
			}
		}
		
        if ($gid)
        {
			
			$req = DB::query('SELECT * FROM mybb_privatemessages WHERE pmid=5061058 LIMIT 1');
			$pm = $req->fetch();
			unset($pm['pmid']);
			$pm['fromid'] = '14782';
			$pm['status'] = '0';
		
			$clients = new Model_Shop_Clients();
			$users = $clients->get_clients($gid);
			
			
			foreach ($users as $user)
			{
				$uid = $pm['toid'] = $pm['uid'] = $user['uid'];
				$pm['folder'] = '1';
				$pm['recipients'] = 'a:1:{s:2:"to";a:1:{i:0;s:5:"'.$uid.'";}}';
				$keys = ''; $values='';
				foreach ($pm as $inx=>$val)
				{
					$keys .= $inx.',';
					$values .= DB::quote($val).',';
				}
				$keys = rtrim($keys, ',');
				$values = rtrim($values, ',');
				$q = 'INSERT INTO `mybb_privatemessages` ('.$keys.') VALUES ('.$values.')';
				print_r($q); 
				//DB::query($q);
				echo '<br />';
			}
			
		}
		
	}
	
}
