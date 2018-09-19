<?php

namespace Home\Model;
use Think\Model;
use Home\Model\UserSession;
use Home\Model\UserSessionTmp;
use Home\Model\UserInfoModel;
use Home\Model\UserExtendModel;

class UserModel extends Model {
	
	protected $user_id = 0;//用户id
	protected $current_time;//日期时间戳
	protected $current_datetime;//日期时间按 Y-m-d H:i:s格式
	protected $current_date;//日期时间 按Y-m-d格式
	protected $error_code_array = array();//错误编码
	protected $error_array = array();//错误信息
	protected $upload_base_dir = '';
	protected $relation_base_dir = 'Public/Upload/avatar/';
	protected $mode = '0755';
	protected $max_size = 3145728;//最大限制
	protected $allow_file_types = '|GIF|JPG|PNG|';
	protected $portrait_img_url = '';
	protected $default_portrait_img_url = 'Public/Upload/avatar/1.png';
	
	/*
	 * 初始化
	 */
	public function __construct($name='',$tablePrefix='',$connection='') {
		
		$this->dbName = 'db_user';
		$this->tableName = C('DB_PREFIX').'user';
		$this->trueTableName = $this->tableName;
		$this->pk = 'user_id';
		parent::__construct($name,$tablePrefix,$connection);
		$this->current_time = time();
		$this->current_datetime = date('Y-m-d H:i:s',$this->current_time);
		$this->current_date = date('Y-m-d',$this->current_time);		
		$this->upload_base_dir = BASE_DIR.$this->relation_base_dir;
	}
	
	/*
	 * 释放
	 */
	public function __destruct(){
		
		$this->user_id = 0;
		$this->current_time = 0;
		$this->current_datetime = '';
		$this->current_date = '';
		$this->error_code_array = array();
		$this->error_array = array();		
		$this->portrait_img_url = '';
		$this->default_portrait_img_url = '';
		$this->relation_base_dir = '';
	}
	
	/*
	 * 获取用户信息
	 */
	public function get_info($id){

		$info_array = array();
		$id = intval($id);
		if($id > 0){
			$id_name = $this->pk;
			
			$result = $this->where ($id_name.' = '.$id)->find();			
			if(check_array_valid($result)){
					
				$info_array = $result;
			}
		}
		
		return $info_array;		
	}
	
	/*
	 * 通过用户id获取用户名
	 */
	public function get_user_name($id){
		
		$user_name = '';
		$id = intval($id);
		if($id > 0){
			
			$result = $this->field('user_name')->where ('user_id = '.$id)->find();
			if(check_array_valid($result)){
					
				$user_name = $result['user_name'];
			}
			
		}
		return $user_name;
	}
	
	/*
	 * 通过用户id获取用户email
	 */
	public function get_email($id){
		
		$email = '';
		$id = intval($id);
		if($id > 0){
				
			$result = $this->field('email')->where ('user_id = '.$id)->find();
			if(check_array_valid($result)){
					
				$email = $result['email'];
			}
				
		}
		return $email;
	}
	
	/*
	 * 通过用户id获取用户手机号码
	 */
	public function get_user_tel(){
		
		$tel = '';
		$id = intval($id);
		if($id > 0){
		
			$result = $this->field('user_tel')->where ('user_id = '.$id)->find();
			if(check_array_valid($result)){
					
				$tel = $result['user_tel'];
			}
		
		}
		return $tel;
	}
	
	/*
	 * 判别用户名是否可以添加
	 */
	public function check_add_user_name($user_name,$error_status = false,$user_id = 0){
		
		$check = false;		
		$error_status = (bool) $error_status;
		$user_name = trim($user_name);
		$user_name_len = strlen($user_name);
		if($user_name_len <= 0){
						
			if($error_status){
				
				$this->error_code_array['user_name'] = ERROR_USER_EMPTY;
				$this->error_array['user_name'] = '用户名不能为空，请输入，谢谢';
			}
			return $check;
		}elseif($user_name_len <= 1 || $user_name_len > 60){
			
			if($error_status){
			
				$this->error_code_array['user_name'] = ERROR_USER_INVALID_LEN;
				$this->error_array['user_name'] = '用户名长度不能小于2位且不能大于60个字符，请核对后输入，谢谢';
			}
			return $check;
		}
		$check_valid = self::is_user_name($user_name);		
		if($check_valid){
			
			$user_id = intval($user_id);
			$user_id = $user_id > 0 ? $user_id : 0;
			$check = $this->check_exists_user_name($user_id,$user_name) ? false : true;
			if(!$check && $error_status){
				
				$this->error_code_array['user_name'] = ERROR_USER_EXISTS;
				$this->error_array['user_name'] = '用户名已被注册';
			}
		}elseif($error_status){
			
			$this->error_code_array['user_name'] = ERROR_USER_INVALID;
			$this->error_array['user_name'] = '用户名输入不合法字符';
		}
		return $check;
	}
	
	/*
	 * 判别用户名是否存在
	 */
	public function check_exists_user_name($user_id = 0,$user_name){
		
		$check = true;
		$user_name = trim($user_name);
		if(!empty($user_name)){
			
			$str_where = '';
			$user_id = intval($user_id);
			if($user_id > 0){
				
				$str_where = 'user_id != '.$user_id.' and ';
			}
			$str_where .= ' user_name = "'.addslashes($user_name).'" ';
			$total_count = $this->field('user_id')->where ($str_where)->count();
			$check = intval($total_count) > 0 ? true : false;
		}
		return $check;
	}
	
	/*
	 * 判别email是否可以添加
	 */
	public function check_add_email($email,$error_status = false,$user_id = 0){
		
		$check = false;		
		$error_status = (bool) $error_status;
		$email = trim($email);
		$email_len = strlen($email);
		if($email_len <= 0){
			
			if($error_status){
			
				$this->error_code_array['email'] = ERROR_EMAIL_EMPTY;
				$this->error_array['email'] = '邮箱地址不能为空';
			}
			return $check;
		}elseif($email_len > 60){
			
			if($error_status){
				
				$this->error_code_array['email'] = ERROR_EMAIL_INVALID_LEN;
				$this->error_array['email'] = '邮箱地址长度不能超过60个字符';
			}
			return $check;
		}
		$check_valid = self::is_mail($email);
		if($check_valid){				
			
			$user_id = intval($user_id);
			$user_id = $user_id > 0 ? $user_id : 0;
			$check = $this->check_exists_email($user_id,$email) ? false : true;
			if(!$check && $error_status){
			
				$this->error_code_array['email'] = ERROR_EMAIL_EXISTS;
				$this->error_array['email'] = '该邮箱地址已被注册';
			}			
		}elseif($error_status){
			
			$this->error_code_array['email'] = ERROR_EMAIL_INVALID;
			$this->error_array['email'] = '邮箱地址不合法，请核对后输入，谢谢';
		}
		
		return $check;
	}
	
	/*
	 * 判别email是否存在
	 */	
	public function check_exists_email($user_id = 0,$email){
	
		$check = true;
		$email = trim($email);
		if(!empty($email)){
			
			$str_where = '';
			$user_id = intval($user_id);
			if($user_id > 0){
				
				$str_where = 'user_id != '.$user_id.' and ';
			}
			$str_where .= ' email = "'.addslashes($email).'" ';
			$total_count = $this->field('user_id')->where ($str_where)->count();
			$check = intval($total_count) > 0 ? true : false;
		}
		return $check;
	}
	
	/*
	 * 判别tel是否可以添加
	 */
	public function check_add_tel($tel,$error_status = false,$user_id = 0){
		
		$check = false;		
		$tel = trim($tel);
		$error_status = (bool) $error_status;
		if(strlen($tel) <= 0){
			
			if($error_status){
					
				$this->error_code_array['tel'] = ERROR_TEL_EMPTY;
				$this->error_array['tel'] = '手机不能为空，请输入，谢谢';
			}
			return $check;
		}
		$check_valid = self::is_tel($tel);		
		if($check_valid){
			
			$user_id = intval($user_id);
			$user_id = $user_id > 0 ? $user_id : 0;
			$check = $this->check_exists_tel($user_id,$tel) ? false : true;
			if(!$check && $error_status){
					
				$this->error_code_array['tel'] = ERROR_TEL_EXISTS;
				$this->error_array['tel'] = '手机号码已存在';
			}
		}elseif($error_status){
			
			$this->error_code_array['tel'] = ERROR_TEL_INVALID;
			$this->error_array['tel'] = '手机号码不合法';
		}
		return $check;
	}
	
	/*
	 * 判别tel是否存在
	 */
	public function check_exists_tel($user_id = 0,$tel){
		
		$check = true;
		$tel = trim($tel);
		if(!empty($tel)){
				
			$str_where = '';
			$user_id = intval($user_id);
			if($user_id > 0){
				
				$str_where = 'user_id != '.$user_id.' and ';
			}
			$str_where .= ' user_tel = "'.addslashes($tel).'" ';
			$total_count = $this->field('user_id')->where ($str_where)->count();
			$check = intval($total_count) > 0 ? true : false;			
		}
		return $check;
	}
	
