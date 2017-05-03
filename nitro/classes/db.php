<?php

Class DB 
{

	private static $_init = false;
    private static $_database = false;
    private static $_transaction = false;
	
	public static $profile_list = array();

    public static function init()
	{	
	
		if (!self::$_init)
		{
    
			$conf = loadConfig('database');
			$conf = $conf[$conf['use']];
			
			if ($conf)
			{
				$db = new PDO($conf['type'].':host='.$conf['hostname'].';dbname='.$conf['database'].';charset=UTF8', $conf['username'], $conf['password'], array(PDO::ATTR_PERSISTENT => true));
				$db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
				$db->setAttribute(PDO::MYSQL_ATTR_INIT_COMMAND, "SET NAME 'utf8'; SET GLOBAL innodb_stats_on_metadata=0;");
				
				self::$_database = $db;				
			}
			self::$_init = true;			
		}
		else
		{
			if (self::$_database)
				return true;
			else 
				return false;
		}
			
		return false;
    
    }
	
	public static function query($qr)
	{
		try {
			
			$qr = (string) $qr;
			
			if (defined('DEBUG_MODE') && DEBUG_MODE) $start = microtime(true);
		
			if ($res = self::$_database->query($qr))
			{
				if (defined('DEBUG_MODE') && DEBUG_MODE) DB::$profile_list[] = array('time'=> microtime(true)-$start, 'qery'=>$qr);
				if (!(strpos(trim($qr),'INSERT')===false) && $res){
					return array('0'=>self::$_database->lastInsertId());
				}
				
				return $res;
			}
			else 
			{
				$error = self::$_database->errorInfo();
				if (isset($error[2]))
				{
					throw new NException($error[2]);
				}
				else 
				{
					throw new NException($e->getMessage());
				}
			}
			return false;
		} 
		catch (Exception $e) 
		{
			
			if (self::$_transaction)
			{
				self::$_database->rollBack();
			}
			
			$error = self::$_database->errorInfo();
			if (isset($error[2]))
			{
				throw new NException($error[2]);
			}
			else 
			{
				throw new NException($e->getMessage());
			}
		}
	}
	
	public static function insert($table_name = '', $data = false)
	{
		$table_name = trim($table_name);
		if (empty($table_name) && empty($data) && empty($where))
		{
			throw new NException('no data for insert into "'.$table_name.'"');
		}
		else {
			
			$q = 'INSERT into `'.trim(self::$_database->quote($table_name),"'").'`';
			$items = ''; $values = '';
			foreach ($data as $key => $value)
			{
				$items .= trim($key).', ';
				$values .= self::$_database->quote((string) $value).', ';
			}
			$q .= ' ('.rtrim($items, ', ').') VALUES ('.rtrim($values, ', ').')';
			if ($res = self::query($q))
			{
				return $res;
			}
			return false;
		}
	}
    
	public static function update($table_name = '', $data = false, $where = false)
	{
		$table_name = trim($table_name);
		if (empty($table_name) && empty($data) && empty($where))
		{
			throw new NException('no data for update');
		}
		else {
			
			$q = 'UPDATE `'.trim(self::$_database->quote($table_name),"'").'`';
			$q .= ' SET ';
			$s = '';
			foreach ($data as $key => $value)
			{
				$s .= '`'.trim($key).'` = '.self::$_database->quote(trim((string) $value)).', ';
			}
			$q .= rtrim($s, ', ').' WHERE '.trim((string) $where);
			return self::query($q);
		}
	}
    
    public static function quote($qr){
        return self::$_database->quote($qr);
    }
	
	public static function beginTransaction()
	{
		return self::$_transaction = self::$_database->beginTransaction();
	}
	
	public static function commitTransaction()
	{
		if (self::$_transaction)
		{
			return self::$_database->commit();
		}
		return false;
	}
	
	public static function asyncQuery($qr, $wait = false)
	{
		$qr = (string) $qr;
		
		try 
		{
			if (defined('MYSQLI_ASYNC'))
			{
				if ($wait)
				{
					$conf = loadConfig('database');
					$conf = $conf[$conf['use']];
				
					$mysqli = new mysqli($conf['hostname'], $conf['username'], $conf['password'], $conf['database']);
					
					if ($req = $mysqli->query($qr, MYSQLI_ASYNC))
					{
							$result = $mysqli->reap_async_query();
							return $resultArray = $result->fetch_assoc();
							$mysqli->close();
					}					
				}
				else
				{
					register_shutdown_function('DB::_shutQuery', $qr);
					return true;					
				}
				
			}
			else 
			{
				return self::query($qr);
			}
		}
		catch (Exception $e) 
		{
			
			if (self::$_transaction)
			{
				self::$_database->rollBack();
			}
			
			$error = self::$_database->errorInfo();
			if (isset($error[2]))
			{
				throw new NException($error[2]);
			}
			else 
			{
				throw new NException($e->getMessage());
			}
		}
	}
	
	public static function _shutQuery($qr)
	{
		$conf = loadConfig('database');
		$conf = $conf[$conf['use']];
		
		$mysqli = new mysqli($conf['hostname'], $conf['username'], $conf['password'], $conf['database']);
		$res = $mysqli->query($qr, MYSQLI_ASYNC);
		//$res = $mysqli->use_result();
		//$mysqli->close();
		return true;
	}
	
}

?>