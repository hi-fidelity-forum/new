<div class="container">
<?php 

  if ($subforums) {
    echo $subforums;
  }
  
?>
<br />

<?php
  if ($themes) {

    foreach ($themes as $item){
    
    ?>
        
        <a href="/frm/thread-<?=$item['tid'];?>.html"><?=$item['subject'];?></a>
        <br />
    
    <?php
        
    }
  }
    
?>
</div>