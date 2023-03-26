<?php

namespace Common;

if(!SITE) {exit('Access Denied');}
class Url
{
    //控制器
    static private $controller;
    //方法
    static private $method;
    static private function init()
    {
        switch (URL_MODE)
        {
            case 0:
                //设置0匹配admin?c=index&m=test
                self::parseUrl();
                break;
            case 1;
                //设置1匹配index/test
                if(isset($_SERVER['PATH_INFO']))
                {
                    self::parsePathInfo();
                }
                break;
            self::parseUrl();
        }
    }
    static private function parsePathInfo()
    {
        //设置1匹配index/test
        preg_match_all('/([^\/]+)\/([^\/]+)/',$_SERVER['PATH_INFO'],$data);
        if(count($data[0]))
        {
            //$data[0]= array(3) {
            // [0]=>
            //    string(10) "index/test"
            //[1]=>
            //    string(5) "id/10"
            //[2]=>
            //    string(7) "page/12"
            //  }
            //$data[0] index/test

            foreach ($data[0] as $key=>$val)
            {
                //0=>index,1=>test
                $tmp=explode('/',$val);
                if($key==0)
                {
                    $_GET[INDEX_CONTROLLER]=$tmp[0];
                    $_GET[INDEX_METHOD]=$tmp[1];


                }else{
                    //id=>10  page=>12
                    $_GET[$tmp[0]]=$tmp[1];
                }
            }

        } else {
            //index
            //0=>'',1=>index
            $tmp=explode('/',$_SERVER['PATH_INFO']);
            if(isset($tmp[1]))
            {
                $_GET[INDEX_CONTROLLER]=$tmp[1];
            }
        }
        self::$controller=ucfirst($_GET[\INDEX_CONTROLLER]);
        if($_GET[INDEX_METHOD]){
            self::$method=$_GET[\INDEX_METHOD];
        }else{
            self::$method='index';
        }
    }
    //解析url
    static private function parseUrl(){
        //?c=type&m=index 判断是否传参
        if(!isset($_GET[INDEX_CONTROLLER]) ||$_GET[INDEX_CONTROLLER]=='')
        {
            $_GET[INDEX_CONTROLLER]=ucfirst(index);
        }
        if(!isset($_GET[INDEX_METHOD]) ||$_GET[INDEX_METHOD]=='')
        {
            $_GET[INDEX_METHOD]='index';
        }
        //复制给变量
        self::$controller=ucfirst($_GET[INDEX_CONTROLLER]);
        self::$method=$_GET[INDEX_METHOD];
    }
    //获取$controller私有属性
    static public function getC($complete=false)
    {
        //初始化parseUrl方法
        if(!self::$controller)
        {
            self::init();
        }
        if($complete)
        {
            return self::$controller.'Controller';
        }else{
            return self::$controller;
        }
    }
    //获取$method私有属性
    static public function getMethod(){
        //初始化parseUrl方法
        if(!self::$method)
        {
            self::init();
        }
        return self::$method;
    }
}