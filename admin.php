<?php
header('Content-type:text/html;charset=utf-8');
//令牌，防止被包含的文件被直接执行
define('SITE','sifangku.com');
define('MODULE','Admin');
define('URL_MODE',1);
include 'Source/Conf/Action.inc.php';