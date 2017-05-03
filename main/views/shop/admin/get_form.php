<?php 

	if ($form){
		
	$filters = isset($form['filters'])?$form['filters']:false;

	echo isset($form['id'])?'':'<span class="form_warning">Форма не назначена</span>';

?>

<form action="/<?=Request::$base_url;?>/shop/edit_form/<?=$form['cid'];?>" method="POST">
	<?php if (isset($form['id'])) { ?><input type="hidden" name="form_id" value="<?=$form['id'];?>" /><?php } ?>
	<input type="hidden" name="cid" value="<?=$form['cid'];?>" />
	<input type="hidden" name="action" value="save" />
	
	<label>Title: </label><input type="text" name="title_template" value="<?=isset($form['title_template'])?$form['title_template']:'';?>" /><br />
	<label>Spec: </label><input type="text" name="spec_template" value="<?=isset($form['spec_template'])?$form['spec_template']:'';?>" /><br />
	
	<input type="checkbox" <?=$form['is_items']?'checked':'';?> name="is_items" /><label>Объявления могут содержяать списки</label><br />
	
	<button type="submit" class="button">Save</button>
</form>

<hr />
<?php 

if ($filters)
{
	echo '<table class="admin_form_table" cellspacing="0" cellpadding="2"><tr class="thead"><td>Title</td><td>Name</td><td>Style</td><td>Type</td><td>Условие</td><td width="20">Status</td><td width="20">compulsory</td></tr>';
	foreach($filters as $filter)
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
<a href="/<?=Request::$base_url;?>/shop/edit_filters/<?=$form['cid'];?>" class="button">Edit Filters</a>
<?php 
?>
<a href="/<?=Request::$base_url;?>/shop/edit_filters/<?=$form['cid'];?>" class="button">Edit Filters</a>

<?
}
?>