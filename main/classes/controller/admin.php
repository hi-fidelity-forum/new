<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Admin extends Admin {

    

	public function action_index()
	{   
        $this->content = View::factory('admin/index');
            
    }

} // End Welcome
