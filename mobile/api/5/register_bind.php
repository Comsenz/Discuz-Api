<?php

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

if($uid && $_GET['openid']) {
	C::t('#mobile#weixin_minapp_user')->insert(array('uid' => $uid, 'openid' => $_GET['openid'], 'status' => 1), false, true);
}