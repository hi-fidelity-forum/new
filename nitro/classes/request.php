<?php defined('SYSPATH') or die('No direct script access.');

class Request
{

	public static $base_url = '/';
	public static $index_file = 'index.php';
    public static $user_agent = '';
	public static $client_ip = '0.0.0.0';
    public static $trusted_proxies = array('127.0.0.1', 'localhost', 'localhost.localdomain');
	public static $current;
	public static $_app = false;
	
	protected static $initial = false;
	
	protected static $_route;
	protected static $_cache_time = 10;
	protected static $controller_prefix = 'controller_';
	protected static $action_prefix = 'action_';
	
	
	
	protected $_requested_with;
    protected $_method;
	protected $_protocol;
	protected $_secure;
	protected $_referrer;
	protected $_routes;
	protected $_response;
	protected $_header;
	protected $_body;
	protected $_directory = '';
	protected $_controller;
	protected $_action;
	protected $_uri;
	protected $_external = FALSE;
	protected $_params = array();
	protected $_get = array();
	protected $_post = array();
	protected $_cookies = array();
	protected $_client;
	
	public function __construct()
	{
		
		if (Request::$initial != NULL)
		{
			return Request::$initial;
		}
		else
		{
			$this->_uri = $uri = $uri = $this->_uri = trim($this->detect_uri(), '/');;
			
			$this->_get = $_GET;
			$this->_post = $_POST;
			
			$this->_protocol = isset($_SERVER['SERVER_PROTOCOL'])?$_SERVER['SERVER_PROTOCOL']:'HTTP/1.0';
			$this->_method = isset($_SERVER['REQUEST_METHOD'])?$_SERVER['REQUEST_METHOD']:'GET';
			
			$this->_secure = !empty($_SERVER['HTTPS']) AND filter_var($_SERVER['HTTPS'], FILTER_VALIDATE_BOOLEAN);
			$this->_referrer = isset($_SERVER['HTTP_REFERER'])?$_SERVER['HTTP_REFERER']:FALSE;
				
			Request::$user_agent = isset($_SERVER['HTTP_USER_AGENT'])?$_SERVER['HTTP_USER_AGENT']:FALSE;
			
			$this->_requested_with = isset($_SERVER['HTTP_X_REQUESTED_WITH'])?$_SERVER['HTTP_X_REQUESTED_WITH']:FALSE;
				
			if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])
				AND isset($_SERVER['REMOTE_ADDR'])
				AND in_array($_SERVER['REMOTE_ADDR'], Request::$trusted_proxies))
			{
				$client_ips = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
				Request::$client_ip = array_shift($client_ips);
				unset($client_ips);
			}
			elseif (isset($_SERVER['HTTP_CLIENT_IP'])
					AND isset($_SERVER['REMOTE_ADDR'])
					AND in_array($_SERVER['REMOTE_ADDR'], Request::$trusted_proxies))
			{
				$client_ips = explode(',', $_SERVER['HTTP_CLIENT_IP']);
				Request::$client_ip = array_shift($client_ips);
				unset($client_ips);
			}
			elseif (isset($_SERVER['REMOTE_ADDR']))
			{
				Request::$client_ip = $_SERVER['REMOTE_ADDR'];
			}
			//var_export($uri);
			Request::$_route = $route = $this->getRoute($uri);
			
			return Request::$initial = $this;
		}
	}

	public function execute()
	{
		
		$params = Request::$_route;
		
		if ($params === NULL || $params === false)
		{
			$uri = $this->_uri;
			throw new Exception('Unable to find a route to match the URI: '.$uri);
			return false;
		}
		
		if (isset($params['directory']))
		{
			// Controllers are in a sub-directory
			$this->_directory = $params['directory'];
		}
		
		if (isset($params['app']))
		{
			// Controllers are in a sub-directory
			if ($app_dir = trim((string) $params['app'], ' /'))
			{
				self::$_app = $app_dir.DS;
			}			
		}

		// Store the controller
		$this->_controller = $params['controller'];
		
		if (isset($params['action']))
		{
			// Store the action
			$this->_action = $params['action'];
		}
		else
		{
			// Use the default action
			$this->_action = Request::$action_prefix;
		}

		// These are accessible as public vars and can be overloaded
		unset($params['controller'], $params['action'], $params['directory'], $params['app']);

		// Params cannot be changed once matched
		$this->_params = $params;
		
		// Apply the client
		// Run controller 
		
		// Store the currently active request
		$previous = Request::$current;

		// Change the current request to this request
		Request::$current = $this;

		$this->_controller = $controller = Request::$controller_prefix.($this->_directory?$this->_directory.'_':'').$this->_controller;
		
		try
		{
			if (!class_exists($controller))
			{	
				throw new Exception('The requested conreller - "'.$controller.'" was not found on this server.');
			}

			// Load the controller using reflection
			$class = new ReflectionClass($controller);

			if ($class->isAbstract())
			{
				throw new Exception('Cannot create instances of abstract '.$controller);
			}

		// Create a new instance of the controller
		$controller = $class->newInstance($this);

		$class->getMethod('before')->invoke($controller);

		// Determine the action to use
		$action = $this->_action;
		

			if ( ! $class->hasMethod(Request::$action_prefix.$action))
			{
				throw new Exception('The requested URL "'.$this->uri().'" not find action = "action_'.$action.'" in conroller="'.$this->_controller.'"');
			}

			$method = $class->getMethod('action_'.$action);
			$method->invoke($controller);

			$class->getMethod('after')->invoke($controller);
		}
		catch (Exception $e)
		{
			if ($previous instanceof Request)
			{
				Request::$current = $previous;
			}

			throw $e;
		}

		// Restore the previous request
		Request::$current = $previous;

		return $this;
	
	}
	
	public static function detect_uri()
	{
		if (PHP_SAPI === 'cli') {
			GLOBAL $argv;
			$a = $argv;
			unset($a[0]);
			$uri = implode('/', $a);
			return $uri;
		}
		
        if (!empty($_SERVER['PATH_INFO']))
			$uri = $_SERVER['PATH_INFO'];
		else
		{
			if (isset($_SERVER['REQUEST_URI']))
				$uri = $_SERVER['REQUEST_URI'];
			elseif (isset($_SERVER['PHP_SELF']))
				$uri = $_SERVER['PHP_SELF'];
			elseif (isset($_SERVER['REDIRECT_URL']))
				$uri = $_SERVER['REDIRECT_URL'];
			else
				throw new Exception('Unable to detect the URI using PATH_INFO, REQUEST_URI, PHP_SELF or REDIRECT_URL');
			
			if ($request_uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH))
				$uri = $request_uri;
			
			$uri = rawurldecode($uri);
			
			$base_url = parse_url(Request::$base_url, PHP_URL_PATH);
			
			if (strpos($uri, $base_url) === 0)
				$uri = (string) substr($uri, strlen($base_url));

			if (Request::$index_file AND strpos($uri, Request::$index_file) === 0)
				$uri = (string) substr($uri, strlen(Request::$index_file));
			
		}
		return $uri;
	}
	
	public function getRoute($uri)
	{
	
		$uri = (string) $uri;
		
		$e_routes = MCache::get('cache_routes');
		
		if (!$e_routes && $routes = loadConfig('routes'))
		{
		
			$r_key     = '<([a-zA-Z0-9_]++)>';
			$r_segment = '[^/.,;?\n]++';
			$r_escape  = '[.\\+*?[^\\]${}=!|]';
			
			//parse routes for preg_match
			$search = array('#[.\\+*?[^\\]${}=!|]#', '(', ')', '<', '>');
			$replace = array('\\\\$0', '(?:', ')?', '(?P<', '>[^/.,;?\n]++)');
			
			foreach ($routes as $name => $route)
			{
				$e_routes[$name]['default'] = array_merge(array('action' => 'index'), $route['default']);
				$e_routes[$name]['expression'] = '#^'.str_replace($search, $replace, $route['uri']).'$#uD';
			}
			
			MCache::set('cache_routes', $e_routes, Request::$_cache_time);
		}
		
		if ($e_routes)
		{
			foreach ($e_routes as $name=>$rt)
			{	
				
				$expression = $rt['expression'];
				if (preg_match($expression, $uri, $matches))
				{
					$params = $rt['default'];
					
					foreach ($matches as $key => $value)
					{
						if (is_int($key)) 
							continue;

						$params[$key] = $value;
					}
					
					return $params;
				}
			}
		}
		
		return false;
	}
	
	public function uri(){return empty($this->_uri) ? Request::$base_url : $this->_uri;}

	public function param($key = NULL, $default = NULL)
	{
		if ($key === NULL)
		{
			return $this->_params;
		}

		return isset($this->_params[$key]) ? $this->_params[$key] : $default;
	}	
	
	static function route()
	{
		if (isset(Request::$_route) && !empty(Request::$_route))
		{
			// Act as a getter
			return Request::$_route;
		}

		// Act as a setter

		return false;
	}

	public function directory($directory = NULL)
	{
		if ($directory === NULL)
		{
			return $this->_directory;
		}

		$this->_directory = (string) $directory;

		return $this;
	}

	
	public function controller($controller = NULL)
	{
		if ($controller === NULL)
		{
			// Act as a getter
			return $this->_controller;
		}

		// Act as a setter
		$this->_controller = (string) $controller;

		return $this;
	}

	public function action($action = NULL)
	{
		if ($action === NULL)
		{
			// Act as a getter
			return $this->_action;
		}

		// Act as a setter
		$this->_action = (string) $action;

		return $this;
	}
	
	public static function app()
	{
		return self::$_app;
	}
	
	public function controller_uri()
	{
		
		$uri = $this->_uri;
		
		$uri = rtrim(str_replace($this->_action, '', $uri),'/');
		
		return $uri;
	}
	
	public function requested_with($requested_with = NULL)
	{
		if ($requested_with === NULL)
		{
			// Act as a getter
			return $this->_requested_with;
		}

		// Act as a setter
		$this->_requested_with = strtolower($requested_with);

		return $this;
	}
	
	public function isAjax()
	{
		return ($this->requested_with() === 'xmlhttprequest' || (isset($_POST['ajax']) && $_POST['ajax']) || (isset($_GET['ajax']) && $_GET['ajax']));
	}
	
	public function is_ajax()
	{
		return $this->isAjax();
	}
	
	public function body($content = NULL)
	{
		if ($content === NULL)
		{
			// Act as a getter
			return $this->_body;
		}

		// Act as a setter
		$this->_body = $content;

		return $this;
	}
	
	static function redirect($url, $statusCode = 303)
	{
	   header('Location: ' . $url, true, $statusCode);
	   die();
	}
	
	static function initial()
	{
		return Request::$initial;
	}
	
	public static function current()
	{
		return Request::$current;
	}

} // End Request
