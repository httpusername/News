<?php
if(!defined('SITE')) exit('Access Denied');
function loadAbs($className){
    $path=PATH_SOURCE.'/'.str_replace('\\','/',$className).'.php';
    if(file_exists($path)){
        require $path;
    }
}
function loadCommonChild($className){
    foreach (scandir(PATH_COMMON) as $val){
        if($val=='.' || $val=='..' || is_file(PATH_COMMON.'/'.$val)){
            continue;
        }

        $classAttr=explode('\\',$className);

        $class=$classAttr[count($classAttr)-1];

        $path=PATH_COMMON.'/'.$val.'/'.$class.'.php';
        if(file_exists($path)){
            include_once $path;
            return true;
        }
    }
}

function abc($a,$b,$c){
    var_dump($a);
    var_dump($b);
    var_dump($c);
}