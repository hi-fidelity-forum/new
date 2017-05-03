<?php defined('SYSPATH') or die('No direct script access.');

class Model_Shop_Category extends Model 
{

	protected static $_all_categories = false;
	protected static $categories_struct = false;
	
	private $table_categories = 'shop_categories';
	
	private $_ad_list;
	private $_filters = null;
	private $_category;
	
	private $_cid = false;
	
	public function __construct($cid = NULL, $filter_string = null)
	{  
		$filter_string = (string) $filter_string;
		
		$this->config = loadConfig('shop_config');
		
		$this->currency = new Model_Currency();
		$this->currency->loadCurrency();
		
		if ($cid !== NULL && ((int) $cid))
		{
			$cid = (int) $cid;
					
			$this->setCid($cid);
			if ($filter_string)
			{
				$this->getFilters($cid, $filter_string);
			}
			$this->_category = $this->getCategory($cid);
		}
			
		parent::__construct();
        return $this;        
    }
	
	function setCid($cid = null)
	{
		if ($cid !== null && ((int) $cid) )
		{
			$this->_category = $this->getCategory($cid);
			return $this->_cid = $cid;
		}
		else
			throw new NExcception('Not specified CID');
	}
	
	function getCid()
	{
		if ($this->_cid !== null)
		{
			return $this->_cid;	
		}
		else
			throw new NExcception('Not specified CID, please use setCid');
	}
	
	public function getCategory($cid = false)
	{
		$cid = (int) $cid;
		if ($cid == false) $cid = $this->getCid();
		
		$all = $this->getAll(true);
		
		if (isset($all[$cid]))
		{
			return $all[$cid];
		}
		return false;
	}
	
	public function getAll($show_disable = false)
	{

		if (self::$_all_categories == false)
		{
			$cats = array();
			
			$all = false;
			
			$show_disable = (boolean) $show_disable;
			
			$q = 'SELECT * FROM shop_categories '.($show_disable?'':'WHERE disable=0').' ORDER BY disporder ASC';
			
			if ($req = DB::query($q))
			{	
				$last_data = $last_def = array('last_ad_date'=>0,'last_ad_id'=>0);
				
				while ($row = $req->fetch())
				{	
					$all[$row['id']] = $row;
					$last_data['last_ad_date'] = $row['last_ad_date'];
					$last_data['last_ad_id'] = $row['last_ad_id'];
					$last_data['last_ad_uid'] = $row['last_ad_uid'];
					$last_data['last_ad_username'] = $row['last_ad_username'];
					$last_data['last_ad_title'] = $row['last_ad_title'];
					$last_data['last_ad_image'] = $row['last_ad_image'];
					
					if ($row['cid'] == 0)
					{
						if (!isset($cats[$row['id']]))
						{
							$cats[$row['id']] = $row;
							$cats[$row['id']]['last_data'] = $last_data;
						} else {
							$last_data = $cats[$row['id']]['last_data'];
							$cats[$row['id']] = array_merge($row, $cats[$row['id']]);
							$cats[$row['id']]['last_data'] = $last_data;
						}
						
					} else {
						
						$cats[$row['cid']]['subcategories'][$row['id']] = $row;
						if (!isset($cats[$row['cid']]['ad_counts'])) $cats[$row['cid']]['ad_counts'] = 0;
						$cats[$row['cid']]['ad_counts'] += $row['ad_counts'];
						
						if (!isset($cats[$row['cid']]['last_data']))
						{
							$cats[$row['cid']]['last_data'] = $last_data;
						} 
						elseif ($cats[$row['cid']]['last_data']['last_ad_date']<$row['last_ad_date'])
						{
							$cats[$row['cid']]['last_data'] = $last_data;
						} 
					}
					
					self::$_all_categories[$row['id']] = $row;
				}
			}
			
			self::$categories_struct = $cats;
		
			return $all;
		} else 
		{
			return self::$_all_categories;
		}
	}
	
	public function getTopStruct($show_disable = false)
	{
		if (self::$categories_struct == false)
		{
			$this->getAll($show_disable);
		}
		return self::$categories_struct;
	}
	
