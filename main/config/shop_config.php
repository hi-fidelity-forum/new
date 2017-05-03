<?php defined('SYSPATH') or die('No direct access allowed.');

return array(

	'table_prefix' => 'shop_',
	'admin_groups' => 4,
	'shop_form_default' => array(
		'is_items' => false,
	),
	'ad_page_limit'=>20,
	'filter_name' => 'filters',
	'user_ads_counts'=>array(
		1=>array('title'=>'Гости', 'count'=>0, 'up_time'=>0, 'is_paid'=>false),
		7=>array('title'=>'Banned', 'count'=>0, 'up_time'=>0, 'is_paid'=>false),
		5=>array('title'=>'Awaiting Activation', 'count'=>0, 'up_time'=>0, 'is_paid'=>false),
		2=>array('title'=>'Пользователь', 'count'=>1, 'up_time'=>30, 'is_paid'=>false),
		14=>array('title'=>'Участник', 'count'=>1, 'up_time'=>30, 'is_paid'=>false),
		13=>array('title'=>'Постоялец', 'count'=>2, 'up_time'=>14, 'is_paid'=>false),
		12=>array('title'=>'Старожил', 'count'=>2, 'up_time'=>14, 'is_paid'=>false),
		11=>array('title'=>'Ветеран', 'count'=>3, 'up_time'=>7, 'is_paid'=>false),
		19=>array('title'=>'Редактор', 'count'=>3, 'up_time'=>7, 'is_paid'=>false),
		20=>array('title'=>'Мастер', 'count'=>25, 'up_time'=>3, 'is_paid'=>true),
		9=>array('title'=>'Продавец', 'count'=>50, 'up_time'=>3, 'is_paid'=>true),
		17=>array('title'=>'Дилер', 'count'=>50, 'up_time'=>1, 'is_paid'=>true),
		4=>array('title'=>'Администратор', 'count'=>500, 'up_time'=>1, 'is_paid'=>true),
	),
	'paid_groups'=>array(
		20=>array('can_new'=>false, 'no_limit'=>false, 'amount'=>200),
		9=>array('can_new'=>true, 'no_limit'=>false, 'amount'=>200),
		17=>array('can_new'=>true, 'no_limit'=>true, 'amount'=>400),
	),
	'amounts'=>array(
		9=>array(20=>100, 50=>200, 100=>400, 150=>500, 200=>600, 250=>700, 300=>800),
		17=>array(50=>400, 100=>600, 150=>700, 200=>800, 250=>900, 300=>1000),
		20=>array(20=>200, 50=>400, 100=>600),
	),	
	'currency_codes'=>array(
		'prefix'=>array(
			'USD'=>1,
			'EUR'=>2,
			'UAH'=>3,
			'RUB'=>4,
		),
		'names'=>array(
			1=>'USD',
			2=>'EUR',
			3=>'UAH',
			4=>'RUB',
		),
	),
	'default_currency'=>1,
);
