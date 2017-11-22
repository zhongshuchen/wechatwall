<?php
/**
 * PHP默认执行时间为30秒
 * 超过30秒就会停止运行
 * 所以将执行时间设为0，也就是不限制
 * 才能长时间地进行推送服务
 */
set_time_limit(0);
/**
 * 允许所有域监听本服务发出的消息
 * 上传到公网后应将其改成内部服务器的域名
 */
header('Access-Control-Allow-Origin: *');

//将文件类型定义为 event-stream
header('Content-Type: text/event-stream');

//关闭缓存
header('Cache-Control: no-cache');

//创建数据库实例
require('../util/database.class.php');
$db=Db::getInstance();

/**
 * 时间节点
 * 记录每一次推送的时间点
 * 加载时初始化为当前时间
 */
$time=time();

while(true){
	/**
	 * 只查询比时间结点更晚的消息，即还没有推送过的消息
	 * 顺带把留言者的消息一起查询出来
	 */
	$messages=$db->select("SELECT * FROM message m  JOIN user u ON m.openid=u.openid WHERE m.posttime>=$time");

	//如果有消息，推送给客户端
	if(!empty($messages)){
		//更新时间结点
		$time=time();
		/**
		 * 打包为json
     * PHP_EOL表示换行符，在linux服务器中等价于 /n
     */
		echo "data: ". json_encode($messages) . PHP_EOL;
		//输出空行表示推送数据结束
		echo PHP_EOL;
		//释放数据缓冲区
		ob_flush();
		//推送到浏览器
		flush();
	}
	//暂停3秒
	sleep(3);
}