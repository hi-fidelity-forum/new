<?php
	if ($uptime>0 && $users)
	{
		echo 'Состояние: <b>Активно</b>';
	}
	else 
	{
		echo 'Состояние: Не активно.';
	}
?>
<br />
<form action="/<?=Request::$base_url;?>/shop/change_uptime/" method="POST">
Время апдейта, каждые: <input type="text" value="<?=$uptime;?>" name="uptime" /> секунд.
<input type="submit" value="Изменить" />
</form>
<strong style="color: #555; font-size: 11px">Для активации автоапа укажите время. Для остановки укажите "0" для времени апдейта.</strong>
<hr />
<?php
	if ($users)
	{
		foreach ($users as $user)
		{
?>
			<strong><?=$user->get('uid');?> - <?=$user->get('username');?></strong><img src="/<?=$user->get('avatar');?>" width="50px" /><br />
<?php 
		}
?>
<?php 
	}
	else
	{
?>
	Нет пользователей для автоапов.
<?php 
	}
?>
<hr />
<form action="/<?=Request::$base_url;?>/shop/add_autoup_user/" method="POST">
	Добавить UID пользователя: <input type="text" value="" name="user_uid">
	<input type="submit" value="Добавить" />
</form>