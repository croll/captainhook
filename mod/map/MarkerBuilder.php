<?php

namespace mod\map;

class MarkerBuilder {

	function __construct() {
		$this->marker = (object)NULL;
	}

	function drawStar($x, $y, $radius, $spikes=5) {
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
		return $coordinates ;
	}
}
