<?php  //  -*- mode:php; tab-width:2; c-basic-offset:2; -*-

namespace mod\site_test;

class Main {

	public static function hook_mod_site_test_http() {
    \mod\webpage\Main::setLayout('mod/site_test/hooks_templates/layout.tpl');
	}

}
