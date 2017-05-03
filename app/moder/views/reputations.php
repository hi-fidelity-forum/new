<div class="admin_page_menu">
	<a href="/<?=Request::$base_url;?>/moder/ads/" class="button">Объявления</a>
    <a href="/<?=Request::$base_url;?>/moder/reputations/" class="button active">Отзывы</a>
</div>
<hr />
<div id="reputation_content">

<?php

if ($reputations)
{
	
	$paging_block = $reputations->createPageLinks('?page={page}');

?>

<div class="pagination float_left" style="margin-top: 7px;">
	<?=$paging_block;?>
</div>
	
<table id="reputation_content" width="100%">

<?php 

	foreach ($reputations->result() as $reputation)
	{
		$rep = (int) $reputation['reputation'];
?>
		<tr>
			<td>
				<div class="reputation_item <?=($rep>=0)?'positive':'negative';?>">
					<a href="/admin/moder/reputations/?delete=<?=$reputation['rid'];?>" class="button red">Отклонить</a>
					<a href="/admin/moder/reputations/?enable=<?=$reputation['rid'];?>" class="button blue">Принять</a>
					&nbsp;
					<span class="title">
					<?php 
						if ($rep>0) { echo '<b>Положительно (+'.$rep.')</b>';}
						elseif ($rep==0) { echo '<b>Нейтрально ('.$rep.')</b>'; }
						elseif ($rep<0) { echo '<b>Отрицательно ('.$rep.')</b>'; }
						echo ' от <a href="/profile/'.$reputation['adduid'].'">'.$reputation['username'].'</a> (<a href="/profile/'.$reputation['adduid'].'/reputations">'.$reputation['adduser_reputation'].'</a>)';
						echo ' кому: <a href="/profile/'.$reputation['uid'].'">'.$reputation['tousername'].'</a> - '.View::format_date($reputation['dateline']);
					?>
					</span>
					<p class="comment">
						<?=$reputation['comments'];?>
					</p>
				</div>
			</td>
		</tr>
<?php 

	}
?>
</table>

<div class="pagination float_left" style="margin-top: 7px;">
	<?=$paging_block;?>
</div>

<?php 
	
}
else
{
	echo 'Елементы ожидающие подтверждения отсутствуют.';	
}
?>
</div>