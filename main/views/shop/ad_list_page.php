<?php 

$cid = $parent->get('id');

if ($ad_list && $ad_list->getTotalPages()>1)
{
	$paging_block = $ad_list->createPageLinks('?page={page}');
}
else $paging_block = '';

$filter_items = false;

?>

<div id="shop-content">
	
	<div class="pagination float_left">
		<?=$paging_block;?>
	</div>
	
	<?php 
	
		$groups_create = $parent->get('groups_create');
		
		if ($session->isAuth() && in_array($session->user()->getGroupID(), explode(',',$groups_create)))
		{
		?>
		<a href="<?=Request::$base_url;?>shop/create_ad<?=$parent->get('id')?'/'.$parent->get('id'):'';?>" class="float_right button blue" style="margin-bottom: 5px;">Создать объявление</a>
		<?php
		}
		?>

	<!--div style="text-align: right; margin: 0 20px 10px 0;" class="float_right">
		Выбор валюты <select name="course" class="select_course">
            <option value="USD" selected="selected">USD</option>
            <option value="UAH">UAH</option>
            <option value="RUR">RUR</option>
            <option value="EUR">EUR</option>
        </select>
	</div-->
	
	
	<div class="clear"></div>
	
	
	
	<table class="main_shop_table" width="100%">
	<tr>
	<?php
	if ($filters->getAll())
	{
	?>
	<td width="200px" class="filters_col" style="background: #eee; border-right: 7px solid #fff; padding: 10px;">
		<form class="filter_form" method="GET">
		<?php
			foreach ($filters->getAll() as $filter_key=>$filter)
			{
			if ($filter['hidden'] == false && $filter['items']){
				echo '<span class="filter_name">'.$filter['title'].'</span><br />';
				
				foreach ($filter['items'] as $item)
				{
					if (isset($filter['type']))
					{
						if (isset($item['active']) && $item['active'])
						{
							$filter_items[$filter['name']][$item['id']] = true;
						}
						$type = $filter['type'];
						if ($type == 'range')
						{
							
						}
						else 
						{
							$active = (isset($item['active']) && $item['active'])?true:false;
							echo '<div class="filter_item">';
							echo '<input type="checkbox" name="'.$filter['name'].'" filter_id="'.$item['id'].'" id="'.$filter['name'].'_'.$item['id'].'" class="filter check" '.($active?' checked':'').' value="'.$item['id'].'" />';
							echo '<label class="item_title" for="'.$filter['name'].'_'.$item['id'].'">'.($active?'<strong>':'').$item['item_title'].($active?'</strong>':'').'</label>';
							echo '</div>';
						}
					}
				}
				echo '<br />';
			}
			}
		?>
			<input type="button" class="filter_form_clear" value="Очистить" />
		</form>
	</td>
	<?php 
	}
	?>
	<td class="product-list_content">
	
	<table class="shop_product-list" border="0" style="border: none !important;">
		<tr class="sort-param-block">
		<td colspan="3" valign="middle">
			<!-- span class="select-region">
				область <b>x</b>
			</span -->
			<div class="float_right">
				<!-- Сортировка:
				<span class="select-sort"><span>по умолчанию</span> <b style="font-size: 8px; top:-3px; position: relative;">&#9660;</b></span> -->
				<div class="select-currency">
					<b class="default-currency"><?=$currency->codeToName($currency->getUserCurrency());?></b>
					<div class="select-currency-list" style="display: none;">
						<span>USD</span>
						<span>EUR</span>
						<span>UAH</span>
						<span>RUB</span>
					</div>
				</div>
				
			</div>
			<div class="clear"></div>
		</td>
		</tr>
	</table>
	
	<?php 
	
	if (isset($ad_list_items) && $ad_list_items){

		echo $ad_list_items;
	 
	} else {
	?>
		Объявления не найдены.
	<?php
	}

?>
	<div class="pagination float_left">
		<?=$paging_block;?>
	</div>
</td></tr>
</table><!-- main_shop_table -->

</div>

<div id="error_res"></div>

<script type="text/javascript">
	
	var key='filters';
	var site_link = '<?=Request::$base_url;?>shop/category/<?=$cid;?>';
	
	var flt_str = '';
	var filter_items = [];
	
	<?php
	if ($filter_items)
	{
		echo 'filter_items = '.json_encode($filter_items);
	}
?>
	
	$('.filter_form input').bind("change paste keyup", function(){
		change_filter(this);
	});
	
	function change_filter(el)
	{
		var input = $(el);
		
		var filter_name = input.attr('name');
		var filter_value = input.attr('value');
		if (input.attr('type') == 'checkbox')
		{
			if (filter_items[filter_name] == undefined)
			{
				filter_items[filter_name] = {};
			}
				
			filter_items[filter_name][filter_value] = input.prop('checked');
		}
		
		for (var key in filter_items)
		{
			var item = filter_items[key];
			var item_str = '';
			if (typeof item == 'object')
			for (var k in item)
			{
				if (item[k] == true)
				{
					if (item_str != '') item_str += '+';
					item_str += k;
				}
			}
			if (item_str != '')
			{
				item_str = key+'='+item_str;
				if (flt_str != '') flt_str += ':';
				flt_str += item_str;
			}
		}
	
		if (flt_str != '')
		{
			window.location = site_link+'/'+flt_str;
		} else {
			window.location = site_link;
		}
	}
	
<?php 
	if ($session->isAuth() && $session->user()->isModer())
	{
?>
	
	function removeAd(el)
	{
		el = $(el);
		var id = el.attr('rem_id');
		var title = el.attr('rem_title');
		if (confirm('Вы собираетесь удалить объявление: '+title))
		{
			$.ajax({
					type: "POST",
					url: "/shop/remove/"+id,
					data: {'id':id, 'ajax':1},
					dataType: "json",
					success: function(res) 
					{
						if (res == '1')
						{
							location.reload();
						}
						else 
						{
							//alert(res);
							alert('Объявления небыло удалено');
						}
					},
					/*
					error: function(e, s){
						//alert(e.responseText);
						$('#error_res').html(e.responseText);
					},
					*/
				});
		}
		return false;
	}
<?php
}
?>
	
	$('.filter_form_clear').click(function()
	{
		window.location = site_link;
	});
	
	$('.select-currency .default-currency').click(function()
	{
		$('.select-currency .select-currency-list').css('display','block');
	});
	
	$('.select-currency .select-currency-list span').click(function()
	{
		var el = $(this);
		var curr = $(el).text();
		$.ajax({
			type: "POST",
			url: "/shop/change_currency/",
			data: {'currency':curr, 'ajax':1},
			dataType: "json",
			success: function(res) 
			{
				location.reload();
			},
		});
		$('.select-currency .select-currency-list').css('display','none');
	});
	
	$('.select-currency .select-currency-list').mouseleave(function()
	{
		$('.select-currency .select-currency-list').css('display','none');
	});
	
</script>	