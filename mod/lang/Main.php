<?php  //  -*- mode:php; tab-width:2; c-basic-offset:2; -*-

namespace mod\lang;

class Main {

  private static function init() {
		global $ch_lang;
		global $ch_langs;

    if (isset($GLOBALS['ch_inited'])) return;
    $GLOBALS['ch_inited']=1;

    if (isset($_COOKIE['ch_lang']) && in_array($_COOKIE['ch_lang'], self::getActiveLangs()))
      $ch_lang=$_COOKIE['ch_lang'];
    else
      $ch_lang='fr_FR';
		$ch_langs=array();

		$ch_langs[$ch_lang]=json_decode(file_get_contents(CH_MODDIR.'/lang/cache/'.$GLOBALS['ch_lang'].'.json'), true);
  }

  public static function hook_core_init_http($hookname, $userdata) {
    self::init();
  }

  public static function hook_mod_webpage_construct($hookname, $userdata, $webpage) {
		global $ch_lang;
		global $ch_langs;

    self::init();

		\mod\cssjs\Main::addJs($webpage, '/mod/cssjs/js/mootools.js');
		\mod\cssjs\Main::addJs($webpage, '/mod/cssjs/js/sprintf-0.7-beta1.js');
		\mod\cssjs\Main::addJs($webpage, '/mod/lang/js/lang.js');
		\mod\cssjs\Main::addJsCode($webpage, "ch_lang='$ch_lang';");
    $langfile=CH_MODDIR.'/lang/cache/'.$GLOBALS['ch_lang'].'.js';
    if (is_file($langfile)) \mod\cssjs\Main::addJs($webpage, '/mod/lang/cache/'.$GLOBALS['ch_lang'].'.js');
	}

  public static function hook_mod_lang_set_lang($hookname, $userdata, $params) {
    self::setCurrentLang($params[1]);
    if ($params[2][0] != '/')
      throw new \Exception('redirect not authorized');
    header("Location: ".$params[2]);
  }

	/*
	public static function hook_smarty_new($hookname, $userdata, $sm) {
		global $ch_lang;
		// we add $lang to smarty, so it compile different cache version with every langs
		$sm->compile_id.='_'.$ch_lang;
	}
	*/

	public static function smartyFunction_t($params, $template) {
    self::init();
		$paf=array();
		foreach($params as $k => $v)
			if (!preg_match('/^p[0-9]+$/', $k))
				$paf[$k]=$v;

		for ($i=0; isset($params['p'.$i]); $i++) $paf['p'][]=$params['p'.$i];
		return self::t($paf);
	}

	private static function t($paf) {
		global $ch_langs;
		global $ch_lang;

    self::init();

		$m=$paf['m'];

    if (isset($ch_langs[$ch_lang]) && isset($ch_langs[$ch_lang][$paf['d']]) && isset($ch_langs[$ch_lang][$paf['d']][$paf['m']]) && $ch_langs[$ch_lang][$paf['d']][$paf['m']] != false)
			$m=$ch_langs[$ch_lang][$paf['d']][$paf['m']];

		$tag=isset($paf['tag']) ? $paf['tag'] : '';
		$p=isset($paf['p']) ? $paf['p'] : array();
		unset($paf['tag']);
		unset($paf['p']);

		if ($tag=='')
			return vsprintf($m, $p);
		else
			return "<$tag>".vsprintf($m, $p)."</$tag>";
    //return "<$tag class='ch_lang_trad' paf=\"".urlencode(json_encode($paf)).'">'.vsprintf($m, $p)."</$tag>";
	}

  public static function ch_t($d, $m) {
    self::init();
    $paf=array('d' => $d, 'm' => $m);
    for ($i=2; $i<func_num_args(); $i++)
      $paf['$'.($i-2)]=func_get_arg($i);
    return self::t($paf);
  }

  public static function getCurrentLang() {
    self::init();
    return $GLOBALS['ch_lang'];
  }

  public static function setCurrentLang($lang) {
    self::init();
    if (!in_array($lang, self::getActiveLangs()))
      throw new \Exception("lang '$lang' is not available");
    $GLOBALS['ch_lang']=$lang;
    setcookie('ch_lang', $lang, time()+60*60*24*30*12*20, '/');
  }

  public static function getActiveLangs() {
    self::init();
    return array("fr_FR", "de_DE");
  }
}
