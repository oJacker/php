<?php 
class User{
	
	
	
	/**
	*���ݿ����Ӿ��
	*@var
	*/
    private $db;
	/**
	*���췽��
	*User constructor
	*@param $_db
	*/
	public function __construct($_db){
	
		$this->_db = $_db;
	}
	
	/**
	*�û���¼
	*@param $username
	*@param $password
	*/
	public function login($username,$password){
	
		
	}
	
	/**
	*�û�ע��
	*@param $username
	*@param $password
	*/
	public function register($username,$password){
		if( $this->_isUsernameExists($username)){   
		    throw new Exception('�û����Ѵ���');
		}
	}
	
	
	
	/**
	*����û��Ƿ����
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