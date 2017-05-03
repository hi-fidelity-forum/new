<?php defined('SYSPATH') or die('No direct script access.');

class Model_Shop_Ad extends Model
{
	private $_active_ad = false;
	private $_filters = false;
	
	public $default_ad = array(
		'title' => '',
		'status' => '0',
		'author_id' => '',
		'author_name' => '',
        'description' => '',
        'price' => '0',
        'currency_name' => 'USD',
        'currency_code' => '1',
		'last_ad_date' => '0'
	);
	
	public function __construct($id = false)
	{    
        parent::__construct();
		
		if ($id)
		{
			$id = (int) $id;
			$ad = $this->_get_ad($id);
			$this->_active_ad = $ad;
			if ($ad['category_id'])
				$this->_filters = new Model_Shop_Filters($ad['category_id']);
		}
    
        return $this;
        
    }
	
	public function _get_ad($id = false)
	{
		$id = $id?( (int) $id):false;
		if ($id)
		{
			//todo: create select query for ad
			
			$q = '
				SELECT *
				FROM shop_ads as ad
				WHERE id='.$id.'
				LIMIT 1';
			
			if ($req = DB::query($q))
			{
				if ($res = $req->fetch())
				{
					$ad = $res;
					return $ad;
				}
			}
			return false;
		}
		
		return false;
	}
	
	public function info()
	{
		return $this->_active_ad;
	}
	
	public function user()
	{
		if ($user = new User($this->_active_ad['author_id']))
		{
			return $user;
		}
		return false;
	}
	
	public function isEditable()
	{
		if (!$this->session->isAuth())
		{
			return false;
		}
		
		if ($this->session->user()->isModer())
		{
			return true;
		} else {
			$user_id = $this->session->user()->get('uid');
			$info = $this->info();
			//todo: проверить прошло ли время или есть ли возможность редактирования
			if ($user_id == $info['author_id'])
			{
				return true;
			}
		}
		return false;
	}
	
	public function is_editable()
	{
		return $this->isEditable();
	}
	
	public function incViews()
	{
		$info = $this->info();
		$id = $info['id'];
		$views = $info['views'];
		$new_views = $views + 1;
		if (DB::asyncQuery('UPDATE LOW_PRIORITY shop_ads SET `views` = '.$new_views.' WHERE `id`='.$id))
		{
			$this->_active_ad['views'] = $new_views;
			return true;
		}
		return false;
	}
	
	public function upDate()
	{
		$info = $this->info();
		
		$ad_id = $info['id'];
		$d['last_ad_date'] = TIME_NOW;
				
		if (DB::update('shop_ads', $d, 'id='.$ad_id))
		{
			Model_Shop_Category::update_last_data($info['category_id']);
			return true;
		}
		return false;
	}
	
	public function put($data)
	{    
        $id = isset($data['id'])?(int) $data['id']:false;
		$cid = isset($data['cid'])?(int) $data['cid']:0;
		
		$currency = new Model_Currency();
		
		if ($id !== false && $ad = $this->_get_ad($id))
		{
			//todo: check status
			
			$ad['title'] = isset($data['title']) ? (string) $data['title'] : $ad['title'];
			$ad['description'] = isset($data['description']) ? (string) $data['description'] : $ad['description'];
			$ad['price'] = isset($data['price']) ? (int) $data['price'] : $ad['price'];
			
			$ad['currency_name'] = isset($data['currency']) ? (string) $data['currency'] : $ad['currency'];
			$ad['currency_code'] = $currency->nameToCode($ad['currency_name']);
			
			$ad['status'] = isset($data['status'])?$data['status']:'2';
			
			if (DB::update('shop_ads', $ad, 'id = '.$id))
			{
				$this->_active_ad = $ad;
				
				return $this;
			}
			
			return false;
			
		} else {
			
			$def_ad = $this->default_ad;
			
			$datetime = TIME_NOW;
			
			$ad['category_id'] = (int) $data['cid'];
			$ad['title'] = isset($data['title']) ? (trim((string) $data['title'])) : $def_ad['title'];
			$ad['create_date'] = $ad['last_ad_date'] = $datetime;
			
			$ad['price'] = isset($data['price']) ? (int) $data['price'] : $def_ad['price'];
			
			$ad['currency_name'] = isset($data['currency']) ? (string) $data['currency'] : $def_ad['currency'];
			$ad['currency_code'] = $currency->nameToCode($ad['currency_name']);
			
			$ad['is_new'] = (isset($data['is_new']) && $data['is_new'])? '1' : '0';
			
			$ad['author_id'] = (int) $data['author_id'];
			$ad['author_name'] = $data['author_name'];
			$ad['spec'] = $data['spec'];
			$ad['form_items'] = serialize($data['form']);
			
			$ad['country_id'] = $data['country_id'];
			$ad['country_title'] = $data['country_title'];
			$ad['region_id'] = $data['region_id'];
			$ad['region_title'] = $data['region_title'];
			$ad['city'] = $data['city'];
			
			//todo: написать функцию добавления объявления с применением транзакций
			
			if ($res = DB::insert('shop_ads', $ad))
			{
				$new_id = $res[0];
				$ad = $this->_get_ad($new_id);
				
				$this->_active_ad = $ad;
				
				return $this;
			}
			return false;
		}		
		
		return false;
        
    }
	
