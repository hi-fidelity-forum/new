<?php defined('SYSPATH') or die('No direct script access.');

class Model_Shop_Filters
{
	//private $_form;
	private $_filters;
	private $_cid;
	private $_filter_request;
	
	protected $_filter_types = array('brand', 'string', 'check', 'select');
	
	public function __construct($cid = false, $filter_str = null)
	{	
		$cid = (int) $cid;
		$this->config = loadConfig('shop_config');
		
		if ($cid)
		{
			$this->_cid = $cid;
			if ($filter_str)
			{
				$this->parseValues($filter_str);
			}
			$this->setCid($cid);
		}
		else {
			throw new NException('Not specified CategoryID for options');
		}        
    }
	
	function getFilterTypes()
	{
		return $this->_filter_types;
	}
	
	function parseValues($str = null)
	{
		if ($str = trim(preg_replace('([^а-яА-Яa-zA-Z0-9&_/ :+=/-])', '', (string) $str)))
		{
			$config = loadConfig('shop_config');
			$items = false;
			foreach (mb_split(':', $str) as $i)
			{
				$m = mb_split('=', $i);
				if (isset($m[1]))
				{
					$items[$m[0]] = mb_split('\+', $m[1]);
				}
			}
			$this->_filter_request['items'] = $items;
		}
		return false;
	}
	
	function setCid($cid = null)
	{
		if ($cid !== null)
		{
			$cid = (int) $cid;
			$this->getAll();
			return $this->_cid = $cid;			
		}
	}
	
	function getCid()
	{
		if ($this->_cid !== null)
		{
			return $this->_cid;
		}
		else
			throw new NException('Not specified CID for options, please use Options::setCid()');
	}
	
