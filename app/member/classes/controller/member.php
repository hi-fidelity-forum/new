<?php defined('SYSPATH') or die('No direct access allowed.');

// Описание класса
class Controller_Member extends Controller {

    // Регистрация пользователей
    public function action_register()
    {
        // Если есть данные, присланные методом POST
        // Загрузка формы логина
        $this->content = View::factory('register');
    }

    // Метод разлогивания
    public function action_logout()
    {
        if($this->session->isAuth())
        {
            if ($this->session->logout()){
                Request::redirect(Request::$base_url);
            } else {
                $this->content = 'Error: not logout this user';
            }
            
         } else {
            Request::redirect(Request::$base_url);
         }
    }

    // Метод логина
    public function action_login()
    {
        // Проверям, вдруг пользователь уже зашел
         if($this->session->isAuth())
         {
            
            Request::redirect(Request::$base_url);
            
         } else {
         
            if ($_POST){
            
                $login = (string) $_POST['login'];
                $password = (string) $_POST['password'];
                $revrite_url = isset($_POST['url'])?((string) $_POST['url']):'/';
				
				if ($user = $this->session->login($login, $password)){
                    
					Request::redirect($revrite_url);
                    
                } else {
                    
                    $this->content = 'User not login';
                    
                }
                
            }
            else {
                $this->content = View::factory('login');
            }
         
         }
    }
}