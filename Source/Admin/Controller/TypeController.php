<?php

namespace Admin\Controller;

use Admin\Model\TypeModel;
use Common\Controller;

if(!defined('SITE')) exit('Access Denied');
class TypeController extends Controller {
    public function __construct()
    {
        $this->model = new TypeModel();
        parent::__construct();
    }

    public function index(){
        var_dump('index...');
    }

}