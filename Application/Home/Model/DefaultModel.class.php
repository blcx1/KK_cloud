<?php
/**
 * 默认db模型
 * @author kivenpc pcttcnc2007@126.com
 * @date 2015-12-12
 *
 */
namespace Home\Model;
abstract class DefaultModel extends \Think\Model{
	
	protected $current_time;
	protected $current_datetime;
	protected $current_date;
	protected $default_order_by = 'id';
	protected $error_array = array();//错误信息
	protected $error_code_array = array();//错误编码	
	protected $is_cache = false;//是否cache
	protected $cache_object = null;//缓存对象
	protected $cache_prefix = '';//模块缓存前缀
	protected $cache_expire = 86400;//缓存时间
	
	/**
	 * 初始化
	 */
	public function __construct($cache_object = null,$name='',$tablePrefix='',$connection='') {
				
		parent::__construct($name,$tablePrefix,$connection);
		
		$this->cache_object = is_null($cache_object) || !is_object($cache_object) ? null : $cache_object;
		$this->is_cache = $this->cache_object == null ? false : true;
		if($this->is_cache){
			
			$cache_expire = intval($this->cache_expire);
			$this->cache_expire = $cache_expire > 0 ? $cache_expire : 86400;
		}		
		$this->current_time = time();
		$this->current_datetime = date('Y-m-d H:i:s',$this->current_time);
		$this->current_date = date('Y-m-d',$this->current_time);		
	}
	
	/**
	 * 设置其他初始化操作
	 */
	protected function init(){
		
		if(!defined('LANG_ID')){
			
			if(!defined('LANG_SET')){
			
				define('LANG_SET',C('DEFAULT_LANG',null,'en-us'));
			}
			$lang_object = new \Home\Model\LanguageModel($this->cache_object);
			$lang_id = $lang_object->get_lang_id(LANG_SET);			
			$lang_id = $lang_id > 0 ? $lang_id : 2;
			$lang_iso = $lang_object->get_lang_iso($lang_id);		
			define('LANG_ID',$lang_id);
		}
	}
	
	/**
	 * 释放
	 **/
	public function __destruct(){
		 
		$this->current_time = 0;
		$this->current_datetime = '';
		$this->current_date = '';
		$this->default_order_by = '';
		$this->error_array = array();
		$this->error_code_array = array();
		$this->cache_object = null;
		$this->cache_prefix = '';
		$this->cache_expire = 0;		
	}
	
	/**
	 * id数组中获取有效的id组成数组返回
	 * @param unknown $id_array
	 * @return multitype:number
	 */
	public static function get_valid_id_array($id_array){
	
		$valid_id_array = array();
		if(check_array_valid($id_array)){
				
			foreach($id_array as $id){
				
				$id = intval($id);
				if($id > 0){
					
					$valid_id_array[] = $id;
				}
			}	
		}
		return $valid_id_array;
	}
	
	/**
	 * 判别是否存在
	 * @param int $user_id
	 * @return boolean
	 */
	public function check_exists($id,$is_delete = false){
	
		$check = false;
		$id = intval($id);
		if($id > 0){
			
			$str_where = $this->pk.' = '.$id;
			if($is_delete){
				
				$str_where .= ' and is_delete = 0 ';
			}
			$total_count = $this->where($str_where)->count();
			if(intval($total_count) > 0){
	
				$check = true;
			}
		}
	
		return $check;
	}
	
	/**
	 * 判别是否有效
	 * @param int $user_id
	 * @return boolean
	 */
	public function check_valid($id){
		
		return $this->check_exists($id);
	}
	
	/**
	 * 获取信息
	 * @param unknown $id
	 * @param string $field_str
	 * @return Ambigous <multitype:, \Think\mixed, boolean, NULL, mixed, unknown, string, object>
	 */
	public function get_info($id,$field_str = '',$extend_where = ''){
	
		$info_array = array();
		$id = intval($id);
		if($id > 0){
				
			$field_str = trim($field_str);
			$str_where = $this->pk.' = '.$id.' '.$extend_where;
			if(strlen($field_str) > 0){
	
				$this->field($field_str);
			}
			$result = $this->where($str_where)->find();
			if(check_array_valid($result)){
					
				$info_array = $result;
			}
		}
	
		return $info_array;
	}

