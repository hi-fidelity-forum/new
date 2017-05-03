<?php

	$expdisplay = '';
	$collapsed_name = 'cat_'.$parent->get('fid').'_c';
	if(isset($collapsed[$collapsed_name]) && $collapsed[$collapsed_name] == "display: show;")
	{
		$expcolimage = "collapse_collapsed.gif";
		$expdisplay = 'style="display: none;"';
		$expaltext = "[+]";
	}
	else
	{
        $expdisplay = '';
		$expcolimage = "collapse.gif";
		$expaltext = "[-]";
	}

?>
<table border="0" cellspacing="1" cellpadding="4" class="tborder">
    <thead>
    <tr>
        <td class="thead" colspan="5" align="center">
            <div class="expcolimage"><img src="/images/<?=$expcolimage;?>" id="cat_<?=$parent->get('fid');?>_img" class="expander" alt="<?=$expaltext;?>" title="<?=$expaltext;?>" /></div>
            <div><strong><a href="/forum/forum-<?=$parent->get('fid');?>.html"><?=$parent->get('name');?></a></strong></div>
        </td>
    </tr>
    </thead>
    <tbody id="cat_<?=$parent->get('fid');?>_e" <?=$expdisplay;?>>
    <tr>
        <td class="tcat" colspan="2"><span class="smalltext"><strong>Форум</strong></span></td>
        <td class="tcat" width="85" align="center" style="white-space: nowrap"><span class="smalltext"><strong>Тем</strong></span></td>
        <td class="tcat" width="85" align="center" style="white-space: nowrap"><span class="smalltext"><strong>Сообщений</strong></span></td>
        <td class="tcat" width="200" align="center"><span class="smalltext"><strong>Последнее сообщение</strong></span></td>
    </tr>

<?php 
  
    $even = 1;
    
    foreach ($subforums as $item){
    
        $even = $even==1?2:1;
    
        $info = $item->forum_info();
    
    ?>
    
    <!-- start: forumbit_depth2_forum -->
    
    <tr class="trow<?=$even;?> forum_depth">
        <td class="trow<?=$even;?>" align="center" valign="top" width="1"><img src="<?=($info['is_read']?'/images/off.gif':'/images/on.gif');?>" title="Нажмите чтобы пометить данный форум как прочитанный" class="ajax_mark_read" id="mark_read_<?=$item->get('fid');?>" style="cursor: pointer;"></td>
        
        <td valign="top">
            <strong class="category_name"><a href="/forum/forum-<?=$item->get('fid');?>.html"><?=$item->get('name');?></a></strong>
            <div class="category_item">
				<?=$item->get('description');?>
            
            <?php 
            if ($sub_list = $item->subforums(TRUE))
			{
				?>
				<div class="sub_categoryes">
					<?php
						foreach ($sub_list as $sub) {
					?>
						<span class="subcategories_title">
							<a href="/forum/forum-<?=$sub['fid'];?>.html" title="<?=$sub['name'];?>"><?=$sub['name'];?></a>
						</span>
					<?php
						}
					?>
				</div><!-- sub_categoryes -->
            <?
            }
            ?>
            </div>
        </td>
        
        <td valign="top" align="center" style="white-space: nowrap"><?=number_format($info['threads'], 0, ',', ' ');?></td>
        <td valign="top" align="center" style="white-space: nowrap"><?=number_format($info['posts'], 0, ',', ' ');?></td>
        <td valign="top" align="right" style="white-space: nowrap; width: 200px;">
            <span class="smalltext">
            <a href="/forum/thread-<?=$info['lastposttid'];?>-lastpost.html" title="<?=$info['lastpostsubject'];?>"><strong><?=View::cutString($info['lastpostsubject'],25);?></strong></a>
            <br><?=View::format_date($info['lastpost']);?><br>Автор: <a href="/profile/<?=$info['lastposteruid'];?>"><?=$info['lastposter'];?></a></span>
            <!-- end: forumbit_depth2_forum_lastpost -->
        </td>
    </tr>
    <!-- end: forumbit_depth2_forum --><!-- start: forumbit_depth2_forum -->
    
    <?php
        
    }
?>
</tbody>
</table>
<br />