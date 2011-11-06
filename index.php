<?php

list($usec, $sec) = explode(' ', microtime());
$script_start = (float) $sec + (float) $usec;
   
session_start();

require_once(dirname(__FILE__).'/core/Core.php');
\core\Core::init();

list($usec, $sec) = explode(' ', microtime());
$script_end = (float) $sec + (float) $usec;
$elapsed_time = round($script_end - $script_start, 5);

echo "RAM: ".\core\Tools::getMemoryUsage()." ; elapsed: ".($elapsed_time * 1000.0)."ms ; class count: ".$class_autoload_count;
