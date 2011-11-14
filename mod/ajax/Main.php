<?php

namespace mod\ajax;

class Main {

  public static function hook_mod_ajax($hookname, $userdata) {
		$request = preg_replace("/^json=/","",file_get_contents('php://input'));
		print_r($request);
  }

}
