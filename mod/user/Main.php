<?php  //  -*- mode:php; tab-width:2; c-basic-offset:2; -*-

namespace mod\user;
use \core\Core;

class Main {

	private static $_cache;

	function __construct() {
	}

	public static function checkAuth($login='',$password='') {
		if ((!empty($login)) && (!empty($password))) {
			if ( (preg_match("/^[a-zA-Z0-9-_]+$/",$login)) && (preg_match("/^[a-zA-Z0-9-_!]+$/",$password)) ) {
				$res = Core::$db->fetchAll('SELECT "full_name", "login" FROM "ch_user" WHERE UPPER("login")=UPPER(?) AND "pass"=md5(?) AND "status"=1', 
																		array($login, $password));
				if (count($res)) {
					$row = $res[0];
					$_SESSION['full_name'] = $row['full_name'];
					$_SESSION['login'] = $row['login'];
					$_SESSION['hash'] = self::genTempHash($login,$password);
					\core\Hook::call('mod_user_login-ok');
					return true;
				} else {
					\core\Hook::call('mod_user_login-failed');
				}
			}
		}
		unset($_SESSION);
		session_destroy();
		return false;
	}

	public static function logout() {
    \core\Hook::call('mod/user/logout');
		unset($_SESSION);
		session_destroy();
	}

	public static function redirectIfNotLoggedIn() {
		if (!self::userIsLoggedIn())
			header('Location: http://'.$_SERVER['HTTP_HOST'].'/login');
	}

	protected static function genTempHash($login,$password) {
		return md5($login.date('Ymdhis')+rand(0,100000).$password);
	}

	public static function addUser($name, $login, $password, $status=1) {
		return Core::$db->exec_returning('INSERT INTO "ch_user" ("full_name", "login", "pass", "status") VALUES (?,?,MD5(?),?)', 
												array($name, $login, $password, $status), 'uid');
	}

	public static function getUserInfos($id) {
		$result = Core::$db->query('SELECT * FROM "ch_user" WHERE "uid"=?',
																	array((int)$id));
		return $result->fetchRow();
	}

	public static function getUserId($name) {
		 $id = Core::$db->fetchOne('SELECT "uid" FROM "ch_user" WHERE LOWER("login")=LOWER(?)',
																	array($name));
		 return ($id) ? (int)$id : NULL;
	}

	public static function delUser($user) {
		if (is_int($user))
			Core::$db->query('DELETE FROM "ch_user" WHERE "uid" = ?', 
												array($user));
		else
			Core::$db->query('DELETE FROM "ch_user" WHERE "login" = ?', 
												array($user));
		return (isset(Core::$db->Affected_Rows)) ? true : false;
	}

	public static function listUsersList() {
		$result = Core::$db->Execute('SELECT * FROM "ch_user"');
		$u = array();
		while($row = $result->fetchRow()) {
			$u[] = $row;
		}
		return $u;
	}

	public static function userIsLoggedIn($hash=NULL) {
		if (empty($_SESSION['hash']) || empty($_SESSION['login'])) return false;
		if ($hash !== NULL && !$_SESSION['hash'] != $hash) return false;
		return true;
	}

	public static function addGroup($name, $status=1) {
		return Core::$db->exec_returning('INSERT INTO "ch_group" ("name", "status") VALUES (?, ?)', 
												array($name, (int)$status), 'gid');
	}
	public static function renameGroup($old, $new) {
		Core::$db->query('UPDATE "ch_group" SET name=? WHERE name=?', array($new, $old));
		return true;
	}

	public static function getGroup($id) {
		$result = Core::$db->query('SELECT * FROM "ch_group" WHERE "gid"=?',array((int)$id));
		return $result->fetchRow();
	}

	public static function getGroupId($name) {
		$id = \core\core::$db->fetchOne('SELECT "gid" FROM "ch_group" WHERE LOWER("name")=LOWER(?)',array($name));
		return ($id) ? (int)$id : NULL;
	}

	public static function delGroup($group) {
		self::delAllGroupAssignation($group);
		if (is_int($group))
			Core::$db->query('DELETE FROM "ch_group" WHERE "gid" = ?', 
												array($group));
		else
			Core::$db->query('DELETE FROM "ch_group" WHERE "name" = ?', 
												array($group));
		return (isset(Core::$db->Affected_Rows)) ? true : false;
	}

	public static function delAllGroupAssignation($group) {
		// Get gid
		if (is_int($group)) $gid = $group;
		else {
			try {
				$gid = self::getGroupId($group);
			} catch (\Exception $e) { 
				throw new \Exception("Unable to assign user to group. Group $group not found");
				return false;
			}
		}
		Core::$db->query('DELETE FROM "ch_user_group" WHERE "gid" = ?', 
											array($gid));
		return (isset(Core::$db->Affected_Rows)) ? true : false;
	}

