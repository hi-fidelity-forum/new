<?php 

	
	//$paging_block = $posts->createPageLinks('?page={page}');

?>
<div id="messaging_dialog">
	<div class="buttons">
		
		<!-- a href="/profile/<?=$user->get('uid');?>/messaging/" class="button">Входящие</a>
		<a href="/profile/<?=$user->get('uid');?>/messaging/sends" class="button">Отправленные</a -->
		<a href="/profile/<?=$user->get('uid');?>/messaging/notifications" class="button blue">Уведомления</a>
		
		<!-- a href="/profile/<?=$user->get('uid');?>/create" class="button blue float_right">Новое письмо</a-->
	</div>
	<div class="clear"></div>

<?php 
	
	if ($notification)
	{
?>
	<div class="message">
		<?=$notification['message'];?>
	</div>
		
<?php
	
	}
	else
	{
		//if not messages
?>
	У вас нет уведомлений
<?php
	}
	
?>

</div>