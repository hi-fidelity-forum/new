<?php defined('SYSPATH') or die('No direct script access.');

class Model_Attach extends Model 
{
	
	private $_image = null;
	
	private $_mode = 'file'; //file or image
	
	private $_info = false;
	
	const MODE_FILE = 0;
	const MODE_IMAGE = 1;
	
	public function __construct($input_name = false, $preload = true)
	{    
		parent::__construct();
		
		if ($input_name)
		{
			$this->setTmpFile($input_name, $preload);			
		}
    
        $this->config = loadConfig('attach_config');
		$this->upload_path = DOCROOT.$this->config['upload_path'];
		$this->table_name = $this->config['table_name'];
		
		return $this;
        
    }
	
	function setTmpFile($input_name, $preload = false)
	{
		$info = $_FILES[$input_name];
			
		$this->_info = $info;
		
		$tmp_name = $info['tmp_name'];
		
		//check file on image type
		if (is_file($tmp_name) && $image_type = exif_imagetype($tmp_name))
		{
			$this->_mode = self::MODE_IMAGE;
			if ($preload)
			{
				$this->loadTmpImage();
			}
		}
		else 
		{
			$this->_mode = self::MODE_FILE;
		}	
		
		return $this;		
	}
	
	public function loadTmpImage()
	{
		$tmp_name = $this->_info['tmp_name'];
		if (is_uploaded_file($tmp_name) && exif_imagetype($tmp_name))
		{
			return $this->setImageFromFile($tmp_name);
		}
	}
	
	public function getImage()
	{
		if ($this->_mode == self::MODE_IMAGE && $this->_image)
		{
			return $this->_image;
		}
		return false;
	}
	
	public function setImageFromFile($image_path)
	{
		if ($image_path && is_file($image_path) && $image_type = exif_imagetype($image_path))
		{
			if ($image_type)
			{
				$image = false;
				
				switch ($image_type) 
				{
					case IMAGETYPE_JPEG:
						$image = imagecreatefromjpeg($image_path);
						break;
					case IMAGETYPE_GIF:
						$image = imagecreatefromgif($image_path);
						break;
					case IMAGETYPE_PNG:
						$image = imagecreatefrompng($image_path);
						break;
					case IMAGETYPE_BMP:
						$image = imagecreatefromwbmp($image_path);
						break;
				}
				if ($image !== false)
				{
					$this->setMode(self::MODE_IMAGE);
					$this->_image = $image;
					return true;
				}
			}
		}
		return false;
	}
	
	function reduceImage($max_width = false, $max_height = false, $save_aspect_ratio = false)
	{
		if ($max_width !== false || $max_height !== false)
		{
			$max_width = (int) $max_width;
			$max_height = (int) $max_height;
			
			if ($image = $this->getImage())
			{
				
				$image_width = (int) imagesx($image);
				$image_height = (int) imagesy($image);

				if (($image_width > $max_width) || ($image_height > $max_height))
				{
					if ($save_aspect_ratio)
					{
						
						if ($image_width > $max_width)
						{
							$q = $image_width / $max_width;
						
							$new_width = $max_width;
							$new_height = $image_height / $q;
						}
						
						if (!isset($new_width)) $new_width = $image_width;
						if (!isset($new_height)) $new_height = $image_height;
												
						if ($max_height < $new_height) 
						{
							$k = $new_height / $max_height;
							$new_height = $new_height / $k;
							$new_width = $new_width / $k;
						}
						
						$resize_image = imagecreatetruecolor($new_width, $new_height);
						imagecopyresampled($resize_image, $image, 0,0,0,0, $new_width, $new_height, $image_width, $image_height);
												
						$new_image = imagecreatetruecolor($max_width, $max_height);
						imagealphablending($new_image, false);
						imagesavealpha($new_image, true);
						$col=imagecolorallocatealpha($new_image,255,255,255,127);
						imagefill($new_image, 0, 0, $col);

						$pos_x = ($max_width - $new_width) / 2;
						$pos_y = ($max_height - $new_height) / 2;
						
						imagecopyresampled($new_image, $resize_image, $pos_x, $pos_y, 0, 0, $new_width, $new_height, $new_width, $new_height);
						imagedestroy($this->_image);
						imagedestroy($resize_image);
						
						$this->_image = $new_image;
						
						return true;
						
					}
					else 
					{
						
					}
				}
				else 
				{
					return true;
				}
			}
		}
		return false;
	}
	
