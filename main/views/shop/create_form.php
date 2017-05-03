
  <table width="100%"><tr>
    <td width="180px">
	<div class="sale_block_items">

		<div class="caption_block" style="width: 600px; padding: 20px 10px;">
			<span class="caption">Выберите раздел</span>			
			
			<?php 
				
				foreach ($cats as $cat)
				{
					if (isset($cat['subcategories']))
					{
						echo '<b>'.$cat['title'].'</b>';
						echo '<br/>';
						$subs = $cat['subcategories'];
						foreach ($subs as $sub){
							echo '<a href="/shop/create_ad/'.$sub['id'].'">&nbsp;&nbsp;'.$sub['title'].'</a><br />';
						}
						echo '<br />';
					}
					else 
					{
						echo '<b><a href="/shop/create_ad/'.$cat['id'].'">'.$cat['title'].'</a></b><br />';
					}
				}
			
			?>
			
			<!--input type="submit" value="Далее" class="button" /-->
		</div>		
	</div>
	
	</td><td width="10px">&nbsp;</td>
	<td>
		<div id="category_form"></div>
	</td>
  </tr></table>