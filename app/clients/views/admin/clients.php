<?php 

	$user_item = false;
    if (!isset($id)){
        $id = 0;
    }
	
	if ($groups) 
	{
?>
<div class="admin_page_menu">
<?php 	
		foreach ($groups as $group)
		{
	
?>

    <a href="/<?=Request::$base_url;?>/clients/view/<?=$group['gid'];?>" class="button<?=($id==$group['gid']?' active':'');?>"><?=$group['title'];?></a>

<?php
		}
?>
	
</div><!-- admin_page_menu -->
<hr />

<?php 
	}

if ($id>0)
{    
   if ($users)
   {
?>
<table class="groups_users" width="100%" border="1" cellpadding="2">
    <tr class="title" style="background: #ccc;">
		<td width="50px">UID</td>
		<td>UserName</td>
		<td>Стоп дата</td>
		<td>Кол-во объявлений</td>
	</tr>
  
<?php 
		$even = 1;
        foreach($users as $user)
        {
			$even = !$even;
			$user_item .= $user['username'].', ';
?>
	<tr<?=$even?' style="background: #eee;"':'';?>>
		<td><a href="/<?=Request::$base_url;?>/clients/edit/<?=$user['uid'];?>"><?=$user['uid'];?></a></td>
		<td><a href="/forum/user-<?=$user['uid'];?>.html"><?=$user['username'];?></a>
		<td><?=$user['order_end'];?></td>
		<td><?=$user['count_ad'];?></td>
	</tr>
<?php 
        }
?>
</table>  
<?php 
   }  
}
?>
<?php 
if ($user_item) 
{
 echo '<hr />'.$user_item;
}
?>