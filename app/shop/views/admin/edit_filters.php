<?php 

$cid = $filters->getCid();
$options = $filters;
$filters = $filters->getAll();

if ($filters)
{
	echo '<table class="admin_form_table"><tr class="thead"><td width="20">&nbsp;</td><td width="160px">Title</td><td width="90px">Name</td><td width="160px">Style</td><td width="70px">Type</td><td width="50px">Условие</td><td width="50px">Статус</td><td width="200px">Обязательное</td><td width="40px"></td></tr>';
	$even = false;
	foreach($filters as $filter)
	{
		$even = !$even;
		echo '<tr id="filter_row_'.$filter['id'].'" class="trow'.($even?'1':'2').'">';
		echo '<td><a href="#" onClick="return edit_filter_click(this)" edit_fid="'.$filter['id'].'" ><img src="/img/icons/edit.png" width="16px" title="Изменить" /></a></td>';
		echo '<td name="title">'.$filter['title'].'</td>';
		echo '<td name="name">'.$filter['name'].'</td>';
		echo '<td name="style">'.$filter['style'].'</td>';
		echo '<td name="type">'.$filter['type'].'</td>';
		echo '<td name="cond">'.($filter['cond']?'AND':'OR').'</td>';
		echo '<td name="hidden">'.($filter['hidden']?'passive':'active').'</td>';
		echo '<td name="compulsory">'.($filter['compulsory']?'yes':'no').'</td>';
		echo '<td>';
		if ($filter['disporder'] > 1) echo '<a href="" onClick="return decOrder('.$filter['id'].')"><b>&uarr;</b></a>';
		if ($filter['disporder'] < count($filters)) echo '&nbsp;<a href="" onClick="return incOrder('.$filter['id'].')"><b>&darr;</b></a>';
		echo '&nbsp;<a href="" onClick="return remove_filter('.$filter['id'].')" class="float_right"><img src="/img/icons/delete.png" height="16px" /></a>';
		echo '</td>';
		echo '</tr>';
		if (in_array($filter['type'], array('select', 'check')))
		{
			echo '<tr class="trow'.($even?'1':'2').'"><td colspan="9" style="padding-left: 50px; font-size: 11px;">';
			if ($filter['items']) foreach ($filter['items'] as $item)
			{
				echo '<div id="filter_item_row_'.$item['id'].'">';
				echo '<a href="#" onClick="return edit_filter_item(this)" fid="'.$filter['id'].'" item_id="'.$item['id'].'" ><img src="/img/icons/edit.png" width="14px" title="Изменить" /></a>';
				echo '<span name="item_title">'.$item['item_title'].'</span> = <span name="item_value">'.$item['item_value'].'</span>';
				echo '<a href="" onClick="return remove_item('.$item['id'].')"><img src="/img/icons/delete.png" height="14px" /></a>';
				echo '</div>';
			}
			echo '<a href="" onClick="return add_item_click(this)" edit_fid="'.$filter['id'].'" style="background: #eee; border: 1px solid #333; padding: 0 5px;">+</a>';
			echo '</td></tr>';
		}
	}
	echo '</table>';
}
?>
<br />
Добавить фильтр
<table class="admin_form_table"><tr class="thead"><td width="160px">Title</td><td width="90px">Name</td><td width="160px">Style</td><td width="70px">Type</td><td width="50px">Условие</td><td width="50px">Статус</td><td width="200px">Обязательное</td></tr>
<tr>
<form id="edit_filter_form" action="" method="POST">
	<input type="hidden" name="cid" value="<?=$cid;?>" />
	<td><input type="text" name="title" value="" /></td>
	<td><input type="text" name="name" value=""/></td>
	<td><input type="text" name="style" value="" /></td>
	<?php 	
	if (isset($options))
	{
	?>
	<td>
		<select name="type">
			<?php 
			foreach ($options->getFilterTypes() as $type)
			{
				echo '<option value="'.$type.'">'.$type.'</option>';
			}
			?>
		</select>
	</td>	
	<?php 		
	}
	?>
	<td><select name="cond">
		<option value="0" text="OR">OR</option>
		<option value="1" text="AND">AND</option>
	</select></td>
	<td><select name="hidden">
		<option value="0" text="active">active</option>
		<option value="1" text="passive">passive</option>
	</select></td>
	<td><select name="compulsory">
		<option value="1" text="yes">yes</option>
		<option value="0" text="no">no</option>
	</select></td>
</tr>
<tr>
	<td>
		<input type="submit" name="action" value="create" />
	</td>
</form>
</tr>
</table>

<div id="edit_form_block" style="display: none; border: 1px solid #555; background: #fff; padding: 5px; position: absolute; z-index: 10;">
<form id="edit_filter_form" action="" method="POST">
	<input type="hidden" name="action" value="edit" />
	<input type="hidden" name="cid" value="<?=$cid;?>" />
	<input type="hidden" name="fid" value="" />
  <nobr style="padding-left: 20px; margin-bottom: 5px; display: block;">
	<input type="text" name="title" value="" style="width: 160px;" />
	<input type="text" name="name" value="" style="width: 90px;" />
	<input type="text" name="style" value="" style="width: 160px;" />
	<?php 	
	if (isset($options))
	{
	?>
	<select name="type">
		<?php 
		foreach ($options->getFilterTypes() as $type)
		{
			echo '<option value="'.$type.'">'.$type.'</option>';
		}
		?>
	</select>
	<?php 		
	}
	?>
	<select name="cond" style="width: 50px;">
		<option value="0" text="OR">OR</option>
		<option value="1" text="AND">AND</option>
	</select>
	<select name="hidden" style="width: 65px;">
		<option value="0" text="active">active</option>
		<option value="1" text="passive">passive</option>
	</select>
	<select name="compulsory" style="width: 50px;">
		<option value="1" text="yes">yes</option>
		<option value="0" text="no">no</option>
	</select>
  </nobr>
	<input type="submit" value="save" />
	<input type="button" onClick="return hide_form()" value="Cancel" />
