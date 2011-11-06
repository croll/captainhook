<?php

namespace mod\page;

class Main {

	private static $_template = 'index';
	private static $_layout = 'layout';

	public static function hook_display($args) {
		if (is_string($args['tpl']) && !preg_match("/^[a-zA-Z0-9-_]+$/", $args['tpl'])) return false;
		$page = (!is_null($args['tpl'])) ? $args['tpl'] : (!empty(self::$_template) ? self::$_template : 'index');
		$ini = parse_ini_file(dirname(__FILE__).'/module.conf',true);
		if (is_array($ini[$page]) && isset($ini[$page]['tpl'])) { 
			self::$_template = str_replace('.tpl', '', $ini[$page]['tpl']);
		} else {
			// chopper une page dans la table page
		}
		$sm = \core\Core::$smarty;
		$sm->assign("page", self::$_template);
		if (!is_null(self::$_layout)) {
			$sm->assign("content", self::$_template.'.tpl');
			$sm->display(self::$_layout.'.tpl');
		} else {
			$sm->display(self::$_template.'.tpl');
		}
	}

	public static function setTemplate($name=null) {
		if (is_null($name) || !preg_match("/^[a-zA-Z0-9-8]+$/", $name)) return false;
		self::$_template = $name.'.tpl';
	}

	public static function setLayout($name) {
		if (!is_null($name) && !preg_match("/^[a-zA-Z0-9-8]+$/", $name)) return false;
		if (is_null($name))
			self::$_layout = null;
		else
			self::$_layout = $name.'.tpl';
	}

	public static function pagination($numResults, $current, $nb) {
		if ($numResults < 1) return null;
		$numPages = ceil($numResults/$nb);
		$currentPageNum = ($current/$nb);
		$pagination = array();
		if ($numPages > 1 && $current > 0) {
			$pagination['prev'] = ($current-$nb);
		} else {
			$pagination['prev'] = null;
		}
		if ($numPages > 1 && ($current+$nb)<$numResults) {
			$pagination['next'] = ($current+$nb);
		} else {
			$pagination['next'] = null;
		}
		$pagination['pages'] = array();
		$num = 0;
		for($i=0;$i<($numPages);$i++) {
			$pagination['pages'][$i]['num'] = $num;
			$num += $nb;
			$pagination['pages'][$i]['label'] = ($num/$nb);
			$pagination['pages'][$i]['selected'] = ($i == $currentPageNum) ? 'true' : false;
		}
		return $pagination;
	}

}
