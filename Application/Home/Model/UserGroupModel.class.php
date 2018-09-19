<?php

namespace Home\Model;
use Think\Model;

class UserGroupModel extends \Home\Model\DefaultModel {
	
	protected $sms_table;
	/**
	 * 初始化
	 */
	public function __construct($cache_object = null,$name='',$tablePrefix='',$connection='') {
		
		$db_prefix = C('DB_PREFIX');
		$this->dbName = 'db_sms';
		$this->tableName = $db_prefix.'group';		
		$this->trueTableName = $this->tableName;
		$this->sms_table = $this->dbName.'.'.$db_prefix.'sms';
		$this->pk = 'id';
		parent::__construct($cache_object,$name,$tablePrefix,$connection);		
	}
	
	/**
	 * 释放
	 **/
	public function __destruct(){
	    		
		parent::__destruct();
	}
	
	/**
	 * 获取联系号码跟组id的对应关系
	 * @param unknown $user_id
	 * @return multitype:Ambigous <>
	 */
	public function get_group($user_id){
		
		$groupData = array();
		$user_id = intval($user_id);
		if($user_id > 0){
				
			$where = array();
			$where['user_id'] = $user_id;
			$field_str = 'id,phone';
			$result = $this->field($field_str)->where($where)->select();
			if (check_array_valid($result)){
				
				foreach ($result as $key=>$value){
					
					$groupData[$value['phone']] = $value['id'];			
				}					
			}	
		}
		return $groupData;	
	}
	
	/**
	 * 更新处理分组的数据
	 * @param unknown $user_id
	 * @param string $check_del
	 */
	public function process($user_id,$check_del = false){
		
		$user_id = intval($user_id);
		$check_del = (bool) $check_del;
		$sms_table = $this->sms_table;
		$group_table = $this->dbName.'.'.$this->tableName;
		
		if($check_del){
			
			$sql = 'delete from '.$group_table.' where user_id = '.$user_id.' and (id not in (select group_id from '.$sms_table.' where `user_id` = '.$user_id.'))';
			$check = $this->execute($sql);
		}
		$sql = 'update '.$group_table.' as g,(select group_id,count(*) as total_count from '.$sms_table.' where user_id = '.$user_id.' group by group_id) as s set g.total_count = s.total_count,g.update_time = "'.$this->current_datetime.'" where g.user_id = '.$user_id.' and g.id = s.group_id';
		$check = $this->execute($sql);
		if($check_del && $check){
	
			$sql = 'delete from '.$group_table.' where user_id = '.$user_id.' and total_count = 0';
			$this->execute($sql);		
		}
		$sql = 'select group_id,count(*) as total_count from '.$sms_table.' where user_id = '.$user_id.' and is_delete = 0 group by group_id ';
		$result = $this->query($sql);
		
		if(check_array_valid($result)){
			
			$sql = 'update '.$group_table.' as g,(select group_id,count(*) as total_count from '.$sms_table.' where user_id = '.$user_id.' and is_delete = 0 group by group_id) as s set g.use_total_count = s.total_count where g.user_id = '.$user_id.' and g.id = s.group_id';
			$check = $this->execute($sql);
		}elseif(is_array($result)){
			
			$sql = 'update '.$group_table.' set use_total_count = 0 where user_id = '.$user_id;
			$check = $this->execute($sql);
		}
			
		$sql = 'update '.$group_table.' set `recy_total_count` = `total_count` - `use_total_count` where user_id = '.$user_id.' and `total_count` >= `use_total_count` ';
		$check = $this->execute($sql);
	}
	
	/**
	 * 获取错误编码
	 **/
	public function get_error_code_array(){
	
		return (array)$this->error_code_array;
	}
	
	/**
	  * 获取错误信息
	  **/
	public function get_error_array(){
	
		return (array)$this->error_array;
	}
}
?>