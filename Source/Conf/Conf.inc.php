<?php
//令牌
if(!SITE) {exit('Access Denied');}
//整个项目所在操作系统的绝对路径
define('PATH_APP', dirname(dirname(dirname(__FILE__))));
//项目核心程序在操作系统的绝对路径
define('PATH_SOURCE',PATH_APP.'/'.'Source');
//公共类在操作系统中的绝对路径
define('PATH_COMMON',PATH_SOURCE.'/Common');
//当前运行的模块的在操作系统的绝对路径
define('PATH_MODULE',PATH_SOURCE.'/'.MODULE);
//当前View模块的在操作系统的绝对路径
define('PATH_VIEW',PATH_MODULE.'/View');

//当前Default模块的在操作系统的绝对路径
defined('PATH_VIEW_SKIN')?null:define('PATH_VIEW_SKIN',PATH_VIEW.'/Default');
//控制器名称
define('INDEX_CONTROLLER','c');
//方法
define('INDEX_METHOD','m');
//0表示url普通模式，1表示PATH_INFO模式
defined('URL_MODE') ? null : define('URL_MODE',0);
//数据库连接地址
define('DB_HOST','localhost');
//数据库管理员
define('DB_USER','root');
//数据库密码
define('DB_PASSWORD',123456);
//数据库名
define('DB_DATABASE','demo');
//数据库端口
define('DB_PROT','3306');
//字符集编码
define('DB_CHARSET','utf-8');
//表前缀
define('DB_PREFIX','');
