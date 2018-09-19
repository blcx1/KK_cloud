<?php

namespace Home\Model;
use Think\Model;

class UserViewModel extends Model {
	
	protected $error_code_array = array();//错误编码
	protected $error_array = array();//错误信息
	
	public function __construct($name='',$tablePrefix='',$connection='') {
		
		$this->dbName = 'db_user';
		$this->tableName = C('DB_PREFIX').'user_view';		
		$this->trueTableName = $this->tableName;
		parent::__construct($name,$tablePrefix,$connection);
		$this->pk = 'user_id';
	}
	
	/*
	 * 释放
	*/
	public function __destruct(){
	
		$this->error_code_array = array();
		$this->error_array = array();
	}	
	
	//获取用户信息
	public function get_info($id){

		$info_array = array();
		$id = intval($id);
		if($id > 0){
			$id_name = $this->pk;
			$result = $this->where ($id_name.' = '.$id)->find();
			if(check_array_valid($result)){

				$result['portrait_img_url'] = getFullUrl($result['portrait_img_url']);
				$info_array = $result;
			}
		}
		
		return $info_array;		
	}	
	
	/**
	 * 网页版忘记密码
	 * @param \Home\Model\UserModel $user_object
	 * @param unknown $email
	 * @param unknown $tel
	 * @param unknown $verify_type
	 * @return number
	 */
	public function web_user_forgot(\Home\Model\UserModel $user_object,$email,$tel,$verify_type){
	
		$user_id = 0;
		$check = false;
		$str_where = '';
		$verify_type = intval($verify_type);
		$this->error_array = array();
		$this->error_code_array = array();
			
		switch($verify_type){
				
			case VERIFY_TYPE_EMAIL:
	
				$email = trim($email);
				$email_len = strlen($email);
				if($email_len > 0 && \Home\Model\UserModel::is_mail($email)){
						
					$check = true;
					$str_where = ' email = "'.addslashes($email).'" ';
				}else{
						
					if($email_len > 0){
	
						$this->error_code_array['email'] = ERROR_EMAIL_INVALID;
						$this->error_array['email'] = '邮箱地址不合法，请核对后输入，谢谢';
					}else{
	
						$this->error_code_array['email'] = ERROR_EMAIL_EMPTY;
						$this->error_array['email'] = '邮箱地址不能为空';
					}
				}
				break;
			case VERIFY_TYPE_TEL:
	
				$tel = trim($tel);
				$tel_len = strlen($tel);
				if($tel_len > 0 && \Home\Model\UserModel::is_tel($tel)){
						
					$check = true;
					$str_where = ' user_tel = "'.addslashes($tel).'" ';
				}else{
						
					if($tel_len > 0){
							
						$this->error_code_array['tel'] = ERROR_TEL_INVALID;
						$this->error_array['tel'] = '手机号码不合法';
					}else{
							
						$this->error_code_array['tel'] = ERROR_TEL_EMPTY;
						$this->error_array['tel'] = '手机不能为空，请输入，谢谢';
					}
				}
				break;
			default :
	
				$this->error_code_array['result'] = ERROR_VERIFY_TYPE_INVALID;
				$this->error_array['result'] = '验证类型不合法';
				break;
		}
		if($check){
				
			$result = $this->field('user_id,user_name')->where($str_where)->find();
			if(check_array_valid($result)){
	
				$user_id = intval($result['user_id']);
				$user_name = $result['user_name'];								
				if($verify_type == VERIFY_TYPE_EMAIL){
						
					$email_verify = \Home\Model\UserModel::get_email_verify(3,6);
					session('verify_code',$email_verify);
					$user_object->send_email($user_id,$user_name,$email,$email_verify,'web_verify','','',NOT_IS_TXI);
				}
			}else{
	
				$this->error_code_array['result'] = ERROR_NOT_MATCH_USER;
				$this->error_array['result'] = '没有匹配的用户账号';
			}
		}
	
		return $user_id;
	}
	
	/**
	 * 忘记密码
	 * @param unknown $email
	 * @param unknown $tel
	 * @param unknown $verify_type
	 * @return number
	 */
	public function user_forgot($session_object,\Home\Model\UserModel $user_object,$email,$tel,$verify_type){
		
		$user_id = 0;
		$check = false;		
		$str_where = '';
		$verify_type = intval($verify_type);		
		$this->error_array = array();
		$this->error_code_array = array();
					
		switch($verify_type){
			
			case VERIFY_TYPE_EMAIL:

				$email = trim($email);
				$email_len = strlen($email);
				if($email_len > 0 && \Home\Model\UserModel::is_mail($email)){
					
					$check = true;
					$str_where = ' email = "'.addslashes($email).'" ';
				}else{
					
					if($email_len > 0){
						
						$this->error_code_array['email'] = ERROR_EMAIL_INVALID;
						$this->error_array['email'] = '邮箱地址不合法，请核对后输入，谢谢';
					}else{
						
						$this->error_code_array['email'] = ERROR_EMAIL_EMPTY;
						$this->error_array['email'] = '邮箱地址不能为空';
					}
				}
				break;
			case VERIFY_TYPE_TEL:
				
				$tel = trim($tel);
				$tel_len = strlen($tel);
				if($tel_len > 0 && \Home\Model\UserModel::is_tel($tel)){
					
					$check = true;
					$str_where = ' user_tel = "'.addslashes($tel).'" ';						
				}else{
					
					if($tel_len > 0){
							
						$this->error_code_array['tel'] = ERROR_TEL_INVALID;
						$this->error_array['tel'] = '手机号码不合法';
					}else{
							
						$this->error_code_array['tel'] = ERROR_TEL_EMPTY;
						$this->error_array['tel'] = '手机不能为空，请输入，谢谢';
					}
				}					
				break;
			default :
				
				$this->error_code_array['result'] = ERROR_VERIFY_TYPE_INVALID;
				$this->error_array['result'] = '验证类型不合法';
				break;				
		}
		if($check){				
			
			$result = $this->field('user_id,user_name')->where($str_where)->find();
			if(check_array_valid($result)){
				
				$user_id = intval($result['user_id']);
				$user_name = $result['user_name'];
				$session_object->change_session_data('user_id',$user_id);
				$session_object->change_session_data('verify_code_count',0);
				if($verify_type == VERIFY_TYPE_EMAIL){
					
					$email_verify = \Home\Model\UserModel::get_email_verify(3,6);
					$session_object->change_session_data('verify_code',$email_verify);					
					$user_object->send_email($user_id,$user_name,$email,$email_verify,'client_verify','','',NOT_IS_TXI);
				}				
			}else{
				
				$this->error_code_array['result'] = ERROR_NOT_MATCH_USER;
				$this->error_array['result'] = '没有匹配的用户账号';
			}
		}		
		
		return $user_id;
	}
	
	/*
	 * 获取错误编码
	*/
	public function get_error_code_array(){
	
		return (array)$this->error_code_array;
	}
	
	/*
	 * 获取错误信息
	*/
	public function get_error_array(){
	
		return (array)$this->error_array;
	}
}
?>