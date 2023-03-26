<?php
namespace Common;
if(!defined('SITE')) exit('Access Denied');
class Factory {
    static function create($type){
        if(class_exists($type)){
            return new $type();
        }else{
            throw new \Exception("{$type} 创建失败.");
        }
    }
}