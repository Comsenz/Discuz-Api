<?php

/**
 *      [Discuz!] (C)2001-2099 Comsenz Inc.
 *      This is NOT a freeware, use is subject to license terms
 *
 *      $Id: login.php 34314 2014-02-20 01:04:24Z nemohou $
 */

if(!defined('IN_MOBILE_API')) {
	exit('Access Denied');
}

$_GET['mod'] = 'logging';
$_GET['action'] = !empty($_GET['action']) ? $_GET['action'] : 'login';
include_once 'member.php';

class mobile_api {

	function common() {

	}

	function output() {
		global $_G;
		
		if($_G['uid'] && $_GET['openid']) {
			if(!C::t('#mobile#weixin_minapp_user')->fetch($_G['uid'])) {
				C::t('#mobile#weixin_minapp_user')->insert(array('uid' => $_G['uid'], 'openid' => $_GET['openid'], 'status' => 1), false, true);
			}
		}

		$variable = array();
		mobile_core::result(mobile_core::variable($variable));
	}

}

?>