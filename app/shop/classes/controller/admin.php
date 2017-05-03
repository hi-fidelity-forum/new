<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Admin extends Admin 
{

	public function __construct(Request $request)
    {	
		parent::__construct($request);
		$this->shop = new Model_Shop();
		$this->category = new Model_Shop_Category();
		$this->breadcrumbs->add('Объявления', 'admin/'.$this->request->controller());
		if ($this->request->param('id') || (isset($_POST['cid']) && !empty($_POST['cid'])))
		{	
			$cid = (int) (isset($_POST['cid'])?$_POST['cid']:$this->request->param('id'));
			$this->category->setCid($cid);
		}
	}

	public function action_index()
	{   
        $this->content = View::factory('admin/shop_index');
    }
	
	public function action_category()
	{   
		$categoryes = $this->category->get_top_struct(true);
		
		$this->content = View::factory('admin/shop_category')
							->set('catecoryes', $categoryes);
    }
	
	public function action_settings()
	{   
        $this->content = View::factory('admin/shop_settings');
    }
	
	public function after()
	{
		if (!$this->request->isAjax() && !isset($_GET['ajax']))
		{
			$this->content = View::factory('admin/shop_top')->set('body',$this->content);
		}
		parent::after();
	}
	
	public function action_create_category()
	{
	
		$this->breadcrumbs->add('Создание категории', $this->request->controller());
		
		if ($_POST){
			//$this->debug($_POST);
			
			if (isset($_POST['action'])){
				$action = (string) $_POST['action'];
				unset($_POST['action']);
				
				$categoryes = $this->category->getAll(true);
				$user = new User();
				$user_groups = $user->allUserGroups();
				
				if ($action == 'create_category')
				{
					$cat_title = (string) $_POST['title'];
					$this->content = View::factory('admin/create_category')
							->set('catecoryes', $categoryes)
							->set('user_groups', $user_groups)
							->set('category_title', $cat_title);
				}
				elseif ($action == 'put_category'){
					$res = $this->category->create_category($_POST);
					//$this->debug($res);
					if ($res)
					{
						$this->redirect(DS.Request::$base_url.'/shop/category');
					} else {
						$this->content = 'Произошла ошибка, раздел не создан!';
					}
				}
			}
		}
	}
	
	public function action_edit_filters()
	{
		
		if ($cid = (int) $this->request->param('id'))
		{

			$filters = $this->category->getFilters();
			
			if (isset($_POST['action']))
			{
				
				if ($_POST['action'] == 'edit')
				{
					$new_opt = $_POST;
					unset($new_opt['action']);
					if ($filters->changeFilter($new_opt))
					{
						$this->request->redirect('/admin/shop/edit_filters/'.$cid);
					}
				}
				elseif ($_POST['action'] == 'create')
				{
					$new_opt = $_POST;
					unset($new_opt['action']);
					if ($filters->addFilter($new_opt))
					{
						$this->request->redirect('/admin/shop/edit_filters/'.$cid);
					}
				}
				elseif ($_POST['action'] == 'remove')
				{
					$fid = (int) $_POST['fid'];
					if ($filters->removeFilter($fid))
					{
						return true;
					}
				}
				elseif ($_POST['action'] == 'remove_item')
				{
					$item_id = (int) $_POST['item_id'];
					if ($filters->removeItem($item_id))
					{
						return true;
					}
				}
				elseif ($_POST['action'] == 'decOrder')
				{
					$fid = (int) $_POST['fid'];
					if ($filters->decOrder($fid))
					{
						return true;
					}
				}
				elseif ($_POST['action'] == 'incOrder')
				{
					$fid = (int) $_POST['fid'];
					if ($filters->incOrder($fid))
					{
						return true;
					}
				}
				elseif ($_POST['action'] == 'put_item')
				{
					
					$new_opt = $_POST;
					unset($new_opt['action']);
					if (isset($_POST['item_id']) && $item_id =  (int) $_POST['item_id'] && isset($_POST['item_title']) && isset($_POST['item_value']))
					{ 
						$item_id =  (int) $_POST['item_id'];
						$item_title = $_POST['item_title'];
						$item_value = $_POST['item_value'];
						if ($filters->changeItem($item_id, $item_title, $item_value))
						{
							$this->request->redirect('/admin/shop/edit_filters/'.$cid);
						}
					}
					elseif (isset($_POST['fid']))
					{
						if ($filters->addFilterItem($new_opt))
						{
							$this->request->redirect('/admin/shop/edit_filters/'.$cid);
						}
					}
				}
				
			}
			$this->content = View::factory('admin/edit_filters')
								->set('filters', $filters);
		}
		else
		{
			$this->content = 'Not category ID';
		}
	}
	
	public function action_edit_category()
	{
		
		if ($_POST && isset($_POST['cid']) && isset($_POST['action']))
		{
			//var_export($_POST);
			$cid = (int) $_POST['cid'];
			if ($_POST['action'] == 'save')
			{
				if ($this->category->get('id') == $cid)
				{
					$cat['title'] = isset($_POST['title'])?$_POST['title']:$this->category->get('title');
					$cat['title_template'] = $_POST['title_template'];
					$cat['spec_template'] = $_POST['spec_template'];
					$cat['is_items'] = isset($_POST['is_items'])?1:0;
					$cat['disable'] = isset($_POST['disable'])?1:0;
					if ($this->category = $this->category->change($cat))
					{
						$cange = true;
					}
				}
			}
		}
		
		$tpl = View::factory('admin/edit_category')->set('category',$this->category);

		$this->content = $tpl;

	}
	
	public function action_get_form()
	{
		$form = $this->category->getCreateForm();
		
		$tpl = View::factory('admin/get_form')->set('form',$form);
		
		if ($this->is_ajax)
		{
			$res = array('content' => $tpl->render());
			$this->content = json_encode($res);	
			return false;
		} else {
			$this->content = $tpl;
			return true;
		}
	}
	
	public function action_autoup()
	{   
		if ($config_file = findFile('config', 'autoup_config'))
		{
		
			$users = array();
			$uptime = 300;
			if ($config = @simplexml_load_file($config_file))
			{
				$uptime = isset($config->uptime)?((int) $config->uptime):((int) $config->addChild('uptime',300));
				$users_uid = isset($config->users)?$config->users:false;
				
				$last_uid = isset($config->last_uid)?$config->last_uid:false;
				
				$users_uid = (array) $users_uid;
				if ($users_uid)
				{
					foreach ($users_uid as $uid){
						$users[] = new User($uid);
					}
					
				} else {
					
				}
			} else {
				$config = new SimpleXMLElement("<config><uptime>300</uptime><users></users></config>");
				$config->asXML($config_file);
			}
			
			$this->content = View::factory('admin/shop_autoup')
					->set('uptime',$uptime)
					->set('users',$users);
		}
    }
	
	public function action_add_autoup_user()
	{
		if ($_POST)
		{
			if (isset($_POST['user_uid']))
			{				
				$uid = (int) $_POST['user_uid'];
				
				if ($uid === false)
				{
					
				}
				else 
				{
					$config_file = find_file('config', 'autoup_config');
									
					$config = simplexml_load_file($config_file);
					$users = (array) $config->users;
					if (!isset($users['u'.$uid]))
					{
						$config->users->addChild('u'.$uid, $uid);
						$config->asXML($config_file);
					}
				}
			}
		}
		$this->redirect('/'.Request::$base_url.'/shop/autoup');
	}
	
	public function action_change_uptime()
	{
		if ($_POST)
		{
			if (isset($_POST['uptime']))
			{
				
				$uptime = (int) $_POST['uptime'];
				
				if ($uptime === false)
				{
					
				}
				else 
				{
					$config_file = findFile('config', 'autoup_config');
									
					$config = simplexml_load_file($config_file);
					$config->uptime = $uptime;
					$config->asXML($config_file);
				}
			}
		}
		$this->redirect('/'.Request::$base_url.'/shop/autoup');
	}
	
	function action_remove_category()
	{
		if ($cid = (int) $this->request->param('id'))
		{
			if ($this->category->get('id') == $cid)
			{
				$category = $this->category;
			}
			else 
			{
				$category = new Model_Shop_Category();
				$category->setCid($cid);
			}
			
			if ($sub = $category->getSub())
			{
				foreach ($sub as $item)
				{
					$sub_cat = new Model_Shop_Category($item['id']);
					$sub_cat->remove();
				}
			}
			
			$category->remove();
			
			$this->request->redirect('/admin/shop/category/');
		}
	}

} // End