<?php 

	$user = isset($users)?$users:new User($request->param('id'));
	
	$fields = $user->getFields();
	
	$session_user = $session->isAuth()?$session->user():false;
	
	$is_moder = $session_user?$session_user->isModer():false;
	$is_admin = $session_user?$session_user->isAdmin():false;
	
?>

<table class="table_info">
<tr>
	<td class="right">Ф.И.О:</td>
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
	
	//var_export($session->getOnlineUsers());
	
?>
<br />
<table border="0" cellspacing="1" cellpadding="4" width="100%" class="tborder">
<tr>
	<td colspan="2" class="thead"><strong class="smalltext">Опции модератора</strong></td>
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
<table border="0" cellspacing="1" cellpadding="4" width="100%" class="tborder">
<tr>
	<td colspan="2" class="thead"><strong class="smalltext">Опции администратора:</strong></td>
</tr>
<tr>
	<td class="trow1">
		<a href="/forum/admin/index.php?module=user-users&amp;action=edit&amp;uid=<?=$user->get('uid');?>">Редактировать этого пользователя  в панели администратора</a><br />
		<a href="/forum/admin/index.php?module=user-banning&amp;uid=<?=$user->get('uid');?>">Забанить  этого пользователя  в панели администратора</a>
	</td>
</tr>
</table>
<?php 
	}
 
}

?>