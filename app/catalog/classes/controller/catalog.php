<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Catalog extends Controller
{

	public function __construct(Request $request)
    {	
		parent::__construct($request);
		$this->breadcrumbs->add('Hi-Fi Forum','');
	}

	public function action_index()
    {
		$this->content = 'Раздел на стадии разработки';
				
    }

} 
