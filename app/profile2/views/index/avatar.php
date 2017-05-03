<?php 

	$status = $user->getStatus();
	$fields = $user->getFields();	
	
	$app_url = Request::$base_url.$request->app().$user->get('uid').'/index/avatar_change';

?>

<link rel="stylesheet" type="text/css" href="/jscripts/cropper/imgareaselect-default.css" />
<script type="text/javascript" src="/jscripts/cropper/jquery.imgareaselect.min.js"></script>
<script language="javascript" type="text/javascript" src="/jscripts/jquery.ajaxfileupload.js"></script>

<style>

#new-atatar {width: 100px; height: 100px; overflow: hidden; position: relative;}
#new-atatar img {position: absolute; }
.add-ad-attach-loader{position: absolute; font-size: 999px; top: 0px; left: 0; cursor: pointer; width: 100%; height: 100%; filter:alpha(opacity: 0); opacity: 0; z-index: 1; padding-left: 240px; margin-right: -240px;}
#image-preview {padding: 10px; border: 1px solid #888; width: 380px; text-align: center;}
#image-preview img{max-width: 360px; margin: auto;}

</style>

<form action="" method="post" enctype="multipart/form-data">

<table class="table_info" style="margin-left: 2px;">
<tr>
	<td width="230px">
		<!--fieldset class="trow2" style="height: 120px;">
		<legend><strong>Новый аватар</strong></legend-->
			<div class="float_left" style="padding: 10px 10px; background: #eee; margin: -2px 0 0 0;">
				<div id="new-atatar">
					<img id="image-cropp" src="/images/no-avatar.png" width="100px" />
				</div>
			</div>
			<div class="float_right" style="position: relative; height: 117px;">
				<div style="position: relative; overflow: hidden;">
					<span class="button blue" id="load_avatar_button" style="width: 70px; text-align: center;">Загрузить</span>
					<input type="file" name="image" class="add-ad-attach-loader" />
				</div>
				<br />				
				<a href="" class="button disable" id="save_button" style="width: 70px; text-align: center;">Сохранить</a><br />
				<br />
				<a href="<?=Request::$base_url.$request->app().$user->get('uid').'/index/';?>" class="button blue" style="margin-top: 3px; width: 70px; position: absolute; bottom: 0; text-align: center;">Отмена</a>
			</div>
		<!--/fieldset-->
	</td>
</tr>
</table>
</form>

<!-- div id="image-preview"><div -->

<script type="text/javascript">
$(document).ready(function () {
    
});

var selector_enable = false;

function run_selector()
{
	if (selector_enable == false)
	{
		/*$('#image-preview img').imgAreaSelect({
        handles: true,
		aspectRatio: '1:1',
		minWidth: 100, minHeight: 100,
        onSelectEnd: onSelected,
		persistent: true,
		x1: 0, y1: 0, x2: 100, y2: 100,
		});
		selector_enable = true;
		*/
		
	}
}

function onSelected(image, e)
{
	var img = $('#image-cropp');
	var aspect = 100 / e.width;
	var wd = Math.round(image.width * aspect);
	var left = -Math.round(e.x1 * aspect);
	var top = -Math.round(e.y1 * aspect);
	//alert(left);
	img.css({'width':wd, 'left':left, 'top': top});
}

$('.add-ad-attach-loader').ajaxfileupload({
  'action': '<?=$app_url;?>',
  'params': {
	'input_name':'image',
	'act': 'load',
	'ajax': 1
  },
  'onComplete': function(response) {
	if (response == '1')
	{
		var img_src = '<?=$app_url;?>?get_preview=1&ajax=1&ver='+Math.floor((Math.random() * 1000) + 1);
		//$('#image-preview').html('<img src="'+img_src+'" />');
		$('#image-cropp').attr("src", img_src);
		$('#image-cropp').css('width','100px');
		$('#save_button').removeClass("disable");
		$('#save_button').addClass("red");
		$('#save_button').attr("href", '<?=$app_url;?>?save_image=1');
		//run_selector();
	}
	else
	{
		alert('Произошла ошибка загрузки изображения');
	}
  },
});

$('#load_avatar_button').live('click', function(){
	var loader = $('.add-ad-attach-loader');
	loader.trigger('click');
	return false;
});

</script>