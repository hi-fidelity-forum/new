<?php defined('SYSPATH') or die('No direct script access.');

class View {

	// Array of global variables
	protected static $_global_data = array();
    
    public static $_time_now = false;

	/**
	 * Returns a new View object. If you do not define the "file" parameter,
	 * you must call [View::set_filename].
	 *
	 *     $view = View::factory($file);
	 *
	 * @param   string  $file   view filename
	 * @param   array   $data   array of values
	 * @return  View
	 */
	public static function factory($file = NULL, array $data = NULL)
	{
		return new View($file, $data);
	}

	protected static function capture($view_filename, array $view_data)
	{
		// Import the view variables to local namespace
		extract($view_data, EXTR_SKIP);

		if (View::$_global_data)
		{
			// Import the global view variables to local namespace
			extract(View::$_global_data, EXTR_SKIP | EXTR_REFS);
		}
		
		// Capture the view output
		
		ob_start();
		
		try
		{
			// Load the view within the current scope
			include $view_filename;
		}
		catch (Exception $e)
		{
			// Delete the output buffer
			//ob_end_clean();
			ob_end_flush();

			// Re-throw the exception
			throw $e;
		}

		// Get the captured output and close the buffer
		return ob_get_clean();
	}

	public static function set_global($key, $value = NULL)
	{
		if (is_array($key))
		{
			foreach ($key as $key2 => $value)
			{
				View::$_global_data[$key2] = $value;
			}
		}
		else
		{
			View::$_global_data[$key] = $value;
		}
	}

	public static function bind_global($key, & $value)
	{
		View::$_global_data[$key] =& $value;
	}

	// View filename
	protected $_file;

	// Array of local variables
	protected $_data = array();

	public function __construct($file = NULL, array $data = NULL)
	{
		if ($file !== NULL)
		{
			$this->set_filename($file);
		}

		if ($data !== NULL)
		{
			// Add the values to the current data
			$this->_data = $data + $this->_data;
		}
        
        if (!View::$_time_now){
            View::$_time_now = getdate();
        }
	}

	public function & __get($key)
	{
		if (array_key_exists($key, $this->_data))
		{
			return $this->_data[$key];
		}
		elseif (array_key_exists($key, View::$_global_data))
		{
			return View::$_global_data[$key];
		}
		else
		{
			throw new Exception('View variable is not set: :var',
				array(':var' => $key));
		}
	}

	/**
	 * Magic method, calls [View::set] with the same parameters.
	 *
	 *     $view->foo = 'something';
	 *
	 * @param   string  $key    variable name
	 * @param   mixed   $value  value
	 * @return  void
	 */
	public function __set($key, $value)
	{
		$this->set($key, $value);
	}

	/**
	 * Magic method, determines if a variable is set.
	 *
	 *     isset($view->foo);
	 *
	 * [!!] `NULL` variables are not considered to be set by [isset](http://php.net/isset).
	 *
	 * @param   string  $key    variable name
	 * @return  boolean
	 */
	public function __isset($key)
	{
		return (isset($this->_data[$key]) OR isset(View::$_global_data[$key]));
	}

	/**
	 * Magic method, unsets a given variable.
	 *
	 *     unset($view->foo);
	 *
	 * @param   string  $key    variable name
	 * @return  void
	 */
	public function __unset($key)
	{
		unset($this->_data[$key], View::$_global_data[$key]);
	}

	/**
	 * Magic method, returns the output of [View::render].
	 *
	 * @return  string
	 * @uses    View::render
	 */
	public function __toString()
	{
		try
		{
			return $this->render();
		}
		catch (Exception $e)
		{
			// Display the exception message
			throw new NException($e);
		}
	}

	/**
	 * Sets the view filename.
	 *
	 *     $view->set_filename($file);
	 *
	 * @param   string  $file   view filename
	 * @return  View
	 * @throws  View_Exception
	 */
	public function set_filename($file)
	{
		if (($path = findFile('views',$file)) === FALSE)
		{
			throw new Exception('The requested view '.$file.' could not be found');
		}

		// Store the file path locally
		$this->_file = $path;

		return $this;
	}

