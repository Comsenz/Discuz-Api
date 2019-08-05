<?php

/**
 *      [Discuz!] (C)2001-2099 Comsenz Inc.
 *      This is NOT a freeware, use is subject to license terms
 *
 *      $Id: check.php 36332 2016-12-30 01:44:19Z nemohou $
 */

if(!defined('IN_MOBILE_API')) {
	exit('Access Denied');
}

require './source/class/class_core.php';

$discuz = C::app();
$discuz->init();

if(!defined('DISCUZ_VERSION')) {
	require './source/discuz_version.php';
}

if(in_array('mobile', $_G['setting']['plugins']['available'])) {
	loadcache('api_checkinfo');
	if (!$_G['cache']['api_checkinfo'] || TIMESTAMP - $_G['cache']['api_checkinfo']['expiration'] > 600) {
		$_G['wechat']['setting'] = unserialize($_G['setting']['mobilewechat']);
		$forums = C::t('forum_forum')->fetch_all_for_grouplist('', array(), 0);
		$countforums = $posts = $threads = 0;
		foreach ($forums as $forum) {
			$posts += $forum['posts'];
			$threads += $forum['threads'];
			$countforums++;
		}
		loadcache('userstats');
		$array = array(
			'discuzversion' => DISCUZ_VERSION,
			'charset' => CHARSET,
			'version' => MOBILE_PLUGIN_VERSION,
			'pluginversion' => $_G['setting']['plugins']['version']['mobile'],
			'regname' => $_G['setting']['regname'],
			'sitename' => $_G['setting']['bbname'],
			'ucenterurl' => $_G['setting']['ucenterurl'],
			'totalthreads' => $threads,
			'totalposts' => $posts,
			'totalforums' => $countforums,
			'totalmembers' => $_G['cache']['userstats']['totalmembers'],
		);
		savecache('api_checkinfo', array('variable' => $array, 'expiration' => TIMESTAMP));
	} else {
		$array = $_G['cache']['api_checkinfo']['variable'];
	}
} else {
    $array = array();
}

$array['formhash'] = formhash();
$array['setting'] = array(
	'repliesrank' => $_G['setting']['repliesrank'],
	'allowpostcomment' => $_G['setting']['allowpostcomment'],
);

$data = mobile_core::json($array);
mobile_core::make_cors($_SERVER['REQUEST_METHOD'], REQUEST_METHOD_DOMAIN);

echo $data;

?>