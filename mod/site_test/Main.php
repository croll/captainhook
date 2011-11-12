<?php  //  -*- mode:php; tab-width:2; c-basic-offset:2; -*-

namespace mod\site_test;

class Main {

	public static function hook_mod_site_test($hookname, $userdata) {
		$page = new \mod\webpage\Main();
    $page->setLayout('mod/site_test/hooks_templates/layout.tpl');
		$page->display();
	}

}
