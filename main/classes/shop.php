<?php defined('SYSPATH') OR die('No direct access allowed.');

Class Shop extends Controller 
{

	//public $template = 'shop';
	
	public $publish_enable = false;

    public function __construct(Request $request)
    {   

		parent::__construct($request);
        
        $this->shop = new Model_Shop();
		
		
        
		if (isset($this->publish_enable) && $this->publish_enable) {
			$this->top_publish = $this->notice->get_top_publish();
		} else {
			$this->top_publish = '';
		}
        
    }    

}