	public function reload()
	{
		$cid = $this->getCid();
		self::$_all_categories = false;
		$this->getAll(true);
		$this->_filters = new Model_Shop_Filters($cid);
		$this->_category = $this->getCategory($cid);
		return $this;
	}
	
	public function change($data)
	{
		$cid = $this->getCid();
		if ($old = $this->getCategory($cid))
		{
			$new_items = false;
			foreach ($data as $key=>$item)
			{
				if (isset($old[$key]))
				{
					$new_items[$key] = $item;
				}
			}
			if ($new_items)
			{
				if (DB::update('shop_categories', $new_items, 'id='.$cid))
				{
					return $this->reload();
				}
			}
			return true;
		}
		return true;
	}
	
	public function get_top_struct($show_disable = false)
	{
		return $this->getTopStruct($show_disable);
	}
	
	public function getSub($cid = false, $show_disable = false)
	{
		if (self::$categories_struct == false)
		{
			$this->getAll($show_disable);
		}
		$cid = $cid?$cid:$this->getCid();
		if (isset(self::$categories_struct[$cid]['subcategories']))
		{
			return self::$categories_struct[$cid]['subcategories'];
		}
		return false;
	}
	
	public function get_sub($cid = 0)
	{
		$cid = (int) $cid;
		return $this->getSub($cid);
	}
	
	public function get($param = false, $cid = null)
	{
		$cid = $cid===null?$this->getCid():((int) $cid);
		
		if ($param)
		{
			if (isset(self::$_all_categories[$cid]))
			{
				if (isset(self::$_all_categories[$cid][$param]))
				{
					return self::$_all_categories[$cid][$param];
				}
			}
		}
		return false;		
	}
    
    public function getAdList()
	{
		$cid = $this->getCid();
		
		$def_curr = $this->currency->getUserCurrency();
		
		if ($this->_ad_list === null)
		{
			$filters = $this->getFilters()->getAll();
			if ($this->_filters && $filter_request = $this->_filters->filterRequest())
			{
				$items = $filter_request['items'];
				$qr = '';
				foreach ($items as $name => $item)
				{
					if (isset($filters[$name]))
					{
						$filter = $filters[$name];
						if ($filter['cond'] == 1)
						{
							//$qr .= "\n".'INNER JOIN `shop_filter_ads` fs ON (ad.id = fs.ad_id AND fs.filter_item_id IN ('.implode(',',$item).'))';
							
							$qr .= "\n".'INNER JOIN (
											SELECT * FROM `shop_filter_ads`
											WHERE filter_item_id IN ('.implode(',',$item).')
											GROUP BY ad_id
											HAVING COUNT(filter_item_id) = '.count($item).'';
							
							$qr .= ') fs_'.$name.' ON (ad.id = fs_'.$name.'.ad_id)';
						}
						else 
						{
							$qr .= "\n".'INNER JOIN `shop_filter_ads` fs_'.$name.' ON (ad.id = fs_'.$name.'.ad_id AND fs_'.$name.'.filter_item_id IN ('.implode(',',$item).'))';
						}
					}
				}
				
				$q = '
					SELECT * FROM (
						SELECT ad_list.*,
						IF (ad_list.currency_code = '.$def_curr.',
							ad_list.price,
							ad_list.price/cv.factor
						) as new_price,
						IF (ad_list.currency_code = '.$def_curr.',
							ad_list.currency_name,
							cv.prefix
						) as currency
						FROM shop_ads ad_list
						LEFT JOIN currency_values cv ON (ad_list.currency_code = cv.code_source AND cv.code_key = '.$def_curr.')
						WHERE ad_list.category_id='.$cid.' AND ad_list.status = 1
					) as ad
					'.$qr.'
					ORDER BY ad.last_ad_date DESC';
			} else {
				$q = '
				SELECT * FROM (
					SELECT ad_list.*, 
						IF (ad_list.currency_code = '.$def_curr.',
							ad_list.price,
							ad_list.price/cv.factor
						) as new_price,
						IF (ad_list.currency_code = '.$def_curr.',
							ad_list.currency_name,
							cv.prefix
						) as currency
					FROM shop_ads ad_list
					LEFT JOIN currency_values cv ON (ad_list.currency_code = cv.code_source AND cv.code_key = '.$def_curr.')
					WHERE ad_list.category_id='.$cid.' AND ad_list.status = 1
				) as ads
				LEFT JOIN (
					SELECT parent_id, COUNT(*) as count_items
					FROM shop_ad_items
					GROUP BY parent_id
				) as items
				ON (ads.is_group=1 AND ads.id = items.parent_id)
				ORDER BY ads.last_ad_date DESC
				';
			}
			
			
				
			if ($page = new Paging($q))
			{
				$page->setPageSize(25);
				return $this->_ad_list = $page->execute();
			}
			
		}
		return $this->_ad_list;
	}
	
