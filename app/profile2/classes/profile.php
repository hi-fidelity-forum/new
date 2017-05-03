<?php defined('SYSPATH') or die('No direct access allowed.');

// Описание класса
class Profile extends Controller
{
	protected $user = false;
	
	public $main_mode = false;
	
	function __construct($r)
	{
		parent::__construct($r);
		
		$this->breadcrumbs->add('Hi-Fi Forum','');
		
		if ($param_uid = (int) $this->request->param('id'))
		{
			$session_uid = $this->session->user()->get('uid');
				
			if ($user = new Model_UserInfo($param_uid))
			{	
				if ($user->get('uid') != 0)
				{
					$this->user = $user;
					
				}
				else {
					$this->content = 'Вы не вошли на портал, или не зарегистрированны.';
					return false;
				}
			}
			
			if ($session_uid == $param_uid)
			{
				$this->main_mode = true;
				$tl = 'Мой профиль';
			}
			else 
			{
				$this->main_mode = false;
				$tl = 'Профиль пользователя '.$this->user->stylizedUserName();
			}
			
			$this->page_title_prefix = $tl;
			$uid = $this->user->get('uid');
			$this->breadcrumbs->add($tl,'profile/'.$uid);
		
		}
		
	}
	
	function after()
	{
		if (!$this->request->isAjax())
		{
			$this->content = View::factory('profile')
					->set('user', $this->user)
					->set('content', $this->content)
					->set('main_mode', $this->main_mode);
		}
		parent::after();
	}
    
}