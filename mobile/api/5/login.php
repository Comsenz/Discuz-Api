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
		global $_G;
		
		if(!submitcheck('loginsubmit', 1, $seccodestatus)) {
			mobile_api::oauths();
		}
	}

	function output() {
		global $_G;

		if(submitcheck('loginsubmit', 1)) {
			$openid = isset($_POST['openid']) ? $_POST['openid'] : '';
			$unionid = isset($_POST['unionid']) ? $_POST['unionid'] : '';
			$type = isset($_GET['type']) ? $_GET['type'] : '';

			C::t('#mobile#mobile_oauths')->update_uid_status($_G['uid'], $openid, 1, $type);

			if($unionid) {
				C::t('#mobile#mobile_unionids')->update_uid_by_unionid($_G['uid'], $unionid);
			}
		}

		$variable = array();
		mobile_core::result(mobile_core::variable($variable));
	}

	function oauths() {
		global $_G;

		$type = isset($_GET['type']) ? $_GET['type'] : '';

		if(in_array($type, array('minapp', 'weixin', 'qq'))) {
			mobile_api::{$type.'login'}();
		}
	}

	function weixinlogin() {
		global $_G;

		$openid = isset($_GET['openid']) ? $_GET['openid'] : '';
		$unionid = isset($_GET['unionid']) ? $_GET['unionid'] : '';

		if(!$openid) {
			$_G['messageparam'][0] = 'param_error';
			$variable = array();
			mobile_core::result(mobile_core::variable($variable));
		}

		mobile_api::_outputdata($openid, $unionid, $_GET['type']);
		
	}

	function minapplogin() {
		global $_G;

		$_G['config'] = array_merge($_G['config'], require './source/plugin/mobile/config_oauths.php');

		$appid = $_G['config']['minapp']['appid'];
		$secret = $_G['config']['minapp']['secret'];
		$code = isset($_GET['code']) ? trim($_GET['code']) : '';

		
		if(!$code || !$appid || !$secret) {
			$_G['messageparam'][0] = 'param_error';
		} 

		$wxinfo = mobile_api::_code2session($appid, $secret, $code);
			
		if(isset($wxinfo->errcode)) {
			$_G['messageparam'][0] = 'weixin_api_error';
			$variable = array();
			mobile_core::result(mobile_core::variable($variable));
		}

		mobile_api::_outputdata($wxinfo->openid, $wxinfo->unionid, $_GET['type']);
	}

	function qqlogin() {
		global $_G;

		$openid = isset($_GET['openid']) ? $_GET['openid'] : '';
		$unionid = isset($_GET['unionid']) ? $_GET['unionid'] : '';

		if(!$openid) {
			$_G['messageparam'][0] = 'param_error';
			$variable = array();
			mobile_core::result(mobile_core::variable($variable));
		}

		mobile_api::_outputdata($openid, $unionid, $_GET['type']);
	}


	function _code2session($appid, $secret, $code) {
		$wxApiurl = sprintf('https://api.weixin.qq.com/sns/jscode2session?appid=%s&secret=%s&js_code=%s&grant_type=authorization_code', $appid, $secret, $code);

		$response = dfsockopen($wxApiurl);
		return json_decode($response);
	}

	function _outputdata($openid, $unionid, $type) {
		global $_G;

		$variable = $userinfo = array();

		if($unionid) {
			$unioninfo = C::t('#mobile#mobile_unionids')->fetch_by_unionid($unionid);
			if(!empty($unioninfo)) {
				$userinfo = C::t('#mobile#mobile_oauths')->fetch_by_uid_type($unioninfo['uid'], $type);

				if(empty($userinfo)) {
					$userinfo = array(
						'uid' => $unioninfo['uid'],
						'openid' => $openid,
						'status' => 1,
						'type' => $type
					);

					C::t('#mobile#mobile_oauths')->insert($userinfo);
				}
			}
		} else {
			$userinfo = C::t('#mobile#mobile_oauths')->fetch_by_openid_type($openid, $type);
		}

		if(!empty($userinfo)) {
			if($userinfo['uid'] && $userinfo['status']) {
				$member = getuserbyuid($userinfo['uid']);
				setloginstatus($member, 1296000);

				$_G['messageparam'][0] = 'login_succeed';
			} else {
				if($type === 'minapp') {
					$variable['openid'] = $openid;
					$variable['unionid'] = $unionid;
				}
				$_G['messageparam'][0] = 'no_bind';
			}
			mobile_core::result(mobile_core::variable($variable));
		}

		C::t('#mobile#mobile_oauths')->insert(array('openid' => $openid, 'type' => $type));

		if($unionid) {
			C::t('#mobile#mobile_unionids')->insert(array('unionid' => $unionid));
		}

		$_G['messageparam'][0] = 'no_bind';
		mobile_core::result(mobile_core::variable($variable));
	}

}

?>