<?php  //  -*- mode:php; tab-width:2; c-basic-offset:2; -*-

namespace mod\user;

class Ajax {

  public static function saveUser($params) {
	//return $params;
	if(isset($params['status']) && $params['status'] == "on") {
		$params['status'] = 1;

	}
	if(!isset($params['uid']) || $params['uid']==0) {
		return \mod\user\Main::hook_mod_user_create($hookname, $userdata, $params, $flags);
	} else {
		return \mod\user\Main::hook_mod_user_update($hookname, $userdata, $params, $flags);
	}
  }
   public static function resign($params) {
	\mod\user\Main::redirectIfNotLoggedIn();
	// check perm
	if (!\mod\user\Main::userHasRight('Manage rights')) {
			return false;
	}
  	return \mod\user\Main::removeUserFromGroup((int)$params['uid'], (int)$params['gid']);
   }
  public static function permGroups($params) {
	\mod\user\Main::redirectIfNotLoggedIn();
	// check perm
	if (!\mod\user\Main::userHasRight('Manage rights')) {
			return false;
	}
	$db=\core\Core::$db;
	$dbParams= array();
	$dbParams[]=(int)$params['rid'];
	// get groups
	$q='SELECT r."rid", g."gid", g."name", g."status" FROM "ch_group" g LEFT JOIN "ch_group_right" gr ON g."gid"= gr."gid" LEFT JOIN "ch_right" r ON gr."rid"=r."rid" WHERE r."rid"=?';

	return $db->fetchAll($q, $dbParams);
  }
  public static function groupPerms($params) {
	\mod\user\Main::redirectIfNotLoggedIn();
	// check perm
	if (!\mod\user\Main::userHasRight('Manage rights')) {
			return false;
	}
	$db=\core\Core::$db;
	$dbParams= array();
	$dbParams[]=(int)$params['gid'];
	// get permissions
	$q='SELECT ri."rid", ri."name" as perm_name, ri.description FROM "ch_right" ri LEFT JOIN "ch_group_right" gr ON ri."rid"=gr."rid" LEFT JOIN "ch_group" g ON gr."gid"=g."gid" WHERE g."gid"=?';

	return $db->fetchAll($q, $dbParams);
  }
  public static function getNotAssignedGroups($params) {
	\mod\user\Main::redirectIfNotLoggedIn();
	// check perm
	if (!\mod\user\Main::userHasRight('Manage rights')) {
			return false;
	}
	$db=\core\Core::$db;
	$dbParams= array();
	$dbParams[]=(int)$params['rid'];
	// get groups
	$q='SELECT g."gid", g."name" , g."status" FROM "ch_group" g WHERE g."gid" NOT IN (SELECT "gid" FROM "ch_group_right" WHERE "rid"=?)';

	return $db->fetchAll($q, $dbParams);
  }
  public static function userPerms($params) {
	\mod\user\Main::redirectIfNotLoggedIn();
	// check perm
	if (!\mod\user\Main::userHasRight('Manage rights')) {
			return false;
	}
	$db=\core\Core::$db;
	$dbParams= array();
	$dbParams[]=(int)$params['uid'];
	$q='SELECT ri."name" as perm_name, ri."description", g.name as "inherit_from_group" FROM "ch_right" ri LEFT JOIN "ch_group_right" gr ON ri."rid"=gr."rid" LEFT JOIN "ch_group" g ON gr."gid"=g."gid" LEFT JOIN "ch_user_group" ug ON g."gid"=ug."gid" LEFT JOIN "ch_user" us ON ug."uid"=us."uid" WHERE us."uid"=?';
	return $db->fetchAll($q, $dbParams);
  }
  public static function selectPerm($params) {
	\mod\user\Main::redirectIfNotLoggedIn();
	// check perm
	if (!\mod\user\Main::userHasRight('Manage rights')) {
			return false;
	}
	$db=\core\Core::$db;
	$dbParams= array();
	$dbParams[]=(int)$params['gid'];
	// select right list that are not assignated to group admin
	$q='SELECT r."name", r."description" FROM "ch_right" r WHERE r."rid" NOT IN (SELECT "rid" FROM "ch_group_right" WHERE "gid"=?)';
	$save=$db->fetchAll($q, $dbParams);
	return $save;
  }
  public static function permList($params) {
	\mod\user\Main::redirectIfNotLoggedIn();
	// check perm
	if (!\mod\user\Main::userHasRight('Manage rights')) {
			return false;
	}
	$db=\core\Core::$db;
	$dbParams= array();
	// select right list that are not assignated to group admin
	$q='SELECT r."rid", r."name", r."description" FROM "ch_right" r';
	return $db->fetchAll($q, NULL);
  }
  public static function userGroups($params) {
	\mod\user\Main::redirectIfNotLoggedIn();
	// check perm
	if (!\mod\user\Main::userHasRight('Manage rights')) {
			return false;
	}
	$db=\core\Core::$db;
	$dbParams= array();
	$dbParams[]= (int)$params['uid'];
	// select right list that are not assignated to group admin
	$q='SELECT u."uid", g."gid", g."name" , g."status" FROM "ch_group" g, ch_user_group ug , ch_user u WHERE  u."uid"= ug.uid AND g."gid"= ug."gid" AND ug."uid" = ?';
	return $db->fetchAll($q, $dbParams);
  }
  public static function groupUserMembership($params) {
	\mod\user\Main::redirectIfNotLoggedIn();
	// check perm
	if (!\mod\user\Main::userHasRight('Manage rights')) {
			return false;
	}
	$db=\core\Core::$db;
	$dbParams= array();
	$dbParams[]= (int)$params['gid'];
	// select right list that are not assignated to group admin
	$q='SELECT u."uid", u."login", u."full_name", u."email", u."created", u."updated", u.status FROM "ch_user" u, ch_user_group ug WHERE u."uid" != ug."uid" AND ug."gid"=?';
	return $db->fetchAll($q, $dbParams);
  }
  public static function usersNotMember($params) {
	\mod\user\Main::redirectIfNotLoggedIn();
	// check perm
	if (!\mod\user\Main::userHasRight('Manage rights')) {
			return false;
	}
	$db=\core\Core::$db;
	$dbParams= array();
	$dbParams[]= (int)$params['gid'];
	// select right list that are not assignated to group admin
	$q='SELECT u."uid", u."login", u."full_name", u."email", u."created", u."updated", u.status FROM "ch_user" u WHERE u."uid" NOT IN (SELECT "uid" FROM "ch_user_group"  WHERE "gid"=?)';
	return $db->fetchAll($q, $dbParams);
  }
  public static function groupList($params) {
	\mod\user\Main::redirectIfNotLoggedIn();
	// check perm
	if (!\mod\user\Main::userHasRight('Manage rights')) {
			return false;
	}
	$db=\core\Core::$db;
	$dbParams= array();
	// select right list that are not assignated to group admin
	$q='SELECT g."gid", g."name", g."status" FROM "ch_group" g';
	return $db->fetchAll($q, NULL);
  }
  public static function userList($params) {
	\mod\user\Main::redirectIfNotLoggedIn();
	// check perm
	if (!\mod\user\Main::userHasRight('Manage rights')) {
			return false;
	}
	$db=\core\Core::$db;
	// select right list that are not assignated to group admin
	$q='SELECT u."uid", u."login", u."full_name", u."email", u."created", u."updated", u.status FROM "ch_user" u';
	return $db->fetchAll($q, NULL);
  }

