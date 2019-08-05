<?php

/**
 *      [Discuz!] (C)2001-2099 Comsenz Inc.
 *      This is NOT a freeware, use is subject to license terms
 *
 *      $Id: table_forum_groupuser.php 31121 2012-07-18 06:01:56Z liulanbo $
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class table_forum_groupuser_ext extends discuz_table
{
	public function __construct() {

		$this->_table = 'forum_groupuser';
		$this->_pk    = '';

		parent::__construct();
	}
	
	public function update_for_user($uid, $fid, $threads = null, $replies = null, $level = null, $nickname = null, $mobile = null) {
		if(empty($uid) || empty($fid)) {
			return array();
		}
		$sqladd = $threads !== null ? 'threads='.intval($threads) : '';
		if($replies !== null) {
			$sqladd .= ($sqladd ? ', ' : '').'replies='.intval($replies);
		}
		if($level !== null) {
			$sqladd .= ($sqladd ? ', ' : '').'level='.intval($level);
		}

		if($nickname !== null) {
			$sqladd .= ($sqladd ? ', ' : '').DB::field('nickname', $nickname);
		}
		if($mobile !== null) {
			$sqladd .= ($sqladd ? ', ' : '').DB::field('mobile', $mobile);
		}
		DB::query("UPDATE %t SET $sqladd WHERE fid=%d AND ".DB::field('uid', $uid), array($this->_table, $fid));
	}

	public function fetch_all_by_fids($fids, $usekey = true) {
		if(empty($fids)) {
			return array();
		}

		$query = DB::query("SELECT * FROM %t WHERE fid IN(%n) AND level IN(1,2,3,4)", array($this->_table, $fids));

		$groupuserlist = array();
		while($groupuser = DB::fetch($query)) {
			if($usekey) {
				$groupuserlist[$groupuser['fid']][$groupuser['uid']] = $groupuser;
			} else {
				$groupuserlist[$groupuser['fid']][] = $groupuser;
			}
		}

		return $groupuserlist;
	}
}

?>