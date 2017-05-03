<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Admin extends Admin {

    public function __construct($r)
    {
        parent::__construct($r);
        $this->rules = new Model_Rules();
    }

	public function action_index()
	{   
		$rules = $this->rules->get_all();
		
		$this->content = View::factory('admin/rules_index')
            ->set('rules',$rules);
            
    }
    
    public function action_edit()
	{
        $id = (int) $this->request->param('id');
        
		$tpl = '';
		
        if ($_POST){
            //$this->debug($_POST);
            
            $res = $this->rules->change($id, $_POST);
            
            if (is_array($res)){
                $this->debug($res);
            } else {
                $tpl .= '<div class="goot_edit">Edit data is ok</div>';
            }
        }
		
		$rules = $this->rules->get_id($id);
		
		$tpl .= View::factory('admin/rules_edit')
            ->set('rules',$rules);
		
		$this->content = $tpl;
            
    }

} // End Welcome
