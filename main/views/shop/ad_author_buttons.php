<div class="float_right" style="margin-right: 0px; margin-top: 10px;">
<?php 

if ($ad){
	
	$ad_obj = $ad;
	$ad = $ad->info();
	
	$app_url = Request::$base_url.'shop/';

	if ($session->user()->get('uid') == $ad['author_id'] || $session->user()->isAdmin())
	{
		if ($session->user()->isAdmin())
		{
			if ($ad['status'] == 2)
			{
		?>
		<a href="<?=$app_url.'approve/'.$ad['id'];?>" class="button blue">Подтвердить</a>
		<?php 
			}
			if ($ad['status'] == 2 || $ad['status'] == 1)
			{
		?>
		<a href="" class="button red" id="reject_button">Отклонить</a>
		<?php
			}
		}
		if (($ad['status'] == 1 && Model_Shop_Ad::isAdCanUp($ad)) || $session->user()->isAdmin())
		{
		?>
		<a href="<?=$app_url.'up/'.$ad['id'];?>" class="button" style="background: #468846; border: 1px solid #238323; color: #fff;">Поднять</a>
		<?php 
		}
		?>
	<a href="<?=$app_url.'edit/'.$ad['id'];?>" class="button blue" id="edit_button">Редактировать</a>
	<a href="<?=$app_url.'remove/'.$ad['id'];?>" class="button red" id="remove_button">Удалить</a>
	<?php
	}
	?>

</div>
<div class="clear"></div>
<div style="display: none; margin-top: 5px; position: relative; float: right; width: 375px;" id="reject_table">

<table width="600px" id="form_reject_table">
	
	<form action="/shop/reject" method="POST" id="form_reject">
	<input type="hidden" name="product-id" value="<?=$ad['id'];?>" />
	<tr>
		<td>
			<input name="reject_message" type="radio" value="Объявление не содержит описания" />
			<label>Отсутствует описание товара</label>
			<br />
			<input name="reject_message" type="radio" value="Объявление содержит изображение из стороних ресурсов" />
			<label>изображение со стороних ресурсов</label>
		</td>
	</tr>
	<tr>
		<td>
			<input name="reject_message" type="radio" id="custom_message" />
			<label>Другое</label><br />
			<textarea name="" id="reject_message" value="" style="margin: 5px 0 5px 25px; width: 400px; display: none;"></textarea>
		</td>
	</tr>
	<tr>
		<td style="padding-top: 10px;">
			<input type="submit" value="Отклонить" class="button red" />
		</td>
	</tr>
	</form>
	
</table>
</div>

<script type="text/javascript">

	$('#remove_button').click(function()
	{
		if (confirm('Вы собираетесь удалить объявление, продолжить?'))
		{
			return true;
		}
		return false;
	});

	$('#reject_button').click(function()
	{		
		$(this).addClass('disable');
		$('#reject_table').css('display','block');		
		return false;
	});
	
	$('#edit_button').click(function()
	{		
		if (<?=$ad['status'];?> == 1)
		{
			if (confirm('Для редактирвония объявление будет отложенно, продолжить?'))
			{
				return true;
			}
		}
		else 
		{
			return true;
		}
		return false;
	});
	
	$('#custom_message').click(function()
	{		
		$('#reject_message').css('display','block');
		$('#reject_message').attr('name','reject_message');
		
	});
	
	$('#form_reject').submit(function()
	{		
		var data = $('#form_reject').serializeArray();
		var mess = '';
		for (x in data)
		{
			if (data[x].name == 'reject_message')
			{
				mess = data[x].value;
			}
		}
		if (mess == '' && mess != 'undefined')
		{
			alert('Вы не указали причину отклонения');
			return false;
		}
		
	});
	
</script>

<?
}
?>
