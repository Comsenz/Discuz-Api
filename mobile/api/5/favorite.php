<?php

/**
 *      [Discuz!] (C)2001-2099 Comsenz Inc.
 *      This is NOT a freeware, use is subject to license terms
 *
 *      $Id: favorite.php 34314 2014-02-20 01:04:24Z nemohou $
 */

if(!defined('IN_MOBILE_API')) {
        exit('Access Denied');
}

$_GET['mod'] = 'spacecp';
$_GET['ac'] = 'favorite';
include_once 'home.php';

class mobile_api {

        function common() {
            global $_G;
            //删除收藏
            if($_GET['op'] == 'delete') {
                if($_GET['type'] == 'thread') {
                    $_GET['type'] = 'tid';
                } elseif($_GET['type'] == 'forum') {
                    $_GET['type'] = 'fid';
                }

                $fav = C::t('home_favorite')->fetch_by_id_idtype(intval($_GET['id']), $_GET['type'], $_G['uid']);
                unset($_GET['id'], $_GET['type']);
                $_GET['favid'] = $fav['favid'];
            }
        }

        function output() {
            $variable = array();
            mobile_core::result(mobile_core::variable($variable));
        }

}

?>