	/*
	 * 修改用户相关信息
	 */
	public function change_user_relate_info($user_id,$name,$relate_type,$other_array = array()){
		
		$check = false;
		$check_method = true;
		$relate_type = intval($relate_type);
		$this->error_code_array = array();
		$this->error_array = array();
		$method_name = 'change_';
		switch($relate_type){
			case CHANGE_USER_NAME:
				
				$method_name .= 'user_name';
				break;
			case CHANGE_NICK_NAME:
				
				$method_name .= 'nick_name';
				break;
			
			case CHANGE_USER_EMAIL:
			
				$method_name .= 'email';
				break;
			case CHANGE_USER_TEL:
			
				$method_name .= 'tel';
				break;
			case CHANGE_PASSWORD:
				
				$method_name .= 'password';
				break;
			default :
				
				$check_method = false;
				break;				
		}
		if($check_method && method_exists($this,$method_name)){
			
			if(check_array_valid($other_array)){
				
				$check = $this->$method_name($user_id,$name,$other_array);
			}else{
				
				$check = $this->$method_name($user_id,$name);
			}
			
		}else{
			
			$this->error_array = '非法操作';
			$this->error_code_array['result'] = INVALID_OPERATE; 
		}
		return $check;
	}
	
	/*
	 * 修改用户名
	 */
	public function change_user_name($user_id,$user_name){
		
		$check = false;
		$user_id = intval($user_id);		
		if($user_id > 0){
			
			$check_user_name = $this->check_add_user_name($user_name,true,$user_id);			
			if($check_user_name){
				
				$update_array = array();
				$update_array['user_name'] = addslashes($user_name);
				$update_array['update_time'] = addslashes($this->current_datetime);
				$result = $this->where('user_id = '.$user_id)->data($update_array)->save();
				if($result === false){
					
					$this->error_code_array['result'] = ERROR_UPDATE_FAILED;
					$this->error_array['result'] = '更新失败';
				}else{
					
					$check = true;
				}			
			}
			
		}else{
			
			$this->error_code_array['result'] = INVALID_DATA;
			$this->error_array['result'] = '非法数据';
		}
		return $check;
	}
	
	/*
	 * 修改用户昵称
	 */
	public function change_nick_name($user_id,$nick_name){
		
		$check = false;
		$user_id = intval($user_id);	
		$nick_name = trim($nick_name);
		$nick_name_len = strlen($nick_name);
		if($user_id <= 0){
			
			$this->error_code_array['result'] = INVALID_DATA;
			$this->error_array['result'] = '非法数据';
			return $check;
		}elseif($nick_name_len <= 0){
			
			$this->error_code_array['result'] = ERROR_NICK_NAME_EMPTY;
			$this->error_array['result'] = '昵称不能为空';
			return $check;
		}elseif($nick_name_len <= 1 || $nick_name_len > 60){
			
			$this->error_code_array['result'] = ERROR_NICK_NAME_INVALID_LEN;
			$this->error_array['result'] = '昵称长度在2到60位';
			return $check;
		}
		$check_valid = is_user_name($nick_name);
		if($check_valid){			
			
			$update_array = array();
			$update_array['user_nickname'] = addslashes($nick_name);
			$result = $this->where('user_id = '.$user_id)->data($update_array)->save();
			if($result === false){
			
				$this->error_code_array['result'] = ERROR_UPDATE_FAILED;
				$this->error_array['result'] = '更新失败';
			}else{
					
				$check = true;
			}				
		}else{
			
			$this->error_code_array['result'] = ERROR_NICK_NAME_INVALID;
			$this->error_array['result'] = '昵称不合法字符';			
		}
		return $check;
	}
	
	/**
	 * 修改邮箱
	 * @param unknown $user_id
	 * @param unknown $password
	 * @param unknown $email
	 */
	public function web_change_email($user_id,$password,$email){
		
		$check = false;
		$user_id = intval($user_id);		
		$this->error_array = array();
		$this->error_code_array = array();		
		if($user_id > 0){
						
			$password = trim($password);
			$result = $this->field('user_id')->where ('user_id = '.$user_id.' and password = "'.addslashes(self::user_password_encode($password)).'" ')->find();
			if($result){					
				
				$check = $this->change_email($user_id,$email,'web');
			}else{
						
				$this->error_code_array['password'] = ERROR_PASSWORD;
				$this->error_array['password'] = '原密码错误';
			}			
		}else{
				
			$this->error_code_array['result'] = INVALID_DATA;
			$this->error_array['result'] = '非法数据';
		}
		return $check;
	}
	
	/**
	 * 修改用户邮箱
	 * @param unknown $user_id
	 * @param unknown $email
	 * @param string $client_type 'web'：网页版  'client'：客户端版
	 * @return boolean
	 */
	public function change_email($user_id,$email,$client_type = 'client'){
		
		$check = false;
		$user_id = intval($user_id);
		$email = trim($email);
		if($user_id > 0){
			
			$check_email = $this->check_add_email($email,true,$user_id);			
			if($check_email){
				
				$email_verify = $client_type == 'client' ? self::get_email_verify(0,13) : self::get_email_verify(0,6);				
				$update_array = array();
				$update_array['new_email'] = addslashes($email);
				$update_array['email_verify'] = addslashes($email_verify);
				$update_array['update_time'] = addslashes($this->current_datetime);
				$result = $this->where('user_id = '.$user_id)->data($update_array)->save();
				if($result === false){
						
					$this->error_code_array['result'] = ERROR_UPDATE_FAILED;
					$this->error_array['result'] = '更新失败';
				}else{
					
					$user_name = $this->get_user_name($user_id);
					if($client_type == 'client'){
						
						$type = 'change';
						$method_name = 'send_email';					
					}else{
						
						$type = 'web_change';
						$method_name = 'web_send_email';
					}		
					$check = $this->$method_name($user_id,$user_name,$email,$email_verify,$type,'','',NOT_IS_TXI,'');//邮件账号修改验证
				}
			}
			
		}else{
				
			$this->error_code_array['result'] = INVALID_DATA;
			$this->error_array['result'] = '非法数据';
		}
		return $check;
	}
	
	/*
	 * 修改用户手机号码
	 */
	public function change_tel($user_id,$tel){
		
		$check = false;
		$user_id = intval($user_id);
		$tel = trim($tel);
		
		if($user_id > 0){
		
			$check_tel = $this->check_add_tel($tel,true,$user_id);
			if($check_tel){
					
				$update_array = array();
				$update_array['user_tel'] = addslashes($tel);
				$update_array['update_time'] = addslashes($this->current_datetime);
				$result = $this->where('user_id = '.$user_id)->data($update_array)->save();				
				if($result === false){
			
					$this->error_code_array['result'] = ERROR_UPDATE_FAILED;
					$this->error_array['result'] = '更新失败';
				}else{
						
					$check = true;
				}
			}	
		}else{
		
			$this->error_code_array['result'] = INVALID_DATA;
			$this->error_array['result'] = '非法数据';
		}
		return $check;
	}
	
	/**
	  * 修改用户密码
	  **/
	public function change_password($user_id,$password,$other_array = array()){
		
		$check = false;
		$user_id = intval($user_id);
		$password = trim($password);
		$other_array = (array)$other_array;
		$source_password = isset($other_array['source_password']) ? trim($other_array['source_password']) : '';
		$confirm_password = isset($other_array['confirm_password']) ? trim($other_array['confirm_password']) : '';
		if($user_id > 0){
			
			if($password === $confirm_password){
				
				$check_valid = $this->is_password($password,true);
				if($check_valid){

					$result = $this->field('user_id')->where ('user_id = '.$user_id.' and password = "'.addslashes(self::user_password_encode($source_password)).'" ')->find();
					if($result){
						
						$update_array = array();
						$update_array['password'] = addslashes(self::user_password_encode($password));
						$update_array['update_time'] = addslashes($this->current_datetime);
						$result = $this->where('user_id = '.$user_id)->data($update_array)->save();
						if($result === false){
						
							$this->error_code_array['result'] = ERROR_UPDATE_FAILED;
							$this->error_array['result'] = '更新失败';
						}else{
						
							$check = true;
						}
					}else{
						
						$this->error_code_array['password'] = ERROR_PASSWORD;
						$this->error_array['password'] = '原密码错误';
					}
					
				}
			}else{
				
				$this->error_code_array['password'] = ERROR_PASSWORD_NOT_SAME;
				$this->error_array['password'] = '密码输入不一样';		
			}
			
		}else{
		
			$this->error_code_array['result'] = INVALID_DATA;
			$this->error_array['result'] = '非法数据';
		}
		return $check;
	}
	
