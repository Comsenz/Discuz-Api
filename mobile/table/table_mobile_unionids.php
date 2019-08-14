<?php

/**
 *      [Discuz!] (C)2001-2099 Comsenz Inc.
 *      This is NOT a freeware, use is subject to license terms
 *
 *      $Id: table_mobile_unionids.php 34398 2014-04-14 07:11:22Z nemohou $
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class table_mobile_unionids extends discuz_table {

	public function __construct() {
		$this->_table = 'mobile_unionids';

		parent::__construct();
	}
    
	public function fetch_by_unionid($unionid) {
		return DB::fetch_first("SELECT * FROM ".DB::table($this->_table)." WHERE ".DB::field('unionid', $unionid));
	}


	public function update_uid_by_unionid($uid, $unionid) {
		if($uid && $unionid) {
			return DB::query("UPDATE ".DB::table($this->_table)." SET uid=%s WHERE ".DB::field('unionid', $unionid), array($uid));
		}
		return 0;
	}
}