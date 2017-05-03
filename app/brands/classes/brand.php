<?php defined('SYSPATH') or die('No direct access allowed.');

class Brand extends DL {

    public static function quick_create($brand_name, $author_id)
	{
        
        $settings = loadConfig('brand_config');
        if ($settings['table_name']){
            $table_name = (string) $settings['table_name'];
            
            $name = (string) $brand_name;
            $uid = (int) $author_id;
            
            $tst = DB::select()->from($table_name)->where('name', '=', $name)->execute()->as_array();
            if ($tst) {
                return array('Error' => 'Brand with name "'.$name.'" is already created');
            }
            
            $cell = array('name','author_id');
            $val = array($name, $uid);
            
            $query = DB::insert($table_name, $cell)->values($val)->execute();
            if ($query){
                return $query[0];
            }
            return false;
        }
        return false;
    }
    
    public static function get_all(){
    
        $letter = strtoupper((string) isset($_GET['letter'])?$_GET['letter']:'');
        if (empty($letter)) $letter = 'A';
        
        $settings = loadConfig('brand_config');
        if ($settings['table_name']){
            $table_name = (string) $settings['table_name'];
            //$res = DB::select()->from($table_name)->where('(locate('.$letter.',content)','=',$letter)->order_by('id', 'DESC')->execute()->as_array();
            //$res = DB::query(Database::SELECT,'SELECT * FROM '.$table_name.' WHERE locate(\''.$letter.'\',lower(name))=1 ORDER BY name')->execute()->as_array();
            $res = DB::query('SELECT * FROM '.$table_name.' WHERE locate(\''.$letter.'\',lower(name))=1 ORDER BY name')->fetchAll();
            return $res;
        }
        return false;
    }
    
    public static function get_by_id($id){
    
        $id = (int) $id;
        
        $settings = loadConfig('brand_config');
        if ($settings['table_name']){
            $table_name = (string) $settings['table_name'];
            if ($res = DB::query('SELECT * FROM '.$table_name.' WHERE id = '. $id))
			{
				$f = $res->fetchAll();
				if ($f[0])
					return $f[0];
            }
            else return false;
        }
        return false;
    }
    
    public static function get_by_name($name){
    
        $name = (string) $name;
        
        $settings = Kohana::$config->load('brand_config');
        if ($settings['table_name']){
            $table_name = (string) $settings['table_name'];
            $res = DB::select()->from($table_name)->where('name', '=', $name)->execute()->as_array();
            if (isset($res[0])) {
                $res = $res[0];
                return $res;
            }
            else return false;
        }
        return false;
    }
    
    public static function get_list(){
        
        $settings = Kohana::$config->load('brand_config');
        if ($settings['table_name']){
            $table_name = (string) $settings['table_name'];
            $res = DB::select('id', 'name')->from($table_name)->order_by('name')->execute()->as_array();
            return $res;
        }
        return false;
    }
    
    public static function change($id,$data){
    
        $cell['id'] = (int) $id;
        
        $settings = Kohana::$config->load('brand_config');
        
        $cell['name'] = isset($data['name']) ? (string) $data['name'] : 'No name';
        $cell['site'] = isset($data['site']) ? (string) $data['site'] : '';
        $cell['country'] = isset($data['country']) ? (string) $data['country'] : '';
        $cell['description'] = isset($data['description']) ? (string) $data['description'] : '';
        $cell['status'] = isset($data['status']) ? (string) $data['status'] : 'NULL';
        
        if (isset($data['new_image'])){
            $tmp_image = realpath(DOCROOT).((string) $data['new_image']);
            if (is_file($tmp_image)) {
                
                $ext = substr($tmp_image,strrpos($tmp_image, ".")+1);

                $save_dir = realpath(DOCROOT).$settings['upload_path'];
                
                $new_image = $save_dir.$id.'.'.$ext;
                
                if (!is_dir($save_dir)){
                    mkdir($save_dir,0777,true);
                }
                
                rename($tmp_image,$new_image);
                
                $image_url = $settings['upload_path'].$id.'.'.$ext;
                
                $cell['logo'] = $image_url;
                
            } else {
                return array('Error' => 'Not image loaded');
            }
        } else {
            $cell['logo'] = isset($data['logo']) ? (string) $data['logo'] : '';
        }
        
        $query = DB::update($settings['table_name'], $cell)->where('id', '=', $id)->set($cell)->execute();
        if ($query) return $query;
        
        return false;
        
    }
    
    public static function remove_by_id($id){
    
        $id = (int) $id;
        
        $settings = Kohana::$config->load('brand_config');
        if ($settings['table_name']){
            
            $brand = Brand::get_by_id($id);
            
            $table_name = (string) $settings['table_name'];
            
            if (!empty($brand['logo'])){
                $file = DOCROOT.$brand['logo'];
                if (is_file($file)){
                    chmod($file,0777);
                    unlink($file);
                }
            }
            $res = DB::delete($table_name)->where('id','=',$id)->execute();
            if (isset($res)) {
                return $res;
            }
            else return false;
        }
        return false;
    }
    
    
    public static function get_alphabet_menu()
	{
        $letter = strtoupper((string) isset($_GET['letter'])?$_GET['letter']:'');
        if (empty($letter)) $letter = 'A';
        
        $settings = loadConfig('brand_config');
        if ($settings['table_name']){
            
            if ($res = DB::query('SELECT DISTINCT SUBSTRING(name FROM 1 FOR 1) AS firstletter, COUNT(*) AS counter FROM brands GROUP BY firstletter'))
			{
                $am = '<div class="alphabet_menu_items">';
                foreach ($res as $key => $val){
                    $cls = '';
                    $symb = strtoupper($val['firstletter']);
                    if ($letter == $symb) {
                        $cls = ' class="active"';
                    }

                    $am .= '<a href="/'.Request::initial()->uri().'?letter='.$symb.'"'.$cls.'>'.$symb.'</a>';
                }
                $am .= '</div><!-- alphabet_menu_items -->';
                
                return $am;
            }
            return false;
            
        }
        return false;
    }

} // 
