<?php 

	$status = $user->getStatus();
	$fields = $user->getFields();	

?>

<div style="padding-left: 5px;">

<form action="" method="post" name="input">

<table class="tborder" border="0" cellspacing="1" cellpadding="4" style="border: none;">
<tr>
	<td class="tcat" colspan="2" style="background: #eee;"><span class="smalltext"><strong>Необходимые поля</strong></span></td>
</tr>
<tr>
<td class="trow2" style="background: #fff;">

<table class="table_info">		
<tr>
	<td width="200px"><span class="smalltext">Место жительства</span>: </td>
	<td><input type="text" name="profile_fields[fid1]" class="textbox" size="0" maxlength="20" value="<?=$fields['fid1'];?>"></td>
</tr>

<tr>
	<td><span class="smalltext">Имя (Фамилия, Отчество)</span>:</td>
	<td><input type="text" name="profile_fields[fid4]" class="textbox" size="0" value="<?=$fields['fid4'];?>"></td>
</tr>
<tr>
	<td><span class="smalltext">Номер телефона</span>:</td>
	<td><input type="text" name="profile_fields[fid5]" class="textbox" size="0" value="<?=$fields['fid5'];?>"></td>
</tr>
<tr>
	<td><span class="smalltext">Подпись</span>: </td>
	<td><textarea name="signature" rows="4" style="width: 400px;"><?=$user->get('signature');?></textarea></td>
</tr>
</table>

</td></tr>
</table>

<table class="tborder" border="0" cellspacing="1" cellpadding="4" style="border: none; margin-top: 5px;">
<tr>
	<td class="tcat" colspan="2" style="background: #eee;"><span class="smalltext"><strong>Дополнительная информация</strong></span></td>
</tr>
<tr>
<td class="trow2" style="background: #fff;">

<table cellspacing="0" cellpadding="4">
<tr>
	<td width="200px"><span class="smalltext">Дата рождения:</span></td>
	<td>
		<?php 
			$bday1 = $bday2 = $bday3 = false;
			
			$bday = $user->get('birthday');
			
			if (!empty($bday))
			{
				$b = explode('-', $bday);
				$bday1 = isset($b[0])?$b[0]:false;
				$bday2 = isset($b[0])?$b[1]:false;
				$bday3 = isset($b[0])?$b[2]:false;
			}
		?>
		<select name="bday1">
			<option value="">&nbsp;</option>
		<?php 
			for ($i=1;$i<=31;$i++)
			{
				echo '<option value="'.$i.'"'.(($bday1==$i)?' selected="selected"':'').'>'.$i.'</option>';
			}
		?>
		</select>
		<select name="bday2">
			<option value="">&nbsp;</option>
			<option value="1"<?=(($bday2==1)?' selected="selected"':'');?>>Январь</option>
			<option value="2"<?=(($bday2==2)?' selected="selected"':'');?>>Февраль</option>
			<option value="3"<?=(($bday2==3)?' selected="selected"':'');?>>Март</option>
			<option value="4"<?=(($bday2==4)?' selected="selected"':'');?>>Апрель</option>
			<option value="5"<?=(($bday2==5)?' selected="selected"':'');?>>Май</option>
			<option value="6"<?=(($bday2==6)?' selected="selected"':'');?>>Июнь</option>
			<option value="7"<?=(($bday2==7)?' selected="selected"':'');?>>Июль</option>
			<option value="8"<?=(($bday2==8)?' selected="selected"':'');?>>Август</option>
			<option value="9"<?=(($bday2==9)?' selected="selected"':'');?>>Сентябрь</option>
			<option value="10"<?=(($bday2==10)?' selected="selected"':'');?>>Октябрь</option>
			<option value="11"<?=(($bday2==11)?' selected="selected"':'');?>>Ноябрь</option>
			<option value="12"<?=(($bday2==12)?' selected="selected"':'');?>>Декабрь</option>
		</select>
		<input type="text" class="textbox" size="4" maxlength="4" name="bday3" value="<?=($bday3?$bday3:'');?>">
	</td>
</tr>
<tr>
	<td>
		<span class="smalltext">Отображение даты рождения:</span>
	</td>
	<td>
		<select name="birthdayprivacy">
		<?php 		
			 $birthdayprivacy = $user->get('birthdayprivacy');
		?>
			<option value="all"<?=(($birthdayprivacy=='all')?' selected="selected"':'');?>>Показывать дату рождения и возраст</option>
			<option value="age"<?=(($birthdayprivacy=='age')?' selected="selected"':'');?>>Показывать только возраст</option>
			<option value="none"<?=((($birthdayprivacy=='none') || ($birthdayprivacy==false))?' selected="selected"':'');?>>Скрывать дату рождения и возраст</option>
		</select>
	</td>
</tr>
<tr>
	<td><span class="smalltext">Адрес веб-сайта:</span></td>
	<td><input type="text" class="textbox" name="website" size="25" maxlength="75" style="width: 180px;" value="<?=$user->get('website');?>"></td>
</tr>
<tr>
	<td><span class="smalltext">Имя в Skype</span>:</td>
	<td><input type="text" name="profile_fields[fid6]" class="textbox" size="0" style="width: 180px;" value="<?=$fields['fid6'];?>"></td>
</tr>
<tr>
	<td><span class="smalltext">Краткое описание системы</span>: </td>
	<td><textarea name="profile_fields[fid2]" rows="4" style="width: 400px;"><?=$fields['fid2'];?></textarea></td>
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