<?php  //  -*- mode:php; tab-width:2; c-basic-offset:2; -*-

namespace mod\langeditor;

class Main {

  public static function hook_mod_langeditor_index($hookname, $userdata) {
		$page = new \mod\webpage\Main();

    $langs=self::loadLangsFiles();

    if (isset($_REQUEST['trad-submit'])) {
      $lang=$_REQUEST['lang'];
      $modname=$_REQUEST['modname'];
      //\core\Core::log($_REQUEST);

      $langarray=array();
      foreach($_REQUEST as $k => $v) {
				foreach($langs[$modname][$lang] as $k2 => $v2) {
					if (md5($k2) == $k) {
						$langarray[$k2]=$v;
						break;
					}
        }
      }

      $file=CH_MODDIR.'/'.$_REQUEST['modname'].'/lang/'.$_REQUEST['lang'].'.js';
      file_put_contents($file, self::myjson_encode($langarray));
    }

    $langs=self::loadLangsFiles();

    $page->smarty->assign('langs', $langs);    
		$page->setLayout('langeditor/index');
		$page->display();
	}

  private static function loadLangsFiles() {
    $langs = array();

    foreach(scandir(CH_MODDIR) as $modname) {
      if (substr($modname, 0, 1) == '.') continue;
      if (!is_dir(CH_MODDIR.'/'.$modname)) continue;
      
      $langdir=CH_MODDIR.'/'.$modname.'/lang';
      if (!file_exists($langdir) || !is_dir($langdir)) continue;

      $langs[$modname]=array();

      foreach(scandir($langdir) as $langname) {
        $langfile = $langdir.'/'.$langname;
        if (substr($langname, 0, 1) == '.') continue;
        if (substr($langname, 0, -3) == '.js') continue;
        if (!is_file($langfile)) continue;
        
        $langname=substr($langname, 0, -3); // remove '.js'

        $langs[$modname][$langname]=json_decode(file_get_contents($langfile), true);
      }
    }

    return $langs;
  }

  private static function myjson_encode($blup, $offset='') {
    if (is_array($blup)) {
      $res='';
      $offset2=$offset.'  ';
      foreach($blup as $k => $v) {
        $res.=($res == '' ? "{\n" : ",\n");
        $res.=$offset2.json_encode($k).' : '.self::myjson_encode($v, $offset2);
      }
      $res.="\n".$offset."}";
      return $res;
    } else return json_encode($blup);
  }
  
}
