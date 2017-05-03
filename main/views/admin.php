<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"><!-- start: index -->
<html xml:lang="ru" lang="ru" xmlns="http://www.w3.org/1999/xhtml">
<head>

<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

<link rel="shortcut icon" href="/favicon.ico">

<meta http-equiv="Content-Script-Type" content="text/javascript">
<link href="/css/shop.css" rel="stylesheet" type="text/css" media="all" />

<script language="javascript" type="text/javascript" src="/jscripts/jquery.js"></script>

<link href="/css/global.css" rel="stylesheet" type="text/css" media="all">
<link href="/css/style.css" rel="stylesheet" type="text/css" media="all">

<link href="/css/admin.css?r=<?=rand();?>" rel="stylesheet" type="text/css" media="all" />

</head>

<body>
<div id="page">

    <div class="header">
        
        <table class="header_table"><tr>
            <td width="20%">
                <a href="/" class="link_logo"><img src="/images/logo.png" /></a>
            </td>
            <td class="header_banner">
                <div class="login_area <?=$session->isAuth()?'logged_in':'';?>">
                <?                
                if($session->isAuth()) {
                ?>
                
                    <div class="userblock">
                        <a href="/forum/usercp.php"><img class="usera_va" src="/<?=$session->user()->get('avatar');?>" title="Мой профиль"></a>
                    </div>
                    <span><a href="/forum/user-<?=$session->user()->get('uid');?>.html"><?=$session->user()->get('username');?></a></span>&nbsp;
                    <a href="/forum/member.php?action=logout&logoutkey=<?=md5($session->user()->get('loginkey'));?>"><img src="/images/icons/logout.png" title="Выход"></a>
                    <br />
                 
                <?
                }
                else {?>
                
                    
                    <script type="text/javascript">
<!--
	lang.username = "Логин";
	lang.password = "Пароль";
	lang.login = "Вход";
	lang.lost_password = "<br /><a href=\"/forum/member.php?action=lostpw\">Забыли пароль?<\/a>";
	lang.register_url = " &mdash; <a href=\"/forum/member.php?action=register\">Зарегистрироваться<\/a>";
	lang.remember_me = "Запомнить меня";
// -->
        </script>
        
                    <span id="quick_login"><a href="http://hi-fidelity-forum.com/forum/member.php?action=login" onclick="MyBB.quickLogin(); return false;">Вход</a>&nbsp;&nbsp;&nbsp;<a href="http://hi-fidelity-forum.com/forum/member.php?action=register">Зарегистрироваться</a></span>

                    
                <?}
                
                ?>
                </div>
            </td>
        </tr></table>
    </div><!-- header -->
    
    <div class="menu_table">
        <div class="menu_block">
            
            <ul class="main_menu_items">
				<li class="active"><a href="/forum">Главная</a>	</li>
                <li><a href="/publish/news">Новости</a>	</li>
                <li><a href="/publish/preview">Обзоры</a>	</li>
                <li><a href="/publish/post">Статьи</a>	</li>
                <li><a href="/publish/music">Музыка</a>	</li>
                <li><a href="/brands">Бренды</a>	</li>
                <li><a href="/catalog">Каталог</a>	</li>
                <li><a href="/archive">Архив</a>	</li>
            </ul>
            
            <div class="right_block">
            
            <?php if ($session->user()->isAdmin()) { ?>
                <a href="http://hi-fidelity-forum.com/forum/admin/index.php"><img src="/images/icons/admin_panel.png" title="Админ панель"></a>
                <a href="http://hi-fidelity-forum.com/forum/modcp.php"><img src="/images/icons/moders.png" title="Модерирование"></a>
            <?php } ?>
               <a href="/forum/search.php?action=getnew"><img src="/images/icons/new_posts.png" title="Новые сообщения" alt="Новые сообщения"></a>

                <a href="/forum/search.php"><img src="/images/icons/search.png" title="Поиск" alt="Поиск"></a>
            <?php if ($session->isAuth()) { ?>
                <a href="/forum/memberlist.php"><img src="/images/icons/users.png" title="Пользователи" alt="Пользователи"></a>
            <?php } ?>
                <a href="/forum/rules/"><img src="/images/icons/rules.png" title="Правила форума" alt="Правила форума"></a>
                <a href="/forum/rules_services.php"><img src="/images/icons/sale.png" title="Платные услуги" alt="Платные услуги"></a>
            <?php if ($session->isAuth()) { ?>
                <a href="http://hi-fidelity-forum.com/forum/private.php"><img src="/images/icons/mail.png" title="Личные сообщения"></a>
            <?php } ?>
                
            </div>
        </div>
    </div> <!-- menu_table -->
    
    <div class="main">
    
  
  <table class="main_table" id="portal" width="100%">
  <tr>
  
  <?php
        //echo Feed::getFeeds();
        if ($core->admin_menu) {
			$admin_menu = $core->admin_menu;
			$options = $core->admin_options;
        ?>
	<td class="feed_cell">
        <div class="feed_block">
          <div class="feed_header">Инструменты</div>
            <div class="admin_menu_block">
            <?php
				
				$act = $request->app()?$request->app():$request->controller();
				$act = trim($act, '/');
				
                foreach ($admin_menu as $key=>$val){
                    if ($options['admin_flag'] == $val['access']) {
                        echo '<a href="/'.Request::$base_url.$val['url'].'" '.($act==$key?' class="active"':'').'>'.$val['title'].'</a>';
                    } elseif (($options['users_access'] || $options['admin_flag']) && !$val['access']) {
                        echo '<a href="/'.Request::$base_url.$val['url'].'" '.($act==$key?' class="active"':'').'>'.$val['title'].'</a>';                         
                    }
                }
            ?>
            </div>
        </div><!-- admin_menu_block-->
	</td>
        <?php
        };
    ?>
  
  <td class="content_cell">
  
	<div class="navigation" style="text-align: left;">
    <!-- start: nav_bit_active -->
	<?php
		$crumbs = $core->breadcrumbs->get_crumbs();
		$cnt = count($crumbs);
		foreach ($crumbs as $key=>$crumb){
			if ($key+1<($cnt)){
	?>
			<a href="/forum/<?=$crumb['url'];?>"><?=$crumb['name'];?> /</a>
	<?php
			} else {
	?>
			<span class="active"><?=$crumb['name'];?></span>
	<?php
			}
		}
	?>    
    <!-- end: nav_bit_active -->
  </div>
  <br />
  
    <?
		if (isset($dump_info))
		{
			echo $dump_info;
		}
	?>
    
    <?=$content;?>
    
  </td>
  
  </tr>
  </table>
    
