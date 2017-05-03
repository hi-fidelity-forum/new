<div id="shop-content">

	<?php 
	if ($session->user()->isAdmin())
	{
	?>
	<div class="main_product-list">
		<a href="<?=Request::$base_url;?>profile/<?=$user->get('uid');?>/ads" class="<?=($request->action()=='index')?' blue':'';?>">Активные (<?=isset($counts['status_1'])?$counts['status_1']:'0';?>)</a>
		<a href="<?=Request::$base_url;?>profile/<?=$user->get('uid');?>/ads/unactive" class="<?=($request->action()=='unactive')?' blue':'';?>">Отложенные (<?=isset($counts['status_0'])?$counts['status_0']:'0';?>)</a>
		<a href="<?=Request::$base_url;?>profile/<?=$user->get('uid');?>/ads/unapprove" class="<?=($request->action()=='unapprove')?' blue':'';?>">Отклонено (<?=isset($counts['status_3'])?$counts['status_3']:'0';?>)</a>
		<a href="<?=Request::$base_url;?>profile/<?=$user->get('uid');?>/ads/inmoder" class="<?=($request->action()=='inmoder')?' blue':'';?>">На модерации (<?=isset($counts['status_2'])?$counts['status_2']:'0';?>)</a>
	
	</div>
	<div class="clear hr"></div>
	<?php 	
	}
	?>
	
	<table class="main_shop_table" width="100%"
	<tr>
	<td class="ad_list_content">
	
	<?php 
	
	if (isset($ads))
	{
		$paging_block = $ads->createPageLinks('?page={page}');
	}
	else $paging_block = '';
	
	if (isset($ad_list_items) && $ad_list_items)
	{
	?>
	<?php 	
	echo $ad_list_items;
	 
	} else {
	?>
		Пользователь не имеет объявлений
	<?php
	}

?>
	<div class="pagination float_left">
		<?=$paging_block;?>
	</div>
</td></tr>
</table><!-- main_shop_table -->

</div>