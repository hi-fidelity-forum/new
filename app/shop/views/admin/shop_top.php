<?php 

	$action = $request->action();
	
	if (!isset($id)){
        $id = "";
    }
	if (!isset($body)){
        $body = "";
    }
	
	if (!isset($_POST['ajax']) || Nitro::$request->is_ajax()){
	
?>
<div class="admin_page_menu">
    <a href="/<?=Request::$base_url;?>/shop/category/" class="button<?=($action=="category"?' active':'');?>">Категории</a>
    <a href="/<?=Request::$base_url;?>/shop/settings/" class="button<?=($action=="settings"?' active':'');?>">Настройки</a>
    <a href="/<?=Request::$base_url;?>/shop/autoup/" class="button<?=($action=="autoup"?' active':'');?>">Авто АПы</a>
</div>
<hr />

<?php
	
	}

	echo $body;

?>