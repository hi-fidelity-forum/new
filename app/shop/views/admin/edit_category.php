<?php 

if ($category){
	
	$cid = $category->get('id');
		
	$filters = $category->getFilters();

?>

<form action="" method="POST">
	<input type="hidden" name="cid" value="<?=$cid;?>" />
	<input type="hidden" name="action" value="save" />
	
	<label>Name: </label><input type="text" name="title" value="<?=$category->get('title')?>" /><br />
	<label>Title template: </label><input type="text" name="title_template" value="<?=$category->get('title_template')?>" /><br />
	<label>Spec template: </label><input type="text" name="spec_template" value="<?=$category->get('spec_template');?>" /><br />
	
	<input type="checkbox" <?=$category->get('is_items')?'checked':'';?> name="is_items" /><label>Объявления могут содержяать списки</label><br />
	<input type="checkbox" <?=$category->get('disable')?'checked':'';?> name="disable" /><label>Скрыть</label><br />
	
	<button type="submit" class="button">Save</button>
</form>

<hr />
<?php 

if ($filters->getAll())
{
	echo '<table class="admin_form_table" cellspacing="0" cellpadding="2"><tr class="thead"><td>Title</td><td>Name</td><td>Style</td><td>Type</td><td>Условие</td><td width="20">Status</td><td width="20">compulsory</td></tr>';
	foreach($filters->getAll() as $filter)
	{
		echo '<tr>';
		echo '<td>'.$filter['title'].'</td>';
		echo '<td>'.$filter['name'].'</td>';
		echo '<td>'.$filter['style'].'</td>';
		echo '<td>'.$filter['type'].'</td>';
		echo '<td>'.($filter['cond']?'AND':'OR').'</td>';
		echo '<td>'.($filter['hidden']?'passive':'active').'</td>';
		echo '<td>'.($filter['compulsory']?'yes':'no').'</td>';
		echo '</tr>';
	}
	echo '</table>';
}
?>
<a href="/<?=Request::$base_url;?>/shop/edit_filters/<?=$cid;?>" class="button">Edit Filters</a>
<?
}
?>