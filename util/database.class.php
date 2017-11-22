<?php
class Db {

	private $host;
	private $port;
	private $username;
	private $password;
	private $dbname;
	private $charset;

	public static $instance;

	public $connection;

	/**
	 * 使用单例模式
	 */
	public static function getInstance(){
		if(!isset(self::$instance)){
			self::$instance=new self();
		}
		return self::$instance;
	}

	/**
	 * 私有构造函数，防止从类外new新实例
	 */
	private function __construct(){
		$this->host='localhost';
		$this->port='3306';
		$this->username='root';
		$this->password='123456';
		$this->dbname='wall';
		$this->charset="utf8";

		$this->connect();
	}

	/**
	 * php 7.0移除了旧版mysql API
	 * 使用增强版 mysqli 来操作数据库
	 */
	public function connect(){
		$this->connection=new mysqli("$this->host:$this->port",$this->username,$this->password,$this->dbname);

		//设置编码
		$this->connection->set_charset($this->charset);
	}

	public function query($sql){
		return $this->connection->query($sql);
	}

	/**
	 * 查询并返回结果集
	 */
	public function select($sql){
		$result=$this->query($sql);
		if($result)
			return $result->fetch_all(MYSQLI_ASSOC);
		return [];
	}

	/**
	 * 查询并返回一条结果
	 */
	public function find($sql){
		$result=$this->query($sql);
		if($result)
			return $result->fetch_array(MYSQLI_ASSOC);
		return [];
	}

	/**
	 * 执行并返回受影响的行数
	 */
	public function execute($sql){
		$this->query($sql);
		return $this->connection->affected_rows;
	}

	/**
	 * 执行插入语句
	 * 返回受影响的函数
	 */
	public function insert($table,$params){

		//构造预处理语句
		foreach($params as $key=>$v){
			$prepare_str[]="?";
			$references[$key]=&$params[$key];
		}
		$prepare_str=implode(',',$prepare_str); 
		//$bind_str=implode('',$bind_str); 
		//array_unshift($references, $bind_str);

		//prepare
		if($stmt=$this->connection->prepare("INSERT INTO $table VALUES ($prepare_str)")){
			 //魔法方法，将数组作为参数列表调用函数
			call_user_func_array(array($stmt,"bind_param"),$references);
			if(!$stmt->execute())
				echo $this->connection->error;
		}
		return $this->connection->affected_rows;
	}

}