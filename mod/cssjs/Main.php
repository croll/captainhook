<?php  //  -*- mode:php; tab-width:2; c-basic-offset:2; -*-

namespace mod\cssjs;

class Main {

	public static function addJs($webpageInstance, $file) {
    if (!isset($webpageInstance->scripts)) $webpageInstance->scripts=array();
		if (!in_array($file, $webpageInstance->scripts))
      $webpageInstance->scripts[] = $file;
	}

	public static function addCss($webpageInstance, $file) {
    if (!isset($webpageInstance->scripts)) $webpageInstance->csss=array();
		if (!in_array($file, $webpageInstance->csss))
      $webpageInstance->csss[] = $file;
	}

}
