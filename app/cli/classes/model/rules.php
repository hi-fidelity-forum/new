<?php defined('SYSPATH') or die('No direct script access.');

class Model_Rules extends Model {

    public function __construct($user = false)
	{
    
        parent::__construct();
		
		$this->table_name = 'rules';
		
        return $this;
        
    }
    
    public function get_headlist()
	{
		
		$q='SELECT r.id, r.title FROM rules AS r';
		
        if ($query = DB::query($q))
		{
			while ($row = $query->fetch())
			{				
				$res[$row['id']] = $row;
			}
			return $res;
		}
            
		return false;
	}
	
	public function get_content($id = 1)
	{
		$id = (int) $id;
		
		$q='SELECT r.id, r.content FROM rules AS r WHERE id='.$id.' LIMIT 1';
		
        if ($query = DB::query($q))
		{
			if ($row = $query->fetch())
			{				
				$res = $row['content'];
			}
			
			return $res;
		}
		
		return false;		
	}
    
}
