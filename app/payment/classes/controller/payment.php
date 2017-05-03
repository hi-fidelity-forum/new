<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Payment extends Simple
{

	public function __construct(Request $request)
    {	
		parent::__construct($request);
		$this->breadcrumbs->add('Hi-Fi Forum','forum/')->add('Платные услуги','payment');
	}

	public function action_index()
	{

		$this->content = 'Index Payment Page';
	}
	
	public function action_service()
	{

		$this->content = View::factory('payment');
	}

} 
