<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Instruction extends Controller 
{
	function __construct($r)
	{
		parent::__construct($r);
		
		$this->breadcrumbs->add('Публикации','publish/');
		
	}

	public function action_index()
	{    
        $this->page_title_prefix = 'Инструкции';
        
        $this->content = View::factory('instruction_index');
        
        //
    }
    
    public function action_editor()
	{    
        $this->page_title_prefix = 'Инструкции';
		
		$this->breadcrumbs->add('Инструкции','instruction/editor');
        
        $this->content = View::factory('instruction_editor');
        
        //
    }
    
} // End instructions
