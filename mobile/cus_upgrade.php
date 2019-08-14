<?php

/**
 *      [Discuz!] (C)2001-2099 Comsenz Inc.
 *      This is NOT a freeware, use is subject to license terms
 *
 *      $Id: cus_upgrade.php 34692 2014-07-09 01:17:48Z qingrongfu $
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

$sql = '';

$sql .= <<<EOF

CREATE TABLE IF NOT EXISTS pre_mobile_oauths (
    `uid` int(11) unsigned NOT NULL,
    `openid` varchar(255) NOT NULL DEFAULT '',
    `status` tinyint(1) NOT NULL DEFAULT '0',
    `type` varchar(255) NOT NULL DEFAULT '',
    KEY `uid` (`uid`),
    KEY `openid` (`openid`)
  ) ENGINE=MyISAM;

CREATE TABLE IF NOT EXISTS pre_mobile_unionids (
    `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
    `uid` int(11) unsigned NOT NULL,
    `unionid` varchar(255) NOT NULL DEFAULT '',
    PRIMARY KEY (`id`)
  ) ENGINE=MyISAM;
EOF;

runquery($sql);

$finish = true;