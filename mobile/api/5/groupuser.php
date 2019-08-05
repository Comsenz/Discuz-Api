<?php

/**
 *      [Discuz!] (C)2001-2099 Comsenz Inc.
 *      This is NOT a freeware, use is subject to license terms
 *
 *      $Id: forumupload.php 35181 2015-01-08 01:51:31Z nemohou $
 */
if (!defined('IN_MOBILE_API')) {
	exit('Access Denied');
}

$_GET['mod'] = 'group';
include_once 'forum.php';

class mobile_api {

	function common() {
	}

	function output() {
		global $_G;
        $variable = array();
        
        $nickname = isset($_POST['nickname']) && $_POST['nickname'] ? trim($_POST['nickname']) : '';
        $mobile = isset($_POST['mobile']) && $_POST['mobile'] ? trim($_POST['mobile']) : '';

        $fid = intval($_GET['fid']);
        if ($_GET['op'] == 'manageuser') {
 			$uid = intval($_GET['uid']);
 			if (empty($uid)) {
 				$_G['messageparam'] = array('update_userinfo_paramerror');
        		mobile_core::result(mobile_core::variable($variable));
 			}
        	$groupuserinfo = C::t('forum_groupuser')->fetch_userinfo($_G['uid'], $fid);
        	$level = isset($groupuserinfo['level']) ? $groupuserinfo['level'] : '';
        	if($level == 1 || $level == 2) { //管理员
        		C::t('forum_groupuser')->update_for_user($uid, $fid, null, null, null, $nickname, $mobile);
        	} else {
        		$_G['messageparam'] = array('update_userinfo_righterror');
        		mobile_core::result(mobile_core::variable($variable));
        	}
        }

        if(submitcheck('groupusersubmit')) {
            C::t('forum_groupuser')->update_for_user($_G['uid'], $fid, null, null, null, $nickname, $mobile);
        }

        $_G['messageparam'] = array('update_userinfo_success');
		
		mobile_core::result(mobile_core::variable($variable));
	}


}

?>