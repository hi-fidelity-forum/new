<h5 class="page_title">Редактирование информации:</h5>
<hr />
<form class="edit_form" method="POST">
    <input type="hidden" name="id" value="<?=$brand['id'];?>" />
    <input type="hidden" name="logo" value="<?=$brand['logo'];?>" />
    <div class="edit_item"><label>Название</label><input type="text" name="name" value="<?=$brand['name'];?>" /></div>
    <div class="edit_item"><label>Сайт</label><input type="text" name="site" value="<?=$brand['site'];?>" /></div>
    <div class="edit_item"><label>Страна</label><input type="text" name="country" value="<?=$brand['country'];?>" /></div>
    <div class="edit_item"><label>Логотип</label><input type="file" name="image" class="load_image_input" /><div class="image_preview"><? if (!empty($brand['logo'])){echo '<img src="'.$brand['logo'].'" />';} ?></div></div>
    <div class="edit_item"><label>Описание</label><textarea class="editor" id="editor" name="description"><?=$brand['description'];?></textarea></div>
    <div class="edit_item"><label>&nbsp;</label><input type="submit" value="Сохранить" class="submit button" />&nbsp;<a href="/<?=Request::initial()->controller();?>/view/<?=$brand['id'];?>" class="submit button">Отмена</a></div>
</form>

<script type="text/javascript" src="/js/jquery.ajaxfileupload.js"></script>
<script type="text/javascript">

  function set_file_loader(el){
    $(el).ajaxfileupload({
      'action': '/js/cropper/load.php',
      'params': {
        'max_width': '170',
        'type': 'brand',
        'prefix' : 'brand_<?=md5(Auth::instance()->get_user()->uid);?>'
      },
      'onComplete': function(response) {
        //console.log('custom handler for file:');
        if (response.status != undefined){
            alert(response.message);
            reset_file_loader();
        }else {
            view_image(response);
        }
      },
      'onStart': function() {
        //if(weWantedTo) return false; // cancels upload
      },
      'onCancel': function() {
        //console.log('no file selected');
      }
    });
  }
  
  function reset_file_loader(){
    $('.image_preview').html('');
    $('.load_image_input').replaceWith('<input type="file" name="image" class="load_image_input" />');            
    set_file_loader($('.load_image_input'));
  }
  
  set_file_loader($('.load_image_input'));
  
  function hide_image_loader(){
            var preview = $('.preview_cropp');
            $('#CropImage').imgAreaSelect({remove: true});
            $(preview).css({display: 'none'});
            $('#CropImage').css({display: 'none'});
  }

  
  function view_image(code){
    $('.image_preview').html('<img src="'+code+'?r='+Math.random()+'" /><input type="hidden" value="'+code+'" name="new_image" class="new_image" />');    
  }

  $('.edit_form').submit(function(){
        
    var err = false;
    
    $('.load_image_input').remove();
    
    return true;
    
  });
</script>