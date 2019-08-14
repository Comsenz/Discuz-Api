<?php

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

if($uid && $_GET['session_key']) {
	C::t('#mobile#mobile_oauths')->update_uid_status($uid, 1, $_GET['session_key']);
}