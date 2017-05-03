<div class="brands_list brands_list_items">

    <div class="brand_title">
        <a href="/brands" rel="index">Все бренды →</a> <?=$brand['name'];?>
    </div>
    <br />

    <h2 class="title"><?=$brand['name'];?></h2>
    
    <?php 

    if($session->isAuth()) {
            if (in_array($session->user()->get('usergroup'), array(4))) {
?>

    <span style="position: relative; top: -4px; left: 5px;">
            <a href="/brands/edit/<?=$brand['id'];?>"><img src="/images/icons/edit.png" height="24px" title="Редактировать" /></a>
            <a href="/brands/remove/<?=$brand['id'];?>" onClick="return confirm('Вы точно хотитие удалить?');"><img src="/images/icons/delete.png" height="24px" title="Удалить" /></a>
    </span>
<?
            }
        }
?>
    
            <?
                if (!empty($brand['banner'])){
                    echo '<img src="'.$brand['banner'].'" />';
                }
            ?>
            <div class="brand-logo float_right">
                <img src="<?=$brand['logo'];?>" width="170">
            </div>
            
            <div class="brand-country">Страна: <span><?=$brand['country'];?></span></div>
            <div class="brand-site">Сайт: <a href="<?=strpos($brand['site'],'http://')?$brand['site']:'http://'.$brand['site'];?>" target="_blank"><?=$brand['site'];?></a></div>
            <p><?=nl2br($brand['description']);?></p>
            
    </div>

    <?php echo is_callable('Publish')?Publish::search_mod('brand_id',$brand['id']):''; ?>