	function getAll($cid = false)
	{
		if ($this->_filters === null)
		{
			$cid = $cid?$cid:$this->_cid;
			$q = 'SELECT f.id AS fid, f.*, items.* FROM (
					SELECT * FROM shop_filters 
					WHERE cid='.$cid.'	
					ORDER BY disporder
				 ) as f
				 LEFT JOIN shop_filter_items AS items
				 ON (items.filter_id = f.id)';
		
			if ($req = DB::query($q))
			{
				$new = false;
				foreach ($req as $row)
				{
					$fid = $row['name'];
					$new[$fid]['title'] = $row['title'];
					$new[$fid]['id'] = $row['fid'];
					$new[$fid]['name'] = $row['name'];
					$new[$fid]['style'] = $row['style'];
					$new[$fid]['type'] = $row['type'];
					$new[$fid]['cond'] = $row['cond'];
					$new[$fid]['hidden'] = $row['hidden'];
					$new[$fid]['disporder'] = $row['disporder'];
					$new[$fid]['compulsory'] = $row['compulsory'];
					unset($row['cid'], $row['name'], $row['fid'], $row['title'], $row['type'], $row['keywords'], $row['hidden']);
					if ($row['id'] != null)
					{
						$new[$fid]['items'][$row['id']] = $row;
						if (isset($this->_filter_request['items']) && $this->_filter_request['items'] !== null)
						{
							$actives = $this->_filter_request['items'];
							if (isset($actives[$new[$fid]['name']]))
							{
								$active_item = $actives[$new[$fid]['name']];
								if (in_array($row['id'], $active_item))
								{
										$new[$fid]['items'][$row['id']]['active'] = true;
								}
							}
						}
						
					} else {
						$new[$fid]['items'] = null;
					}
				}
				if (isset($new['brand']) && $new['brand']['items'])
				{
					$items = $new['brand']['items'];
					$arr_keys = false;
					$arr_values = false;
					foreach ($items as $key=>$value)
					{
						$arr_keys[$key] = $value['id'];
						$arr_values[$key] = $value['item_title'];
					}
					array_multisort($arr_values, SORT_ASC, $arr_keys, SORT_DESC, $items);
					$new_items = false;
					foreach ($items as $n)
					{
						$new_items[$n['id']] = $n;
					}
					$new['brand']['items'] = $new_items;
				}
				return $this->_filters = $new;
			}
		}
		else
			return $this->_filters;
	}
	
	function getFilterById($fid = false)
	{
		$fid = (int) $fid;
		if ($fid && $all = $this->getAll())
		{
			foreach ($all as $key=>$item)
			{
				if ($fid == $item['id'])
				{
					return $item;
				}
			}
		}
		return false;
	}
	
	function getFilterByOrder($order = false)
	{
		$order = (int) $order;
		if ($order && $all = $this->getAll())
		{
			foreach ($all as $key=>$item)
			{
				if ($order == $item['disporder'])
				{
					return $item;
				}
			}
		}
		return false;
	}
	
	function changeFilter($data = false)
	{
		if (gettype($data) == 'array')
		{
			$row = $data;
			$new['title'] = $row['title'];
			$new['name'] = $row['name'];
			$new['style'] = $row['style'];
			$new['type'] = $row['type'];
			$new['cond'] = $row['cond'];
			$new['hidden'] = $row['hidden'];
			$new['compulsory'] = $row['compulsory'];
			$id = $row['fid'];
			if (DB::update('shop_filters', $new, 'id = '.$id))
			{
				return true;
			}
		}
		return false;
	}
	
	function addFilter($data)
	{
		if (gettype($data) == 'array')
		{
			$row = $data;
			$cid = (int) $row['cid'];
			$disporder = count($this->getAll($cid))+1;
			$new['cid'] = $cid;
			$new['disporder'] = $disporder;
			$new['title'] = $row['title'];
			$new['name'] = $row['name'];
			$new['style'] = $row['style'];
			$new['type'] = $row['type'];
			$new['cond'] = $row['cond'];
			$new['hidden'] = $row['hidden'];
			$new['compulsory'] = $row['compulsory'];
			if (DB::insert('shop_filters', $new))
			{
				return true;
			}
		}
		return false;
	}
	
	function decOrder($fid = false)
	{
		if ($fid = (int) $fid)
		{
			$cid = $this->getCid();
			$all_filters = $this->getAll($cid);
			if ($f = $this->getFilterById($fid))
			{
				$old_order = $f['disporder'];
				if ($old_order > 1)
				{
					$prev_order = false;
					$prev_filter = false;
					foreach ($all_filters as $k=>$filter)
					{
						if (($filter['disporder'] < $old_order) && $filter['disporder'] > $prev_order)
						{
							$prev_order = $filter['disporder'];
							$prev_filter = $filter;
						}
					}
					if ($prev_order)
					{
						if (DB::beginTransaction())
						{
							DB::update('shop_filters', array('disporder'=>$prev_order), 'id='.$f['id']);
							DB::update('shop_filters', array('disporder'=>$old_order), 'id='.$prev_filter['id']);
							
							if (DB::commitTransaction())
							{
								return true;
							}
						}
					}
				}
			}
			return true;
		}
	}
	
	function incOrder($fid = false)
	{
		if ($fid = (int) $fid)
		{
			$cid = $this->getCid();
			$all_filters = $this->getAll($cid);
			if ($f = $this->getFilterById($fid))
			{
				$old_order = $f['disporder'];
				if ($old_order < count($all_filters))
				{
					$next_order = count($all_filters);
					$next_filter = false;
					foreach ($all_filters as $k=>$filter)
					{
						if (($filter['disporder'] > $old_order) && $filter['disporder'] <= $next_order)
						{
							$next_order = $filter['disporder'];
							$next_filter = $filter;
						}
					}
					if (DB::beginTransaction())
					{
						DB::update('shop_filters', array('disporder'=>$next_order), 'id='.$f['id']);
						DB::update('shop_filters', array('disporder'=>$old_order), 'id='.$next_filter['id']);
						
						if (DB::commitTransaction())
						{
							return true;
						}
					}
				}
			}
			return true;
		}
	}
	
	function removeFilter($fid = false)
	{
		$fid = (int) $fid;
		if ($filter = $this->getFilterById($fid))
		{
			if (DB::beginTransaction())
			{
				if ($filter['items'])
				{
					$filter_item_ids = false;
					foreach ($filter['items'] as $item)
					{
						$filter_item_ids[] = $item['id'];
					}
					if ($filter_item_ids)
					{
						$filter_item_ids = implode(',', $filter_item_ids);
						DB::query('DELETE FROM shop_filter_ads WHERE filter_item_id IN('.$filter_item_ids.')');
						DB::query('DELETE FROM shop_filter_items WHERE id IN('.$filter_item_ids.')');
					}
				}
				
				DB::query('DELETE FROM shop_filters WHERE id = '.$fid);
				if (DB::commitTransaction())
				{
					return true;
				}
			}
		}
	}
	
	function removeByCid($cid = false)
	{
		$cid = (int) $cid?$cid:$this->getCid();
		
		if ($all_filters = $this->getAll($cid))
		{
		
			$filter_ids = false;
			$filter_item_ids = false;
			
			foreach ($all_filters as $filter)
			{
				$filter_ids[] = $filter['id'];
				if ($filter['items'])
				{
					foreach ($filter['items'] as $item)
					{
						$filter_item_ids[] = $item['id'];
					}
				}
			}
			
			if (DB::beginTransaction())
			{	
				if ($filter_item_ids)
				{
					$filter_item_ids = implode(',', $filter_item_ids);
					DB::query('DELETE FROM shop_filter_ads WHERE filter_item_id IN('.$filter_item_ids.')');
					DB::query('DELETE FROM shop_filter_items WHERE id IN('.$filter_item_ids.')');
				}
				
				if ($filter_ids)
				{
					$filter_ids = implode(',', $filter_ids);
					DB::query('DELETE FROM shop_filters WHERE id IN('.$filter_ids.')');
				}
				
				if (DB::commitTransaction())
				{
					return true;
				}
			}
		}
	}
	
	function removeItem($item_id = false)
	{
		$item_id = (int) $item_id;
		if ($item_id && DB::beginTransaction())
		{
			DB::query('DELETE FROM shop_filter_ads WHERE filter_item_id = '.$item_id);
			DB::query('DELETE FROM shop_filter_items WHERE id = '.$item_id);
			
			if (DB::commitTransaction())
			{
				return true;
			}
		}
	}
	
	function addFilterItem($data)
	{
		if (gettype($data) == 'array')
		{
			$row = $data;
			
			$new['filter_id'] = $row['fid'];
			$new['item_title'] = $row['item_title'];
			$new['item_value'] = $row['item_value'];
			if (DB::insert('shop_filter_items', $new))
			{
				return true;
			}
		}
		return false;
	}
	
	function changeItem($item_id = false, $item_title, $item_value)
	{
		$item_id = (int) $item_id;
		if ($item_id)
		{
			$new['item_title'] = $item_title;
			$new['item_value'] = $item_value;
			if (DB::update('shop_filter_items', $new, 'id = '.$item_id))
			{
				return true;
			}
		}
		return false;
	}
	
	function filterRequest()
	{
		if ($this->_filter_request !== null)
		{
			if (isset($this->_filter_request['items']) && $this->_filter_request['items'] !== null)
			{
				
				return $this->_filter_request;
			}
		}
		return false;
	}
	
}