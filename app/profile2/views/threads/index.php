<?php 


	$User = $session->isAuth()?$session->user():false;
	
	$is_moder = $User?$User->isModer():false;
	$is_admin = $User?$User->isAdmin():false;

	$paging_block = $threads->createPageLinks('?page={page}');
?>
<div style="padding-left: 5px;">

<style type="text/css">
	.tborder .trow1 {background: #eee;}
</style>

<table border="0" cellspacing="1" cellpadding="4" class="tborder" style="clear: both; border: none;">
<?php 
if ($threads->getTotalCount() > 0) 
  {
?>

<tr>
	<td class="tcat" colspan="3" style="background: #eee;"><span class="smalltext"><strong>Название темы</strong></span></td>
	<td class="tcat" width="100" align="center" style="white-space: nowrap; background: #eee;"><span class="smalltext"><strong>Раздел</strong></span></td>
	<td class="tcat" width="60" align="center" style="white-space: nowrap; background: #eee;"><span class="smalltext"><strong>Сообщений</strong></span></td>
	<td class="tcat" width="60" align="center" style="white-space: nowrap; background: #eee;"><span class="smalltext"><strong>Просмотров</strong></span></td>
	<td class="tcat" width="150" align="center" style="background: #eee;"><span class="smalltext"><strong>Последнее сообщение</strong></span></td>
	<?php
	
	if ($session->user()->isModer())
	{
		echo '<td class="tcat" align="center" width="1" style="background: #eee;"><input type="checkbox" name="allbox" onclick="selectAll(this)"></td>';
	}
	
	?>
</tr>

<?php

  $even = false;
  $sticky = false;
  $last_time = TIME_NOW-604800;
  
  $is_announcements = false;
  
	$ppp = (($session->isAuth()) && ($session->user()->get('ppp') >0))?$session->user()->get('ppp'):20;
	
	$forum_lastread = 0;
	
	$first_thread = reset($threads);
	
	foreach ($threads->result() as $item){
	
	$even = !$even;
    
	$bold = false;
	
	if (isset($forum_info['is_read']) && $forum_info['is_read'] == true)
	{
		//if forum is readed
	} else {
		
		if ($last_time<$forum_lastread) $last_time = $forum_lastread;

		if (isset($item['is_reads']) && $item['is_reads'] && ($item['is_read_dateline'] > $item['lastpost']) || ($last_time > $item['lastpost']))
		{
			$bold = false;
		}
		else 
		{
			$bold = true;
		}
	}
	
	?>
	
<tr>
	<td align="center" class="trow<?=$even?'2':'1';?> forumdisplay_regular" width="2%">
	<?php 
		
		$t = 'empty';
		
		$is_moder = $session->user()->isModer();
		$is_admin = $session->user()->isAdmin();
		
		if ($item['replies']>0 && $item['replies']<=50) $t = 'newth';
		if (!isset($item['is_reads']) && $item['replies']<=50) $t = 'off';
		if ($item['replies']>50) $t = 'hot';
		if ($item['closed']) $t = 'offlock';
		
		//$icon = $t.'folder';
		$icon = $t;
		
		echo '<img src="/images/'.$icon.'.gif">';
	?>
	</td>
    <td class="trow<?=$even?'2':'1';?> forumdisplay_regular" width="0px" style="padding: 0;"></td>

	<td class="trow<?=$even?'2':'1';?> forumdisplay_regular">
		
		<?php if ($item['attachmentcount']>0)
		{
			echo '<div style="float: right;"><img src="/images/paperclip.gif" alt="" title="Прикреплений: '.$item['attachmentcount'].'."></div>';
		} ?>
		
		<div>
			<span>
				<?php if ($bold){
				?>
				<a href="/forum/thread-<?=$item['tid'];?>-newpost.html">
					<img src="/images/jump.gif" alt="Перейти к первому непрочитанному сообщению" title="Перейти к первому непрочитанному сообщению">
				</a>
				<?php } ?>
				<a href="/forum/thread-<?=$item['tid'];?>.html" class="<?=($is_moder || $is_admin)?'subject_editable ':'';?><?=$bold?'subject_new':'subject_old';?>" id="tid_<?=$item['tid'];?>"><?=$item['subject'];?></a>
				<span class="smalltext">
					<?php
					
					$item['posts'] = $item['replies'] + 1;
					
					$pg_cnt = $item['posts'] / $ppp;
					$pg_cnt = ceil($pg_cnt);
					
					if ($pg_cnt>1)
					{
						echo '<br /><nobr> <a href="/forum/thread-'.$item['tid'].'.html">1</a> ';
						echo ' <a href="/forum/thread-'.$item['tid'].'-page-2.html">2</a>';
						
						if ($pg_cnt>2) echo ' <a href="/forum/thread-'.$item['tid'].'-page-3.html">3</a>';
						if ($pg_cnt>3) echo ' <a href="/forum/thread-'.$item['tid'].'-page-4.html">4</a>';
						if ($pg_cnt>4 && $pg_cnt == 5) echo ' <a href="/forum/thread-'.$item['tid'].'-page-5.html">5</a>';
						if ($pg_cnt > 5) echo ' ... <a href="/forum/thread-'.$item['tid'].'-page-'.$pg_cnt.'.html">'.$pg_cnt.'</a> </nobr>';
					}
					?>
					
				</span>
			</span>
			
		</div>
	</td>

	<td align="center" class="trow<?=$even?'2':'1';?> forumdisplay_regular forum_depth"><a href="/forum/forum-<?=$item['fid'];?>.html"><?=$item['forum_name'];?></a></td>
	<td align="center" class="trow<?=$even?'2':'1';?> forumdisplay_regular forum_depth"><a href="javascript:MyBB.whoPosted(<?=$item['tid'];?>);"><?=number_format($item['replies'], 0, ',', ' ');?></a></td>
	<td align="center" class="trow<?=$even?'2':'1';?> forumdisplay_regular forum_depth"><?=number_format($item['views'], 0, ',', ' ');?></td>
	
	<td class="trow<?=$even?'2':'1';?> forumdisplay_regular forum_depth" style="white-space: nowrap; text-align: left;">
		<a href="/forum/thread-<?=$item['tid'];?>-lastpost.html" title="Последнее сообщение">
			<img src="/images/ps_minioff.gif" />
			<span class="lastpost smalltext"><?=View::format_date($item['lastpost']);?></span>
			
		</a>
		<br />
		<a href="/forum/user-<?=$item['lastposteruid'];?>.html"><?=$item['lastposter'];?></a></span>
	</td>
	<?php
	
	if ($session->user()->isModer())
	{
		echo '<td class="trow'.($even?'2':'1').'" align="center" style="white-space: nowrap"><input type="checkbox" class="checkbox" name="inlinemod_'.$item['tid'].'" tid="'.$item['tid'].'" value="1" style="vertical-align: middle;"></td>';
	}
	
	?>
	
</tr>
    
    <?php
        
    }
    
?>

<tr>
	<td  align="left" colspan="<?=($is_moder || $is_admin)?8:7;?>">
			
<div class="pagination float_left" style="margin-top: 7px;">

<?php 

echo $paging_block;

?>

</div>

<?php
	if ($session->user()->isModer())
	{
?>
<table width="100%" align="center" border="0">
			<tbody><tr class="">
				<td align="left" valign="top"></td>
				<td align="right" valign="top" style="text-align: right !important;"><!-- start: search_results_threads_inlinemoderation -->

<form action="" method="post" id="moderation_form">

<span class="smalltext"><strong>Редактирование темы:</strong></span>
<select name="action" id="action">
	<option value="">--</option>
	<?php 
	if ($session->user()->isAdmin())
	{
	?>
	<optgroup label="Административные инструменты">
		<option value="movetosafe">Отправить в сейф</option>
		<option value="moveto">Переместить</option>
	</optgroup>
	<?php
	}
	?>
	<optgroup label="Стандартные инструменты">
		<option value="multiclosethreads">Закрыть</option>		
		<!-- option value="multiopenthreads">Открыть тему</option>
		<option value="multistickthreads">Прикрепить тему</option>
		<option value="multiunstickthreads">Открепить тему</option>
		<option value="multideletethreads">Удалить тему</option>
		<option value="multimovethreads">Переместить тему</option>
		<option value="multiapprovethreads">Подтвердить тему</option>
		<option value="multiunapprovethreads">Отклонить тему</option -->
	</optgroup>
	
</select>
<input type="submit" class="button red" name="go" value="Выполнить (0)" id="inline_go">&nbsp;
<input type="button" onclick="javascript:inlineModeration.clearChecked();" value="Очистить" class="button red">
<input type="hidden" name="url" value="/profile/<?=$user->get('uid');?>/threads">
<input type="hidden" name="moditems" id="moditems" value="">

<div style="display: none;" id="forums_list">
	<span class="smalltext"><strong>Выберите раздел</strong></span>
	<select name="fid" size="1" id="forum_id">
	<option value="">--</option>
	<?php 
		
		$forum = new Model_Forum();
		
		function recurs($data)
		{
			$result = false;
			$inx = $data['inx'];
			$cls = $data['cls'];
			$str = $data['str'];
			$sr = $data['sr'];
			
			if ($sub = $cls->get_forums(false, $inx))
			{
				
				foreach ($sub as $item)
				{
					$fid = $item['fid'];
					$result[$fid] = $item;
					$str .= '<option value="'.$fid.'"> '.$sr.' '.$item['name'].'</option>';
					$result[$fid] = $s = recurs(array('inx'=>$fid, 'str'=>$str, 'cls'=>$cls, 'sr'=>$sr.'--'));
					$str = $s['str'];
				}
				$data[$inx]['sub'] = $result;
			}
			$data['str'] = $str;				
			return $data;
		}
		
		$list = recurs(array('inx'=>0, 'cls'=>$forum, 'sr'=>'', 'str'=>''));
		
		echo $list['str'];
		
	?>
	</select>
</div>

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
				selected_items[selected_items.length] = $(this).attr('tid');
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
		var tid = $(this).attr('tid');
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
	  echo '<span style="font-size: 14px; padding-left: 10px;">Темы отсутствуют</span>';
  }
?>
	</td>
</tr>

</table>
</div>