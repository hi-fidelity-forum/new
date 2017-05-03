<?php defined('SYSPATH') or die('No direct access allowed.');

class Controller_Forum2 extends Controller 
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
	
    
} // 
