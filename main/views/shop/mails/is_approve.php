<?php 

if (isset($ad) && $ad)
{
	$ad_obj = $ad;
	$ad = $ad->info();
}
?>
Ваше объявление активировано.<br />
<br />
Просмотреть объявление можно по ссылке<br />
<a href="http://hi-fidelity-forum.com/shop/view/<?=$ad['id'];?>"><?=$ad['title'];?></a>