	public static function getUserGroups($user, $key=NULL) {
		if ((int)($user))
			$dbParam[]=$user;
		else {
			$dbParam[]=self::getUserId($user);
		}
		$db=\core\Core::$db;
		$q="SELECT gr.gid, gr.name FROM ch_group gr LEFT JOIN ch_user_group ug ON gr.gid = ug.gid LEFT JOIN ch_user us ON ug.uid=us.uid WHERE us.uid=?";
		$result = $db->query($q, $dbParam);
		$g= array();
		while($row = $result->fetchRow()) {
			switch($key) {
				case 'id':
					$g[] = $row['gid'];
				break;
				case 'name':
					$g[] = $row['name'];
				break;
				default:
					$g[] = array('id' => $row['gid'], 'name' => $row['name']);
			}
		}
		return $g;
	}

	public static function getGroupsList($status=1) {
		$result = Core::$db->query('SELECT * FROM "ch_group" WHERE status=?',
															array($status));
		$g = array();
		while($row = $result->fetchRow()) {
			$g[] = $row;
		}
		return $g;
	}

	public static function userBelongsToGroup($group, $user=NULL) {
		if (is_null($user)) {
			if (!empty($_SESSION['login'])) $user = $_SESSION['login'];
			else return false;
		}
		$uid = (is_string($user)) ? self::getUserId($user) : $user;
		return (Core::$db->fetchOne('SELECT gr."gid" FROM "ch_group" gr LEFT JOIN "ch_user_group" ug ON gr."gid" = ug."gid" LEFT JOIN "ch_user" us ON ug."uid"=us."uid" WHERE us."uid"=? AND gr."name"=?',
																	array((int)$uid, $group))) ? true : false;

	}

	public static function assignUserToGroup($login, $group) {
		// Get uid
		if ((int)($login)) $uid = $login;
		else {
			try {
				$uid = self::getUserId($login);
			} catch (\Exception $e) { 
				throw new \Exception("Unable to assign user to group. User $login not found");
				return false;
			}
		}
		// Get gid
		if ((int)($group)) $gid = $group;
		else {
			try {
				$gid = self::getGroupId($group);
			} catch (\Exception $e) { 
				throw new \Exception("Unable to assign user to group. Group $group not found");
				return false;
			}
		}
		return (Core::$db->exec_returning('INSERT INTO "ch_user_group" (uid, gid) VALUES (?,?)',
																	array((int)$uid, (int)$gid), 'ugid')) ? true : false;
	}
	
	public static function removeUserFromAllGroups($user) {
		$groups =self::getUserGroups($user, 'name');
		for ($i=0; $i < count($groups); $i++) {
			self::removeUserFromGroup($user, $groups[$i]);
		}
	}
	public static function removeUserFromGroup($login, $group) {
		// Get uid
		if ((int)($login)) $uid = $login;
		else {
			try {
				$uid = self::getUserId($login);
			} catch (\Exception $e) { 
				throw new \Exception("Unable to assign user to group. User $login not found");
				return false;
			}
		}
		// Get gid
		if ((int)($group)) $gid = $group;
		else {
			try {
				$gid = self::getGroupId($group);
			} catch (\Exception $e) { 
				throw new \Exception("Unable to assign user to group. Group $group not found");
				return false;
			}
		}
		return (Core::$db->Query('DELETE FROM "ch_user_group" WHERE uid = ? AND  gid = ?',array((int)$uid, (int)$gid))) ? true : false;
	}

	public static function addRight($name, $description=NULL) {
		$right = self::getRightId($name);
		if (!is_null($right)) {
			throw new \Exception("A right with name $name already exist");
			return false; 
		} else {
			return Core::$db->exec_returning('INSERT INTO "ch_right" ("name", "description") VALUES (?,?)', 
												array($name, $description),'rid');
		}
	}

	public static function getRight($name) {
		return Core::$db->fetchOne('SELECT * FROM "ch_right" WHERE "name"=?',
															array($name));
	}

	public static function getRightId($name) {
	 $id = Core::$db->fetchOne('SELECT "rid" FROM "ch_right" WHERE "name"=?',
															array($name));
	 return ($id) ? (int)$id : NULL;
	}

	public static function delRight($name) {
		// Delete right assignation
		self::delRightAssignation($name);
		// Delete Right
		Core::$db->query('DELETE FROM "ch_right" WHERE "name" = ?', array($name));
		return (isset(Core::$db->Affected_Rows)) ? true : false;
	}

