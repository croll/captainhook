<?php  //  -*- mode:php; tab-width:2; c-basic-offset:2; -*-

namespace mod\site_test;

class Main {

	private static $urlmatches;

	public static function hook_mod_site_test($hookname, $userdata, $urlmatches) {
		self::$urlmatches=$urlmatches;
		$page = new \mod\webpage\Main();
    $page->setLayout('mod/site_test/templates/welcome.tpl');
		$page->display();
	}

	public static function hook_mod_site_test_tests($hookname, $userdata, $urlmatches) {
		self::$urlmatches=$urlmatches;
		$page = new \mod\webpage\Main();
		$json = false;
		switch($urlmatches[1]) {
		case 'field': $page->setLayout('mod/site_test/templates/field.tpl'); break;
		case 'js': $page->setLayout('mod/site_test/templates/js.tpl'); break;
		case 'ajax': 
			$page->setLayout('mod/site_test/templates/ajax.tpl'); 
			$json = true;
			break;
		case 'ajax2': 
			$page->setLayout('mod/site_test/templates/ajax2.tpl'); 
			$json = true;
			break;
		}
		if (!$json) $page->display();
			else echo $page->toJSON();
	}

	public static function hook_mod_smarty_blockstart_mod_site_test_content($hookname, $userdata) {
		//echo "[start]";
	}

	public static function hook_mod_smarty_blockend_mod_site_test_content($hookname, $userdata) {
		//echo "[end]";
	}

}
