<?php 

if ($ad){

	//todo: this error user fields
	$user_fields = $ad->user()->getFields();
	
	$attachments = $ad->get_attachments();
	
	$ad_obj = $ad;
	$ad = $ad->info();
	
	$app_url = Request::$base_url.'shop/';
	
?>

<script src="/jscripts/fancybox/jquery.fancybox.js?v=3" type="text/javascript"></script>
<link type="text/css" href="/jscripts/fancybox/jquery.fancybox.css?v=3" media="screen" rel="stylesheet" />

<link rel="stylesheet" type="text/css" href="/jscripts/fancybox/helpers/jquery.fancybox-thumbs.css?v=1.0.7" />
<script type="text/javascript" src="/jscripts/fancybox/helpers/jquery.fancybox-thumbs.js?v=1.0.7"></script>


<?php

if (($session->user()->get('uid') == $ad['author_id']))
{
	if ($ad['status'] == 2)
	{
	?>
	<div class="red_alert" style="background: #c8fcd1; border: 1px solid green; color: #026CB1;">
		Ваше обьявление отправлено на модерацию
	</div>
	<?php 	
	}
	
	if ($ad['status'] == 0)
	{
	?>
	<div class="red_alert" style="background: #eee; border: 1px solid green; color: 333;">
		Объявление не завершено, требуется активация.
	</div>
	<?php 	
	}
	
}

if ($ad['reject'] && (($session->user()->get('uid') == $ad['author_id']) || $session->user()->isAdmin()))
{
	?>
	<div class="red_alert">
		Ваше обьявление отклонено: <span><?=$ad['reject'];?></span>
	</div>
	<?php
}
?>

<div class="product-view-page" id="shop-content">
	
	<table class="product-main-table" width="100%" align="left">
	<tr>
		<td class="product-pic-cell" width="310px">
			
				<div class="product-pic" >
					
					<div class="image-preview">
					
					<?php 
					
						if (!isset($ad['image']) || empty($ad['image']) || $attachments == false)
						{
					?>
						<img src="/img/shop/no-image.png" width="450px" />
					<?php
						} else {
						
							$img = reset($attachments);
					?>
						<a href="" class="fancybox_first">
							<img src="/<?=$img['file_name'];?>" />
						</a>
					<?php
						}
					?>
					</div>
					
					<div class="product-attachments">
					<ul class="product-attach-previews" id="product-attach-previews">
					<?php
						if ($attachments)
						{
							foreach ($attachments as $attach)
							{
						?>
						<li class="product-item" tag="<?=$attach['tag'];?>" order="<?=$attach['order'];?>">
							<a href="/<?=$attach['file_name'];?>" class="fancybox" data-fancybox-group="thumb" title="<?=$ad['title'];?>">
								<img src="/<?=$attach['file_name'];?>" title="<?=$ad['title'];?>" alt="<?=$ad['title'];?>" />
							</a>
						</li>
						<?php 
							}
						}
					?>
					</ul>
					<div class="clear"></div>
				</div><!-- ad_attachments -->
				
				</div>
		</td><!-- ad-pic-cell -->
		<td class="product-desc-cell">
			<table class="product-desc-block">
			<tr>
				<td class="desc-head">
					<h3 class="product-title"><?=$ad['title'];?></h3>
					<div class="price_block">
						<span style="color: #b44; font-size: 14px; font-weight: bold; line-height: 24px;"><?=number_format($ad['price'], 0, ',', ' ');?> <?=$ad['currency_name'];?></span>
						<!--select name="currency">
							
						</select-->
					</div><!-- price_block -->
				</td><!-- desc-head -->
			</tr>
			<?php 
			
			if ($specifications = $ad_obj->getSpecifications())
			{
			?>
			<tr>
				<td class="desc-content">
					<table>
			<?php
				
				foreach ($specifications as $key=>$spec)
				{
					echo '<tr><td width="180px">';
					echo '<span class="desc-item">'.$spec['title'].'</span>';
					echo '</td><td>';
					if (isset($spec['value']) && gettype($spec['value']) == 'array')
					{
						foreach ($spec['value'] as $v)
						{
							echo '<span class="desc-item">'.$v['title'].'</span><br />';
						}
					}
					else 
					{
						echo '<span class="desc-item">'.$spec['value'].'</span>';
					}
					echo '</td></tr>';
				}
			?>
						<tr><td width="180px"><span class="desc-item">Состояние товара</span></td><td><span class="desc-item" style="color: #b44;"><?=$ad['is_new']?'Новый':'Бывший в употреблении';?></span></td></tr>
					</table>
				</td><!-- desc-content -->
			</tr>
			<?php 
			}
			?>				
			<tr>
				<td class="desc-footer">
					<div class="user-info">
						<div class="user_avatar float_right" style="text-align: center; width: 80px; margin-right: 12px;">
							<a href="/profile/<?=$ad_obj->user()->get('uid');?>"><b style="font-famaly: Verdana; font-size: 12px;"><?=$ad_obj->user()->stylizedUserName();?></b></a>
							<div class="user_status">
							<?php
								$user = $ad_obj->user();
								$status = $user->getStatus();
							?>
								<div class="item" style="margin-bottom: 5px;"><?=$status['stars'];?></div>
							</div>
							<a href="/profile/<?=$ad_obj->user()->get('uid');?>" class="user_avatar float_right"><img src="<?=$ad_obj->user()->getAvatarSrc();?>" style="max-width: 80px;"/></a>
						</div>
					<table>
						<?php 
						if ($status['image'])
						{
						?>
						<div class="item" style="margin: -3px 0 3px 7px;"><?=$status['image']?'<img src="/'.$status['image'].'" />':'';?></div>
						<?php
						}
						?>
						
						<tr><td width="180px"><span class="city">Город</span></td><td><span><?=$ad['city'];?></span></td></tr>
						<tr><td width="180px"><span class="city">Местоположение</span></td><td><span><?=$ad['country_title'].' / '.$ad['region_title'];?></span></td></tr>
						<tr><td width="180px"><span class="contact_face">Контактное лицо</span></td><td><span><b><?=isset($user_fields['fid4'])?$user_fields['fid4']:'';?></b></span></td></tr>
						<tr><td width="180px"><span class="phone">Телефон</span></td><td><span><b><?=isset($user_fields['fid5'])?$user_fields['fid5']:'';?></b> </span></td></tr>
						<tr><td width="180px"><span class="reputation">Репутация продавца</span></td><td><span><a href="/profile/<?=$ad_obj->user()->get('uid');?>/reputations"><?=$ad_obj->user()->get('reputation');?></a></span></td></tr>
					</table>
					<span class="link_to_users_ad"><a href="/profile/<?=$ad['author_id'];?>/ads">Все товары продавца</a></span>
					<!-- span>Обновленно: <?=$ad['last_ad_date'];?></span -->
					<span><a href="/forum/private.php?action=send&uid=<?=$ad['author_id'];?>">Задать вопрос</a></span>
					
					</div><!-- user-info -->
				</td><!-- desc-footer -->
			</tr>
			</table><!-- ad-desc-block -->
		</td>
	</tr>
	</table><!-- .ad-edit-table -->
	<div class="clear"></div>
	
	<div class="product-description"><?=nl2br($ad['description']);?></div>
	
</div>

<?php
}
?>

<script type="text/javascript">
    
	$(document).ready(function() {
        $(".fancybox").fancybox({
				
				padding: 5,
				closeBtn  : true,
				arrows    : true,
				
				type : 'image',
				
				scrolling: 'no',
				
				helpers : {
					thumbs : {
						width  : 70,
						height : 70
					}
				}
        });
        
    });
	
	<?php 
	if ($attachments)
	{
	?>
						
	function gallery_show()
	{
		$.fancybox.open([<?php 
			foreach ($attachments as $attach)
			{
				echo '{href: "/'.$attach['file_name'].'"},';
			}
		?>
			], {
				
				padding: 5,
				closeBtn  : true,
				arrows    : true,
				
				type : 'image',
				
				scrolling: 'no',
				
				helpers : {
					thumbs : {
						width  : 70,
						height : 70
					}
				}
        });
	}
	<?php 
	}
	?>
	
	$(".fancybox_first").click(function() {
		gallery_show();
		return false;
	});
	

</script>