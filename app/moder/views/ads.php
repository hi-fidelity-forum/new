<div class="admin_page_menu">
	<a href="/<?=Request::$base_url;?>/moder/ads/" class="button active">Объявления</a>
    <a href="/<?=Request::$base_url;?>/moder/reputations/" class="button">Отзывы</a>
</div>
<hr />
<?php 

if (isset($ads) && $ads)
{

?>
	
<table width="100%">
<?php
	foreach ($ads as $item)
	{
?>
	<tr>
		<td width="70px">
			<a href="/shop/view/<?=$item['id'];?>"><img src="/<?=$item['image']?$item['image']:'img/shop/no-image.png';?>" width="78px" /></a>
		</td>
		<td style="padding-left: 5px;">
			<span><a href="/shop/view/<?=$item['id'];?>"><?=$item['title'];?></a></span><br />			
			<span class="smalltext"><?=$item['spec'];?></span><br />			
			<span class="product-author smalltext">Автор: <a href="/forum/user-<?=$item['author_id'];?>.html"><?=$item['author_name'];?></a></span>
		</td>
	</tr>
<?php 
	}
?>
</table>
<?php 
}
else 
{
?>

<?php 
}
?>