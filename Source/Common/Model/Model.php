<?php
namespace Common;
if(!defined('SITE')) exit('Access Denied');
class Model {
    protected $config;
    protected $db;
    protected $tableName;
    protected $tablePrefix;
    protected $trueTableName;
    protected $fields;
    protected $fieldsType;
    protected $pk;
    protected $error;
    //验证规则
    protected $validate;
    //默认验证规则
    protected $validateD;
    protected $trueValidate;
    //规则的验证时机
    protected $vTime;
    //规则的默认验证时机
    protected $vTimeD=array(self::INSERT,self::DELETE,self::UPDATE,self::SELECT);
    protected $cTime;
    const INSERT=1;
    const DELETE=2;
    const UPDATE=3;
    const SELECT=4;
    const UPDATE_CHECK_PK=5;
    //验证的提示信息
    protected $vMessage;
    protected $vMessageD=array(
        'null'=>'{field}没有数据，请检查.',
        'type'=>'{field}无效的数据类型.',
        'between'=>'{field}的值必须介于{rule}',
        'notbetween'=>'{field}不得介于{rule}',
        'in'=>'{field}必须属于{rule}',
        'notin'=>'{field}不得属于{rule}',
        'length'=>'{field}的长度必须介于{rule}',
        'unique'=>'{field}中已经存在{value}',
        'regex'=>'{field}包含非法字符',
        'function'=>'{field}没有通过函数{function}的验证',
        'method'=>'{field}没有通过方法{method}的验证',
        'equal'=>'{field}必须等于{rule}',
        'notequal'=>'{field}不得等于{rule}',
        'confirm'=>'{field}与{rule}输入不一致'
    );
    protected $vFieldsAlias;

    public function __construct(){
        $this->db=new DB($this->parseConfig());
    }

    /**
     * Notes:
     * @param $pk
     * @param $autoValidation
     * @return array|false|null
     */
    public function delete($pk,$autoValidation=true) {
        $this->setCTime(self::UPDATE_CHECK_PK);
        if(is_array($pk)) {
            $placeholder='';
            $type='';
            foreach ($pk as $val){
                $placeholder.='?,';

                if(!$this->validation(array($this->getPK()=>$val))){
                    return false;
                }
                $type.=$this->getFieldsType($this->getPK());
            }
            $placeholder=rtrim($placeholder,',');
            $query="delete from {$this->getTableName()} where {$this->getPK()} in({$placeholder})";
        } else {
            $query="delete from {$this->getTableName()} where {$this->getPK()}=?";
            $type=$this->getFieldsType($this->getPK());
//            delete from test where id in '10,12,13'
            $pk=array($pk);
            if(!$this->validation(array($this->getPK()=>$pk))){
                return false;
            }
        }


        $param=array(
            'sql'=>$query,
            'bind'=>array($type,$pk)
        );
        return $this->db->execute($param);
    }

