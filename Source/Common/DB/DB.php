<?php
namespace Common;
if(!SITE) {exit('Access Denied');}
class DB extends \mysqli
{
    private $lastInsID;
    private $config=array(
        'host'=>'',
        'user'=>'',
        'password'=>'',
        'database'=>'',
        'port'=>'3306',
        'charset'=>'utf-8'
    );
    public function __construct($config)
    {
        $this->config=array_merge($this->config,$config);
        @parent::__construct($this->config['host'], $this->config['user'], $this->config['password'], $this->config['database'], $this->config['port']);
        if($this->connect_error){
            $this->dError($this->connect_error);
        }
        $this->set_charset($this->config['charset']);
    }
    protected function dError($error) {
        throw new \Exception($error);
    }
//$param=array(
//'sql'=>'select * from test where id>?',
//'bind'=>array('i',$id)
//);
    public function execute($param){
        $stmt=$this->stmt_init();
        if($stmt->prepare($param['sql'])){
            if(isset($param['bind'])){
                foreach ($param['bind'][1] as $key=>$val){
                    $tmp[]=&$param['bind'][1][$key];
                }
                array_unshift($tmp,$param['bind'][0]);
                if(!@call_user_func_array(array($stmt,'bind_param'),$tmp)){
                    $this->dError('参数绑定失败.');
                }
            }
            if($stmt->execute()){
                if($stmt->result_metadata()){
                    $result=$stmt->get_result();
                    return $result->fetch_all(MYSQLI_ASSOC);
                }
                $this->lastInsID=$stmt->insert_id;
                return $stmt->affected_rows;
            }else{
                $this->dError($stmt->error);
            }
        }else{
            $this->dError($stmt->error);
        }
    }
    public function getLastInsID() {
         if($this->lastInsID){
             return $this->lastInsID;
         } else {
             $this->dError('insert_id获取失败');
         }
    }

    public function escape($data){
        if(is_string($data)){
            return $this->real_escape_string($data);
        }
        if(is_array($data)){
            foreach ($data as $key=>$val){
                $data[$key]=$this->escape($val);
            }
        }
        return $data;
    }
}