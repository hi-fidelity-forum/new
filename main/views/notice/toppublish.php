<?php
    
if ($items)
{
?>

<table class="forum_news_top_block"><tr>
<?php 

    $counter = 0;
    foreach ($items as $item)
    {
        $counter++;
    
?>

<td class="forum_news_item<?=(($counter == 3)?' three_cell':'');?>">
        
        <table class="news_item_table">
        <tr>
            <td class="news_pic_cell">
                <table class="news_pic"><tr><td>
                    <a href="/publish/<?=$item['type'];?>/<?=$item['id'];?>">
                        <img src="<?=$item['image'];?>" class="news_image" title="<?=$item['title'];?>" />
                    </a>
                </td></tr></table>
            </td>
            <td class="news_content">
                <a class="news_title" href="/publish/<?=$item['type'];?>/<?=$item['id'];?>" title="<?=$item['title'];?>"><?=View::cutString($item["title"],30);?></a>
                <div class="news_content_info">
                    <?=mb_substr(html_entity_decode(strip_tags($item['content']),ENT_QUOTES,'utf-8'),0,152,'utf-8');?>...
                    <div class="ratush"></div>
                </div>
              </td>
            </tr>
        </table>
        <div class="clear"></td>

<?php 
    }
?>
<div class="clear"></div></tr></table><!-- forum_news_top_block -->

<?php 
}
?>
