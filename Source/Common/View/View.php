<?php
namespace Common;
if(!SITE) {exit('Access Denied');}
class View {
    private $data;
    private $viewPathDefault;
    public function display($path=null){
        if($path==null){
            $path=$this->getViewPath();
        }
        if(file_exists($path)){
            include_once $path;
        }else{
            echo "{$path} 不存在.";
        }
    }
    public function inc($path=null){
        //当前相对路劲，绝对路径，common
        $pathArr=array(
            $path,
            PATH_VIEW_SKIN.'/'.Url::getC().'/'.$path.'.view.php',
            PATH_VIEW_SKIN.'/Common'.'/'.$path.'.view.php',
            null
        );
        foreach ($pathArr as $val){
            if(file_exists($val)){
                include $val;
                break;
            }
            if($val==null){
                echo "{$path} 不存在.";
            }
        }

    }
    public function setData($name,$val){
        $this->data[$name]=$name;
    }
    public function getData($name=null){
        if($name=null){
            return $this->data;
        }else{
            return $this->data[$name];
        }
    }
    private function getViewPath(){
        if(!isset($this->viewPathDefault)){
            $this->viewPathDefault=\PATH_VIEW_SKIN.'/'.Url::getC().'/'.Url::getMethod().'.view.php';
        }
        return $this->viewPathDefault;
    }
}