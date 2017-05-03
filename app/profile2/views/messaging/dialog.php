<div id="messaging_dialog">

<div class="pagination">
	<a href="/<?=$request->app().$user->get('uid');?>/messaging/" title="Все письма" class="button"><img src="/img/icons/back_arrow.png" height="12px" style="margin-top: -4px;" /> Все письма</a>
</div>

<?php 

$editor = new Editor();

?>

<table width="100%" class="tborder">
	<tr>
		<td class="tcat" width="150px"><span class="smalltext">&nbsp;<strong>Автор</strong></span></td>
		<td class="tcat" align="left"><span class="smalltext"><strong>Сообщение</strong></span></td>
	</tr>
	<tr>
		<td colspan="2">
<?php 

if ($dialog)
{
	
	$list = $dialog;
	
	$parser = new Parser();
	
	$parser_options = array(
				'allow_html' => 0,
				'allow_mycode' => 1,
				'allow_smilies' => 1,
				'allow_imgcode' => 1,
				'nl2br'=>1,
			);
			
	$even = true;
	
	$users = false;
	
	foreach ($list as $item)
	{
		$even = !$even;
	?>
			<table width="100%" class="dialog_item">
			<tr class="title">
				<td class="left trow<?=$even?'2':'1';?>">
					<?
						$uid = $item['uid'];
						if (isset($users[$uid]))
						{
							$user = $users[$uid];
						}
						else 
						{
							$user = new User($uid);
							$users[$uid] = $user;
						}
						echo View::factory('users/simpleUserInfo')->set('user', $user);
					?>
				</td>
				<td class="trow<?=$even?'2':'1';?>">
					<span class="date">Когда: <?=View::format_date($item['datetime']);?></span>
					<?=$parser->parse_message($item['message'], $parser_options);?>
				</td>
			</tr>
			</table>
	<?php 
	}
}

echo $editor_box = $editor->getEditorBox();

?>
		</td>
	</tr>
</table>
</div>