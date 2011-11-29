<?php

namespace mod\cssjs;

class Ajax {

  public static function getScriptFiles($params) {
		$js = dirname(__FILE__)."/../$params[mod]/js/$params[class].js";
		$css = dirname(__FILE__)."/../$params[mod]/css/$params[class].css";
		$outp = array();
		if (is_file($js)) {
			$outp['js'] = file_get_contents($js);
		} else {
			return -1;
		}
		if (is_file($css))
			$outp['css'] = file_get_contents($css);
		return $outp;
  }

}
