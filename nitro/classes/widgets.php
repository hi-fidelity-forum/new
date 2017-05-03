<?php 

Class Widgets extends DL
{
	
	
	function __get($obj_name)
	{
		$file_name = $obj_name;
		if ($file = findFile('widgets', $file_name))
		{
			$class_name = 'Widgets_'.$obj_name;
			require_once ($file);
			$obj = new $class_name;
			return $obj;
		}
		parent::__get($obj_name);
	}
	
}