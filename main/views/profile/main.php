<?php 

	//$group = $user->getGroup();

?>
<table class="user_main_page">
	<tr class="top_block">
		<td class="leftside" align="center">
			<?php
				if ($avatar = $user->get('avatar'))
				{
					echo '<!-- '.$avatar.' -->';
					if (mb_strpos($avatar, 'http') === false)
					{
						$avatar = str_replace('./', '/', $avatar);
						
						$m = explode('?', $avatar);
						$p = $m[0];
						if (!file_exists(DOCROOT.$p))
						{
							$avatar = '/images/avatars/hf.jpg';
						}
						
						$avatar = ltrim($avatar, '/');
						$avatar = '/'.$avatar;
					}
				}
				else 
				{
					$avatar = '/images/avatars/hf.jpg';
				}
			?>
			<img src="<?=$avatar;?>" class="user_avatar" />
		</td>
		<td class="info_block">
			<h3 class="username"><?=$user->stylizedUserName();?></h3>
			<div class="user_status">
			<?php
				$status = $user->getStatus();
			?>
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
				echo '<span style="font-size: 11px;">На форуме </span><img src="/images/buddy_online.gif" title="На фаруме" />';
			}
			else 
			{
				echo '<span style="font-size: 11px;">Нет на форуме </span><img src="/images/buddy_offline.gif" title="Нет на форуме"/>';
			}
			
			?>
			
		</td>
		<td class="rightside">
			<div class="send_buttons">
				<?php
				 if ($session->isAuth())
				 {
					 if ($session->user()->isModer() || $user->get('receivepms'))
					 {
						echo '<a href="/forum/private.php?action=send&uid='.$user->get('uid').'" class="button blue">Отправить сообщение</a>';
					 }
					 if ($session->user()->isModer() || !$user->get('hideemail'))
					 {
						echo '<a href="/forum/member.php?action=emailuser&uid='.$user->get('uid').'" class="button blue">Отправить e-mail</a>'; 
					 }
					 //echo '<a class="button blue" href="/forum/usercp.php?action=do_editlists&amp;add_username='.$user->get('username').'&amp;my_post_key='.$session->postkey().'">Добавить в друзья</a>';
				 }
				 ?>
			</div>
		</td>
	</tr>
	<tr class="bottom_block">
		<td clas="leftside">
			<ul class="vertical_menu">
				<li><a href="<?=Request::$base_url.$request->controller_uri();?>/view" class="<?=$request->action()=='view'?'active':'';?>">Основное</a>
				<li><a href="<?=Request::$base_url.$request->controller_uri();?>/posts" class="<?=$request->action()=='posts'?'active':'';?>">Сообщения</a>
				<li><a href="<?=Request::$base_url.$request->controller_uri();?>/threads" class="<?=$request->action()=='threads'?'active':'';?>">Темы</a>
				<?php
				if (!($user->isModer() || $user->isAdmin())) {
				?>
				<li><a href="<?=Request::$base_url.$request->controller_uri();?>/reputations" class="<?=$request->action()=='reputations'?'active':'';?>">Репутация</a>
				<?php
				}
				?>
				<li><a href="<?=Request::$base_url.$request->controller_uri();?>/ads" class="<?=$request->action()=='ads'?'active':'';?>">Объявления</a>
			</ul>
		</td>
		<td class="user_content" colspan="2">
			<?=$content;?>
		</td>
	</tr>
</table>