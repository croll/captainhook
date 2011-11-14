<?php  //  -*- mode:php; tab-width:2; c-basic-offset:2; -*-

namespace mod\site_test;

class Main {

	public static function hook_mod_site_test($hookname, $userdata, $urlmatches) {
		$page = new \mod\webpage\Main();
    $page->setLayout('mod/site_test/templates/welcome.tpl');
		$page->display();
	}

	public static function hook_mod_site_test_tests($hookname, $userdata, $urlmatches) {
		$form = new \mod\field\FieldForm('site_test_myform', 'mod/site_test/templates/myform.tpl');
		$page = new \mod\webpage\Main();
		$page->setLayout('mod/site_test/templates/field.tpl');
		if ($form->isPosted() && $form->isValid()) {
			$page->smarty->assign('site_test_firstname', $form->get_value('firstname'));
			$page->smarty->assign('site_test_lastname', $form->get_value('lastname'));
			$page->display();
		} else {
			$page = new \mod\webpage\Main();
			$page->setLayout('mod/site_test/templates/field.tpl');
			$page->smarty->assign('site_test_myform', $form->get_html());
			$page->display();
		}
	}

	public static function hook_mod_smarty_blockstart_mod_site_test_content($hookname, $userdata) {
		//echo "[start]";
	}

	public static function hook_mod_smarty_blockend_mod_site_test_content($hookname, $userdata) {
		//echo "[end]";
	}

}
