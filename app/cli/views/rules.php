<div class="rules_page">

	<div class="rules_menu">
<?php 
	
	$active_id = $active_id?$active_id:1;
	
	if ($headlist){
		foreach ($headlist as $item){
?>
		<div class="rules_menu_item<?=($item['id']==$active_id)?' active':'';?>">
			<a href="/rules/<?=$item['id'];?>"><?=$item['title'];?></a>
		</div>
<?php 
		}
	}

?>
		<div class="clear"></div>
	</div>
	
	<div class="rules_content">
		<?=$content;?>
	</div>
	
</div>