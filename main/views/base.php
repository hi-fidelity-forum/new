<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"><!-- start: index -->
<html xml:lang="ru" lang="ru" xmlns="http://www.w3.org/1999/xhtml">
<head>

<title><?=($request->controller() != 'controller_shop')?'Hi-Fi Forum  - ':'';?><?=$page_title_prefix?$page_title_prefix:'';?></title>

<?=$meta_description?'<meta name="Description" content="'.$meta_description.'" />':'';?>

<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

<link rel="shortcut icon" href="/favicon.ico">

<meta http-equiv="Content-Script-Type" content="text/javascript">

<?php
	if (isset($load_old_javascript) && $load_old_javascript)
	{
?>
<script type="text/javascript" src="/jscripts/prototype.js?ver=1602"></script>
<script type="text/javascript" src="/jscripts/general.js?ver=1603"></script>
<script type="text/javascript" src="/jscripts/popup_menu.js?ver=1602"></script>

<link href="/css/global.css?r=<?=rand();?>" type="text/css" rel="stylesheet">
<link href="/css/style.css?r=<?=rand();?>" rel="stylesheet" type="text/css" media="all">
<link href="/css/shop.css?rev=<?=rand();?>" rel="stylesheet" type="text/css" media="all">
<?php 		
	}
	else 
	{
?>
	<script src='/jscripts/jquery.js?r=<?=rand();?>' charset='utf-8' type='text/javascript'></script>
<?php 		
	}
?>

<link href="/css/global.css?r=<?=rand();?>" type="text/css" rel="stylesheet">
<link href="/css/style.css?rev=<?=rand();?>" rel="stylesheet" type="text/css" media="all">
<link href="/css/shop.css?rev=<?=rand();?>" rel="stylesheet" type="text/css" media="all">

<?php 

	if (Request::app() && is_file(DOCROOT.'css/'.trim(Request::app(),'/').'.css'))
	{
		echo '<link href="/css/'.trim(Request::app(),'/').'.css" rel="stylesheet" type="text/css" media="all">';
	}
?>

<script type="text/javascript" async="" src="https://www.google-analytics.com/ga.js"></script>
<script type="text/javascript">

  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', 'UA-28193037-1']);
  _gaq.push(['_trackPageview']);

  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();
  
  var google_ad_client = "ca-pub-2049749958240566";
  var google_ad_slot = "3476339932";
  var google_ad_width = 200;
  var google_ad_height = 200;


  var cookieDomain = ".hi-fidelity-forum.com";
  var cookiePath = "/";
  var cookiePrefix = "";
  var deleteevent_confirm = "Вы уверены, что хотите удалить это событие?";
  var removeattach_confirm = "Вы уверены что хотите удалить прикрепление из этого сообщения?";
  var loading_text = 'Загрузка...';
  var saving_changes = 'Сохранение изменений...';
  var use_xmlhttprequest = "1";
  var my_post_key = "<?=$session->postKey();?>";
  var imagepath = "images";

