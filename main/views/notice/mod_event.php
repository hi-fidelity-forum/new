<?php 
if (isset($events) && $events){
?>
    <div class="red_alert" style="border: 1px solid green; background: #c8fcd1 !important;"><a style="background: #c8fcd1 !important;" href="/admin/moder/">Премодерация:</a> <?=isset($events['ads'])?'<a href="/admin/moder/ads">объявлений - '.$events['ads'].'</a>':'';?><?=isset($events['reputation'])?' <a href="/admin/moder/reputations">отзывы - '.$events['reputation'].'</a>':'';?></div>
<?php 
}
?>