	/**
	 * Assigns a variable by name. Assigned values will be available as a
	 * variable within the view file:
	 *
	 *     // This value can be accessed as $foo within the view
	 *     $view->set('foo', 'my value');
	 *
	 * You can also use an array to set several values at once:
	 *
	 *     // Create the values $food and $beverage in the view
	 *     $view->set(array('food' => 'bread', 'beverage' => 'water'));
	 *
	 * @param   string  $key    variable name or an array of variables
	 * @param   mixed   $value  value
	 * @return  $this
	 */
	public function set($key, $value = NULL)
	{
		if (is_array($key))
		{
			foreach ($key as $name => $value)
			{
				$this->_data[$name] = $value;
			}
		}
		else
		{
			$this->_data[$key] = $value;
		}

		return $this;
	}

	/**
	 * Assigns a value by reference. The benefit of binding is that values can
	 * be altered without re-setting them. It is also possible to bind variables
	 * before they have values. Assigned values will be available as a
	 * variable within the view file:
	 *
	 *     // This reference can be accessed as $ref within the view
	 *     $view->bind('ref', $bar);
	 *
	 * @param   string  $key    variable name
	 * @param   mixed   $value  referenced variable
	 * @return  $this
	 */
	public function bind($key, & $value)
	{
		$this->_data[$key] =& $value;

		return $this;
	}

	/**
	 * Renders the view object to a string. Global and local data are merged
	 * and extracted to create local variables within the view file.
	 *
	 *     $output = $view->render();
	 *
	 * [!!] Global variables with the same key name as local variables will be
	 * overwritten by the local variable.
	 *
	 * @param   string  $file   view filename
	 * @return  string
	 * @throws  View_Exception
	 * @uses    View::capture
	 */
	public function render($file = NULL)
	{
		if ($file !== NULL)
		{
			$this->set_filename($file);
		}

		if (empty($this->_file))
		{
			throw new View_Exception('You must set the file to use within your view before rendering');
		}
		
        // Combine local and global data and capture the output
		return View::capture($this->_file, $this->_data);
	}
    
    public static function cutString($string, $maxlen, $strip_tags = true){
        
        if (mb_strlen($string, 'UTF-8') > $maxlen)
		{
			if ($strip_tags)
			{
				$string = mb_substr(html_entity_decode(strip_tags($string),ENT_QUOTES,'utf-8'),0,$maxlen,'utf-8');
			} else {
				$string = mb_substr(html_entity_decode($string,ENT_QUOTES,'utf-8'),0,$maxlen,'utf-8');
			}
            $string .= '...';
            
        }
        
        return $string;
    }
    
    public static function format_date($date = false, $seek = false, $show_time = true){
        if ($date = (int) $date){
        
            $res = $date;
            
            $now = View::$_time_now;
            
            $d = getdate($date);
			
			if ($show_time === true)
			{            
				if (($d['year'] == $now['year']) && ($d['yday'] == $now['yday'])){
					return ($seek?'':'Сегодня ').date('H:i',$date);
				} else if (($d['year'] == $now['year']) && ($d['yday'] - $now['yday'] == 1)){
					return ($seek?'':'Вчера ').date('H:i',$date);
				} 
				
				return date('d-m-Y H:i', $date);
            }
			else 
			{
				if (($d['year'] == $now['year']) && ($d['yday'] == $now['yday'])){
					return ($seek?'':'Сегодня ').date('H:i',$date);
				} else if (($d['year'] == $now['year']) && ($now['yday'] - $d['yday'] == 1)){
					return ($seek?'':'Вчера ').date('H:i',$date);
				} 
				
				return date('d-m-Y', $date);
			}
        }
        
    }
	
	static function htmlspecialchars_uni($message)
	{
		$message = preg_replace("#&(?!\#[0-9]+;)#si", "&amp;", $message); // Fix & but allow unicode
		$message = str_replace("<", "&lt;", $message);
		$message = str_replace(">", "&gt;", $message);
		$message = str_replace("\"", "&quot;", $message);
		return $message;
	}

} // End View
