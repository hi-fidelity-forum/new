<?php 

	$parser = new Parser();
	$User = $session->isAuth()?$session->user():false;
	
	$is_moder = $User?$User->isModer():false;
	$is_admin = $User?$User->isAdmin():false;

	$paging_block = $posts->createPageLinks('?page={page}');
	
?>

<table border="0" cellspacing="1" cellpadding="4" class="tborder" style="clear: both; border: none;" width="100%">

<!-- tr>
	<td class="tcat">
		<span class="smalltext"><strong>Сообщение</strong></span>
		
	</td>
	
</tr-->

<tr><td style="padding: 0 0 0 4px;">	

<?php

  $even = false;
  $sticky = false;
  $last_time = TIME_NOW-604800;
  
  $is_announcements = false;
  
  if ($posts->getTotalCount() > 0)
  {

	foreach ($posts->result() as $item){
	
	$even = !$even;

?>

<table width="100%" style="margin-bottom: 10px; border: none;" cellpadding="5px">
<tr style="background: #eee;">
	<td style="font-size: 12px;"><a href="/forum/thread-<?=$item['tid'];?>-post-<?=$item['pid'];?>.html"><?=$item['subject'];?></a> <span style="font-size: 11px; color: #555;">/ <?=View::format_date($item['dateline']);?></span></td>
	<td width="150" align="center" style="white-space: nowrap">
		<span class="smalltext"><a href="/forum/forum-<?=$item['fid'];?>.html"><?=$item['forum_name'];?></a></span>
	</td>
	<?php
	
	if ($session->user()->isModer())
	{
		echo '<td width="10"><input type="checkbox" class="checkbox float_right" name="inlinemod_'.$item['tid'].'" pid="'.$item['pid'].'" value="1" style="vertical-align: middle;"></td>';
	}
	
	?>
</tr>
<tr>
	<td colspan="<?=$session->user()->isModer()?'3':'2';?>" class="trow1 forumdisplay_regular" style="background: #fff;">
		<?php 
			$post = View::htmlspecialchars_uni($item['message']);
			// What we do here is parse the post using our post parser, then strip the tags from it
			$parser_options = array(
				'allow_html' => 0,
				'allow_mycode' => 1,
				'allow_smilies' => 0,
				'allow_imgcode' => 0,
			);
			$post = $parser->parse_message($post, $parser_options);
			echo $post;
		?>
	</td>

</tr>
    
    <?php
        
    }
    
?>

</table>
			
<div class="pagination float_left" style="margin-top: 7px; width: 100%;">

<?php 

echo $paging_block;

?>

<?php
	
		if ($session->user()->isModer())
		{
			echo '<div class="float_right">Выбрать все <input type="checkbox" name="allbox" onclick="selectAll(this)" class="float_right" /></div>';
		}
		
		?>

</div>

<?php
	if ($session->user()->isModer())
	{
?>
<table width="100%" align="center" border="0">
			<tbody><tr class="">
				<td align="left" valign="top"></td>
				<td align="right" style="text-align: right;" valign="top"><!-- start: search_results_threads_inlinemoderation -->

<form action="" method="post" id="moderation_form" class="float_right">

<span class="smalltext"><strong>Редактирование сообщений:</strong></span>
<select name="action" id="action">
	<option value="">--</option>
	<?php 
	if ($session->user()->isAdmin())
	{
	?>
	<optgroup label="Административные инструменты">
		<option value="remove">Удалить</option>
	</optgroup>
	<?php
	}
	?>
	<optgroup label="Стандартные инструменты">
		<!-- option value="multiclosethreads">Закрыть</option -->
	</optgroup>
	
</select>
<input type="submit" class="button red" name="go" value="Выполнить (0)" id="inline_go">&nbsp;
<input type="button" onclick="javascript:inlineModeration.clearChecked();" value="Очистить" class="button red">
<input type="hidden" name="url" value="/profile/<?=$user->get('uid');?>/posts">
<input type="hidden" name="moditems" id="moditems" value="">


</form>

</td>
</tr>
</tbody></table>
		
<script type="text/javascript">

	var selected_count = 0;
	var selected_items = [];
	
	$('select#action').change(function(){
		var action = $(this).prop('value');
		if (action == 'moveto')
		{
			$('#forums_list').css('display', 'block');
		}
		else {
			$('#forums_list').css('display', 'none');
		}
	});
	
	$('#moderation_form').submit(function()
	{
		var is_items = false;
		$('input.checkbox').each(function (i) {
			var check = $(this).prop('checked');
			if (check == true) 
			{
				if (is_items == false) is_items = true;
				selected_items[selected_items.length] = $(this).attr('pid');
			}
		});
		if (is_items == false)
		{
			alert('Нет выбраных объектов');
			return false;
		}
		else
		{
			if ($('select#action').prop('value') == 'moveto' && $('select#forum_id').prop('value') == '')
			{
				alert('Не выбран раздел куда переместить темы');
				return false;
			}
			$('#moditems').val(selected_items);
			return true;
		}
	});
	
	function selectAll(el)
	{
		var check = $(el).prop('checked');
		var parent = $(el).parents('table');
		parent.find('input.checkbox').each(function (i) {
			$(this).prop('checked',check);
			var parent = $(this).parent().parent();
			if (check == true)
			{
				selected_count = selected_count + 1;
				parent.toggleClass('trow_selected');
			}
			else
			{
				parent.removeClass('trow_selected');
			}
		});
		if (check == false) 
		{
			selected_count = 0;
			
		}
		updateSelectedCount();	
	}
	
	function updateSelectedCount()
	{
		$('#inline_go').val('Выполнить ('+selected_count+')');
	}
	
	$('input.checkbox').change(function(){
		var check = $(this).prop('checked');
		var pid = $(this).attr('pid');
		var parent = $(this).parent().parent();
		if (check == true)
		{
			selected_count = selected_count + 1;
			parent.toggleClass('trow_selected');
		}
		else
		{
			if (selected_count > 0) selected_count = selected_count - 1;
			parent.removeClass('trow_selected');
		}
		updateSelectedCount();
	});
	
</script>
		
<?php 
	}
	//trow_selected
?>

<div class="clear"></div>
<?php 
  }
  else 
  {
	  echo '<span style="font-size: 14px; padding-left: 5px;">Сообщения отсутствуют</span>';
  }
?>
	</td>
</tr>

</table>