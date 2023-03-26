<?php
namespace Common;
if(!defined('SITE')) exit('Access Denied');
class Model
{
    //数据库
    protected $db;
    //数据库配置信息
    protected $config;
    //表名
    protected $tableName;
    //表前缀
    protected $tableNamePerfix;
    //加上前缀的真实的表名
    protected $tureTableName;
    //字段
    protected $fields;
    //字段类型
    protected $filedsType;
    //主键
    protected $pk;
    public function __construct(){
        $this->db=new DB($this->parseConfig());
        var_dump($this->getFields());
    }
    //获取主键
    protected function getPK(){
        if(!isset($this->pk)) {
            $this->parseFileds();
        }
        return $this->pk;
    }
    //获取字段类型
    protected function getFiledsType($filed=null){
        if(!isset($this->filedsType)) {
            $this->parseFileds();
        }
        if($filed){
            return $this->filedsType[$filed];
        }else{
            return $this->filedsType;
        }
    }
    //获取字段
    protected function getFields(){
        if(!isset($this->fields)) {
            $this->parseFileds();
        }
        return $this->fields;
    }
    //解析字段、字段类型、主键
    protected function parseFileds() {
        $param=array(
            'sql'=>"show columns from {$this->getTableName()}"
        );
       $fields=$this->db->execute($param);
       foreach ($fields as $val){
           if($val['Key']=='PRI') $this->pk=$val['Field'];
           $this->fields[]=$val['Field'];
           if(strpos($val['Type'],'int')!==false){
               $this->filedsType[$val['Field']]='i';
           }else {
               $this->filedsType[$val['Field']]='s';
           }
       }
    }
    //数据库配置信息
    protected function parseConfig()
    {
        if(!isset($this->config)){
            $this->config=array();

            if(DB_HOST) {
                $this->config['host']=DB_HOST;
            }
            if(DB_USER) {
                $this->config['user']=DB_USER;
            }
            if(DB_PASSWORD) {
                $this->config['password']=DB_PASSWORD;
            }
            if(DB_DATABASE) {
                $this->config['database']=DB_DATABASE;
            }
            if(DB_PROT) {
                $this->config['port']=DB_PROT;
            }
            if(DB_CHARSET) {
                $this->config['charset']=DB_CHARSET;
            }

            return $this->config;
        }
    }
    //获取表前缀
    protected function getTablePerfix(){
        if(!isset($this->tableNamePerfix)) {
            if(DB_PERFIX) {
                $this->tableNamePerfix=DB_PERFIX;
            } else {
                $this->tableNamePerfix='';
            }
        }

        return $this->tableNamePerfix;
    }
    // 获取表名
    protected function getTableName(){
        if(!isset($this->tureTableName)) {
            if(!isset($this->tableName)){return false;};
            $this->tureTableName=$this->tableNamePerfix.$this->tableName;
        }
        return $this->tureTableName;
    }
}