</script>

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
                        <a href="/forum/usercp.php"><img class="usera_va" src="https://hi-fidelity-forum.com/<?=$session->user()->get('avatar');?>" title="Мой профиль"></a>
                    </div>
                    <span><a href="<?=Request::$base_url;?>profile/<?=$session->user()->get('uid');?>"><?=$session->user()->get('username');?></a></span>&nbsp;
                    <a href="<?=Request::$base_url;?>member/logout"><img src="/images/icons/logout.png" title="Выход"></a>
                    <br />
                 
                <?
                }
                else {?>
				
					
					<script type="text/javascript">
						lang.username = "Логин";
						lang.password = "Пароль";
						lang.login = "Вход";
						lang.lost_password = "<br /><a href=\"/forum/member.php?action=lostpw\">Забыли пароль?<\/a>";
						lang.register_url = " &mdash; <a href=\"/forum/member.php?action=register\">Зарегистрироваться<\/a>";
						lang.remember_me = "Запомнить меня";
					</script>

					<span id="quick_login">
						<a href="/forum/member.php?action=login" onclick="MyBB.quickLogin(); return false;">Вход</a>&nbsp;&nbsp;&nbsp;<a href="/forum/member.php?action=register">Зарегистрироваться</a>
					</span>
					
                <?}
                
                ?>
                </div>
            </td>
        </tr></table>
    </div><!-- header -->
    
    <div class="menu_table">
        <div class="menu_block">
            
            <ul class="main_menu_items">
				<li <?=(($request->controller()=='controller_home')?' class="active"':'');?>><a href="/">Главная</a>	</li>
                <li><a href="/publish/news">Новости</a>	</li>
                <li><a href="/publish/preview">Обзоры</a>	</li>
                <li><a href="/publish/post">Статьи</a>	</li>
                <li><a href="/publish/music">Музыка</a>	</li>
                <li><a href="/brands">Бренды</a>	</li>
                <li><a href="/catalog">Каталог</a>	</li>
                <li><a href="/archive">Архив</a>	</li>
            </ul>
            
            <div class="right_block">
            
            <?php if ($session->isAuth() && $session->user()->isAdmin()) { ?>
                    <a href="/forum/admin/index.php"><img src="/images/icons/admin_panel.png" title="Админ панель"></a>
            <?php } 
                  if ($session->isAuth() && $session->user()->isAdmin()) { ?>
                <a href="/forum/modcp.php"><img src="/images/icons/moders.png" title="Модерирование"></a>
            <?php } ?>
               <a href="/forum/search.php?action=getnew"><img src="/images/icons/new_posts.png" title="Новые сообщения" alt="Новые сообщения"></a>

                <a href="/forum/search.php"><img src="/images/icons/search.png" title="Поиск" alt="Поиск"></a>
            <?php if ($session->isAuth()) { ?>
                <a href="/forum/memberlist.php"><img src="/images/icons/users.png" title="Пользователи" alt="Пользователи"></a>
            <?php } ?>
                <a href="/rules/"><img src="/images/icons/rules.png" title="Правила форума" alt="Правила форума"></a>
                <a href="/service/"><img src="/images/icons/sale.png" title="Платные услуги" alt="Платные услуги"></a>
            <?php if ($session->isAuth()) { ?>
                <a href="/forum/private.php"><img src="/images/icons/mail.png" title="Личные сообщения"></a>
            <?php } ?>
                
            </div>
        </div>
    </div> <!-- menu_table -->
	
	<?php //echo '<div class="red_alert"><span><b>Внимание!!!</b> 30.03.2017 c 2:00 до 4:00 будут проводиться технические работы и сайт будет недоступен</span></div>'; ?>
	<?php //echo '<div class="red_alert" style="border: 1px solid green; background: #c8fcd1 !important;"><span><b style="color: green;">Внимание!!!</b> Возобновлен прием платежей за размещение платных объявлений в разделе "Куплю-Продам"</span></div>'; ?>
    
    <div class="main">
    
  <?=$notice->get_all();?>
  <?php if (isset($top_publish)) echo $top_publish; ?>
  
  <div class="navigation" style="text-align: left;">
    <!-- start: nav_bit_active -->
	<?php
		$crumbs = $breadcrumbs->get_crumbs();
		$cnt = count($crumbs);
		foreach ($crumbs as $key=>$crumb){
			if ($key+1<($cnt)){
	?>
			<a href="/<?=$crumb['url'];?>"><?=$crumb['name'];?> /</a>
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
  
  <table class="main_table simple_main_table" id="portal" width="100%">

  <tr>
  
  <td class="content_cell">
    <!-- ?=$dump_info;? -->
    
    <?=$content;?>
    
  </td>
  
  <td width="210" valign="top" class="right_sidebar">
  
  <?php     
        echo file_get_contents(DOCROOT.'tpl/forum_rightside.tpl');
  ?>
  </td>
  
  </tr>
  </table>
    
</div><!-- main -->
    
<div id="footer_page">

<!-- social -->

    <div class="footer_info">

    <?php     
        echo file_get_contents(DOCROOT.'tpl/forum_bottom.tpl');
    ?>
    
</div>
<!-- footer_info -->

    <div class="bottommenu">
				
					<span class="smalltext">
                        <a href="/forum/showteam.php">Администрация форума</a> |
                        <a href="/forum/stats.php">Статистика форума</a> | 

                        <a href="mailto:info@hi-fidelity-forum.com">Обратная связь</a> | 
                        <a href="#content">Вернуться к содержимому</a> | 
                        <a href="/forum/misc.php?action=help">Справка</a> | 
                        <a href="/forum/archive/index.php">Лёгкий режим</a> | 
                        <a href="/forum/misc.php?action=syndication">Список RSS</a>
                    </span>
				</div>
			</div>
	<div id="copyright">Powered by <a href="/">Hi Fidelity Forum</a> © 2001-2017</div>
    </div>
</div><!-- #page -->

</body>
</html>
<?php

if (defined('DEBUG_MODE') && DEBUG_MODE)
{
	$online_users = $session->getOnlineUsers();
	var_export(count($online_users)); echo ', ';
	var_export($session->getCountGuests());
}

?>