    /**
     * Notes: 修改数据接口
     * @param $data
     * @param $pk
     * @param $autoValidation
     * @return array|false|null
     */
    public function update($data,$pk,$autoValidation=true){
        if(isset($data[$this->getPK()])){
            unset($data[$this->getPK()]);
        }
        if($autoValidation){
            $this->setCTime(self::UPDATE_CHECK_PK);
            if(!$this->validation(array($this->getPK()=>$pk))){
                return false;
            }
            $this->setCTime(self::UPDATE);
            if(!$this->validation($data)){
                return false;
            }
        }
        $fieldsList='';
        $type='';
        foreach ($data as $key=>$value){
            if(!in_array($key,$this->getFields())){
                unset($data[$key]);
                continue;
            }
            $fieldsList.="`{$key}`=?,";
            $type.=$this->getFieldsType($key);
        }
        if(!count($data)){
            $this->error='无合法数据.';
            return false;
        }
        $type.=$this->getFieldsType($this->getPK());
        $fieldsList=rtrim($fieldsList,',');
        array_push($data,$pk);
        $param=array(
            'sql'=>"UPDATE `{$this->getTableName()}` SET {$fieldsList} WHERE `{$this->getPK()}`=?",
            'bind'=>array($type,$data)
        );
        return $this->db->execute($param);
    }
    public function getDb()
    {
        return $this->db;
    }
    public function add($data,$autoValidation=true){

        if($autoValidation) {
            $this->setCTime(self::INSERT);
            if(!$this->validation($data)) {
                var_dump(false,$this->getError());
                return false;
            };
        }

        if(isset($data[$this->getPK()])) {
            unset($data[$this->getPK()]);
        }

        foreach ($data as $key=>$val) {
           if( !in_array($key, $this->getFields()) ){
               var_dump("{$key}字段已剔除,不存在字段中---msg=>{$data[$key]}");
               var_dump('<br/>');
               unset($data[$key]);
               continue;
           }
           $type.=$this->getFieldsType($key);
        }

        if(!count($data)) {
            $this->error='无合法数据';
            return false;
        }

        $fieldsNameList=implode(',',array_keys($data));
        $fieldsNameListValue=implode(',',array_fill(0,count($data),'?'));

        $param=array(
            'sql'=>"insert into {$this->getTableName()}($fieldsNameList) values($fieldsNameListValue)",
            'bind'=>array($type,$data)
        );
        if($result=$this->db->execute($param)) {
            var_dump($result);
        }
    }
    public function validation($data){
        if(!isset($this->cTime)) {
            $this->error="操作状态不明确";
            return  false;
        }
        foreach ($this->getValidate() as $key=>$val){
            if(array_key_exists($key,$data)){
                foreach ($val as $ruleType=>$rule){
                    if($ruleType=='null') continue;
                    //获取当前字段的当前验证规则类型它的验证时机
                    //当前的时机
                    if(in_array($this->cTime,$this->getVTime($key,$ruleType))){
                        $tmp=$data[$key];
                        if($ruleType=='unique'){
                            $tmp=array(
                                'fieldName'=>$key,
                                'fieldValue'=>$data[$key]
                            );
                        }
                        if($ruleType=='confirm'){
                            $tmp=array($data[$rule],$data[$key]);
                        }
                        if(!$this->check($tmp,$ruleType,$rule)){
                            $this->error=$this->getVMessage($key,$ruleType,$rule,$data[$key]);
                            return false;
                        }
                    }
                }
            }else{
                if(in_array($this->cTime,$this->getVTime($key,'null'))){
                    if(!$val['null']){
                        $this->error=$this->getVMessage($key,'null',null,null);
                        return false;
                    }
                }
            }
        }
        return true;
    }
    protected function check($value,$ruleType,$rule){
        switch ($ruleType){
            case 'type':
                if($rule=='i'){
                    return is_numeric($value);
                }elseif($rule=='s'){
                    return is_string($value);
                }else{
                    return true;
                }
                break;
            case 'between':
                $between=explode(',',$rule);
                return $value>=$between[0] && $value<=$between[1];
                break;
            case 'notbetween':
                $notbetween=explode(',',$rule);
                return $value<$notbetween[0] || $value>$notbetween[1];
                break;
            case 'in':
                $in=explode(',',$rule);
                return in_array($value,$in);
                break;
            case 'notin':
                $notin=explode(',',$rule);
                return !in_array($value,$notin);
                break;
            case 'length':
                $length=explode(',',$rule);
                if(count($length)==1){
                    return mb_strlen($value,'utf-8')==$length[0];
                }elseif(count($length)==2){
                    return mb_strlen($value,'utf-8')>=$length[0] && mb_strlen($value,'utf-8')<=$length[1];
                }
                break;
            case 'unique':
                if($rule){
                    $param=array(
                        'sql'=>"select {$value['fieldName']} from {$this->getTableName()} where {$value['fieldName']}=?",
                        'bind'=>array($this->getFieldsType($value['fieldName']),array($value['fieldValue']))
                    );
                    if(count($this->db->execute($param))){
                        return false;
                    }else{
                        return true;
                    }
                }else{
                    return true;
                }
                break;
            case 'regex':
                return preg_match($rule,$value);
                break;
            case 'equal':
                return $value==$rule;
                break;
            case 'notequal':
                return $value!=$rule;
                break;
            case 'confirm':
                return $value[0]==$value[1];
                break;
            case 'function':
                if(isset($rule[1])){
                    $param=$rule[1];
                    array_unshift($param,$value);
                }else{
                    $param=array($value);
                }
                return call_user_func_array($rule[0],$param);
                break;
            case 'method':
                if(isset($rule[1])){
                    $param=$rule[1];
                    array_unshift($param,$value);
                }else{
                    $param=array($value);
                }
                return call_user_func_array(array($this,$rule[0]),$param);
                break;
        }
    }
    protected function getVMessage($field,$ruleType,$rule,$val){
        if(isset($this->vMessage[$field][$ruleType])){
            $message=$this->vMessage[$field][$ruleType];
        }else{
            $message=$this->vMessageD[$ruleType];
        }
        if(strpos($message,'{field}')!==false){
            $message=str_replace('{field}',$this->getVFieldsAlias($field),$message);
        }
        if(strpos($message,'{rule}')!==false){
            $message=str_replace('{rule}',$rule,$message);
        }
        if(strpos($message,'{value}')!==false){
            $message=str_replace('{value}',$val,$message);
        }
        if(strpos($message,'{function}')!==false){
            $message=str_replace('{function}',$rule[0],$message);
        }
        if(strpos($message,'{method}')!==false){
            $message=str_replace('{method}',$rule[0],$message);
        }
        return $message;
    }
    protected function getVFieldsAlias($field){
        if(isset($this->vFieldsAlias[$field])){
            return $this->vFieldsAlias[$field];
        }else{
            return $field;
        }
    }

