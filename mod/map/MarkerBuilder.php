<?php

namespace mod\map;

class MarkerBuilder {

	protected $_imaObj;
	protected $_drawObj;
	protected $_verticalOffset = 1;
	protected $_horizontalOffset = 0;
	protected $_params;
	protected $_rotation;

	function __construct($params) {
		$this->_params = $params;
		$this->_imgObj = new \Imagick();
		$this->_imgObj->newImage($params['size'][0], $params['size'][1], "none");
		$this->_drawObj = new \ImagickDraw();
		$this->_drawObj->setFillColor($params['color']);

		if (isset($params['alpha'])) {
			$this->_drawObj->setFillOpacity($params['alpha']);
			$this->_drawObj->setStrokeOpacity($params['alpha']);
		}


		if ($params['strokewidth']) {
			$this->_drawObj->setStrokeWidth($params['strokewidth']);
		}

		if ($params['strokecolor']) {
			$this->_drawObj->setStrokeColor($params['strokecolor']);
		}

		$this->draw();
		if ($params['text']) {
			$this->_drawObj->setFontSize(12);
			$this->_drawObj->setFillColor('#ffffff');
			$this->_drawObj->setStrokeColor('transparent');
			$metrics = $this->_imgObj->queryFontMetrics($this->_drawObj, $params['text']);
			$this->_drawObj->annotation(($params['size'][0]/2)-($metrics['textWidth']/2)+$this->_horizontalOffset, ($params['size'][1]/2)-$metrics['descender']+$this->_verticalOffset, $params['text']);
		}
		$this->_imgObj->drawImage($this->_drawObj);
		if ($this->_rotation)
			$this->_imgObj->rotateImage(new \ImagickPixel('none'), $this->_rotation);
	}

	function getIconPath() {
		$this->_imgObj->setImageFormat('png');
		$this->_imgObj->writeImage(CH_MODDIR.'/map/cache/'.$this->_params['filename']);
		$this->_imgObj->destroy();
		return '/mod/map/cache/'.$this->_params['filename'];
	}

	function draw() {
	}

}

class MarkerCircle extends MarkerBuilder {

	function draw() {
		$this->_drawObj->circle($this->_params['size'][0]/2, $this->_params['size'][0]/2, ($this->_params['size'][1]/2)-$this->_params['strokewidth'], $this->_params['size'][1]-$this->_params['strokewidth']);  
	}

}

class MarkerSquare extends MarkerBuilder {

	function draw() {
		$this->_drawObj->rectangle(0, 0, $this->_params['size'][0]-$this->_params['strokewidth'], $this->_params['size'][1]-$this->_params['strokewidth']);  
	}

}

class MarkerRectangle extends MarkerBuilder {

	function draw() {
		$this->_drawObj->rectangle(0, ($this->_params['size'][1]/4)+$this->_params['strokewidth'], $this->_params['size'][0]-$this->_params['strokewidth'], ($this->_params['size'][1]-($this->_params['size'][1]/4))-$this->_params['strokewidth']);  
	}

}

class MarkerRoundrectangle extends MarkerBuilder {

	function draw() {
		$this->_drawObj->rectangle(0, ($this->_params['size'][1]/4)+$this->_params['strokewidth'], $this->_params['size'][0]-$this->_params['strokewidth'], ($this->_params['size'][1]-($this->_params['size'][1]/4))-$this->_params['strokewidth']);  
	}

}

class MarkerDiamond extends MarkerBuilder {

	function draw() {
		$this->_drawObj->rectangle($this->_params['strokewidth'], $this->_params['strokewidth'], $this->_params['size'][0]-$this->_params['strokewidth'], $this->_params['size'][1]-$this->_params['strokewidth']);  
		$this->_rotation = 45;
	}

}

class MarkerTriangle extends MarkerBuilder {

	function draw() {
		$this->_verticalOffset = 3;
		$coords = array();
		$coords[] = array('x' => $this->_params['size'][0]/2-$this->_params['strokewidth']+1, 'y' => $this->_params['strokewidth']);
		$coords[] = array('x' => $this->_params['size'][0]-$this->_params['strokewidth'], 'y' => $this->_params['size'][1]-$this->_params['strokewidth']);
		$coords[] = array('x' => $this->_params['strokewidth'], 'y' => $this->_params['size'][1]-$this->_params['strokewidth']);
		$this->_drawObj->polygon($coords);
	}

}

class MarkerTrianglerectangle extends MarkerBuilder {

	function draw() {
		$this->_verticalOffset = 3;
		$this->_horizontalOffset = -3;
		$coords = array();
		$coords[] = array('x' => $this->_params['strokewidth'], 'y' => $this->_params['strokewidth']);
		$coords[] = array('x' => $this->_params['size'][0]-$this->_params['strokewidth'], 'y' => $this->_params['size'][1]-$this->_params['strokewidth']);
		$coords[] = array('x' => $this->_params['strokewidth'], 'y' => $this->_params['size'][1]-$this->_params['strokewidth']);
		$this->_drawObj->polygon($coords);
	}

}

class MarkerTrianglerectangleinverted extends MarkerBuilder {

	function draw() {
		$this->_verticalOffset = 3;
		$this->_horizontalOffset = -3;
		$coords = array();
		$coords[] = array('x' => $this->_params['strokewidth'], 'y' => $this->_params['strokewidth']);
		$coords[] = array('x' => $this->_params['size'][0]-$this->_params['strokewidth'], 'y' => $this->_params['size'][1]-$this->_params['strokewidth']);
		$coords[] = array('x' => $this->_params['strokewidth'], 'y' => $this->_params['size'][1]-$this->_params['strokewidth']);
		$this->_drawObj->polygon($coords);
		$this->_rotation = 180;
	}

}

class MarkerParallelogram extends MarkerBuilder {

	function draw() {
		$this->_horizontalOffset = -3;
		$coords = array();
		$coords[] = array('x' => 0, 'y' => $this->_params['strokewidth']);
		$coords[] = array('x' => (($this->_params['size'][0]/3)*2)-$this->_params['strokewidth'], 'y' => $this->_params['strokewidth']);
		$coords[] = array('x' => $this->_params['size'][0]-$this->_params['strokewidth'], 'y' => $this->_params['size'][1]-$this->_params['strokewidth']);
		$coords[] = array('x' => ($this->_params['size'][0]/3), 'y' => $this->_params['size'][1]-$this->_params['strokewidth']);
		$this->_drawObj->polygon($coords);
	}

}
