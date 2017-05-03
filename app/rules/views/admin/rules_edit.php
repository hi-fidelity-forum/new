<h5 class="page_title">Редактирование информации:</h5>
<div class="rules_edit_page">
<form class="edit_form" method="POST">
    <input type="hidden" name="id" value="<?=$rules['id'];?>" />
    <div class="edit_item"><label>Название</label><input type="text" name="title" value="<?=$rules['title'];?>" /></div>
    <div class="edit_item"><label>Описание</label><textarea class="editor" id="editor" name="description"><?=$rules['content'];?></textarea></div>
    <div class="edit_item"><label>&nbsp;</label><input type="submit" value="Сохранить" class="submit button" />&nbsp;<a href="/<?=Request::initial()->controller();?>/view/<?=$rules['id'];?>" class="submit button">Отмена</a></div>
</form>

<script type="text/javascript">

  
</script>

</div><!-- .rules_edit_page -->