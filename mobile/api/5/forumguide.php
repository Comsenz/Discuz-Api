<?php

/**
 *      [Discuz!] (C)2001-2099 Comsenz Inc.
 *      This is NOT a freeware, use is subject to license terms
 *
 *      $Id: hotthread.php 34314 2014-02-20 01:04:24Z nemohou $
 */

if(!defined('IN_MOBILE_API')) {
	exit('Access Denied');
}

$_GET['mod'] = 'guide';
$_GET['view'] = isset($_GET['view']) ? $_GET['view'] : 'hot';
include_once 'forum.php';

class mobile_api {

	function common() {
	}

	function output() {
		global $_G;

		foreach($GLOBALS['data'][$_GET['view']]['threadlist'] as $tid=>$thread) {


			//获取message和附件
			$post = array_pop(C::t('forum_post')->fetch_all_by_tid('tid:'.$thread['tid'], $thread['tid'], true, '', 0, 0, 1));
			$attachlist = C::t('forum_attachment_n')->fetch_all_by_id('tid:'.$thread['tid'], 'pid', $post['pid']);

			foreach($attachlist as $aid => $attach) {

				if($attach['remote']) {
					$attach['attachment'] = $_G['setting']['ftp']['attachurl'].'forum/'.$attach['attachment'];
				} else {
					$attach['attachment'] = strpos($_G['setting']['attachurl'], 'http') !== false ? $_G['setting']['attachurl'].'forum/'.$attach['attachment'] : $_G['siteurl'].$_G['setting']['attachurl'].'forum/'.$attach['attachment'];
				}

				$attach['thumb'] = mobile_core::thumb($attach['aid'], '0', '268', '380');
				$type = '';
				$fileext = addslashes(strtolower(substr(strrchr($attach['filename'], '.'), 1, 10)));
				if(in_array($fileext, array('jpg', 'jpeg', 'gif', 'png', 'bmp'))) {
					$type = 'image';
				} elseif($fileext === 'mp3') {
					$type = 'audio';
				} elseif($fileext === 'mp4') {
					$type = 'video';
				}
				$attach['type'] = $type;
				$attachlist[$aid] = $attach;
			}

			$GLOBALS['data'][$_GET['view']]['threadlist'][$tid]['attachlist'] = array_values($attachlist);

			$GLOBALS['data'][$_GET['view']]['threadlist'][$tid]['avatar'] = avatar($thread['authorid'], 'middle', true);
		}

		$GLOBALS['data'][$_GET['view']]['threadlist'] = $GLOBALS['data'][$_GET['view']]['threadlist'] ? $GLOBALS['data'][$_GET['view']]['threadlist'] : array();

		$variable = array(
			'data' => array_values($GLOBALS['data'][$_GET['view']]['threadlist']),
			'perpage' => $GLOBALS['perpage'],
		);
		mobile_core::result(mobile_core::variable($variable));
	}

}

?>