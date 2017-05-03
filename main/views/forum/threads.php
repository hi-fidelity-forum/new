<?php 

	$User = $session->user();
	
	$is_moder = $session->user()->isModer();
	$is_admin = $session->user()->isAdmin();

	$forum_info = $parent->forum_info();
	
	$fid = $parent->get('fid');

	//create paging block
	$paging_block = $threadsPage->createPageLinks('/forum/forum-'.$fid.'-page-{page}.html');

	$threads = $threadsPage->result();
	
if ($session->user()->isModer())
{
?>
<script type="text/javascript">
<!--
	var go_text = "Выполнить";
	var all_text = "10474";
	var inlineType = "forum";
	var inlineId = <?=$fid;?>;
// -->
</script>

<script type="text/javascript" src="/jscripts/inline_moderation.js?ver=1600"></script>
<script type="text/javascript" src="/jscripts/inline_edit.js?ver=1400"></script>
<?php 
}
?>
<div class="pagination float_left">

<?php 

echo $paging_block;

?>

</div>

<?php 
if ($session->isAuth())
{
?>
<div class="float_right">
	<a href="/forum/newthread.php?fid=<?=$fid;?>" class="button_newthead">Создать тему</a>
</div>
<?php
}
?>

<table border="0" cellspacing="1" cellpadding="4" class="tborder" style="clear: both;">
<tr>
		<td class="thead" colspan="<?=($is_moder || $is_admin)?7:6;?>">
			<div style="float: right;">
<span class="smalltext"><strong><a href="misc.php?action=markread&amp;fid=<?=$fid;?>&amp;my_post_key=<?=$session->postKey();?>">Отметить этот форум прочитанным</a>
<?php 
if ($session->isAuth())
{
?>
	| <a href="/forum/usercp2.php?action=addsubscription&amp;type=forum&amp;fid=<?=$fid;?>&amp;my_post_key=<?=$session->postKey();?>">Подписаться на этот форум</a>
<?php
}
?>
	</strong></span>
			</div>
			<div>
				<strong><?=$parent->get('name');?></strong>
			</div>
		</td>
	</tr>
<tr>
		<td class="tcat" colspan="3" width="66%"><span class="smalltext"><strong>Тема / Автор</strong></span></td>
		<td class="tcat" align="center" width="7%"><span class="smalltext"><strong><a href="forum-<?=$fid;?>.html?datecut=0&amp;sortby=replies&amp;order=desc">Ответов</a> </strong></span></td>
		<td class="tcat" align="center" width="7%"><span class="smalltext"><strong><a href="forum-<?=$fid;?>.html?datecut=0&amp;sortby=views&amp;order=desc">Просмотров</a> </strong></span></td>
		
		<td class="tcat" align="right" width="20%"><span class="smalltext"><strong><a href="forum-<?=$fid;?>.html?datecut=0&amp;sortby=lastpost&amp;order=desc">Последнее сообщение</a> <!-- start: forumdisplay_orderarrow -->
		<?php
			if ($is_moder || $is_admin)
			{
				echo '<td class="tcat" align="center" width="1"><input type="checkbox" name="allbox" onclick="inlineModeration.checkAll(this)"></td>';
			}
		?>
	
	</strong></span></td>
