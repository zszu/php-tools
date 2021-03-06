<?php
/**
 * Class DatabaseTool
 * 数据库备份还原类
 * @author zsz
 */
class DatabaseTool
{
    private $handler;
    private $config = [
        'host' => 'localhost',
        'port' => 3306,
        'user' => 'root',
        'password' => 'root',
        'database' => 'test',
        'charset' => 'utf8',
        'target' => 'test.sql'
    ];
    private $tables = [];
    private $error;
    private $begin; //开始时间
    /**
     * 构造方法
     * @param array $config
     */
    public function __construct($config = [])
    {
        $this->begin = microtime(true);
        $config = is_array($config) ? $config : [];
        $this->config = array_merge($this->config, $config);
        //启动PDO连接
        try
        {
            $this->handler = new PDO("mysql:host={$this->config['host']}:{$this->config['port']};dbname={$this->config['database']}", $this->config['user'], $this->config['password']);
        }
        catch (PDOException $e)
        {
            $this->error = $e->getMessage();
            return false;
        }
        catch (Exception $e)
        {
            $this->error = $e->getMessage();
            return false;
        }
    }

    /**
     * 备份
     * @param array $tables
     * @return bool
     */
    public function backup($tables = [])
    {
        //存储表定义语句的数组
        $ddl = [];
        //存储数据的数组
        $data = [];
        $this->setTables($tables);
        if (!empty($this->tables))
        {
            foreach ($this->tables as $table)
            {
                $ddl[] = $this->getDDL($table);
                $data[] = $this->getData($table);
            }
            //开始写入
            $this->writeToFile($this->tables, $ddl, $data);
        }
        else
        {
            $this->error = '数据库中没有表!';
            return false;
        }
    }

    /**
     * 设置要备份的表
     * @param array $tables
     */
    private function setTables($tables = [])
    {
        if (!empty($tables) && is_array($tables))
        {
            //备份指定表
            $this->tables = $tables;
        }
        else
        {
            //备份全部表
            $this->tables = $this->getTables();
        }
    }

    /**
     * 查询
     * @param string $sql
     * @return mixed
     */
    private function query($sql = '')
    {
        $stmt = $this->handler->query($sql);
        $stmt->setFetchMode(PDO::FETCH_NUM);
        $list = $stmt->fetchAll();
        return $list;
    }

    /**
     * 获取全部表
     * @return array
     */
    private function getTables()
    {
        $sql = 'SHOW TABLES';
        $list = $this->query($sql);
        $tables = [];
        foreach ($list as $value)
        {
            $tables[] = $value[0];
        }
        return $tables;
    }

    /**
     * 获取表定义语句
     * @param string $table
     * @return mixed
     */
    private function getDDL($table = '')
    {
        $sql = "SHOW CREATE TABLE `{$table}`";
        $ddl = $this->query($sql)[0][1] . ';';

        return $ddl;
    }
    private function getVersion(){
        $sql = "SELECT VERSION();";
        return $this->query($sql)[0][0];
    }

    /**
     * 获取表数据
     * @param string $table
     * @return mixed
     */
    private function getData($table = '')
    {
        $sql = "SHOW COLUMNS FROM `{$table}`";
        $list = $this->query($sql);

        //字段
        $columns = '';
        //需要返回的SQL
        $query = '';
        foreach ($list as $value)
        {
            $columns .= "`{$value[0]}`,";
        }
        $columns = substr($columns, 0, -1);
        $data = $this->query("SELECT * FROM `{$table}`");

        foreach ($data as $value)
        {
            $dataSql = null;
            foreach ($value as $v)
            {
                $dataSql .= ($v == NULL ? ' null,' : " '{$v}',");
            }
            //去除开始的空格
            $dataSql = substr($dataSql, 1);
            //去除结尾的 , 符号
            $dataSql = substr($dataSql, 0 , -1);
//            $query .= "INSERT INTO `{$table}` ({$columns}) VALUES ({$dataSql});\r\n";
            $query .= "INSERT INTO `{$table}` VALUES ({$dataSql});\r\n";

        }
        return $query;
    }

    /**
     * 写入文件
     * @param array $tables
     * @param array $ddl
     * @param array $data
     */
    private function writeToFile($tables = [], $ddl = [], $data = [])
    {
        $time = date('Y-m-d H:i:s' , time());
        $host = $this->config['host'];
        $port = $this->config['port'];
        $database = $this->config['database'];
        $sql_version =$this->getVersion();


        $str = "/*
Navicat MySQL Data Transfer

Source Server         : $host
Source Server Version : $sql_version
Source Host           : $host:$port
Source Database       : $database

Target Server Type    : MYSQL
Target Server Version : $sql_version
File Encoding         : 65001

Date: $time
*/

SET FOREIGN_KEY_CHECKS=0;\r\n";
        $str .= "";
        $i = 0;
        foreach ($tables as $table)
        {
            $str .= "-- ----------------------------\r\n";
            $str .= "-- Table structure for {$table}\r\n";
            $str .= "-- ----------------------------\r\n";
            $str .= "DROP TABLE IF EXISTS `{$table}`;\r\n";
            $str .= $ddl[$i] . "\r\n";

            $str .= "-- ----------------------------\r\n";
            $str .= "-- Records of {$table}\r\n";
            $str .= "-- ----------------------------\r\n";
            $str .= $data[$i] . "\r\n";
            $i++;

        }
        echo file_put_contents($this->config['target'], $str) ? '备份成功!花费时间' . (microtime(true) - $this->begin) . 'ms' : '备份失败!';
    }

    /**
     * 错误信息
     * @return mixed
     */
    public function getError()
    {
        return $this->error;
    }
    //还原数据库
    public function restore($path = '')
    {
        if (!file_exists($path))
        {
            $this->error('SQL文件不存在!');
            return false;
        }
        else
        {
            $sql = file_get_contents($path);
            try
            {
                $this->handler->exec($sql);
                echo '还原成功!花费时间', (microtime(true) - $this->begin) . 'ms';
            }
            catch (PDOException $e)
            {
                $this->error = $e->getMessage();
                return false;
            }
        }
    }
}

$db = new DatabaseTool(['target' => './backup/2020_11_23_1606096052' . '.sql']);
$db->backup();
//$db->restore('./backup/2020_11_23_1606096052.sql');