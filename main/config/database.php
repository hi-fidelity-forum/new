<?php defined('SYSPATH') OR die('No direct access allowed.');

return array
(

	'use'=>'old',

	'default' => array
	(
		'type'       => 'mysql',
		'hostname'   => 'localhost',
		'database'   => 'hifidelity',
		'username'   => 'u_hifidelity',
		'password'   => 'Ve7nmY',
		'table_prefix' => '',
		'charset'      => 'utf8',
		'caching'      => FALSE,
		'profiling'    => TRUE,
	),
	
	'old' => array
	(
		'type'       => 'mysql',
		'hostname'   => 'localhost',
		'database'   => 'hififoru',
		'username'   => 'u_hififoru',
		'password'   => '0ClP6m3F',
		'table_prefix' => '',
		'charset'      => 'utf8',
		'caching'      => FALSE,
		'profiling'    => TRUE,
	),
);