<?php  //  -*- mode:php; tab-width:2; c-basic-offset:2; -*-

namespace mod\lang;

class Ajax {

  public static function setCurrentLang($params) {
		
	\mod\lang\Main::setCurrentLang($params['lang']);
	return \mod\lang\Main::getCurrentLang($params['lang']);
  }
}
	
