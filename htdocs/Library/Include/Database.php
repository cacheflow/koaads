<?php
class Database
{
	private $mysqli;
    private $key = 'default';
    private $module_id;
    private $basic_key = "default";
    private $table_name = "error_db_reports";   //To save any errors
	
	function __construct($module_id = 0)
	{
        $this->module_id = $module_id;
        $this->connect();
        if (!$this->check_configuration())
            exit;
	}
    
    function __destruct()
    {
        if (!empty($this->mysqli))
        {
            foreach ($this->mysqli as $key=>$object)
            {
                $this->mysqli[$key]->close();
                $this->mysqli[$key] = null;
                unset($this->mysqli[$key]);
            }
        }
    } 
    
    //check configuration
    private function check_configuration()
    {
        $this->key = $this->basic_key;
        
        $table = "error_db_reports";
        $columns = array("id"=>true, "uid"=>true, "module_id"=>true, "query"=>true, "error_number"=>true, "error_message"=>true, "date"=>true);
        
        $check['table'] = false;
        $check['columns'] = false;
        $q = "SHOW TABLES";
        if ($result = $this->result($q))
        {
            while ($row = $result->fetch_row())
            {
                if ($row[0] == $table)
                    $check['table'] = true;
            }   
        }
        else
        {
            //Error
        }
        
        if (!$check['table'])
        {
            echo "The `error_db_reports` table doesn't exist.\n";
            return false;
        }
        
        $q = "SHOW COLUMNS FROM {$table}";
        if($result = $this->result($q))
        {
            while ($row = $result->fetch_row())
            {
                if (isset($columns[$row[0]]))
                {
                    unset($columns[$row[0]]); //going through the list and checkng if each column exists.
                }
            }
        }
        else
        {
            //Error
        }
        
        //if the list is empty, all columns exist and all is gravvy
        if (count($columns) > 0)
        {
            foreach ($columns as $col_name=>$whatever)
            {
                echo "The column `{$col_name}` is missing from `{$table}`.";
            }
            return false;
        }
        
        return true;
    }
    
    private function connect()
    {
        if (isset($this->mysqli[$this->key]) && !empty($this->mysqli[$this->key]))
            return;

        $config['host'] = DB_HOST;
        $config['user'] = DB_USER;
        $config['password'] = DB_PASS;
        $config['database'] = DB_NAME;
        
        $this->mysqli[$this->key] = new mysqli($config['host'], $config['user'], $config['password']);
        $this->mysqli[$this->key]->select_db($config['database']);

        if ($this->mysqli[$this->key]->connect_error) 
            die('Connect Error (' . $this->mysqli[$this->key]->connect_errno . ') ' . $this->mysqli[$this->key]->connect_error);

        if (!$this->mysqli[$this->key]->set_charset("utf8"))
            die("The system failed to set default character set to UTF-8");
    }

	function __get($what)
	{
		switch ($what)
		{
			case 'affected_rows' :
				return $this->mysqli[$this->key]->affected_rows;
			
			case 'insert_id' :
				return $this->mysqli[$this->key]->insert_id;
            
            case 'error' :
                return $this->mysqli[$this->key]->error;
                
            case 'errno' :
                return $this->mysqli[$this->key]->errno;  
            
            case 'found' :
                return $this->total();
		}
	}
		
	function __set($what, $to)
	{
		switch($what)
		{  
            case 'key' :
				$this->key = $to; 
                $this->connect();
            break; 
            
            case 'module_id' :
                $this->module_id = $to;
            break;
		}
	}
    
    function total()
    {
        $result = $this->mysqli[$this->key]->query("SELECT FOUND_ROWS()");
        $tmp = $result->fetch_row();
        return $tmp[0];    
    }

	function result($query)
	{
        if (empty($query))
            return false;
            
        if (!($result = $this->mysqli[$this->key]->query($query)))
        {
            $this->record_error($query);
        }
        
        return $result;
	}
    
    function real_escape_string($string)
    {
        return $this->mysqli[$this->key]->real_escape_string($string);
    }
    
    function record_error($query)
    {                                                    
        $uid = (isset($_SESSION['uid'])) ? $_SESSION['uid'] : 0;
        $q = "INSERT INTO {$this->table_name} (uid, module_id, query, error_number, error_message, date) VALUES ";
        $q .= "({$uid}, {$this->module_id}, '" . $this->real_escape_string($query) . "', {$this->mysqli[$this->basic_key]->errno}, ";
        $q .= "'" . $this->real_escape_string($this->mysqli[$this->basic_key]->error) . "', UNIX_TIMESTAMP())";
        if (!$this->mysqli[$this->basic_key]->query($q))
        {
            //Error
            echo "MySQL Error: " . $this->mysqli[$this->basic_key]->error;
            exit;
        }
    }
    
    function reset_error()
    {
        $q = "DELETE FROM {$this->table_name}";
        return $this->result($q);
    }
    
    function get_error_list($offset, $limit, &$total)
    {
        $list = null;
        $q = "SELECT SQL_CALC_FOUND_ROWS * FROM {$this->table_name} ORDER BY date DESC LIMIT {$offset}, {$limit}";
        if ($result = $this->result($q))
        {
            while ($row = $result->fetch_assoc())
            {
                $list[] = $row;    
            }
        }
        
        $total = $this->total();
        return $list;
    }
}
?>