<?php

namespace Home\Model;
use Think\Model;

if(!defined('LANG_SET')){

	define('LANG_SET',C('DEFAULT_LANG',null,'zh-cn'));
}
class TemplateModel extends Model {
	
	protected $current_time;//日期时间戳
	protected $current_datetime;//日期时间按 Y-m-d H:i:s格式
	protected $current_date;//日期时间 按Y-m-d格式
	protected $error_code_array = array();//错误编码
	protected $error_array = array();//错误信息
	
	/**
	  * 初始化
	  **/
	public function __construct($name='',$tablePrefix='',$connection='') {		
		
		$this->dbName = 'db_user';
		$this->tableName = C('DB_PREFIX').'template';
		$this->trueTableName = $this->tableName;
		$this->pk = 'id';
		parent::__construct($name,$tablePrefix,$connection);
		$this->current_time = time();
		$this->current_datetime = date('Y-m-d H:i:s',$this->current_time);
		$this->current_date = date('Y-m-d',$this->current_time);		
	}
	
	/**
	  * 获取信息
	  * @param $id 
	  **/
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
	
	/**
	 * 获取唯一编码
	 * @param $id
	 */
	public function get_template_code($id){
		
		$id = intval($id);
		$template_code = '';
		if($id > 0){
			
			$result = $this->field('template_code')->where ('id = '.$id)->find();
			if(check_array_valid($result)){
					
				$template_code = stripslashes($result['template_code']);
			}
		}
		return $template_code;
	}
	
	/**
	 * 通过编码获取id
	 */
	public function get_id_to_template_code($template_code,$check_valid = false){
		
		$id = 0;
		$template_code = trim($template_code);
		if(strlen($template_code) <= 1){
				
			return $id;
		}
		
		$str_where = '';
		$check_valid = (bool) $check_valid;
		$template_code = strtoupper($template_code);		
		if($check_valid){
			
			$str_where .= 'status = 1 and ';
		}
		$str_where .= 'is_delete = 0 and lang_iso_code = "'.addslashes(LANG_SET).'" and template_code = "'.addslashes($template_code).'"';
		$result = $this->field('id')->where($str_where)->find();
		if(check_array_valid($result)){
			
			$id = intval($result['id']);
		}
		return $id;
	}
	
	/**
	 * 通过编码获取信息
	 */
	public function get_info_to_template_code($template_code,$check_valid = false){
		
		$info_array = array();
		$template_code = trim($template_code);
		if(strlen($template_code) <= 1){
				
			return $info_array;
		}
		
		$str_where = '';
		$check_valid = (bool) $check_valid;
		$template_code = strtoupper($template_code);		
		if($check_valid){
			
			$str_where .= 'status = 1 and ';
		}
		$str_where .= 'is_delete = 0 and lang_iso_code = "'.addslashes(LANG_SET).'" and template_code = "'.addslashes($template_code).'"';
		$result = $this->where($str_where)->find();
		if(check_array_valid($result)){
			
			$info_array = $result;
		}
		return $info_array;
	}
	
	/**
	 * 判别是否存在
	 */
	public function check_exists_template_code($id = 0,$template_code,$lang_iso_code){
	
		$check = false;
		$id = intval($id);
		$template_code = trim($template_code);
		if(strlen($template_code) <= 1){
			
			return $check;
		}
		$template_code = strtoupper($template_code);
		$str_where = '';
		if($id > 0){
				
			$str_where = 'id != '.$id.' and ';
		}
		$str_where .= 'lang_iso_code = "'.addslashes($lang_iso_code).'" and template_code = "'.addslashes($template_code).'"';
		$total_count = $this->where($str_where)->count();
		if($total_count > 0){

			$check = true;
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