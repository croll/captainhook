<?php  //  -*- mode:php; tab-width:2; c-basic-offset:2; -*-

namespace mod\page;

class Ajax {

  public static function savePage($params) {
	//return $params;
	if(!$params['pid'] || $params['pid']==0) {
		return \mod\page\Main::hook_mod_page_create($hookname, $userdata, $params, $flags);
	} else {
		return \mod\page\Main::hook_mod_page_update($hookname, $userdata, $params, $flags);
	}
  }
  public static function deletePage($params) {
	\mod\user\Main::redirectIfNotLoggedIn();
	// check perm 
	if (!\mod\user\Main::userHasRight('Manage page')) {
			return false;
	}
	$pid=$params['pid'];
	$db=\core\Core::$db;
	$del=$db->query("DELETE FROM ch_page WHERE pid=?", array((int)$pid));
	return $pid;		

  }
  public static function authorList($params) {
	if (!\mod\user\Main::userHasRight('Manage page')) {
			return false;
	}
	$db=\core\Core::$db;
	$authors=$db->fetchAll("SELECT DISTINCT ON (uid) login , full_name FROM ch_page , ch_user WHERE authorid = uid;", NULL);
	return $authors;		
  }
}
