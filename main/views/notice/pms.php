<div class="pm_alert" id="pm_notice">
	<div class="float_right"><a href="private.php?action=dismiss_notice&amp;my_post_key=059ef7fbb3e07d3cc0cff0af217c848f" title="Пропустить уведомление" onclick="return MyBB.dismissPMNotice()"><img src="/images/dismiss_notice.gif" alt="Пропустить уведомление" title="[x]"></a></div>
<?php 

if ($info){

$unread = count($info);
$pm = $info[0];

if ($unread>1) {
?>
    <div><strong>У вас <?=$unread;?> непрочитанных сообщения.</strong> Последнее сообщение пришло от <a href="/forum/user-<?=$pm['fromid'];?>.html"><?=$pm['username'];?></a> с названием <a href="/forum/private.php?action=read&pmid=<?=$pm['pmid'];?>" style="font-weight: bold;"><?=$pm['subject'];?></a></div>
<?php
} else {
?>
    <div><strong>У вас 1 непрочитанное сообщение</strong> от пользователя <a href="/forum/user-<?=$pm['fromid'];?>.html"><?=$pm['username'];?></a> с названием <a href="/forum/private.php?action=read&pmid=<?=$pm['pmid'];?>" style="font-weight: bold;"><?=$pm['subject'];?></a></div>
<?php 
}

}
?>

</div>