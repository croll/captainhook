<?php

namespace mod\map;

class Marker {

	private $_marker = array();

	function __construct($type, $geometry) {
		$this->_marker['type'] = $type;
		$this->_marker['geometry'] = $geometry;
	}

	function setGeometry($geometry) {
		$this->_marker['geometry'] = $params['geometry'];
	}

	function setId($id) {
		$this->_marker['id'] = $id;
	}

	function setIconParams($params) {
		$filename = md5(serialize($params)).'.png';
		$cachedImage = CH_MODDIR.'/map/cache/'.$filename;
		if (!is_file($cachedImage)) {
			$params['filename'] = $filename;
			$markerImage = self::buildIcon($params);
		} else
			$markerImage = '/mod/map/cache/'.$filename;
		$this->_marker['icon']['iconUrl'] = $markerImage;
		$this->_marker['icon']['iconSize'] = $params['size'];
	}

	public function get() {
		return $this->_marker;
	}

	function setPopupParams($params) {
		$this->_marker['popup'] = $params;
	}

	public static function buildIcon($params) {
		require_once(dirname(__FILE__).'/MarkerBuilder.php');
		$className = '\\mod\\map\\Marker'.ucfirst($params['shape']);
		try {
			$class = new $className($params);
			return $class->getIconPath();
		} catch (\Exception $e) {
			\core\Core::log($e->getMessage());
		}
	}

}
?>
