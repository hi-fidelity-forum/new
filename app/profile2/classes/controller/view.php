<?php defined('SYSPATH') or die('No direct access allowed.');

// Описание класса
class Controller_View extends Profile
{
	
	function action_index()
    {
		$uid = $this->user->get('uid');
		$red = '/'.$this->request->app().$uid;
		$this->request->redirect($red);
		
    }
	   
}