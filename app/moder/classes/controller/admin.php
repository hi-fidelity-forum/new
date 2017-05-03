<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Admin extends Admin 
{

	public function __construct(Request $request)
    {	
		parent::__construct($request);
		$this->breadcrumbs->add('Панель премодерации', 'admin/moder');
	}

	public function action_index()
	{   
		$notice = new Notice();
		
		$mod_events = $notice->getModEvents();
		
		$this->content = View::factory('index')->set('events', $mod_events);
		
    }
	
	public function action_reputations()
	{   
	
		$rep_class = new Model_Reputation();
		
		if ($_GET && isset($_GET['delete']) && ($rid = (int) $_GET['delete']))
		{
			if ($rep_class->remove($rid))
			{
				$this->request->redirect('/admin/moder/reputations/');
			}
			else 
			{
				$this->content .= 'Произошла ошибка, не удалось удалить отзыв';
			}
		}
		
		if ($_GET && isset($_GET['enable']) && ($rid = (int) $_GET['enable']))
		{
			if ($rep_class->enable($rid))
			{
				$this->request->redirect('/admin/moder/reputations/');
			}
		}
		
		$reputations = $rep_class->getDisabled();
				
		$this->content .= View::factory('reputations')->set('reputations', $reputations);
		
    }
	
	public function action_ads()
	{   
		$shop = new Model_Shop();
		
		$ads = $shop->getUnApprovedAds();
		
		$this->content = View::factory('ads')->set('ads', $ads);
		
    }
	

} // End