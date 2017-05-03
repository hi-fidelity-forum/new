<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Brands extends Controller {

	public function action_index()
	{    
		$this->page_title_prefix = 'Бренды';
        $page = isset($_GET['page'])?((int) $_GET['page']):0;
        
        if (!$page) {
        
            $brands = Brand::get_all();
            //$this->debug($brands);
            $alphabet_menu = Brand::get_alphabet_menu();
            $this->content = View::factory('brands')->bind('brands',$brands)->bind('alphabet_menu',$alphabet_menu);
        }
        else {
            $this->debug($page);
            $this->content = View::factory('brands');
        }
        
        if($this->session->isAuth()) {
            if (in_array($this->session->user()->get('usergroup'), array(4))) {
                $this->content = View::factory('brands_create_form') . $this->content;
            }
        }
        
        //
    }
    
    
    public function action_quick_create(){
        if ($_POST) {
            if ($_POST['brand_name'] && $_POST['user_id']){
                
                $brand_name = (string) $_POST['brand_name'];
                $user_id = (int) $_POST['user_id'];
                $res = Brand::quick_create($brand_name,$user_id);
                
                if (is_array($res)) {
                    $this->debug($res);
                    $brands = Brand::get_all();
                    //$this->debug($brands);
                    $this->content = View::factory('brands')->set('brands',$brands);
                    return false;
                }
                elseif (is_int($res)) {
                    Request::current()->redirect('/'.Request::initial()->controller().'/edit/'.$res);
                }
                else {
                    $this-debug(array('Error'=>'not create brand'));
                    $this->content = View::factory('brands');
                }
            }
            else {
                Request::current()->redirect(Request::initial()->controller());
            }
            $this->content = View::factory('brands');
        }
        else {
            Request::current()->redirect(Request::initial()->controller());
        }
    }
    
    public function action_edit(){
    
        $id = (int) $this->request->param('id');
    
        if ($_POST){
            //$this->debug($_POST);
            
            $res = Brand::change($id,$_POST);
            
            if (is_array($res)){
                $this->debug($res);
            } else {
                Request::current()->redirect('/'.Request::initial()->controller().'/view/'.$id);  
            }
        }
    
        if(!Auth::instance()->logged_in()) {
            Request::current()->redirect(Request::initial()->controller());
        }
        
        $brand = Brand::get_by_id($id);
        
        if (!$brand){
            Request::current()->redirect(Request::initial()->controller());
        }
        //$this->debug($brand);
        $this->content = View::factory('brand_edit')->set('brand',$brand);
    }
    
    public function action_view(){
    
        $id = (int) $this->request->param('id');
        
        $brand = Brand::get_by_id($id);
        
        if (!$brand){
            Request::current()->redirect(Request::initial()->controller());
        }
        //$this->debug($brand);
        
        $this->page_title_prefix = 'Бренды - '.$brand['name'];
        
        $this->content = View::factory('brand_view')->set('brand',$brand);
        
    }
    
    public function action_remove(){
    
        $id = (int) $this->request->param('id');
        
        $brand = Brand::remove_by_id($id);
        
        if ($brand){
            Request::current()->redirect(Request::initial()->controller());
        }
        
        $this->debug('Error: not remove this brand');
        $brand = Brand::get_by_id($id);
        //$this->debug($brand);
        $this->content = View::factory('brand_view')->set('brand',$brand);
        
    }

} // End Welcome
