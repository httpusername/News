<?php

use Common\ControllerFactory;
use Common\Url;

if(!defined('SITE')) exit('Access Denied');
include_once 'Conf.inc.php';
include_once 'Functions.inc.php';
spl_autoload_register('loadAbs');
spl_autoload_register('loadCommonChild');
try {
    $method=Url::getMethod();
    ControllerFactory::create()->$method();
}catch (Exception $e){
    exit($e->getMessage());
}