  public static function membersList($params) {
	\mod\user\Main::redirectIfNotLoggedIn();
	// check perm
	if (!\mod\user\Main::userHasRight('Manage rights')) {
			return false;
	}
	$db=\core\Core::$db;
	$dbParams= array();
	$dbParams[]= (int)$params['gid'];
	// select right list that are not assignated to group admin
	$q='SELECT g."gid", u."uid", u."login", u."full_name", u."email", u."created", u."updated", u.status FROM "ch_user" u, ch_user_group ug , ch_group g WHERE g."gid"=ug."gid" AND u."uid"= ug."uid" AND ug."gid"=?';
	return $db->fetchAll($q, $dbParams);
  }
  public static function selectGroup($params) {
	\mod\user\Main::redirectIfNotLoggedIn();
	// check perm
	if (!\mod\user\Main::userHasRight('Manage rights')) {
			return false;
	}
	$db=\core\Core::$db;
	$dbParams= array();
	$dbParams[]=(int)$params['uid'];
	// select right list that are not assignated to group admin
	$q='SELECT g."name" FROM "ch_group" g WHERE g."gid" NOT IN (SELECT "gid" FROM "ch_user_group" WHERE "uid"=?)';
	$save=$db->fetchAll($q, $dbParams);
	return $save;
  }
  public static function getUser($params) {
	\mod\user\Main::redirectIfNotLoggedIn();
	// check perm
	if (!\mod\user\Main::userHasRight('Manage rights')) {
			return false;
	}
	return \mod\user\Main::getUserInfos($params['uid']);
  }

