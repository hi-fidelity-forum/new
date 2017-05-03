<div class="rules_page">

	<div class="rules_menu">
<?php 
	
	$active_id = $active_id?$active_id:1;
	
	if ($headlist){
		foreach ($headlist as $item){
?>
		<div class="rules_menu_item<?=($item['id']==$active_id)?' active':'';?>">
			<a href="/service/<?=$item['id'];?>"><?=$item['title'];?></a>
		</div>
<?php 
		}
	}

?>
		<div class="clear"></div>
	</div>

<p>Уважаемые господа!</p>
<p>Предлагаем Вам воспользоваться  платными услугами на  нашем ресурсе.</p>
<p>Форум «Hi-Fidelity-Forum.com»  существует с 2001  года. <br />
	На сегодня это:</p>
<ul>
	<li>более 100 000 посещений в сутки;</li>
	<li>более 65 000 зарегистрированных пользователей;</li>
	<li>более 160 000 тем и 3 000 000 сообщений.</li>
</ul>
	
	<div class="rules_page">
		<?=$content;?>
	</div>
	
</div>