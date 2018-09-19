<?php

namespace Home\Model;
use Think\Model;

class ContactsVersionModel extends \Home\Model\DefaultModel {
	
	public function __construct($cache_object = null,$name='',$tablePrefix='',$connection=''){
		
		$db_prefix = C('DB_PREFIX');
		$this->dbName = 'db_contacts';
		$this->tableName = $db_prefix.'contacts_version';
		$this->trueTableName = $this->tableName;
		$this->pk = 'id';
		parent::__construct($cache_object,$name,$tablePrefix,$connection);
	}
	
	/**
	 * 获取版本信息
	 * @param unknown $user_id
	 * @return Ambigous <multitype:number , \Think\mixed, boolean, NULL, multitype:, mixed, unknown, string, object>
	 */
	public function get_version_info($user_id){
		
		$user_id = intval($user_id);
		$version_info = array('id'=>0,'version_id'=>0,'add_time'=>$this->current_time);		
		if($user_id > 0){
			
			$field_str = 'id,version_id,add_time';
			$str_where = 'user_id = '.$user_id;
			$result = $this->field($field_str)->where($str_where)->find();
			if(check_array_valid($result)){
				
				$version_info = $result;
			}else{
				
				$info_array = array();
				$info_array['user_id'] = $user_id;
				$info_array['version_id'] = 0;
				$info_array['add_time'] = $this->current_datetime;
				$info_array['update_time'] = $this->current_datetime;
				$result = $this->info_add($info_array);
				if($result){
					
					$version_info['id'] = intval($result);					
				}				
			}
			$version_info['add_time'] = strtotime($version_info['add_time']);
		}		
		return $version_info;
	}
	
	/**
	 * 更新版本值
	 * @param unknown $version_auto_id
	 * @param unknown $user_id
	 * @param unknown $version_id
	 * @return boolean
	 */
	public function update_version_id($version_auto_id,$user_id,$version_id){
		
		$check = false;
		$user_id = intval($user_id);
		$version_auto_id = intval($version_auto_id);
		$version_id = intval($version_id);
		if($user_id > 0 && $version_auto_id > 0 && $version_id > 0 ){
			
			$update_array = array();
			$update_array['version_id'] = $version_id;
			$update_array['update_time'] = $this->current_datetime;
			$str_where = ' '.$version_auto_id.' and user_id = '.$user_id;
			$check = $this->info_update($str_where,$update_array);
		}
		return $check;
	}	
}
?>