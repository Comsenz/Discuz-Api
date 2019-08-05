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

include_once 'group.php';

class mobile_api {

	function common() {
		global $_G;

		require_once libfile('function/group');
		require_once libfile('function/forum');
		$variable = array();

		$page = max(1, intval($_GET['page']));
		$tpp = $_GET['tpp'] ? intval($_GET['tpp']) : 10;

		$start = ($page - 1) * $tpp;

		$fieldarray = array('description', 'icon', 'name', 'fup', 'jointype');
		$fids = self::_withfids();
		$sort = 0;
		$getcount = 0;
		$grouplevel = array();
		
		$num = array();
		if($_GET['mod'] != 'my') {
			$num = array($start, $tpp);
		}

		$grouplistcount = self::_grouplist('dateline', $fieldarray, 1, $fids, $sort, 1, $grouplevel);
		$grouplist = self::_grouplist('dateline', $fieldarray, $num, $fids, $sort, $getcount, $grouplevel);

		$grouplist = self::_gotuserlist($grouplist);

		if(isset($_GET['fid'])) {
			$fid = intval($_GET['fid']);
			loadforum();
			$variable['groupinfo'] = $grouplist[0];

			$count = C::t('forum_groupuser')->fetch_count_by_fid($fid, 1);
			$realnames = array();
            foreach(C::t('common_member_profile')->fetch_all($variable['groupinfo']['uids']) as $user) {
				$realnames[$user['uid']] = $user['realname'];
			}

			foreach($variable['groupinfo']['userlist'] as $uid => $user) {
				$user['realname'] = $realnames[$uid];
				$user['avatar'] = avatar($user['uid'], 'middle', true);
				$variable['groupinfo']['userlist'][$uid] = $user;
			}

			$founderinfo = C::t('common_member_profile')->fetch($_G['forum']['founderuid']);
			$variable['groupinfo']['founderuid'] = $_G['forum']['founderuid'];
			if($groupusernicknames[$_G['forum']['founderuid']]) {
				$variable['groupinfo']['foundername'] = $groupusernicknames[$_G['forum']['founderuid']];
			} elseif($founderinfo['realname']) {
				$variable['groupinfo']['foundername'] = $founderinfo['realname'];
			} else {
				$variable['groupinfo']['foundername'] = $_G['forum']['foundername'];
			}
			$variable['groupinfo']['founderavatar'] = avatar($_G['forum']['founderuid'], 'middle', true);
			$variable['groupinfo']['moduser'] = $count; 

			unset($variable['groupinfo']['uids']);
		} else {
			$variable['grouplist'] = $grouplist;
		}

		$variable['total'] = $grouplistcount;
		$variable['tpp'] = $tpp;
		mobile_core::result(mobile_core::variable($variable));
		
	}

	function output() {
	}


	function _gotuserlist($grouplist) {
		global $_G;
		if(!empty($grouplist)) {
			$fids = array();
			foreach($grouplist as $key => $value) {
				$fids[] = $value['fid'];
			}
			$groupusers = C::t('#mobile#forum_groupuser_ext')->fetch_all_by_fids($fids);
			foreach($grouplist as $key => $value) {
				$ingroup = 0;
				$value['iconnew'] = 1;
				$value['icons'] = array();
				if(strpos($value['icon'], 'groupicon.gif') !== false) {
					$value['iconnew'] = 0;
				}
				$value['userlist'] = $groupusers[$value['fid']];
				$value['users'] = count($groupusers[$value['fid']]);
				$index = 0;
				foreach($groupusers[$value['fid']] as $user) {
					if($index < 9 && $value['iconnew'] == 0) {
						$value['icons'][] = avatar($user['uid'], 'small', 1);
					}
					if($_G['uid'] == $user['uid']) {
						$ingroup = 1;
					}
					$value['uids'][] = $user['uid'];
				}
				$value['ingroup'] = $ingroup;
				$grouplist[$key] = $value;
			}
		}

		return $grouplist;
	}

