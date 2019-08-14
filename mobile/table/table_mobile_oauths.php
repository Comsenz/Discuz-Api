<?php

/**
 *      [Discuz!] (C)2001-2099 Comsenz Inc.
 *      This is NOT a freeware, use is subject to license terms
 *
 *      $Id: table_mobile_oauths.php 34398 2014-04-14 07:11:22Z nemohou $
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class table_mobile_oauths extends discuz_table {

	public function __construct() {
		$this->_table = 'mobile_oauths';

		parent::__construct();
	}

	public function fetch_by_uid_type($uid, $type) {
		return DB::fetch_first("SELECT * FROM ".DB::table($this->_table)." WHERE ".DB::field('uid', $uid) . " AND " . DB::field('type', $type));
	}

	public function update_uid_status($uid, $openid, $status = 1, $type = '') {
		if($uid && $status && $openid && $type) {
			$where = array('type' => $type, 'openid' => $openid);
			$data = array('uid' => $uid, 'status' => $status);
			return DB::update($this->_table, $data, $where);
		}
		return 0;
	}

	public function fetch_by_openid_type($openid, $type = '') {
		$where = DB::field('openid', $openid)." AND ".DB::field('type', $type);

        return DB::fetch_first("SELECT * FROM ".DB::table($this->_table)." WHERE ".$where);
	}

	public function fetch_all_by_uid($uid, $order = '') {
		return DB::fetch_all("SELECT * FROM ".DB::table($this->_table)." WHERE ".DB::field('uid', $uid).$order);
	}

	public function update_uid_type($uid, $type, $status) {
		if($uid && $type) {
			$where = array('uid' => $uid, 'type' => $type);
			$data = array('status' => $status);
			return DB::update($this->_table, $data, $where);
		}
		return 0;
	}

}