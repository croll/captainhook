<?php

namespace mod\ajax;

class Main {

  public static function hook_mod_ajax($hookname, $userdata, $urlparams) {
		$args = preg_split('#/#', $urlparams[1]);
		$methodParams = array();
		switch($args[0]) {
			case 'call':
				if (!isset($args[1]) || !is_string($args[1]) || !isset($args[2]) || !is_string($args[2])) {
					echo -1;
					return false;
				}
				$method = (strstr($args[2], '?')) ? substr($args[2], 0, strpos($args[2], '?', 0)) : $args[2];
				while(list($k, $v) = each($_REQUEST)) {
					$methodParams[$k] = filter_var($v, FILTER_SANITIZE_STRING);
				}
				header('Cache-Control: no-cache, must-revalidate');
				header('Expires: Mon, 10 Jan 1970 05:00:00 GMT');
				header('Content-type: application/json');
				echo json_encode(self::call($args[1], $method, $methodParams));
				break;
			default:
				echo null;
		}
  }

	public static function call($mod, $method, $params=null) {
		if (!is_array($params)) $params = array($params);
		try {
			return call_user_func_array('\\mod\\'.$mod.'\\Ajax::'.$method, array($params));
		} catch (\Exception $e) {
      throw $e;
		}
	}

}
