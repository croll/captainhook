<?php  //  -*- mode:php; tab-width:2; c-basic-offset:2; -*-

namespace mod\cssjs;

class Main {

	public static function addJs($webpageInstance, $file) {
		$webpageInstance->scripts[] = $file;
	}

	public static function addCss($webpageInstance, $file) {
		$webpageInstance->csss[] = $file;
	}

}
