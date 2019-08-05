<?php
/**
 *      [Discuz!] (C)2001-2099 Comsenz Inc.
*      This is NOT a freeware, use is subject to license terms
*
*      $Id: forumimage.php 32489 2013-01-29 03:57:16Z monkey $
*/

if(!defined('IN_MOBILE_API')) {
	exit('Access Denied');
}

include_once 'forum.php';

class mobile_api {
	function common() {
		global $_G;
		if(!defined('IN_DISCUZ') || empty($_GET['aid']) || empty($_GET['size']) || empty($_GET['key'])) {
			header('location: '.$_G['siteurl'].'static/image/common/none.gif');
			exit;
		}

		$allowsize = array('960x960', '268x380', '266x698', '2000x2000');
		if(!in_array($_GET['size'], $allowsize)) {
			header('location: '.$_G['siteurl'].'static/image/common/none.gif');
			exit;
		}

		$nocache = !empty($_GET['nocache']) ? 1 : 0;
		$daid = intval($_GET['aid']);
		$type = !empty($_GET['type']) ? $_GET['type'] : 'fixwr';
		list($w, $h) = explode('x', $_GET['size']);
		$dw = intval($w);
		$dh = intval($h);
		$thumbfile = 'image/'.helper_attach::makethumbpath($daid, $dw, $dh);$attachurl = helper_attach::attachpreurl();
		if(!$nocache) {
			if(file_exists($_G['setting']['attachdir'].$thumbfile)) {
				header('location: '.$attachurl.$thumbfile);
			}
		}

		define('NOROBOT', TRUE);

		$id = !empty($_GET['atid']) ? $_GET['atid'] : $daid;
		if(dsign($id.'|'.$dw.'|'.$dh) != $_GET['key']) {
			header('location: '.$_G['siteurl'].'static/image/common/none.gif');
		}

		if($attach = C::t('forum_attachment_n')->fetch('aid:'.$daid, $daid, array(1, -1))) {
			if(!$dw && !$dh && $attach['tid'] != $id) {
				header('location: '.$_G['siteurl'].'static/image/common/none.gif');
			}
			header("HTTP/1.1 301 Moved Permanently");
			header("Last-Modified:".date('r'));
			header('Expires: '.gmdate('D, d M Y H:i:s', TIMESTAMP + 3600).' GMT');
			if($attach['remote']) {
				$filename = $_G['setting']['ftp']['attachurl'].'forum/'.$attach['attachment'];
			} else {
				$filename = $_G['setting']['attachdir'].'forum/'.$attach['attachment'];
			}
			require_once libfile('class/image');
			$img = new image;
			if($img->Thumb($filename, $thumbfile, $w, $h, $type)) {
				if($nocache) {
					header('Content-Type: image');
					@readfile($_G['setting']['attachdir'].$thumbfile);
					@unlink($_G['setting']['attachdir'].$thumbfile);
				} else {
					header('Location: '.$attachurl.$thumbfile);
				}
			} else {
				header('Content-Type: image');
				@readfile($filename);
			}
		}
		exit;
	}
}
?>