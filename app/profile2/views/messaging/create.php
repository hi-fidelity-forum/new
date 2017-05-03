<script type="text/javascript" src="/jscripts/jquery.autocomplete.js"></script>
<link href="/css/jquery.autocomplete.css" rel="stylesheet" type="text/css" media="all">

<div id="sendto_block">
	<table width="100%" class="tborder" cellpadding="4" cellspacing="1">
		<tr>
			<td width="50px">
				<label class="smalltext">Кому:</label>
			</td>
			<td>
				<input type="text" id="username" />
				<span id="send_avatar"></span>
			</td>
		</tr>
	</table>
</div>

<form action="" method="POST" id="message_form">

	<input type="hidden" name="toid" value="" id="toid" />
	<input type="hidden" name="create" value="1" />
	
	<table width="100%" class="tborder" cellpadding="4" cellspacing="1">
		<tr>
			<td width="50px" >
				<label class="smalltext">Тема:</label>
			</td>
			<td>
				<input type="text" name="subject" id="subject" value="" />
			</td>
		</tr>
	</table>
	
	<?=$editor_box;?>
	
</form>

<script>

  $('#message_form').submit(function()
  {
	  
	var err = '';

	if ($("#toid").val() == '')
	{
		err = err + "Не указан получатель\n";
	}
	
	if ($("#subject").val() == '')
	{
		err = err + "Не указана тема сообщения\n";
	}
	
	if ($("#message").val() == '')
	{
		err = err + "Вы не ввели текст сообщения\n";
	}
	
	if (err)
	{
		alert(err);
	} 
	else 
	{
		return true;
	}
	
	return false;
	  
  });

  $(function() {
    
	$("#username").autocomplete(false, {
		url:'/users/jsearch/?ajax=1',
		dataType: 'json',
		formatItem: function(item) {
			return item.name;
		},
		minChars: 2,
	}).result(function(event, data){
		if (data.avatar != 'undefined')
		{
			var ava = data.avatar;
			var toid = data.uid;
			$('#send_avatar').html('<img src="http://hi-fidelity-forum.com/'+ava+'" height="20px" align="top" />');
			$('#toid').val(toid);
		}
	});

  });
  
</script>