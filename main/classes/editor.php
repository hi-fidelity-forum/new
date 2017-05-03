<?php defined('SYSPATH') or die('No direct script access.');

class Editor extends DL
{

    public function __construct()
	{    
		
        return $this;	
        
    }
    
	function getEditorBox()
	{
		$editor = false;
		
		$smilies = DB::query('SELECT * FROM mybb_smilies WHERE showclickable = 1 ORDER BY disporder ASC')->fetchAll();
		
		$editor = View::factory('editor')->set('smilies', $smilies);
		
		return $editor;
	}
    
}