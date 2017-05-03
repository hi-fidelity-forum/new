<?php

	if ($modlist = $parent->modlist())
	{
		
		echo '<span class="smalltext">Модераторы: ';
		$mdstr = '';
		foreach ($modlist as $mod)
		{
			$mdstr .= '<strong><a href="user-'.$mod['uid'].'.html" style="color: #CC0000;">'.$mod['username'].'</a></strong>, ';
		}
		echo trim($mdstr, ', ');	
		echo '</span><br />';
	}
	
	//$parent->get('fid')
	$online_users = $session->getOnlineUsers();
	
	
	$members = $online_users['members'];

?>

<span class="smalltext">Пользователи, просматривающие этот форум (<?=$online_users['member_count'];?>): 
<?php 
    if ($members) 	foreach ($members as $user)
    {
        if ($user['invisible'] == 0)
        {
?>
        <a href="/forum/user-<?=$user['uid'];?>.html"><?=$user['username'];?></a>,
<?php 
        
        } elseif ($User->is_admin() || $User->is_moder()) {
        ?>
        <a href="/forum/user-<?=$user['uid'];?>.html"><?=$user['username'];?></a><?=($user['invisible']?'*':'');?>,
        <?php
        }
    
    }

?> Гостей: <?=$session->getCountGuests();?>.