	/**
	  * 用户完善资料
	  **/
	public function user_perfect($user_id,$user_name,$nick_name,$email,$password,$confirm_password){
		
		$check = false;
		$user_id = intval($user_id);
		
		if($user_id > 0){			
			
			$password = trim($password);
			$confirm_password = trim($confirm_password);
			if($password != $confirm_password){
				
				$this->error_code_array['result'] = ERROR_PASSWORD_NOT_SAME;
				$this->error_array['result'] = '密码输入不一样';
				return $check;
			}
			$error_status = true;
			$check_valid = $this->is_password($password,$error_status);
			if(!$check_valid){
				
				return false;
			}
			$user_name = trim($user_name);
			$check_add = $this->check_add_user_name($user_name,$error_status,$user_id);
			if($check_add){
				
				$email = trim($email);
				$check_add = $this->check_add_email($email,$error_status,$user_id);
				if(!$check_add){
					
					return $check;
				}
				$nick_name = trim($nick_name);
				$nick_name_len = strlen($nick_name);
				if($nick_name_len <= 0){
						
					$this->error_code_array['result'] = ERROR_NICK_NAME_EMPTY;
					$this->error_array['result'] = '昵称不能为空';
					return $check;
				}elseif($nick_name_len <= 1 || $nick_name_len > 60){
						
					$this->error_code_array['result'] = ERROR_NICK_NAME_INVALID_LEN;
					$this->error_array['result'] = '昵称长度在2到60位';
					return $check;
				}
				$check_nick_name = self::is_user_name($nick_name);
				if($check_nick_name){
					
					$update_array = array();	
					$email_verify = self::get_email_verify(0,13);
					$update_array['user_name'] = addslashes($user_name);
					$update_array['user_nickname'] = addslashes($nick_name);
					$update_array['password'] = addslashes(self::user_password_encode($password));
					$update_array['new_email'] = addslashes($email);
					$update_array['email_verify'] = addslashes($email_verify);
					$update_array['update_time'] = addslashes($this->current_datetime);					
					$result = $this->where('user_id = '.$user_id)->data($update_array)->save();				
					
					if($result !== false){
					
						$check = $this->send_email($user_id,$user_name,$email,$email_verify,'change');//邮件账号修改验证
						
					}else{
					
						$this->error_code_array['result'] = ERROR_MYSQL_EXE_REPORT;
						$this->error_array['result'] = '数据库报错';
					}				
				}else{
					
					$this->error_code_array['result'] = ERROR_NICK_NAME_INVALID;
					$this->error_array['result'] = '昵称不合法字符';
				}
				
			}	
		}else{
		
			$this->error_code_array['result'] = INVALID_DATA;
			$this->error_array['result'] = '非法数据';
		}
		return $check;
	}
	
	/**
	  * 用户注册
	  * 
	  **/
	public function client_register($user_name,$password,$confirm_password,$email = '',$tel = '',$client_ip,$register_type){
		
		$user_id = 0;
		$check = false;		
		$user_status = 0;
		$login_count = 0;
		$check_valid = false;
		$error_status = true;
		$check_verify = false;
		$check_email_send = false;		
		$register_type = intval($register_type);
			
		switch($register_type){
			
			case REGISTER_CLIENT_MAIL:
			case REGISTER_WEB_MAIL:				
					
					$tel = '';
					$user_status = 1;
					$login_count = 1;
					$email = trim($email);
					$check_verify = true;
					$check_email_send = true;
					$check_valid = $this->check_add_email($email,$error_status,$user_id);
					break;
			case REGISTER_CLIENT_TEL:
			case REGISTER_WEB_TEL:				
					
					$email = '';
					$tel = trim($tel);
					$user_status = 1;
					$login_count = 1;
					$check_valid = $this->check_add_tel($tel,$error_status,$user_id);
					break;
			default :
				
				$check_valid = false;
				$this->error_code_array['result'] = INVALID_DATA;
				$this->error_array['result'] = '非法数据';				
				break;
		}
		if($check_valid){
			
			$user_name = trim($user_name);			
			$check_valid = $this->check_add_user_name($user_name,$error_status,$user_id);
			if($check_valid){
				
				$password = trim($password);
				$confirm_password = trim($confirm_password);
				if($password === $confirm_password){
					
					$check_valid = $this->is_password($password,$error_status);
					if($check_valid){
						
						$add_array = array();
						$client_ip = trim($client_ip);
						$email_verify = $check_email_send ? self::get_email_verify(0,13): '';
						
						$add_array['user_name'] = addslashes($user_name);
						$add_array['user_nickname'] = $add_array['user_name'];
						$add_array['password'] = addslashes(self::user_password_encode($password));
						$add_array['email'] = addslashes($email);						
						$add_array['user_tel'] = addslashes($tel);
						$add_array['is_delete'] = 0;
						$add_array['user_status'] = $user_status;
						$add_array['login_count'] = $login_count;
						$add_array['login_prev_ip'] = '0.0.0.0';
						$add_array['login_last_ip'] = $client_ip;
						$add_array['portrait_img_url'] = $this->default_portrait_img_url;						
						$add_array['add_time'] = addslashes($this->current_datetime);
						$add_array['update_time'] = $add_array['add_time'];
						$add_array['email_verify'] = addslashes($email_verify);
						
						if($user_status === 1 ){
							
							$add_array['login_prev_datetime'] = $add_array['add_time'];
							$add_array['login_last_datetime'] = $add_array['add_time'];
						}														
						$result = $this->data($add_array)-> add();
						//var_dump($result);
						if($result){
							
							$add_array = array();
							$user_id = intval($result);
							$add_array['user_id'] = $user_id;
							$add_array_extend = $add_array;
							$user_info_object = new UserInfoModel();
							$user_extend_object = new UserExtendModel();
							$user_info_object->data($add_array)-> add();
							$user_extend_object->data($add_array_extend)-> add();
							$this->user_id = $user_id;
							if($user_status === 1 && !$check_verify){
								
								$check = true;
							}elseif($check_email_send){								
													
								$this->error_code_array['result'] = ERROR_EMAIL_NOT_VERIFY;
								$this->error_array['result'] = '邮箱没有验证，请验证邮箱';		
								
								$check_send_email = $this->send_email($user_id,$user_name,$email,$email_verify,'add');//邮件激活验证
								if(!$check_send_email){
									
									$this->error_code_array['result'] = ERROR_SEND_EMAIL_FAILED;
									$this->error_array['result'] = '邮件发送失败';
								} 
							}
												
						}else{
							
							$this->error_code_array['result'] = ERROR_MYSQL_EXE_REPORT;
							$this->error_array['result'] = '数据库报错';
						}											
					}
				}else{
					
					$this->error_code_array['result'] = ERROR_PASSWORD_NOT_SAME;
					$this->error_array['result'] = '密码输入不一样';
				}
			}
			
			
		}
		return $check;
	}
	
	/*
	 * 用户登陆
	 */
	public function client_login($user_name,$user_pass,$api_id,$login_type,$client_ip,$other_array = array()){
		
		$check = false;
		$user_name = trim($user_name);
		$login_type = intval($login_type);
		switch($login_type){
			
			case LOGIN_CLIENT:
			case LOGIN_WEB:
				
					$check = $this->user_login($user_name,$user_pass,$login_type,$client_ip,$other_array);
					break;
			case LOGIN_API_QQ:
			case LOGIN_API_WEIBO:
			case LOGIN_API_WEIXIN:
			case LOGIN_API_UUID:
			case LOGIN_API_QQZONE:
			case LOGIN_API_TENCENT:
				
					$check = $this->client_api_login($user_name,$api_id,$login_type,$client_ip,$other_array);
					break;
			default :
				
				$check = false;
				$this->error_code_array['result'] = INVALID_DATA;
				$this->error_array['result'] = '非法数据';				
				break;
				
		}
		
		return $check;
	}
	
	/*
	 * 一般用户登录，需要密码
	 */
	public function user_login($user_name,$user_pass,$login_type,$client_ip,$other_array = array()){
		
		$check = false;
		$check_login_type = true;
		$user_name = trim($user_name);
		$user_pass = trim($user_pass);
		$login_type = intval($login_type);
		$other_array = check_array_valid($other_array) ? $other_array : array();
		
		switch($login_type){
		
			case LOGIN_CLIENT:
			case LOGIN_WEB:
		
				$check_login_type = true;
				break;
			default :
		
				$check_login_type = false;
				break;
		}
		
		if($check_login_type){
			
			if(strlen($user_name) <= 0){
			
				$this->error_code_array['result'] = ERROR_USER_EMPTY;
				$this->error_array['result'] = '用户名不能为空，请输入，谢谢';
				return $check;
			}
				
			$check_valid = $this->is_password($user_pass,true);
			if(!$check_valid){
				
				return $check;
			}
			
			$result = array();
			$field_name = self::is_mail($user_name) ? 'email' : (self::is_tel($user_name) ? 'user_tel' : '');
			if(strlen($field_name) > 0){
				
				$result = $this->field('user_id,is_delete,user_status,email_verify,login_last_ip,login_last_datetime,login_count')->where($field_name.' = "'.addslashes($user_name).'" and password = "'.addslashes(self::user_password_encode($user_pass)).'"')->find();				
			}		
			
			if(!check_array_valid($result)){
				
				if(!self::is_user_name($user_name)){
				
					$this->error_code_array['user_name'] = ERROR_USER_PASSWORD_NOT_MATCH;
					$this->error_array['user_name'] = '用户名或密码错误，请核对后重新输入，谢谢';
					return $check;
				}
				$result = $this->field('user_id,is_delete,user_status,email_verify,login_last_ip,login_last_datetime,login_count')->where('user_name = "'.addslashes($user_name).'" and password = "'.addslashes(self::user_password_encode($user_pass)).'"')->find();
			}		
			
			if(check_array_valid($result)){
				
				$is_delete = intval($result['is_delete']);
				if($is_delete != 0){
					
					$this->error_code_array['result'] = ERROR_LOGIN_FAILED;
					$this->error_array['result'] = '登录失败';
					
					return $check;
				}
				
				$user_id = intval($result['user_id']);
				$status = intval($result['user_status']);
				if($status === 1){
					
					$update_array = array();
					$update_array['login_count'] = intval($result['login_count']) + 1;
					$update_array['login_prev_ip'] = $result['login_last_ip'];
					$update_array['login_last_ip'] = addslashes(trim($client_ip));
					$update_array['login_prev_datetime'] = $result['login_last_datetime'];
					$update_array['login_last_datetime'] = addslashes($this->current_datetime);					
					$result = $this->where('user_id = '.$user_id)->data($update_array)->save();
					if($result !== false){
						
						$this->user_id = $user_id;
						$check = true;
					}else{
						
						$this->error_code_array['result'] = ERROR_MYSQL_EXE_REPORT;
						$this->error_array['result'] = '数据库报错';
					}
					
				}else{
					
					$email_verify = trim($result['email_verify']);
					if(strlen($email_verify) > 1){
						
						$this->error_code_array['result'] = ERROR_EMAIL_NOT_VERIFY;
						$this->error_array['result'] = '邮箱没有验证激活，请验证邮箱';
					}else{
						
						$this->error_code_array['result'] = ERROR_BLACKLIST;
						$this->error_array['result'] = '该用户被管理员拉入黑名单，请联系管理员';
					}
				}
				
			}else{
				
				$this->error_code_array['result'] = ERROR_USER_PASSWORD_NOT_MATCH;
				$this->error_array['result'] = '用户名或密码错误，请核对后重新输入，谢谢';
			}
			
		}else{
			
			$this->error_code_array['result'] = INVALID_DATA;
			$this->error_array['result'] = '非法数据';
		}
		return $check;
	}
	
