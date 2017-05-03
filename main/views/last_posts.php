<script type="text/javascript">
<!--

var spinner=null;

function prostats_reload()
{
	if(spinner){return false;}
	this.spinner = new ActivityIndicator("body", {image: "/images/spinner_big.gif"});
	new Ajax.Request('/forum/xmlhttp.php?action=prostats_reload&my_post_key='+my_post_key, {method: 'post',postBody:"", onComplete:prostats_done});
	return false;
}

function prostats_done(request)
{
	if(this.spinner)
	{
		this.spinner.destroy();
		this.spinner = '';
	}
	if(request.responseText.match(/<error>(.*)<\/error>/))
	{
		message = request.responseText.match(/<error>(.*)<\/error>/);
		alert(message[1]);
	}
	else if(request.responseText)
	{
		$("prostats_table").innerHTML = request.responseText;
	}
}
-->
</script>
<div id="prostats_table">		
<table border="0" cellspacing="1" cellpadding="4" class="tborder">
<thead>
<tr>
<td class="thead" colspan="5">

<div class="expcolimage"><a href="" onclick="return prostats_reload();" class="button_reload">Обновить</a></div>
<div><strong class="prostats_title">Новые сообщения</strong></div>
</td>
</tr>
</thead>


<tbody>

		<tr valign="top">
		<!-- start: prostats_newestposts -->
        </tr>
        <tr class="tcat smalltext">
            <td>Тема</td><td>Время&nbsp;</td><td>Автор</td><td>Ответил</td><td>Форум</td>
		</tr>
		<!-- start: prostats_newestposts_row -->

<?php 
    if ($posts) {

    foreach ($posts as $post) {
    
    ?>
    <tr class="trow1 smalltext">
		<td class="first_cell">
            <img src="/images/ps_minion.gif" style="vertical-align:middle;" alt="">&nbsp;
            <a href="thread-<?=$post['tid'];?>-lastpost.html" title="<?=$post['subject'];?>"><?=$post['subject'];?></a></td><td width="170">
            <a href="thread-<?=$post['tid'];?>-lastpost.html" style="text-decoration: none;"><font face="arial" style="line-height:10px;">▼</font></a>
            <?=View::format_date($post['lastpost'], TRUE);?></td><td width="170"><a href="/profile/<?=$post['uid'];?>"><?=$post['username'];?></a></td><td width="170"><a href="/profile/<?=$post['lastposteruid'];?>"><?=$post['lastposter'];?></a></td><td width="210" style="white-space: nowrap"><a href="/forum/forum-<?=$post['fid'];?>.html" title="<?=isset(Model_Forum::$_all_forums[$post['fid']]['name'])?Model_Forum::$_all_forums[$post['fid']]['name']:'';?>"><?=isset(Model_Forum::$_all_forums[$post['fid']]['name'])?Model_Forum::$_all_forums[$post['fid']]['name']:'';?></a></td>
    </tr>

<!-- end: prostats_newestposts_row -->
<!-- end: prostats_newestposts -->

<?php 
        }
    }
?>		
		
		</tbody>
		</table>
		<br>
		</div>