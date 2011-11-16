<?php  //  -*- mode:php; tab-width:2; c-basic-offset:2; -*-

namespace mod\site_test;

class Main {

	public static function hook_mod_site_test($hookname, $userdata, $urlmatches) {
		$page = new \mod\webpage\Main();
    $page->setLayout('mod/site_test/templates/welcome.tpl');
		$page->display();
	}

	public static function hook_mod_site_test_tests($hookname, $userdata, $urlmatches) {
		$page = new \mod\webpage\Main();
		$json = false;
		switch($urlmatches[1]) {
		case 'field': self::test_field($page); break;
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

	private static function test_field($page) {
		$form = new \mod\field\FieldForm('site_test_myform', 'mod/site_test/templates/myform.tpl');

		$page->setLayout('mod/site_test/templates/field.tpl');
		if ($form->isPosted() && $form->isValid()) {
			$form->sqlinsert('ch_sitetest_person');
			$page->smarty->assign('site_test_firstname', $form->getValue('firstname'));
			$page->smarty->assign('site_test_lastname', $form->getValue('lastname'));
		} else {
			$page->setLayout('mod/site_test/templates/field.tpl');
			$page->smarty->assign('site_test_myform', $form->get_html($page));
		}
	}

	public static function hook_mod_smarty_blockstart_mod_site_test_content($hookname, $userdata) {
		//echo "[start]";
	}

	public static function hook_mod_smarty_blockend_mod_site_test_content($hookname, $userdata) {
		//echo "[end]";
	}

	public static function test() {
		return "zob";
	}
}
