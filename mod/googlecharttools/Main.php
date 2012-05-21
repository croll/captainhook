<?php  //  -*- mode:php; tab-width:2; c-basic-offset:2; -*-

namespace mod\googlecharttools;

class Main {

	private $_datas = array();
	private $_rows = array();

	function __construct() {
		$this->_datas['cols'] = array();
		$this->_datas['cels'] = array();
	}

	function addColumn($label, $type, $id=null, $pattern=null, $p) {
		if (!in_array($type, array('boolean', 'number', 'string', 'date', 'datetime', 'timeofday'))) {
			throw new \Exception('Data type invalid');
		}
		$this->_datas['cols'][] = array('id' => $id, 'label' => $label, 'pattern' => $pattern, 'type' => $type, 'p' => $p);
	}

	function addRow($value, $stringValue=null, $p=null) {
		$this->_rows[] = array('v' => $value, 'f' => $stringValue, 'p' => null);
	}

	function addP($p) {
		$this->_datas['p'] = $p;
	}

	function getJSON() {
		foreach(array_chunk($this->_rows, sizeof($this->_datas['cols'])) as $rows) {
			$this->_datas['rows'][]['c'] = $rows;
		}
		return json_encode($this->_datas);
	}

}
