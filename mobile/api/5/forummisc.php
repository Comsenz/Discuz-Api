<?php

/**
 *      [Discuz!] (C)2001-2099 Comsenz Inc.
 *      This is NOT a freeware, use is subject to license terms
 *
 *      $Id: forummisc.php 35102 2014-11-18 10:09:27Z nemohou $
 */
if (!defined('IN_MOBILE_API')) {
    exit('Access Denied');
}

$_GET['mod'] = 'misc';
include_once 'forum.php';

class mobile_api {

    function common() {
    }

    function output() {
		global $_G;
		
		$action = isset($_GET['action']) ? trim($_GET['action']) : 'applylsit';
		$variable = $data = array();

		if($action == 'viewvote') {
			$data['polloptions'] = $GLOBALS['polloptions'];
			if(isset($_GET['polloptionid'])) {
				$data['voterlist'] = $GLOBALS['voterlist'];
				foreach($data['voterlist'] as $uid => $user) {
					$user['avatar'] = avatar($user['uid'], 'small', true);
					$data['voterlist'][$uid] = $user;
				}
				$data['voterlist'] = array_values($data['voterlist']);
				unset($data['polloptions']);
			}
		} elseif($action == 'activityapplylist') {
			if(!empty($GLOBALS['query'])) {
				foreach($GLOBALS['query'] as $key => $value) {
					$value['dateline'] = date('Y-m-d H:i', $value['dateline']);
					$dbufielddata = dunserialize($value['ufielddata']);
					if(!empty($dbufielddata['userfield'])) {
						foreach ($dbufielddata['userfield'] as $ukey => $uvalue) {
							$value['dbufielddata'][$ukey] = array('title' => $_G['cache']['profilesetting'][$ukey]['title'], 'value' => $uvalue);
						}
					}
	
					$value['ufielddata'] = $GLOBALS['applylist'][$key]['ufielddata'];
					$data['applylist'][] = $value;
				}
			}

			$data['activityinfo'] = array();
			$data['activityinfo']['subject'] = $GLOBALS['thread']['subject'];
			$data['activityinfo']['applynumber'] = $GLOBALS['activity']['applynumber'];
			$data['activityinfo']['number'] = $GLOBALS['activity']['number'];
			$data['activityinfo']['days'] = $GLOBALS['activity']['starttimeto'];
	
		}

		$variable[$action] = $data;
		
        mobile_core::result(mobile_core::variable($variable));
    }

}

?>
