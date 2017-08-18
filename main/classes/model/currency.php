<?php defined('SYSPATH') or die('No direct script access.');

class Model_Currency extends Model 
{
	
	private static $_currency_values = null;

	public function __construct()
	{    
        $this->_get_currency_url = 'https://www.liqpay.ua/exchanges/exchanges.cgi';
		
		parent::__construct();
		
		$this->config = loadConfig('shop_config');
		
		$this->_currency_codes = $this->config['currency_codes'];
		
		return $this;
        
    }
	
	public function codeToName($code = 1)
	{
		if ($code = (int) $code)
		{	
			if (isset($this->_currency_codes['names'][$code]))
			{
				return $this->_currency_codes['names'][$code];
			}
		}
	}
	
	public function nameToCode($name = '')
	{
		if ($name = (string) $name)
		{	
	
			if (isset($this->_currency_codes['prefix'][$name]))
			{
				return $this->_currency_codes['prefix'][$name];
			}
		}
	}
	
	public function loadCurrency()
	{
		if (self::$_currency_values === null)
		{
			if ($currency_values = MCache::get('currency_values'))
			{
				self::$_currency_values = $currency_values;
				return $currency_values;				
			}
			else 
			{
				if ($cf = @file_get_contents($this->_get_currency_url))
				{
					
					$cf = str_replace('RUR','RUB',$cf);
					
					$xml = simplexml_load_string($cf);
					
					$array = json_decode(json_encode($xml),TRUE);
					$currency_values = false;
					foreach ($array as $key=>$value)
					{
						foreach ($value as $name => $curr)
						{
							$currency_values[$name][$key] = 1/((float) $curr);
						}
					}
					
					if ($currency_values)
					{
						
						$req = DB::query('SELECT * FROM currency_values');
						if ($res = $req->fetchAll())
						{
							if (DB::beginTransaction())
							{
								foreach ($res as $v)
								{
									if (isset($currency_values[$this->nameToCode($v['code_key'])]))
									{
										$factor = $currency_values[$this->nameToCode($v['code_key'])][$this->nameToCode($v['code_source'])];
										if ($factor != $v['factor'])
										{
											DB::update('currency_values', array('factor'=>$factor), 'inx='.$v['inx']);
										}
									}
								}
							}
							DB::commitTransaction();
						}
						else 
						{
							
							if (DB::beginTransaction())
							{
								foreach ($currency_values as $key=>$items)
								{
									foreach ($items as $name=>$value)
									{
										$new_curr = array();	
										$new_curr['code_key'] = $this->get_code($key);
										$new_curr['code_source'] = $this->get_code($name);
										$new_curr['factor'] = $value;
										DB::insert('currency_values', $new_curr);
									}
								}
							}
							DB::commitTransaction();
						}
						
						MCache::set('currency_values', $currency_values, 3600);
						return $currency_values;
					}
				}
			}
		}
		else 
		{
			return self::$_currency_values;
		}
	}
	
	public function getUserCurrency()
	{
		
		if ($curr = (int) Cookie::get('user_currency', false))
		{
			return $curr;
		}
		else 
		{
			if ($this->session->isAuth())
			{
				$user = $this->session->user();
				
				if ($fields = $user->getFields())
				{
					if ($curr = (int) $fields['currency'])
					{
						Cookie::set('user_currency', $curr, 15*24*60*60);
						return $curr;
					}
				}
			}
		}
		
		//if not load currency for user from cookie or db, then return default
		$curr = $this->config['default_currency'];
		return $curr;
	}

	
	public function setCurr()
	{
		if (DB::beginTransaction())
		{
			foreach ($this->_currency_codes['prefix'] as $name=>$code)
			{
				DB::update('shop_ads', array('currency_code'=>$code), 'currency = "'.$name.'"');
			}
		}
		DB::commitTransaction();
	}
	
	public function setCodes()
	{
		if (DB::beginTransaction())
		{
			foreach ($this->_currency_codes['prefix'] as $name=>$code)
			{
				DB::update('shop_ads', array('currency_code'=>$code), 'currency_name = "'.$name.'"');
			}
		}
		DB::commitTransaction();
	}
	
}