	public static function assignRight($name, $group) {
		// Get the gid
		if ((int)$group) $gid = $group;
		else {
			try {
				$gid = self::getGroupId($group);
			} catch (\Exception $e) { 
				throw new \Exception("Unable to assign right. Group $group not found");
				return false;
			}
		}
		// If right exists get his id
		if ((int)$name) {

			$rid = $name;
		} else {
			$rid = self::getRightId($name);
		}
		if (!$rid) {
			throw new \Exception("Unable to assign right. Right $name does not exist");
			return false;
		}
		// Check if right is not already assigned
		if (self::groupHasRight($group, $name)) {
			throw new \Exception("Right $name already assigned to group $group");
			return true;
		}
		$assignationId = Core::$db->exec_returning('INSERT INTO "ch_group_right" ("gid", "rid") VALUES (?,?)',array($gid, $rid),'grid');

		// Group rights changed, we destroy the cache
		self::$_cache = NULL;

		// Return
		return $assignationId;
	}

	public static function delRightAssignation($right, $group=NULL) {
		if (is_int($right))
			$rid = $right;
		else 
			$rid = self::getRightId($right);

		if (is_null($group)) {
			Core::$db->query('DELETE FROM "ch_group_right" WHERE "rid"=?',
																		array($rid));
		} else {
			Core::$db->query('DELETE FROM "ch_group_right" WHERE "rid"=? AND gr."gid"=?',
																		array($rid, (int)$gid));
		}
		return (isset(Core::$db->Affected_Rows)) ? true : false;
	}

	public static function getUserRights($user) {
		$uid = (is_string($user)) ? self::getUserId($user) : $user;
		$result = Core::$db->query('SELECT ri."name" FROM "ch_right" ri LEFT JOIN "ch_group_right" gr ON ri."rid"=gr."rid" LEFT JOIN "ch_group" g ON gr."gid"=g."gid" LEFT JOIN "ch_user_group" ug ON g."gid"=ug."gid" LEFT JOIN "ch_user" us ON ug."uid"=us."uid" WHERE us."uid"=?',
															array($uid));
		$r = array();
		while($row = $result->fetchRow()) {
			$r[] = $row['name'];
		}
		return (sizeof($r) > 0) ? $r : NULL;
	}

	public static function getGroupRights($group) {
		$gid = (is_string($group)) ? self::getGroupId($group) : $group;
		$result = Core::$db->query('SELECT ri."name" FROM "ch_right" ri LEFT JOIN "ch_group_right" gr ON ri."rid"=gr."rid" LEFT JOIN "ch_group" g ON gr."gid"=g."gid" WHERE g."gid"=?',
															array($gid));
		$r = array();
		while($row = $result->fetchRow()) {
			$r[] = $row['name'];
		}
		return (sizeof($r) > 0) ? $r : NULL;
	}

	public static function userHasRight($right, $user=NULL) {
			if (is_null($user)) {
				if (!empty($_SESSION['login'])) $user = $_SESSION['login'];
				else return self::groupHasRight('anonymous',$right); 
			}
			$uid = (is_string($user)) ? self::getUserId($user) : $user;
			if (!isset(self::$_cache) || is_null(self::$_cache['u']) || !isset(self::$_cache['u'][$uid]) || is_null(self::$_cache['u'][$uid])) {
				self::$_cache['u'][$uid] = self::getUserRights($uid);
			}
			return (is_array(self::$_cache['u'][$uid]) && in_array($right, self::$_cache['u'][$uid])) ? true : false;
	}	

	public static function groupHasRight($group, $right) {
			$gid = (is_string($group)) ? self::getGroupId($group) : $group;
			if (!isset(self::$_cache) || is_null(self::$_cache['g']) || !isset(self::$_cache['g'][$gid]) || is_null(self::$_cache['g'][$gid])) {
				self::$_cache['g'][$gid] = self::getGroupRights($gid);
			}
			return (is_array(self::$_cache['g'][$gid]) && in_array($right, self::$_cache['g'][$gid])) ? true : false;
	}	

	public static function hook_mod_user_login($hookname, $userdata, $urlmatches) {
		$displayForm = true;
		$page = new \mod\webpage\Main();
		$form = new \mod\field\FieldForm($page->smarty, 'user_loginform', 'user/login_form_fields');
		$page->setLayout('user/login');
		if (!self::userIsLoggedIn()) { 
			if ($form->isPosted() && $form->isValid()) {
				$l = \core\Tools::cleanString($form->getValue('login'));
				$p = \core\Tools::cleanString($form->getValue('password'));
				if(self::checkAuth($l, $p)) {
					$displayForm = false;
					$page->smarty->assign('login_ok', true);
					$page->smarty->assign('url_redirect', ((isset($_REQUEST['from'])) ? urldecode($_REQUEST['from']) : 'http://'.$_SERVER['HTTP_HOST']));
				} else {
					$page->smarty->assign('login_failed', true);
				}
			}
		} else {
			$page->smarty->assign('url_redirect', 'http://'.$_SERVER['HTTP_HOST']);
			$displayForm = false;
		}
		if ($displayForm)
			$page->smarty->assign('loginform', $form->getHtml($page));
		$page->display();
	}

