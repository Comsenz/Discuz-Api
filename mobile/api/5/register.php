<?php

/**
 *      [Discuz!] (C)2001-2099 Comsenz Inc.
 *      This is NOT a freeware, use is subject to license terms
 *
 *      $Id: register.php 32489 2013-01-29 03:57:16Z monkey $
 */

if(!defined('IN_MOBILE_API')) {
	exit('Access Denied');
}
include_once 'member.php';

class mobile_api {

	function common() {
		global $_G;

		if(!empty($_POST['regsubmit'])) {

			$_G['setting']['seccodestatus'] = 0;
			$_G['setting']['regverify'] = 0;
			$_G['setting']['bbrules'] = 0;
			$_G['setting']['bbrulesforce'] = 0;

			$ctl_obj = new register_ctl();
			
			$ctl_obj->setting = $_G['setting'];
			$ctl_obj->setting['regstatus'] = $ctl_obj->setting['regstatus'] ? $ctl_obj->setting['regstatus'] : 1;
			$ctl_obj->setting['secqaa']['status'] = 0;
			$ctl_obj->setting['sendregisterurl'] = FALSE;
			
			$ctl_obj->template = 'member/register';
			$ctl_obj->extrafile = dirname(__FILE__).'/register_bind.php';
			$ctl_obj->on_register();
		} else {
			global $_G;

			$variable = array(
				'reginput' => $_G['setting']['reginput'],
			);
			mobile_core::result(mobile_core::variable($variable));
		}
	}

	function output() {
		global $_G;
		
		$variable = array();
		mobile_core::result(mobile_core::variable($variable));
	}

}