<?php 

if ($ad){

	$user_fields = $ad->user()->getFields();
	
	$attachments = $ad->get_attachments();
	$ad_obj = $ad;
	$ad = $ad->info();

	if ($change)
	{
		echo '<div style="background: lime; border: 1px solid green; padding: 5px; margin-bottom: 5px;">Изменения сохранены</div>';
	}

?>

<div class="product-edit-block" id="shop-content">
	
<table class="product-edit-table" width="100%" align="left">
<tr>
	<td class="product-pic-cell" width="310px" style="padding-right: 2px;">
		<div class="product-pic">
			
			<div class="image-preview">
			
				<?php 
					if (!isset($ad['image']) || empty($ad['image']))
					{
				?>
				<img src="/img/shop/no-image.png" width="450px" />
				<div class="add-product-image">
					<span class="button blue">Добавить</span>
				<?php
					} else {
					
						$img = reset($attachments);
				?>
				<img src="/<?=$img['file_name'];?>" width="450px" />
				<div class="add-product-image">
					<span class="button blue">Изменить</span>
					
				<?php
					}
				?>
					<input type="file" name="image" class="add-product-image-loader" />
				</div>
			</div>
		</div>
		<div class="product-attachments">
			<ul class="product-attach-previews" id="product-attach-previews" style="margin-top: 18px;">
			<?php
				if ($attachments)
				{
					foreach ($attachments as $attach)
					{
				?>
				<li class="product-item" tag="<?=$attach['tag'];?>" order="<?=$attach['order'];?>">
					<img src="/<?=$attach['file_name'];?>" />
					<input type="button" value="" class="remove_button" alt="Удалить" title="Удалить" />
				</li>
				<?php 
					}
				}
			?>
			</ul>
			<div id="add-product-attach-button-container" style="display: none;">
				<div class="add-product-attach"  style="border: 1px solid #eee; text-align: center; background: #fff;">
					<span class="image-button" alt="Добавить" title="Добавить"><img src="/images/add-photo.png" width="40px" style="margin-top: 10px;" /></span>
					<input type="file" name="image" class="add-product-attach-loader" alt="Добавить" title="Добавить" />
				</div>
			</div>
			
			<div class="clear"></div>
			<div class="attach-delay-block" style="display: none;"><span><img src="/img/spinner.gif" /></span></div>
		</div><!-- product-attachments -->
		</div>
	</td>
	<td class="product-desc-cell" style="border: 0;">
	
		<div class="product-desc-block">
			<form action="" method="POST" id="form_edit_ad">
				<table class="product-desc-table" width="100%">
				<tr>
					<td style="padding-bottom: 7px;">
						<!--input type="text" name="title" value="<?=$ad['title'];?>" /-->
						<strong style="line-height: 12px;"><?=$ad['title'];?></strong>
						<a href="/shop/edit_filters/<?=$ad['id'];?>" class="float_right"><img src="/images/icons/edit.png" width="16px" title="Изменить параметры"></a>
						<br /><span class="smalltext"><?=$ad['spec'];?></span><br />
					</td>
				</tr>
				<tr>
					<td style="padding-right: 9px;">
						<textarea name="description" class="product-description" style="width: 100%;"><?=$ad['description'];?></textarea>
					</td>
				</tr>
				<tr>
					<td>
						<input type="text" name="price" value="<?=isset($ad['price'])?$ad['price']:'0';?>" style="width: 60px; height: 16px; border: 1px solid #3b88c4; padding-left: 3px; font-size: 12px; text-align: left;" />
						<select name="currency" style="height: 20px; border: 1px solid #aaa;">
							<option value="UAH" <?=(isset($ad['currency_name']) && $ad['currency_name'] == 'UAH')?' selected':'';?>>UAH</option>
							<option value="RUB" <?=(isset($ad['currency_name']) && $ad['currency_name'] == 'RUB')?' selected':'';?>>RUB</option>
							<option value="USD" <?=(isset($ad['currency_name']) && $ad['currency_name'] == 'USD')?' selected':'';?>>USD</option>
							<option value="EUR" <?=(isset($ad['currency_name']) && $ad['currency_name'] == 'EUR')?' selected':'';?>>EUR</option>
						</select>
	
						<div class="float_right">
							<input type="submit" class="button blue" value="Активировать" />
							
							<?php 
							if ($session->user()->isAdmin())
							{
							?>
							<button type="submit" name="action" value="approve" class="button red">Подтвердить</button>
							<?php 
							}
							?>
						</div>
					</td>
				</tr>
				</table>
			</form>
		</div>
	</td>
</tr>
</table><!-- .product-edit-table -->
	
</div>

<script language="javascript" type="text/javascript" src="/jscripts/jquery.ajaxfileupload.js"></script>

<script type="text/javascript">

function appendAddButton()
{	
	var block = $('#add-product-attach-button-container').html();
	var def = $('#product-attach-previews').html();
	$('#product-attach-previews').html(def+block);
	set_attach_loader($('.add-product-attach-loader'));
}

$('.product-item .remove_button').live('click', function(){
	var pr = $(this).parent();
	var tag = $(pr).attr('tag');
	var order = $(pr).attr('order');
	remove_attach(tag,order);
});

$('.add-product-image .button').live('click', function(){
	return false;
	var loader = $('.add-product-image-loader');
	loader.trigger('click');
	
});

function parse_images_object(obj)
{
	var first_image_block = $('.image-preview');
		
	var items_block = $('#product-attach-previews');
		
	if (typeof obj === 'object' && Object.keys(obj).length>0)
	{		
		var list = '';
		
		var cnt = 0;
		first_img = '';
		
		for(var key in obj)
		{
			var img = obj[key];			
			var image_url = '/'+img['file_name'];
			if (key == 1)
			{
				first_img = img['file_name'];
			}
			cnt++;
			list += '<li class="product-item" tag="'+img['tag']+'" order="'+img['order']+'">'
					+'	<img src="/'+img['file_name']+'" height="60px" />'
					+'	<input type="button" value="" class="remove_button" />'
					+'</li>';
		}
		items_block.html(list);
		if (cnt < 10) appendAddButton();
		var image = first_image_block.find('img');
		$(image).attr('src', '/'+first_img);
		var button = first_image_block.find('.button');
		$(button).text('Изменить');
	} else {
		var image = first_image_block.find('img');
		$(image).attr('src', '/img/shop/no-image.png');
		var button = first_image_block.find('.button');
		$(button).text('Добавить');
		items_block.html('');
		appendAddButton();
	}
}

function reload_images()
{
	$.ajax({
		type: "POST",
		url: "/shop/get_attach/<?=$ad['id'];?>",
		data: {
			'ad': <?=$ad['id'];?>,
			'ajax': 1
		},
		success: function(data){
			parse_images_object(data)
		},
		dataType: "json"
	});
	
	$('.attach-delay-block').css({'display':'none'});
}


function set_file_loader(el){
    $(el).ajaxfileupload({
      'action': '/shop/image_add/<?=$ad['id'];?>',
      'params': {
		'ad_id': '<?=$ad['id'];?>',
		'input_name':'image',
		'ajax': 1
      },
      'onComplete': function(response) 
	  {
		if (response){
			reload_images();
			var button = $('#add-product-image').find('.button');
			$(button).text('Изменить');
        }
      },
      'onStart': function() {
        $('.attach-delay-block').css({'display':'block'});
      },
      'onCancel': function() {
        $('.attach-delay-block').css({'display':'none'});
      }
    });
  }

function set_attach_loader(el){
    $(el).ajaxfileupload({
      'action': '/shop/attach_add/<?=$ad['id'];?>',
      'params': {
		'ad_id': '<?=$ad['id'];?>',
		'input_name':'image',
		'ajax': 1
      },
      'onComplete': function(response) {
		if (response.status != undefined){
            alert(response.message);
            reset_file_loader();
        }else {
			reload_images();
			var button = $('#add-product-image').find('.button');
			$(button).text('Изменить');
            //view_image(response);
        }
      },
      'onStart': function() {
        $('.attach-delay-block').css({'display':'block'});
      },
      'onCancel': function() {
        $('.attach-delay-block').css({'display':'none'});
      }
    });
  }
  
function remove_attach(tag, order)
{
	if (tag != false && order != false)
	{
		$('.attach-delay-block').css({'display':'block'});
		$.ajax({
			type: "POST",
			url: "<?=Request::$base_url;?>shop/remove_attach/<?=$ad['id'];?>",
			data: {
				'tag': tag,
				'order': order,
				'ajax': 1
			},
			complete: function(data){
				reload_images();
				$('#add-product-attach-button').css('display','block');
			},
			dataType: "json"
		});
	}
}

  set_file_loader($('.add-product-image-loader'));
  //set_attach_loader($('.add-product-attach-loader'));
  
<?php 
	if (!($attachments && count($attachments) >9))
	{
		echo 'appendAddButton();';
	}
?>

</script>

<?
}
?>