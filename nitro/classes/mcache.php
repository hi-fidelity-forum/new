<?php defined('SYSPATH') or die('No direct script access.');

Class MCache {

    private static $_initial = false;
	private static $_memcache = false;

	
	public static function init()
	{
		if (!self::$_initial)
		{
			//return self::$_memcache = FALSE;
			if (class_exists('Memcache'))
			{
				$m = new Memcache;
				$m->connect('localhost', 11211) or die ("Could not connect");				
				self::$_memcache = $m;
				self::$_initial = TRUE;
				return true;
			}
			else 
			{
				self::$_memcache = FALSE;
			}
			
		}
		else {
			if (self::$_memcache)
				return true;
			else
				return false;
		}
		
		return false;
		
	}
	
	public static function set($key, $value, $time, $bool = false)
	{
		if (self::init())
		{	
			return self::$_memcache->set(md5($key), $value, $bool, $time);
		}
		return false;
	}
	
	public static function get($key)
	{
		if (self::init())
		{
			if ($res = self::$_memcache->get(md5($key)))
				return $res;
			else 
				return false;
		}
		return false;
	}
	
}