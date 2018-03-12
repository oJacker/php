<?php 
class User{
	
	
	
	/**
	*数据库连接句柄
	*@var
	*/
    private $db;
	/**
	*构造方法
	*User constructor
	*@param $_db
	*/
	public function __construct($_db){
	
		$this->_db = $_db;
	}
	
	/**
	*用户登录
	*@param $username
	*@param $password
	*/
	public function login($username,$password){
	
		
	}
	
	/**
	*用户注册
	*@param $username
	*@param $password
	*/
	public function register($username,$password){
		if( $this->_isUsernameExists($username)){   
		    throw new Exception('用户名已存在');
		}
	}
	
	
	
	/**
	*检测用户是否存在
	*@param $username
	*@return bool
	*/
	private function _isUsernameExists($username){
		
		
		$exists = false;
		$sql = 'select * from `user` where `username`=:username';
		$stmt = $this->_db->prpare($sql);
		$stmt->bindParam(':username',$username);
		$stmt->execute();
		$result = $stmt->fetch(PDO::FETCH_ASSOC);
		return !empty($result);
	}
	

}

?>