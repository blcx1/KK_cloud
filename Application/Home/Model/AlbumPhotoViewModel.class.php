<?php
/**
 *用户相册及相片对应关系视图
 *date 2015-08-17
 *autor:kiven pcttcnc2007@126.com
 */

namespace Home\Model;
use Think\Model;

class AlbumPhotoViewModel extends Model {
	
	protected $default_order_by = 'photo_id';
	protected $error_code_array = array();//错误编码
	protected $error_array = array();//错误信息
	
	public function __construct($name='',$tablePrefix='',$connection='') {
		
		$this->dbName = 'db_photo';
		$this->tableName = C('DB_PREFIX').'album_photo_view';		
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
	 * 获取某用户某相册列表
	 * @param unknown $user_id
	 * @param unknown $album_id
	 * @param unknown $page_no
	 * @param unknown $page_size
	 * @return multitype:number multitype: Ambigous <\Home\Model\Ambigous, multitype:, unknown>
	 */
	public function get_client_list($user_id,$album_id,$page_no,$page_size){

		$list_array = array();
		$user_id = intval($user_id);
		$album_id = intval($album_id);
		$this->error_array = array();
		$this->error_code_array = array();		
		
		if($user_id > 0 && $album_id > 0){
			
			$server_path = '';
			$list_array = array('total_count'=>0,'page_count'=>0,'list'=>array());
			$user_album_object = new \Home\Model\UserAlbumModel();
			$check_exists = $user_album_object->is_exists($album_id,$user_id,true,false);			
			if($check_exists){
				
				$order_by = 'photo_id';
				$order_way = 'desc';
				$group_by = $join_str = $field_str = '';
				$where = 'is_delete = 0 and status = 1 and album_id = '.$album_id.' and user_id = '.$user_id;
				$page_size = intval($page_size);
				$page_size = $page_size > 0 ? $page_size : 50;
				
				$total_count = $this->get_list_count($group_by,$where,$join_str,$field_str);					
				if($total_count > 0){
				
					$list_array['total_count'] = $total_count;
					$list_array['page_count'] = $this->get_page_count($total_count,$page_size,$group_by,$where,$join_str,$field_str,false);
					$field_str = 'album_id,photo_id,user_id,photo_name,photo_path,small_photo_path,photo_size,photo_width,photo_height,photo_key,photo_type,add_time,update_time';
					$result = $this->get_page_list($page_no,$page_size,$order_by,$order_way,$group_by,$where,$join_str,$field_str);
					$server_path_array = $user_album_object->get_info($album_id,'server_path');
					$server_path = isset($server_path_array['server_path']) ? trim($server_path_array['server_path']) : '';
					
					foreach($result as $key=>$value){
							
						$list_array['list'][$key] = $value;
						$list_array['list'][$key]['photo_path'] = \Home\Model\UserPhotoModel::get_url($server_path,$value['photo_path'],true);						
						$list_array['list'][$key]['small_photo_path'] = \Home\Model\UserPhotoModel::get_url($server_path,$value['small_photo_path'],true);
						
					}
				}
			}
						
		}else{
			
			$this->error_array['result'] = '非法数据';
			$this->error_code_array['result'] = INVALID_DATA;
		}
		
		return $list_array;
	}	
	
	/**
	 * 不限制排序及个数获取列表
	 * @param string $group_by
	 * @param unknown $where
	 * @param string $join_str
	 * @param string $field_str
	 * @return Ambigous <multitype:, \Think\mixed, string, boolean, NULL, unknown, mixed, object>
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
	
	/**
	 * 获取信息
	 * @param unknown $id
	 * @param string $field_str
	 * @return Ambigous <multitype:, \Think\mixed, boolean, NULL, mixed, unknown, string, object>
	 */
	public function get_info($str_where,$field_str = ''){
	
		$info_array = array();		
		if(check_array_valid($str_where) || (is_string($str_where) && strlen(trim($str_where)) > 0)){
				
			$field_str = trim($field_str);			
			if(strlen($field_str) > 0){
	
				$this->field($field_str);
			}
			$result = $this->where ($str_where)->find();
			if(check_array_valid($result)){
					
				$info_array = $result;
			}
		}
	
		return $info_array;
	}
	
	public function get_album_photo_list($user_id,$page_no,$page_size,$is_delete = 0){
		
		$user_id = intval($user_id);
		$list_array = array('total_count'=>0,'list'=>array());
		if($user_id <= 0){
			
			return $list_array;
		}
		$where = array();
		$is_delete = intval($is_delete) == 1 ? 1 : 0;		
		
		$where["v_ap.user_id"] = $user_id;
		$where["v_ap.is_delete"] = $is_delete;
		$where["v_ap.status"] = 1;
		$join_str =' as v_ap inner JOIN db_photo.tb_user_album as a on a.id = v_ap.album_id';
		
		$total_count = $this->get_list_count('',$where,$join_str,'distinct v_ap.album_id');
		if($total_count > 0){
			
			$order_by ="a.add_time";
			$order_way = 'desc';
			$group_by = ' v_ap.album_id';
			$field_str = 'v_ap.album_id as id,COUNT(v_ap.photo_id) as count,a.album_name,a.server_path,a.client_path,a.default_photo_path';
			$result = $this->get_page_list($page_no,$page_size,$order_by,$order_way,$group_by,$where,$join_str,$field_str);
			$list = array();
			if (check_array_valid($result)){
			
				foreach($result as $key=>$val){
						
					$list[$key] = $val;
					$list[$key]['photo_path'] = getFullUrl($val["server_path"].$val["default_photo_path"]);
				}
			}
			$list_array['total_count'] = $total_count;
			$list_array['list'] = $list;
		}
		return $list_array;
	}
}
?>