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


require './source/class/class_core.php';
require_once libfile('function/member');

$discuz = C::app();
$discuz->init();

$_G['config'] = array_merge($_G['config'], require './source/plugin/mobile/config_minapp.php');

$appid = $_G['config']['minapp']['appid'];
$secret = $_G['config']['minapp']['secret'];
$code = isset($_GET['code']) ? trim($_GET['code']) : '';

if(!$code || !$appid || !$secret) {
    $result['code'] = -1;
    $result['message'] = 'param_error';
    outjson($result);
}

$wxApiurl = sprintf('https://api.weixin.qq.com/sns/jscode2session?appid=%s&secret=%s&js_code=%s&grant_type=authorization_code', $appid, $secret, $code);

$response = dfsockopen($wxApiurl);
$wxinfo = json_decode($response);
if(isset($wxinfo->errcode)) {
    $result['code'] = -2;
    $result['message'] = 'weixin_api_error';
    outjson($result);
}


$userinfo = C::t('#mobile#weixin_minapp_user')->fetch_by_openid($wxinfo->openid);
if(!empty($userinfo)) {
    C::t('#mobile#weixin_minapp_user')->update($openid, array('session_key' => $wxinfo->session_key));
    $member = getuserbyuid($userinfo['uid']);
    setloginstatus($member, 1296000);
} else {
    // $username = 'minapp_'.strtolower(random(8));
    // $uid = register($username);

    $result['code'] = -3;
    $result['openid'] = $wxinfo->openid;
    $result['message'] = 'no_user';
    outjson($result);
    

    // C::t('#mobile#weixin_minapp_user')->insert(array('uid' => $uid, 'openid' => $wxinfo->openid, 'session_key' => $wxinfo->session_key, 'unionid' => $wxinfo->unionid));

    // if(C::memory()->enable && strtolower(C::memory()->type) === 'redis') {

    //     $redis = new memory_driver_redis();
    //     $redis = $redis->instance();

    //     $data = json_encode(array(
    //         'type' => 'newuser',
    //         'username' => $username,
    //         'uid' => $uid
    //     ));
    //     $redis->obj->publish('sitefeed', $data);
    // }

}

$result['code'] = 0;
$result['data'] = array(
    'auth' => $_G['cookie']['auth'],
    'saltkey' => $_G['cookie']['saltkey'],
    'formhash' => FORMHASH,
    'uid' => $_G['member']['uid']
);
$result['message'] = 'login_success';
outjson($result);


function outjson($result) {
    echo json_encode($result);
    exit;
}

function register($username, $groupid = 0) {
    global $_G;
    if(!$username) {
        return false;
    }

    loaducenter();
    $groupid = !$groupid ? $_G['setting']['newusergroupid'] : $groupid;

    $password = md5(random(10));
    $email = 'minapp_'.strtolower(random(10)).'@null.null';
    $uid = uc_user_register(addslashes($username), $password, $email, '', '', $_G['clientip']);
    if($uid <= 0) {
        if($uid == -3) {
            $username = 'minapp_'.strtolower(random(5));
            register($username);
        }
        return false;
    }

    $init_arr = array('credits' => explode(',', $_G['setting']['initcredits']));
    C::t('common_member')->insert($uid, $username, $password, $email, $_G['clientip'], $groupid, $init_arr);

    if($_G['setting']['regctrl'] || $_G['setting']['regfloodctrl']) {
        C::t('common_regip')->delete_by_dateline($_G['timestamp']-($_G['setting']['regctrl'] > 72 ? $_G['setting']['regctrl'] : 72)*3600);
        if($_G['setting']['regctrl']) {
            C::t('common_regip')->insert(array('ip' => $_G['clientip'], 'count' => -1, 'dateline' => $_G['timestamp']));
        }
    }

    if($_G['setting']['regverify'] == 2) {
        C::t('common_member_validate')->insert(array(
            'uid' => $uid,
            'submitdate' => $_G['timestamp'],
            'moddate' => 0,
            'admin' => '',
            'submittimes' => 1,
            'status' => 0,
            'message' => '',
            'remark' => '',
        ), false, true);
        manage_addnotify('verifyuser');
    }

    setloginstatus(array(
        'uid' => $uid,
        'username' => $username,
        'password' => $password,
        'groupid' => $groupid,
    ), 1296000);

    include_once libfile('function/stat');
    updatestat('register');


    require_once libfile('cache/userstats', 'function');
    build_cache_userstats();
    return $uid;
}
?>