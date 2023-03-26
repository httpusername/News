<?php
namespace Common;
if(!defined('SITE')) exit('Access Denied');
class ControllerFactory extends Factory{
    static function create($type=null){
        'Admin\Controller\IndexController';
        $controller='\\'.MODULE.'\\Controller\\'.Url::getC(true);
        return parent::create($controller);
    }
}