	function _grouplist($orderby = 'dateline', $fieldarray = array(), $num = 1, $fids = array(), $sort = 0, $getcount = 0, $grouplevel = array()) {
		global $_G;
		if(isset($_GET['kw']) && empty($fids)) {
			return array();
		}

		$fups = self::_getfups();
		$query = C::t('#mobile#forum_forum_ext')->fetch_all_for_fup_grouplist($orderby, $fieldarray, $num, $fids, $sort, $getcount, $fups);
		if($getcount) {
			return $query;
		}
		
		$firstgroup = $_G['cache']['grouptype']['first'];
		$secondgroup = $_G['cache']['grouptype']['second'];

		$grouplist = array();
		foreach($query as $group) {
			$fup = $group['fup'];
			
			$secondid = $secondgroup[$fup]['fup'];
			$grouptype = array('first' => $firstgroup[$secondid], 'second' => $secondgroup[$fup]);

			$group['iconstatus'] = $group['icon'] ? 1 : 0;
			$group['grouptype'] = $grouptype;
			isset($group['icon']) && $group['icon'] = $_G['siteurl'].get_groupimg($group['icon'], 'icon');
			isset($group['banner']) && $group['banner'] = get_groupimg($group['banner']);
			$group['orderid'] = $orderid ? intval($orderid) : '';
			isset($group['dateline']) && $group['dateline'] = $group['dateline'] ? dgmdate($group['dateline'], 'd') : '';
			isset($group['lastupdate']) && $group['lastupdate'] = $group['lastupdate'] ? dgmdate($group['lastupdate'], 'd') : '';
			$group['level'] = !empty($grouplevel) ? intval($grouplevel[$group['fid']]) : 0;
			isset($group['description']) && $group['description'] = cutstr($group['description'], 130);
			$grouplist[] = $group;
			$orderid ++;
		}
	
		return $grouplist;
	}


	function _withfids() {
		global $_G;
		$fid = isset($_GET['fid']) ? intval($_GET['fid']) : 0;
		$mod = isset($_GET['mod']) ? trim($_GET['mod']) : '';
		$keyword = isset($_GET['kw']) ? rawurldecode(dhtmlspecialchars(trim($_GET['kw']))) : '';

		$fids = array();
		
		if($fid) {
			$fids = array($fid);
		} elseif($keyword) {
			require_once libfile('function/search');
			$keyword = str_replace('+', ' ', $keyword);
			list($srchtxt, $srchtxtsql) = searchkey($keyword, "name LIKE '%{text}%'", true);

			$fups = self::_getfups();
			$query = C::t('forum_forum')->fetch_all_search_group($fups, $srchtxtsql, 0, $_G['setting']['search']['group']['maxsearchresults']);

			foreach($query as $group) {
				$fids[] = $group['fid'];
			}

		} elseif($mod == 'my') {
			$fids = array(0);
			if($_G['uid']) {
				$usergroup = C::t('forum_groupuser')->fetch_all_group_for_user($_G['uid'], 0, 0, 0, 0);
				if($usergroup) {
					foreach($usergroup as $group) {
						if($group['level'] > 0) {
							$fids[] = $group['fid'];
						}
					}
				}
			}
		}

		return $fids;
	}

	function _getfups() {
		global $_G;
		$groupid = isset($_GET['groupid']) ? intval($_GET['groupid']) : 0;
		$forumid = isset($_GET['forumid']) ? intval($_GET['forumid']) : 0;
		$subid = isset($_GET['subid']) ? intval($_GET['subid']) : 0;
		
		$fups = array();
		
		if($groupid) {
			$sendlist = $_G['cache']['grouptype']['first'][$groupid]['secondlist'];
			foreach($sendlist as $k => $v) {
				foreach($_G['cache']['grouptype']['second'][$v]['threelist'] as $value) {
					$fups[] = $value;
				}
			}
		}

		if($forumid && in_array($forumid, $sendlist)) {
			$fups = array();
			foreach($_G['cache']['grouptype']['second'][$forumid]['threelist'] as $value) {
				$fups[] = $value;
			}
		}

		if($subid && in_array($subid, $_G['cache']['grouptype']['second'][$forumid]['threelist'])) {
			$fups = array();
			$fups[] = $subid;
		}

		if(empty($fups) && ($groupid || $forumid || $subid)) {
			$fups = array(0);
		}
		
		return $fups;
	}
}

?>