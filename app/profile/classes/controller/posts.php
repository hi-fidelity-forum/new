<?php defined('SYSPATH') or die('No direct access allowed.');

// Описание класса
class Controller_Posts extends Profile
{
	
	function action_index()
    {
		
		if ($_POST)
		{
			if (isset($_POST['action']))
			{
				$action = (string) $_POST['action'];
				switch ($action) 
				{
					case "remove":
						if (isset($_POST['moditems']))
						{
							
							$posts = new Model_Forum_Posts();
							
							$moditems = $_POST['moditems'];
							$moditems = explode(',', $moditems);
							
							if ($posts->remove($moditems))
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
			
			
		$this->page_title_prefix = $this->page_title_prefix.' - Сообщения';
		$this->breadcrumbs->add('Сообщения',$this->request->controller_uri().'/posts');
		
		if ($posts = $this->user->getUserPosts())
		{
			$posts->execute();
			$this->content = View::factory('posts/index')->set('posts', $posts)->set('user', $this->user);
		}
    }
    
}