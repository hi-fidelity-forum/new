<?php 
	$category_title = $category_title?$category_title:'';
?>

<form method="post" action="/<?=Request::$base_url;?>/shop/create_category">

  <table class="admin_form_table">
  <tr><td class="form_items">
	<input type="hidden" name="action" value="put_category" />	
	<label>Название категории: </label><input type="text" name="title" value="<?=$category_title;?>" /><br />
  </td></tr>
  <tr><td class="form_items">
	<label>Раздел:</label>
	<select name="category_id">
		<option value="0">Главная</option>
		<?php 
		
		if ($catecoryes){
			foreach ($catecoryes as $cat){
				echo '<option value="'.$cat['id'].'">'.$cat['title'].'</option>';
			}
		}
		?>
	</select>
  </td></tr>
  <tr><td class="form_items">
	<label>Права доступа для групп:</label>
	
	<table width="100%"><tr>
	<td width="130px">
		<div class="caption_block">
			<span class="caption">Чтение</span>
			<input type="hidden" name="groups_read" value="0" class="groups_read" />
			<input type="checkbox" checked class="checkbox_all_group" onchange="check_all_group(this, 'groups_list', 'groups_read')"/><label>Все группы</label><br />
			<select size="10" multiple="multiple" class="groups_list" disabled onchange="on_change_group_list(this, 'groups_read')">
		<?php 
		
		if ($user_groups){
			foreach ($user_groups as $group){
				echo '<option value="'.$group['gid'].'">'.$group['title'].'</option>';
			}
		}
		?>
			</select>
		</div>
	</td>
	<td style="padding-left: 10px;" width="130px">
		<div class="caption_block">
			<span class="caption">Создание</span>
			<input type="hidden" name="groups_create" value="0" class="groups_create" />
			<input type="checkbox" checked class="checkbox_all_group" onchange="check_all_group(this, 'groups_list_create', 'groups_create')"/><label>Все группы</label><br />
			<select size="10" multiple="multiple" class="groups_list_create" disabled onchange="on_change_group_list(this, 'groups_create')">
		<?php 
		
		if ($user_groups){
			foreach ($user_groups as $group){
				echo '<option value="'.$group['gid'].'">'.$group['title'].'</option>';
			}
		}
		?>
			</select>
		</div>
	</td>
	<td></td>
	</tr></table>
  </td></tr>
  <tr><td class="form_items">
	<input type="submit" value="Сохранить" class="button" />
	<a href="/<?=Request::$base_url;?>/shop/category" class="button">Отмена</a>
  <td></tr>
  </table><!-- .admin_form_table -->
</form>

<script type="text/javascript">
<!--

	function check_all_group(e, list_block, item_block)
	{
		var el = $(e);
		var list = $('.'+list_block);
		var rul = $('.'+item_block);
		if (el.is(':checked') == true) 
		{
			list.attr('disabled','true');
			rul.val('0');
		} else {
			list.removeAttr('disabled');
			if (list.val() != null){
				rul.val(list.val());
			}
			
		}
	}
	
	function on_change_group_list(el, item_block)
	{
		var list = $(el);
		
		var rul = $('.'+item_block);
		
		if (list.val() != null){
			rul.val(list.val());
		}
	}
	
// -->
</script>