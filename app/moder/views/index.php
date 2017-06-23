<div class="admin_page_menu">
	<a href="/<?=Request::$base_url;?>/moder/ads/" class="button">Объявления</a>
    <a href="/<?=Request::$base_url;?>/moder/reputations/" class="button">Отзывы</a>
</div>
<hr />
<?php 

if (isset($events) && $events){
?>
    <div class="red_alert"><a href="" "/forum/modcp.php?action=reports">На премодерации: <?=isset($events['ads'])?'объявлений - '.$events['ads']:'';?><?=isset($events['reputation'])?' отзывы - '.$events['reputation']:'';?></a></div>
<?php 
}
else 
{
	echo 'Елементы ожидающие подтверждения отсутствуют.';
}