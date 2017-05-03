<?php 

	//$group = $user->getGroup();

?>
<table class="user_main_page">
	<tr class="top_block">
		<td class="leftside" align="center">
			<img src="http://hi-fidelity-forum.com<?=($user->get('avatar') && is_file(DOCROOT.ltrim($user->get('avatar'),'./')))?'/'.(ltrim($user->get('avatar'),'./')):'/images/avatars/hf.jpg';?>" class="user_avatar" />
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

		</td>
		<td class="rightside">
			<!-- a class="button" href="http://hi-fidelity-forum.com/forum/usercp.php?action=do_editlists&amp;add_username=<?=$user->get('username')?>&amp;my_post_key=<?=$session->postkey();?>"><img src="/images/add_buddy.gif" alt="Добавить в друзья"> Добавить в друзья</a -->	
		</td>
	</tr>
	<tr class="bottom_block">
		<td clas="leftside">
			<ul class="vertical_menu">
				<li><a href="<?=Request::$base_url.$request->controller_uri();?>/view" class="<?=$request->action()=='view'?'active':'';?>">Основное</a>
				<li><a href="<?=Request::$base_url.$request->controller_uri();?>/posts" class="<?=$request->action()=='posts'?'active':'';?>">Посты</a>
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