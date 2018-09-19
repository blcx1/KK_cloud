<?php

namespace Home\Model;
use Think\Model;

class loginLogModel extends \Home\Model\DefaultModel {
		
	protected $delay_time_clear = 1296000;
	protected $delay_time = 1800;//延迟插入	
	protected $delay_datetime = '';//延迟有效时间临界
	
	/**
	 * 初始化
	 * @param string $cache_object
	 * @param string $name
	 * @param string $tablePrefix
	 * @param string $connection
	 */
	public function __construct($cache_object = null,$name='',$tablePrefix='',$connection=''){
		
		$db_prefix = C('DB_PREFIX');
		$db_name = 'db_cloud';
		$this->tableName = $db_prefix.'login_log';
		$this->trueTableName = $this->tableName;
		$this->pk = 'id';
		parent::__construct($cache_object,$name,$tablePrefix,$connection);
		
		$delay_time = $this->delay_time > 0 ? $this->delay_time : 1800;
		$this->delay_datetime = date('Y-m-d H:i:s',$this->current_time - $delay_time); 
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \Home\Model\ContactsBaseModel::__destruct()
	 */
	public function __destruct(){
		
		parent::__destruct();
		$delay_time_clear = $this->delay_time_clear > 0 ? $this->delay_time_clear : 1296000;
		$str_where = ' add_time < "'.date('Y-m-d H:i:s',$this->current_time - $delay_time_clear).'" ';		
		$this->info_force_delete($str_where);
	}
	
	/**
	 * 设置登录次数
	 * @param unknown $user_account
	 * @param unknown $login_type
	 */
	public function set_login_count($user_account,$login_type){
				
		$ip = get_client_ip(0,true);
		$login_type = intval($login_type);
		$user_account = trim($user_account);		
		$current_datetime = $this->current_datetime;
		$str_where = ' update_time >= "'.$this->delay_datetime.'" and user_account = "'.addslashes($user_account).
		             '" and ip ="'.$ip.'" and login_type = '.$login_type;
		$result = $this->field('id')->where($str_where)->find();
		if(check_array_valid($result)){
			
			$id = $result['id'];
			$where = $result;
			$update_array = array();
			$update_array['login_count'] = array('exp','login_count + 1');
			$update_array['update_time'] = $current_datetime;
			$this->info_update($where,$update_array);
		}else{
			
			$info_array = array();
			$info_array['user_account'] = $user_account;
			$info_array['ip'] = $ip;
			$info_array['login_type'] = $login_type;
			$info_array['login_count'] = 1;
			$info_array['add_time'] = $current_datetime;
			$info_array['update_time'] = $current_datetime;
			
			$this->info_add($info_array);
		}
	}
	
	/**
	 * 获取登录次数
	 * @param unknown $user_account
	 * @param unknown $login_type
	 * @return number
	 */
	public function get_login_count($user_account,$login_type){
		
		$login_count = 0;
		$ip = get_client_ip(0,true);
		$login_type = intval($login_type);
		$user_account = trim($user_account);		
		$str_where = ' update_time >= "'.$this->delay_datetime.'" and user_account = "'.addslashes($user_account).
		             '" and login_type = '.$login_type;
		$result = $this->field('sum(login_count) as total_count')->where($str_where)->find();
		if(check_array_valid($result)){
			
			$login_count = intval($result['total_count']);
		}
		return $login_count;
	}
	
	/**
	 * 登录成功清除次数
	 * @param unknown $user_account
	 * @param unknown $login_type
	 */
	public function clear_login_count($user_account,$login_type){
		
		$ip = get_client_ip(0,true);
		$login_type = intval($login_type);
		$user_account = trim($user_account);		
		$str_where = ' update_time >= "'.$this->delay_datetime.'" and user_account = "'.addslashes($user_account).
		             '" and ip ="'.$ip.'" and login_type = '.$login_type;
		$result = $this->field('id')->where($str_where)->find();
		if(check_array_valid($result)){
			
			$where = $result;
			$update_array = array();
			$update_array['is_login'] = 1;
			$update_array['login_count'] = 0;
			$update_array['update_time'] = $this->current_datetime;			
			$this->info_update($where,$update_array);
		}
	}	
}
?>