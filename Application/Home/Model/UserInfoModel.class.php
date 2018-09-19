<?php

namespace Home\Model;
use Think\Model;
use Home\Model\UserModel;
use Home\Model\UserViewModel;

class UserInfoModel extends Model {
	
	protected $error_code_array = array();//错误编码
	protected $error_array = array();//错误信息
	
	//初始化
	public function __construct($name='',$tablePrefix='',$connection='') {
		
		$this->dbName = 'db_user';
		$this->tableName = C('DB_PREFIX').'user_info';
		$this->trueTableName = $this->tableName;
		$this->pk = 'user_id';
		parent::__construct($name,$tablePrefix,$connection);	
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
					
				$info_array = $result;
			}
		}
	
		return $info_array;
	}
	
	//判别是否存在
	public function check_exists_and_add($user_id,$check_add = false){
	
		$check = false;
		$user_id = intval($user_id);
		$check_add = (bool) $check_add;
		if($user_id > 0){
				
			$total_count = $this->where ('user_id = '.$user_id)->count();
			if($total_count > 0){
	
				$check = true;
			}elseif($check_add){
	
				$add_array = array();
				$add_array['user_id'] = $user_id;
				$result = $this->data($add_array)-> add();
				if($result){
						
					$check = true;
				}
			}
		}
		return $check;
	}
	
	/**
	 * 用户完善其他资料
	 * @param unknown $user_id
	 * @param unknown $sex
	 * @param unknown $birthday
	 * @param unknown $address
	 * @return boolean
	 */
	public function user_other_perfect($user_id,$sex,$birthday,$address){
		
		$check = false;
		$user_id = intval($user_id);
		$this->error_array = array();
		$this->error_code_array = array();
		if($user_id > 0){
				
			$sex = intval($sex);			
			$birthday = trim($birthday);
			$address = trim($address);
			
			if(!is_date($birthday)){
		
				$this->error_code_array['result'] = ERROR_DAY_INVALID;
				$this->error_array['result'] = '日期格式不合法';
				return $check;
			}
								
			if($this->check_exists_and_add($user_id,true)){
				
				$sex = $sex >=0 && $sex <=2 ? $sex : 0;
				$birthday = date('Y-m-d',strtotime($birthday));
				
				$update_array = array();				
				$update_array['sex'] = $sex;
				$update_array['birthday'] = addslashes($birthday);				
				$update_array['address'] = addslashes($address);				
				$result = $this->where('user_id = '.$user_id)->data($update_array)->save();
				$check = $result !== false ? true : false;
			}			
			if(!$check){
					
				$this->error_code_array['result'] = ERROR_MYSQL_EXE_REPORT;
				$this->error_array['result'] = '数据库报错';
			}
		}else{
		
			$this->error_code_array['result'] = INVALID_DATA;
			$this->error_array['result'] = '非法数据';
		}
		return $check;
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