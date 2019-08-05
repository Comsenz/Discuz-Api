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
		$m = isset($_GET['m']) ? trim($_GET['m']) : '';
		if($m === 'grouptype') {
			$variable['grouptype'] = $_G['cache']['grouptype'];
		}

		if(isset($_GET['createsubmit'])) {
			$_G['forum'] = is_array($_G['forum']) ? $_G['forum'] : array();
			$_G['forum']['fid'] = $GLOBALS['newfid'];
		} elseif($_GET['op'] == 'checkuser') {
			$uids = array();
			foreach($GLOBALS['checkusers'] as $key => $value) {
				$value['avatar'] = avatar($value['uid'], 'middle', true);
				$uids[] = $value['uid'];
				$GLOBALS['checkusers'][$key] = $value;
			}

			$usernicknames = array();
            foreach(C::t('common_member_profile')->fetch_all($uids) as $user) {
                $usernicknames[$user['uid']] = $user['realname'];
			}
			$variable['checkusers'] = $GLOBALS['checkusers'];
			$variable['usernicknames'] = $usernicknames;
		}
			

		$_G['forum']['icon'] = $_G['siteurl'].$_G['forum']['icon'];

		$variable['forum'] = mobile_core::getvalues($_G['forum'], array('fid', 'fup', 'name', 'description', 'icon', 'jointype', 'gviewperm'));

		mobile_core::result(mobile_core::variable($variable));
	}


}

?>