	function imageToString()
	{
		if ($image = $this->getImage())
		{
			ob_start();
			imagepng($image);
			return(ob_get_clean());
		}
		return false;
	}
	
	function imageSize()
	{
		if ($image = $this->getImage())
		{
			$image_width = (int) imagesx($image);
			$image_height = (int) imagesy($image);
			return array('width'=>$image_width, 'height'=>$image_height);
		}
		return false;
	}
	
	private function setMode($mode)
	{
		if ($mode !== null && ($mode = (bool) $mode))
		{
			$this->_mode = $mode;
		}
		return $this->_mode;
	}
	
	public function fileInfo()
	{
		
		return false;
	}
	
	function isImage()
	{
		return ($this->_mode == self::MODE_IMAGE);
	}
	
	function isFile()
	{
		return ($this->_mode == self::MODE_FILE);
	}
	
	public function setFile()
	{
		
	}	
	
	public function get($tag, $order = false)
	{
		if ($order === false)
		{
			$q = 'SELECT * FROM '.$this->table_name.' WHERE tag='.DB::quote($tag).' ORDER BY `order`';
			if ($req = DB::query($q))
			{
				$res = false;
				while ($row = $req->fetch())
				{				
					$res[$row['order']] = $row;
				}
				return $res;
			}
			return false;
		} else {
			$order = (int) $order;
			$q = 'SELECT * FROM '.$this->table_name.' WHERE `tag`='.DB::quote($tag).' AND `order` ='.$order;
			if ($req = DB::query($q))
			{
				if ($res = $req->fetch()){
					return $res;
				}
			}
		}
		return false;
	}
	
