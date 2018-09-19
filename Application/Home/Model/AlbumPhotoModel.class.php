<?php
/**
 *相册及图片对应关系
 *date 2015-05-12
 *autor:kiven pcttcnc2007@126.com
 */

namespace Home\Model;
use Think\Model;

class AlbumPhotoModel extends Model {
	
	protected $default_order_by = 'photo_id';
	protected $error_code_array = array();//错误编码
	protected $error_array = array();//错误信息
	
	public function __construct($name='',$tablePrefix='',$connection='') {
		
		$this->dbName = 'db_photo';
		$this->tableName = C('DB_PREFIX').'album_photo';		
		$this->trueTableName = $this->tableName;
		parent::__construct($name,$tablePrefix,$connection);		
	}
	
	/**
	 * 释放
	 */
	public function __destruct(){
	
		$this->default_order_by = '';		
		$this->error_array = array();
		$this->error_code_array = array();	
	}
	
	/**
	 * 判别是否存在
	 * @param unknown $photo_id
	 * @param unknown $album_id
	 * @return boolean
	 */
	public function check_exists($photo_id,$album_id){
		
		$check = false;
		$photo_id = intval($photo_id);
		$album_id = intval($album_id);
		if($photo_id >= 0 && $album_id >= 0){
			
			$str_where = ' album_id = '.$album_id.' and photo_id = '.$photo_id.' ';
			$count = $this->where($str_where)->count();
			if($count > 0){
				
				$check = true;
			}
		}
		return $check;
	}
	
	/**
	 * 信息添加
	 * @param unknown $info_array
	 * @return Ambigous <\Think\mixed, boolean, string, unknown>|boolean
	 */
	public function info_add($info_array = array()){		
		
		$info_array = check_array_valid($info_array) ? $info_array : array();
		if(count($info_array) > 0){
			
			return $this->data($info_array)->add();
		}else{
			
			return false;
		}		
	}
	
	/**
	 * 信息更新
	 * @param unknown $where
	 * @param unknown $update_array
	 * @return boolean
	 */
	public function info_update($where,$update_array = array()){
	
		$check = false;
		if(count($update_array) > 0 && (check_array_valid($where) || (is_string($where) && strlen(trim($where)) > 0))){
				
			$result = $this->where($where)->data($update_array)->save();
			$check = $result === false ? false : true;
		}
		return $check;
	}
	
	/**
	 * 信息删除
	 * @param unknown $where
	 * @return boolean
	 */
	public function info_delete($where){
		
		$check = false;
		if(check_array_valid($where) || (is_string($where) && strlen(trim($where)) > 0)){
			
			$check = $this->where($where)->delete();
			$check = $check === false ? false : true;
		}
		return $check;		
	}
	
	/**
	 * 信息移动
	 * @param unknown $info_array
	 * @param boolean $check_source_exist
	 * @param boolean $check_dest_exist
	 * @return boolean
	 */
	public function info_move($info_array = array(),$check_source_exist = true,$check_dest_exist = true){
		
		$check = false;		
		$info_array = check_array_valid($info_array) ? $info_array : array();
		if(count($info_array) > 0){
				
			$dest_album_id = isset($info_array['dest_album_id']) ? intval($info_array['dest_album_id']) : 0;
			$source_album_id = isset($info_array['source_album_id']) ? intval($info_array['source_album_id']):0;
			$photo_id = isset($info_array['photo_id']) ? intval($info_array['photo_id']):0;
			if($dest_album_id > 0 && $source_album_id > 0 && $photo_id > 0 && $dest_album_id != $source_album_id){
				
				$check_source_exist = (bool)$check_source_exist;					
				$check_exist = $check_source_exist ? $this->check_exists($photo_id,$source_album_id) : true;			
				
				if($check_exist){
				    
					$check_dest_exist = (bool) $check_dest_exist;
					$check_exist = $check_dest_exist ? $this->check_exists($photo_id,$dest_album_id) : false;
					if(!$check_exist){
						
						$where_array = $update_array = array();
						
						$where_array['photo_id'] = $photo_id;
						$where_array['album_id'] = $source_album_id;
												
						$update_array['album_id'] = $dest_album_id;
						$check = $this->info_update($where_array,$update_array);
						
					}else{
						
						$check = true;
					}				    
				}				
			}			
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
	public function getList($group_by = '',$where,$join_str = '',$field_str = ''){
	
		$list_array = array();
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
			
		$result = $this->select();
		if(check_array_valid($result)){
	
			$list_array = $result;
		}
		return $list_array;
	}
	
	/**
	 * 删除整个相册与相片之间的对应关系
	 * @param unknown $album_id
	 * @return boolean
	 */
	public function client_delete_album($album_id){
		
		$check = false;
		$album_id = intval($album_id);
		if($album_id > 0){
			
			$where = 'album_id = '.$album_id;
			$check = $this->info_delete($where);
		}
		
		return $check;
	}
	
	/**
	 * 批量删除某个相册中的相片
	 * @param unknown $album_id
	 * @param unknown $photo_id_array
	 * @param bool $check_array
	 * @return Ambigous <boolean, \Think\mixed, unknown>
	 */
	public function client_delete($album_id,$photo_id_array = array(),$check_array = true){
		
		$check = false;
		$album_id = intval($album_id);
		
		$photo_id_array = check_array_valid($photo_id_array) ? $photo_id_array : array();
		if($album_id > 0 && check_array_valid($photo_id_array)){
			
			$check_array = (bool) $check_array;
			if($check_array){
				
				$tmp_array = array();
				foreach($photo_id_array as $value){
				
					$tmp_id = intval($value);
					if($tmp_id > 0 && !in_array($tmp_id,$tmp_array)){
							
						$tmp_array[] = $tmp_id;
					}
				}
				$photo_id_array = $tmp_array;
			}
			
			$where = 'album_id = '.$album_id.' and photo_id in ('.implode(',',$photo_id_array).') ';
			$check = $this->info_delete($where);
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