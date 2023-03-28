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

    public function add() {
        $data=array('name'=>'jack','pic'=>'uploads/1/2','puc'=>'uploads/1/2');
        $this->model->add($data);
    }
    public function update(){
        $data=array('id'=>100,'name'=>'孙胜利1','pic'=>'uploads/12120/1.pic');
        var_dump($this->model->update($data,1));
        var_dump($this->model->getError());
    }
    public function delete(){
//        var_dump($this->model->delete(1));
        var_dump($this->model->delete(array(1,2,3)));
        var_dump($this->model->getError());
    }
}