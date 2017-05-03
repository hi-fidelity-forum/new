<?php defined('SYSPATH') or die('No direct script access.');

class Model_Forum_Posts extends Model {

    public function __construct(){
    
        parent::__construct();
    
        $this->config = loadConfig('forum_config');
        
        $this->table_name = 'posts';
        $this->table_prefix = $this->config['table_prefix'];
        
        return $this;
        
    }
    
    public function last_posts($limit = 15){
    
        $limit = (int) $limit;
        
        //$q = 'SELECT * FROM '.$this->table_prefix.$this->table_name.' ORDER BY last DESC LIMIT '.$limit;
        
        $q='SELECT t.subject,t.username,t.uid,t.tid,t.fid, l.usergroup as authorgid, u.usergroup as postergid, t.lastpost,t.lastposter,t.lastposteruid,t.replies,tr.uid AS truid,tr.dateline
            FROM (
                SELECT * FROM '.$this->table_prefix.'threads
                WHERE visible=1
                ORDER BY lastpost DESC 
                LIMIT '.$limit.'
            ) as t
            LEFT JOIN '.$this->table_prefix.'threadsread tr ON (tr.tid=t.tid AND tr.uid='.((int)$this->session->user()->get('uid')).')
            LEFT JOIN '.$this->table_prefix.'users u on (t.lastposteruid=u.uid)
            LEFT JOIN '.$this->table_prefix.'users l on (t.uid=l.uid)
            ';
        
        $res = DB::query($q)->fetchAll();
        
        if ($res){
            return $res;
        }
        
        return false;

    }
	
	public function remove($mod_items)
	{
		
		return false;
	}
    
}
