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
		if ($_GET['action'] == 'manage') {
			$temp = $_GET;
			$formhash = isset($_POST['formhash']) ? $_POST['formhash'] : '';
			if (isset($_POST['newname'])) {
				$_POST['groupthreadtype'] = 1;
				$temp['groupthreadtype'] = 1;
				$newname = isset($_POST['newname']) && is_array($_POST['newname']) ? $_POST['newname'] : array();
				$newenable = array();
				$newdisplayorder = array();
				$count = count($newname);
				if ($count > 0) {
					$newenable = array_fill(0, $count, 1);
					if (!empty($_POST['newdisplayorder'])) {
						foreach ($newname as $key_order => $value_order) {
						    if (isset($_POST['newdisplayorder'][$key_order])) {
						    	$newdisplayorder[$key_order] = $_POST['newdisplayorder'][$key_order];
						    } else {
						    	$newdisplayorder[$key_order] = 0;
						    }
						} 
					} else {
						$newdisplayorder = array_fill(0, $count, 0);
					}
					
				}
				$temp['newenable'] = $newenable;
				$temp['newdisplayorder'] = $newdisplayorder;
				$temp['newname'] = $newname;
			}
			$enable = isset($_POST['enable']) && is_array($_POST['enable']) ? $_POST['enable'] : '';
			$displayorder = isset($_POST['displayorder']) && is_array($_POST['displayorder']) ? $_POST['displayorder'] : '';
			$name = isset($_POST['name']) && is_array($_POST['name']) ? $_POST['name'] : '';
			$delete = isset($_POST['delete']) && is_array($_POST['delete']) ? $_POST['delete'] : '';

			if (!empty($enable) || !empty($displayorder) || !empty($name) || !empty($delete)) {
				$temp['groupthreadtype'] = 1;
				$temp['threadtypesnew'] = array();
			    $temp['threadtypesnew']['status'] = 1;
				$temp['threadtypesnew']['options'] = array(); 
			}
			// if (!empty($enable)) {
			// 	$temp['threadtypesnew']['options']['enable'] = $enable;
			// }
			// if (!empty($displayorder)) {
			// 	$temp['threadtypesnew']['options']['displayorder'] = $displayorder;
			// }
			if (!empty($name)) {
				$temp['threadtypesnew']['options']['name'] = $name;
				$temp['threadtypesnew']['options']['enable'] = array();
				$temp['threadtypesnew']['options']['displayorder'] = array();
				foreach ($name as $key_name => $value_name) {
					 	if (isset($_POST['displayorder'][$key_name])) {
					 		$temp['threadtypesnew']['options']['displayorder'][$key_name] = $_POST['displayorder'][$key_name];
					 	} else {
					 		$temp['threadtypesnew']['options']['displayorder'][$key_name] = 0;
					 	}
					 	$temp['threadtypesnew']['options']['enable'][$key_name] = 1;
				}
			}
			if (!empty($delete)) {
				$temp['threadtypesnew']['options']['delete'] = $delete;
			}
			$_GET = $temp;
		}
	}

	function output() {
		global $_G;
    	$variable = array();
		$threadtypes = $checkeds = array();
		if(empty($_G['forum']['threadtypes'])) {
			$checkeds['status'][0] = 'checked';
			$display = 'none';
		} else {
			$display = '';
			$_G['forum']['threadtypes']['status'] = 1;
			foreach($_G['forum']['threadtypes'] as $key => $val) {
				$val = intval($val);
				$checkeds[$key][$val] = 'checked';
			}
		}
		foreach(C::t('forum_threadclass')->fetch_all_by_fid($_G['fid']) as $type) {
			$type['enablechecked'] = isset($_G['forum']['threadtypes']['types'][$type['typeid']]) ? ' checked="checked"' : '';
			$type['name'] = dhtmlspecialchars($type['name']);
			$threadtypes[] = $type;
		}
		$variable['threadtypes'] = $threadtypes;
		mobile_core::result(mobile_core::variable($variable));
	}


}

?>