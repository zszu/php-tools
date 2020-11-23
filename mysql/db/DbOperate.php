<?php
class DbOperate
{

    private $pdo;//pdo 对象
    private $config = [];
    private $error;
    private $res; //执行后的结果
    private $table;
    private $where;
    private static $instance;
    private function __construct($config = [])
    {
//        $config = require '../config/db.php';

        $config = is_array($config) ? $config : [];
        $this->config = array_merge($this->config , $config);

        try {
            $options = [PDO::MYSQL_ATTR_INIT_COMMAND  => "SET NAMES {$config['charset']}"];
            $this->pdo = new PDO($config['dsn'] , $config['username'] , $config['password'] , $options);
            //把结果序列化成stdClass
            //$this->pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);
        }catch (PDOException $e){
            $this->error = $e->getMessage();
            return false;
        }catch (Exception $e){
            $this->error = $e->getMessage();
            return false;
        }

    }
    private function __clone()
    {
    }

    public static function getInstance($config){
        if(self::$instance == null){
            self::$instance = new self($config);
        }
        return self::$instance;
    }

    public function table($table){
        $this->table = $table;
        return $this;
    }
    public function where($condition){
        $where = '';

        foreach ($condition as $ck => $cv){
//            操作符格式   [操作符作符, 操作数1, 操作数2, ...]
//            and
//            ['and', 'id=1', 'id=2']---->id=1 AND id=2
// ['and', 'type=1', ['or', 'id=1', 'id=2']]---->type=1 AND (id=1 OR id=2)

// between and,['between', 'id', 1, 10]---->id BETWEEN 1 AND 10
//like,['like', 'name', 'tester']---->name LIKE '%tester%'
//in ['in', 'id', [1, 2, 3]]---->id IN (1, 2, 3)
            if(is_int($ck)){

            }else{
                $where .= "`{$ck}` = '{$cv}' and ";
            }
        }
        $this->where = $where;
        return $this;
    }
    //执行 sql
    public function query($sql){
        $res = $this->pdo->query($sql);
        if($res){
            $this->res = $res;
        }
    }
    public function queryPrepare($condition = []){
        $username = '%admin%';
        $sql = "SELECT * FROM `tsj_user` WHERE `username` LIKE :username";
        $res = $this->pdo->prepare($sql);
        $res->execute([':username'=>$username]);
        $this->res = $res;
    }
    //执行 sql 返回受影响的 条数
    public function exec($sql){
        $res = $this->pdo->exec($sql);
        if($res){
            $this->res = $res;
        }
    }
    /**
     * @return array
     * @desc 从返回结果集中 一条 ， 如果没有返回 false
     */
    public function fetch(){
        return $this->res->fetch();
    }
    /**
     * @return array
     * @desc 从返回结果集 ， 如果没有返回 false
     */
    public function fetchAll(){
        return $this->res->fetchAll();
    }
    /**
     * @return mixed|false
     * @desc 从结果结果集中 的下一行返回单独的一列 ， 如果没有返回 false
     */
    public function fetchColumn(){
        return $this->res->fetchColumn();
    }
    public function findOne($sql){
        $this->query($sql);
        return $this->fetch();
    }
    public function findAll($sql){
        $this->query($sql);
        return $this->fetchAll();
    }


    private function getSql($type , $condition){
        $felds = '(';
        $values = '(';
        foreach ($condition as $k => $v){
            $felds .= "`{$k}`,";
            $values .= "'{$v}',";
        }
        $felds = substr($felds , 0 ,-1);
        $values = substr($values , 0 ,-1);
        $felds .= ')';
        $values .= ')';
        $sql = "{$type} INTO `{$this->table}` {$felds} value {$values};";
        return $sql;
    }

    //根据字段查询
    public function select($felds , $condition){
        $feldStr = '';
        $conditionStr = '';
        foreach ($felds as $fv){
            $feldStr .= "`{$fv}`,";
        }
        foreach ($condition as $ck => $cv){
            $conditionStr .= "`{$ck}` = '{$cv}' and ";
        }
        $feldStr = substr($feldStr , 0 ,-1);
        $conditionStr = substr($conditionStr , 0 ,-4);
        $sql = "SELECT {$feldStr} FROM `{$this->table}`  WHERE {$conditionStr};";
        $this->query($sql);
        return $this;
    }
    public function all(){
        return $this->fetchAll();
    }
    public function one(){
        return $this->fetch();
    }
    //添加
    public function insert($condition){
        $felds = '(';
        $values = '(';
        foreach ($condition as $k => $v){
            $felds .= "`{$k}`,";
            $values .= "'{$v}',";
        }
        $felds = substr($felds , 0 ,-1);
        $values = substr($values , 0 ,-1);
        $felds .= ')';
        $values .= ')';
        $sql = "INSERT INTO `{$this->table}` {$felds} value {$values};";
        $this->exec($sql);
        return $this->res;
    }
    //修改
    public function update($condition , $data){
        $felds = '';
        $where = '';
        foreach ($data as $k => $v){
            $felds .= "`{$k}` = '{$v}',";
        }
        foreach ($condition as $ck => $cv){
            $where .= "`{$ck}` = '{$cv}' and ";
        }
        $felds = substr($felds , 0 ,-1);
        $where = substr($where , 0 ,-4);

        $sql = "UPDATE `{$this->table}` SET {$felds}  WHERE {$where};";
        $this->exec($sql);
        return $this->res;
    }
    //删除
    public function delete($condition){
        $where = '';
        foreach ($condition as $ck => $cv){
            $where .= "`{$ck}` = '{$cv}' and ";
        }
        $where = substr($where , 0 ,-4);
        $sql = "DELETE FROM `{$this->table}` WHERE {$where};";
        $this->exec($sql);
        return $this->res;
    }


    public function LastInsertId(){
        return $this->res->lastInsertId();
    }


}