	/**
	 * 信息添加
	 * @param unknown $info_array
	 * @param string $clear_cache
	 * @return Ambigous <\Think\mixed, boolean, string, unknown>|boolean
	 */
	public function info_add($info_array = array(),$clear_cache = false){
	
		$info_array = check_array_valid($info_array) ? $info_array : array();
		if(count($info_array) > 0){
				
			$result = $this->data($info_array)->add();
			if($result && $clear_cache){
				
				$this->clear_cache_list();
			}
			return $result;
		}else{
				
			return false;
		}
	}
	
	/**
	 * 通过id进行删除cache
	 * @param unknown $id
	 */
	public function clear_cache_from_id($id){
		
		$id = intval($id);
		if($id > 0){
			
			$field_str = $this->pk ? $this->pk : 'id';
			$where = $field_str.' = '.$id;
			$this->clear_cache($where);
		}		
	}
	
	/**
	 * 清除对应的cache
	 * @param unknown $where
	 */
	public function clear_cache($where){
		
		$is_cache = $this->is_cache;
		$result = array();
		if($is_cache && is_object($this->cache_object)){
		
			$field_str = $this->pk ? $this->pk : 'id';
			$result = $this->field($field_str)->where($where)->select();			
			if($result){
			
				$cache_prefix = $this->cache_prefix;
				$cache_source_object = $this->cache_object->get_object();
				$check_mulit = false;
				if(method_exists($cache_source_object,'keys') && method_exists($cache_source_object,'delete')){
					
					$check_mulit = true;
				}
				foreach($result as $value){
					
					$id = intval($value[$field_str]);
					$key_id = $cache_prefix.'-id'.$id;
					$this->cache_object->rm($key_id);
					
					$key_detail = $cache_prefix.'-client_detail-id'.$id;
					$this->cache_object->rm($key_detail);
					
					if($check_mulit){
						
						$key = $key_id.'-*';
						$keys_array = $cache_source_object->keys($key);
						//	var_dump($keys_array);
						if(check_array_valid($keys_array)){
						
							foreach($keys_array as $key){
						
								$cache_source_object->delete($key);
							}
						}
						
						$key = $key_detail.'-*';
						$keys_array = $cache_source_object->keys($key);
						//	var_dump($keys_array);
						if(check_array_valid($keys_array)){
						
							foreach($keys_array as $key){
						
								$cache_source_object->delete($key);
							}
						}
					}else{
						
						$key_id_other_client = $key_id.'-m0';
						$this->cache_object->rm($key_id_other_client);
						
						$key_id_other_admin = $key_id.'-m1';
						$this->cache_object->rm($key_id_other_admin);
					}
					$this->clear_cache_db($id);					
				}
				$this->clear_cache_list();				
			}
		}
	}
	
	/**
	 * 清除缓存列表
	 */
	public function clear_cache_list(){
		
		if($this->is_cache && is_object($this->cache_object)){
			
			$cache_source_object = $this->cache_object->get_object();
			if(method_exists($cache_source_object,'keys') && method_exists($cache_source_object,'delete')){
				
				$cache_prefix = $this->cache_prefix;
				$key = $this->cache_object->getOptions('prefix').$cache_prefix.'-client_list*';
			//	var_dump($key);
				$keys_array = $cache_source_object->keys($key);
			//	var_dump($keys_array);
				if(check_array_valid($keys_array)){
				
					foreach($keys_array as $key){
				
						$cache_source_object->delete($key);
					}
				}
			}			
		}
	}
	
	/**
	 * 清除缓存db库对应的数据
	 * @param unknown $id
	 */
	public function clear_cache_db($id){
		
	}
	
	/**
	 * 信息更新
	 * @param unknown $where
	 * @param unknown $update_array
	 * @param string $clear_cache
	 * @return boolean
	 */
	public function info_update($where,$update_array = array(),$clear_cache = false){
	
		$check = false;
		if(count($update_array) > 0 && (check_array_valid($where) || (is_string($where) && strlen(trim($where)) > 0))){
			
			if($clear_cache){
				
				$this->clear_cache($where);//清除对应的cache
			}			
			$result = $this->where($where)->data($update_array)->save();
			$check = $result === false ? false : true;
		}
		return $check;
	}
	
	/**
	 * 信息删除
	 * @param unknown $where
	 * @param string $clear_cache
	 * @return boolean
	 */
	public function info_delete($where,$clear_cache = false){
	
		$check = false;
		if(check_array_valid($where) || (is_string($where) && strlen(trim($where)) > 0)){
				
			if($clear_cache){
				
				$this->clear_cache($where);//清除对应的cache
			}			
			$check = $this->where($where)->delete();
			$check = $check === false ? false : true;
		}
		return $check;
	}
	
