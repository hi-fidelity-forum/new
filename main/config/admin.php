<?php defined('SYSPATH') or die('No direct access allowed.');

return array(

    'admin_groups' => '4',
    'users_access' => array('15206','31351'),
    'admin_menu' => array (
        'index' => array('title'=>'Главная', 'url'=>'/index/', 'access'=>'0'),
        'moder' => array('title'=>'Премодерация', 'url'=>'/moder/', 'access'=>'1'),
        'rules' => array('title'=>'Правила', 'url'=>'/rules/', 'access'=>'1'),
        'publish' => array('title' => 'Публикации', 'url'=>'/publish/', 'access'=>'1'),
        'adv' => array('title' => 'Реклама', 'url'=>'/adv/', 'access'=>'0'),
        'stats' => array('title' => 'Статистика', 'url'=>'/stats/', 'access'=>'0'),
        'groups' => array('title' => 'Группы', 'url'=>'/groups/', 'access'=>'0'),
        'clients' => array('title' => 'Клиенты', 'url'=>'/clients/', 'access'=>'0'),
        'shop' => array('title' => 'Объявления', 'url'=>'/shop/', 'access'=>'1'),
    ),
    'tpl_path' => 'tpl',
);
