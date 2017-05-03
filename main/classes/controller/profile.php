<?php defined('SYSPATH') or die('No direct access allowed.');

// Описание класса
class Controller_Profile extends Controller
{

	private $user = false;
	
	function action_view()
    {
	
		if ($this->user)
		{
			$this->page_title_prefix = $this->page_title_prefix.' - Основное';
			$this->breadcrumbs->add('Основное',$this->request->controller_uri().'/view');
			
			$this->content = View::factory('profile/view')
					->set('user', $this->user);
		}
    }
	
	function action_posts()
	{
		if ($this->user)
		{
			$this->page_title_prefix = $this->page_title_prefix.' - Сообщения';
			$this->breadcrumbs->add('Сообщения',$this->request->controller_uri().'/posts');
			
			if ($posts = $this->user->getUserPosts())
			{
				$posts->execute();
				$this->content = View::factory('profile/posts')->set('posts', $posts);
			}
		}
	}
	
	function action_threads()
	{
		if ($this->user)
		{
			
			$threads = new Model_Forum_Threads();
			
			if ($_POST)
			{
				
				if (isset($_POST['action']))
				{
					$action = (string) $_POST['action'];
					switch ($action) {
						case "movetosafe":
							if (isset($_POST['moditems'])){
								$moditems = $_POST['moditems'];
								$moditems = explode(',', $moditems);
								if ($threads->moveThreadsToFid($moditems, 87))
								{
									//$this->request->redirect('/'.$this->request->uri());
								}
							}
							
						break;
						case "moveto":
							if (isset($_POST['moditems']) && isset($_POST['fid'])){
								$fid = (int) $_POST['fid'];
								$moditems = $_POST['moditems'];
								$moditems = explode(',', $moditems);
								
								if ($threads->moveThreadsToFid($moditems, $fid))
								{
									$this->request->redirect('/'.$this->request->uri());
								}
							}
						break;
						default:
						break;
					}
				}
			}
			
			$this->page_title_prefix = $this->page_title_prefix.' - Темы';
			$this->breadcrumbs->add('Темы',$this->request->controller_uri().'/threads');
			
			if ($threads = $this->user->getUserThreads())
			{
				$threads->execute();
				$this->content = View::factory('profile/threads')->set('threads', $threads)->set('user', $this->user);
			}
			
		}
	}
	
	function action_reputations()
	{
		$display_group = $this->user->getDisplayGroupId();
		if ($this->user && !($this->user->isAdmin() || $this->user->isModer() || $display_group == '6'))
		{
			$this->page_title_prefix = $this->page_title_prefix.' - Репутация';
			$this->breadcrumbs->add('Репутация',$this->request->controller_uri().'/reputations');
			
			$reputations = $this->user->getReputations();
			$reputations_all = $reputations->getAll();
			
			$reputation_neg = $reputations->countNegative();
			
			$this->content = '';
			
			$user_group_display = $this->session->user()->getDisplayGroup();
			$user_group_activity = $this->session->user()->getGroupByActivity();
			
			$ud = $this->user->getDisplayGroup();
			//var_export($ud['canusercp']);
			
			if ($this->session->isAuth() && ($this->session->user()->get('uid') != $this->user->get('uid')) && $user_group_display['cangivereputations'] && $ud['canusercp'] || $this->session->user()->isAdmin())
			{
				$max_rep_display = (int) $user_group_display['reputationpower'];
				$max_rep_activity = (int) $user_group_activity['reputationpower'];
				
				$max_rep = $max_rep_display>=$max_rep_activity?$max_rep_display:$max_rep_activity;
				
				if ($max_rep > 0)
				{
					if ($_POST && !($this->user->isModer()))
					{
						if (isset($_POST['change']) && isset($_POST['comments']))
						{
							$author_id = $this->session->user()->get('uid');
							$comments = (string) $_POST['comments'];
							$rep = (int) $_POST['reputation'];
							
							if ($reputations->put($author_id, $rep, $comments))
							{
								$this->request->redirect('/'.$this->request->uri());
							}
							else 
							{
								$this->content .= 'Произошла ошибка, репутация не изменена';
							}
						}
					}
					
					if ($_GET && isset($_GET['delete']) && ($rid = (int) $_GET['delete']))
					{
						if ($reputations->remove($rid))
						{
							$this->request->redirect('/'.$this->request->uri());
						}
						else 
						{
							$this->content .= 'Произошла ошибка, не удалось удалить отзыв';
						}
					}
					
					$user_rep = $reputations->getByAuthor($this->session->user()->get('uid'));
					$this->content .= View::factory('reputations/reputation_change')
										->set('user_reputation', $user_rep)->set('user', $this->user)
										->set('max_rep', $max_rep);
				}
			}
			
			$this->content .= View::factory('reputations/reputations')
				->set('reputations', $reputations_all)->set('user', $this->user)
				->set('reputation_neg', $reputation_neg);
		}
		else 
		{
			$this->content = 'Страница не доступна';
		}
	}
	
	
	public function action_ads()
	{
		if ($this->user)
		{
			
			$uid = $this->user->get('uid');
			$this->breadcrumbs->add('Объявления',$this->request->controller_uri().'/ads');
			/*
			if ($ads = $this->user->getUserAds())
			{
				$ads->execute();
				$ad_list_items = View::factory('profile/ads/items')->set('ad_list',$ads);
				$this->content = View::factory('profile/ads/page')->set('ad_list_items',$ad_list_items);
			} else {
				$this->content = 'Пользователь не имеет объявлений';
			}
			*/
			$this->content = 'Раздел на стадии разработки';
		}
	}
	
	function __construct($r)
	{
		
		parent::__construct($r);
		
		$this->user = false;
		
		$this->breadcrumbs->add('Hi-Fi Forum','forum/');
			
		$user_id = (int) $this->request->param('id');
		if ($user_id)
		{
			if ($user = new Model_UserInfo($user_id))
			{
				if ($user->get('uid') != 0)
				{
					$this->user = $user;
					if ($this->session->user()->get('uid') == $user_id)
					{
						//$this->request->redirect('/usercp/');
						$tl = 'Мой профиль';
						$this->page_title_prefix = $tl;
						//return true;
						
					} else {
						$tl = 'Профиль пользователя '.$this->user->get('username');
						$this->page_title_prefix = $tl;
					}
					$this->breadcrumbs->add($tl,$this->request->controller_uri().'/view');
				}
				else {
					$this->content = 'Пользователь не найден';
				}
			}
		}
		
	}
	
	function after()
	{
		if ($this->user)
		{
			$this->content = View::factory('profile/main')
				->set('user', $this->user)
				->set('content', $this->content);
		} else {
			$this->content = 'Ну указан UID пользователя';
		}
		parent::after();
		return false;
	}
    
}