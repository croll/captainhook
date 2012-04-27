<?php
/**
 * Extended PDO Classes (for PHP 5.2 and above)
 * 
 * @author Evgeny Vrublevsky <veg@tut.by>
 * @version 0.4 [19.11.2010]
 */

class PDOEX extends PDO
{
	/**
	 * Extended PDO class constructor (with original arguments)
	 * 
	 * By default PDO uses PDO::FETCH_BOTH mode. It requires a lot of memory. 
	 * PDO::FETCH_ASSOC is uset there by default.
	 * 
	 * @param string $dsn		Information required to connect to the database
	 * @param string $username	The user name for the DSN string
	 * @param string $password	The password for the DSN string
	 */
	public function __construct($dsn, $username, $password, $options = array())
	{
		$options += array
		(
			PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
			PDO::ATTR_STATEMENT_CLASS => array('PDOStatementEx', array($this)),
			PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
			PDO::ATTR_EMULATE_PREPARES => false,
		);
		
		parent::__construct($dsn, $username, $password, $options);
	}
	
    /**
     * Set default fetch mode
     * 
     * @param integer $mode
     * @return void
     */
	public function setFetchMode($mode)
	{
		$this->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, $mode);
	}
	
    /**
     * Safely quotes a value for an SQL statement
     *
     * In PDO all input values simply converted to string and returned in the 
     * quotes, but this is not always correct behavior. For example, NULL and
     * false values returned as "''" string.
     *
     * @param mixed $value The value to quote
     * @return mixed An SQL-safe quoted string
     */
	public function quote($value, $parameter_type = PDO::PARAM_STR)
	{
		if(is_int($value))		return (string) $value;
		if(is_null($value))		return 'NULL';
		if(is_bool($value))		return (string) intval($value);
		if(is_float($value))	return sprintf('%F', $value);
		if(is_array($value))	return $this->quoteArray($value);
		return parent::quote($value, $parameter_type);
	}
	
	public function quoteArray($items)
	{
		$result = '';
		foreach($items as $item)
		{
			$result .= $this->quote($item) . ',';
		}
		$result = substr($result, 0, -1);
		return $result;
	}
	
	
	/*
	protected $replacements;
	protected function replacePlaceholder($matches)
	{
		if(!isset($this->replacements[$matches[1]])) throw new Exception("Variable {$matches[0]} is undefined");
		return $this->replacements[$matches[1]];
	}
	*/

    /**
	 * Emulation of the prepared statements syntax without regexps!
	 * TODO: если передаётся массив, то проверять чтобы он подставлялся в скобочки! (типа защита от подставы)
	 */
	public function buildQuery($statement, $bind = false)
	{
		if(!$bind) return $statement;
		if(!is_array($bind)) throw new Exception('bind must be an array'); // $bind = array($bind);
		$is_vector = count(array_diff_key($bind, array_keys($bind))) === 0;
		if($is_vector)
		{
			// TODO: Ignore placeholders in the quoted strings
			$parts = explode('?', $statement);
			$build = array_shift($parts);
			if(count($parts) != count($bind))
			{
				throw new PDOException("Invalid parameters count");
			}
			while(count($parts))
			{
				$build .= $this->quote(array_shift($bind));
				$build .= array_shift($parts);
			}
			return $build;
		}
		else
		{
			$replacements = array();
			foreach($bind as $key => $value) $replacements[':'.$key] = $this->quote($value);
			return strtr($statement, $replacements);
			/*
			$this->replacements = array();
			foreach($bind as $key => $value)
			{
				$this->replacements[$key] = $this->quote($value);
			}
			return preg_replace_callback('#\B:([a-z0-9_]+)\b#i', array($this, 'replacePlaceholder'), $statement);
			*/
		}
	}

	public function query($statement, $bind = false)
	{
		if($bind)
		{
			$statement = $this->buildQuery($statement, $bind);
			// $prepared = $this->prepare($statement);
			// $prepared->execute($bind);
			// return $prepared;
		}
		return parent::query($statement);
	}
	
	public function exec($statement, $bind = false)
	{
		if($bind)
		{
			$statement = $this->buildQuery($statement, $bind);
		}
		return parent::exec($statement);
	}
	
	public function exec_returning($statement, $bind = false, $returning = false)
	{
		if($bind)
		{
			$statement = $this->buildQuery($statement, $bind);
		}
		$ret=parent::exec($statement);
        if ($returning) return $this->lastInsertId();
        else return $ret;
	}
	
    /**
     * Fetches the first column of the first row of the SQL result
     */
	public function fetchOne($sql, $bind = false)
	{
		$query = $this->query($sql, $bind);
		$result = $query->fetchOne();
		$query->closeCursor();
		return $result;
	}
	
    /**
     * Fetches the first row of the SQL result
     */
	public function fetchRow($sql, $bind = false)
	{
		$query = $this->query($sql, $bind);
		$result = $query->fetchRow();
		$query->closeCursor();
		return $result;
	}

	/**
     * Fetches the first column of all SQL result rows as an array.
     */
	public function fetchCol($sql, $bind = false)
	{
		return $this->query($sql, $bind)->fetchCol();
	}
	
	/**
     * Fetches all rows as an array.
     */
	public function fetchAll($sql, $bind = false)
	{
		return $this->query($sql, $bind)->fetchAll();
	}
	
    /**
     * Fetches all SQL result rows as an array of key-value pairs.
     * The first column is the key, the second column is the value.
     */
	public function fetchPairs($sql, $bind = false)
	{
		return $this->query($sql, $bind)->fetchPairs();
	}

	/**
     * Fetches all SQL result rows as an associative array.
     * The first column is the key, the entire row array is the value. Rows with 
     * duplicate values in the first column will overwrite previous data.
	 */
	public function fetchAssoc($sql, $bind = false)
	{
        return $this->query($sql, $bind)->fetchAssoc();
	}
}

/**
 * PDOStetement Extended
 */
class PDOStatementEx extends PDOStatement
{
    protected $db;
	
    protected function __construct($db) 
	{
		$this->db = $db;
	}
	
    /**
     * Fetches the first column of the first row of the SQL result. 
     */
	public function fetchOne()
	{
		return $this->fetchColumn(0);
	}
	
    /**
     * Fetches the first row of the SQL result.
     */
	public function fetchRow()
	{
		return $this->fetch();
	}
	
	/**
     * Fetches the first column of all SQL result rows as an array.
     */
	public function fetchCol()
	{
		return $this->fetchAll(PDO::FETCH_COLUMN, 0);
	}
	
    /**
     * Fetches all SQL result rows as an array of key-value pairs.
     * The first column is the key, the second column is the value.
     */
	public function fetchPairs()
	{
		return $this->fetchAll(PDO::FETCH_KEY_PAIR);
	}

	/**
     * Fetches all SQL result rows as an associative array.
     * The first column is the key, the entire row array is the value. Rows with 
     * duplicate values in the first column will overwrite previous data.
	 */
	public function fetchAssoc()
	{
        while ($row = $this->fetch()) 
		{
            $index = reset($row);
            $data[$index] = $row;
        }
        return $data;
	}
	
	/**
     * Returns a list of columns in current resultset
	 */
	public function getColumns()
	{
		$count = $this->columnCount();
		$names = array();
		for($index = 0; $index < $count; $index++)
		{
			$meta = $this->getColumnMeta($index);
			$names[] = $meta['name'];
		}
		return $names;
	}
}
?>