</form>
</div>

<div id="edit_item_block" style="display: none; border: 1px solid #555; background: #fff; padding: 5px; position: absolute; z-index: 10;">
	<form id="edit_item_form" action="" method="POST">
		<input type="hidden" name="action" value="put_item" />
		<input type="hidden" name="fid" value="" />
		<input type="hidden" name="item_id" value="" />
		<input type="text" name="item_title" value="" />
		<input type="text" name="item_value" value="" />
		<input type="submit" value="save" />
	</form>
</div>

<script type="text/javascript">
	
	function edit_filter_click(el)
	{
		el = $(el);
		var el_offset = el.offset();
		var px = el_offset.left;
		var py = el_offset.top-8;
		var fid = el.attr('edit_fid');
		var blk = $('#edit_form_block');
		$('#mask').css('display','block');
		blk.css({'display':'block', 'left':px, 'top':py});		
		
		//var vl = $('#edit_filter_form input[name="title"]');
		$('#edit_filter_form input[name="fid"]').val(fid);
		$('#edit_filter_form input[name="title"]').val($('#filter_row_'+fid+' td[name="title"]').text());
		$('#edit_filter_form input[name="name"]').val($('#filter_row_'+fid+' td[name="name"]').text());
		$('#edit_filter_form input[name="style"]').val($('#filter_row_'+fid+' td[name="style"]').text());
		$('#edit_filter_form select[name="type"] option[value="'+$('#filter_row_'+fid+' td[name="type"]').text()+'"]').prop('selected', true);
		$('#edit_filter_form select[name="cond"] option[text="'+$('#filter_row_'+fid+' td[name="cond"]').text()+'"]').prop('selected', true);
		$('#edit_filter_form select[name="hidden"] option[text="'+$('#filter_row_'+fid+' td[name="hidden"]').text()+'"]').prop('selected', true);
		$('#edit_filter_form select[name="compulsory"] option[text="'+$('#filter_row_'+fid+' td[name="compulsory"]').text()+'"]').prop('selected', true);
		
		return false;
	}
	
	function edit_filter_item(el)
	{
		el = $(el);
		var el_offset = el.offset();
		var px = el_offset.left;
		var py = el_offset.top-8;
		var fid = el.attr('fid');
		var item_id = el.attr('item_id');
		var blk = $('#edit_item_block');
		$('#item_mask').css('display','block');
		blk.css({'display':'block', 'left':px, 'top':py});		
		
		$('#edit_item_form input[name="fid"]').val(fid);
		$('#edit_item_form input[name="item_id"]').val(item_id);
		$('#edit_item_form input[name="item_title"]').val($('#filter_item_row_'+item_id+' span[name="item_title"]').text());
		$('#edit_item_form input[name="item_value"]').val($('#filter_item_row_'+item_id+' span[name="item_value"]').text());
		
		return false;
	}
	
	function add_item_click(el)
	{
		el = $(el);
		var el_offset = el.offset();
		var px = el_offset.left;
		var py = el_offset.top;
		var fid = el.attr('edit_fid');
		var blk = $('#edit_item_block');
		$('#item_mask').css('display','block');
		blk.css({'display':'block', 'left':px, 'top':py});
		
		$('#edit_item_form input[name="fid"]').val(fid);
		
		return false;
	}
	
	function hide_form()
	{
		var blk = $('#edit_form_block');
		blk.css({'display':'none'});
		$('#mask').css('display','none');
		return false;
	}
	
	function hide_item_form()
	{
		$('#edit_item_block').css('display','none');
		$('#item_mask').css('display','none');
		return false;
	}
	
	function decOrder(fid)
	{
		$.post("/admin/shop/edit_filters/<?=$cid;?>'",
		{'fid':fid, 'cid': "<?=$cid;?>", 'action': 'decOrder'},
		function(r) {
			location.reload();
		});
		return false;
	}
	
	function incOrder(fid)
	{
		$.post("/admin/shop/edit_filters/<?=$cid;?>'",
		{'fid':fid, 'cid': "<?=$cid;?>", 'action': 'incOrder'},
		function(r) {
			location.reload();
		});
		return false;
	}
	
	function remove_filter(fid)
	{
		if (confirm('Вы действительно хотите удалить фильтр?'))
		{
			$.post("/admin/shop/edit_filters/<?=$cid;?>'",
				{'fid':fid, 'cid': "<?=$cid;?>", 'action': 'remove'},
				function(r) {
					location.reload();
				}
			);
		}
		return false;
	}
	
	function remove_item(item_id)
	{
		if (confirm('Вы действительно хотите удалить опцию фильтра?'))
		{
			$.post("/admin/shop/edit_filters/<?=$cid;?>'",
				{'item_id':item_id, 'cid': "<?=$cid;?>", 'action': 'remove_item'},
				function(r) {
					location.reload();
				}
			);
		}
		return false;
	}
	
</script>
<div id="mask" onClick="hide_form()" style="display: none; position: absolute; top: 0; left: 0; width: 100%; height: 100%; opacity: 0.6; background: #333; z-index: 0;"></div>
<div id="item_mask" onClick="hide_item_form()" style="display: none; position: absolute; top: 0; left: 0; width: 100%; height: 100%; opacity: 0.6; background: #333; z-index: 0;"></div>