	function changeForm($id, $data = false)
	{
		if ($id !== false && $def_ad = $this->_get_ad($id))
		{
			
			$ad['title'] = isset($data['title']) ? (trim((string) $data['title'])) : $def_ad['title'];
			
			$ad['spec'] = $data['spec'];
			$ad['form_items'] = serialize($data['form']);
			
			//$ad['price'] = isset($data['price']) ? (int) $data['	'] : $def_ad['price'];
			//$ad['currency'] = isset($data['currency']) ? (string) $data['currency'] : $def_ad['currency'];
			$ad['is_new'] = (isset($data['is_new']) && $data['is_new'])? '1' : '0';
			/*
			$ad['country_id'] = $data['country_id'];
			$ad['country_title'] = $data['country_title'];
			$ad['region_id'] = $data['region_id'];
			$ad['region_title'] = $data['region_title'];
			$ad['city'] = $data['city'];
			*/
			
			if ($res = DB::update('shop_ads', $ad, 'id='.$id))
			{
				$ad = $this->_get_ad($id);
				$this->_active_ad = $ad;				
				return $this;
			}
		}
		return false;
	}
	
	function approve($update = true)
	{
		if ($info = $this->info())
		{
			$info = $this->info();
			$ad_id = $info['id'];
			$form_items = unserialize($info['form_items']);
			$ad_options = $this->_filters;
			$filters = $ad_options->getAll();
			
			//var_export($filters);
			$new_items = false;
			$inx = 1;
			foreach ($form_items as $key=>$item)
			{
				if (isset($filters[$key]) && $filters[$key]['hidden'] != 1)
				{
					if (gettype($item) == 'array')
					{
						foreach ($item as $k=>$v)
						{
							$new_items[$inx]['filter_name'] = $filters[$key]['name'];
							$new_items[$inx]['filter_id'] = $filters[$key]['id'];
							$new_items[$inx]['filter_type'] = $filters[$key]['type'];
							$new_items[$inx]['filter_item_value'] = trim($v);
							
							if ($filters[$key]['items'])
							{
								$item_id = false;
								foreach ($filters[$key]['items'] as $fi)
								{
									if (strtolower(trim($fi['item_value'])) == strtolower(trim($v)))
									{
										$item_id = $fi['id'];
									}
								}
								$new_items[$inx]['filter_item_id'] = $item_id;
							}
							else 
							{
								$new_items[$inx]['filter_item_id'] = false;
							}
							
							$inx++;
						}
						
					}
					else 
					{
						$new_items[$inx]['filter_name'] = $filters[$key]['name'];
						$new_items[$inx]['filter_id'] = $filters[$key]['id'];
						$new_items[$inx]['filter_type'] = $filters[$key]['type'];
						$new_items[$inx]['filter_item_value'] = trim($item);
						
						if ($filters[$key]['items'])
						{
							$item_id = false;
							foreach ($filters[$key]['items'] as $fi)
							{
								if (strtolower(trim($fi['item_value'])) == strtolower(trim($item)))
								{
									$item_id = $fi['id'];
								}
							}
							$new_items[$inx]['filter_item_id'] = $item_id;
						}
						else 
						{
							$new_items[$inx]['filter_item_id'] = false;
						}
						
						$inx++;
					}
				}
			}
			

			if (DB::beginTransaction())
			{
				if ($new_items)
				{
					$q = 'INSERT INTO shop_filter_ads (filter_item_id, ad_id) VALUES ';
					foreach ($new_items as $k=>$item)
					{
						if ($item['filter_item_id'])
						{
							$q .= '('.$item['filter_item_id'].', '.$ad_id.'),';
						}
						else 
						{
							if (in_array($item['filter_type'], array('brand', 'string')))
							{
								if (($value = $item['filter_item_value']) && $item['filter_id'])
								{
									$new_filter_item = array();
									$new_filter_item['filter_id'] = $item['filter_id'];
									$new_filter_item['item_title'] = $value;
									$new_filter_item['item_value'] = $value;
									$filter_id = DB::insert('shop_filter_items', $new_filter_item);
									$filter_id = $filter_id[0];
									$new_items[$k]['filter_item_id'] = $filter_id;
									$q .= '('.$filter_id.', '.$ad_id.'),';
								}
							}
						}
					}
					$q = rtrim($q, ',');
					DB::query($q);
				}
				
				$d['status'] = '1';
				$d['reject'] = '';
				if ($update)
				{
					$d['last_ad_date'] = TIME_NOW;
				}
				
				DB::update('shop_ads', $d, 'id='.$ad_id);
				//todo: добавлять в список категорий last_id
				Model_Shop_Category::update_last_data($info['category_id']);
				
				if (DB::commitTransaction())
				{
					return true;
				}
				
			}
		}
		return false;
	}
	
