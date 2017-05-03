<?php

return array
(

	'profile' => array(
		'uri' => 'profile/<id>(/<controller>(/<action>(/<inx>)))',
		'default' => array('app' => 'profile', 'controller' => 'index', 'action' => 'index')
	),
	
	'profile2' => array(
		'uri' => 'profile2/<id>(/<controller>(/<action>(/<inx>)))',
		'default' => array('app' => 'profile2', 'controller' => 'index', 'action' => 'index')
	),
	
	'shop' => array(
		'uri' => 'shop(/<action>(/<id>(/<filters>)))',
		'default' => array('controller' => 'shop')
	),
	
	'admin' => array(
		'uri' => 'admin(/<app>(/<action>(/<id>)))',
		'default' => array('controller' => 'admin')
	),
	
	'forum' => array(
		'uri' => 'forum(/<action>(/<id>))',
		'default' => array('controller' => 'forum', 'action'=>'index')
	),
	
		
	'forum_index' => array(
		'uri' => 'forum/index.php',
		'default' => array('app' => 'forum', 'controller' => 'forum', 'action'=>'index')
	),
	
		
	'forum2' => array(
		'uri' => 'forum2/index.php',
		'default' => array('controller' => 'forum2', 'action'=>'index')
	),
	
	'forum_id' => array(
		'uri' => 'forum/forum-(<id>).html',
		'default' => array('app'=> 'forum', 'controller' => 'forum', 'action' => 'forum')
	),
	
	'publish' => array(
		'uri' => 'publish(/<action>(/<id>))',
		'default' => array('app' => 'publish', 'controller' => 'publish', 'action'=>'index')
	),
	
	'brands' => array(
		'uri' => 'brands(/<action>(/<id>))',
		'default' => array('app' => 'brands', 'controller' => 'brands')
	),
	
	'member' => array(
		'uri' => 'member(/<action>(/<id>))',
		'default' => array('app' => 'member', 'controller' => 'member', 'action'=>'login')
	),
	
	/*
	'profile' => array(
		'uri' => 'profile(/<id>(/<action>))',
		'default' => array('app'=> 'profile', 'controller' => 'profile', 'action'=>'index')
	),
	*/
	
	'users' => array(
		'uri' => 'users(/<action>(/<id>))',
		'default' => array('app'=> 'users', 'controller' => 'users', 'action'=>'index')
	),
	
	'catalog' => array(
		'uri' => 'catalog(/<id>)',
		'default' => array('app' => 'catalog', 'controller' => 'catalog', 'action' => 'index', 'id' => 1)
	),
	
	'rules' => array(
		'uri' => 'rules(/<id>)',
		'default' => array('app' => 'rules', 'controller'    => 'rules', 'action'    => 'index', 'id' => 1)
	),
	
	'service' => array(
		'uri' => 'service(/<id>)',
		'default' => array('app' => 'service', 'controller'    => 'service', 'action'    => 'index', 'id' => 1)
	),
	
	'instruction' => array(
		'uri' => 'instruction(/<action>(/<id>))',
		'default' => array('app' => 'instruction', 'controller' => 'instruction', 'action' => 'index', 'id' => 1)
	),
	
	
	'cli' => array(
		'uri' => 'cli(/<action>(/<id>))',
		'default' => array('app' => 'cli', 'controller' => 'cli')
	),
	
	
	'index' => array(
		'uri' => '(<controller>(/<action>))',
		'default' => array('controller' => 'home', 'action'=>'index')
	),

);