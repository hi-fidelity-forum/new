<form method="post" action="<?=Request::$base_url;?>member/login/">
    <input name="url" type="hidden" value="<?=Request::$base_url;?>">
    <input name="login" id="quick_login_name_username" type="text" value="Логин" class="textbox" onfocus="if(this.value == 'Логин') { this.value=''; }" onblur="if(this.value == '') { this.value='Логин'; }">&nbsp;
    <input name="password" id="quick_login_password" type="password" value="Пароль" class="textbox" onfocus="if(this.value == 'Пароль') { this.value=''; }" onblur="if(this.value == '') { this.value='Пароль'; }">&nbsp;
    <input name="submit" type="submit" value="Вход" class="button">
    — <a href="<?=Request::$base_url;?>/member.php?action=lostpw">Забыли пароль?</a>
    — <a href="<?=Request::$base_url;?>/member.php?action=register">Зарегистрироваться</a>
</form>