	function getSpecifications()
	{
		$info = $this->info();
		$form_items = unserialize($info['form_items']);
		$ad_options = $this->_filters;
		$filters = $ad_options->getAll();
		$new_arr = false;
		//var_export($filters);
		foreach ($form_items as $key=>$item)
		{
			if (isset($filters[$key]))
			{
				$new_arr[$key]['title'] = $filters[$key]['title'];
				if (gettype($item) == 'array')
				{
					foreach ($item as $k=>$v)
					{
						if (isset($filters[$key]['items'][$k]))
						{
							$new_arr[$key]['value'][$k]['title'] = $filters[$key]['items'][$k]['item_title'];
						}
					}
				}
				else 
				{
					if (in_array($filters[$key]['type'], array('brand','string','range')))
					{
						$new_arr[$key]['value'] = $item;
					} 
					else 
					{
						if (isset($filters[$key]['items']))
						{
							foreach ($filters[$key]['items'] as $i)
							{
								if (strtolower($i['item_value']) == strtolower($item))
								{
									$new_arr[$key]['value'] = $i['item_title'];
								}
							}
						}
					}
				}
				
			}
		}
		return $new_arr;
	}
	
	public function unApproved()
	{
		
		if ($this->is_editable() && $info = $this->info())
		{
			$ad_id = $info['id'];
			
			if (DB::beginTransaction())
			{
				
				$this->_remove_filter_items($ad_id);
				
				$this->_active_ad['status'] =  $ad['status'] = '0';
				
				DB::update('shop_ads', $ad, 'id='.$ad_id);
				
				Model_Shop_Category::update_last_data($info['category_id']);
				if (DB::commitTransaction())
				{
					return true;
				}
			}
		}
		
		return false;
	}
	
	public function remove()
	{
		
		if ($this->is_editable() && $info = $this->info())
		{
			$ad_id = $info['id'];
			$cid = $info['category_id'];
			$attach = new Model_Attach();
			if ($att = $attach->get('ad'.$ad_id))
			{
				if (!$attach->remove_attach('ad'.$ad_id))
				{
					return false;
				}
			}
			
			if (DB::beginTransaction())
			{
				if ($info['status'] == 1)
				{
					if (!$this->createRemovedAd($info))
						return false;
				}
				$this->_remove_filter_items($info['id']);
							
				if ($file_name = $info['image'])
				{
					$remove_file = DOCROOT.$file_name;
					if (is_file($remove_file))
					{
						chmod($remove_file, 0777);
						unlink($remove_file);
					}
					$rmdir = DOCROOT.'uploads/attachments/' . $cid.'/'.$ad_id;
					rmdir($rmdir);
				}
				//todo: удалять из списка категорий если объява last_ad
				DB::query('DELETE FROM shop_ads WHERE id = '.$ad_id);
				Model_Shop_Category::update_last_data($info['category_id']);
				if (DB::commitTransaction())
				{
					return true;
				}
			}
		}
		
		return false;
	}
	