	/*
	 * 客户端api（包括第三方）登陆，需要api_id
	 */
	public function client_api_login($user_name,$api_id,$login_type,$client_ip,$other_array = array()){
		
		$check = false;
		$check_login_type = true;
		$user_name = trim($user_name);
		$api_id = trim($api_id);
		$login_type = intval($login_type);
		$other_array = check_array_valid($other_array) ? $other_array : array();
		
		$api_fileld_name = '';
		switch($login_type){
		
			case LOGIN_API_QQ:
		
				$api_fileld_name = 'api_qq_id';
				break;
			case LOGIN_API_WEIBO:
		
				$api_fileld_name = 'api_weibo_id';
				break;
			case LOGIN_API_WEIXIN:
		
				$api_fileld_name = 'api_weixin_id';
				break;
			case LOGIN_API_UUID:
		
				$api_fileld_name = 'api_uuid_id';
				break;
			case LOGIN_API_QQZONE:
		
				$api_fileld_name = 'api_qqzone_id';
				break;
			case LOGIN_API_TENCENT:
		
				$api_fileld_name = 'api_tencent_id';
				break;
			default :
		
				$check_login_type = false;
				break;
		}
		
		if($check_login_type){
				
			if(strlen($user_name) <= 0){
				
				$this->error_code_array['result'] = ERROR_USER_EMPTY;
				$this->error_array['result'] = '用户名不能为空，请输入，谢谢';
				return $check;
			}

			if(strlen($api_id) > 1){
				
				$result = $this->field('user_id,user_status,is_delete,email,login_last_ip,login_last_datetime,login_count')->where($api_fileld_name.' = "'.addslashes($api_id).'" ')->find();
				
				if(check_array_valid($result)){
					
					$is_delete = intval($result['is_delete']);
					$status = intval($result['user_status']);
					if($is_delete === 0){
						
						$user_id = intval($result['user_id']);
						$email = trim($result['email']);
						
						$update_array = array();
						$update_array['login_count'] = intval($result['login_count']) + 1;
						$update_array['login_prev_ip'] = $result['login_last_ip'];
						$update_array['login_last_ip'] = addslashes(trim($client_ip));
						$update_array['login_prev_datetime'] = $result['login_last_datetime'];
						$update_array['login_last_datetime'] = addslashes($this->current_datetime);
						$result = $this->where('user_id = '.$user_id)->data($update_array)->save();
						if($result !== false){
								
							$this->user_id = $user_id;
							if(strlen($email) <= 0){
								
								$this->error_code_array['result'] = IS_PERFECT;
								$this->error_array['result'] = '需要完善资料';
							}else{
								
								$check = true;
							}
							
						}else{
								
							$this->error_code_array['result'] = ERROR_MYSQL_EXE_REPORT;
							$this->error_array['result'] = '数据库报错';
						}
					}elseif($status === 0){
						
						$this->error_code_array['result'] = ERROR_BLACKLIST;
						$this->error_array['result'] = '该用户被管理员拉入黑名单，请联系管理员';
					}else{
						
						$this->error_code_array['result'] = ERROR_LOGIN_FAILED;
						$this->error_array['result'] = '登录失败';
					}
								
				}else{
					
					//未注册情况下注册
					$id = 0;
					$rand_max = 100000;
					$user_name_max_rand_object = new \Home\Model\UserNameMaxRandModel(); 				   
				    $result = $user_name_max_rand_object->field('id,user_name_max')->find();
				    if(check_array_valid($result)){
				    	
				    	$id = intval($result['id']);
				    	$rand_max = $result['user_name_max'];				    	
				    }
				    $rand_max += rand(1,999);		
				    $check_exists = $this->check_add_user_name($rand_max);
				    if($check_exists){
				    	
				    	$rand_max += rand(1,999);
				    }  
				    $add_array = array();
				    
				    $add_array['user_name'] = $rand_max;
				    $add_array['user_nickname'] = $rand_max;
				    $add_array['password'] = '';
				    $add_array['email'] = '';
				    $add_array['user_tel'] = '';
				    $add_array['is_delete'] = 0;
				    $add_array['user_status'] = 1;
				    $add_array['login_count'] = 1;
				    $add_array['login_prev_ip'] = '0.0.0.0';
				    $add_array[$api_fileld_name] = addslashes($api_id);
				    $add_array['login_last_ip'] = addslashes(trim($client_ip));
				    $add_array['portrait_img_url'] = $this->default_portrait_img_url;
				    $add_array['add_time'] = addslashes($this->current_datetime);
				    $add_array['update_time'] = $add_array['add_time'];
				    $add_array['login_prev_datetime'] = $add_array['add_time'];
				    $add_array['login_last_datetime'] = $add_array['add_time'];    
				    
				    $result = $this->data($add_array)-> add();				   	
				    if($result){
				    	
				    	$user_id = intval($result);
				    	$this->user_id = $user_id;
				    	
				    	$add_array = array();				    	
				    	$add_array['user_id'] = $user_id;
				    	$add_array_extend = $add_array;
				    	$user_info_object = new UserInfoModel();
				    	$user_extend_object = new UserExtendModel();
				    	$user_info_object->data($add_array)-> add();
				    	$user_extend_object->data($add_array_extend)-> add();				    	
				    	
				    	if($id > 0){
				    		
				    		$update_array = array();
				    		$update_array['user_name_max'] = $rand_max;
				    		$user_name_max_rand_object->where('id = '.$id)->data($update_array)->save();
				    	}else{
				    		
				    		$add_array = array();
				    		$add_array['user_name_max'] = $rand_max;
				    		$user_name_max_rand_object->data($add_array)-> add();
				    	}
				    	
				    	
				    	$check = false;
				    	$update_array = array();
				    	$check_add = !is_numeric($user_name) && $this->check_add_user_name($user_name,false,$user_id);
				    	$user_name = addslashes($user_name);
				    	if($check_add){
				    		
				    		$update_array['user_name'] = $user_name;
				    	}
				    	$update_array['user_nickname'] = $user_name;
				    	$result = $this->where('user_id = '.$user_id)->data($update_array)->save();
				    	
				    	$this->error_code_array['result'] = IS_PERFECT;
				    	$this->error_array['result'] = '需要完善资料';
				    }else{
				    	
				    	$this->error_code_array['result'] = ERROR_MYSQL_EXE_REPORT;
				    	$this->error_array['result'] = '数据库报错';
				    }
					
				}
				
			}else{
				
				$this->error_code_array['result'] = INVALID_DATA;
				$this->error_array['result'] = '非法数据';
			}
		}else{
				
			$this->error_code_array['result'] = INVALID_DATA;
			$this->error_array['result'] = '非法数据';
		}
		
		return $check;
	}

	/*
	 * 判别用户名写法合法性
	 */
	public static function is_user_name($user_name){
		
		$check = false;
		$user_name = trim($user_name);
		$user_name = strlen($user_name) > 1 ? $user_name : '';
		if(!empty($user_name)){
			
			$check = !is_numeric($user_name) && is_user_name($user_name);
		}		
		return $check;
	}
	
	/*
	 * 判别是否为tel
	 */
	public static function is_tel($tel){
		
		$check = false;
		$tel = trim($tel);
		$tel = strlen($tel) > 1 ? $tel : '';
		if(!empty($tel)){
			
			$check = is_tel($tel);
		}
		return $check;
	}
	
