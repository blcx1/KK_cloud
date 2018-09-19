<?php

namespace Home\Model;
use Think\Model;
use Home\Model\UserModel;
use Home\Model\UserViewModel;

class UserExtendModel extends Model {
	
	//初始化
	public function __construct($name='',$tablePrefix='',$connection='') {
		
		$this->dbName = 'db_user';
		$this->tableName = C('DB_PREFIX').'user_extend';
		$this->trueTableName = $this->tableName;
		$this->pk = 'user_id';
		parent::__construct($name,$tablePrefix,$connection);	
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
}
?>