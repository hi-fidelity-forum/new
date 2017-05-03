<?php 

	$suser = $session->isAuth()?$session->user():false;
	
	$is_moder = $suser?$suser->isModer():false;
	$is_admin = $suser?$suser->isAdmin():false;

	$paging_block = $reputations->createPageLinks('?page={page}');
	
?>

<?php 
	
  $even = false;
  
  if ($reputations)
  {
	  $count_rep = $reputations->getTotalCount();
	  $positive_rep = $count_rep - $reputation_neg;
?>

<p style="margin: 5px 5px 5px;">
	<span class="smalltext">Репутация: <strong><?=$user->get('reputation');?></strong></span><br />
	<span class="smalltext">Всего отзывов: <strong><?=$reputations->getTotalCount();?></strong></span><br />
	<span class="smalltext reputation_positive">Положительных: <strong><?=$positive_rep;?></strong></span><br />
	<span class="smalltext reputation_negative">Отрицательных: <strong><?=$reputation_neg;?></strong></span>
</p>

<table width="100%" cellspacing="1" cellpadding="3" class="tborder" style="clear: both; margin-bottom: 5px; border: none;">	
<!--tr><td class="thead smalltext"><strong style="line-height: 14px;">Отзывы</strong></td></tr-->
<tr><td>

<div id="reputation_content">

<?php
	
	foreach ($reputations->result() as $reputation)
	{
		
		$even = !$even;
		
		$rep = (int) $reputation['reputation'];
?>

	<div class="reputation_item <?=($rep>=0)?'positive':'negative';?>">
		<span class="title"><?php 
			if ($rep>0) { echo '<b>Положительно (+'.$rep.')</b>';}
			elseif ($rep==0) { echo '<b>Нейтрально ('.$rep.')</b>'; }
			elseif ($rep<0) { echo '<b>Отрицательно ('.$rep.')</b>'; }
			echo ' от <a href="/profile/'.$reputation['adduid'].'">'.$reputation['username'].'</a> (<a href="/profile/'.$reputation['adduid'].'/reputations">'.$reputation['adduser_reputation'].'</a>) - '.View::format_date($reputation['dateline']);
		?>
		</span>
		<?php
			if ($is_admin)
			{
				echo '<div class="float_right"><a href="/'.$request->uri().'/?delete='.$reputation['rid'].'">Удалить</a></div>';
			}
		?>
		<p class="comment">
			<?=$reputation['comments'];?>
		</p>
	</div>

<?php 

	}

?>
	
<div class="pagination">
	<?=$paging_block;?>
</div>

</div>

</td>
</tr>
</table>

<?php
  }
 ?>