  public static function getUserListSimple($params) {
	\mod\user\Main::redirectIfNotLoggedIn();
	$q='SELECT "full_name" FROM "ch_user"';
	$ret = array();
	foreach(\core\Core::$db->fetchAll($q, NULL) as $u) {
		$ret[] = $u['full_name'];
	}
	return $ret;
  }
  
  public static function getGroup($params) {
	\mod\user\Main::redirectIfNotLoggedIn();
	// check perm
	if (!\mod\user\Main::userHasRight('Manage rights')) {
			return false;
	}
	return \mod\user\Main::getGroup($params['gid']);
  }
  public static function getPerm($params) {
	\mod\user\Main::redirectIfNotLoggedIn();
	// check perm
	if (!\mod\user\Main::userHasRight('Manage rights')) {
			return false;
	}
	$db=\core\Core::$db;
	$dbParams= array();
	$dbParams[]=(int)$params['rid'];
	$q='SELECT r."rid", r."name", r."description" FROM "ch_right" r WHERE r."rid"=?';
	$perm = $db->fetchAll($q, $dbParams);
	return $perm;
  }
  public static function deleteUser($params) {
	\mod\user\Main::redirectIfNotLoggedIn();
	// check perm
	if (!\mod\user\Main::userHasRight('Manage rights')) {
			return false;
	}
	$uid=$params['uid'];
	// unassign user to all assigned groups
  \mod\user\Main::delUser($uid);
	return 	$uid;

  }
  public static function deleteGroup($params) {
	\mod\user\Main::redirectIfNotLoggedIn();
	// check perm
	if (!\mod\user\Main::userHasRight('Manage rights')) {
			return false;
	}
	$gid=$params['gid'];
	// delete the rights assigned to the group
	//\mod\user\Main::removeUserFromAllGroups($uid);
	// delete group
	$db=\core\Core::$db;
	$del=$db->query("DELETE FROM ch_group WHERE gid=?", array((int)$gid));

	return 	$gid;

  }
  public static function deleteGroupPerm($params) {
	\mod\user\Main::redirectIfNotLoggedIn();
	// check perm
	if (!\mod\user\Main::userHasRight('Manage rights')) {
			return false;
	}
	$dbParams = array();
	$dbParams[] = (int)$params['rid'];
	$dbParams[] = (int)$params['gid'];
	// delete the rights assigned to the group
	$db=\core\Core::$db;
	$del=$db->query("DELETE FROM ch_group_right WHERE rid=? AND gid=?", $dbParams);

	return 	$params;

  }
  public static function deletePerm($params) {
	\mod\user\Main::redirectIfNotLoggedIn();
	// check perm
	if (!\mod\user\Main::userHasRight('Manage rights')) {
			return false;
	}
	$rid=$params['rid'];
	// delete the rights assigned to the group
	//\mod\user\Main::removeUserFromAllGroups($uid);
	// delete group
	$db=\core\Core::$db;
	$del=$db->query("DELETE FROM ch_right WHERE rid=?", array((int)$rid));

	return 	$rid;

  }
  public static function saveUserGroups($params) {
	\mod\user\Main::redirectIfNotLoggedIn();
	// check perm
	if (!\mod\user\Main::userHasRight('Manage rights')) {
			return false;
	}
	$groups = explode(',', $params['groups']);
	for ($i=0; $i < count($groups); $i++) {
		if ($groups[$i] != '') {
			\mod\user\Main::assignUserToGroup($params["uid"], $groups[$i]);
		}

	}
	return $params;

  }
  public static function saveGroupMembership($params) {
	\mod\user\Main::redirectIfNotLoggedIn();
	// check perm
	if (!\mod\user\Main::userHasRight('Manage rights')) {
			return false;
	}
	$users = explode(',', $params['users']);
	for ($i=0; $i < count($users); $i++) {
		if ($users[$i] != '') {
			\mod\user\Main::assignUserToGroup($users[$i], (int)$params['gid']);

		}

	}
	return $params;
  }
  public static function savePermGroups($params) {
	\mod\user\Main::redirectIfNotLoggedIn();
	// check perm
	if (!\mod\user\Main::userHasRight('Manage rights')) {
			return false;
	}
	$groups = explode(',', $params['groups']);
	for ($i=0; $i < count($groups); $i++) {
		if ($groups[$i] != '') {
			\mod\user\Main::assignRight($params['rid'], $groups[$i]);

		}

	}
	return $params;
  }