	public static function hook_mod_user_logout() {
		self::logout();
		$page = new \mod\webpage\Main();
		$page->setLayout('user/login');
		$page->smarty->assign('logout', true);
		$page->smarty->assign('url_redirect', 'http://'.$_SERVER['HTTP_HOST']);
		$page->display();
	}
	public static function hook_mod_user_manage_users($hookname, $userdata, $matches,$flags) {
                self::redirectIfNotLoggedIn();
		if (!self::userHasRight('Manage rights')) {
			return "error Manage user ";
		}
		$page = new \mod\webpage\Main();
		// get lang
		$lang=\mod\lang\Main::getCurrentLang();
		$page->smarty->assign('lang', $lang);
		
		$page->setLayout('user/admin/default');
		$page->display();
	}
  public static function hook_mod_user_edit($hookname, $userdata, $matches, $flags) {
		self::redirectIfNotLoggedIn();
		// check perm 
		if (!self::userHasRight('Manage rights')) {
			return false;
		}
		$uid=$matches[1]; 
		$view = self::getUserInfos($uid);
                $page = new \mod\webpage\Main();
		$page->smarty->assign('user', $view);
    		$page->smarty->assign('user_mode', 'edit');
                if ($flags & \mod\regroute\Main::flag_xmlhttprequest) {
                        $page->smarty->fetch('user/admin/edit');
                } else {
                        $page->setLayout('user/admin/edit');
                        $page->display();
                }
  }
  public static function hook_mod_user_create($hookname, $userdata, $matches, $flags) {
                \mod\user\Main::redirectIfNotLoggedIn();
		// check perm 
		if (!self::userHasRight('Manage rights')) {
			return false;
		}
		$db=\core\Core::$db;
		// prepare data for storage
		if ($matches['active'] == "on") $matches['active']=1;
		$dbParams=array();
		$login=self::cleanString($matches['login']);
		$dbParams[]=$login;	
		$dbParams[]=$matches['full_name'];	
		$dbParams[]=(int)$matches['active'];	
		$dbParams[]=$matches['email'];	
		$dbParams[]=$matches['password'];	
		$dbParams[]=date("Y-m-d H:i:s");	
		$dbParams[]=date("Y-m-d H:i:s");	

		return $db->exec_returning("INSERT INTO ch_user (
				login, 
				full_name, 
				status, 
				email, 
				pass, 
				created, 
				updated) VALUES 
					(?,?,?,?,md5(?),?,?)", $dbParams, 'uid');
		// commented While missing some optionality for drirect 
		// assignement to groups after user creation
		// return self::assignUserToGroup($login, "Registered");
  }

  public static function hook_mod_user_update($hookname, $userdata, $matches, $flags) {
                self::redirectIfNotLoggedIn();
		// check perm 
		if (!self::userHasRight('Manage rights')) {
			return false;
		}
		$db=\core\Core::$db;
		// prepare data for storage
		$dbParams=array();
		$q ="UPDATE ch_user 
				    SET login=?,
					full_name=?,
					status=?,
					email=?,";
		$login=self::cleanString($matches['login']);
		$dbParams[]=$login;	
		$dbParams[]=$matches['full_name'];	
		$dbParams[]=(int)$matches['active'];	
		$dbParams[]=$matches['email'];;
		if ($matches['password']) {
			$dbParams[]=$matches['password'];	
			$q .=" pass=md5(?),";
		}
		$dbParams[]=date("Y-m-d H:i:s");	
		$q .=" updated=? WHERE uid=?";
		$dbParams[]=(int)$matches['uid'];	
		$query= $db->query($q, $dbParams);
		return $matches['uid'];
	}
  private static function dbSort($sort) {
		$s=explode('_',$sort);
		$s[1]=strtoupper($s[1]);
		return $s[0]." ".$s[1];
  }
  private static function order_by($sort) {
		$sorted = self::dbSort($sort);
		$q =" ORDER BY ".$sorted;
		return $q;
   } 
  public static function cleanString($msg, $toUrl=false) { 
                // clea a string to make it compliant with the use of system_name compliant with a clean web url encoded path        
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
