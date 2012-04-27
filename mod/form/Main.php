<?php  //  -*- mode:php; tab-width:2; c-basic-offset:2; -*-

namespace mod\form;

class Main {

  public static function smartyBlock_form($params, $content, &$smarty, &$repeat) {
		foreach(array('mod', 'file') as $p) {
			if (empty($params[$p]))
				throw new \Exception("param $p is  not provided");
		}
		if ($repeat) {
			$form = new Form($params, $smarty);
			$form->assign();
		} else {
			$form = new Form($params);
			$formId = 'ch_form_'.$form->getId();
			return $smarty->tpl_vars->$formId->value->render($content);
		}

  }

}

