<?php
namespace Admin\Model;
use Common\Model;
if(!defined('SITE')) exit('Access Denied');
class TypeModel extends Model {
    public function __construct(){
        $this->tableName='test';
        /*
        数据是否可以不传	null
        数据类型	type
        数值的区间	between
        数值不得在某个区间	notbetween
        数据是否属于某个范围	in
        数据不得属于某个范围	notin
        数据长度的验证	length
        数据是否可以重复	unique
        数据是否符合我们所描述的正则表达式	regex
        数据是否符合某个自定义函数	function
        数据是否符合某个方法	method
        数据是否等于某个值	equal
        数据不得等于某个值	notequal
        两条数据是否一致	confirm
        */
        $this->validate=array(
            'id'=>array(
                'null'=>true,
                'type'=>'i',
                'between'=>'0,4294967295',
//                'notbetween'=>'20,30',
                'unique'=>true
            )
        );
        $this->vTime=array(
            'id'=>array(
//                'notbetween'=>array(self::INSERT,self::DELETE,self::UPDATE,self::SELECT),
//                'unique'=>array(self::DELETE,self::UPDATE,self::SELECT)
            )
        );
        $this->vMessage=array(
            'id'=>array(
                'type'=>'id必须是一个数字'
            ),
            'name'=>array(
                'in'=>'name必须在某个范围内'
            )
        );
        $this->vFieldsAlias=array(
            'name'=>'栏目名称'
        );
        parent::__construct();
    }
    public function test(){
        var_dump('test方法...');
        return false;
    }
}