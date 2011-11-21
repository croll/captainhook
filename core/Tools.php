<?php  //  -*- mode:php; tab-width:2; c-basic-offset:2; -*-

namespace core;

class Tools {

	public static function getMemoryUsage() {
		$size = memory_get_usage(true);
		$unit=array('B','kB','MB','GB','TB','PB');
		return @round($size/pow(1024,($i=floor(log($size,1024)))),2).' '.$unit[$i];
	}

	public static function cleanString($str) {
		$str = mb_convert_encoding($str, 'UTF-8', 'UTF-8');
		return htmlentities($str, ENT_QUOTES, 'UTF-8');
	}

}