	function create_category($data = false)
	{
		if ($data)
		{
			$cid = (int) $data['category_id'];
			$title = (string) $data['title'];
			$groups_read = (string) $data['groups_read'];
			$groups_create = (string) $data['groups_create'];
			
			$q = '
				INSERT INTO '.$this->table_categories.'
				(`cid`, `title`, `disable`, `disporder`, `last_ad_id`, `last_ad_date`, `groups_read`, `groups_create`)
				VALUES ('.$cid.', "'.$title.'", 0, 0, 0, 0, "'.$groups_read.'", "'.$groups_create.'")
			';
			
			//print_r($q);
			if ($res = DB::query($q)){
				return $res;
			}
			
			return false;
		}
	}
	
	public function getFilters($cid = false, $filter_string = false)
	{
		if ($this->_filters === null)
		{
			$cid = (int) $cid;
			if (!$cid) $cid = $this->getCid();				
			$filters = new Model_Shop_Filters($cid, $filter_string);
			$this->_filters = $filters;
			return $filters;
		}
		else 
		{
			return $this->_filters;
		}
		return false;
	}
	
	
	
	function getParent()
	{
		$cid = $this->getCid();		
		$pid = $this->get('cid');
		
		return new Model_Shop_Category($pid);
	}
	
	public static function update_last_data($cid = false)
	{
		if ($cid !== false)
		{
			$q = 'SELECT * FROM shop_ads WHERE `category_id` = '.$cid.' AND status=1 ORDER BY last_ad_date DESC LIMIT 1';
			
			if ($ad = DB::query($q)->fetch())
			{
				$count = 0;
				if ($req = DB::query('SELECT COUNT(*) as count FROM shop_ads WHERE `category_id` = '.$cid.' AND status=1 LIMIT 1'))
				{
					$rows = $req->fetch();
					$count = $rows['count'];
				}
				$s='UPDATE `shop_categories`
					SET
						`ad_counts` = '.DB::quote($count).',
						`last_ad_date` = '.DB::quote($ad['last_ad_date']).',
						`last_ad_id` = '.DB::quote($ad['id']).',
						`last_ad_uid` = '.DB::quote($ad['author_id']).',
						`last_ad_username` = '.DB::quote($ad['author_name']).',
						`last_ad_title` = '.DB::quote($ad['title']).',
						`last_ad_image` = '.DB::quote($ad['image']).'
					WHERE `id` = '.$cid.'
				';
				
				if (DB::query($s))
				{					
					return true;
				}
			}
		}
	}
	
	public function remove()
	{
		$cid = $this->getCid();
		$filters = $this->getFilters();
		$filters->removeByCid($cid);
		if (DB::beginTransaction())
		{
			DB::query('DELETE FROM shop_ads WHERE category_id = '.$cid);
			DB::query('DELETE FROM shop_ad_items WHERE category_id = '.$cid);
			DB::query('DELETE FROM shop_categories WHERE id = '.$cid);
			if (DB::commitTransaction())
			{
				$rmdir = DOCROOT.'uploads/attachments/' . $cid;
				if (is_dir($rmdir))
				{
					system('rm -rf ' . escapeshellarg($rmdir), $retval);
				}				
				return true;
			}
		}
		return false;
	}
}