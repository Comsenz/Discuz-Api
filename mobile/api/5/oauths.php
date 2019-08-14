<?php

/**
 *      [Discuz!] (C)2001-2099 Comsenz Inc.
 *      This is NOT a freeware, use is subject to license terms
 *
 *      $Id: oauths.php 34314 2014-02-20 01:04:24Z nemohou $
 */

if(!defined('IN_MOBILE_API')) {
	exit('Access Denied');
}

$_GET['mod'] = 'logging';
$_GET['action'] = 'login';
include_once 'member.php';

class mobile_api {

	function common() {
        global $_G;
        $variable = $users = array();

        if(!$_G['uid']) {
            $_G['messageparam'][0] = 'no_login';
            mobile_core::result(mobile_core::variable($variable));
        }

        $op = isset($_GET['op']) ? $_GET['op'] : '';
        if($op === 'unbind') {
            if(submitcheck('unbind')) {
                $type = isset($_POST['type']) ? $_POST['type'] : '';
                $result = C::t('#mobile#mobile_oauths')->update_uid_type($_G['uid'], $type, 0);
                $_G['messageparam'][0] = 'unbind_succeed';
            }
        } else {
            $users = C::t('#mobile#mobile_oauths')->fetch_all_by_uid($_G['uid'], ' GROUP BY type');
            $types = array_diff(array('weixin', 'minapp', 'qq'), array_column($users, 'type'));
            foreach($types as $type) {
                $users[] = array(
                    'uid' => $_G['uid'],
                    'openid' => '',
                    'status' => '0',
                    'session_key' => '',
                    'type' => $type
                );
            }
            $variable['users'] = $users;
        }

		mobile_core::result(mobile_core::variable($variable));
	}

	function output() {
	}


}

?>