</tr>
<?php

  $even = false;
  $sticky = false;
  $last_time = TIME_NOW-604800;
  
  $is_announcements = false;
  
  if ($announcements)
  {
	$is_announcements = true;
?>
	<tr>
		<td class="trow_sep" colspan="<?=(($is_moder || $is_admin)?7:6);?>">Объявление форума</td>
	</tr>
  
<?php 

	foreach ($announcements as $ann)
	{
	
		if($ann['startdate'] > $User->get('lastvisit') && !isset($_COOKIE[$ann['aid']]))
		{
			$class = ' class="subject_new"';
			$folder = "on.gif";
		}
		else
		{
			$class = ' class="subject_old"';
			$folder = "off.gif";
		}

		// Mmm, eat those announcement cookies if they're older than our last visit
		if(isset($_COOKIE[$ann['aid']]) && ($_COOKIE[$ann['aid']] < $User->get('lastvisit')))
		{
			unset($_COOKIE[$ann['aid']]);
		}
	
?>
	<tr>
		<td align="center" class="trow1" width="2%"><img src="/images/<?=$folder;?>" alt=""></td>
		<td align="center" class="trow1" width="2%">&nbsp;</td>
		<td class="trow1">
			<a href="announcement-<?=$ann['aid'];?>.html" <?=$class;?>><?=$ann['subject'];?></a>
			<div class="author smalltext"><a href="/profile/<?=$ann['uid'];?>"><?=$ann['username'];?></a></div>
		</td>
		<td align="center" class="trow1">-</td>
		<td align="center" class="trow1">-</td>

		<td class="trow1" style="white-space: nowrap; text-align: right"><span class="smalltext"><?=View::format_date($ann['startdate']);?></span></td>
		<?=($is_moder || $is_admin)?'<td class="trow1">&nbsp;</td>':'';?>
	</tr>
<?php 
	}
  }

  if ($threads) {

	$ppp = $User->get('ppp')?$User->get('ppp'):20;
	
	$forum_lastread = $parent->get('lastread');
	
	$first_thread = reset($threads);
	
	if ($sticky == false && $is_announcements == true && $first_thread['sticky']==0)
	{
		echo '<tr><td class="trow_sep" colspan="'.(($is_moder || $is_admin)?7:6).'">Темы форума</td></tr>';
	}
	
    foreach ($threads as $item){
	
	if ($item['sticky']>0 && $sticky == false)
	{
		$sticky = true;
		echo '<tr><td class="trow_sep" colspan="'.(($is_moder || $is_admin)?7:6).'">Важные темы</td></tr>';
	} elseif ($sticky == true && $item['sticky']==0)
	{
		$sticky = false;
		echo '<tr><td class="trow_sep" colspan="'.(($is_moder || $is_admin)?7:6).'">Темы форума</td></tr>';
	}
	
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
						echo '(Страницы: ';
						
						echo '<a href="/forum/thread-'.$item['tid'].'.html">1</a> ';
						echo ' <a href="/forum/thread-'.$item['tid'].'-page-2.html">2</a>';
						
						if ($pg_cnt>2) echo ' <a href="/forum/thread-'.$item['tid'].'-page-3.html">3</a>';
						if ($pg_cnt>3) echo ' <a href="/forum/thread-'.$item['tid'].'-page-4.html">4</a>';
						if ($pg_cnt>4 && $pg_cnt == 5) echo ' <a href="/forum/thread-'.$item['tid'].'-page-5.html">5</a>';
						if ($pg_cnt > 5) echo ' ... <a href="/forum/thread-'.$item['tid'].'-page-'.$pg_cnt.'.html">'.$pg_cnt.'</a>';
						
						echo ' )';
					}
					?>
					
				</span>
			</span>
			<div class="author smalltext"><a href="/profile/<?=$item['uid'];?>"><?=$item['username'];?></a></div>
		</div>
	</td>

	<td align="center" class="trow<?=$even?'2':'1';?> forumdisplay_regular forum_depth"><a href="javascript:MyBB.whoPosted(<?=$item['tid'];?>);"><?=number_format($item['replies'], 0, ',', ' ');?></a></td>
	<td align="center" class="trow<?=$even?'2':'1';?> forumdisplay_regular forum_depth"><?=number_format($item['views'], 0, ',', ' ');?></td>
	
	<td class="trow<?=$even?'2':'1';?> forumdisplay_regular forum_depth" style="white-space: nowrap; text-align: left;">
		<span class="lastpost smalltext"><?=View::format_date($item['lastpost']);?><br>
		<a href="/forum/thread-<?=$item['tid'];?>-lastpost.html">Последнее сообщение</a>: <a href="/profile/<?=$item['lastposteruid'];?>"><?=$item['lastposter'];?></a></span>
	</td>
	
		<?php
			if ($is_moder || $is_admin)
			{
				echo '<td class="trow'.($even?'2':'1').'" align="center" style="white-space: nowrap"><input type="checkbox" class="checkbox" name="inlinemod_'.$item['tid'].'" id="inlinemod_'.$item['tid'].'" value="1"></td>';
			}
		?>

</tr>
    
    <?php
        
    }
  }
    
?>

<tr>
		<td class="tfoot" align="left" colspan="<?=($is_moder || $is_admin)?7:6;?>">
			
<div class="pagination float_left" style="margin-top: 7px;">

<?php 

echo $paging_block;

?>

</div>

<div class="float_right">
	<a href="/forum/newthread.php?fid=<?=$fid;?>" class="button_newthead" style="margin-top: 7px !important;">Создать тему</a>
</div>
<div class="clear"></div>

	</td>
</tr>

</table>

<?php 
if ($is_admin || $is_moder) {
?>
<br />
<div class="float_right moderations_block" style="text-align: right;">

<form action="/forum/moderation.php" method="post">
<input type="hidden" name="my_post_key" value="<?=$session->postKey();?>">
<input type="hidden" name="fid" value="<?=$fid;?>">
<input type="hidden" name="modtype" value="inlinethread">
<span class="smalltext"><strong>Модерирование темы:</strong></span>
<select name="action">
	<option value="delayedmoderation"></option>
	<optgroup label="Стандартные инструменты">
		<option value="multiclosethreads" selected="selected">Закрыть темы</option>
		<option value="multiopenthreads">Открыть темы</option>
		<option value="multistickthreads">Прикрепить темы</option>
		<option value="multiunstickthreads">Открепить темы</option>
		<?php if ($is_admin) echo '<option value="multideletethreads">Удалить темы</option>'; ?>
		<option value="multimovethreads">Переместить темы</option>
		<option value="multiapprovethreads">Подтвердить темы</option>
		<option value="multiunapprovethreads">Отклонить темы</option>
	</optgroup>
	
</select>
<input type="submit" class="button" name="go" value="Выполнить (0)" id="inline_go">&nbsp;
<input type="button" onclick="javascript:inlineModeration.clearChecked();" value="Очистить выделение" class="button">
</form>
</div>

<?php
}
?>

<div class="clear"></div>
