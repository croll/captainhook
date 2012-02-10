<?php  //  -*- mode:php; tab-width:2; c-basic-offset:2; -*-

namespace mod\lang;

class Main {

  public static function hook_core_init_http($hookname, $userdata) {
		global $ch_lang;
		global $ch_langs;

		$ch_lang='fr_FR';
		$ch_langs=array();

		$ch_langs[$ch_lang]=json_decode(file_get_contents(CH_MODDIR.'/lang/cache/'.$GLOBALS['ch_lang'].'.js'), true);
		\core\Core::log($GLOBALS['ch_langs']);
  }

  public static function hook_mod_webpage_construct($hookname, $userdata, $webpage) {
		global $ch_lang;
		global $ch_langs;

		\mod\cssjs\Main::addJs($webpage, '/mod/cssjs/js/mootools.js');
		\mod\cssjs\Main::addJs($webpage, '/mod/cssjs/js/sprintf-0.7-beta1.js');
		\mod\cssjs\Main::addJs($webpage, '/mod/lang/js/lang.js');
	}

	/*
	public static function hook_smarty_new($hookname, $userdata, $sm) {
		global $ch_lang;
		// we add $lang to smarty, so it compile different cache version with every langs
		$sm->compile_id.='_'.$ch_lang;
	}
	*/

	public static function smartyFunction_t($params, $template) {
		$paf=array('d' => $params['d'], 'm' => $params['m'], 'p' => array());
		for ($i=0; isset($params['p'.$i]); $i++) $paf['p'][]=$params['p'.$i];
		return self::ch_t($paf);
	}

	private static function ch_t($paf) {
		global $ch_langs;
		global $ch_lang;

		$m=$paf['m'];

    if (isset($ch_langs[$ch_lang]) && isset($ch_langs[$ch_lang][$paf['d']]) && isset($ch_langs[$ch_lang][$paf['d']][$paf['m']]))
			$m=$ch_langs[$ch_lang][$paf['d']][$paf['m']];
		
		return '<span class="ch_lang_trad" paf="'.urlencode(json_encode($paf)).'">'.vsprintf($m, $paf['p']).'</span>';
	}

}