	public function createRemovedAd($info)
	{
		/* параметры для создания
			id = параметр должен соответствовать удаленному объявлению
			tid = параметр должен соответствовать перемещенной теме
			category_id = id категории где был товар или тема
			title = название 
			description = описание товара
		*/
		//проверяем наличие параметров
		if ((isset($info['id']) || isset($info['tid'])) && isset($info['category_id']) && isset($info['title']) && isset($info['description']))
		{
			$data['ad_id'] = isset($info['id'])?$info['id']:'0';
			$data['tid'] = isset($info['tid'])?$info['tid']:'0';
			$data['category_id'] = $info['category_id'];
			$data['title'] = $info['title'];
			$data['description'] = $info['description'];
			
			if ($inx = DB::insert('shop_removed_ads', $data))
			{
				return $inx;
			}
			
		}
		return false;
	}
	
	private function _remove_filter_items($ad_id = false)
	{
		if ($ad_id)
		{
			//remove approved filter items
			$q='SELECT a.* FROM `shop_filter_ads` AS a
				LEFT JOIN shop_filter_items AS i ON (i.id = a.filter_item_id)
				LEFT JOIN shop_filters AS f ON (f.id = i.filter_id)
				WHERE a.ad_id = '.$ad_id.' AND (f.type IN '."('brand', 'string'))";
			if ($req = DB::query($q))
			{
				$filter_items = false;
				foreach ($req as $item)
				{
					$filter_items[$item['filter_item_id']] = $item['filter_item_id'];
				}
				
				if ($filter_items)
					{
					$filter_items = implode(',', $filter_items);
					
					$freq = DB::query('SELECT * FROM `shop_filter_ads` WHERE filter_item_id IN ('.$filter_items.')');
					$fi = false;
					foreach ($freq as $item)
					{
						$fi[$item['filter_item_id']][$item['ad_id']] = $item['ad_id'];
					}
					$filter_items_del = false;
					foreach ($fi as $inx=>$i)
					{
						if (count($i) < 2)
						{		
							$filter_items_del[$inx] = $inx;
						}
					}
					
					DB::query('DELETE FROM shop_filter_ads WHERE ad_id = '.$ad_id);
					
					if ($filter_items_del)
					{
						$filter_items_del = implode(',', $filter_items_del);
						//todo: удалять только поля типа brand или string
						DB::query('DELETE FROM shop_filter_items WHERE id IN ('.$filter_items_del.')');
					}
				}
			}
			return true;
		}
		return false;
	}
	
	public function reject($reject_message = false)
	{	
		if ($reject_message && $this->is_editable() && $info = $this->info())
		{
			$ad_id = $info['id'];
			
			if (DB::beginTransaction())
			{
				if ($info['status'] != 0)
				{
					$this->_remove_filter_items($ad_id);
				}
				$data['status'] = '3';
				$data['reject'] = $reject_message;
				//todo: удалять из списка категорий если объява last_ad
				DB::update('shop_ads', $data, 'id='.$ad_id);
				$this->_active_ad['reject'] = $reject_message;
				Model_Shop_Category::update_last_data($info['category_id']);
				if (DB::commitTransaction())
				{
					return true;
				}
			}		
		}
		return false;
	}
	
	public function get_attachments()
	{
	
		$ad_id = (int) $this->_active_ad['id'];
		
		$attach = new Model_Attach();
		
		return $attach->get('ad'.$ad_id);
	}
	
	public function add_attach($data = false, $order = false)
	{
		$ad_id = (int) $this->_active_ad['id'];
		
		if ($ad_id !== false && $data !==false)
		{
		
			$info = $this->info();
			$cid = $info['category_id'];
			$input_name = (string) $_POST['input_name'];
				
			$attach = new Model_Attach($input_name, false);
			
			if ($attach->isImage())
			{
				//$attach->loadTmpImage();
				$attachments = $attach->get('ad'.$ad_id);
				//todo: обновить фукционал для загрузки атачментов и добавить ресайз
				//todo: также реализовать превьюшки
				if ($image_info = $attach->upload_file('ad'.$ad_id, $order, false, $cid, $ad_id))
				{
					if (is_array($image_info))
					{
					
						if (($order==false && $attachments == false) || $order == 1)
						{
						
							$attach_file = $image_info['file_name'];
							
							$save_path = 'uploads/attachments/' . $cid.'/'.$ad_id;
							
							$preview = new Model_Attach();
							if ($preview->setImageFromFile(DOCROOT.$attach_file))
							{
								$preview->reduceImage(80,53,true);
								$img = $preview->imageToString();
								$file_name = $save_path.'/preview_ad'.$ad_id.'.png';
								$image_file_name = DOCROOT.$file_name;
								if (file_put_contents($image_file_name, $img))
								{
									DB::update('shop_ads', array('image'=>$file_name), 'id='.$ad_id);
								}
							}
						}
						
						return $image_info;						
					}
					else {
						return false;
					}
					return false;
				}
			}
		}
		return false;
	}
	
	public function remove_attach($order = false)
	{
		if ($ad_id = (int) $this->_active_ad['id'])
		{
			$info = $this->info();
			$cid = $info['category_id'];
			
			if ($order)
			{
				$tag = 'ad'.$ad_id;
				
				$attach = new Model_Attach();
				
				if ($attach->remove_attach($tag, $order))
				{
					if ($order == 1)
					{
						$attachments = $attach->get('ad'.$ad_id);
						if ($attachments == false)
						{
							if ($file_name = $info['image'])
							{
								$remove_file = DOCROOT.$file_name;
								if (is_file($remove_file))
								{
									chmod($remove_file, 0777);
									unlink($remove_file);
								}
								$rmdir = DOCROOT.'uploads/attachments/' . $cid.'/'.$ad_id;
								rmdir($rmdir);
							}
							
							$q = 'UPDATE `shop_ads`
									SET
										`image`=""
									WHERE `id` = '.$ad_id;
							DB::query($q);
						
						} else {
						
							if ($image = $attachments[1])
							{
								$attach_file = $image['file_name'];
							
								$save_path = 'uploads/attachments/' . $cid. '/' . $ad_id;
								
								if (!is_dir(DOCROOT.$save_path))
								{
									if (!is_dir(DOCROOT.'uploads/attachments/'.$cid))
									{
										mkdir(DOCROOT.'uploads/attachments/'.$cid);
										chmod(DOCROOT.'uploads/attachments/'.$cid, 0777);
									}
									mkdir(DOCROOT.$save_path);
									chmod(DOCROOT.$save_path, 0777);
								}
								
								$preview = new Model_Attach();
								if ($preview->setImageFromFile(DOCROOT.$attach_file))
								{
									$preview->reduceImage(80,53,true);
									$img = $preview->imageToString();
									$file_name = $save_path.'/preview_ad'.$ad_id.'.png';
									$image_file_name = DOCROOT.$file_name;
									if (file_put_contents($image_file_name, $img))
									{
										DB::update('shop_ads', array('image'=>$file_name), 'id='.$ad_id);
									}
								}
							}
						}
					}
					return true;
				}
			}
		}
		return false;
	}
	
	public static function isAdCanUp($info = false)
	{
		if ($info)
		{
			$user = new User($info['author_id']);
			$gid = $user->getGroupID();
			
			$config = loadConfig('shop_config');
			
			if (isset($config['user_ads_counts'][$gid]))
			{
				$opt = $config['user_ads_counts'][$gid];
				$last_ad_date = $info['last_ad_date'];
				$ad_time = $last_ad_date;
				if ($ad_time + ((int) $opt['up_time']) * 60*60*24 < TIME_NOW)
				{
					return true;
				}
			}
		}
		return false;
	}
	
}