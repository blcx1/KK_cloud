<?php
/**
 *语言模型
 *date 2016-03-04
 *autor:kiven pcttcnc2007@126.com
 */

namespace Home\Model;
use Think\Model;
class LanguageModel extends \Home\Model\DefaultModel{
	
	protected $cache_prefix = 'Language';//模块缓存前缀
	protected $cache_expire = 86400;//缓存时间	
	protected $id_lang_list = array();
	protected $iso_lang_list = array();
	
	/**
	 * 初始化
	 * @param string $name
	 * @param string $tablePrefix
	 * @param string $connection
	 * @param string $cache_object
	 */
	public function __construct($cache_object = null,$name='',$tablePrefix='',$connection='') {
		
		$db_prefix = C('DB_PREFIX');
		$this->dbName = 'db_admin';
		$this->tableName = $db_prefix.'language';		
		$this->trueTableName = $this->tableName;
		$this->pk = 'id';
		parent::__construct($cache_object,$name,$tablePrefix,$connection);
		$this->init();
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \Admin\Model\DefaultModel::init()
	 */
	protected function init(){
		
		$lang_list = $this->get_lang_list();	
		$this->id_lang_list = $lang_list['id_lang_list'];
		$this->iso_lang_list = $lang_list['iso_lang_list'];
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \Admin\Model\DefaultModel::__destruct()
	 */
	public function __destruct(){		
		
		parent::__destruct();
	}
	
	/**
	 * 获取语言列表
	 * @return multitype:
	 */
	public function get_lang_list(){
		
		$check_cache = false;
		$lang_list = array();
		$lang_list['id_lang_list'] = array();
		$lang_list['iso_lang_list'] = array();
		$cache_object = $this->cache_object;
		if($cache_object != null && is_object($cache_object)){
		
			$check_cache = true;
			$cache_name = $this->cache_prefix.'-lang_list';
			$cache_value = $cache_object->get($cache_name);
			if($cache_value){
					
				$result = @unserialize(base64_decode(urldecode($cache_value)));
				if(check_array_valid($result)){
				
					return $result;
				}
			}
		}
		$group_by = $join_str = '';
		$order_by = 'id';
		$order_way = 'asc';
		$where = 'is_delete = 0';
		$field_str = 'id,iso_code,name';
		$result = $this->getList($order_by,$order_way,$group_by,$where,$join_str,$field_str);
		if(check_array_valid($result)){
			
			$id_lang_list = array();
			$iso_lang_list = array();
			foreach($result as $value){
				
				$lang_id = $value['id'];
				$iso_code = $value['iso_code'];
				$id_lang_list[$lang_id] = $value;
				$iso_lang_list[$iso_code] = $value;
				unset($id_lang_list[$lang_id]['id']);
				unset($iso_lang_list[$iso_code]['iso_code']);				
			}
			$lang_list['id_lang_list'] = $id_lang_list;
			$lang_list['iso_lang_list'] = $iso_lang_list;		
			if($check_cache){
				
				$cache_value = @urlencode(base64_encode(serialize($lang_list)));
				$cache_object->set($cache_name,$cache_value);
			}	
		}
		return $lang_list;
	}
	
	/**
	 * 获取语言id
	 * @param unknown $lang_iso
	 * @return number
	 */
	public function get_lang_id($lang_iso){
		
		$lang_id = 0;
		$lang_iso = trim($lang_iso);
		if(!empty($lang_iso)){
			
			$iso_lang_list = $this->iso_lang_list;
			$lang_id = isset($iso_lang_list[$lang_iso]['id']) ? intval($iso_lang_list[$lang_iso]['id']) : 0;
		}
		return $lang_id;
	}
	
	/**
	 * 获取语言编码
	 * @param unknown $lang_id
	 * @return string
	 */
	public function get_lang_iso($lang_id){
		
		$lang_iso = '';
		$lang_id = intval($lang_id);
		if($lang_id > 0){
			
			$id_lang_list = $this->id_lang_list;
			$lang_iso = isset($id_lang_list[$lang_id]['iso_code']) ? trim($id_lang_list[$lang_id]['iso_code']) : '';
		}
		return $lang_iso;
	}
	
	/**
	 * 获取语言名称
	 * @param unknown $lang_id
	 * @return string
	 */
	public function get_lang_name($lang_id){
		
		$lang_name = '';
		$lang_id = intval($lang_id);
		if($lang_id > 0){
				
			$id_lang_list = $this->id_lang_list;
			$lang_name = isset($id_lang_list[$lang_id]['name']) ? trim($id_lang_list[$lang_id]['name']) : '';
		}
		return $lang_name;
	}
	
	/**
	 * 获取属性值
	 * @param unknown $name
	 * @return Ambigous <NULL, unknown, string>
	 */
	public function get_value($name){
		
		$value = null;
		$name = trim($name);
		if(!empty($name)){
			
			$value = isset($this->$name) ? $this->$name : $value;
		}
		return $value;
	}
	
	/**
	 * 获取显示列表
	 * @param unknown $where_array
	 * @param number $page_no
	 * @param number $page_size
	 * @param string $order_by
	 * @param string $order_way
	 * @param string $group_by
	 * @return unknown
	 */
	public function get_show_list($where_array = array(),$page_no = 1,$page_size = 30,$order_by = 'id',$order_way = ' desc ',$group_by = ''){
	
		$list_array = array();
	
		$join_str = '';				
		$field_str = 'id,name,iso_code,add_time,update_time';	
		$result = $this->get_page_list($page_no,$page_size,$order_by,$order_way,$group_by,$where_array,$join_str,$field_str);
		if(check_array_valid($result)){
				
			
			$list_array = $result;
		}
		return $list_array;
	}
	
	/**
	 * 获取显示列表总个数
	 * @param unknown $where_array
	 * @param string $group_by
	 * @return unknown
	 */
	public function get_show_list_count($where_array = array(),$group_by = ''){
	
		$list_count = 0;
		$field_str = $join_str = '';		
		$list_count = $this->get_list_count($group_by,$where_array,$join_str,$field_str);
	
		return $list_count;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \Admin\Model\DefaultModel::info_update_process()
	 */
	public function info_update_process($id,$check_exists = false){
		
		$check = false;
		$check_valid = true;
		$id = intval($id);		
		$this->error_array = array('name'=>'','iso_code'=>'','result'=>'');
		$exists_check = $check_exists ? $this->check_exists($id) : ($id > 0 ? true : false);
		if($exists_check){
			
			$name = trim(filter_set_value($_REQUEST,'name','','string'));
			$iso_code = trim(filter_set_value($_REQUEST,'iso_code','','string'));
			$name = strip_tags($name);
			$iso_code = strip_tags($iso_code);
			if(strlen($name) <= 0){
			
				$check_valid = false;
				$this->error_array['name'] = L('NotEmpty');
			}
			if(strlen($iso_code) < 0){
			
				$check_valid = false;
				$this->error_array['iso_code'] = L('NotEmpty');
			}
			
			$result = $this->where('id != '.$id.' and iso_code = "'.addslashes($iso_code).'"')->count();
			if($result > 0){
			
				$check_valid = false;
				$this->error_array['iso_code'] = L('AddRepeat');
			}
			if($check_valid){
			
				$where = $update_array = array();
				$current_datetime = $this->current_datetime;
				$where['id'] = $id;
				$update_array['name'] = $name;
				$update_array['iso_code'] = $iso_code;				
				$update_array['update_time'] = $current_datetime;
				$check = $this->info_update($where,$update_array);
				if(!$check){
						
					$this->error_array['result'] = L('ModifyFailure');
				}
			}
		}else{
			
			$this->error_array['result'] = L('IllegalData');
		}		
		return $check;
	}
}
?>