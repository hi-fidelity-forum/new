<?php defined('SYSPATH') or die('No direct access allowed.');

// Описание класса
class Controller_Ads extends Profile
{	
	
	function action_index()
    {	
	
		$this->breadcrumbs->add('Объявления', '/index');		
		
		$this->shop = new Model_Shop();
		$uid = $this->user->get('uid');
		$counts = $this->shop->getAdsCountByUid($uid);
		//var_export($counts);
		
		if ($ads = $this->shop->getUserAds($uid, 1))
		{
			
			if ($this->session->isAuth() && $this->session->user()->get('uid') == $this->user->get('uid'))
			{
				$ads->execute();
				$page = View::factory('shop/ad_list_main')->set('ad_list',$ads)->set('user', $this->user)->set('counts', $counts);
			}
			else 
			{
				
				$ads->execute();
				if ($ads->getTotalCount()>0)
				{
					$ad_list_items = View::factory('shop/ad_list_items')->set('ad_list',$ads)->set('user', $this->user);
				}
				else 
				{
					$ad_list_items = false;
				}
				$page = View::factory('shop/ad_list_user')->set('ad_list_items',$ad_list_items)->set('ads', $ads)->set('user', $this->user)->set('counts', $counts);
			}
			
		} else {
			$page = 'Обявлений не найдено';
		}
		
		$this->content = $page;
		
    }

	function action_unactive()
    {	
	
		$this->breadcrumbs->add('Объявления', '/index');		
		
		$this->shop = new Model_Shop();
		$uid = $this->user->get('uid');
		$counts = $this->shop->getAdsCountByUid($uid);
		
		if ($ads = $this->shop->getUserAds($uid, 0))
		{
			if ($this->session->isAuth() && $this->session->user()->get('uid') == $this->user->get('uid'))
			{
				$ads->execute();
				$page = View::factory('shop/ad_list_main')->set('ad_list',$ads)->set('user', $this->user)->set('counts', $counts);
			}
			else 
			{
				$ads->execute();
				$ad_list_items = View::factory('shop/ad_list_items')->set('ad_list',$ads);
				$page = View::factory('shop/ad_list_user')->set('ad_list_items',$ad_list_items)->set('user', $this->user)->set('counts', $counts);
			}
			
		} else {
			$page = 'Обявлений не найдено';
		}
		
		$this->content = $page;
		
    }
	
	function action_unapprove()
    {	
	
		$this->breadcrumbs->add('Объявления', '/index');		
		
		$this->shop = new Model_Shop();
		$uid = $this->user->get('uid');
		$counts = $this->shop->getAdsCountByUid($uid);
		
		if ($ads = $this->shop->getUserAds($uid, 3))
		{
			if ($this->session->isAuth() && $this->session->user()->get('uid') == $this->user->get('uid'))
			{
				$ads->execute();
				$page = View::factory('shop/ad_list_main')->set('ad_list',$ads)->set('user', $this->user)->set('counts', $counts);
			}
			else 
			{
				$ads->execute();
				$ad_list_items = View::factory('shop/ad_list_items')->set('ad_list',$ads);
				$page = View::factory('shop/ad_list_user')->set('ad_list_items',$ad_list_items)->set('user', $this->user)->set('counts', $counts);
			}
			
		} else {
			$page = 'Обявлений не найдено';
		}
		
		$this->content = $page;
		
    }
	
	function action_inmoder()
    {	
	
		$this->breadcrumbs->add('Объявления', '/index');		
		
		$this->shop = new Model_Shop();
		$uid = $this->user->get('uid');
		$counts = $this->shop->getAdsCountByUid($uid);
		
		if ($ads = $this->shop->getUserAds($uid, 2))
		{
			if ($this->session->isAuth() && $this->session->user()->get('uid') == $this->user->get('uid'))
			{
				$ads->execute();
				$page = View::factory('shop/ad_list_main')->set('ad_list',$ads)->set('user', $this->user)->set('counts', $counts);
			}
			else 
			{
				$ads->execute();
				$ad_list_items = View::factory('shop/ad_list_items')->set('ad_list',$ads);
				$page = View::factory('shop/ad_list_user')->set('ad_list_items',$ad_list_items)->set('user', $this->user)->set('counts', $counts);
			}
			
		} else {
			$page = 'Обявлений не найдено';
		}
		
		$this->content = $page;
		
    }
	   
}