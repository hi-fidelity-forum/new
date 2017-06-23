<?php defined('SYSPATH') or die('No direct access allowed.');

// Описание класса
class Controller_Index extends Profile
{
	
	function action_index()
    {
		
		$this->page_title_prefix = $this->page_title_prefix.' - Основное';
		$this->breadcrumbs->add('Основное',$this->request->controller_uri().'/index');
			
		$this->content = View::factory('index/index')
			->set('user', $this->user)
			->set('main_mode', $this->main_mode);
    }
	
	function action_edit()
	{
		
		if ($this->session->isAuth() && ($user_id = (int) $this->request->param('id')))
		{
			if (($user_id == $this->session->user()->get('uid')) || $this->session->user()->isAdmin())
			{

				if ($_POST)
				{
					$change = false;
					
					$data = false;
					
					//var_export($_POST);
					
					if (isset($_POST['birthdayprivacy']) && !empty($_POST['birthdayprivacy']))
					{
						$birthdayprivacy = (string) $_POST['birthdayprivacy'];
						if (in_array($birthdayprivacy, array('all', 'age', 'none')))
						{
							$data['birthdayprivacy'] = $birthdayprivacy;
						}
					}
					
					if (isset($_POST['signature']))
					{
						$data['signature'] = (string) $_POST['signature'];
					}
					
					if (isset($_POST['website']))
					{
						$data['website'] = (string) $_POST['website'];
					}
					
					if (isset($_POST['bday1']) && isset($_POST['bday2']) && isset($_POST['bday3']))
					{
						$bday1 = (int) $_POST['bday1'];
						$bday2 = (int) $_POST['bday2'];
						$bday3 = (int) $_POST['bday3'];
						
						if ($bday1 && $bday2 && $bday3)
						{
							$data['birthday'] = $bday1.'-'.$bday2.'-'.$bday3;
						}
					}
					
					$f = $_POST['profile_fields'];
					
					if ($this->user->changeFields($f))
					{
						$change = true;
					} else $change = false;
					
					if ($this->user->changeInfo($data))
					{
						$change = true;
					} else $change = false;
					
					if ($change)
					{
						$uid = $this->user->get('uid');
						$red = '/'.$this->request->app().$uid;
						$this->request->redirect($red);
					}
					
				}
				
				$this->page_title_prefix = $this->page_title_prefix.' - Редактирование профиля';
				$this->breadcrumbs->add('Основное',$this->request->controller_uri().'/index/')->add('Редактирование', '');
					
				$this->content = View::factory('index/edit')
					->set('user', $this->user);
					
			}
		}	
	}
	
	function action_avatar_change()
	{
		if ($this->session->isAuth() && ($user_id = (int) $this->request->param('id')))
		{
			if (($user_id == $this->session->user()->get('uid')) || $this->session->user()->isAdmin())
			{
				$this->page_title_prefix = $this->page_title_prefix.' - Смена аватара';
				$this->breadcrumbs->add('Основное',$this->request->controller_uri().'/index/')->add('Смена аватара', '');
				
				if (isset($_GET['get_preview']) && $_GET['get_preview'])
				{
					$key = md5('preview'.$this->user->get('uid'));
					if ($preview = MCache::get($key))
					{
						header('Content-Type: image/png');
						echo $preview['scale']['source'];
						exit;
					}
				}
				
				if (isset($_GET['save_image']) && $_GET['save_image'])
				{
					$key = md5('preview'.$this->user->get('uid'));
					if ($img = MCache::get($key))
					{
						$avatar = 'uploads/avatars/avatar_'.$this->user->get('uid').'.png';
						$avatar_path = DOCROOT.$avatar;
						$image = $img['scale']['source'];
						
						if (file_put_contents($avatar_path, $image))
						{
							$this->user->changeInfo( array('avatar'=>'/'.$avatar.'?dateline='.TIME_NOW) );
							MCache::set($key, false, 0);
						}
					}
					$this->request->redirect(Request::$base_url.$this->request->app().$this->user->get('uid').'/index/');
				}
				
				if ($_POST && isset($_POST['act']))
				{
					$action = $_POST['act'];
					
					if ($action == 'load' && isset($_FILES['image']))
					{
						
						$att = new Model_Attach('image');
						
						$peview = array();
						
						if ($att->isImage())
						{
							
							$att->reduceImage(100, 100, true);
							
							$preview['scale']['info'] = $att->imageSize();
							$preview['scale']['source'] = $att->imageToString();
							
							$key = md5('preview'.$this->user->get('uid'));
							
							$tpl = '0';
							
							if (MCache::set($key, $preview, 1800))
							{
								$tpl = '1';
							}
							
							if ($this->request->isAjax())
							{
								$this->content = $tpl;
							}
						}
					}
				}
				else
				{
					$this->content = View::factory('index/avatar')
						->set('user', $this->user);
				}
			}
		}
		
	}
    
}