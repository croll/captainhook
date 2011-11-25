<?php
/**
 * MySQL PDO Extension Class (for MySQL 5.0.2 and above)
 * 
 * @author Evgeny Vrublevsky <veg@tut.by>
 * @version 0.4 [19.11.2010]
 */

require_once(dirname(__FILE__)."/pdoex.php");

class MySQL extends PDOEX
{
	public function __construct($host = false, $username = false, $password = false, $dbname = false, $persistent = false, $charset = 'utf8')
	{
		if(is_array($host)) extract($host, EXTR_OVERWRITE);
		
		// Prepare
		$host = explode(':', $host);
		$dsn = "mysql:" 
			. (!empty($host[0]) ? "host={$host[0]};" : "") 
			. (!empty($host[1]) ? "port={$host[1]};" : "")
			. (($dbname) ? "dbname={$dbname};" : "");
		$options = array
		(
			PDO::ATTR_PERSISTENT => $persistent,
			PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES {$charset}",
			// PDO::MYSQL_ATTR_DIRECT_QUERY => false,
		);
		parent::__construct($dsn, $username, $password, $options);
		
		// Enforce strict mode
		$modes = $this->query('SELECT @@session.sql_mode AS sql_mode')->fetchColumn();
		$modes = (empty($modes)) ? array() : array_map('trim', explode(',', $modes));
		if (!in_array('TRADITIONAL', $modes))
		{
			// TRADITIONAL includes STRICT_ALL_TABLES and STRICT_TRANS_TABLES
			if (!in_array('STRICT_ALL_TABLES', $modes))		$modes[] = 'STRICT_ALL_TABLES';
			if (!in_array('STRICT_TRANS_TABLES', $modes))	$modes[] = 'STRICT_TRANS_TABLES';
		}
		$modes = implode(',', $modes);
		$this->exec("SET SESSION sql_mode='{$modes}'");
	}
}
?>