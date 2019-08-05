<?php

/**
 *      [Discuz!] (C)2001-2099 Comsenz Inc.
 *      This is NOT a freeware, use is subject to license terms
 *
 *      $Id: newthread.php 35251 2015-04-09 06:05:51Z nemohou $
 */

if(!defined('IN_MOBILE_API')) {
	exit('Access Denied');
}

$_GET['mod'] = 'post';
$_GET['action'] = 'newthread';
include_once 'forum.php';

class mobile_api {

	function common() {
		if($_GET['mapifrom'] == 'ios' || $_GET['mapifrom'] == 'android') {
			$exp = '/#\[\\d{2}_\\d{2}\]/';
			preg_match_all($exp, $_POST['message'], $match);
			if($match) {
				foreach($match[0] as $value) {
					$typeid = intval(substr($value, 2, 2));
					$displayorder = intval(substr($value, 5, 2));
					$smiley = DB::fetch_first('SELECT code FROM ' . DB::table('common_smiley') . ' WHERE typeid=' . $typeid . ' AND displayorder=' . $displayorder);
					$_GET['message'] = str_replace($value, $smiley['code'], $_GET['message']);
					$_GET['noticetrimstr'] = str_replace($value, $smiley['code'], $_GET['noticetrimstr']);
				}
			}

			$exp = '/#\[\\d{2}_\\d{2}\]/';
			preg_match_all($exp, $_POST['noticetrimstr'], $nmatch);

			if($nmatch) {
				foreach($nmatch[0] as $value) {
					$typeid = intval(substr($value, 2, 2));
					$displayorder = intval(substr($value, 5, 2));
					$smiley = DB::fetch_first('SELECT code FROM ' . DB::table('common_smiley') . ' WHERE typeid=' . $typeid . ' AND displayorder=' . $displayorder);
					$_GET['noticetrimstr'] = str_replace($value, $smiley['code'], $_GET['noticetrimstr']);
				}
			}
		}
	}

	function post_mobile_message($message, $url_forward, $values, $extraparam, $custom) {
		if($values['tid'] && $values['pid']) {
			global $_G;

			$threadstatus = DB::result_first("SELECT status FROM ".DB::table('forum_thread')." WHERE tid='$values[tid]'");
			if(!empty($_POST['allowsound'])) {
				$setstatus = array(1, 0, 0);
			} elseif(!empty($_POST['allowphoto'])) {
				$setstatus = array(0, 1, 1);
			} elseif(!empty($_POST['allowlocal'])) {
				$setstatus = array(0, 1, 0);
			} else {
				$setstatus = array(0, 0, 1);
			}
			foreach($setstatus as $i => $bit) {
				$threadstatus = setstatus(13 - $i, $bit, $threadstatus);
			}
			C::t('forum_thread')->update($values['tid'], array('status' => $threadstatus));

			$poststatus = DB::result_first("SELECT status FROM ".DB::table('forum_post')." WHERE pid='$values[pid]'");
			$poststatus = setstatus(4, 1, $poststatus);
			if(!empty($_POST['allowlocal'])) {
				$poststatus = setstatus(6, 1, $poststatus);
			}
			if(!empty($_POST['allowsound'])) {
				$poststatus = setstatus(7, 1, $poststatus);
			}
			if(!empty($_POST['mobiletype'])) {
				$mobiletype = base_convert($_POST['mobiletype'], 10, 2);
				$mobiletype = sprintf('%03d', $mobiletype);
				for($i = 0;$i < 3;$i++) {
					$poststatus = setstatus(10 - $i, $mobiletype{$i}, $poststatus);
				}
			}
			C::t('forum_post')->update(0, $values['pid'], array('status' => $poststatus));

			if($_POST['location']) {
				list($mapx, $mapy, $location) = explode('|', dhtmlspecialchars($_POST['location']));
				C::t('forum_post_location')->insert(array(
					'pid' => $values['pid'],
					'tid' => $values['tid'],
					'uid' => $_G['uid'],
					'mapx' => $mapx,
					'mapy' => $mapy,
					'location' => $location,
				));
			}
		}
	}

	function output() {
		global $_G;
		$variable = array(
			'tid' => $GLOBALS['tid'],
			'pid' => $GLOBALS['pid'],
		);
		if(!empty($_G['forum']['threadtypes'])) {
			foreach($_G['forum']['threadtypes']['types'] as $typeid => $typename) {
				$_G['forum']['threadtypes']['types'][$typeid] = strip_tags($typename);
			}
			$variable['threadtypes'] = $_G['forum']['threadtypes'];
		}
		mobile_core::result(mobile_core::variable($variable));
	}

}

?>