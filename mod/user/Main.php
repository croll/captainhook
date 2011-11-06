<?php

namespace mod\user;
use \core\Core;

class Main {

	private static $_cache;

	function __construct() {
	}

	public static function checkAuth($login='',$password='') {
		if ((!empty($login)) && (!empty($password))) {
			if ( (preg_match("/^[a-zA-Z0-9-_]+$/",$login)) && (preg_match("/^[a-zA-Z0-9-_]+$/",$password)) ) {
				if ($row = Core::$db->GetOne('SELECT `full_name` FROM `ch_user` WHERE UPPER(`login`)=UPPER(?) AND `pass`=md5(?) AND `status`=1', 
																			array($login, $password))) {
					if ($row) {
						$_SESSION['full_name'] = $row['full_name'];
						$_SESSION['hash'] = self::genTempHash($login,$password);
            \core\Hook::call('login_ok');
					} else {
            \core\Hook::call('login_failed');
          }
				}
			}
		}
		unset($_SESSION);
		session_destroy();
	}

	public static function logout() {
    \core\Hook::call('mod/user/logout');
		unset($_SESSION);
		session_destroy();
		header('Location: index.php');
	}

	protected static function genTempHash($login,$password) {
		return md5($login.date('Ymdhis')+rand(0,100000).$password);
	}

	public static function addUser($name, $login, $password, $status=1) {
		Core::$db->Execute('INSERT INTO `ch_user` (`full_name`, `login`, `pass`, `status`) VALUES (?,?,MD5(?),?)', 
												array($name, $login, $password, $status));
		return (isset(Core::$db->Insert_ID)) ? Core::$db->Insert_ID : NULL;
	}

	public static function getUserInfos($id) {
		$result = Core::$db->Execute('SELECT * FROM `ch_user` WHERE `uid`=?',
																	array((int)$id));
		return $result->FetchObject();
	}

	public static function getUserId($name) {
		 $id = Core::$db->GetOne('SELECT `uid` FROM `ch_user` WHERE LOWER(`login`)=LOWER(?)',
																	array($name));
		 return ($id) ? (int)$id : NULL;
	}

	public static function delUser($user) {
		if (is_int($user))
			Core::$db->Execute('DELETE FROM `ch_user` WHERE `name` = ?', 
												array($user));
		else
			Core::$db->Execute('DELETE FROM `ch_user` WHERE `uid` = ?', 
												array($user));
		return (isset(Core::$db->Affected_Rows)) ? true : false;
	}

	public static function isAuthentified($hash=NULL) {
		if (empty($_SESSION['hash']) || empty($_SESSION['login'])) return false;
		if ($hash !== NULL && !$_SESSION['hash'] != $hash) return false;
		return true;
	}

	public static function addGroup($name, $status=1) {
		Core::$db->Execute('INSERT INTO `ch_group` (`name`, `status`) VALUES (?, ?)', 
												array($name, (int)$status));
		return (isset(Core::$db->Insert_ID)) ? Core::$db->Insert_ID : NULL;
	}

	public static function getGroup($id) {
		$result = Core::$db->Execute('SELECT * FROM `ch_group` WHERE `gid`=?',
																	array((int)$id));
		return $result->FetchObject();
	}

	public static function getGroupId($name) {
		 $id = Core::$db->GetOne('SELECT `gid` FROM `ch_group` WHERE LOWER(`name`)=LOWER(?)',
																	array($name));
		 return ($id) ? (int)$id : NULL;
	}

	public static function delGroup($group) {
		if (is_int($group))
			Core::$db->Execute('DELETE FROM `ch_group` WHERE `name` = ?)', 
												array($group));
		else
			Core::$db->Execute('DELETE FROM `ch_group` WHERE `gid` = ?)', 
												array($group));
		return (isset(Core::$db->Affected_Rows)) ? true : false;
	}

	public static function getUserGroups($user) {
		if (is_int($user))
			$result = Core::$db->Execute('SELECT `gid`, `name`, `status` FROM `ch_group` gr LEFT JOIN `ch_user_group` ug ON gr.`gid` = ug.`gid` LEFT JOIN `ch_user` us ON ug.`uid`=us.`uid`  WHERE us.`name`=?',
																	array($name));
		else {
			$uid = self::getUserId($name);
			$result = Core::$db->Execute('SELECT `gid`, `name`, `status` FROM `ch_group` gr LEFT JOIN `ch_user_group` ug ON gr.`gid` = ug.`gid` LEFT JOIN `ch_user` us ON ug.`uid`=us.`uid` WHERE us.`uid`=?',
																	array((int)$uid));
		}
		while($row = $result->FetchNextObject()) {
			$g[] = $row;
		}
		return $g;
	}

