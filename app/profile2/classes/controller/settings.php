<?php defined('SYSPATH') or die('No direct access allowed.');

// Описание класса
class Controller_Settings extends Profile
{
	
	function __construct($r)
	{
		parent::__construct($r);
		
		if ($this->session->isAuth() && ($user_id = (int) $this->request->param('id')))
		{
			if (!(($user_id == $this->session->user()->get('uid')) || $this->session->user()->isAdmin()))
			{
				$this->request->redirect(Request::$base_url.$this->request->app().$this->user->get('uid').'/');
			}
		}
		else 
		{
			$this->request->redirect(Request::$base_url.$this->request->app().$this->user->get('uid').'/');
		}
	}
	
	function action_index()
    {
		
		$this->page_title_prefix = $this->page_title_prefix.' - Настройки';
		$this->breadcrumbs->add('Настройки',$this->request->controller_uri().'/settings');
		
		if ($this->session->isAuth() && ($user_id = (int) $this->request->param('id')))
		{
			if (($user_id == $this->session->user()->get('uid')) || $this->session->user()->isAdmin())
			{
				$change_flag = false;
				
				if ($_POST)
				{
					$post_data = $_POST;
					unset($post_data['save']);
					$new_data = false;
					foreach ($post_data as $key=>$item)
					{
						if ($this->user->get($key) !== null)
						{
							if ($key != 'timezone')
							{
								if ($item == 'on')
									$value = '1';
								else
									$value = $item;
							}
							else 
							{
								if (($item >= -12) && ($item <= 12))
								{
									$value = $item;
								}
								else
									$value = null;
							}
							if ($value !== null && $this->user->get($key) != $value)
							{
								$new_data[$key] = $value;
							}
						}
					}
					
					if ($new_data && $this->user->changeInfo($new_data))
					{
						$uid = $this->user->get('uid');
						$this->user = new Model_UserInfo($uid);
						$change_flag = true;
					}
					
				}
				
				$this->content = View::factory('settings/edit')
					->set('user', $this->user)
					->set('change_flag', $change_flag);
			}
		}
		
    }
	
	function action_change_pass()
	{
		if ($this->session->isAuth() && ($user_id = (int) $this->request->param('id')))
		{
			if (($user_id == $this->session->user()->get('uid')) || $this->session->user()->isAdmin())
			{
				$change_flag = false;
				
				
				
			}
		}
	}
    
}