    /**
     * Notes:
     */
    protected function parseFields(){
        $param=array(
            'sql'=>"SHOW COLUMNS FROM {$this->getTableName()}"
        );
        $fields=$this->db->execute($param);
        foreach ($fields as $val){
            if($val['Key']=='PRI') $this->pk=$val['Field'];
            $this->fields[]=$val['Field'];
            if(strpos($val['Type'],'int')!==false){
                $this->fieldsType[$val['Field']]='i';
                $this->validateD[$val['Field']]['type']='i';
            }else{
                $this->fieldsType[$val['Field']]='s';
            }
            if($val['Null']=='YES' || $val['Extra']=='auto_increment'){
                $this->validateD[$val['Field']]['null']=true;
            }else{
                $this->validateD[$val['Field']]['null']=false;
            }

        }
    }
    protected function getValidate(){
        if(!isset($this->trueValidate)){
            foreach ($this->getValidateD() as $key=>$val){
                if(isset($this->validate[$key])){
                    $this->trueValidate[$key]=array_merge($val,$this->validate[$key]);
                }else{
                    $this->trueValidate[$key]=$val;
                }
            }
        }
        return $this->trueValidate;
    }
    protected function getValidateD(){
        if(!isset($this->validateD)){
            $this->parseFields();
        }
        return $this->validateD;
    }
    protected function getFields(){
        if(!isset($this->fields)){
            $this->parseFields();
        }
        return $this->fields;
    }
    protected function getFieldsType($field=null){
        if(!isset($this->fieldsType)){
            $this->parseFields();
        }
        if($field){
            return $this->fieldsType[$field];
        }else{
            return $this->fieldsType;
        }
    }
    protected function getPK(){
        if(!isset($this->pk)){
            $this->parseFields();
        }
        return $this->pk;
    }
    protected function parseConfig(){
        if(!isset($this->config)){
            $this->config=array();
            if(defined('DB_HOST')){
                $this->config['host']=\DB_HOST;
            }
            if(defined('DB_USER')){
                $this->config['user']=\DB_USER;
            }
            if(defined('DB_PASSWORD')){
                $this->config['password']=\DB_PASSWORD;
            }
            if(defined('DB_DATABASE')){
                $this->config['database']=\DB_DATABASE;
            }
            if(defined('DB_PORT')){
                $this->config['port']=\DB_PORT;
            }
            if(defined('DB_CHARSET')){
                $this->config['charset']=\DB_CHARSET;
            }
        }
        return $this->config;
    }
    protected function getTablePrefix(){
        if(!isset($this->tablePrefix)){
            if(defined('DB_PREFIX')){
                $this->tablePrefix=\DB_PREFIX;
            }else{
                $this->tablePrefix='';
            }
        }
        return $this->tablePrefix;
    }
    protected function getTableName(){
        if(!isset($this->trueTableName)){
            $this->trueTableName=$this->getTablePrefix().$this->tableName;
        }
        return $this->trueTableName;
    }
    public function getError(){
        return $this->error;
    }
    //获取当前字段的当前验证规则类型它的验证时机
    protected function getVTime($filed,$ruleType){
        if(isset($this->vTime[$filed][$ruleType])){
            if(is_array($this->vTime[$filed][$ruleType])){
                return $this->vTime[$filed][$ruleType];
            }else{
                return array($this->vTime[$filed][$ruleType]);
            }
        }else{
            return $this->vTimeD;
        }
    }
    public function setCTime($param){
        $this->cTime=$param;
    }
}