<?php
/**
 *
 * @authors cheney (2270292886@qq.com)
 * @date    2017-08-31 10:46:25
 * @version $Id$
 */
header("Content-type:text/html;charset=utf-8");

function p($var) {
	echo '<pre>';
	print_r($var);
	echo '</pre>';
}
/**
 * 数据库查询
 * @param  [type] $sql [description]
 * @return [type]      [description]
 */
function query($sql) {
	$dsn = "mysql:host=localhost;dbname=weixindb";
	$username = 'root';
	$password = 'CHENguo!@123';
	try {
		$pdo = new Pdo($dsn, $username, $password);
		$pdo->query("set names utf-8");
		$result = $pdo->query($sql);
		$row = $result->fetchAll(PDO::FETCH_ASSOC);
		return $row;
	} catch (PDOExcetion $e) {
		die($e->getMessage());
	}
}

/**
 * 日志
 * @param  	String $filename 	日志文件名
 * @param  	String $context  	日志内容串
 */
function writeLog($filename, $context) {
	$time = $_SERVER['REQUEST_TIME'];
	$date = date("Y-m-d H:i:s", $time);

	file_put_contents($filename, $date . "-----------------------------------------" . PHP_EOL, FILE_APPEND);
	file_put_contents($filename, $context . PHP_EOL, FILE_APPEND);
}
?>
