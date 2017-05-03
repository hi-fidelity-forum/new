<?php defined('SYSPATH') or die('No direct access allowed.');

class Controller_Forum extends Controller 
{

	public function __construct(Request $request)
    {	
		parent::__construct($request);
		if ($request->action() == 'index') $this->notice->publish_enable(true);
		$this->breadcrumbs->add('Hi-Fi Forum','');
		$this->forum = new Model_Forum();
		$this->load_old_javascript = true;
	}

	public function action_index()
    {
		$tpl = '';
        $tpl_forums = '';
		
		$parent = $this->forum;
        
        $forums = $this->forum->subforums();
        
        $collapsed = $this->forum->get_collapsed();
		
		$this->page_title_prefix .= 'Главная';
		
		if ($forums){
            
            foreach ($forums as $forum){
                
                $sub = $forum->subforums();

                $tpl_forums .= View::factory('index_subforums')
                    ->set('subforums',$sub)
                    ->set('parent',$forum)
                    ->set('collapsed', $collapsed);
                    
            }
            
        }
        
        $tpl .= $tpl_forums;
		
		//Shop index block
		
		$shop = new Model_Shop_Category();
		$cat_list = $shop->get_top_struct();
		
		//print_r($cat_list); exit;
		$tpl .= View::factory('shop/index_block')
                    ->set('cat_list',$cat_list);
		
		//end shop block
		
		
		$this->posts = new Model_Forum_Posts();
		
		$last_posts = $this->posts->last_posts();
        
        $tpl .= View::factory('last_posts')
                    ->set('posts',$last_posts);
                    
        $forum_stats = $this->forum->forum_stats();
		
		if ($this->session->isAuth()){
            $online_users = $this->session->getOnlineUsers();
        } else {
            $online_users = false;
        }
        
        $tpl .= View::factory('index_stats')
                    ->set('online_users',$online_users)
                    ->set('parent_user',$this->session->user())
                    ->set('stats',$forum_stats);
        
        $this->content = $tpl;
        
    }
	
	public function action_forum()
	{
		
		$param = $this->request->param('id');
		
		$mas = explode('-page-',$param);
		
		$pid = (int) $mas[0];
		
		$page = isset($mas[1])?( (int) $mas[1]):1;
		
		$forum = new Model_Forum($pid);
		
		if ($forum->isAccess() == false)
		{
			$this->breadcrumbs->add('Ошибка');
			$this->content = View::factory('not_reg');
			return true;
		}
		
		$forum_info = $forum->forum_info();
		
		$parent_list = $forum->get_parent_list();
		
		$parent_list_str = '';
		
		if ($parent_list) 
		{	
			for ($i = count($parent_list); $i > 0; $i--) {
				$this->breadcrumbs->add($parent_list[$i-1]['name'],'forum/forum-'.$parent_list[$i-1]['fid'].'.html');
				$parent_list_str .= $parent_list[$i-1]['fid'].',';
			}
			$parent_list_str .= $pid;
		}
		
		$this->breadcrumbs->add($forum->get('name'),'forum/forum-'.$forum->get('fid').'.html');
		
		$this->page_title_prefix = $forum->get('name');
		
		$tpl = '';
		
		//$tpl .= View::factory('onlineusers')->set('parent',$forum);
		
		if ($sub = $forum->subforums())
		{
			
			$tpl .= View::factory('forum/subforums')
                    ->set('subforums',$sub)
                    ->set('parent',$forum);
		}
		
		
		$threadsPage = $forum->getThreadsPage();
		$threadsPage->setPageCur($page);
		$threadsPage->execute();
		$announcements = $forum->get_announcements($parent_list_str);	
		
		$tpl .= $this->content = View::factory('forum/threads')
					->set('threadsPage',$threadsPage)
					->set('announcements',$announcements)
					->set('parent',$forum);
		
		$this->content = $tpl;
		
	}
	
    public function action_theme()
	{
        $this->content = $id;
    }
    
} // 
