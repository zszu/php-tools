<?php
$config = require '../config/db.php';

require './DbOperate.php';
$table = 'tsj_articles';

$db = DbOperate::getInstance($config);


$felds = ['id','type','title'];
$condition = ['type' => '2'];
$data = ['type' => '222'];

var_dump($db->table($table)->select($felds , $condition)->all());
die;

var_dump($db->table($table)->insert($condition));
die;

var_dump($db->table($table)->update($condition , $data));
die;

var_dump($db->table($table)->delete($condition));
die;

