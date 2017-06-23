<?php 

	$suser = $session->isAuth()?$session->user():false;
	
	$is_moder = $suser?$suser->isModer():false;
	$is_admin = $suser?$suser->isAdmin():false;

	$paging_block = $reputations->createPageLinks('?page={page}');
	
  $even = false;
  
  if ($reputations)
  {
?>

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
			echo ' от <a href="/user/'.$reputation['adduid'].'">'.$reputation['username'].'</a> (<a href="/user/'.$reputation['adduid'].'/reputations">'.$reputation['adduser_reputation'].'</a>) - '.View::format_date($reputation['dateline']);
		?>
		</span>
		<p class="comment">
			<?=$reputation['comments'];?>
		</p>
	</div>

<?php 

	}

?>
	
<div class="pagination float_left" style="margin-top: 7px;">
	<?=$paging_block;?>
</div>

</div>

<?php
  }
 ?>