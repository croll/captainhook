<?php  //  -*- mode:php; tab-width:2; c-basic-offset:2; -*-

namespace mod\user;

require_once('Main.php');

function ajax_auth($json) {
  $resp = (object)null;
  $resp->message = Main::login($json->login, $json->passwd);
  return $resp;
}
