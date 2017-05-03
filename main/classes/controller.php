<?php defined('SYSPATH') or die('No direct script access.');

Class Controller extends Front
{

    public $template	= 'base';
    public $content	= '';
    public $auto_render	= TRUE;
    public $page_title_prefix = FALSE;
    public $meta_description = FALSE;
    public $meta_keywords = FALSE;
	
	public $load_old_javascript = false;
    
    public $dump_info = NULL;
    
    protected $current_page;
	
	function __construct($request)
	{
		parent::__construct($request);
	}
    
    public function before()
	{	
		/*
		$result = DB::query("SHOW FULL PROCESSLIST");
		while ($row=$result->fetch()) {
		  $process_id=$row["Id"];
		  if ($row["Time"] > 60 ) {
			$sql="KILL $process_id";
			DB::query($sql);
		  }
		}
		*/
		$is_ajax = $this->is_ajax = $this->request->is_ajax() || (isset($_POST['ajax']) && $_POST['ajax']) || (isset($_GET['ajax']) && $_GET['ajax']);
		
		if ($this->auto_render === TRUE && !$is_ajax)
		{
			// Load the template
			$this->template = View::factory($this->template);
		} else {
			$this->auto_render = false;
		}
	}

	public function after()
	{
        
        if ($this->auto_render === TRUE)
		{
			$this->template->content = $this->content;
			$this->template->page_title_prefix = $this->page_title_prefix;
			$this->template->meta_description = $this->meta_description;
			$this->template->meta_keywords = $this->meta_keywords;
			$this->template->load_old_javascript = $this->load_old_javascript;
            $this->template->core = $this;
			$this->request->body($this->template->render());
        } else {
            $this->request->body($this->content);
        }
	}
    
    public function redirect($redlink)
	{
    
        $redlink = (string) $redlink;
		
		if ($this->request->is_ajax()){
			echo '{"redirect":"'.$redlink.'"}';
			exit;
			return false;
		}
        
        header('Location: '.$redlink);
    
        return false;
    
    }
    
}