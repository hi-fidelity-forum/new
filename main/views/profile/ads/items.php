<table class="shop_ad_list">
	<?php
		$even = true;
		
		foreach ($ad_list->result() as $ad){
			$even = !$even;
		?>
			<tr class="shop_ad_list_row <?=$even?' even':'';?>">
				<td class="image_cell">
					<div class="ad-item-image">
						<?php
							if (isset($ad['image']) && !empty($ad['image']))
							{
						?>
							<a href="<?=Request::$base_url;?>shop/view/<?=$ad['id'];?>"><img src="/<?=$ad['image'];?>" style="max-width: 100px;" /></a>
						<?php 
							} else {
						?>
							<a href="<?=Request::$base_url;?>shop/view/<?=$ad['id'];?>"><img src="/img/shop/no-image.png" style="max-height: 80px; max-width: 80px;" /></a>
						<?php
							}
						?>
					</div>
				</td>
				<td class="ad_info">
				<div class="ad_info_block">
					<a href="<?=Request::$base_url;?>shop/view/<?=$ad['id'];?>"><strong><?=$ad['title'];?></strong></a>
					<?php 
						if (isset($ad['count_items']) && !empty($ad['count_items'])){
							?>
								<span>Наименований: <?=$ad['count_items'];?></span>
							<?php 
						}
					?>
					<br />
					<span class="ad_date"><?=View::format_date($ad['last_ad_date']);?></span>
					<?php 
					if ($session->user()->isModer() || $ad['author_id'] == $session->user()->get('uid'))
					{
					?>
					<br />
					<div class="ad_info_edit_block">
						<a href="<?=Request::$base_url;?>shop/edit/<?=$ad['id'];?>" class="button">Редактировать</a>
					</div><!-- ad_info_edit_block -->
					<?php
					}
					?>
				</div><!-- ad_info_block -->
				</td>
				<td>
					<span class="ad_author"><a href="/forum/user-<?=$ad['author_id'];?>.html"><?=$ad['author_name'];?></a></span><br />			
				</td>
				<td class="ad_price_cell">
					<span class="ad_price"><?=$ad['price'];?></span>
				</td>
			</tr>
		<?php 
			}
		?>
		</table>
</div>