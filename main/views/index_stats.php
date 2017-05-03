<table border="0" cellspacing="1" cellpadding="4" class="tborder">
<thead>
<tr>
<td class="thead">
<div class="expcolimage"><img src="/images/collapse.gif" id="boardstats_img" class="expander" alt="[-]" title="[-]" style="cursor: pointer;"></div>
<div><strong>Статистика форума</strong></div>
</td>
</tr>
</thead>
<tbody style="" id="boardstats_e">

<?php 

  if (isset($online_users) && $online_users != false) {
   /*
    //$members = $online_users['members'];
    
  ?>
<!-- start: index_whosonline -->
<!-- tr>
<td class="tcat"><span class="smalltext"><strong>Кто на форуме</strong> [<a href="online.php">Список онлайн</a>]</span></td>
</tr>
<tr>
<td class="trow1"><span class="smalltext">Пользователей за последние 15 минут: <b><?=$online_users['count'];?></b> (<i>зарегистрированных</i>: <b><?=$online_users['member_count'];?></b>, <i>скрытых</i>: <b><?=$online_users['anon_count'];?></b>, <i>гостей</i>: <b><?=$online_users['guest_count'];?></b>).<br />
<?php 
	/*
    foreach ($members as $user)
    {
        if ($user['invisible'] == 0)
        {
?>
        <a href="/forum/user-<?=$user['uid'];?>.html"><?=$user['username'];?></a>,
<?php 
        
        } elseif ($parent_user->is_admin() || $parent_user->is_moder()) {
        ?>    
        <a href="/forum/user-<?=$user['uid'];?>.html"><?=$user['username'];?></a><?=($user['invisible']?'*':'');?>,
        <?php
        }
    
    }
	*/
?>
</td>
</tr>
<!-- end: index_whosonline -->
    
<!-- start: index_stats -->
<!--tr><td class="tcat"><span class="smalltext"><strong>Статистика форума</strong></span></td></tr-->
<?php 

}

if ($stats){
?>
<tr>
<td class="trow1"><span class="smalltext">
На этом форуме создано тем: <b><?=number_format($stats['numthreads'],0,',',' ');?></b>.<br>
Всего сообщений: <b><?=number_format($stats['numposts'],0,',',' ');?></b>.<br>
Зарегистрированных пользователей: <b><?=number_format($stats['numusers'],0,',',' ');?></b>.<br>
Последний зарегистрированный пользователь: <b><a href="/profile/<?=$stats['uid'];?>"><?=$stats['username'];?></a></b><br>
<?php 
if (isset($stats['mostonline'])){
    $mostonline = $stats['mostonline'];
?>
Рекорд посещаемости форума - <b><?=number_format($mostonline['numusers'],0,',',' ');?></b>, зафиксирован <b><?=View::format_date($mostonline['time']);?></b>
<?php 
}
?>
</span>
</td>
</tr>
<?php
}
?>
<!-- end: index_stats -->
</tbody>
</table>