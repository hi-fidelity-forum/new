<div id="reply_form">

<form action="" method="POST" id="message_form">

<table width="100%" id="editor_box" cellspacing="1">	
	<tr><td class="thead" colspan="4"><strong class="smalltext">Создать ответ</strong></td></tr>
	<tr>
		<td style="text-align: center !important; padding-top: 5px; width: 150px;">
			<?php 
				$user = $session->user();
				
				if ($avatar = $user->get('avatar'))
				{
					echo '<!-- '.$avatar.' -->';
					if (mb_strpos($avatar, 'http') === false)
					{
						$avatar = str_replace('./', '/', $avatar);
						
						$m = explode('?', $avatar);
						$p = $m[0];
						if (!file_exists(DOCROOT.$p))
						{
							$avatar = '/images/avatars/hf.jpg';
						}
						
						$avatar = ltrim($avatar, '/');
						$avatar = '/'.$avatar;
					}
				}
				else 
				{
					$avatar = '/images/avatars/hf.jpg';
				}
			?>
			<a href="/profile/<?=$user->get('uid');?>"><strong><?=$user->stylizedUserName();?></strong></a>
			<div class="user_status">
			<?php
				$status = $user->getStatus();
			?>
				<div class="item"><?=$status['title'];?></div>
				<div class="item"><?=$status['stars'];?>
				<div class="item"><?=$status['image']?'<img src="/'.$status['image'].'" />':'';?></div>
			</div>
			<img src="<?=$avatar;?>" style="margin-top: 3px;" />
		</td>
		<td style="padding: 3px;">
			<table width="100%">
			<tr>
				<td style="padding: 5px 0;">
					<input type="submit" value="Отправить" name="reply" class="button blue float_left" />
					<div id="bbcode_bb_bar">
						<a href="#" id="b" title="Жирный"><img src="/images/bbimage/bold.gif" style="border: none;"></a>
						<a href="#" id="i" title="Наклон"><img src="/images/bbimage/italic.gif" style="border: none;"></a>
						<a href="#" id="u" title="Подчеркивание"><img src="/images/bbimage/underline.gif" style="border: none;"></a>
						<span class="toolbar_sep">|</span>
						<a href="#" id="quote" title="Цитата"><img src="/images/bbimage/quote.gif" style="border: none;"></a>
						<a href="#" id="url" title="Ссылка"><img src="/images/bbimage/link.gif" style="border: none;"></a>
						<!-- a href="#" id="img" title=""><img src="/images/bbimage/image.gif" style="border: none;"></a -->
						
					</div>
					<div id="smiles_block" style="display: block;">
							<a href="#" id="smiley" title="Смайлы">
								<img src="/img/icons/smiley.gif" style="border: none;">
							</a>
							<?php 
							if (isset($smilies))
							{
								echo '<table id="table_smilies" style="width: 100%; height: 320px; margin: 3px 0 0; border: 1px solid #888;">';
								echo '<tr><td class="thead" colspan="4"><strong>Смайлики</strong></td></tr>';
								echo '<tr>';
								$cnt = 1;
								foreach ($smilies as $smile)
								{
									echo '<td><img src="'.$smile['image'].'" border="0" class="smilie" alt="'.$smile['find'].'" style="cursor: pointer;" title="'.$smile['name'].'"></td>';
									$cnt = $cnt + 1;
									if ($cnt >4){
										$cnt = 1;
										echo '</tr></tr>';
									}
								}
								echo '</tr></table>';
							}
							?>
						</div>
					<div class="clear" style="margin-bottom: 3px;"></div>
					<textarea name="message" id="message"></textarea>
				</td>
			</tr>
			</table>
		</td>
	</tr>
	
</table>

</form>

</div>

<script type="text/javascript">
	
	$('#table_smilies .smilie').click(function() {
		var el = $(this);
		var alt = el.attr('alt');
		insert(' '+alt+' ', '');
		hide_smiles();
		return false;
	});
	
	$('#bbcode_bb_bar a').click(function() {
      var button_id = $(this).attr("id");
      var start = '['+button_id+']';
      var end = '[/'+button_id+']';

	  var param="";
	  if (button_id=='img')
	  {
	     param=prompt("Enter image URL","http://");
		 if (param)
			start+=param;
		 }
		else if (button_id=='url')
		{
			param=prompt("Enter URL","http://");
			if (param) 
				start = '[url=' + param + ']';
		}
      insert(start, end);
      return false;
    });
	
  function insert(start, end) 
  {
	
	var element = $('#message').get(0);
	
    if (document.selection) {
       element.focus();
       sel = document.selection.createRange();
       sel.text = start + sel.text + end;
    } else if (element.selectionStart || element.selectionStart == '0') {
       element.focus();
       var startPos = element.selectionStart;
       var endPos = element.selectionEnd;
       element.value = element.value.substring(0, startPos) + start + element.value.substring(startPos, endPos) + end + element.value.substring(endPos, element.value.length);
    } else {
      element.value += start + end;
    }
  }
  
  var smile_block_show = false;
  
  $('#smiley').click(function()
  {
	  if (smile_block_show == false)
	  {
		  show_smiles();
	  } 
	  else 
	  {
		  hide_smiles();
	  }
	  return false;
  });
  
  function show_smiles()
  {
	  smile_block_show = true;
	  $('#table_smilies').css('display','table');
	  $('#smiley').addClass('active');
  }
  
  function hide_smiles()
  {
	  smile_block_show = false;
	  $('#table_smilies').css('display','none');
	  $('#smiley').removeClass('active');
  }
  
  
</script>