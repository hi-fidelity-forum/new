<table class="shop_product-list" border="0" style="border: none !important;">

	<?php
		$even = true;
		
		foreach ($ad_list->result() as $ad){
			$even = !$even;
		?>
			<tr class="shop_product-list_row <?=$even?' even':'';?>">
				<td class="image_cell">
					<div class="product-item-image">
					<div class="img-preview">
						<?php
							if (isset($ad['image']) && !empty($ad['image']))
							{
						?>
							<a href="<?=Request::$base_url;?>shop/view/<?=$ad['id'];?>"><img src="/<?=$ad['image'];?>" /></a>
						<?php 
							} else {
						?>
							<a href="<?=Request::$base_url;?>shop/view/<?=$ad['id'];?>"><img src="/img/shop/no-image.png" width="79px" height="53px" /></a>
						<?php
							}
						?>
					</div>
				</td>
				<td class="product-info">
					<div class="product-info_block">
						<a class="product-title" href="<?=Request::$base_url;?>shop/view/<?=$ad['id'];?>"><strong><?=$ad['title'];?></strong></a>
						<span class="product-desc"><?=$ad['spec'];?></span>
						
						<?php 
							if (isset($ad['count_items']) && !empty($ad['count_items'])){
								?>
									<span>Наименований: <?=$ad['count_items'];?></span>
								<?php 
							}
						?>
						<span class="author_location"><?=$ad['city'].' - '.$ad['region_title'];?></span>
						
						<span class="product-author smalltext"><a href="/forum/user-<?=$ad['author_id'];?>.html"><?=$ad['author_name'];?></a></span>
						<span class="product-date"><?=View::format_date($ad['last_ad_date']);?></span>
						<span class="product-date"><img src="/images/icons/views_icon.png" height="12px" align="top" style="margin-top: 5px;" title="просмотров <?=$ad['views'];?>" /> <?=$ad['views'];?></span>
					</div><!-- product-info_block -->
				</td>
				
				<td class="product-price_cell">
					<?php 
						if ($ad['price']>0)
						{
							echo '<span class="product-price float_right" style="color: #b44; font-size: 14px;">'.number_format($ad['new_price'], 0, ',', ' ') . ' <span class="smalltext">' . $ad['currency'].'<span></span>';
						}
					?>
					<div class="clear"></div>
					<?php 
					if ($session->isAuth() && ($session->user()->isModer() || $ad['author_id'] == $session->user()->get('uid')))
					{
					?>
					
					<div class="float_right">
						<?php 
						if ($ad['is_new'])
						{
							//echo '<span style="color: #b44;">Новый</span>';
						}
						?>
						<!--nobr>
							<a href="" rem_id="<?=$ad['id'];?>" rem_title="<?=$ad['title'];?>" onClick="return removeAd(this);" style="color: red;">Удалить</a>&nbsp;&nbsp;
							<a href="<?=Request::$base_url;?>shop/edit/<?=$ad['id'];?>" class="buttons">Редактировать</a>
						</nobr-->
					</div>
					<?php
					}
					?>
				</td>
			</tr>
		<?php 
			}
		?>
		</table>
</div>

<script type="text/javascript">

	

</script>