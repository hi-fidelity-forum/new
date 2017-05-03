<?php defined('SYSPATH') or die('No direct script access.');

class Breadcrumbs {

	static $bread = array();
    
    function add($name = '', $url='')
	{    
		$name = (string) $name;
		$url = (string) $url;
		Breadcrumbs::$bread[] = array('name'=>$name, 'url'=>$url);
        return $this;
    }
	
	function set($id = 0, $name = '', $url='')
	{    
		$id = (int) $id;
		$name = (string) $name;
		$url = (string) $url;
		if (isset(Breadcrumbs::$bread[$id])){
			Breadcrumbs::$bread[$id] = array('name'=>$name, 'url'=>$url);
		}
        return $this;
    }
	
	function get_crumbs()
	{
		$bread = Breadcrumbs::$bread;
		return $bread;
	}
    
}