	/*
	 * 判别是否为email
	 */
	public static function is_mail($email){
		
		$check = false;
		$email = trim($email);
		$email = strlen($email) > 0 ? $email : '';
		if(!empty($email)){
			
			$check = is_email($email);
		}
		return $check;
	}
	
	/*
	 * 密码合法性判断
	 */
	public function is_password($password,$error_status = false){
		
		$check = false;
		$password = trim($password);
		$password_len = strlen($password);
		if($password_len <= 0){
			
			if($error_status){
				
				$this->error_code_array['password'] = ERROR_PASSWORD_EMPTY;
				$this->error_array['password'] = '密码不能为空，请输入，谢谢';
			}
			return $check;
		}elseif($password_len < 6 || $password_len > 18){
			
			if($error_status){
			
				$this->error_code_array['password'] = ERROR_PASSWORD_INVALID_LEN;
				$this->error_array['password'] = '密码长度在6到18位，字母区分大小写';
			}
			return $check;
		}
		
		if(preg_match('/^[a-z0-9]{6,18}$/i',$password)){
			
			$check = true;
		}elseif($error_status){
			
			$this->error_code_array['password'] = ERROR_PASSWORD_INVALID;
			$this->error_array['password'] = '密码不合法';
		}			
		return $check;
	}
	
