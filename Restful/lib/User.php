<?php
require_once __DIR__.'/ErrorCode.php';
class User{
	/**
	*
	*@var
	*/
    private $db;
	/**
	*
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
		if(empty($username)){
			throw new Exception('用户名不能为空',ErrorCode::USERNAME_CANNOT_EMPTY);
		}
		if(empty($password)){
			throw new Exception('密码不能为空',ErrorCode::PASSWORD_CANNOT_EMPTY);
		}
		$sql='select * from `user` where `username`=:username and `password`=:password';
		$password=$this->_md5($password);
		$stmt = $this->_db->prepare($sql);
		$stmt->bindParam(':username',$username);
		$stmt->bindParam(':password',$password);
		if(!$stmt->execute()){
			throw new Exception('服务器内容部错误',ErrorCode::SERVER_INTERANL_ERROR);
		}
		$user= $stmt->fetch(PDO::FETCH_ASSOC);
		if(empty($user)){
			throw new Exception('用户名或密码错误',ErrorCode::USERNAME_OR_PASSWORD_INVALID);
		}
		unset($user['password']);
		return $user;
		
	}
	
	/**
	*用户注册
	*@param $username
	*@param $password
	*/
	public function register($username,$password){
	    
	    if(empty($username)){
	        throw new Exception('用户名不能为空',ErrorCode::USERNAME_CANNOT_EMPTY);
	    }
		if( $this->_isUsernameExists($username)){   
		    throw new Exception('用户名已存在', ErrorCode::USERNAME_EXISTS);
		}
		if (empty($password)){
		    throw new Exception('密码不能为空',ErrorCode::PASSWORD_CANNOT_EMPTY);
		}

		//写入sql
		$sql = 'INSERT INTO `user`(`username`,`password`,`createAt`) VALUES(:username,:password,:createAt)';
		$createAt = time();
		$password=$this->_md5($password);
		$stmt = $this->_db->prepare($sql);
		$stmt->bindParam(':username',$username);
		$stmt->bindParam(':password',$password);
		$stmt->bindParam('createAt',$createAt);

		if(!$stmt->excute()){
			throw new Exception('注册失败',ErrorCode::REGISTER_FAIL);
		}
		return[
			'userId'=>$this->_db->lastInsertId(),
			'username'=>$username,
			'createAt'=>$createAt
		];
	}

	/**
	 * MD5加密
	 * @param unknown $string
	 * @param string $key
	 */
	private  function _md5($str,$key = 'imooc'){
	    return md5($str.$key);
	    
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