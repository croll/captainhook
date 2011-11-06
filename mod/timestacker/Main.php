<?php

namespace mod\timestacker;

class Timestacker {

	function __construct() {
	}

	function getStack($id) {
		$res = Core::$db->Execute('SELECT * FROM ch_stack WHERE sid=?',
															array((int)$id));
		if (!$res) return null;
		return $res->fetchObject();
	}

	function newStack($name, $description, $color=null, $date=null) {
		$res = Core::$db->Execute('INSERT INTO ch_stack (name, description, color, `date`) VALUES (?,?,?,?)',
															array($name, $description, $color, $date));
		if (!Core::$db->Insert_ID) {
			Core::log("Unable to create stack $name");
			return false;
		}
		return true;
	}

	function assignStack($sid, $uid, $owner=0) {
		$res = Core::$db->Execute('INSERT INTO ch_stack_assignation (sid, uid, owner) VALUES (?,?,?)',
															array((int)$sid, (int)$uid, (int)$owner));
		if (!Core::$db->Insert_ID) {
			Core::log("Unable to assign stack $sid");
			return false;
		}
		return true;
	}

	function getStackAssignation($id) {
		$res = Core::$db->Execute('SELECT uid, owner FROM ch_stack_assignation WHERE sid=?',
															array((int)$id));
		if (!$res) return null;
		$row = $res->fetchObject();
		return $row->uid;
	}

	function getUserStacks($uid) {
		$res = Core::$db->Execute('SELECT s.*, a.owner FROM ch_stack s LEFT JOIN ch_stack_assignation a ON s.sid=a.sid WHERE a.uid=?',
															array((int)$uid));
		if (!$res) return null;
		$stacks = array();
		while($row = $res->fetchNextObject()) {
			$stacks[] = $row;
		}
		return $stacks;
	}

	function getStackTasks($sid) {
		$res = Core::$db->Execute('SELECT t.* FROM ch_stack s LEFT JOIN ch_task_stack ts ON s.sid=ts.sid WHERE s.sid=?',
															array((int)$sid));
		if (!$res) return null;
		$tasks = array();
		while($row = $res->fetchObject()) {
			$tasks[] = $row;
		}
		return $tasks;
	}

	function assignTaskToStack($tid, $sid) {
		$res = Core::$db->Execute('INSERT INTO ch_task_stack (tid, sid) VALUES (?,?)',
															array((int)$tid,(int)$uid));
		if (!Core::$db->Insert_ID) {
			Core::log("Unable to assign task $tid to stack $sid");
			return false;
		}
		return true;
	}

	function getTaskWIP($tid) {
		$res = Core::$db->Execute('SELECT t.*, w.*, u.full_name LEFT JOIN ch_task_wip w ON t.tid=w.tid LEFT JOIN ch_user u ON w.uid=u.uid WHERE t.tid=?',
															array((int)$tid));
		if (!$res) return null;
		return $res->fetchObject();
	}

	function setTaskWIP($tid, $description, $uid, $start, $stop) {
		$res = Core::$db->Execute('INSERT INTO ch_task_wip (tid, description, uid, start, stop) VALUES (?,?,?,?,?)',
															array((int)$tid, $description, (int)$uid, $start, $stop));
		if (!Core::$db->Insert_ID) {
			Core::log("Unable to assign task $tid to stack $sid");
			return false;
		}
		return true;
	}

}
