<div class="simpleUserInfo">
			<?php
			
				$fields = $user->getFields();
			?>
			<a href="/profile/<?=$user->get('uid');?>"><span class="username"><?=$user->stylizedUserName();?></span></a>
			<?php
			
				$online = false;
				
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
			<div class="user_status">
			<?php
				$status = $user->getStatus();
			?>
				<div class="item"><?=$status['title'];?></div>
				<div class="item"><?=$status['stars'];?>
				<div class="item"><?=$status['image']?'<img src="/'.$status['image'].'" />':'';?></div>
			</div>
			<img src="<?=$user->getAvatarSrc();?>" class="user_avatar" />
			<?php if (!empty($fields['fid1']))
				{
			?>
			<div class="info_item"><label>Откуда:</label> <?=$fields['fid1'];?></div>
			<div class="info_item"><label>Сообщений:</label> <?=$user->get('postnum');?></div>
			<?php
				}
			?>
			<!-- div class="info_item"><label>Согласий:</label> <?=$user->get('thxcount');?></div -->
			<?php
			if (!($user->isModer() || $user->isAdmin())) {
			?>
			<div class="info_item"><label>Репутация:</label> <b><?=$user->get('reputation');?></b></div>			
			<?php
			}
			?>
</div>