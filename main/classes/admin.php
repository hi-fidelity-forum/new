<?php defined('SYSPATH') OR die('No direct access allowed.');

Class Admin extends Controller {
	
	public $publish_enable = false;

    public function __construct(Request $request)
    {        
        parent::__construct($request);
		
		Request::$base_url = 'admin';
		
		$this->template	= 'admin';
		
		$this->controller_url = DS.Request::$base_url.DS.$request->controller();
		
		$settings = loadConfig('admin');
        $this->settings = $settings;
		$options = false;
		if ($this->session->isAuth()){
            $options['admin_flag'] = in_array($settings['admin_groups'],explode(',',$this->session->user()->get('usergroup')));
            $options['users_access'] = in_array($this->session->user()->get('uid'),$settings['users_access']);
            if (!$options['admin_flag'] && !$options['users_access']){
                $request->redirect('/');
            }
        } else {
            $this->request->redirect('/');
        }
        
		$this->breadcrumbs->set(0, 'Admin Panel','admin/');
        
        $this->admin_menu = $this->settings['admin_menu'];
		$this->admin_options = $options;
		
		if (isset($this->publish_enable) && $this->publish_enable) {
			$this->top_publish = $this->notice->get_top_publish();
		} else {
			$this->top_publish = '';
		}
        
    }    

}