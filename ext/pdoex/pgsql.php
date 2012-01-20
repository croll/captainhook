<?php
/**
 * PgSQL PDO Extension Class
 * 
 * @author Nicolas Dimitrijevic <niclone@croll.fr>
 * @version 0.1 [20.01.2012]
 */

require_once(dirname(__FILE__)."/pdoex.php");

class PgSQL extends PDOEX {
  	public function exec_returning($statement, $bind = false, $returning = false) {
      $statement.=' RETURNING "'.$returning.'"';
      return $this->fetchOne($statement, $bind);
	}
}
