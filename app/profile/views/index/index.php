<?php 

	$status = $user->getStatus();
	$fields = $user->getFields();	
	
	$app_url = Request::$base_url.$request->app().$user->get('uid').'/';
?>

<table class="user_main_page" width="100%">
<tr>
	<td class="info_block" style="text-align: center; padding-right: 0;">
		
		<?php
		 if ($session->isAuth())
		 {
			 echo '<div class="send_buttons float_right">';
			 
			 if ($session->user()->isModer() || $user->get('receivepms'))
			 {
				echo '<a href="/forum/private.php?action=send&uid='.$user->get('uid').'" class="button">Отправить сообщение</a><br />';
			 }
			 if ($session->user()->isModer() || !$user->get('hideemail'))
			 {
				echo '<a href="/forum/member.php?action=emailuser&uid='.$user->get('uid').'" class="button">Отправить e-mail</a>'; 
			 }
			 //echo '<a class="button blue" href="/forum/usercp.php?action=do_editlists&amp;add_username='.$user->get('username').'&amp;my_post_key='.$session->postkey().'">Добавить в друзья</a>';
			 echo '</div>';
		 }
		 ?>
		
		<table class="table_info">
		<tr>
			
			<td>
			
				<h3 class="username" style="font-size: 18px;">
				<?=$user->stylizedUserName();?>
				
				<?php
				
				$online = false;
				
				//var_export($session);
				if ($online_users = $session->getOnlineUsers())
				{	
					foreach($online_users as $u)
					{
						if ($user->get('uid') == $u['uid'])
						{
							$online = true;
						}
					}
				}
				
				if ($online === true)
				{
					echo '<img src="/images/buddy_online.gif" title="На фаруме" />';
				}
				else 
				{
					echo '<img src="/images/buddy_offline.gif" title="Нет на форуме"/>';
				}
				
				?>
				</h3>
				
				<div class="user_status">
					<div class="item"><?=$status['title'];?></div>
					<div class="item"><?=$status['stars'];?>
					<div class="item"><?=$status['image']?'<img src="/'.$status['image'].'" />':'';?></div>
				</div>
				
				<div class="info_item"><label>Сообщений:</label> <?=$user->get('postnum');?></div>
				<!-- div class="info_item"><label>Согласий:</label> <?=$user->get('thxcount');?></div -->
				<?php
				if (!($user->isModer() || $user->isAdmin())) {
				?>
				<div class="info_item"><label>Репутация:</label> <b><?=$user->get('reputation');?></b></div>			
				<?php
				}
				?>
				<div class="info_item"><label>UID:</label> <?=$user->get('uid');?></div>
				<?php
					if ($session->isAuth() && (($user->get('uid') == $session->user()->get('uid')) || $session->user()->isModer()))
					{
						echo '<div class="info_item">';
						if ($session->user()->isModer())
						{
							echo '<label>Уровень предупреждений:</label> <a href="/forum/warnings.php?uid='.$user->get('uid').'">'.( (int) $user->get('warningpoints') * 10 ).'%</a>';
							echo ' [<a href="/forum/warnings.php?action=warn&uid='.$user->get('uid').'">Предупредить</a>]';
						}
						else 
						{
							echo '<label>Уровень предупреждений:</label> <a href="/forum/usercp.php">'.( (int) $user->get('warningpoints') * 10 ).'%</a>';
						}
						echo '</div>';
					}
				?>
				
				
			</td>
		</tr>
		</table>
		<hr />
		<?php 
		if ($main_mode || $session->user()->isAdmin())
		{
			echo '<a href="'.Request::$base_url.$request->app().$user->get('uid').'/index/edit" class="float_right">Редактировать</a>';
		}
		?>
		<table class="table_info">		
		<tr>
			<td class="right" width="120px">Ф.И.О:</td>
			<td><?=$fields['fid4'];?></td>
		</tr>
		<tr>
			<td class="right">Откуда:</td>
			<td><?=$fields['fid1'];?></td>
		</tr>
		<tr>
			<td class="right">Телефон:</td>
		<?php 
		if ($session->isAuth())
		{
			echo '<td>'.$fields['fid5'].'</td>';
		}
		else 
		{
			echo '<td><span title="для просмотра необходима регистрация">xxx-xxxxxxx</span>)</td>';
		}
		
		if ($fields['fid6'])
		{
		?>
		<tr>
			<td class="right">Skype:</td>
			<td><?=nl2br($fields['fid6']);?></td>
		</tr>
		<?php
		}
		?>
		<tr>
			<td class="right">Возраст:</td>
			<td>
				<?php
					if ($user->get('birthday'))
					{
						if ($df = date_create_from_format ('j-n-Y', $user->get('birthday')))
						{
							$res = date_diff(new DateTime('now'), $df)->y;
							echo $res;
						}
					}
				?>
			</td>
		</tr>
		<tr>
			<td class="right">Дата регистрации:</td>
			<td><?=View::format_date($user->get('regdate'), false, false);?></td>
		</tr>
		<tr>
			<td class="right">Последний визит:</td>
			<td><?=View::format_date($user->get('lastactive'), false, false);?></td>
		</tr>
		<?php 
		if ($user->get('website'))
		{
		?>
		<tr>
			<td class="right">Сайт:</td>
			<td><a href="<?=$user->get('website');?>"><?=$user->get('website');?></a></td>
		</tr>
		<?php
		}

		if ($user->get('signature'))
		{
		?>
		<tr>
			<td class="right">Подпись:</td>
			<td><?=$user->get('signature');?>
		<?php
		}
		 
		if ($fields['fid2'])
		{
		?>
		<tr>
			<td class="right">Краткое описание системы:</td>
			<td><?=nl2br($fields['fid2']);?></td>
		</tr>
		<?php
		}
		?>
		</table>
		
		<?php

		if ($session->isAuth() && $session->user()->isModer())
		{
		?>
		<br />
		<table border="0" cellspacing="1" cellpadding="4" width="100%" class="">
		<tr>
			<td colspan="2" class="thead" style="background: #333; font: 12px Verdana;"><strong>Опции модератора</strong></td>
		</tr>
		<tr>
			<td class="trow2">
				<?php 
				if ($notice = $user->get('usernotes'))
				{
					echo 'Заметки модератора: <span style="color: red;">'.$notice.'</span><br />';
				}
				else 
				{
					echo 'В настоящее время нет никаких записей на этого пользователя<br>';
				}
				?>
			</td>
		</tr>
		<tr>
			<td class="trow1">
				<a href="/forum/modcp.php?action=editprofile&amp;uid=<?=$user->get('uid');?>">Редактировать в панели модератора</a><br />
				<a href="/forum/modcp.php?action=banuser&amp;uid=<?=$user->get('uid');?>">Забанить в панели модератора</a>
			</td>
		</tr>

		</tbody></table>

		<?php
			if ($session->user()->isAdmin())
			{
		?>
		<table border="0" cellspacing="1" cellpadding="4" width="100%" class="">
		<tr>
			<td colspan="2" class="thead" style="background: #333; font: 12px Verdana;"><strong>Опции администратора</strong></td>
		</tr>
		<tr>
			<td class="trow1">
				<a href="/forum/admin/index.php?module=user-users&amp;action=edit&amp;uid=<?=$user->get('uid');?>">Редактировать в панели администратора</a><br />
				<a href="/forum/admin/index.php?module=user-banning&amp;uid=<?=$user->get('uid');?>">Забанить в панели администратора</a>
			</td>
		</tr>
		</table>
		<?php 
			}
		 
		}

		?>
		
	</td>
</tr>
</table>