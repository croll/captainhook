<?php

namespace mod\map;

class MarkerBuilder {
	
	protected $_imaObj;
	protected $_drawObj;
	protected $_params;

	function __construct($params) {
		$this->_params = $params;
		$this->_imgObj = new \Imagick();
		$this->_imgObj->newImage($params['size'][0], $params['size'][1], "none");
		$this->_drawObj = new \ImagickDraw();
		$this->_drawObj->setFontSize(12);
		$this->_drawObj->setFillColor(new \ImagickPixel($params['color']));

		if ($params['strokecolor']) {
			$this->_drawObj->setStrokeColor(new \ImagickPixel($params['strokecolor']));
		}
		$this->draw();
		$this->_imgObj->drawImage($this->_drawObj);
	}

	function getIconPath() {
		$this->_imgObj->setImageFormat('png');
		$this->_imgObj->writeImage(CH_MODDIR.'/map/cache/'.$this->_params['filename']);
		return '/mod/map/cache/'.$this->_params['filename'];
	}

	function draw() {
	}

}

class MarkerCircle extends MarkerBuilder {

	function draw() {
		$this->_drawObj->circle($this->_params['size'][0]/2, $this->_params['size'][0]/2, ($this->_params['size'][1]/2)-2, $this->_params['size'][1]-2);  
	}

}

class MarkerStar extends MarkerBuilder {

	function draw() {
		$coordinates = array();
		$angel = 360 / $spikes ;
		$outer_shape = array();
		for($i=0; $i<$spikes; $i++){
			$outer_shape[$i]['x'] = $x + ($radius * cos(deg2rad(270 - $angel*$i)));
			$outer_shape[$i]['y'] = $y + ($radius * sin(deg2rad(270 - $angel*$i)));
		}
		$inner_shape = array();
		for($i=0; $i<$spikes; $i++){
			$inner_shape[$i]['x'] = $x + (0.5*$radius * cos(deg2rad(270-180 - $angel*$i)));
			$inner_shape[$i]['y'] = $y + (0.5*$radius * sin(deg2rad(270-180 - $angel*$i)));
		}
		foreach($inner_shape as $key => $value){
			if($key == (floor($spikes/2)+1))
				break;
			$inner_shape[] = $value;
			unset($inner_shape[$key]);
		}
		$i=0;
		foreach($inner_shape as $value){
			$inner_shape[$i] = $value;
			$i++;
		}
		foreach($outer_shape as $key => $value){
			$coordinates[] = $outer_shape[$key]['x'];
			$coordinates[] = $outer_shape[$key]['y'];
			$coordinates[] = $inner_shape[$key]['x'];
			$coordinates[] = $inner_shape[$key]['y'];
		}
		$this->_drawObj->polygon($coordinates);
	}

}
