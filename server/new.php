<?php
//创建数据库实例
require('../util/database.class.php');
$db=Db::getInstance();

//通过json返回代码和结果
function result($errorCode,$message){
	echo json_encode([
		'errorCode'=>$errorCode,
		'message'=>$message
	]);
}

session_start();

if(!isset($_SESSION['wall_open_id'])){
	result(1,'未登录');
	die;
}
$openid=$_SESSION['wall_open_id'];
if(!isset($_POST['content'])){
	result(2,'消息为空');
	die;
}

//处理留言消息

$time=time();
$content=$_POST['content'];

$a=$db->execute("INSERT INTO message VALUES (null,'$openid','$content',$time)");
if($a>0){
	result(0,'留言成功');
} else {
	result(3,'留言失败');
}