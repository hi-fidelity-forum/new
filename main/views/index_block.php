<link href="/css/shop.css" rel="stylesheet" type="text/css" media="all">

<div class="shop_index_block">
	<div class="block_title thead">
		<strong><a href="<?=Request::$base_url;?>shop/">Объявления</a></strong>
	</div>
	
	<div class="index_list">
		<table class="cat_categories" width="100%">
		<tr>
			<td class="tcat" colspan="2"><span class="smalltext"><strong>Категории</strong></span></td>
			<td class="tcat" width="80" align="center" style="white-space: nowrap"><span class="smalltext"><strong>Объявлений</strong></span></td>		
			<td class="tcat" width="300" align="left"><span class="smalltext"><strong>Последнее объявление</strong></span></td>
		</tr>
		<?php 
			$even = 1;
			
			if (isset($cat_list))
			{		
				//print_r($cat_list);
				foreach ($cat_list as $cat)
				{
					$even = $even==1?2:1;
		?>
			<tr class="trow<?=$even;?> forum_depth">
				<td class="trow<?=$even;?>" align="center" valign="top" width="1"><img src="/images/on.gif" ></td>
				
				<td valign="top" class="category_item">				
					<?php
					
					if (isset($cat['subcategories']))
					{
					?>
					<strong class="category_name"><a href="<?=Request::$base_url;?>shop/subcategory/<?=$cat['id'];?>"><?=$cat['title'];?></a></strong>
					<div class="sub_categoryes">
					<?php 
						if (isset($cat['subcategories'])){
							foreach ($cat['subcategories'] as $sub){
					?>
						&nbsp;&nbsp;&nbsp;&nbsp;
						<span class="subcategories_title">
							<a href="<?=Request::$base_url;?>shop/category/<?=$sub['id'];?>"><?=$sub['title'];?></a>
							<!--&nbsp;<span title="Количество объявлений">(<?=$sub['ad_counts'];?>)</span>-->
						</span>
					<?php
							}
						}
					?>
					</div><!-- sub_categoryes -->
					<?php 
					} else { //Not is sub category
					?>
						<strong class="category_name"><a href="<?=Request::$base_url;?>shop/category/<?=$cat['id'];?>"><?=$cat['title'];?></a></strong>
					<?php 
					}
					?>
				</td><!-- category_info -->
				<td>
					<?=$cat['ad_counts'];?>
				</td>
				<td class="last_cat_info" align="left">
				    <?php
						if (isset($cat['last_data']) && $cat['ad_counts']>0)
						{
						?>
							<img src="/<?=is_file('../'.$cat['last_data']['last_ad_image'])?$cat['last_data']['last_ad_image']:'img/shop/no-image.png';?>" width="90px" height="67px" align="left" style="margin-right: 10px;" />
							<a href="<?=Request::$base_url;?>shop/view/<?=$cat['last_data']['last_ad_id'];?>" title="<?=$cat['last_data']['last_ad_title'];?>" ><strong><?=View::cutString($cat['last_data']['last_ad_title'],25);?></strong></a><br />
							<?=View::format_date($cat['last_data']['last_ad_date']);?>
						<?php
						} else {
							echo 'Нет объявлений';
						}
					?>
					</td><!-- last_cat_info -->
				</tr><!-- category_row -->
		<?php
				}
			}
		?>
		</table><!-- cat_categories -->
	</div><!-- index_list -->
	
</div>
<br />