	public static function getGroupsList($status=1) {
		$result = Core::$db->Execute('SELECT * FROM `ch_group` WHERE status=?',
															array($status));
		$g = array();
		while($row = $result->FetchNextObject()) {
			$g[] = $row;
		}
		return $g;
	}

	public static function addRight($name, $description=NULL) {
		$right = self::getRightId($name);
		if (!is_null($right)) {
			throw new \Exception("A right with name $name already exist");
			return false; 
		} else {
			Core::$db->Execute('INSERT INTO `ch_right` (`name`, `description`) VALUES (?,?)', 
												array($name, $description));
				return (isset(Core::$db->Insert_ID)) ? Core::$db->Insert_ID : NULL;
		}
	}

	public static function getRight($name) {
		return Core::$db->GetOne('SELECT * FROM `ch_right` WHERE `name`=?',
															array($name));
	}

	public static function getRightId($name) {
	 $id = Core::$db->GetOne('SELECT `rid` FROM `ch_right` WHERE `name`=?',
															array($name));
	 return ($id) ? (int)$id : NULL;
	}

	public static function delRight($name) {
		// Delete right assignation
		self::delRightAssignation($name);
		// Delete Right
		Core::$db->Execute('DELETE FROM `ch_right` WHERE `name` = ?', 
											array($name));
		return (isset(Core::$db->Affected_Rows)) ? true : false;
	}

	public static function assignRight($name, $group) {
		// Get the gid
		if (is_int($group)) $gid = $group;
		else {
			try {
				$gid = self::getGroupId($group);
			} catch (\Exception $e) { 
				throw new \Exception("Unable to assign right. Group $group not found");
				return false;
			}
		}
		// If right exists get his id
		$rid = self::getRightId($name);
		if (!$rid) {
			throw new \Exception("Unable to assign right. Right $name does not exist");
			return false;
		}
		// Check if right is not already assigned
		if (self::groupHasRight($group, $name)) {
			throw new \Exception("Right $name already assigned to group $group");
			return true;
		}
		self::groupHasRight($group, $right);
		self::groupHasRight($group, $right);
		self::groupHasRight($group, $right);
		Core::$db->Execute('INSERT INTO `ch_group_right` (`gid`, `rid`) VALUES (?,?)', 
											array($gid, $rid));
		$assignationId = (isset(Core::$db->Insert_ID)) ? Core::$db->Insert_ID : NULL;

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

		echo "$right - $rid<br>";
		if (is_null($group)) {
			Core::$db->Execute('DELETE FROM `ch_group_right` WHERE `rid`=?',
																		array($rid));
		} else {
			Core::$db->Execute('DELETE FROM `ch_group_right` WHERE `rid`=? AND gr.`gid`=?',
																		array($rid, (int)$gid));
		}
		return (isset(Core::$db->Affected_Rows)) ? true : false;
	}

	public static function getUserRights($user) {
		$uid = (is_string($user)) ? self::getUserId($user) : $user;
		$result = Core::$db->Execute('SELECT ri.`name` FROM `ch_right` ri LEFT JOIN `ch_group_right` gr ON ri.`rid`=gr.`rid` LEFT JOIN `ch_group` g ON gr.`gid`=g.`gid` LEFT JOIN `ch_user_group` ug ON g.`gid`=ug.`gid` LEFT JOIN `ch_user` us ON ug.`uid`=us.`uid` WHERE us.`uid`=?',
															array($uid));
		$r = array();
		while($row = $result->FetchNextObject()) {
			$r[] = $row->NAME;
		}
		return (sizeof($r) > 0) ? $r : NULL;
	}

	public static function getGroupRights($group) {
		$gid = (is_string($group)) ? self::getGroupId($group) : $group;
		$result = Core::$db->Execute('SELECT ri.`name` FROM `ch_right` ri LEFT JOIN `ch_group_right` gr ON ri.`rid`=gr.`rid` LEFT JOIN `ch_group` g ON gr.`gid`=g.`gid` WHERE g.`gid`=?',
															array($gid));
		$r = array();
		while($row = $result->FetchNextObject()) {
			$r[] = $row->NAME;
		}
		return (sizeof($r) > 0) ? $r : NULL;
	}

	public static function userHasRight($user, $right) {
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

	public static function hook_core_init_http() {
		try {
			#echo self::addRight("test", "un droit de test")."<br>"; 
			#echo self::assignRight("test", "admin")."<br>"; 
			#echo self::delRight('test');	
			#echo (int) self::userHasRight('admin', 'test');
			#echo (int) self::groupHasRight('admin', 'test');
		} catch (\Exception $e) {
			echo $e->getMessage();
		}
	}

}
