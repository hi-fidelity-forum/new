<table border="0" cellspacing="1" cellpadding="4" class="tborder">
<tr>
    <td class="thead" colspan="5" align="center"><strong>Подфорумы в '<?=$parent->get('name');?>'</strong></td>
</tr>
<tr>
<td class="tcat" width="2%">&nbsp;</td>
<td class="tcat" width="59%"><span class="smalltext"><strong>Форум</strong></span></td>
<td class="tcat" width="7%" align="center" style="white-space: nowrap"><span class="smalltext"><strong>Тем</strong></span></td>
<td class="tcat" width="7%" align="center" style="white-space: nowrap"><span class="smalltext"><strong>Сообщений</strong></span></td>
<td class="tcat" width="15%" align="center"><span class="smalltext"><strong>Последнее сообщение</strong></span></td>
</tr>

<?php 

	$even = 1;
    
    foreach ($subforums as $item){
    
        $even = $even==1?2:1;
    
        $info = $item->forum_info();
		
    ?>
    
    <!-- start: forumbit_depth2_forum -->
    
    <tr class="trow<?=$even;?> forum_depth">
		<?php 
			if ($info['is_read'] || ($item->get('lastread') && $item->get('lastread')>$item->get('lastpost')))
			{
		?>
        <td class="trow<?=$even;?>" align="center" valign="top" width="1"><img src="/images/off.gif" title="Нет новых сообщений"></td>
		<?php 
		} else {
		?>
		<td class="trow<?=$even;?>" align="center" valign="top" width="1"><img src="/images/on.gif" class="ajax_mark_read" id="mark_read_<?=$item->get('fid');?>" style="cursor: pointer;"></td>
        <?php 
		}
		?>
		
        <td valign="top">
            <strong><a href="/forum/forum-<?=$item->get('fid');?>.html"><?=$item->get('name');?></a></strong>
            <div class="smalltext"><?=$item->get('description');?>
            
            <?php 
            if ($sub_list = $item->subforums()){
                echo '<br><strong>Подфорумы:</strong>';
                foreach ($sub_list as $sub) {
            ?>
                <img src="/images/minion.gif" class="subforumicon"><a href="/forum/forum-<?=$sub->get('fid');?>.html" title=""><?=$sub->get('name');?></a>
            <?
                }
            }
			
			if ($mdlist = $parent->modlist($item->get('fid')))
			{

				echo '<div class="smalltext"><br>Модераторы: ';
				$mdstr = '';
				foreach ($mdlist as $mod)
				{
					$mdstr .= '<a href="user-'.$mod['uid'].'.html">'.$mod['username'].'</strong>, ';
				}
				echo trim($mdstr, ', ');	
				echo '</div>';
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
</table>
<br />