## 使用方法

- 修改配置文件，config_minapp.php， 将申请好的小程序 appkey appsecret 修改好
- 增加新表，SQL如下
- 把mobile目录覆盖discuz source/plugin/mobile目录
- 修改小程序配置文件，将域名换成您网站域名
- 上传小程序测试
- 该接口也适用于App调用

## 增加小程序登录绑定表
```
CREATE TABLE `pre_weixin_minapp_user` (
  `uid` int(11) unsigned NOT NULL,
  `openid` char(35) NOT NULL DEFAULT '',
  `status` tinyint(1) NOT NULL DEFAULT '0',
  `session_key` char(255) NOT NULL DEFAULT '',
  `unionid` char(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`openid`),
  KEY `uid` (`uid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
```