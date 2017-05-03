<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Rules extends Controller
{

	public function __construct(Request $request)
    {	
		parent::__construct($request);
		$this->rules = new Model_Rules();
		$this->breadcrumbs->add('Hi-Fi Forum','forum/');
	}

	public function action_index()
    {
		$headlist = $this->rules->get_headlist();
		
		$id = (int) $this->request->param('id');
		
		$id = ($id && isset($headlist[$id]))?$id:1;
		
		$this->breadcrumbs->add($headlist[$id]['title'],'rules/'.$id);
		
		$content = $this->rules->get_content($id);
		
		$this->content = View::factory('rules')
				->set('active_id',$id)
                ->set('headlist',$headlist)
                ->set('content',$content);
				
    }

} 