	/**
	 * 获取列表
	 * @param number $start
	 * @param number $len
	 * @param unknown $order_by
	 * @param string $order_way
	 * @param string $group_by
	 * @param unknown $where
	 * @param string $join_str
	 * @param string $field_str
	 * @return Ambigous <multitype:, unknown>
	 */
	public function get_list($start = 0,$len = 30,$order_by,$order_way = 'desc',$group_by = '',$where,$join_str = '',$field_str = ''){
	
		$list_array = array();
		$start = intval($start);
		$len = intval($len);
		$start = $start > 0 ? $start : 0;
		$len = $len > 0 ? $len : 30;
		$order_by = trim($order_by);
		$order_by = strlen($order_by) > 0 ? $order_by : $this->default_order_by;
		$order_way = strtolower(trim($order_way));
		$order_way = strlen($order_way) > 0 ? $order_way : 'desc';
		$group_by = trim($group_by);
		$join_str = trim($join_str);
		$field_str = trim($field_str);
		$field_str = $field_str != '*' ? $field_str : '';
		if(strlen($field_str) > 0 ){
	
			$this->field($field_str);
		}
		if(strlen($join_str) > 0){
	
			$this->join($join_str);
		}
		if(check_array_valid($where) || (is_string($where) && strlen(trim($where)) > 0)){
	
			$this->where($where);
		}
		if(strlen($group_by) > 0){
	
			$this->group($group_by);
		}
			
		$result = $this->order($order_by.' '.$order_way)->limit($start,$len)->select();
		if(check_array_valid($result)){
	
			$list_array = $result;
		}
		return $list_array;
	}
	
	/**
	 * 获取列表总个数
	 * @param string $group_by
	 * @param unknown $where
	 * @param string $join_str
	 * @param string $field_str
	 * @return number
	 */
	public function get_list_count($group_by = '',$where,$join_str = '',$field_str = ''){
	
		$list_count = 0;
		$group_by = trim($group_by);
		$join_str = trim($join_str);
		$field_str = trim($field_str);
		$field_str = $field_str != '*' ? $field_str : '';
		$field_str_len = strlen($field_str);
		if($field_str_len > 0 ){
	
			$this->field('count('.$field_str.') as total_count');
		}
		if(strlen($join_str) > 0){
	
			$this->join($join_str);
		}
		if(check_array_valid($where) || (is_string($where) && strlen(trim($where)) > 0)){
	
			$this->where($where);
		}
		if(strlen($group_by) > 0){
	
			$this->group($group_by);
		}
		if($field_str_len > 0){
	
			$result = $this->find();
			if(check_array_valid($result)){
	
				$list_count = $result['total_count'];
			}
		}else{
	
			$list_count = $this->count();
		}
	
		$list_count = intval($list_count);
	
		return $list_count;
	}
	
	/**
	 * 按页码获取列表
	 * @param number $page_no
	 * @param number $page_size
	 * @param unknown $order_by
	 * @param string $order_way
	 * @param string $group_by
	 * @param unknown $where
	 * @param string $join_str
	 * @param string $field_str
	 */
	public function get_page_list($page_no = 1,$page_size = 30,$order_by,$order_way = 'desc',$group_by = '',$where,$join_str = '',$field_str = ''){
	
		$page_no = intval($page_no);
		$page_size = intval($page_size);
		$page_no = $page_no > 0 ? $page_no : 1;
		$page_size = $page_size > 0 ? $page_size : 30;
		$start = ($page_no - 1)*$page_size;
	
		return $this->get_list($start,$page_size,$order_by,$order_way,$group_by,$where,$join_str,$field_str);
	}
	
	/**
	 * 按页码数
	 * @param number $total_count
	 * @param number $page_size
	 * @param string $group_by
	 * @param unknown $where
	 * @param string $join_str
	 * @param string $field_str
	 * @param string $check_total_count
	 * @return number
	 */
	public function get_page_count($total_count = 0,$page_size = 30,$group_by = '',$where,$join_str = '',$field_str = '',$check_total_count = true){
	
		$page_count = 1;
		$page_size = intval($page_size);
		$page_size = $page_size > 0 ? $page_size : 30;
		$total_count = intval($total_count);
		$total_count = $total_count > 0 ? $total_count : 0;
		$check_total_count = (bool) $check_total_count;
		if($total_count == 0 && $check_total_count){
	
			$total_count = $this->get_list_count($group_by,$where,$join_str,$field_str);
		}
		if($total_count > 1){
	
			$page_count = ceil($total_count/$page_size);
		}
	
		return $page_count;
	}
		