	public function upload_file($tag=false, $order = false, $title = false, $cat = false, $sub = false)
	{
		if ($tag && $this->_info)
		{
			$set_order = $order;
			
			$create = true;
				
			if ($att = $this->get($tag))
			{
				if ($order === false)
				{
					$el = end($att);
					$order = $el['order'] + 1;
				} else {
					$order = (int) $order;
					if (isset($att[$order]))
					{
						$create = false;
					}
				}
				
			} else {
				$order = 1;
			}
			
			$name = $this->_info['name'];
			$ext = strtolower(substr($name,strrpos($name, ".")+1));
			$title = $title?((string) $title):$name;
			
			$title = $title.'_'.$order;
			
			$file_name = md5($tag.$title).'.'.$ext;
			
			if (!is_dir($this->upload_path))
			{
				mkdir($this->upload_path);
				chmod($this->upload_path, 0777);
			}
			
			if ($cat != false)
			{
				$save_path = $this->upload_path . $cat;
				if (!is_dir($save_path))
				{
					mkdir($save_path);
					chmod($save_path, 0777);
				}
				
				$url_file = $this->config['upload_path'].$cat;
				
				if ($sub)
				{
					$save_path = $save_path.'/'.$sub;
					if (!is_dir($save_path))
					{
						mkdir($save_path);
						chmod($save_path, 0777);
					}
					$url_file .= '/'.$sub;
				}
				$url_file .= '/'.$file_name;
			}
			else 
			{
				$save_dir_year = Date::formatted_time('NOW','Y');
				$save_dir_month = Date::formatted_time('NOW','m');
				$save_path = $this->upload_path . $save_dir_year. '/' . $save_dir_month;
				
				if (!is_dir($save_path))
				{
					if (!is_dir($this->upload_path.$save_dir_year))
					{
						mkdir($this->upload_path.$save_dir_year);
						chmod($this->upload_path.$save_dir_year, 0777);
					}
					mkdir($save_path);
					chmod($save_path, 0777);
				}
				$url_file = $this->config['upload_path'].$save_dir_year.'/'.$save_dir_month.'/'.$file_name;
			}
			
			$save_file_name = $save_path . '/'. $file_name;
			
			
			if (move_uploaded_file($this->_info['tmp_name'],$save_file_name))
			{        
				//todo: сделать проверку на размер (max 1200x800), и если больше то ресайзить
				
				list($w_i, $h_i, $type) = getimagesize($save_file_name);
				
				if (1200 < $w_i){
					$q = ((int) $w_i) / 1200;
					$w_o = 1200;
					
					$h_o = $h_i / $q;
				}
				
				$mh = 800;
						
				if (!isset($h_o)) {$h_o = $h_i;}
				if (!isset($w_o)) {$w_o = $w_i;}
				if ($mh < $h_o) {
					$k = $h_o / $mh;
					$h_o = $h_o / $k;
					$w_o = $w_o / $k;
				}
				
				if (isset($h_o) && isset($w_o))
				{
					
					$img_o = imagecreatetruecolor($w_o, $h_o);
					
					if($type == IMAGETYPE_JPEG ) { 
						$image = imagecreatefromjpeg($save_file_name);
					} elseif( $type == IMAGETYPE_GIF ) {
						$image = imagecreatefromgif($save_file_name);
					} elseif( $type == IMAGETYPE_PNG ) {
						$image = imagecreatefrompng($save_file_name);
					}
					
					imagecopyresampled($img_o, $image, 0, 0, 0, 0, $w_o, $h_o, $w_i, $h_i);
					
					if($type == IMAGETYPE_JPEG ) {
						imagejpeg($img_o,$save_file_name,75);
					} elseif($type == IMAGETYPE_GIF ) {
						imagegif($img_o,$save_file_name);
					} elseif($type == IMAGETYPE_PNG ) {
						imagepng($img_o,$save_file_name);
					}
				}
				
				if ($create == true)
				{
					$q = 'INSERT INTO '.$this->table_name.'
						  (`tag`,`file_name`,`title`, `order`)
						  VALUES ('.DB::quote($tag).', '.DB::quote($url_file).', '.DB::quote($title).', '.$order.')';
				} else {
					if ($set_order != false && isset($att[$set_order]))
					{
						$remove_file = DOCROOT.$att[$set_order]['file_name'];
						if (is_file($remove_file))
						{
							chmod($remove_file, 0777);
							unlink($remove_file);
						}
					}
					$q = 'UPDATE '.$this->table_name.'
						  SET
							`file_name` = '.DB::quote($url_file).', 
							`title` = '.DB::quote($title).'
						  WHERE `tag`='.DB::quote($tag).' AND `order`='.$order;
				}
				
				if ($req = DB::query($q))
				{
					$res = $this->get($tag, $order);
					return $res;
				} else {
					return '{error: "not add attach in db"}';
				}
				/*				
				
				*/
			} else {
				return '{error: "not load image"';
			}
		}
		return false;
	}
	
	public function remove_attach($tag = false, $order = false)
	{
		if ($tag != false)
		{
			if ($order == false)
			{
				//remove all orders by tag
				
				if ($att = $this->get($tag))
				{
					$q = 'DELETE FROM '.$this->table_name.' WHERE `tag`='.DB::quote($tag);
				
					if ($req = DB::query($q))
					{
						foreach ($att as $item)
						{
							$remove_file = DOCROOT.$item['file_name'];
							if (is_file($remove_file))
							{
								chmod($remove_file, 0777);
								unlink($remove_file);
							}
						}
						return true;
					}
				}
				
				return true;
				
			}
			else 
			{
			
				$att = $this->get($tag, $order);
				
				$remove_file = DOCROOT.$att['file_name'];
				if (is_file($remove_file))
				{
					chmod($remove_file, 0777);
					unlink($remove_file);
				}
				
				$q = 'DELETE FROM '.$this->table_name.' WHERE `tag`='.DB::quote($tag).' AND `order` ='.$order;
				
				if ($req = DB::query($q))
				{
					if ($att = $this->get($tag))
					{
						if (!isset($att[1])){
							$first = reset($att);
							$qr = 'UPDATE '.$this->table_name.'
								  SET
									`order` = 1
								  WHERE `tag`='.DB::quote($tag).' AND `order`='.$first['order'];
							DB::query($qr);
						}
					}
					return true;
				}
			}
		}
		
	}
	
}