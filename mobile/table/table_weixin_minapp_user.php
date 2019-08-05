<?php

/**
 *      [Discuz!] (C)2001-2099 Comsenz Inc.
 *      This is NOT a freeware, use is subject to license terms
 *
 *      $Id: table_weixin_minapp_user.php 34398 2014-04-14 07:11:22Z nemohou $
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class table_weixin_minapp_user extends discuz_table {

	public function __construct() {
		$this->_table = 'weixin_minapp_user';
		$this->_pk = 'uid';
		$this->_pre_cache_key = 'weixin_minapp_user_';
		$this->_cache_ttl = 0;

		parent::__construct();
    }
    
    public function fetch_by_openid($openid) {
        return DB::fetch_first("SELECT * FROM ".DB::table($this->_table)." WHERE ".DB::field('openid', $openid));
    }
}