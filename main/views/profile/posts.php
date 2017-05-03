<?php 

	$parser = new Parser();
	$User = $session->isAuth()?$session->user():false;
	
	$is_moder = $User?$User->isModer():false;
	$is_admin = $User?$User->isAdmin():false;

	$paging_block = $posts->createPageLinks('?page={page}');
	
?>

<table border="0" cellspacing="1" cellpadding="4" class="tborder" style="clear: both;" width="100%">

<tr>
	<td class="tcat"><span class="smalltext"><strong>Сообщение</strong></span></td>
</tr>
<tr><td>	

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

<table width="100%" style="margin-bottom: 10px; border: 1px solid #aaa;" cellpadding="5px">
<tr style="background: #ccc;">
	<td style="font-size: 12px;"><a href="/forum/thread-<?=$item['tid'];?>-post-<?=$item['pid'];?>.html"><?=$item['subject'];?></a> / <?=View::format_date($item['dateline']);?></td>
	<td width="150" align="center" style="white-space: nowrap"><span class="smalltext"><strong>Форум</strong></span></td>
</tr>
<tr>
	<td class="trow1 forumdisplay_regular">
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
	
	<td align="center" class="trow1 forumdisplay_regular forum_depth"><a href="/forum/forum-<?=$item['fid'];?>.html"><?=$item['forum_name'];?></a></td>

</tr>
</table>
    
    <?php
        
    }
  }
  else 
  {
	echo '<span class="smalltext">Сообщения отсутствуют</span>';
  }
    
?>
			
<div class="pagination float_left" style="margin-top: 7px;">

<?php 

echo $paging_block;

?>

</div>

<div class="clear"></div>

	</td>
</tr>

</table>