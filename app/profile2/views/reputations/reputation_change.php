<?php 

if ($user_reputation['disabled'] == 1)
{
?>
	<div style="background: #c8fcd1; border: 1px solid green; line-height: 14px; font-size: 12px; padding: 4px; margin: 0 0 0 3px;"><center>Ваш отзыв принят и ожидает модерации</center></div>
<?php 
}
else {
?>

<div style="padding: 0 0 0 5px;">
<table width="100%" cellspacing="1" cellpadding="3" class="tborder" style="clear: both; border: none;">	
	<!--tr><td class="thead smalltext" colspan="3"><strong style="line-height: 14px;"><?=$user_reputation?'Изменить отзыв':'Добавить отзыв';?></strong></td></tr-->
	<tr>
		<td style="background: #eee !important;" class="tcat" width="60px"><span class="smalltext"><strong>Оценка</strong></span></td>
		<td class="tcat" align="left" style="white-space: nowrap; background: #eee !important;"><span class="smalltext"><strong>Комментарий</strong></span></td>
		<td class="tcat" width="85" style="background: #eee !important;">&nbsp;</td>
	</tr>
	<tr>
	
<form action="" method="POST" id="change_reputation_form">

<?php 

if (!isset($max_rep))
{
	$user_group = $session->user()->getDisplayGroup();
	$max_rep = (int) $user_group['reputationpower'];
}

if ($user_reputation && $user_reputation['disabled'] == 0)
{
?>

	<td style="padding-top: 5px !important; padding-left: 0;">
		<select name="reputation" style="height: 20px; width: 65px;">
		<?php 
			for ($i=$max_rep;$i>=-$max_rep;$i--)
			{
				if ($i != 0) echo '<option value="'.$i.'"'.($user_reputation['reputation']==$i?' selected':'').'>'.($i>0?'+':'').$i.'</option>';
			}
		?>
		</select>
	</td>
	<td style="padding-top: 5px !important;">
		<input type="text" value="<?=$user_reputation['comments'];?>" name="comments" id="comments" style="width: 100%;" />
	</td>

<?php 
}
else
{
?>
	
	<td style="padding-top: 5px !important; padding-left: 0;">
		<select name="reputation" style="height: 20px; width: 65px;">
		<?php 
			for ($i=$max_rep;$i>=-$max_rep;$i--)
			{
				if ($i != 0) echo '<option value="'.$i.'">'.($i>0?'+':'').$i.'</option>';
			}
		?>
		</select>
	</td>
	<td style="padding-top: 5px !important;">
		<input type="text" value="" name="comments" id="comments" style="width: 100%;" />
	</td>
	
<?php 
}	
?>

	<td style="text-align: right !important; padding: 5px 0 0;">
		<input type="submit" value="Изменить" style="height: 20px;" name="change" class="button blue" />
	</td>

	
</form>

</tr>
</table>

</div>

<script type="text/javascript">

	$('#change_reputation_form').submit(function()
	{
		var inp = $('input#comments');
		if (inp.val() == '')
		{
			alert('Введите комментарий к отзыву');
			return false;
		}
	});

</script>

<?php
}
?>