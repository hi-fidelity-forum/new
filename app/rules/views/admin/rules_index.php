<table border=1 cellpadding="5px">

<?php

if ($rules){

    foreach ($rules as $item){
        ?>
            <tr>
                <td><span><?=$item['title'];?></span></td>
                <td><a href="/admin/rules/edit/<?=$item['id'];?>">Edit</a></td>
            </tr>
        <?php 
    }

}

?>

</table>