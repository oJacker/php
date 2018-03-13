<?php
/**
 * 连接数据库并返回连接句柄
 */
$pdo = new PDO('mysql:host=localhost;dbname=restful','root','123456',array(PDO::MYSQL_ATTR_INIT_COMMAND => "set names utf8",PDO::ATTR_EMULATE_PREPARES=>false));
return $pdo;