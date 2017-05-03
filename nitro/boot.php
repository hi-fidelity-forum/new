<?php

Class Boot 
{

	public function load()
	{

		if ($request = new Request())
		{
			
			mark_debug_time('Request init');
			
			set_exception_handler(array('NException', 'handler'));
			set_error_handler('error_dispetcher');
			
			DL::addGlobalObject('request', $request);
			
			echo $request->execute()->body();
			
			mark_debug_time('End! $request->execute()');			
		} 
		else
		{
			return false;	
		}
	}
	
	public function __construct()
	{
		if (version_compare(phpversion(), '5.3.0', '<') == true) { die ('>PHP5.3 Only'); }
		
		if (!defined('EXT')) define('EXT', '.php');
		if (!defined('DS')) define('DS', DIRECTORY_SEPARATOR);

		if (isset($_GET['debug_mode']) && $_GET['debug_mode'] == '1')
		{
			if (!defined('DEBUG_MODE')) define('DEBUG_MODE', TRUE);
		} else {
			if (!defined('DEBUG_MODE')) define('DEBUG_MODE', FALSE);
		}
		
		mark_debug_time('Start boot');
		
		define('SYSPATH', realpath(dirname(__FILE__)).DS);
		
		$time = time();		
		define ('TIME_NOW', $time);
		
		spl_autoload_register('loadClass');
		
	}
	
	public function showErrors($se = false)
	{
		$se = (boolean) $se;
		if ($se === true) {
			define('DEBUG', TRUE);
			error_reporting(E_ALL | E_STRICT);
			ini_set('display_errors', 1);
		}
		else
		{
			define('DEBUG', FALSE);
			error_reporting(FALSE);
			ini_set('display_errors', false);
		}
	}
	
	
	public function __destruct()
	{
		show_debug_time();
	}
	
}

function findFile($dir, $file, $app = false)
{    
	$path = $dir.DS.$file.EXT;
	$wdir = (class_exists('Request') && isset(Request::$_app))?Request::$_app:false;
	
	if (!empty($wdir) && is_file(APPPATH.$wdir.$path))
	{
		return APPPATH.$wdir.$path;
	}
	elseif (is_file(MAINPATH.$path))
	{
		return MAINPATH.$path;
	}
	elseif (is_file(SYSPATH.$path))
	{
		return SYSPATH.$path;
	}
	return false;

}

function loadClass($class_name)
{
	// Загрузка классов «на лету»
	try
	{
		$filename = str_replace('_','/',strtolower($class_name));
		
		if ($file = findFile('classes',$filename))
		{
			return require_once ($file);
		}
		return false;
	}
	catch (Exception $e)
	{
		throw new NException($e);
	}
}

function loadConfig($name = null)
{
	static $_configs = array();
	
	$name = (string) $name;
	if (!$name)
		return false;
	elseif (isset($_configs[$name]))
		return $_configs[$name];
	elseif ($file = findFile('config',$name))	
	{
		$res = include($file);
		$_configs[$name] = $res;
		return $res;
	} else {
		throw new NException('Not find config file "'.$name.'"');
	}
}

function debug($value){
	GLOBAL $dump_info;
	$dump_info .= '<pre>'.var_export($value, TRUE).'</pre>';
}

function show_debug_time()
{
	if (DEBUG_MODE) 
	{
		GLOBAL $dump_times;
		
		if (count($dump_times)>1)
		{
			$dmp = '';
			
			$rest = count($dump_times);
			$dmp .= 'All run times: '.($dump_times[$rest-1]['time'] - $dump_times[0]['time']). '<br />';
			$dmp .= '<table style="color: white;">';
			
			foreach ($dump_times as $key => $val) 
			{
				$dmp .= '<tr><td align="right">'.$val['title'].': </td><td>'.$val['relative']."</td></tr>";
			}
			
			echo '<hr />';
			echo '<pre style="color: white; background: black; text-align: left;">'.$dmp.'</pre>';
		}
		
		var_export(isset(DB::$profile_list)?DB::$profile_list:'');
	}
}

function mark_debug_time($str = false)
{	
	GLOBAL $dump_times;	
	$time = microtime(true);	
	$count = count($dump_times);
	
	if ($str != false) $mark = (string) $str; else $mark = $count+1;	
	
	$rel = $count>0?($time - $dump_times[$count-1]['time']):false;
	$dump_times[$count] = array('title' => $mark, 'time' => $time, 'relative' => $rel);
}

function error_dispetcher($code, $error, $file = NULL, $line = NULL)
{
	if (error_reporting() & $code)
		throw new ErrorException($error, $code, 0, $file, $line);
	return TRUE;
}

?>