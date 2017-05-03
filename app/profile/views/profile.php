<?php 

	//$group = $user->getGroup();
	
	$user_uid = $user->get('uid');
	
	$app_url = Request::$base_url.$request->app().$user_uid.'/';
	
?>
<table class="user_main_page">
	
	<tr class="bottom_block">
		<td class="leftside">
				<div class="user_avatar">
					<img src="<?=$user->getAvatarSrc();?>" />
					<?php 
					if ($main_mode || $session->user()->isAdmin())
					{
						echo '<div class="change_link"><a href="'.$app_url.'index/avatar_change" title="Сменить аватар">Cменить</a></div>';
					}
					?>
				</div>
				
			<ul class="vertical_menu">
				<li><a href="<?=$app_url;?>index" class="<?=$request->controller()=='controller_index'?'active':'';?>">Основное</a>
				<!-- li><a href="<?=$app_url;?>ads" class="<?=$request->controller()=='controller_ads'?'active':'';?>">Объявления</a -->
				<li><a href="<?=$app_url;?>posts" class="<?=$request->controller()=='controller_posts'?'active':'';?>">Сообщения</a>
				<li><a href="<?=$app_url;?>threads" class="<?=$request->controller()=='controller_threads'?'active':'';?>">Темы</a>
				
				<?php
				if (!($user->isModer() || $user->isAdmin()) || ($session->user()->isModer())) {
				?>
				<li><a href="<?=$app_url;?>reputations" class="<?=$request->controller()=='controller_reputations'?'active':'';?>">Репутация</a>
				<?php
				}
				
				if ($main_mode == true)
				{
				?>
				<!--li><a href="<?=$app_url;?>messaging" class="<?=$request->controller()=='controller_messaging'?'active':'';?>">Почта</a-->
				<?php 
				}
				if ($main_mode == true || $session->user()->isAdmin())
				{
				?>
				<li><a href="<?=$app_url;?>settings" class="<?=$request->controller()=='controller_settings'?'active':'';?>">Настройки</a>
				<?php 
				}
				?>
				<li><a href="<?=$app_url;?>ads" class="<?=$request->controller()=='controller_ads'?'active':'';?>">Объявления</a>
				<?php 
				if ($session->isAuth() && !$user->isModer())
				{
				?>
				<li><a href="" "<?=$app_url;?>freinds" class="<?=$request->controller()=='controller_freinds'?'active':'';?> disable">Окружение</a-->
				<?php 
				}
				?>
			</ul>
		</td>
		<td class="user_content" colspan="2" style="padding-top: 0px;">
			<?=$content;?>
		</td>
	</tr>
</table>