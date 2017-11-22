<?php
//配置你的开发者ID和密码
$app_id='';
$app_secret='';

//工程根目录地址
$domain='http://abc.com/wall';

//没有code,发起授权请求获取code
if(!isset($_GET['code'])){

	//回调地址，即本页面
	$url=urlencode("$domain/client/login.php");
	//跳转到授权页面
	echo ("<script>
	window.location.href='https://open.weixin.qq.com/connect/oauth2/authorize?appid=".$app_id."&redirect_uri=$url&response_type=code&scope=snsapi_userinfo&state=STATE#wechat_redirect';
	</script>");
	exit;
} else {
	$code=$_GET['code'];

	//从微信端获取 access_token 和openid
	$response=file_get_contents("https://api.weixin.qq.com/sns/oauth2/access_token?appid=$app_id&secret=$app_secret&code=$code&grant_type=authorization_code");
	$response=json_decode($response,true);
	if(isset($response['errcode'])){
		echo 'ERROR '.$response['errcode'];
		exit;
	}
	$token=$response['access_token'];
	$openid=$response['openid'];

	require('../util/database.class.php');
	$db=Db::getInstance();
	$user=$db->find("SELECT * FROM user WHERE openid='$openid'");

	//如果数据库，没有该用户，用access_token拉取用户信息
	if(empty($user)){
		$response=file_get_contents("https://api.weixin.qq.com/sns/userinfo?access_token=$token&openid=$openid&lang=zh_CN");
			if(isset($response['errcode'])){
				echo 'ERROR '.$response['errcode'];
				exit;
		}
		$response=json_decode($response,true);
		//将用户信息存入数据库
		$nickname=$response['nickname'];
		$avatarUrl=$response['headimgurl'];

		$db->execute("INSERT INTO user VALUES ('$openid','$nickname','$avatarUrl')");
	}

	//进入登录状态
	session_start();
	$_SESSION['wall_open_id']=$openid;
	
	//进入首页
	header("Location: index.php");
}