</div><!-- main -->
    
<div id="footer_page">

<!-- social -->

    <div class="footer_info">

    
    
	</div><!-- footer_info -->

    <div class="bottommenu">
				<div class="float_right">
                <!-- start: footer_languageselect -->
                <div class="float_roght">
                    <a href="/forum/index.php?my_post_key=<?=$session->postKey();?>&language=russian"><img src="/images/icons/rus.png" title="Русский"></a>
                    <a href="/forum/index.php?my_post_key=<?=$session->postKey();?>&language=english"><img src="/images/icons/eng.png" title="English"></a>
                </div>
                <!-- end: footer_languageselect --></div>
				<div>
					<span class="smalltext">
                        <a href="showteam.php">Администрация форума</a> |
                        <a href="stats.php">Статистика форума</a> | 

                        <a href="mailto:info@hi-fidelity-forum.com">Обратная связь</a> | 
                        <a href="#content">Вернуться к содержимому</a> | 
                        <a href="/forum/misc.php?action=help">Справка</a> | 
                        <a href="http://hi-fidelity-forum.com/forum/archive/index.php">Лёгкий режим</a> | 
                        <a href="http://hi-fidelity-forum.com/forum/misc.php?action=syndication">Список RSS</a>
                    </span>
				</div>
			</div>
    </div>
    <div id="copyright">Powered by <a href="/">Hi Fidelity Forum</a> © 2001-2013</div>
</div><!-- #page -->
</body>
</html>