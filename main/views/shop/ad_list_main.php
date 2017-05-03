<div id="shop-content">

	<a href="<?=Request::$base_url;?>shop/create_ad/" class="button blue float_right" style="margin-bottom: 5px;">Создать объявление</a>
	<div class="main_product-list">
		<a href="<?=Request::$base_url;?>profile/<?=$user->get('uid');?>/ads" class="<?=($request->action()=='index')?' blue':'';?>">Активные (<?=isset($counts['status_1'])?$counts['status_1']:'0';?>)</a>
		<a href="<?=Request::$base_url;?>profile/<?=$user->get('uid');?>/ads/unactive" class="<?=($request->action()=='unactive')?' blue':'';?>">Отложенные (<?=isset($counts['status_0'])?$counts['status_0']:'0';?>)</a>
		<a href="<?=Request::$base_url;?>profile/<?=$user->get('uid');?>/ads/unapprove" class="<?=($request->action()=='unapprove')?' blue':'';?>">Отклонено (<?=isset($counts['status_3'])?$counts['status_3']:'0';?>)</a>
		<a href="<?=Request::$base_url;?>profile/<?=$user->get('uid');?>/ads/inmoder" class="<?=($request->action()=='inmoder')?' blue':'';?>">На модерации (<?=isset($counts['status_2'])?$counts['status_2']:'0';?>)</a>
	
	</div>
	<div class="clear hr"></div>
	
<table class="main_shop_table" width="100%">
<tr>
<td class="product-list_content">
	
<?php
$paging_block = '';
 
if ($ad_list->getTotalCount()>0)
{
	if ($ad_list->getTotalPages() > 1)
	{
		$paging_block = $ad_list->createPageLinks('?page={page}');
	}
?>
	
<table class="shop_product-list" style="border: none;">

	<?php
		$even = true;
		
		foreach ($ad_list->result() as $ad){
			$even = !$even;
		?>
			<tr class="shop_product-list_row <?=$even?' even':'';?>">
				<td class="image_cell">
					<div class="product-item-image">
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
						<a href="<?=Request::$base_url;?>shop/view/<?=$ad['id'];?>" class="product-title" style="width: auto; display: inline-block;"><strong><?=$ad['title'];?></strong></a>
						
						<span class="product-desc"><?=$ad['spec'];?></span>
						
						<?php 
							if (isset($ad['count_items']) && !empty($ad['count_items'])){
								?>
									<span>Наименований: <?=$ad['count_items'];?></span>
								<?php 
							}
						?>
						<span class="author_location"><?=$ad['city'].' - '.$ad['region_title'];?></span>
						
						<span class="product-author"><a href="/forum/user-<?=$ad['author_id'];?>.html" class="smalltext"><?=$ad['author_name'];?></a></span>
						<span class="product-date"><?=View::format_date($ad['last_ad_date']);?></span>
						<span class="product-date"><img src="/images/icons/views_icon.png" height="12px" align="top" style="margin-top: 5px;" title="просмотров <?=$ad['views'];?>" /> <?=$ad['views'];?></span>
					</div><!-- product-info_block -->
				</td>
				
				<td class="product-price_cell">
					<?php 
						if ($ad['price']>0)
						{
							echo '<span class="product-price float_right" style="color: #b44; font-size: 14px;">'.number_format($ad['price'], 0, ',', ' ') . ' '. $ad['currency'] . '</span>';
						}
					?>
					<div class="clear"></div>
					<div class="float_right">
						
						<!--nobr>
							<a href="" rem_id="<?=$ad['id'];?>" rem_title="<?=$ad['title'];?>" onClick="return removeAd(this);" style="color: red;">Удалить</a>&nbsp;&nbsp;
							<a href="<?=Request::$base_url;?>shop/edit/<?=$ad['id'];?>" class="buttons">Редактировать</a>
						</nobr-->
						<div style="clear"></div>
						<?php 
						if (Model_Shop_Ad::isAdCanUp($ad) || $session->user()->isAdmin())
						{
						?>
						<div style="margin-top: 12px; margin-right: 12px;">
							<a href="/shop/up/<?=$ad['id'];?><?=(isset($_GET['page'])?'?page='.$_GET['page']:'');?>" title="поднять"><img src="/images/icons/up.png" width="20px" /></a>
						</div>
						<?php
						}
						?>
					</div>
					
					<?php 
					if ($session->user()->isModer() || $ad['author_id'] == $session->user()->get('uid'))
					{
					?>
					
					
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
	
<?php 	 
} else {
?>
	<span style="font: 12px Verdana; margin-left: 8px;">Объявлений не найдено</span>
<?php
}
?>

</td></tr>
</table><!-- main_shop_table -->

<div class="pagination float_left">
	<?=$paging_block;?>
</div>

</div>