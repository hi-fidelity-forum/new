<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Shop extends Shop {

	public function __construct(Request $request)
    {	
		parent::__construct($request);
		$this->breadcrumbs->add('Hi-Fi Forum', '')->add('Объявления', 'shop/');
		$this->page_title_prefix = 'Объявления - Hi-Fi Forum';
		
	}
	
	public function action_index()
    {		
		$cat_list = $this->shop->get_top_category();
		//print_r($cat_list); exit;
		$tpl = View::factory('shop/index_block')
                    ->set('cat_list',$cat_list);	
		$this->content = $tpl;
    }
	
	public function action_view()
    {
		$id = (int) $this->request->param('id');
		if ($id === false)
		{
			$this->content = 'Не указан id объявления';
		} else {
			
			if ($ad = $this->shop->get_ad($id))
			{	
				
				$info = $ad_data = $ad->info();
				
				if ($info['status'] == 1)
				{
					$ad->incViews();
				}
				
				$category = $this->shop->get_category($ad_data['category_id']);
				if ($pid = $category->get('cid'))
				{
					$parent = $this->shop->get_category($pid);
					$this->breadcrumbs->add($parent->get('title'), 'shop/subcategory/'.$parent->get('id'));
				}
				
				$this->breadcrumbs->add($category->get('title'),'shop/category/'.$category->get('id'))->add($ad_data['title'], 'shop/view/'.$ad_data['id']);
				$this->page_title_prefix = $ad_data['title'].' | '.$category->get('title');
				$this->meta_description = $ad_data['title'].' - '.$ad_data['spec'];
				
				$tpl = View::factory('shop/view_ad')->set('ad',$ad);
				if ($this->session->isAuth() && $this->session->user()->isAdmin())
				{
					$tpl .= View::factory('shop/ad_mod_buttons')->set('ad',$ad);
				}
				elseif ($this->session->isAuth() && $this->session->user()->get('uid') == $info['author_id'])
				{
					$tpl .= View::factory('shop/ad_author_buttons')->set('ad',$ad);
				}
				
				$this->content = $tpl;
				
			}
			elseif ($item = $this->shop->findOutDatedByAdId($id))
			{
				$this->redirect(Request::$base_url.'shop/outdated/'.$item['id'], 301);
			}
			else 
			{
				$this->content = 'Объявление не найдено';
			}
		}
	}
	
	public function action_up()
	{
		$id = (int) $this->request->param('id');
		if ($id !== false && $ad = $this->shop->get_ad($id))
		{
			if ($ad->isEditable())
			{
				$info = $ad->info();
				if ($ad->isAdCanUp($info) || $this->session->user()->isAdmin())
				{
					if ($ad->upDate())
					{
						if (isset($_GET['page']))
						{
							$page = (int) $_GET['page'];
							//var_export($page); exit;
							$this->redirect(Request::$base_url.'profile/'.$info['author_id'].'/ads?page='.$page);
						}
						else 
						{
							$this->redirect(Request::$base_url.'profile/'.$info['author_id'].'/ads');
						}
					}
				}
			}
		}
	}	
	
	public function action_approve()
    {
		$id = (int) $this->request->param('id');
		if ($id !== false && $ad = $this->shop->get_ad($id))
		{
			if ($ad->isEditable())
			{
				$info = $ad->info();
				if ($ad->approve(false))
				{
					$message = View::factory('shop/mails/is_approve')->set('ad', $ad)->render();
					$subject = 'Ваше объявление активировано';
					$pm = new Model_PM();
					$pm->createNotification($info['author_id'], $message, $subject);
					
					$this->redirect(Request::$base_url.'admin/moder/ads');
				}
				else 
				{
					$this->content = 'Не удалось подтвердить объявление';
				}
			}
			
		} else {
			
			$this->content = 'Не указан id объявления';
			
		}
	}
	
	public function action_category()
    {
		
		$this->load_old_javascript = false;
		
		$cid = (int) $this->request->param('id');
		$filter_string = (string) $this->request->param('filters');
		
		$category = new Model_Shop_Category($cid, $filter_string);
		
		$filters = $category->getFilters();
		
		$parent = $category->getParent();
		
		$this->breadcrumbs->add($parent->get('title'), 'shop/subcategory/'.$parent->get('id'))
			->add($category->get('title'), 'shop/category/'.$category->get('id'));
		
		$this->page_title_prefix = $category->get('title').' - Объявления - Hi-Fi Forum';
		
		$ad_list = $category->getAdList();
		
		$this->currency = new Model_Currency();
		
		$this->currency->setCodes();
		
		if ($ad_list)
		{
			
			$ad_list_items = View::factory('shop/ad_list_items')
					->set('ad_list',$ad_list);
		} else {
			$ad_list_items = false;
		}
		
		$this->content = View::factory('shop/ad_list_page')
					->set('ad_list', $ad_list)
					->set('ad_list_items',$ad_list_items)
					->set('currency',$this->currency)					
					->set('parent', $category)
					->set('filters', $filters);
    }
	
	public function action_subcategory()
    {
		$cid = (int) $this->request->param('id');
		
		$category = $parent_data = $this->shop->get_category($cid);
		$this->breadcrumbs->add($category->get('title'), 'shop/subcategory/'.$category->get('id'));
		
		$this->page_title_prefix = $category->get('title').' - Объявления - Hi-Fi Forum';
		
		$cat_list = $this->shop->get_subcategory($cid);
		$tpl = View::factory('shop/sub_block')
                    ->set('cat_list',$cat_list);
		
		$this->content = $tpl;
    }
	
	public function action_create_ad()
    {
	
	  if ($this->session->isAuth()) 
	  {
		  
		$can_create = false;
		$can_new = false; 
		$user = $this->session->user();
		if ($user->isAdmin())
		{
			$can_new = 1024;
		}
		
		//check count ads limit
		if (!$user->isModer())
		{
			
			$counts = $this->shop->getAdsCountByUid($user->get('uid'));
			
			$can_new = false;
			
			if ($counts['all'] < $counts['limit_count'])
			{
				$can_create = true;
				if ($counts['is_new'] < $counts['limit_new'])
				{
					$can_new = ($counts['limit_new']==1024)?1024:($counts['limit_new'] - $counts['is_new']);
				}
				else 
				{
					$can_new = 0;
				}
			}
			else 
			{
				$can_create = false;
			}
		}
		else 
		{
			$can_create = true;
		}
		
		if ($can_create)
		{
			
			$gid = $user->getGroupID();
			
			if ($cid = (int) $this->request->param('id'))
			{	
				$parent = new Model_Shop_Category($cid);

				$groups_create = $parent->get('groups_create');
		
				if (in_array($gid, explode(',',$groups_create)))
				{
				
					$this->breadcrumbs->add($parent->get('title'),'shop/category/'.$cid);
					
					if ($_POST) 
					{
						if (isset($_POST['action']) && $_POST['action'] == 'create_ad')
						{
							unset($_POST['action']);
							$ad = new Model_Shop_Ad();
							
							if ($ad = $ad->put($_POST))
							{
								$info = $ad->info();
								$this->redirect(Request::$base_url.'shop/edit/'.$info['id']);
							} else {
								//todo: not created ad show notice
							}
						}
					}
					
					//var_export($parent);
					$this->content = View::factory('shop/form')->set('category',$parent)->set('can_new', $can_new);
				}
				else 
				{
					$this->breadcrumbs->add($parent->get('title'),'shop/category/'.$cid);
					$this->content = 'У вас нет возможности создавать объявления в данном разделе, по причине ограничения прав доступа';
					return false;
				}
				
			} else {
				$categories = $this->shop->get_category();
				$cats = $categories->getTopStruct();
				
				$this->content = View::factory('shop/create_form')
								->set('cats',$cats);
			}
			
			$this->breadcrumbs->add('Создание объявления','shop/create_ad');
		
		}
		else 
		{
			$this->content = View::factory('shop/create_limit');
		}							
	  } 
	  else 
	  {
		$this->content = View::factory('not_reg');
	  }
    }
	
	public function action_edit()
	{		
		if ($this->session->isAuth()) 
		{
			$user_id = $this->session->user()->get('uid');
			
			if ($ad_id = (int) $this->request->param('id'))
			{			
				$ad = $this->shop->get_ad($ad_id);
				//$access = $this->shop->ad_access($this->user);
				
				if ($ad->is_editable())
				{
					
					$change = false;
					
					$info = $ad->info();
					
					$def_status = $info['status'];
					
					if (!$this->session->user()->isAdmin())
					{
						$ad->unApproved();
					}
					
					if ($user_id == $info['author_id'])
					{
						$this->breadcrumbs->add('Мои объявления','profile/'.$this->session->user()->get('uid').'/ads')->add('Редактирование','shop/edit_ad');
					}
					else {
						$category = $this->shop->get_category($info['category_id']);
						if ($pid = $category->get('cid'))
						{
							$parent = $this->shop->get_category($pid);
							$this->breadcrumbs->add($parent->get('title'), 'shop/subcategory/'.$parent->get('id'));
						}
						
						$this->breadcrumbs->add($category->get('title'),'shop/category/'.$category->get('id'))->add($info['title'], 'shop/view/'.$info['id']);
					}
					
					if ($_POST)
					{
						$data = $_POST;
						
						$data['id'] = $ad_id;
						
						if ($res = $ad->put($data))
						{
							if (isset($data['action']) && $data['action'] == 'approve' && $this->session->user()->isAdmin())
							{
								
								$up = ($def_status == 2)?true:false;
								
								if ($up)
								{
									if ($ad->approve(false))
									{
										$message = View::factory('shop/mails/is_approve')->set('ad', $ad)->render();
										$subject = 'Ваше объявление активировано';
										$pm = new Model_PM();
										$pm->createNotification($info['author_id'], $message, $subject);
										
										//var_export($info);
										$this->redirect(Request::$base_url.'admin/moder/ads');
										return true;
									}
								}
								else 
								{
									if ($def_status != 1)
									{
										if ($ad->approve(false))
										{
											//var_export($info);
											$this->redirect(Request::$base_url.'admin/moder/ads');
											return true;
										}
									}
									elseif ($def_status == 1)
									{
										DB::update('shop_ads', array('status'=>1), 'id='.$ad_id);
									}
								}
							}
							else 
							{
								if ($this->session->user()->isAdmin())
								{
									$ad->unApproved();
									DB::update('shop_ads', array('status'=>2), 'id='.$ad_id);
								}
							}
							$change = true;
							$ad = $res;
							$data = $ad->info();
							
							$this->redirect('/shop/view/'.$data['id']);
						}
						$this->redirect('/shop/view/'.$ad_id);
					}					
					$this->content = View::factory('shop/edit_ad')->set('ad',$ad)->set('user', $this->session->user())->set('change',$change);
				} else {
					$this->content = 'Вы не имеете право редактировать это объявление';
				}
			}
		} else {
			$this->content = View::factory('not_reg');
		}
		
	}
	
	public function action_edit_filters()
	{
		if ($this->session->isAuth()) 
		{
			$user_id = $this->session->user()->get('uid');
			
			if ($ad_id = (int) $this->request->param('id'))
			{			
				$ad = $this->shop->get_ad($ad_id);
				
				if ($ad->isEditable())
				{
					
					$info = $ad->info();
					
					if ($_POST)
					{
						if (isset($_POST['change_form']) && ($_POST['change_form'] == '1'))
						{
							if ($info['status'] == 1)
							{
								$ad->unApproved();
							}
							if ($ad->changeForm($info['id'], $_POST))
							{
								$this->redirect('/shop/edit/'.$info['id']);
							}
						}
						$this->redirect('/shop/edit/'.$info['id']);
					}
					
					$form_items = unserialize($info['form_items']);
					
					$category = new Model_Shop_Category($info['category_id']);
					
					$can_new = false; 
					$user = $this->session->user();
					if ($user->isAdmin())
					{
						$can_new = 1024;
					}
					
					//check count ads limit
					if (!$user->isModer())
					{
						
						$counts = $this->shop->getAdsCountByUid($user->get('uid'));
						
						$can_new = false;						
						if ($counts['all'] < $counts['limit_count'])
						{
							if ($counts['is_new'] < $counts['limit_new'])
							{
								$can_new = ($counts['limit_new']==1024)?1024:($counts['limit_new'] - $counts['is_new']);
							}
							else {
								$can_new = 0;
							}
						}
					}
					
					
					$this->content = View::factory('shop/form_edit')
							->set('ad', $ad)
							->set('category', $category)
							->set('can_new', $can_new)
							->set('form_items', $form_items);
				}
			}
		}
	}
	
	public function action_reject()
	{		
		if ($this->session->isAuth() && $this->session->user()->isAdmin()) 
		{
			$data = $_POST;
			if (isset($data['ad_id']) && isset($data['reject_message']))
			{
				if ($ad = $this->shop->get_ad((int) $data['ad_id']))
				{
					$info = $ad->info();
					if ($info['status'] != 3)
					{
						$reject_message = (string) $data['reject_message'];
						
						if ($ad->reject($reject_message))
						{
							$info = $ad->info();
						
							$message = View::factory('shop/mails/un_approve')->set('ad', $ad)->render();
							$subject = 'Ваше объявление отклонено';
							$pm = new Model_PM();
							$pm->createNotification($info['author_id'], $message, $subject);
							
							$this->redirect(Request::$base_url.'admin/moder/ads');
						}
						else 
						{
							$this->redirect(Request::$base_url.'shop/view/'.$data['ad_id']);
						}
					}
					else 
					{
						$this->content = 'Сообщение уже отклоненно';
					}
				}
			}
		}
	}
	
	public function action_get_form()
	{
		if ($this->request->param('id') || (isset($_POST['cid']) && !empty($_POST['cid'])))
		{		
			$cid = (int) (isset($_POST['cid'])?$_POST['cid']:$this->request->param('id'));
			
			$form = $this->shop->get_category($cid)->get_form();
			
			//$this->debug($form);
			
			$tpl = View::factory('shop/form')->set('form',$form);
				
			if ($this->is_ajax)
			{
				$res = array('content' => $tpl->render());
				$this->content = json_encode($res);	
				return false;
			} else {
				$this->content = $tpl;
				return true;
			}
		} else {
			$this->content = '{"error":"Not cid"}';			
		}
	}
	
	public function action_image_add()
	{
		$ad_id = (int) $this->request->param('id');
		
		$ad = $this->shop->get_ad($ad_id);
		
		if ($ad->is_editable())
		{
		
			if (isset($_POST['input_name']))
			{
				if ($image = $ad->add_attach($_POST, 1))
				{
					$this->content = $image['file_name'];
					return $image['file_name'];
				}
			}
		}
		
		return false;
	}
	
	public function action_outdated()
	{
		if ($id = (int) $this->request->param('id'))
		{
			if ($item = $this->shop->getOutDated($id))
			{
				//var_export($item);
				$this->content = View::factory('shop/outdated_ad')->set('ad',$item);
			}
			else 
			{
				$this->content = 'Объявление не существует или было удалено<br /><a href="/">Перейти на главную страницу</a>';
			}
		}
		else 
		{
			//show all removed items
			$items = $this->shop->getOutDated();
			
			$this->content = View::factory('shop/outdated_list')->set('outdated',$items);
		}
	}
	
	public function action_attach_add()
	{
		$ad_id = (int) $this->request->param('id');
		
		$ad = $this->shop->get_ad($ad_id);
		
		if ($ad->is_editable())
		{
		
			if (isset($_POST['input_name']))
			{
				if ($image = $ad->add_attach($_POST))
				{
					$this->content = $image['file_name'];
					return $image['file_name'];
				}
			}
		}
		
		return false;
	}
	
	public function action_get_attach()
	{
		$ad_id = (int) $this->request->param('id');
		
		$ad = $this->shop->get_ad($ad_id);
		
		$attachments = $ad->get_attachments();
		
		if ($attachments)
		{
			$this->content = json_encode($attachments);
		} else {
			$this->content = '{}';
		}
		
	}
	
	public function action_remove()
	{
		if ($_POST && isset($_POST['id']) && $this->session->user()->isAdmin())
		{
			$ad_id = (int) $_POST['id'];
			$ad = new Model_Shop_Ad($ad_id);
			
			if ($ad->isEditable())
			{
				$info = $ad->info();
				
				if ($ad->remove())
				{
					echo '1';
				}
				else 
				{
					echo '0';
				}
			}
			else 
			{
				echo '0';
			}
			exit;
			return true;
		}
		else 
		{
			$ad_id = (int) $this->request->param('id');
			if ($ad = $this->shop->get_ad($ad_id))
			{
				if ($ad->isEditable())
				{
				$info = $ad->info();
				if ($ad->remove())
					{
						$this->redirect(Request::$base_url.'shop/category/'.$info['category_id']);
					}
					else 
					{
						$this->redirect(Request::$base_url.'shop/view/'.$info['id']);
					}
				}
			}
		}
	}
	
	public function action_remove_attach()
	{
		$ad_id = (int) $this->request->param('id');
		
		if (isset($_POST['tag']) && isset($_POST['order']) && $_POST['tag'] == 'ad'.$ad_id)
		{
			$order= (int) $_POST['order'];
			
			$ad = $this->shop->get_ad($ad_id);
			
			$attachments = $ad->remove_attach($order);
			
			if ($attachments)
			{
				$this->content = json_encode($attachments);
			} else {
				$this->content = 'empty';
			}
		}
	}
	
	public function action_change_currency()
	{
		if (isset($_POST['currency']) || isset($_GET['currency']))
		{
			$curr = isset($_POST['currency'])?$_POST['currency']:$_GET['currency'];
			
			$config = loadConfig('shop_config');
			
			if (isset($config['currency_codes']['prefix']) && in_array($curr, $config['currency_codes']['names']))
			{
				if ($new_currency = $config['currency_codes']['prefix'][$curr])
				{
					if ($this->session->isAuth())
					{
						$user = new Model_UserInfo($this->session->user()->get('uid'));
						if ($user->changeFields(array('currency'=>$new_currency)))
						{
							//
						}
					}
					Cookie::set('user_currency', $new_currency, 15*24*60*60);
					echo $new_currency;
				}				
			}
		}
	}


}