	/**
	 * 不限制排序及个数获取列表
	 * @param string $group_by
	 * @param unknown $where
	 * @param string $join_str
	 * @param string $field_str
	 * @return  <multitype:, \Think\mixed, string, boolean, NULL, unknown, mixed, object>
	 */
	public function getList($order_by,$order_way = 'desc',$group_by = '',$where,$join_str = '',$field_str = ''){
	
		$list_array = array();
		$group_by = trim($group_by);
		$join_str = trim($join_str);
		$field_str = trim($field_str);
		$field_str = $field_str != '*' ? $field_str : '';
		$order_by = trim($order_by);
		$order_by = strlen($order_by) > 0 ? $order_by : $this->default_order_by;
		$order_way = strtolower(trim($order_way));
		$order_way = strlen($order_way) > 0 ? $order_way : 'desc';
		if(strlen($field_str) > 0 ){
	
			$this->field($field_str);
		}
		if(strlen($join_str) > 0){
	
			$this->join($join_str);
		}
		if(check_array_valid($where) || (is_string($where) && strlen(trim($where)) > 0)){
	
			$this->where($where);
		}
		if(strlen($group_by) > 0){
	
			$this->group($group_by);
		}
			
		$result = $this->order($order_by.' '.$order_way)->select();
		if(check_array_valid($result)){
	
			$list_array = $result;
		}
		return $list_array;
	}	
	
	/**
	 * 修改一列值
	 * @param unknown $name
	 * @param unknown $value
	 * @param unknown $where
	 * @param string $clear_cache
	 * @return boolean
	 */
	public function change_name($name,$value,$where,$clear_cache = false){
		
		$check = false;
		$name = trim($name);
		if(strlen($name) > 0 && in_array($name,$this->fields) && (check_array_valid($where) || (is_string($where) && strlen(trim($where)) > 0))){
			
			$update_array = array();
			$update_array[$name] = $value;
			if($clear_cache){
				
				$this->clear_cache($where);//清除对应的cache
			}			
			$result = $this->where($where)->data($update_array)->save();
			if($result !== false){
				
				$check = true;
			}				
			
		}
		return $check;
	}
	
	/**
	 * 获取某个id下某一列值
	 * @param unknown $id
	 * @param unknown $name
	 * @return string
	 */
	public function getFieldValue($id,$name){
		
		$field_value = '';
		$id = intval($id);
		$name = trim($name);
		$this->_checkTableInfo();
		$filed_array = check_array_valid($this->fields) ? $this->fields : array();
		if($id > 0 && strlen($name) > 0 && in_array($name,$filed_array)){
			
			 $result = $this->field($name)->where($this->pk.' = '.$id)->find();
			 if(check_array_valid($result)){
			 	
			 	$field_value = $result[$name];
			 }
		}		
		return $field_value;
	}

	/**
	 * 强制删除
	 * @param unknown $where
	 * @param string $clear_cache
	 * @return boolean
	 */
	public function info_force_delete($where,$clear_cache = false){
	
		$check = false;
		if(check_array_valid($where) || (is_string($where) && strlen(trim($where)) > 0)){

			if($clear_cache){
				
				$this->clear_cache($where);//清除对应的cache
			}			
			$result = $this->where($where)->delete();			
			$check = $result === false ? false : true;
		}
		return $check;
	}
	
	/**
	 * 获取错误信息
	 * @return multitype:
	 */
	public function get_error_array(){
		
		return $this->error_array;
	}
	
	/**
	 * 获取错误编码
	 */
	public function get_error_code_array(){
		
		return $this->error_code_array;
	}
	
	/**
	 * 上传对象配置
	 * @param \Think\Upload $upload_object
	 */
	protected function upload_config(\Think\Upload $upload_object){

		// 上传文件
		$upload_object->maxSize = 0; // 设置附件上传大小
		$upload_object->exts = array (
				'jpg',
				'gif',
				'png',
				'jpeg'
		); // 设置附件上传类型
		$upload_object->autoSub = true;
		$upload_object->rootPath = './';
		//$upload->subName = "";
		$upload_object->savePath = './Public/Upload/'; // 设置附件上传目录
	}
	
	/**
	 * 获取上传对象
	 */
	protected function get_upload_object(){
	
		$upload_object = new \Think\Upload();//初始化
		$this->upload_config($upload_object);//上传对象配置
	
		return $upload_object;
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
}
?>