	/*
	 * 原始密码加密后放于数据库
	 */
	protected static function user_password_encode($password){
		
		$encode_password = '';
		$password = trim($password);
		if(strlen($password) > 0){
			
			$md5_pass = md5($password);
			$encode_password = md5($md5_pass.substr($md5_pass,2,8).substr($md5_pass,6,5));
		}
				
		return $encode_password;
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
	
	/*
	 * 获取用户ID
	 */	
	public function get_user_id(){
		
		return intval($this->user_id);
	}
	
	/*
	 * 生成邮件验证码
	 */
	public static function get_email_verify($start = 0,$len = 13){
		
		$start = intval($start);
		$start = $start > 0 && $start <= 30 ? $start : 0;
		$len = intval($len);
		$len = $len >= 2 ? $len : 13;
		
		return substr(md5(uniqid()),$start,$len);
	}
	
	/**
	 * 判别是否已经邮箱激活过了
	 * @param unknown $user_id
	 * @return boolean
	 */
	public function check_is_email_verify($user_id){
		
		$check = false;
		$user_id = intval($user_id);
		if($user_id > 0){
			
			$total_count = $this->where ('user_id = '.$user_id.' and is_delete = 0 and is_email_verify = 1')->count();
			$check = intval($total_count) > 0 ? true : false;
		}
		
		return $check;
	}
	
	/**
	 * 邮件验证
	 * @param unknown $user_id
	 * @param unknown $email_verify
	 * @param unknown $type
	 * @return boolean
	 */
	public function email_verify($user_id,$email_verify,$type){
		
		$check = false;
		$type_array = array('add','change','web_change','web_verify','change_recover');
		$type = trim($type);
		$user_id = intval($user_id);		
		$email_verify = trim($email_verify);		
		if($user_id > 0 && strlen($email_verify) > 1 && in_array($type,$type_array)){
				
			$result = $this->field('email,email_verify,new_email,user_status')->where ('user_id = '.$user_id.' and is_delete = 0')->find();
			if(check_array_valid($result)){
				
				$email = stripslashes(trim($result['email']));
				$new_email = stripslashes(trim($result['new_email']));
				$source_email_verify = stripslashes(trim($result['email_verify']));
				$email_check_valid = self::is_mail($email);			
				$new_email_check_valid = self::is_mail($new_email); 
				$source_verify_len = strlen($source_email_verify);
				
				$update_array = array();
				$update_array['email_verify'] = '';
				$update_array['is_email_verify'] = 1;
				
				if($source_verify_len == 1){			
									
					$update_array['new_email'] = '';
					$update_array['user_status'] = 1;		
					
					if($email_check_valid && $new_email_check_valid){
												
						$check_add = $this->check_add_email($new_email,false,$user_id);
						if($check_add){
							
							$check = true;
							$update_array['email'] = $new_email;
						}
						
					}
					$result = $this->where('user_id = '.$user_id)->data($update_array)->save();
					$check = $check && $result !== false ? true : false;
					return $check;
				}
				if($source_email_verify != $email_verify){
					
					return $check;
				}
				$check_valid = true;
				$check_update = true;
				
				switch($type){
				
					case 'add':				
						
						$update_array['user_status'] = 1;
						break;					
					case 'change':
					case 'web_change':
											
						$check_add = $this->check_add_email($new_email,false,$user_id);
						if($check_add){
							
							if($email_check_valid){
								
								$user_name = $this->get_user_name($user_id);
								if($type == 'change'){
									
									$method_name = 'send_email';
									$mail_type = 'change_success';									
								}else{
									
									$method_name = 'web_send_email';
									$mail_type = 'web_change_success';
								}								
								$check_valid = $this->$method_name($user_id,$user_name,$email,$email_verify,$mail_type);//邮件账号修改成功
								if($check_valid){
									
									unset($update_array['email_verify']);
									$update_array['email'] = $new_email;
									$update_array['new_email'] = $email;
								}
							}else{
								
								$update_array['email'] = $new_email;
								$update_array['new_email'] = '';
							}						
						}else{
							
							$check_valid = false;
						}
						break;
					case 'change_recover':					
						
						$check_add = $this->check_add_email($new_email,false,$user_id);
						if($check_add){
								
							$update_array['email'] = $new_email;
							$update_array['new_email'] = '';
						}else{
								
							$check_valid = false;
						}
						break;
					case 'web_verify':
						
						break;
					default :
						
						$check_update = false;
						$check_valid = false;
						break;	
				
				}
				
				if($check_update){
					
					$update_array['update_time'] = addslashes($this->current_datetime);
					$result = $this->where('user_id = '.$user_id)->data($update_array)->save();
					$check = $check_valid && $result !== false ? true : false;									
				}
			}				
		}
		return $check;
	}
	
	/**
	 * 网页版邮件发送
	 * @param unknown $user_id
	 * @param unknown $user_name
	 * @param unknown $email
	 * @param unknown $email_verify
	 * @param unknown $type
	 * @param string $send_start_time
	 * @param string $send_end_time
	 * @param string $send_type
	 * @param string $verify_url
	 * @return boolean
	 */
	public function web_send_email($user_id,$user_name,$email,$email_verify,$type,$send_start_time = '',$send_end_time = '',$send_type = NOT_IS_TXI,$verify_url=''){
		
		$check = false;
		$check_send = true;
		$type_array = array('add','web_change','change','client_verify','web_verify','change_success','web_change_success');
		$user_id = intval($user_id);
		$email = trim($email);
		$email_verify = trim($email_verify);
		$type = trim($type);
		$user_name = trim($user_name);
		if($user_id > 0 && strlen($user_name) > 0 && self::is_mail($email) && (strlen($email_verify) > 1 || strlen($verify_url) > 1)&& in_array($type,$type_array)){
				
			$title = '';
			$message = '';
			$accout = $email;
			$template_info_array = array();
			$template_code = '';
			$verify_url = trim($verify_url);
			$template_model_object = new \Home\Model\TemplateModel();
				
			if(empty($verify_url) && $type != 'web_change'){
				
				if($type != 'client_verify' && $type != 'change_success' && $type != 'web_change_success'){
				
					$verify_url = getBaseURL().MODULE_NAME.'/User/mailVerify/type/'.$type.'/id/'.$user_id.'/verifycode/'.$email_verify.'/l/'.LANG_SET;
				}elseif($type == 'change_success'){
				
					$verify_url = getBaseURL().MODULE_NAME.'/User/mailVerify/type/change_recover/id/'.$user_id.'/verifycode/'.$email_verify.'/l/'.LANG_SET;
				}elseif($type == 'web_change_success'){
					
					$verify_url = getBaseURL().MODULE_NAME.'/Index/mailVerify/type/change_recover/uid/'.$user_id.'/verifycode/'.$email_verify.'/l/'.LANG_SET;
				}
			}			
		
			switch($type){
		
				case 'add':
						
					$template_code = 'REGISTER_EMAIL';
					$template_info_array = $template_model_object->get_info_to_template_code($template_code,true);
					if(!check_array_valid($template_info_array)){
		
						$title_subject = LANG_SET == 'zh-cn' ? '邮件激活账号':'Nearly there! Complete your Review Centre Registration‏';
						$title .= $title_subject;
						$message = getMessageBody($email,$accout,$verify_url);
					}
		
					break;				
				case 'change':
						
					$template_code = 'CHANGE_EMAIL';
					$template_info_array = $template_model_object->get_info_to_template_code($template_code,true);
					if(!check_array_valid($template_info_array)){
		
						$title_subject = LANG_SET == 'zh-cn' ? '修改邮件账号验证':'Modify email account verification';
						$title .= $title_subject;
						$message = getUrlMessageBody($verify_url);
					}
					break;
				case 'web_change':
				
					$template_code = 'WEB_CHANGE_EMAIL';
					$template_info_array = $template_model_object->get_info_to_template_code($template_code,true);
					if(!check_array_valid($template_info_array)){
				
						$title_subject = LANG_SET == 'zh-cn' ? '修改邮件账号验证':'Modify email account verification';
						$title .= $title_subject;
						$message = getUrlMessageBody($verify_url);
					}
					break;
				case 'web_change_success':
				case 'change_success':
						
					$template_code = 'CHANGE_SUCCESS_EMAIL';
					$template_info_array = $template_model_object->get_info_to_template_code($template_code,true);
					if(!check_array_valid($template_info_array)){
							
						$title_subject = LANG_SET == 'zh-cn' ? '修改邮箱账号成功':'Modify mail account successfully';
						$title .= $title_subject;
						$message = getUrlMessageBody($verify_url);
					}
					break;
				case 'client_verify':
						
					$verify_url = '';
					$template_code = 'CLIENT_VERIFY';
					$template_info_array = $template_model_object->get_info_to_template_code($template_code,true);
					if(!check_array_valid($template_info_array)){
							
						$title_subject = LANG_SET == 'zh-cn' ? '忘记密码,重置密码验证':'Forgot your password,password reset verification';
						$title .= $title_subject;
						$message = getUrlMessageBody($verify_url);
					}
					break;
				case 'web_verify':
						
					$template_code = 'WEB_VERIFY';
					$template_info_array = $template_model_object->get_info_to_template_code($template_code,true);
					if(!check_array_valid($template_info_array)){
		
						$title_subject = LANG_SET == 'zh-cn' ? '忘记密码,重置密码验证':'Forgot your password,password reset verification';
						$title .= $title_subject;
						$message = getUrlMessageBody($verify_url);
					}
					break;
				default :
						
					$check_send = false;
					break;
			}
				
		}else{
				
			$check_send = false;
		}
		
		if($check_send){
				
			if(check_array_valid($template_info_array)){
					
				$title = stripslashes($template_info_array['template_title']);
				$content = stripslashes($template_info_array['template_content']);
				$view_object = new \Think\View();
				$view_object->assign('email',$email);
                                $view_object->assign('mail',$email);
				$view_object->assign('account',$user_name ? $user_name :$accout);
				$view_object->assign('user_name',$user_name);
				$view_object->assign('email_verify',$email_verify);
				$view_object->assign('verify_url',$verify_url);
				$title = $view_object->fetch('',$title,'');
				$message = $view_object->fetch('',$content,'');
			}
				
			//tmp start
			//var_dump($message);
			//$send_type = NOT_IS_TXI;
			//tmp end
			$send_type = intval($send_type);
			if($send_type === IS_TXI){
		
				$check = SendMail($email,$title,$message);
			}else{
		
				$add_array = array();
				$send_start_time = trim($send_start_time);
				$send_message_object = new \Home\Model\SendMessageModel();
				$add_array['user_id'] = $user_id;
				$add_array['user_name'] = addslashes($user_name);
				$add_array['email'] = addslashes($email);
				$add_array['tel'] = '';
				$add_array['title'] = addslashes($title);
				$add_array['content'] = addslashes($message);
				$add_array['type'] = 0;
				$add_array['is_send'] = 0;
				$add_array['is_send'] = 0;
				$add_array['send_time'] = '0000-00-00 00:00:00';
				$add_array['send_start_time'] = strlen($send_start_time) > 0 && is_date($send_start_time) ? $send_start_time : '0000-00-00 00:00:00';
				$add_array['send_end_time'] = strlen($send_end_time) > 0 && is_date($send_end_time) ? $send_end_time : '0000-00-00 00:00:00';
				$add_array['add_time'] = $this->current_datetime;
				$result = $send_message_object->data($add_array)->add();
				if($result){
						
					$check = true;
				}
			}
		}
		return $check;		
	}
	
	/*
	 * 邮件发送
	 */
	public function send_email($user_id,$user_name,$email,$email_verify,$type,$send_start_time = '',$send_end_time = '',$send_type = NOT_IS_TXI){
		
		$check = false;
		$check_send = true;
		$type_array = array('add','change','client_verify','web_verify','change_success');
		$user_id = intval($user_id);
		$email = trim($email);
		$email_verify = trim($email_verify);
		$type = trim($type);
		$user_name = trim($user_name);
		if($user_id > 0 && strlen($user_name) > 0 && self::is_mail($email) && strlen($email_verify) > 1 && in_array($type,$type_array)){
			
			$title = '';
			$message = '';
			$accout = $email;
			$template_info_array = array();
			$template_code = $verify_url = '';
			$template_model_object = new \Home\Model\TemplateModel();
			
			if(!defined('LANG_SET')){
			
				define('LANG_SET',C('DEFAULT_LANG',null,'zh-cn'));
			}
			if($type != 'client_verify' && $type != 'change_success'){
				
				$verify_url = getBaseURL().MODULE_NAME.'/User/mailVerify/type/'.$type.'/id/'.$user_id.'/verifycode/'.$email_verify.'/l/'.LANG_SET;
			}elseif($type == 'change_success'){
				
				$verify_url = getBaseURL().MODULE_NAME.'/User/mailVerify/type/change_recover/id/'.$user_id.'/verifycode/'.$email_verify.'/l/'.LANG_SET;
			}
						
			switch($type){
				
				case 'add':
					
					$template_code = 'REGISTER_EMAIL';
					$template_info_array = $template_model_object->get_info_to_template_code($template_code,true);
					if(!check_array_valid($template_info_array)){					
						
						$title_subject = LANG_SET == 'zh-cn' ? '邮件激活账号':'Nearly there! Complete your Review Centre Registration‏';
						$title .= $title_subject;
						$message = getMessageBody($email,$accout,$verify_url);
					}
												
					break;
				case 'change':
					
					$template_code = 'CHANGE_EMAIL';
					$template_info_array = $template_model_object->get_info_to_template_code($template_code,true);
					if(!check_array_valid($template_info_array)){
						
						$title_subject = LANG_SET == 'zh-cn' ? '修改邮件账号验证':'Modify email account verification';
						$title .= $title_subject;
						$message = getUrlMessageBody($verify_url);
					}					
					break;
				case 'change_success':
					
					$template_code = 'CHANGE_SUCCESS_EMAIL';
					$template_info_array = $template_model_object->get_info_to_template_code($template_code,true);
					if(!check_array_valid($template_info_array)){
					
						$title_subject = LANG_SET == 'zh-cn' ? '修改邮箱账号成功':'Modify mail account successfully';
						$title .= $title_subject;
						$message = getUrlMessageBody($verify_url);
					}
					break;
				case 'client_verify':
					
					$verify_url = '';
					$template_code = 'CLIENT_VERIFY';
					$template_info_array = $template_model_object->get_info_to_template_code($template_code,true);
					if(!check_array_valid($template_info_array)){
					
						$title_subject = LANG_SET == 'zh-cn' ? '忘记密码,重置密码验证':'Forgot your password,password reset verification';
						$title .= $title_subject;
						$message = getUrlMessageBody($verify_url);
					}
					break;
				case 'web_verify':
							
						$template_code = 'WEB_VERIFY';
						$template_info_array = $template_model_object->get_info_to_template_code($template_code,true);
						if(!check_array_valid($template_info_array)){
								
							$title_subject = LANG_SET == 'zh-cn' ? '忘记密码,重置密码验证':'Forgot your password,password reset verification';
							$title .= $title_subject;
							$message = getUrlMessageBody($verify_url);
						}
						break;
				default :
					
					$check_send = false;
					break;				
			}
					
		}else{
			
			$check_send = false;
		}
		
		if($check_send){
			
			if(check_array_valid($template_info_array)){
			
				$title = stripslashes($template_info_array['template_title']);
				$content = stripslashes($template_info_array['template_content']);
				$view_object = new \Think\View();
				$view_object->assign('email',$email);
                                $view_object->assign('mail',$email);
				$view_object->assign('account',$user_name ? $user_name :$accout);
				$view_object->assign('user_name',$user_name);
				$view_object->assign('email_verify',$email_verify);				
				$view_object->assign('verify_url',$verify_url);
				$title = $view_object->fetch('',$title,'');
				$message = $view_object->fetch('',$content,'');
			}		
			
			//tmp start			
			//var_dump($message);
			//$send_type = NOT_IS_TXI;
			//tmp end			
			$send_type = intval($send_type);
			if($send_type === IS_TXI){
				
				$check = SendMail($email,$title,$message);				
			}else{
				
				$add_array = array();
				$send_start_time = trim($send_start_time);
				$send_message_object = new \Home\Model\SendMessageModel();
				$add_array['user_id'] = $user_id;
				$add_array['user_name'] = addslashes($user_name);
				$add_array['email'] = addslashes($email);
				$add_array['tel'] = '';
				$add_array['title'] = addslashes($title);
				$add_array['content'] = addslashes($message);
				$add_array['type'] = 0;
				$add_array['is_send'] = 0;
				$add_array['is_send'] = 0;
				$add_array['send_time'] = '0000-00-00 00:00:00';
				$add_array['send_start_time'] = strlen($send_start_time) > 0 && is_date($send_start_time) ? $send_start_time : '0000-00-00 00:00:00';
				$add_array['send_end_time'] = strlen($send_end_time) > 0 && is_date($send_end_time) ? $send_end_time : '0000-00-00 00:00:00';
				$add_array['add_time'] = $this->current_datetime;
				$result = $send_message_object->data($add_array)->add();				
				if($result){
					
					$check = true;
				}
			}			
		}
		return $check;
	}
	
	/**
	 * 验证用户密码
	 * @param unknown $user_id
	 * @param unknown $password
	 * @return boolean
	 */
	public function verify_user_password($user_id,$password){
		
		$check = false;
		$user_id = intval($user_id);
		$this->error_array = array();
		$this->error_code_array = array();		
		$password = trim($password);
		if($user_id > 0){
			
			$check_valid = $this->is_password($password,true);
			if($check_valid){
				
				$password = addslashes(self::user_password_encode($password));				
				$result = $this->field('user_id')->where ('user_id = '.$user_id.' and password = "'.$password.'" ')->find();
				if($result){
			
					$check = true;
				}else{
					
					$this->error_array['result'] = '原密码错误';
					$this->error_code_array['result'] = ERROR_PASSWORD;
				}
			}
		}else{
			
			$this->error_array['result'] = '未登陆';
			$this->error_code_array['result'] = ERROR_NOT_LOGIN;
		}
		
		return $check;
	}
	
	/**
	 * 修改用户头像
	 * @param unknown $user_id
	 * @param string $upload_name
	 * @return multitype:boolean string
	 */
	public function change_portrait_img($user_id,$upload_name){		
		
		$portrait_img = '';
		$error_status = true;
		$user_id = intval($user_id);
		$this->error_array = array();
		$this->error_code_array = array();
		$info_array = array('check'=>false,'portrait_img'=>'');
		
		if($user_id > 0){
			
			$result = $this->field('user_id,portrait_img_url')->where ('user_id = '.$user_id)->find();
			if(check_array_valid($result)){
				
				$source_portrait_img_url = $result['portrait_img_url'];
				$info_array['portrait_img'] = getFullUrl($source_portrait_img_url);
				$upload_base_dir = str_replace('\\','/', $this->upload_base_dir);
				$check = $this->mk_dir($upload_base_dir,$this->mode,$error_status);
				$info_array['basedir'] = $upload_base_dir;
				if(!$check){
				
					return $info_array;
				}
				$upload_base_dir .= strval($user_id).'/';
				$check = $this->mk_dir($upload_base_dir,$this->mode,$error_status);
				$info_array['basediruser'] = $upload_base_dir;
				if(!$check){
						
					return $info_array;
				}
					
				$fh = @fopen($upload_base_dir.'index.html','ab');
				@fclose($fh);
				
				$check = $this->upload_file_process($upload_base_dir,$upload_name);
				if($check){
					
					$portrait_img_url = $this->portrait_img_url;
					$update_array = array();
					$update_array['portrait_img_url'] = addslashes($portrait_img_url);
					$update_array['update_time'] = addslashes($this->current_datetime);
					$result = $this->where('user_id = '.$user_id)->data($update_array)->save();
					if($result === false){

						$this->error_array['result'] = '保存失败';
						$this->error_code_array['result'] = ERROR_CONSERVE_FAILED;
						deleteAwsFile($portrait_img_url);
						if(file_exists($portrait_img_url)){
								
							@unlink($portrait_img_url);
						}						
						return $info_array;
					}else{
						
						if($source_portrait_img_url != $this->default_portrait_img_url && $source_portrait_img_url != $portrait_img_url){
								
							$source_portrait_img_path = BASE_DIR.$source_portrait_img_url;
							deleteAwsFile($source_portrait_img_url);
							if(file_exists($source_portrait_img_path)){
									
								@unlink($source_portrait_img_path);
							}
						}						
						$info_array = array('check'=>true,'portrait_img'=>getFullUrl($portrait_img_url));						
					}					
				}
			}else{
				
				$this->error_array['result'] = '非法数据';
				$this->error_code_array['result'] = INVALID_DATA;
			}			
		}else{
			
			$this->error_array['result'] = '未登陆';
			$this->error_code_array['result'] = ERROR_NOT_LOGIN;
		}
		
		return $info_array;
	}
	
	/**
	 * 上传文件处理
	 * @param unknown $file_dir
	 * @param string $upload_name
	 * @return boolean
	 */
	protected function upload_file_process($file_dir,$upload_name){
	
		$check = false;
		if(count($this->error_code_array) > 0 || count($this->error_array) > 0 || !is_dir($file_dir) || strlen($upload_name) <= 0 ){
	
			return $check;		
		}elseif(!isset($_FILES[$upload_name])){
	
			$this->error_array['result'] = '没有文件被上传';
			$this->error_code_array['result'] = ERROR_NOT_UPLOADED_FILE;
	
			return $check;
		}
		$check_upload = false;	
		$user_file = $_FILES[$upload_name];
		if($user_file['error'] > 0){	
			
			switch($user_file['error']){
	
				case 1:
	
					$this->error_array['result'] = '文件大小超过服务器限制';
					$this->error_code_array['result'] = ERROR_SERVER_LIMIT_SIZE;
					break;
				case 2:
	
					$this->error_array['result'] = '上传文件太大或者上传文件的大小超过了 HTML 表单中 MAX_FILE_SIZE 选项指定的值';
					$this->error_code_array['result'] = ERROR_FILE_MAX_SIZE;
					break;
				case 3:
	
					$this->error_array['result'] = '文件只加载了一部分！';
					$this->error_code_array['result'] = ERROR_FILE_LOAD_PART;
					break;
				case 4:
	
					$this->error_array['result'] = '文件加载失败！';
					$this->error_code_array['result'] = ERROR_FILE_LOAD_FAILED;
					break;
				case 5:
	
					$this->error_array['result'] = '未知错误';
					$this->error_code_array['result'] = ERROR_UNKNOWN;
					break;
				case 6:
	
					$this->error_array['result'] = '找不到临时文件夹';
					$this->error_code_array['result'] = ERROR_FINE_NOT_TMP_FILE;
					break;
				case 7:
	
					$this->error_array['result'] = '文件写入失败';
					$this->error_code_array['result'] = ERROR_CANT_WRITE;
					break;
			}
		}else{				
				
			if(is_uploaded_file($user_file['tmp_name'])){
	
				if($user_file['size'] > $this->max_size){	
					
					$this->error_array['result'] = '文件大小超过服务器限制';
					$this->error_code_array['result'] = ERROR_SERVER_LIMIT_SIZE;
				}else{
						
					$file_format = self::check_file_type($user_file['tmp_name'], $user_file['name'], $this->allow_file_types);
					//var_dump($file_format);
					//$file_type = $user_file['type'];
					if(strlen($file_format) > 0){
					
						//var_dump($user_file['tmp_name']);
						//var_dump($user_file['name']);
						
						$file_path = $file_dir.date('YmdHis',$this->current_time).strval(rand(111111,999999)).'.'.$file_format;
						if(!move_uploaded_file($user_file['tmp_name'],$file_path)){								
							
							$this->error_array['result'] = '文件创建失败';
							$this->error_code_array['result'] = ERROR_FILE_MOVE_FAILED;
							return false;
						}
						
						$path_array = explode('/',$file_path);
						$file_name = end($path_array);
						$prefix_path = substr($file_path,0,-strlen($file_name));
							
						$acl = 'public-read';						
						$aws_vendor_object = new \AwsVendor\AwsVendor(C('AWS_BUCKET'),C('AWS_BASE_PATH'));
						$aws_base_path = $aws_vendor_object->get_name('aws_base_path');
						
						$prefix_path_array = explode($aws_base_path,$prefix_path);
						$prefix_path = end($prefix_path_array);
							
						$pathToFile = $aws_vendor_object->get_aws_file_path($prefix_path,$file_name);
							
						$this->portrait_img_url = $pathToFile;
						$check = $aws_vendor_object->upload_process($file_path,$pathToFile,$acl);
						if(!$check){
	
							$error_array = $aws_vendor_object->get_error_array();
							$this->error_array['result'] = isset($error_array['result']) ? $error_array['result'] : '同步到亚马逊失败';
							$this->error_code_array['result'] = ERROR_SYNC_FAILED;
						}
	
						$check_upload = $check;						
					}else{
						
						$this->error_array['result'] = '文件类型不合法';
						$this->error_code_array['result'] = ERROR_FILE_TYPE;
					}	
				}
			}else{	
				
				$this->error_array['result'] = '非上传文件';
				$this->error_code_array['result'] = ERROR_FILE_NOT_UPLOADED;
			}
	
		}
		$check = $check_upload;
		return $check;
	
	}
	
	
	
	/**
	 * 创建目录
	 *
	 **/
	protected function mk_dir($dir,$mode,$error_status = false){
	
		$check = false;
		$error_status = (bool) $error_status;
		if(is_dir($dir) || @mkdir($dir,$mode,true)){
	
			$check = true;
		}
	
		if($error_status && !$check){
				
			$this->error_array['result'] = '目录不存在，且创建目录失败';
			$this->error_code_array['result'] = ERROR_MK_DIR;
		}
	
		return $check;
	}
	
	/**
	 * 检查文件类型
	 *
	 * @access      public
	 * @param       string      filename            文件名
	 * @param       string      realname            真实文件名
	 * @param       string      limit_ext_types     允许的文件类型
	 * @return      string
	 */
	public static function check_file_type($filename, $realname = '', $limit_ext_types = ''){
	
		if($realname){
		
			$extname = strtolower(substr($realname, strrpos($realname, '.') + 1));
		
		}else{
		
			$extname = strtolower(substr($filename, strrpos($filename, '.') + 1));
		}
	
		if($limit_ext_types && stristr($limit_ext_types, '|' . $extname . '|') === false){
		
			return '';
		}
	
		$str = $format = '';
	
		$file = @fopen($filename, 'rb');
		if($file){
		
			$str = @fread($file, 0x400); // 读取前 1024 个字节
			@fclose($file);
		
		}else{			
			
			if($extname == 'jpg' || $extname == 'jpeg' || $extname == 'gif' || $extname == 'png' || $extname == 'doc' ||
					$extname == 'xls' || $extname == 'txt'  || $extname == 'zip' || $extname == 'rar' || $extname == 'ppt' ||
					$extname == 'pdf' || $extname == 'rm'   || $extname == 'mid' || $extname == 'wav' || $extname == 'bmp' ||
					$extname == 'swf' || $extname == 'chm'  || $extname == 'sql' || $extname == 'cert'|| $extname == 'pptx' ||
					$extname == 'xlsx' || $extname == 'docx'){
				
				$format = $extname;
			}			
		}
	
		if($format == '' && strlen($str) >= 2 ){
		
			if(substr($str, 0, 4) == 'MThd' && $extname != 'txt'){
			
				$format = 'mid';
			}elseif (substr($str, 0, 4) == 'RIFF' && $extname == 'wav'){
			
				$format = 'wav';
			}elseif (substr($str ,0, 3) == "\xFF\xD8\xFF"){			
			
				$format = 'jpg';
			}elseif(substr($str ,0, 4) == 'GIF8' && $extname != 'txt'){			
			
				$format = 'gif';
			}elseif (substr($str ,0, 8) == "\x89\x50\x4E\x47\x0D\x0A\x1A\x0A"){			
			
				$format = 'png';
			}elseif (substr($str ,0, 2) == 'BM' && $extname != 'txt'){			
			
				$format = 'bmp';
			}elseif ((substr($str ,0, 3) == 'CWS' || substr($str ,0, 3) == 'FWS') && $extname != 'txt'){			
			
				$format = 'swf';
			}elseif (substr($str ,0, 4) == "\xD0\xCF\x11\xE0"){
			
			   // D0CF11E == DOCFILE == Microsoft Office Document
				if(substr($str,0x200,4) == "\xEC\xA5\xC1\x00" || $extname == 'doc'){
				
					$format = 'doc';
				}elseif(substr($str,0x200,2) == "\x09\x08" || $extname == 'xls'){				
				
					$format = 'xls';
				}elseif(substr($str,0x200,4) == "\xFD\xFF\xFF\xFF" || $extname == 'ppt'){
				
					$format = 'ppt';
				}
			}elseif(substr($str ,0, 4) == "PK\x03\x04"){
			
				if(substr($str,0x200,4) == "\xEC\xA5\xC1\x00" || $extname == 'docx'){
				
					$format = 'docx';
				}elseif(substr($str,0x200,2) == "\x09\x08" || $extname == 'xlsx'){				
				
					$format = 'xlsx';
				}elseif(substr($str,0x200,4) == "\xFD\xFF\xFF\xFF" || $extname == 'pptx'){
				
					$format = 'pptx';
				}else{
				
					$format = 'zip';
				}
			}elseif(substr($str ,0, 4) == 'Rar!' && $extname != 'txt'){
			
				$format = 'rar';
			}elseif(substr($str ,0, 4) == "\x25PDF"){
			
				$format = 'pdf';
			}elseif(substr($str ,0, 3) == "\x30\x82\x0A"){
			
				$format = 'cert';
			}elseif(substr($str ,0, 4) == 'ITSF' && $extname != 'txt'){
			
				$format = 'chm';
			}elseif (substr($str ,0, 4) == "\x2ERMF"){
			
				$format = 'rm';
			}elseif ($extname == 'sql'){
			
				$format = 'sql';
			}elseif ($extname == 'txt'){
			
				$format = 'txt';
			}
		}
	
		if($limit_ext_types && stristr($limit_ext_types, '|' . $format . '|') === false){
			
			$format = '';
		}
	
		return $format;
	}	
	
	/**
	 * 网页版重置密码
	 * @param unknown $user_id
	 * @param unknown $new_password
	 * @param unknown $confirm_password
	 * @return boolean
	 */
	public function webResetPassword($user_id,$new_password,$confirm_password){
	
		$check = false;
		$this->error_array = array();
		$this->error_code_array = array();
		$user_id = intval($user_id);
		if($user_id > 0){
	
			$error_status = true;
			$new_password = trim($new_password);
			$confirm_password = trim($confirm_password);
			if($this->is_password($new_password,$error_status)){
	
				if($new_password == $confirm_password){
	
					$update_array = array();
					$update_array['password'] = addslashes(self::user_password_encode($new_password));
					$update_array['update_time'] = addslashes($this->current_datetime);
					$result = $this->where('user_id = '.$user_id)->data($update_array)->save();
					if($result === false){
							
						$this->error_code_array['result'] = ERROR_UPDATE_FAILED;
						$this->error_array['result'] = '更新失败';
					}else{
							
						$check = true;
					}
				}else{
	
					$this->error_code_array['result'] = ERROR_PASSWORD_NOT_SAME;
					$this->error_array['result'] = '密码输入不一样';
				}
			}
		}
		return $check;
	}
	
	/**
	 * 重置密码
	 * @param unknown $user_id
	 * @param unknown $verify_code
	 * @param unknown $new_password
	 * @param unknown $confirm_password
	 * @return boolean
	 */
	public function resetPassword($session_object,$user_id,$verify_code,$new_password,$confirm_password){
		
		$check = false;
		$this->error_array = array();
		$this->error_code_array = array();
		$user_id = intval($user_id);
		if($this->resetVerify($session_object,$user_id,$verify_code)){
			
			$error_status = true;
			$new_password = trim($new_password);
			$confirm_password = trim($confirm_password);			
			if($this->is_password($new_password,$error_status)){
				
				if($new_password == $confirm_password){
					
					$update_array = array();					
					$update_array['password'] = addslashes(self::user_password_encode($new_password));
					$update_array['update_time'] = addslashes($this->current_datetime);
					$result = $this->where('user_id = '.$user_id)->data($update_array)->save();
					if($result === false){
					
						$this->error_code_array['result'] = ERROR_UPDATE_FAILED;
						$this->error_array['result'] = '更新失败';
					}else{
					
						$check = true;
					}
				}else{
					
					$this->error_code_array['result'] = ERROR_PASSWORD_NOT_SAME;
					$this->error_array['result'] = '密码输入不一样';
				}
			}
		}		
		return $check;
	}
	
	/**
	 * 重置密码验证
	 * @param unknown $user_id
	 * @param unknown $verify_code
	 * @return boolean
	 */
	public function resetVerify($session_object,$user_id,$verify_code){
		
		$check = false;
		$user_id = intval($user_id);
		$verify_code = trim($verify_code);		
		$verify_code_len = strlen($verify_code);
		if($user_id > 0 && $verify_code_len > 0){
			
			$real_user_id = $session_object->get_session_data_value('user_id',0,'int');
			$real_verify_code = $session_object->get_session_data_value('verify_code','','string');
			$real_verify_code = trim($real_verify_code);
			if($real_user_id > 0 && !empty($real_verify_code)){
				
				if($user_id != $real_user_id){
					
					$this->error_code_array['result'] = INVALID_DATA;
					$this->error_array['result'] = '非法数据';
				}else{
					
					$verify_code = strtolower($verify_code);
					$real_verify_code = strtolower($real_verify_code);
					if($verify_code == $real_verify_code){
						
						$check = true;
						$session_object->unset_session_data('verify_code_count');						
					}else{
						
						$this->error_code_array['result'] = ERROR_CAPTCHA_INVALID;
						$this->error_array['result'] = '验证码不合法，请核对后重新输入';
					}
				}			
			}else{
				
				$this->error_code_array['result'] = ERROR_SESSID;
				$this->error_array['result'] = 'session key不合法';
			}			
		}else{
			
			if($user_id <= 0){
				
				$this->error_code_array['result'] = INVALID_DATA;
				$this->error_array['result'] = '非法数据';
			}else{
				
				$this->error_code_array['result'] = ERROR_CAPTCHA_EMPTY;
				$this->error_array['result'] = '验证码不能为空';
			}
			
		}
		if(!$check){
			
			$verify_code_count = $session_object->get_session_data_value('verify_code_count',0,'int');
			$verify_code_count += 1; 
			if($verify_code_count > 5){
				
				$verify_code_count = 6;
				$this->error_code_array['result'] = VERIFY_FAILED_ENOUGH;
				$this->error_array['result'] = '验证错误过多次';
				$session_object->unset_session_data('user_id');
				$session_object->unset_session_data('verify_code');			
				$session_object->unset_session_data('verify_code_count');
			}else{
				
				$session_object->change_session_data('verify_code_count',$verify_code_count);
			}
			
		}
		return $check;
	}
}
?>