<?php  //  -*- mode:php; tab-width:2; c-basic-offset:2; -*-

namespace mod\contactform;

class Ajax {

	public static function update($params) {
		if (empty($params['key']) || empty($params['privateKey']) || empty($params['mailSubject'])) return false;
		$config = json_decode(\mod\config\Main::get('contactform', 'config', '{}'), true);
		$config['key'] = $params['key'];
		$config['privateKey'] = $params['privateKey'];
		$config['mailSubject'] = $params['mailSubject'];
		$config['sender'] = $params['sender'];
		\mod\config\Main::set('contactform', 'config', json_encode($config));
		return true;
	}

	public static function addCategory($params) {
		if (empty($params['category'])) return false;
		$config = json_decode(\mod\config\Main::get('contactform', 'config', '{}'), true);
		if (!isset($config['categories']))
			$config['categories'] = array();
		if (isset($config['categories'][$category])) 
			return false;
		$config['categories'][$params['category']] = array();
		\mod\config\Main::set('contactform', 'config', json_encode($config));
		return true;
	}

	public static function deleteCategory($params) {
		if (empty($params['category'])) return false;
		$config = json_decode(\mod\config\Main::get('contactform', 'config', '{}'), true);
		if (!isset($config['categories'][$params['category']]))
			return false;
		unset($config['categories'][$params['category']]);
		\mod\config\Main::set('contactform', 'config', json_encode($config));
		return true;
	}

	public static function addMail($params) {
		if (empty($params['category'])) return false;
		if (empty($params['mail'])) return false;
		$config = json_decode(\mod\config\Main::get('contactform', 'config', '{}'), true);
		if (!isset($config['categories'][$params['category']]))
			return false;
		if (in_array($params['mail'], $config['categories'][$params['category']])) 
			return false;
		$config['categories'][$params['category']][] = $params['mail'];
		\mod\config\Main::set('contactform', 'config', json_encode($config));
		return true;
	}

	public static function deleteMail($params) {
		if (empty($params['category'])) return false;
		if (empty($params['mail'])) return false;
		$config = json_decode(\mod\config\Main::get('contactform', 'config', '{}'), true);
		if (!isset($config['categories'][$params['category']]))
			return false;
		if (!in_array($params['mail'], $config['categories'][$params['category']])) 
			return false;
		$num = array_search($params['mail'], $config['categories'][$params['category']]);
		unset($config['categories'][$params['category']][$num]);
		\mod\config\Main::set('contactform', 'config', json_encode($config));
		return true;
	}

}
