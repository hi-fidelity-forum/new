<div id="shop-content">
<?php 

if ($user)
{
		$client_groups = false;
		foreach ($groups as $gr)
		{
			if ($gr['is_clients_group'] == 1)
			{
				$client_groups[$gr['gid']] = $gr;
			}
		}
?>

	<div class="navigation"><a href="/<?=Request::$base_url;?>/clients/view/<?=$user['gid'];?>"><b><?=$groups[$user['gid']]['title'];?></b></a> / <?=$user['username'];?></div>
	<hr />
<?php 
	if ($change)
	{
		echo '<div style="background: lime; border: 1px solid green; padding: 5px; margin-bottom: 5px;">Платежка добавленна</div><br />';
	}
?>
	
	<div class="caption_block">
		<span class="caption">Добавить платеж</span>			
		<br />
		<form action="" method="POST">
			<table border="0" cellpadding="5px">
				<input type="hidden" name="uid" value="<?=$user['uid'];?>" />
				<tr><td align="right">Старт дата</td><td><input type="text" name="start" placeHolder="31.01.2017" value="" /></td></tr>
				<tr><td align="right">Стоп дата</td><td><input type="text" name="end" placeHolder="31.01.2017" value="" /></td></tr>
				<tr><td align="right">Дата оплаты</td><td><input type="text" name="payment_date" placeHolder="31.01.2017" value="" /></td></tr>
				<tr>
					<td align="right">Статус</td>
					<td>
						<select name="gid">
						<?php 
							foreach ($client_groups as $g)
							{
								echo '<option value="'.$g['gid'].'" '.($user['gid']==$g['gid']?' selected':'').'>'.$g['title'].'</option>';
							}
						?>
						</select>
					</td>
				</tr>
				<tr><td align="right">Сумма</td><td><input type="text" name="amount" value="" style="width: 50px;" /></td></tr>
				<tr>
					<td align="right">Объяв.</td>
					<td>
						<select name="count_ad">
							<option value="20">20</option>
							<option value="50">50</option>
							<option value="100">100</option>
							<option value="150">150</option>
							<option value="200">200</option>
							<option value="250">250</option>
							<option value="300">300</option>
						</select>
					</td>
				</tr>
				<tr><td>&nbsp;</td><td><input type="submit" class="button" name="add_order" value="Добавить" /></td></tr>				
			</table>
	</div>
	
	<br /><br />
	<table border="1" width="50%" cellpadding="4px">
		<tr><td align="right" width="150px">UID </td><td><?=$user['uid'];?></td></tr>
		<tr><td align="right">UserName </td><td><?=$user['username'];?></td></tr>
		<tr><td align="right">Текущий статус </td><td><?=isset($groups[$user['gid']]['title'])?$groups[$user['gid']]['title']:'';?></td></tr>
	</table>
	
<?php     
	if ($user['orders'])
	{
?>
	<br /><br />
	<strong>Платежи:</strong><br /><br />
	<table border="1" width="600px" cellpadding="3px">
		<tr class="title" style="background: #ccc;">
			<td>Старт дата</td>
			<td>Стоп дата</td>
			<td>Дата оплаты</td>
			<td>Статус</td>
			<td>Кол-во объявлений</td>
			<td>Сумма</td>
		</tr>
<?php 
		foreach ($user['orders'] as $order)
		{
?>
		<tr <?=($user['client_order_id'] && $order['id'] == $user['client_order_id'])?' style="background: lime;"':'';?>>
			<td><?=$order['start'];?></td>
			<td><?=$order['end'];?></td>
			<td><?=$order['payment_date'];?></td>
			<td><?=isset($groups[$order['gid']]['title'])?$groups[$order['gid']]['title']:'';?></td>
				<td><?=$order['count_ad'];?></td>
			<td><?=$order['amount'];?></td>
		</tr>
<?php 			
		}
	}
?>
	</table>
<?php 
}
?>
</div>