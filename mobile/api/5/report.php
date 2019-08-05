<?php

/**
 *      [Discuz!] (C)2001-2099 Comsenz Inc.
 *      This is NOT a freeware, use is subject to license terms
 *
 *      $Id: report.php 34314 2019-05-23 01:04:24Z leiyu $
 */

if(!defined('IN_MOBILE_API')) {
	exit('Access Denied');
}

$_GET['mod'] = 'report';
include_once 'misc.php';

class mobile_api {

	function common() {

	}

	function output() {
        $variable = array();

		mobile_core::result(mobile_core::variable($variable));
    }

}

?>