<?php 

if (isset($ad) && $ad)
{
	$ad_obj = $ad;
	$ad = $ad->info();
}
?>
Ваше объявление было отклоненно.<br />
<br />
Причина отклонения: <?=$ad['reject'];?>.<br />
<br />
Просмотреть объявление и отредактировать можно по ссылке<br />
<a href="http://hi-fidelity-forum.com/shop/view/<?=$ad['id'];?>"><?=$ad['title'];?></a>