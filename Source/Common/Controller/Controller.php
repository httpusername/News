<?php

namespace Common;
if(!defined('SITE')) exit('Access Denied');
class Controller
{
    protected $view;
    protected $model;
    public function __construct(){
        $this->view=new View();
    }
    public function __call($name,$args){
        var_dump($name);
        var_dump(':((');
    }
}