<?php
/**
 * SQLite3 PDO Extension Class
 * 
 * @author Evgeny Vrublevsky <veg@tut.by>
 * @version 0.4 [19.11.2010]
 */

require_once(dirname(__FILE__)."/pdoex.php");

class SQLite extends PDOEX
{
	public function __construct($filename, $persistent = false)
	{
		if(is_array($host)) extract($host, EXTR_OVERWRITE);
		
		// Prepare
		$dsn = "sqlite:{$filename}";
		$options = array
		(
			PDO::ATTR_PERSISTENT => $persistent,
		);
		parent::__construct($dsn, false, false, $options);
	}
}
?>