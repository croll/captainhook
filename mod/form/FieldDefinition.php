<?php

namespace mod\form;

abstract class Field {

	public $name;
	public $description;
	public $id;
	private static $_cache = NULL;

	function __construct() {
		if (!isset(self::$_cache) || !isset(self::$_cache[$this->name]) || empty(self::$_cache[$this->name]['id'])) {
			if (!is_array(self::$_cache))
				self::$_cache = array();
			$modinfos = Core::$db->GetRow('SELECT `did` AS id, `name`, `description` FROM ch_field_definition WHERE name=?',
				array($this->name));
			if ($modinfos)
				self::$_cache[$this->name] = $modinfos;
		} 
		if (isset(self::$_cache[$this->name]) && is_array(self::$_cache[$this->name])) {
			$this->id = (isset(self::$_cache[$this->name]['id']) && (self::$_cache[$this->name]['id'])) ? self::$_cache[$this->name]['id'] : NULL;
		} else {
			$this->id = NULL;
		}
	}

	function add() {
		Core::$db->Execute('INSERT INTO `ch_field_definition` (`name`, `description`, `fid`, `foid)', 
												array($this->name, $this->description));
		return (isset(Core::$db->Insert_ID)) ? Core::$db->Insert_ID : NULL;
	}

	function del() {
			Core::$db->Execute('DELETE FROM ch_field_definiion WHERE `did` = ? ', 
																array($this->id));

			if (!Core::$db->Affected_Rows()) {
				throw new \Exception("Module was not installed in database");
				return false;
			}

			Core::$db->Execute('DELETE FROM ch_field_definition WHERE `fid` = ? ', 
																array($this->id));
      $this->id = null;
	}

	function getList($name) {
		$result = Core::$db->Execute('SELECT * FROM `ch_field_definition` WHERE `did`=?',
															array($this->id));
		$r = array();
		while($row = $result->FetchNextObject()) {
			$r[] = $row;
		}
		return (sizeof($r) > 0) ? $r : NULL;
	}

}
