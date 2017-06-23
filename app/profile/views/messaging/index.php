<?php 

	$is_moder = $user?$user->isModer():false;
	$is_admin = $user?$user->isAdmin():false;

	//$paging_block = $posts->createPageLinks('?page={page}');

?>
<div id="messaging_dialog">
	<div class="buttons">
		
		<!-- a href="/profile/<?=$user->get('uid');?>/messaging/" class="button blue">Входящие</a>
		<a href="/profile/<?=$user->get('uid');?>/messaging/sends" class="button">Отправленные</a -->
		<a href="/profile/<?=$user->get('uid');?>/messaging/notifications" class="button">Уведомления</a>
		
		<!--a href="/profile/<?=$user->get('uid');?>/messaging/create" class="button blue float_right">Новое письмо</a-->
	</div>
	<div class="clear"></div>

<?php 
	
	if ($messages)
	{
		?>
		<table width="100%" class="tborder" cellpadding="4" cellspacing="1">
		
		<tr>
			<td colspan="2" class="thead"><strong class="smalltext">Беседы</strong></td>
			<td class="thead"><strong class="smalltext">Последнее сообщение</strong></td>
		</tr>
		
		<?php 
		
			$even = true;
			
			foreach ($messages as $item)
			{
				$even = !$even;
				
				$author = $item['author'];
				
		?>
		
			<tr class="trow2 forum_depth">
			
				<td align="center" class="trow2 forumdisplay_regular" width="40px">
					<?php 
						
						$author_img = $author->getAvatarSrc();
					?>
					<a href="/profile/<?=$item['lastsider'];?>"><img src="<?=$author_img;?>" width="40px" /></a>
				</td>
				<td class="trow<?=$even?'2':'1';?>" align="right" style="white-space: nowrap;">
					<a href="/<?=$request->uri();?>/dialog/<?=$item['id'];?>" <?=($item['lastmessagetime']>$item['lastread']?' style="font-weight: bold;"':'');?>><?=$item['title'];?></a>
					<br /><span class="smalltext">Aвтор: <a href="/profile/<?=$item['author_id'];?>"><?=$author->stylizedUserName();?></a></span>
				</td>
				
				<td class="trow<?=$even?'2':'1';?>" width="150px">				
					<span class="smalltext"><?=View::format_date($item['lastmessagetime']);?></span>
				</td>
			</tr>
		<?php 
		}
		
?>
		</table>
<?php
	
	}
	else
	{
		//if not messages
?>
	У вас нет переписок
<?php
	}
	
?>

</div>