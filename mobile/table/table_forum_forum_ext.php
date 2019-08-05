<?php

/**
 *      [Discuz!] (C)2001-2099 Comsenz Inc.
 *      This is NOT a freeware, use is subject to license terms
 *
 *      $Id: table_forum_forum.php 36284 2016-12-12 00:47:50Z nemohou $
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class table_forum_forum_ext extends discuz_table
{
	public function __construct() {

		$this->_table = 'forum_forum';
		$this->_pk    = 'fid';
		$this->_pre_cache_key = 'forum_forum_';

		parent::__construct();
	}

	public function fetch_all_for_fup_grouplist($orderby = 'displayorder', $fieldarray = array(), $num = 1, $fids = array(), $sort = 0, $getcount = 0, $fups = array()) {
		if($fieldarray && is_array($fieldarray)) {
			$fieldadd = '';
			foreach($fieldarray as $field) {
				$fieldadd .= $field.', ';
			}
		} else {
			$fieldadd = 'ff.*, ';
		}
		$start = 0;
		if(is_array($num)) {
			list($start, $snum) = $num;
		} else {
			$snum = $num;
		}

		$addsql = '';
		if(!empty($fups)) {
			$addsql = 'AND '.DB::field('fup', $fups);
		}

		$orderbyarray = array('displayorder' => 'f.displayorder DESC', 'dateline' => 'ff.dateline DESC', 'lastupdate' => 'ff.lastupdate DESC', 'membernum' => 'ff.membernum DESC', 'thread' => 'f.threads DESC', 'activity' => 'f.commoncredits DESC');
		$useindex = $orderby == 'displayorder' ? 'USE INDEX(fup_type)' : '';
		$orderby = !empty($orderby) && $orderbyarray[$orderby] ? "ORDER BY ".$orderbyarray[$orderby] : '';
		$limitsql = $num ? "LIMIT $start, $snum " : '';
		$field = $sort ? 'fup' : 'fid';
		$fids = $fids && is_array($fids) ? 'f.'.$field.' IN ('.dimplode($fids).')' : '';
		if(empty($fids)) {
			 $levelsql = " AND f.level>'-1'";
		}

		$fieldsql = $fieldadd.' f.fid, f.name, f.threads, f.posts, f.todayposts, f.level as flevel ';
		if($getcount) {
			return DB::result_first("SELECT count(*) FROM ".DB::table($this->_table)." f $useindex WHERE".($fids ? " $fids AND " : '')." f.type='sub' " .$addsql. " AND f.status=3 $levelsql");
		}
		return DB::fetch_all("SELECT $fieldsql FROM ".DB::table($this->_table)." f $useindex LEFT JOIN ".DB::table("forum_forumfield")." ff ON ff.fid=f.fid WHERE".($fids ? " $fids AND " : '')." f.type='sub' " .$addsql. " AND f.status=3 $levelsql $orderby $limitsql");
	}

	public function fetch_all_search_group($fups, $conditions, $start = 0, $limit = 50) {
		if(!empty($conditions) && !is_string($conditions)) {
			return array();
		}
		$typesql = 'type=\'sub\'';
		$addsql = '';
		if(!empty($fups)) {
			$addsql = 'AND '.DB::field('fup', $fups);
		}
		return DB::fetch_all("SELECT fid FROM ".DB::table($this->_table)." WHERE status='3' ".$addsql." AND $typesql %i ".DB::limit($start, $limit), array($conditions));
	}
}

?>