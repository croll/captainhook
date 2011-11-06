<?php

namespace mod\user;

require_once('Main.php');

function ajax_auth($json) {
  $resp = (object)null;
  $resp->message = Main::login($json->login, $json->passwd);
  return $resp;
}
