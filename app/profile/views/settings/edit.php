<?php 

	$status = $user->getStatus();
	$fields = $user->getFields();	
	
	$tpp = $user->get('tpp');
	$ppp = $user->get('ppp');
	
	
if (isset($change_flag) && $change_flag == true)
{
	echo '<div style="background: lime; color: black; line-height: 24px; border: 1px solid #555;">Изменения сохранены</div>';
}

?>

<div style="padding-left: 5px;">

<form action="" method="post" name="input">

<table class="tborder" border="0" cellspacing="1" cellpadding="4" style="border: none;">
<tr>
	<td class="tcat" colspan="2" style="background: #eee;"><span class="smalltext"><strong>Основные настройки</strong></span></td>
</tr>
<tr>
<td class="trow2" style="background: #fff;">
<table class="table_info">
<tr>
	<td><span>Тем на страницу:</span></td>
	<td>
		<select name="tpp">
			<option value="">По умолчанию</option>
			<option value="25" <?=($tpp==25)?' selected':'';?>>25 тем </option>
			<option value="50" <?=($tpp==50)?' selected':'';?>>50 тем </option>
		</select>
	</td>
</tr>

<tr>
	<td><span>Сообщений на страницу:</span></td>
	<td>
		<select name="ppp">
			<option value="">По умолчанию</option>
			<option value="10"<?=($ppp==10)?' selected':'';?>>10 сообщений </option>
			<option value="25"<?=($ppp==25)?' selected':'';?>>25 сообщений </option>
			<option value="50"<?=($ppp==50)?' selected':'';?>>50 сообщений </option>
		</select>
	</td>
</tr>

<tr>
	<td><span><label for="showsigs">Показывать подписи пользователей.</label></span></td>
	<td valign="top" width="1"><input type="hidden" name="showsigs" value="0" /><input type="checkbox" class="checkbox" name="showsigs" value="1" id="showsigs" <?=($user->get('showsigs')?'checked="checked"':'')?> /></td>
</tr>
<tr>
	<td><span><label for="showavatars">Показывать аватары пользователей.</label></span></td>
	<td valign="top" width="1"><input type="hidden" name="showavatars" value="0" /><input type="checkbox" class="checkbox" name="showavatars" value="1" id="showavatars" <?=($user->get('showavatars')?'checked="checked"':'')?> /></td>
</tr>

<tr>
	<td><span><label for="invisible">Скрыть меня из списка 'Кто на форуме'.</label></span></td>
	<td valign="top" width="1"><input type="hidden" name="invisible" value="0" /><input type="checkbox" class="checkbox" name="invisible" value="1" id="invisible" <?=($user->get('invisible')?'checked="checked"':'')?> /></td>
</tr>

<tr>
	<td><span>Выберите часовой пояс из списка.</span></td>
	<td>
		
		<select name="timezone" id="timezoneoffset">
		
		<?php
		
		if (!$utz = $user->get('timezone'))
		{
			$utz = false;
		}
		
		$i = -120;
		while ($i<=120) 
		{
			$v = intval($i/10);
			$d = fmod($i, 10);
			$k = $v.($d?'.5':'');
			echo '<option value="'.$k.'" '.(($utz == $k)?' selected':'').'>GMT '.$v.($d?':30':':00').'</option>';
			$i = $i+5;
		}
		?>
		</select>
	</td>
</tr>
<tr>
	<td><span>Настройка летнего времени:</span></td>
	<td>
		<select name="dstcorrection">
			<option value="2"<?=($user->get('dstcorrection')==2?' selected="selected"':'')?>>Автоматически корректировать настройки</option>
			<option value="1"<?=($user->get('dstcorrection')==1?' selected="selected"':'')?>>Всегда использовать летнее время</option>
			<option value="0"<?=($user->get('dstcorrection')==0?' selected="selected"':'')?>>Не использовать летнее время</option>
		</select>
	</td>
</tr>
</table>

</td></tr>
</table>
<table class="tborder" border="0" cellspacing="1" cellpadding="4" style="border: none; margin-top: 5px;">
<tr>
	<td class="tcat" colspan="2" style="background: #eee;"><span class="smalltext"><strong>Сообщения и Уведомления</strong></span></td>
</tr>
<tr>
<td class="trow2" style="background: #fff;">

<table class="table_info">
<tr>
	<td><span><label for="hideemail">Скрыть адрес электронной почты.</label></span></td>
	<td valign="top" width="1"><input type="hidden" name="hideemail" value="0" /><input type="checkbox" class="checkbox" name="hideemail" id="hideemail" value="1" <?=($user->get('hideemail')?'checked="checked"':'')?>></td>
</tr>
<tr>
	<td><span><label for="receivepms">Получать личные сообщения от участников.</label></span></td>
	<td valign="top" width="1"><input type="hidden" name="receivepms" value="0" /><input type="checkbox" class="checkbox" name="receivepms" id="receivepms" value="1" <?=($user->get('receivepms')?'checked="checked"':'')?>></td>
</tr>

<tr>
	<td><span><label for="pmnotice">Оповещать всплывающим окном о получении нового ЛС.</label></span></td>
	<td valign="top" width="1"><input type="hidden" name="pmnotice" value="0" /><input type="checkbox" class="checkbox" name="pmnotice" id="pmnotice" value="1" <?=($user->get('pmnotice')?'checked="checked"':'')?>></td>
</tr>
<tr>
	<td><span><label for="pmnotify">Уведомлять меня по электронной почте о получении ЛС.</label></span></td>
	<td valign="top" width="1"><input type="hidden" name="pmnotify" value="0" /><input type="checkbox" class="checkbox" name="pmnotify" id="pmnotify" value="1" <?=($user->get('pmnotify')?'checked="checked"':'')?>></td>
</tr>
<tr>
	<td><span><label for="subscriptionmethod">Вариант подписки по умолчанию:</label></span></td>
	<td>
		<select name="subscriptionmethod" id="subscriptionmethod">
			<option value="0"<?=($user->get('subscriptionmethod')==0?' selected="selected"':'')?>>Не подписываться</option>
			<option value="1"<?=($user->get('subscriptionmethod')==1?' selected="selected"':'')?>>Подписываться без уведомлений</option>
			<option value="2"<?=($user->get('subscriptionmethod')==2?' selected="selected"':'')?>>Подписываться с уведомлениями</option>
		</select>
	</td>
</tr>

</table>

<table class="tborder" border="0" cellspacing="1" cellpadding="4" style="border: none; margin-top: 5px;">
<tr>
	<td class="tcat" colspan="2" style="background: #eee;"><span class="smalltext"><strong>Объявления</strong></span></td>
</tr>
<tr>
<td class="trow2" style="background: #fff;">

	<table class="table_info">
		<tr>
			<td><span><label for="currency">Валюта по умолчанию.</label></span></td>
			<td valign="top">
				<?=var_export($fields['currency']);?>
				<select name="currency" id="default_currency">	
					<option value="1"<?=($fields['currency']==1 || $fields['currency']==0)?' selected="selected"':'';?>>USD</option>
					<option value="2"<?=$fields['currency']==2?' selected="selected"':'';?>>EUR</option>
					<option value="3"<?=$fields['currency']==3?' selected="selected"':'';?>>UAH</option>
					<option value="4"<?=$fields['currency']==4?' selected="selected"':'';?>>RUB</option>
				</select>
			</td>
		</tr>
	</table>

</td>
</tr>
</table>

</td></tr>
<tr><td>
<br />
<center><input type="submit" class="button blue" value="Сохранить" name="save" />

</td></tr>
</table>

</form>

</div>