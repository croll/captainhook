<?php  //  -*- mode:php; tab-width:2; c-basic-offset:2; -*-

namespace mod\page;

class Main {

  public static function hook_mod_page_render($hookname, $userdata, $matches, $flags) {
		// check perm
		if (!\mod\user\Main::userHasRight('View page')) {
			return false;
		}
		//get lang
		$lang=\mod\lang\Main::getCurrentLang();
		// get function params
		$sysname=$matches[1];
		// get page
		$view = \mod\page\Main::getPageBySysName($sysname);
		$page = new \mod\webpage\Main();
		// assign data to the smarty template
		$page->smarty->assign('lang', $lang);
		$page->smarty->assign('page', $view);
		$page->smarty->assign('page_name', $sysname);
		$page->smarty->assign('page_mode', 'view');
    // as this function to be available both for http and ajax request set both layout options
		//return $matches;
		if ($flags & \mod\regroute\Main::flag_xmlhttprequest) {
			$page->smarty->fetch('page/page');
    } else {
      $page->setLayout('page/page');
      $page->display();
    }
  }
  public static function getTranslated($sysname, $lang) {
    // check perm
    if (!\mod\user\Main::userHasRight('View page')) {
			return false;
    }
    $db=\core\Core::$db;

    // check if page is a translation of a page or a reference page
    $ilr=$db->fetchAll('SELECT "pid", "sysname", "id_lang_reference", "lang"  FROM "ch_page" WHERE "sysname"=?', array($sysname));
    $ilr = $ilr[0];
    // if lang == lang then return sysname
    if ($ilr['lang'] == $lang) {
      return $sysname;
    }
    $dbParams=array();
    if ($ilr['id_lang_reference'] == 0) {
      // if idLangReference == 0 -> reference page
      $dbParams[]= $ilr['pid'];
      $dbParams[]= $lang;
      $trans= $db->fetchOne('SELECT "sysname" FROM "ch_page" WHERE "id_lang_reference"=? AND "lang"=?', $dbParams);
      if($trans) {
        return $trans;
      } else {
        return $sysname;
      }
    } else {
      // else -> is translation of a page
      // get reference page
      $myref= $db->fetchAll('SELECT "pid", "sysname", "id_lang_reference", "lang"  FROM "ch_page" WHERE "pid"=?', array($ilr['id_lang_reference']));
      if (!$myref) {
        return $sysname;
      }
      $myref=$myref[0];
      if ($myref['lang'] == $lang) {
        return $myref['sysname'];
      } else {
        $dbParams[]= $myref['pid'];
        $dbParams[]= $lang;
        $tt = $db->fetchOne('SELECT "sysname" FROM "ch_page" WHERE "id_lang_reference"=? AND "lang"=?', $dbParams);
        if (!$tt) {
          return $sysname;
        } else {
          return $tt;
        }
      }
    }
  }
  public static function hook_mod_page_create($hookname, $userdata, $matches, $flags) {
		\mod\user\Main::redirectIfNotLoggedIn();
		// check perm
		if (!\mod\user\Main::userHasRight('Manage page')) {
			return false;
		}
		$db=\core\Core::$db;
		// prepare data for storage

		$userId= \mod\user\Main::getUserId($_SESSION['login']);

		$dbParams=array();
		$dbParams[]=$matches['name'];
		$dbParams[]= self::cleanMyString($matches['name']);
		$dbParams[]=(int)$userId;
		$dbParams[]=(int)$matches['published'];
		$dbParams[]=$matches['lang'];
		$dbParams[]=(int)$matches['id_lang_reference'];
		$dbParams[]=stripslashes(html_entity_decode($matches['content']));
		$dbParams[]=date("Y-m-d H:i:s");
		$dbParams[]=date("Y-m-d H:i:s");
		return $db->exec_returning("INSERT INTO ch_page (
				name,
				sysname,
				authorid,
				published,
				lang,
				id_lang_reference,
				content,
				created,
				updated) VALUES
					(?,?,?,?,?,?,?,?,?)", $dbParams, 'pid');
  }
  public static function hook_mod_page_update($hookname, $userdata, $matches, $flags) {
    \mod\user\Main::redirectIfNotLoggedIn();
		// check perm
		if (!\mod\user\Main::userHasRight('Manage page')) {
			return false;
		}
		$db=\core\Core::$db;
		// prepare data for storage
		$dbParams=array();
		$dbParams[]=$matches['name'];
		$dbParams[]=self::cleanMyString($matches['name']);;
		$dbParams[]=(int)$matches['published'];
		$dbParams[]=$matches['lang'];
		$dbParams[]=(int)$matches['id_lang_reference'];

		$dbParams[]=stripslashes(html_entity_decode($matches['content']));
		$dbParams[]=date("Y-m-d H:i:s");
		$dbParams[]=(int)$matches['pid'];

		$query= $db->query("UPDATE ch_page
				    SET name=?,
					sysname=?,
					published=?,
					lang=?,
					id_lang_reference=?,
					content=?,
					updated=?
				    WHERE pid=?", $dbParams);
		return $matches['pid'];
	}
  public static function hook_mod_page_edit($hookname, $userdata, $matches, $flags) {
    \mod\user\Main::redirectIfNotLoggedIn();
		// check perm
		if (!\mod\user\Main::userHasRight('Manage page')) {
			return false;
		}
		$pid=$matches[1];
		$view = self::getPageById($pid);
    $page = new \mod\webpage\Main();
		//get lang
		$lang=\mod\lang\Main::getCurrentLang();
		$ll= array();
		if (isset($pid)) {
			$ll['lang']=$view['lang'];
		} else {
			$ll['lang']=$lang;
		}
		$idReferences = self::idLangReference($ll);
		$page->smarty->assign('lang', $lang);
		$page->smarty->assign('idRefs', $idReferences);
		$page->smarty->assign('page', $view);
    $page->smarty->assign('page_mode', 'edit');
    if ($flags & \mod\regroute\Main::flag_xmlhttprequest) {
      $page->smarty->fetch('page/edit');
    } else {
      $page->setLayout('page/edit');
      $page->display();
    }
  }
  public static function idLangReference($params) {
    // check perm
    if (!\mod\user\Main::userHasRight('Manage page')) {
			return false;
    }
    $db=\core\Core::$db;
    $dbParams= array();
    $dbParams[]=$params['lang'];
    return $db->fetchAll("SELECT pid, sysname, name, lang FROM ch_page WHERE lang != ?", $dbParams);
  }

  public static function hook_mod_page_list($hookname, $userdata, $matches, $flags) {
		\mod\user\Main::redirectIfNotLoggedIn();

		// check perm
		if (!\mod\user\Main::userHasRight('Manage page')) {
			return false;
		}
		// check for optionals parameters
		if (isset($matches[1])) {
			$check=split('/', $matches[1]);
			$params=array();
			for ($i=0; $i <= count($check); $i++) {
				if ($check[$i] != "") {
					$iNext= $i+1;
					$params[$check[$i]]= $check[$iNext];
					$i++;
				}
			}
			if ($params["sort"]) {
				$sort = $params["sort"];
			}
			if ($params["maxrow"] && (int)$params["maxrow"]) {
				$maxrow=$params["maxrow"];
			}
			if ($params["offset"] && (int)$params["offset"]) {
				$offset=$params["offset"];
			}
			if ($params["filter"]) {
				$filter=$params["filter"];
			}
		}
		// set default list parameter
		if (!isset($sort)) $sort="updated_desc";
		if (!isset($maxrow)) $maxrow= 10;
		if (!isset($offset)) $offset= 0;
    $page = new \mod\webpage\Main();
		$db=\core\Core::$db;
		$dbParams=array();

		$mid = "";
		if (isset($filter)) {
			$filters = split('@', $filter);
			for($i=0; $i<count($filters); $i++) {
				//var_dump($filters);
				$fd=split(':', $filters[$i]);
				if ($fd[0] == 'login') {
				 	$mid .=" AND u.login = ?";
					$dbParams[]=$fd[1];
				} else if ($fd[0] == 'name') {
				 	$mid .=" AND p.name = ?";
					$dbParams[]=$fd[1];

				} else if ($fd[0] == 'published') {
				 	$mid .=" AND p.published = ?";
					$dbParams[]=(int)$fd[1];
				} else if ($fd[0] == 'lang') {
				 	$mid .=" AND p.lang = ?";
					if ($fd[1]==0) {
						$dbParams[]="fr_FR";
					} else {
						$dbParams[]="de_DE";
					}
				} else if ($fd[0] == 'id_lang_reference') {
					if ($fd[1]==0) {
				 		$mid .=" AND p.id_lang_reference = ?";
					} else {
				 		$mid .=" AND p.id_lang_reference != ?";
					}
					$dbParams[]=0;
				}
			}
		}
		$dbParams[]=(int)$maxrow;
		$dbParams[]=(int)$offset;

		// mysql version
		//$q="SELECT p.`pid`, p.`sysname`, p.`name`, p.`authorId`, u.`login`, u.`full_name`, p.`published`, p.`created`, p.`updated` FROM `ch_page` p, `ch_user` u WHERE p.`authorId` = u.`uid` ORDER BY ? LIMIT ?,?".$mid;
		$q="SELECT p.pid, p.sysname, p.name, p.authorid, u.login, u.full_name, p.published, p.lang, p.id_lang_reference, p.created, p.updated FROM ch_page p, ch_user u WHERE p.authorid = u.uid";
		$q .= $mid;
		$q .= self::order_by($sort);
		$q .=" LIMIT ? OFFSET ?";
		$list = $db->fetchAll($q, $dbParams);

		// quant mysql version
		//$q2="SELECT count(p.`pid`) as `quant` FROM `ch_page` p, `ch_user` u WHERE p.`authorId` = u.`uid`";

		$q2="SELECT count(p.pid) as quant FROM ch_page p, ch_user u WHERE p.authorid = u.uid";
		$quant= $db->fetchOne($q2, NULL);
		// get lang
		$lang=\mod\lang\Main::getCurrentLang();
		$page->smarty->assign('lang', $lang);

		$page->smarty->assign('list', $list);
		if (isset($filter)) {
			$page->smarty->assign('filter', $filter);
		}
		$page->smarty->assign('sort', $sort);
		$page->smarty->assign('offset', $offset);
		$page->smarty->assign('maxrow', $maxrow);
		$page->smarty->assign('quant', $quant);
    $page->smarty->assign('page_mode', 'list');
    if ($flags & \mod\regroute\Main::flag_xmlhttprequest) {
      $page->smarty->fetch('page/list');
    } else {
      $page->setLayout('page/list');
      $page->display();
    }
  }
  private function dbSort($sort) {
		$s=explode('_',$sort);
		$s[1]=strtoupper($s[1]);
		return $s[0]." ".$s[1];
  }
  private function order_by($sort) {
		$sorted = self::dbSort($sort);
		$q =" ORDER BY ".$sorted;
		return $q;
  }
  public static function getPageBySysName($name) {
		if (!\mod\user\Main::userHasRight('View page')) {
			return false;
		}
		$db=\core\Core::$db;
		//mysql
		//$result = $db->query('SELECT p.`pid`, p.`sysname`, p.`name`, p.`authorId`, u.`login`, u.`full_name`, p.`published`, p.`created`, p.`updated`, p.`content` FROM `ch_page` p, ch_user u WHERE `sysname`=?', array($name));
		//postgres
		$result = $db->query('SELECT p.pid, p.sysname, p.name, p.authorid, u.login, u.full_name, p.published, p.lang, p.id_lang_reference, p.created, p.updated, p.content FROM ch_page p, ch_user u WHERE sysname=?', array($name));
		return $result->fetchRow();
  }
  public static function getPageById($id) {
		$db=\core\Core::$db;
		//mysql
		//$result = $db->query('SELECT p.`pid`, p.`sysname`, p.`name`, p.`authorId`, u.`login`, u.`full_name`, p.`published`, p.`created`, p.`updated`, p.`content` FROM `ch_page` p, ch_user u WHERE `pid`=?', array((int)$id));
		//postgresql

		$result = $db->query('SELECT p.pid, p.sysname, p.name, p.authorid, u.login, u.full_name, p.published, p.lang, p.id_lang_reference, p.created, p.updated, p.content FROM ch_page p LEFT JOIN ch_user u ON u.uid = p.authorid WHERE pid=?', array($id));
    return $result->fetchRow();
  }
  /**
  * Clean my string.
  *
  * @param string String to be cleaned
  * @return string Cleaned string
  *
  * It a simple function intended to be used to clean a string to make it compliant with the use of system_name compliant with a clean web url encoded path.
  *
  */

  public static function cleanMyString($msg, $toUrl=false) {
  	if (empty($msg)) return false;
                $msg = self::removeAccents($msg);
                $msg = str_replace("'", '_', $msg);
                $msg = str_replace('%20', ' ', $msg);
                $msg = preg_replace('~[^\\pL0-9-]+~u', '_', $msg);
                $msg = trim($msg, "_");
                $msg = strtolower($msg);
                $msg = preg_replace('~[^_a-z0-9-]+~', '', $msg);
                if ($toUrl) {
                        $msg = iconv("utf-8", "us-ascii//TRANSLIT", $msg);
                        $msg = str_replace('_', '-', $msg);
                }
                return $msg;
        }
  public static function removeAccents($msg) {
                if (empty($msg)) return false;
                $search = explode(",","ç,æ,œ,á,é,í,ó,ú,à,è,ì,ò,ù,ä,ë,ï,ö,ü,ÿ,â,ê,î,ô,û,å,e,i,ø,u");
                $replace = explode(",","c,ae,oe,a,e,i,o,u,a,e,i,o,u,a,e,i,o,u,y,a,e,i,o,u,a,e,i,o,u");
                return str_replace($search, $replace, $msg);
  }

}
