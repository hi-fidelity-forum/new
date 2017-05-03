<div class="caption_block">
	<span class="caption">Создание новой категории</span>
	<form method="post" action="/<?=Request::$base_url;?>/shop/create_category" class="create_category">
		<input type="hidden" name="action" value="create_category" />
		<label>Новый раздел: </label><input type="text" name="title" class="title" value="Введите название..." />
		<input type="submit" value="Создать" class="button" />
	</form>
</div>
<br />
<div class="caption_block">
	<span class="caption">Разделы</span>
	<table width="100%" class="admin_form_table">
		<?php
			
		if (isset($catecoryes)) {
			
			$cats = $catecoryes;
			$even = true;
			foreach ($cats as $cat){
				$even = !$even;
				echo '<tr'.($even?' style="background: #eee;"':'').'><td align="left" width="50"><a href="/'.$request->controller_uri().'/edit_category/'.$cat['id'].'" class="button">Опции</a></td><td><strong>'.$cat['title'].'</strong></td></tr>';
				if (isset($cat['subcategories'])){
					$subs = $cat['subcategories'];
					foreach ($subs as $sub){
						$even = !$even;
						echo '<tr'.($even?' style="background: #eee;"':'').'><td align="left"><a href="/'.$request->controller_uri().'/get_form/'.$sub['id'].'" class="button">Опции</a></td><td>&nbsp;&nbsp;'.$sub['title'].'</td></tr>';
					}
				}
			}
		}
		
		?>
	</table>
</div>  