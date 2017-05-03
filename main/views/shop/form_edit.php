<script type="text/javascript" src="/jscripts/jquery.autocomplete.js"></script>
<link href="/css/jquery.autocomplete.css" rel="stylesheet" type="text/css" media="all">
<?php 

$ad_info = $ad->info();

if ($category)
{
	$form = array();
	
	$form['cid'] = $category->get('id');
	
	if (!$title_template = $category->get('title_template'))
	{
		$form['title_template'] = '{title}';
		$form['spec_template'] = '';
	}
	else 
	{
		$form['title_template'] = $category->get('title_template');
		$form['spec_template'] = $category->get('spec_template');
	}
	
	$User = $session->user();
	if (!$filters = $category->getFilters()->getAll())
	{
		$form['filters'] = array('title'=>array(
			'title'=>'Название',
			'name'=>'title',
			'type'=>'string',
			'style'=>'{title}',
			'hidden'=>1,
			'cond'=>0,
			'compulsory'=>1
		));
		$filters = $form['filters'];
	}
	
?>

<form class="shop_create_form" method="post" action="">

	<div class="preview_product-form">
		<strong class="title_value"><?=$ad_info['title'];?></strong>
		<br />
		<span class="spec_value"><?=$ad_info['spec'];?></span><br />
	</div>

	<input type="hidden" name="change_form" id="change_form_item" value="0" />
	
	<input type="hidden" name="title" class="form_title" value="" />
	<input type="hidden" name="spec" class="form_spec" value="" />
		
	<div class="form_items_block">
		<table class="form_items table_info">
		
		<?php if (isset($filters) && $filters)
		{
			foreach ($filters as $filter)
			{
				echo '<tr>';
				
				switch ($filter['type']) {
					case 'brand':
						echo '<td class="align_right"><label>'.$filter['title'].'</label></td>';
						echo '<td><input name="form['.$filter['name'].']" type="text" filter_name="'.$filter['name'].'" id="brand_input" value="'.(isset($form_items[$filter['name']])?htmlspecialchars($form_items[$filter['name']]):'').'" /><?td>';
					break;
					case 'string':
						echo '<td class="align_right"><label>'.$filter['title'].'</label></td>';
						echo '<td><input name="form['.$filter['name'].']" type="text" filter_name="'.$filter['name'].'" value="'.(isset($form_items[$filter['name']])?htmlspecialchars($form_items[$filter['name']]):'').'" /></td>';
					break;
					case 'check':
						if (isset($filter['items']) && $filter['items'] != false)
						{
							echo '<td class="align_right"><label>'.$filter['title'].'</label></td><td>';
							foreach ($filter['items'] as $item)
							{
								$checked = false;
								if (isset($form_items[$filter['name']][$item['id']]))
									$checked=true;
								echo '<input name="form['.$filter['name'].']['.$item['id'].']" type="checkbox" filter_name="'.$filter['name'].'" value="'.$item['item_value'].'" '.($checked?'checked':'').' />';
								echo '<label>'.$item['item_title'].'</label><br />';
							}
							echo '</td>';
						}
					break;
					case 'select':
						if (isset($filter['items']) && $filter['items'] != false)
						{
							echo '<td class="align_right"><label>'.$filter['title'].'</label></td><td>';
							foreach ($filter['items'] as $item)
							{
								$checked = false;
								if (isset($form_items[$filter['name']]))
								{
									if ($item['item_value'] == $form_items[$filter['name']])
										$checked=true;
								}
								echo '<input name="form['.$filter['name'].']" type="radio" filter_name="'.$filter['name'].'" value="'.$item['item_value'].'" '.($checked?'checked':'').' />';
								echo '<label>'.$item['item_title'].'</label><br />';
							}
							echo '</td>';
						}
					break;
					case 'range':
						echo '<td class="align_right"><label>'.$filter['title'].'</label></td>';
						echo '<td><input name="form['.$filter['name'].']" type="text" filter_name="'.$filter['name'].'" value="" style="width: 40px;" /></td>';
					break;
				}
				
				echo '</tr>';
			}
		}
		?>
		
			<?php 
			if (isset($can_new) && $can_new)
			{
			?>
			<tr>
				<td class="align_right"><label>Новый</label></td>
				<td><input type="checkbox" name="is_new" class="input_checkbox" id="is_new" <?=($ad_info['is_new']?'checked':'');?> /> <span class="smalltext"><?=($can_new==1024)?'':'<i>(доступно к отметке "Новый" - '.$can_new.' товара)</i>';?></span></td>
			</tr>
			<?php
			}
			?>
		</table>

	</div>
	
	<button type="submit" class="button blue" style="margin-left: 116px; width: 100px;">Сохранить</button>
	
	
	
</form>

<script type="text/javascript">

	var title_template = '<?=$form['title_template'];?>';
	var spec_template = '<?=$form['spec_template'];?>';
	var items = [];
	
<?php 
	if ($filters)
		foreach ($filters as $filter)
		{
			echo '	items["'.$filter['name'].'"] = [];'."\n";
			echo '	items["'.$filter['name'].'"]["type"]="'.$filter['type'].'";'."\n";
			echo '	items["'.$filter['name'].'"]["style"]="'.$filter['style'].'";'."\n";
			echo '	items["'.$filter['name'].'"]["title"]="'.$filter['title'].'";'."\n";
			echo '	items["'.$filter['name'].'"]["compulsory"]="'.($filter['compulsory']?1:0).'";'."\n";
			if (isset($form_items[$filter['name']]))
			{
				$fi = $form_items[$filter['name']];
				if (gettype($fi) == 'array')
				{
					$vl = '';
					foreach ($fi as $it)
					{
						$vl .= $it.', ';
					}
					$vl = rtrim($vl, ', ');
					echo '	items["'.$filter['name'].'"]["value"]="'.$vl.'";'."\n"."\n";
				}
				else 
				{
					echo '	items["'.$filter['name'].'"]["value"]="'.htmlspecialchars($fi).'";'."\n"."\n";
				}
			}
			else 
			{
				echo '	items["'.$filter['name'].'"]["value"]="";'."\n"."\n";
			}
		}
	?>
	
	$('.form_items input').bind("change paste keyup", function(){
		change_title($(this))
		$('.preview_product-form').css('background','#c8fcd1');
	});
	
	$('#select_country').live("change", function(){
		var cn = $(this);
		var country = cn.val();
		if (country)
		{
			var title = $( "#select_country option:selected" ).text();
			$('#country_title').val(title);
			$.ajax(
			{
				dataType: "json",
				url: "/cli/get_regions/"+country,
				success: function(res)
				{
					var reg = $('#select_region');
					if (res)
					{
						reg.html('');
						reg.removeAttr('disabled');
						reg.append($("<option></option>")
								.attr("value", "")
								.text("--")
							);
						for (var inx in res)
						{
							reg.append($("<option></option>")
								.attr("value", res[inx].id)
								.text(res[inx].title)
							);
						}
					}
				}
			});
		}
	});
	
	$('#select_region').live("change", function(){
		var rg = $(this);
		var region = rg.val();
		if (region)
		{
			var title = $("#select_region option:selected" ).text();
			$('#region_title').val(title);
		}
	});
	
	$('.shop_create_form').submit(function(){
		var err = '';
		for (var key in items)
		{
			if ((items[key]['value'] == "") && (items[key]['compulsory'] == '1'))
			{
				err += "   "+items[key]['title']+"\n";
			}
		}
		/*
		if ($.trim($('.shop_create_form #select_country').val()) == "")
		{
			err += "   Страна\n";
		}
		if ($.trim($('.shop_create_form #select_region').val()) == "")
		{
			err += "   Область\n";
		}
		if ($.trim($('.shop_create_form #city').val()) == "")
		{
			err += "   Город\n";
		}
		if ($.trim($('.shop_create_form #price').val()) == "")
		{
			err += "   Цена\n";
		}
		*/
		if (err == '')
		{
			return true;
		}
		else 
		{
			alert("Вы не указали:\n\n"+err);
			return false;
		}
		return false;
	});
		
	function change_title(obj)
	{	
		var title_str = title_template;
		var spec_str = spec_template;
		var ttl = $('.form_title');
		var spec = $('.form_spec');
		var title = obj.val();
		var filter_name = obj.attr('filter_name');
		for (var key in items)
		{
			if (key == filter_name)
			{
				var type = items[key]["type"];
				var vl = '';
				if (type == 'check')
				{
					if (obj.prop('checked') == false)
					{
						var val = items[key]["value"];
						var t = title;
						title = val.replace(', '+t, '');
						title = title.replace(t+',', '');
						title = title.replace(t, '');
					} 
					else 
					{
						if (items[key]["value"] != '') 
						{
							title = items[key]["value"] + ', '+title;
						}
					}
				}
				items[key]["value"] = title;
			}
			var stl = items[key]["style"];
			if (items[key]["value"] != '')
			{
				var new_title = stl.replace('{'+key+'}', items[key]["value"]);
			}
			else 
			{
				var new_title = '';
			}
			title_str = title_str.replace('{'+key+'}', new_title);
			spec_str = spec_str.replace('{'+key+'}', ' '+new_title);
		}
		$('.title_value').html(title_str);
		$('.spec_value').html(spec_str);
		ttl.val(title_str);
		spec.val(spec_str);
		$('#change_form_item').val('1');
	}
	
	$(function() 
	{
		
		$("#brand_input").autocomplete(false, {
			url:'/cli/brands/',
			dataType: 'json',
			formatItem: function(item) {
				return item.name;
			},
			minChars: 1,
		}).result(function(event, data){
			change_title($(this));
		});
	})

</script>

<?
}
?>