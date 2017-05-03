<?php

Class Front extends DL
{
	
	function __construct()
	{
		
		DB::init();
		mark_debug_time('DB init');
		
		#$widgets = new Widgets();
		#DL::addGlobalObject('Widgets', $widgets);
			
		if ($session = new Session()) 
			DL::addGlobalObject('session', $session);
		else 
			throw new Exception('System load Session');
		mark_debug_time('Init Session');
		
		if ($br = new Breadcrumbs())
			DL::addGlobalObject('breadcrumbs', $br);
		if ($nt = new Notice())
			DL::addGlobalObject('notice', $nt);
		
		mark_debug_time('Init BreadCrumbs');
			
	}
	
}