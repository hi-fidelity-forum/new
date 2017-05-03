<div class="caption_block">
	<span class="caption">Создание новой категории</span>
	<form method="post" action="/<?=Request::$base_url;?>/shop/create_category" class="create_category">
		<input type="hidden" name="action" value="create_category" />
		<label>Новый раздел: </label><input type="text" name="title" class="title" placeholder="Введите название..." value="" />
		<input type="submit" value="Создать" class="button" />
	</form>
</div>
<br />
<div class="caption_block">
	<span class="caption">Разделы</span>
	<table width="100%" class="admin_form_table">
		<?php
			
		if (isset($catecoryes)) 
		{
			$cats = $catecoryes;
			$even = true;
			foreach ($cats as $cat)
			{
				$even = !$even;
				echo '<tr'.($even?' style="background: #eee;"':'').'><td align="left" width="50"><a href="/'.$request->controller_uri().'/edit_category/'.$cat['id'].'" class="button">Опции</a></td><td><strong'.($cat['disable']?' style="color: #888;"':'').'>'.$cat['title'].'</strong></td>';
				echo '<td width="20px"><a href="/'.$request->controller_uri().'/remove_category/'.$cat['id'].'" onClick="return remove_category('.$cat['id'].')"><img src="/img/icons/delete.png" height="14px" /></a></td>';
				echo '</tr>';
				if (isset($cat['subcategories']))
				{
					$subs = $cat['subcategories'];
					foreach ($subs as $sub)
					{
						$even = !$even;
						echo '<tr'.($even?' style="background: #eee;"':'').'><td align="left"><a href="/'.$request->controller_uri().'/edit_category/'.$sub['id'].'" class="button">Опции</a></td><td'.($sub['disable']?' style="color: #888;"':'').'>&nbsp;&nbsp;'.$sub['title'].'</td>';
						echo '<td width="20px"><a href="/'.$request->controller_uri().'/remove_category/'.$sub['id'].'" onClick="return remove_category('.$sub['id'].')"><img src="/img/icons/delete.png" height="14px" /></a></td>';
						echo '</tr>';
					}
				}
			}
		}
		
		?>
	</table>
</div>  

<script type="text/javascript">

	function remove_category()
	{
		if (confirm('Вы действительно хотите удалить категорию?'))
		{
			return true;
		}
		return false;
	}

</script>