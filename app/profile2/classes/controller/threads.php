<?php defined('SYSPATH') or die('No direct access allowed.');

// Описание класса
class Controller_Threads extends Profile
{
	
	function action_index()
    {
		if ($this->user)
		{
			
			$threads = new Model_Forum_Threads();
			
			if ($_POST)
			{
				
				if (isset($_POST['action']))
				{
					$action = (string) $_POST['action'];
					switch ($action) {
						case "movetosafe":
							if (isset($_POST['moditems'])){
								$moditems = $_POST['moditems'];
								$moditems = explode(',', $moditems);
								if ($threads->moveThreadsToFid($moditems, 87))
								{
									//$this->request->redirect('/'.$this->request->uri());
								}
							}
							
						break;
						case "moveto":
							if (isset($_POST['moditems']) && isset($_POST['fid'])){
								$fid = (int) $_POST['fid'];
								$moditems = $_POST['moditems'];
								$moditems = explode(',', $moditems);
								
								if ($threads->moveThreadsToFid($moditems, $fid))
								{
									$this->request->redirect('/'.$this->request->uri());
								}
							}
						break;
						default:
						break;
					}
				}
			}
			
			$this->page_title_prefix = $this->page_title_prefix.' - Темы';
			$this->breadcrumbs->add('Темы',$this->request->controller_uri().'/threads');
			
			if ($threads = $this->user->getUserThreads())
			{
				$threads->execute();
				$this->content = View::factory('threads/index')->set('threads', $threads)->set('user', $this->user);
			}
			
		}
    }
	   
}