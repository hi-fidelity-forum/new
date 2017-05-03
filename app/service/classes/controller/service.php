<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Service extends Controller
{

	public function __construct(Request $request)
    {	
		parent::__construct($request);
		$this->breadcrumbs->add('Hi-Fi Forum','forum/');
		
		$this->menu = array();
		
		$this->menu[1] = array('tpl'=>'index', 'id'=>1, 'title'=>'Платные услуги');
		$this->menu[2] = array('tpl'=>'banners', 'id'=>2, 'title'=>'Размещение баннеров');
	}

	public function action_index()
    {
	
		$pid = (int) $this->request->param('id');
		
		$pid = $pid?$pid:1;
		
		$this->breadcrumbs->add($this->menu[$pid]['title'],'service/'.$pid);
		
		$content = View::factory('service/'.$this->menu[$pid]['tpl']);
		
		$this->content = View::factory('service')
				->set('active_id',$pid)
				->set('headlist', $this->menu)
				->set('content', $content);
				
    }

} 
