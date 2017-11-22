<?php
session_start();
if(!isset($_SESSION['wall_open_id'])){
	header("Location: login.php");
	exit;
}
$openid=$_SESSION['wall_open_id'];

//获取当前用户数据
require('../util/database.class.php');
$db=Db::getInstance();
$user=$db->find("SELECT * FROM user where openid='$openid'");
if(empty($user))
	die('用户不存在');
?>

<!DOCTYPE html>
<html lang="zh-CN">
<head>
	<meta charset="UTF-8">
	<title>欢迎使用微信墙</title>
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
	<script src="https://cdn.bootcss.com/jquery/3.2.1/jquery.min.js"></script>
</head>
<body>
	<p>欢迎您：<?php echo $user['nickname'];?></p>
	<div>
		<textarea id="message" rows="10" style="width:100%;border:solid 2px #000;"></textarea>
	</div>
	<div>
		<button id="post-button" style="width:100%;font-size:20px">留言</button>
	</div>
	<script>
		$(document).ready(function(){
			$('#post-button').click(function(){
				$.post('../server/new.php',{
					content:$('#message').val()
				},function(response){
					var data=JSON.parse(response); //解析json数据
					alert(data.message);
				})
			});
		});
	</script>
</body>
</html>