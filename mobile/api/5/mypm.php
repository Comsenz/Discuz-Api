<?php

/**
 *      [Discuz!] (C)2001-2099 Comsenz Inc.
 *      This is NOT a freeware, use is subject to license terms
 *
 *      $Id: mypm.php 35183 2015-01-14 07:46:53Z nemohou $
 */

if(!defined('IN_MOBILE_API')) {
	exit('Access Denied');
}

$_GET['mod'] = 'space';
$_GET['do'] = 'pm';
include_once 'home.php';

class mobile_api {

	function common() {
	}

	function output() {
		global $_G;
		foreach($GLOBALS['list'] as $_k => $_v) {
			if($_v['lastdateline']) {
				$GLOBALS['list'][$_k]['vdateline'] = dgmdate($_v['lastdateline'], 'u');
			} elseif($_v['dateline']) {
				$GLOBALS['list'][$_k]['vdateline'] = dgmdate($_v['dateline'], 'u');
			}

			//新加客户端表情解析
			$smiley = in_array($_GET['smiley'], array('yes', 'no')) ? $_GET['smiley'] : 'yes';
			if($smiley == 'no') {
				$GLOBALS['list'][$_k]['message'] = mobile_core::smiley($GLOBALS['list'][$_k]['message']);
			}

			if($_GET['mapifrom'] == 'ios') {
					$GLOBALS['list'][$_k]['fromavatar'] = avatar($_v['msgfromid'], 'small', true);
					$GLOBALS['list'][$_k]['toavatar'] = avatar($_v['touid'], 'small', true);
					$GLOBALS['list'][$_k]['message'] = preg_replace('/<([a-zA-Z]+)[^>]*>/', '', $GLOBALS['list'][$_k]['message']);
					$GLOBALS['list'][$_k]['message'] = preg_replace('/<\/([a-zA-Z]+)>/', '', $GLOBALS['list'][$_k]['message']);
			} elseif($_GET['mapifrom'] == 'android') {
					$GLOBALS['list'][$_k]['fromavatar'] = avatar($_v['msgfromid'], 'small', true);
					$GLOBALS['list'][$_k]['toavatar'] = avatar($_v['touid'], 'small', true);
			}
		}
		$variable = array(
			'list' => mobile_core::getvalues($GLOBALS['list'], array('/^\d+$/'), array('plid', 'isnew', 'vdateline', 'subject', 'pmid', 'msgfromid', 'msgfrom', 'message', 'touid', 'tousername', 'fromavatar', 'toavatar', 'id', 'authorid', 'author', 'dateline', 'numbers')),
			'count' => $GLOBALS['count'],
			'perpage' => $GLOBALS['perpage'],
			'page' => intval($GLOBALS['page']),
		);
		if($_GET['subop']) {
			$variable = array_merge($variable, array('pmid' => $GLOBALS['pmid']));
		}
		mobile_core::result(mobile_core::variable($variable));
	}

}

?>