<?php

/**
 *      [Discuz!] (C)2001-2099 Comsenz Inc.
 *      This is NOT a freeware, use is subject to license terms
 *
 *      $Id: sendpm.php 35183 2015-01-14 07:46:53Z nemohou $
 */

if(!defined('IN_MOBILE_API')) {
	exit('Access Denied');
}

$_GET['mod'] = 'spacecp';
$_GET['ac'] = 'pm';
$_GET['op'] = 'send';
include_once 'home.php';

class mobile_api {

	function common() {
		$_POST = $_GET;

		if($_GET['mapifrom'] == 'ios' || $_GET['mapifrom'] == 'android') {
			$exp = '/#\[\\d{2}_\\d{2}\]/';
			preg_match_all($exp, $_POST['message'], $match);

			if($match) {
				foreach($match[0] as $value) {
					$typeid = intval(substr($value, 2, 2));
					$displayorder = intval(substr($value, 5, 2));
					$smiley = DB::fetch_first('SELECT code FROM ' . DB::table('common_smiley') . ' WHERE typeid=' . $typeid . ' AND displayorder=' . $displayorder);
					$_POST['message'] = str_replace($value, $smiley['code'], $_POST['message']);
				}
			}
		}
	}

	function output() {
		global $_G;
		$variable = array(
			'pmid' => $GLOBALS['return']
		);
		mobile_core::result(mobile_core::variable($variable));
	}

}

?>