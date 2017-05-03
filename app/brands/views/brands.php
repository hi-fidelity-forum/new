<div class="brands_list">

<h2 class="page_title">Бренды</h2>

        <div class="alphabet_menu">
            <?=$alphabet_menu;?>
        </div>      
<?php 

    foreach ($brands as $key=>$val){
    ?>
        
        <div class="brands_list_items">
            <div class="brand-logo">
                <a href="/brands/view/<?=$val['id'];?>"><img src="<?=$val['logo'];?>" width="170"></a>  
            </div>
            <a href="/brands/view/<?=$val['id'];?>" class="title"><?=$val['name'];?></a>
            <div class="brand-country">Страна: <span><?=$val['country'];?></span></div>
            <div class="brand-site">Сайт: <a href="<?=strpos($val['site'],'http://')?$val['site']:'http://'.$val['site'];?>" target="_blank"><?=$val['site'];?></a></div>
            <p><?=View::cutString($val['description'],700);?></p>
            <div><a class="read-more" href="/brands/view/<?=$val['id'];?>">Подробнее</a></div>
            <div class="clear"></div>
        </div>
    <?
    }

?>
</div>