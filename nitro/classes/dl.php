<?php defined('SYSPATH') or die('No direct script access.');

Class DL
{

	private static $_vars = array();
	
	static function addGlobalObject($obj_name, $obj)
	{
		$obj_name = (string) $obj_name;
		
		if (isset(DL::$_vars[$obj_name]))
		{
			throw new Exception('Non used object "'.$obj_name.'" because is already');
		} else {
			DL::$_vars[$obj_name] = $obj;
			View::set_global(DL::$_vars);
		}
	}
	
	function __get($obj_name)
	{
		$obj_name = (string) $obj_name;
		if (isset(DL::$_vars[$obj_name]))
			return DL::$_vars[$obj_name];
		else
			throw new NException('Undefined variable "'.$obj_name.'"');
	}
	
}