  public static function saveGroupPerms($params) {
	\mod\user\Main::redirectIfNotLoggedIn();
	// check perm
	if (!\mod\user\Main::userHasRight('Manage rights')) {
			return false;
	}
	$perms = explode(',', $params['perms']);
	for ($i=0; $i < count($perms); $i++) {
		if ($perms[$i] != '') {
			\mod\user\Main::assignRight($perms[$i], (int)$params['gid']);

		}

	}
	return $params;
  }
  public static function saveGroup($params) {
	\mod\user\Main::redirectIfNotLoggedIn();
	// check perm
	if (!\mod\user\Main::userHasRight('Manage rights')) {
			return false;
	}
	$db=\core\Core::$db;
	$dbParams= array();
	$dbParams[]=$params['name'];

	if (isset($params['active'])) $dbParams[]=1;
	else $dbParams[]=0;

	if ($params['gid']) {
		$dbParams[]=$params['gid'];
		$q='UPDATE "ch_group" set name=?, status=? WHERE gid=?';
		$save=$db->query($q, $dbParams);
		return $params['gid'];
	} else {
		$q='INSERT INTO "ch_group" ("name", "status") VALUES (?,?)';
		return $db->exec_returning($q, $dbParams,'gid');
	}
  }
  public static function savePerm($params) {
	\mod\user\Main::redirectIfNotLoggedIn();
	// check perm
	if (!\mod\user\Main::userHasRight('Manage rights')) {
			return false;
	}
	$db=\core\Core::$db;
	$dbParams= array();
	$dbParams[]=$params['name'];
	$dbParams[]=$params['description'];
	if (isset($params['rid']) && $params['rid'] !=0) {
		$dbParams[]=$params['rid'];
		$q='UPDATE "ch_right" set name=?, description=? WHERE rid=?';
		$save=$db->query($q, $dbParams);
	} else {
		return $db->exec_returning('INSERT INTO "ch_right" ("name", "description") VALUES (?,?)', $dbParams, 'rid');
	}
  }
}
