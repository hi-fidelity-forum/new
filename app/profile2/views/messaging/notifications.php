<?php 

	
	//$paging_block = $posts->createPageLinks('?page={page}');

?>
<div id="messaging_dialog">
	<div class="buttons">
		
		<!-- a href="/profile/<?=$user->get('uid');?>/messaging/" class="button">Входящие</a>
		<a href="/profile/<?=$user->get('uid');?>/messaging/sends" class="button">Отправленные</a-->
		<a href="/profile/<?=$user->get('uid');?>/messaging/notifications" class="button blue">Уведомления</a>
		
		<!--a href="/profile/<?=$user->get('uid');?>/create" class="button blue float_right">Новое письмо</a-->
	</div>
	<div class="clear"></div>

<?php 
	
	if ($notifications->getTotalCount()>0)
	{
		?>
		<table width="100%" class="tborder" cellpadding="4" cellspacing="1">
		
		<tr>
			<td colspan="1" class="thead"><strong class="smalltext">Название</strong></td>
			<td colspan="1" class="thead" width="150px"><strong class="smalltext">Время</strong></td>
		</tr>
		
		<?php 
		
			$even = false;
			
			foreach ($notifications->result() as $item)
			{
				$even = !$even;
				
		?>
		
			<tr class="trow<?=$even?'2':'1';?>">
			
				<td align="left" class="trow<?=$even?'2':'1';?>">
					<?php 
					if ($item['is_read'])
					{
						echo '<a href="/profile/'.$item['uid'].'/messaging/notifications/'.$item['id'].'">'.$item['subject'].'</a>';
					}
					else 
					{
						echo '<a href="/profile/'.$item['uid'].'/messaging/notifications/'.$item['id'].'"><b>'.$item['subject'].'</b></a>';
					}
					?>					
				</td>
				<td class="trow<?=$even?'2':'1';?>" width="150" align="right">
					<span class="smalltext"><?=View::format_date($item['dateline']);?></span>
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
	У вас нет уведомлений
<?php
	}
	
?>

</div>