<?php
/**
 *用户相册
 *date 2015-08-17
 *autor:kiven pcttcnc2007@126.com
 */

namespace Home\Model;
use Think\Model;

class UserAlbumModel extends Model {
	
	protected $default_order_by = 'id';
	protected $current_time;//日期时间戳
	protected $current_datetime;//日期时间按 Y-m-d H:i:s格式
	protected $current_date;//日期时间 按Y-m-d格式
	protected $error_code_array = array();//错误编码
	protected $error_array = array();//错误信息
	protected $mode = '0755';
	protected $upload_base_dir = 'Public/Upload/album/';
	protected $album_photo_view_table = '';
	
	public function __construct($name='',$tablePrefix='',$connection='') {
		
		$this->dbName = 'db_photo';
		$table_prefix = C('DB_PREFIX');
		$db_prefix = $this->dbName.'.'.$table_prefix;
		$this->tableName = $table_prefix.'user_album';
		$this->album_photo_view_table = $db_prefix.'album_photo_view';
		$this->trueTableName = $this->tableName;
		$this->pk = 'id';
		parent::__construct($name,$tablePrefix,$connection);
		
		$this->current_time = time();
		$this->current_datetime = date('Y-m-d H:i:s',$this->current_time);
		$this->current_date = date('Y-m-d',$this->current_time);		
	}
	
	/**
	 * 释放
	 */
	public function __destruct(){
		
		$this->mode = '';
		$this->current_date = '';
		$this->current_time = 0;
		$this->upload_base_dir = '';
		$this->current_datetime = '';
		$this->default_order_by = '';		
		$this->error_array = array();
		$this->error_code_array = array();
		$this->album_photo_view_table = '';
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
			$result = $this->where ($str_where)->find();
			if(check_array_valid($result)){
					
				$info_array = $result;
			}
		}
	
		return $info_array;
	}
	
	/**
	 * 判别是否存在
	 * @param unknown $id
	 * @param unknown $user_id
	 * @param string $check_user
	 * @param string $is_admin
	 * @return boolean
	 */
	public function is_exists($id,$user_id,$check_user = true,$is_admin = false){
		
		$check = false;
		$id = intval($id);		
		if($id > 0){
			
			$user_id = intval($user_id);
			$check_user = (bool) $check_user;			
			if($check_user && $user_id <= 0){
				
				return $check;
			}
			
			$is_admin = (bool) $is_admin;
			$id_name = $this->pk;
			$str_where = $id_name.' = '.$id;
			
			if($check_user){
				
				$str_where .= ' and user_id = '.$user_id;
			}
			if(!$is_admin){
				
				$str_where .= ' and is_delete = 0 and status = 1 ';
			}
			$result = $this->field('id')->where($str_where)->find();
			if(check_array_valid($result)){
			
				$check = true;
			}
		}
		
		return $check;
	}
	
	/**
	 * 判别是否存在
	 * @param unknown $user_id
	 * @param unknown $album_id
	 * @return boolean
	 */
	public function check_exists($user_id,$client_path,$is_admin = false){
		
		$check = false;
		$user_id = intval($user_id);		
		if($user_id >= 0){
			
			$is_admin = (bool) $is_admin;
			$client_path = trim($client_path);
			if(strlen($client_path) > 0 && substr($client_path,-1) != '/'){
				
				$client_path .= '/';
			}
			
			$str_where = ' user_id = '.$user_id;
			if(!$is_admin){
			
				$str_where .= ' and is_delete = 0 and status = 1 ';
			}
			$str_where .= ' and client_path = "'.addslashes($client_path).'" ';
			$result = $this->field('id')->where($str_where)->find();
			if(check_array_valid($result)){
				
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
	
	public function get_album_list($user_id,$page_no,$page_size,$is_delete = 0){
		
		$list_array = array();
		$user_id = intval($user_id);
		$this->error_array = array();
		$this->error_code_array = array();
		
		if($user_id > 0){
				
			$order_by = 'id';
			$order_way = 'desc';
			$group_by = $join_str = $field_str = '';
			$is_delete = intval($is_delete) == 1 ? 1 : 0;
			$where = 'is_delete = '.$is_delete.' and status = 1 and user_id = '.$user_id;
			$page_size = intval($page_size);
			$page_size = $page_size > 0 ? $page_size : 100;
			$list_array = array('total_count'=>0,'page_count'=>0,'list'=>array());
			$total_count = $this->get_list_count($group_by,$where,$join_str,$field_str);
				
			if($total_count > 0){
		
				$list_array['total_count'] = $total_count;
				$list_array['page_count'] = $this->get_page_count($total_count,$page_size,$group_by,$where,$join_str,$field_str,false);
				$field_str = 'id as album_id,user_id,album_name,client_path,server_path,default_photo_path,album_note,total_count,unfinished_total_count,add_time,update_time';
				$result = $this->get_page_list($page_no,$page_size,$order_by,$order_way,$group_by,$where,$join_str,$field_str);
				foreach($result as $key=>$value){
						
					$server_path = trim($value['server_path']);
					unset($value['server_path']);
					$list_array['list'][$key] = $value;
					$list_array['list'][$key]['default_photo_path'] = \Home\Model\UserPhotoModel::get_url($server_path,$value['default_photo_path'],true);
				}
			}
		}else{
				
			$this->error_array['result'] = '非法数据';
			$this->error_code_array['result'] = INVALID_DATA;
		}
		
		return $list_array;
	}
	
	/**
	 * 客户端获取列表
	 * @param unknown $user_id
	 * @param unknown $page_no
	 * @param unknown $page_size
	 * @return Ambigous <multitype:, string, multitype:number multitype: >
	 */
	public function get_client_list($user_id,$page_no,$page_size){
		
		return $this->get_album_list($user_id,$page_no,$page_size,0);		
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
	
	/**
	 * 客户端删除相册
	 * @param array $album_id_array
	 * @param int $user_id
	 */
	public function client_delete($album_id_array,$user_id){
		
		$check = true;
		$user_id = intval($user_id);
		$album_id_array = check_array_valid($album_id_array) ? $album_id_array : array();
		$this->error_array = array();
		$this->error_code_array = array();		
		
		if($user_id > 0 && count($album_id_array) > 0){
			
			$tmp_album_id_array = array();
			foreach($album_id_array as $album_id_value){
				
				$album_id = intval($album_id_value);
				if($album_id > 0 && !in_array($album_id,$tmp_album_id_array)){
					
					$tmp_album_id_array[] = $album_id;
				}
			}
			
			$len = count($tmp_album_id_array);
			if($len > 0){
				
				$field_str = 'id,server_path';
				$str_where = 'user_id = '.$user_id.'  and (id in ('.implode(',',$tmp_album_id_array).')) ';
				$list_array = $this->get_list(0,$len,'id','desc','',$str_where,'',$field_str);
				if(check_array_valid($list_array)){
					
					$server_path_array = $album_id_array = array();					
					foreach($list_array as $list_value){
						
						$album_id = $list_value['id'];
						$album_id_array[] = $album_id;
						$server_path_array[$album_id] = trim($list_value['server_path']);
					}
					$album_id_str = implode(',',$album_id_array);					
					$str_where = 'user_id = '.$user_id.'  and album_id in ('.$album_id_str.')';
					$field_str = 'album_id,photo_id,photo_path,small_photo_path';
					$album_photo_view_object = new \Home\Model\AlbumPhotoViewModel();
					
					$list_array = $album_photo_view_object->getList('',$str_where,'',$field_str);	
					
					if(check_array_valid($list_array)){
						
						$str_where = ' album_id in ('.$album_id_str.') ';
						$album_photo_object = new \Home\Model\AlbumPhotoModel();
						$check = $album_photo_object->info_delete($str_where);
						if($check){
							
							$small_photo_path_array = $photo_path_array = $photo_id_array = array();
							foreach($list_array as $list_value){
									
								$photo_id = $list_value['photo_id'];
								if(in_array($photo_id,$photo_id_array)){
									
									continue;
								}
								$album_id = $list_value['album_id'];
								$server_path = $server_path_array[$album_id];
								$photo_id_array[] = $photo_id;
								$photo_path = trim($list_value['photo_path']);
								$small_photo_path = trim($list_value['small_photo_path']);
								$photo_path_array[$photo_id] = $server_path.$photo_path;
								$small_photo_path_array[$photo_id] = $server_path.$small_photo_path;								
							}
							$field_str = 'photo_id';
							$str_where = ' photo_id in ('.implode(',',$photo_id_array).')';
							
							$list_array = $album_photo_object->get_list(0,count($photo_id_array),'photo_id','desc','',$str_where,'',$field_str);
							if(check_array_valid($list_array)){
							
								foreach($list_array as $value){
							
									$photo_id = $value['photo_id'];
									$key = array_search($photo_id,$photo_id_array);
									if($key != null){
											
										unset($photo_id_array[$key]);
									}
								}									
							}
														
							if(check_array_valid($photo_id_array)){
									
								foreach($photo_id_array as $photo_id){
							
									$photo_path = $photo_path_array[$photo_id];
									if(file_exists($photo_path)){
							
										@unlink($photo_path);
									}
									$small_photo_path = $small_photo_path_array[$photo_id];
									if(file_exists($small_photo_path)){
							
										@unlink($small_photo_path);
									}
									if(C('IS_AWS_URL')){
										
										deleteAwsFile($photo_path);
										deleteAwsFile($small_photo_path);
									}
								}
								$str_where = ' id in ('.implode(',',$photo_id_array).')';
								$user_photo_object = new \Home\Model\UserPhotoModel();
								$check = $user_photo_object->info_delete($str_where);
							}							
						}					
					}
					
					
					if($check){
						$count = $this->get_list_count($str_where);
						if($count<=0){
							
							$str_where = ' id in ('.$album_id_str.')';
							$check = $this->info_delete($str_where);
							
						}
					}
					if(!$check){
						
						$this->error_array['result'] = '删除失败';
						$this->error_code_array['result'] = ERROR_DEL_FAILED;
					}
				}else{
					
					$this->error_array['result'] = '非法数据';
					$this->error_code_array['result'] = INVALID_DATA;
				}
				
			}else{
			
				$this->error_array['result'] = '非法数据';
				$this->error_code_array['result'] = INVALID_DATA;
			}
		}else{
			
			$this->error_array['result'] = '非法数据';
			$this->error_code_array['result'] = INVALID_DATA;
		}
		return $check;
	}
	
	/**
	 * 客户端相片移动
	 * @param unknown $user_id
	 * @param unknown $dest_album_id
	 * @param unknown $source_album_id
	 * @param unknown $photo_id_array
	 * @return boolean
	 */
	public function client_move($user_id,$dest_album_id,$source_album_id,$photo_id_array){
		
		$check = false;
		$user_id = intval($user_id);
		$dest_album_id = intval($dest_album_id);
		$source_album_id = intval($source_album_id);
		$this->error_array = array();
		$this->error_code_array = array();
		
		if($user_id > 0 && $dest_album_id > 0 && $source_album_id > 0 && check_array_valid($photo_id_array)){			
			
			$str_where = 'user_id = '.$user_id.' and is_delete = 0 and (id in ('.strval($dest_album_id).','.strval($source_album_id).')) ';
			$total_count = $this->get_list_count('',$str_where,'','');
			if($total_count == 2){
				
				$tmp_photo_id_array = array();
				foreach($photo_id_array as $photo_id_value){
					
					$photo_id = intval($photo_id_value);
					if($photo_id > 0 && !in_array($photo_id,$tmp_photo_id_array)){
						
						$tmp_photo_id_array[]= $photo_id;
					}
				}
				$len = count($tmp_photo_id_array);
				if($len > 0){
					
					$str_where = 'user_id = '.$user_id.' and is_delete = 0 and album_id = '.$source_album_id.' and photo_id in ('.implode(',',$tmp_photo_id_array).')';
					$field_str = 'photo_id';
					$album_photo_view_object = new \Home\Model\AlbumPhotoViewModel();						
					$list_array = $album_photo_view_object->getList('',$str_where,'',$field_str);
					if(count($list_array) > 0){
						
						$photo_id_array = $tmp_info_array = $info_array = array();
						$check_source_exist = $check_dest_exist = false;
						$tmp_info_array['dest_album_id'] = $dest_album_id;
						$tmp_info_array['source_album_id'] = $source_album_id;						
						$album_photo_object = new \Home\Model\AlbumPhotoModel();
						foreach($list_array as $list_value){

							$photo_id = $list_value['photo_id'];
							$photo_id_array[] = $photo_id;
							$info_array = $tmp_info_array;
							$info_array['photo_id'] = $photo_id;
							$check = $album_photo_object->info_move($info_array,$check_source_exist,$check_dest_exist);
							
							if(!$check){
								
								break;
							}
						}						
						if($check){
							
							$str_where = 'album_id = '.$source_album_id.' and photo_id in ('.implode(',',$photo_id_array).') ';
							$check = $album_photo_object->info_delete($str_where);
							if($check){
								
								$update_array = array();
								$update_array['update_time'] = $this->current_datetime;
								$join_str = $group_by = $field_str = '';
								$str_base_where = ' user_id = '.$user_id.' and is_delete = 0 ';							
								
								$str_where = $str_base_where.' and album_id = '.$source_album_id;
								$update_array['total_count'] = $album_photo_view_object->get_list_count($group_by,$str_where,$join_str,$field_str);
								$str_where .= ' and status = 0 ';
								$update_array['unfinished_total_count'] = $album_photo_view_object->get_list_count($group_by,$str_where,$join_str,$field_str);
								$str_where = $str_base_where.' and id = '.$source_album_id;
								$check = $this->info_update($str_where,$update_array);
								
								$str_where = $str_base_where.' and album_id = '.$dest_album_id;
								$update_array['total_count'] = $album_photo_view_object->get_list_count($group_by,$str_where,$join_str,$field_str);
								$str_where .= ' and status = 0 ';
								$update_array['unfinished_total_count'] = $album_photo_view_object->get_list_count($group_by,$str_where,$join_str,$field_str);
								$str_where = $str_base_where.' and id = '.$dest_album_id;
								$check = $this->info_update($str_where,$update_array);
								if($check){
														
									$this->update_default_path(array($source_album_id));
								}
							}
						}else{
							
							$this->error_array['result'] = '操作失败';
							$this->error_code_array['result'] = ERROR_OPERATE_FAILED;
						}
					}else{
				
						$this->error_array['result'] = '非法数据';
						$this->error_code_array['result'] = INVALID_DATA;
					}				
				}else{
				
					$this->error_array['result'] = '非法数据';
					$this->error_code_array['result'] = INVALID_DATA;
				}
				
			}else{
				
				$this->error_array['result'] = '非法数据';
				$this->error_code_array['result'] = INVALID_DATA;
			}
		}else{
				
			$this->error_array['result'] = '非法数据';
			$this->error_code_array['result'] = INVALID_DATA;
		}
		
		return $check;
	}
	
	/**
	 * 客户端相片复制
	 * @param unknown $user_id
	 * @param unknown $dest_album_id
	 * @param unknown $source_album_id
	 * @param unknown $photo_id_array
	 * @return boolean
	 */
	public function client_copy($user_id,$dest_album_id,$source_album_id,$photo_id_array){
		$check = false;
		$user_id = intval($user_id);
		$dest_album_id = intval($dest_album_id);
		$source_album_id = intval($source_album_id);
		$this->error_array = array();
		$this->error_code_array = array();
		$error_arrayaaaa = "asdasd";
		if($user_id > 0 && $dest_album_id > 0 && $source_album_id > 0 && check_array_valid($photo_id_array)){
			$field_str = 'id as album_id,server_path';
			$str_where = 'user_id = '.$user_id.' and is_delete = 0 and (id in ('.strval($dest_album_id).','.strval($source_album_id).')) ';
			$result = $this->get_list(0,2,'id','desc','',$str_where,'',$field_str);			
			if(count($result) == 2){
	
				$tmp_photo_id_array = array();
				foreach($photo_id_array as $photo_id_value){
						
					$photo_id = intval($photo_id_value);
					if($photo_id > 0 && !in_array($photo_id,$tmp_photo_id_array)){
	
						$tmp_photo_id_array[]= $photo_id;
					}
				}
				$len = count($tmp_photo_id_array);
				if($len > 0){
					
					$error_status = true;
					$check_mk_dir = false;
					foreach($result as $value){
						
						$album_id = $value['album_id'];
						$server_path = trim($value['server_path']);
						if($album_id == $source_album_id){
							
							$source_server_path = $server_path;
							if(empty($source_server_path) || !is_dir($source_server_path)){
								
								$this->error_array['result'] = '目录不存在，且创建目录失败';
								$this->error_code_array['result'] = ERROR_MK_DIR;								
								break;
							}
						}else{
							
							$dest_server_path = $server_path;
							if(!empty($dest_server_path) && !is_dir($dest_server_path)){
								
								$this->error_array['result'] = '目录不存在，且创建目录失败';
								$this->error_code_array['result'] = ERROR_MK_DIR;
								break;
							}elseif(empty($dest_server_path)){
								
								$check_mk_dir = true;
								$dest_server_path = $this->mkdir_server_path($user_id,$error_status);								
							}
							$dest_server_small_path = $this->mkdir_small_server_path($user_id,$error_status);
						}
					}
					if(count($this->error_array) > 0 || count($this->error_code_array) > 0){
						
						return $check;						
					}
					$current_datetime = $this->current_datetime;
					if($check_mk_dir){
						
						$where = 'id = '.$dest_album_id;
						$update_array = array();
						$update_array['server_path'] = $dest_server_path;
						$update_array['update_time'] = $current_datetime;
						$check = $this->info_update($where,$update_array);
						if(!$check){
							
							$this->error_array['result'] = '操作失败';
							$this->error_code_array['result'] = ERROR_OPERATE_FAILED;
							return false;
						}
					}	
					
					$str_where = 'user_id = '.$user_id.' and is_delete = 0 and status = 1 and album_id = '.$source_album_id.' and photo_id in ('.implode(',',$tmp_photo_id_array).')';
					$field_str = 'photo_name,photo_path,small_photo_path,photo_size,photo_width,photo_height,photo_type';
					$album_photo_view_object = new \Home\Model\AlbumPhotoViewModel();
					$list_array = $album_photo_view_object->getList('',$str_where,'',$field_str);
					if(count($list_array) > 0){
						
						$j = 0;
						$album_small_photo_array = array();
						$current_time = $this->current_time;
						$user_photo_object = new \Home\Model\UserPhotoModel();
						$album_photo_object = new \Home\Model\AlbumPhotoModel();						
						foreach($list_array as $list_value){
														
							$source_photo_path = trim($list_value['photo_path']);
							$source_file_path = $source_server_path.$source_photo_path;

							if(!file_exists($source_file_path)){
							
								$this->error_array['result'] = '文件不存在';
								$this->error_code_array['result'] = ERROR_FILE_NOT_EXISTS;
								return false;
							}
							$file_format = '';
							$source_photo_path_array = explode('.',$source_photo_path);
							if(is_array($source_photo_path_array)){
									
								$file_format = '.';
								$file_format .= end($source_photo_path_array);
							}
							$photo_path = date('YmdHis',$current_time + $j).strval(rand(111111,999999)).$file_format;
							$file_path = $dest_server_path.$photo_path;
							
						
							if(copy($source_file_path,$file_path)){
																
								if(C('IS_AWS_URL')){
										
									$check = $user_photo_object->aws_upload_process($file_path);
									if(!$check){
							
										return false;
									}
								}
								
								$small_photo_path = $user_photo_object->small_photo_generation($dest_server_small_path,$file_path);
								
								$add_array = $list_value;								
								$add_array['user_id'] = $user_id;
								$add_array['photo_tmp_path'] = '';
								$add_array['photo_path'] = $photo_path;
								$add_array['small_photo_path'] = $small_photo_path;
								$add_array['is_delete'] = 0;
								$add_array['status'] = 1;
								$add_array['photo_key'] = md5_file($file_path);
								$add_array['add_time'] = $current_datetime;
								$add_array['update_time'] = $current_datetime;
								$id = $user_photo_object->info_add($add_array);
								if(intval($id) > 0){
									
									$add_array = array();
									$add_array['photo_id'] = $id;
									$add_array['album_id'] = $dest_album_id;
									$result = $album_photo_object->info_add($add_array);
									if($result === false){
										
										$this->error_array['result'] = '操作失败';
										$this->error_code_array['result'] = ERROR_OPERATE_FAILED;
										return false;
									}			
									if(!empty($small_photo_path)){
										
										$album_small_photo_array = array('default_photo_id'=>$id,'default_photo_path'=>$small_photo_path);
									}
								}else{
									
									$this->error_array['result'] = '操作失败';
									$this->error_code_array['result'] = ERROR_OPERATE_FAILED;
									return false;
								}
								
							}else{
							
								$this->error_array['result'] = '文件复制失败';
								$this->error_code_array['result'] = ERROR_FILE_COPY_FAILED;
								return false;
							}							
							$j++;
						}
						
						$update_array = array();
						$update_array['update_time'] = $current_datetime;						
						$str_where = ' user_id = '.$user_id.' and is_delete = 0 and album_id = '.$dest_album_id;				
						$update_array['total_count'] = $album_photo_view_object->get_list_count('',$str_where,'','');
						$str_where .= ' and status = 0 ';
						$update_array['unfinished_total_count'] = $album_photo_view_object->get_list_count('',$str_where,'','');
						if(count($album_small_photo_array) > 0){
							
							$update_array['default_photo_id'] = $album_small_photo_array['default_photo_id'];
							$update_array['default_photo_path'] = $album_small_photo_array['default_photo_path'];
						}
						$where = 'id = '.$dest_album_id;
						$result = $this->info_update($where,$update_array);
						$check = $result === false ? false : true;	
					
					}else{
					
						$this->error_array['result'] = '非法数据';
						$this->error_code_array['result'] = INVALID_DATA;
					}
					
				}else{
					
					$this->error_array['result'] = '非法数据';
					$this->error_code_array['result'] = INVALID_DATA;
				}
			
			}else{
				
				$this->error_array['result'] = '非法数据';
				$this->error_code_array['result'] = INVALID_DATA;
			}
			
		}else{
			
			$this->error_array['result'] = '非法数据';
			$this->error_code_array['result'] = INVALID_DATA;
		}
	
		$this->error_array['result'] = '啊实打实大';
		return $check;
	}
	
	/**
	 * 修改默认图片
	 * @param unknown $album_id
	 * @param unknown $default_photo_id
	 * @param unknown $user_id
	 * @param string $check_error
	 * @return boolean
	 */
	public function change_default_photo($album_id,$default_photo_id,$user_id,$check_error = false){
		
		$check = false;
		$user_id = intval($user_id);
		$album_id = intval($album_id);
		$check_error = (bool) $check_error;
		$default_photo_id = intval($default_photo_id);
		if($album_id > 0 && $user_id > 0 && $default_photo_id > 0){
			
			$album_photo_view_object = new \Home\Model\AlbumPhotoViewModel();
			$field_str = 'photo_id,small_photo_path';
			$str_where = 'album_id = '.$album_id.' and photo_id = '.$default_photo_id.' and user_id = '.$user_id;
			$info_array = $album_photo_view_object->get_info($str_where,$field_str);
			
			if(check_array_valid($info_array)){
				
				$update_array = array();				
				$default_photo_path = trim($info_array['small_photo_path']);
				$str_where = 'id = '.$album_id.' and user_id ='.$user_id;
				$update_array['default_photo_id'] = $default_photo_id;
				$update_array['default_photo_path'] = $default_photo_path;
				$update_array['update_time'] = $this->current_datetime;
				$check = $this->info_update($str_where,$update_array);
			}elseif($check_error){
			
				$this->error_array['result'] = '非法数据';
				$this->error_code_array['result'] = INVALID_DATA;
			}
			
			
		}elseif($check_error){
			
			$this->error_array['result'] = '非法数据';
			$this->error_code_array['result'] = INVALID_DATA;
		}
		return $check;
	}
	
	/**
	 * 修改本地路径
	 * @param unknown $album_id
	 * @param unknown $client_path
	 * @param unknown $user_id
	 * @param string $check_error
	 * @return boolean
	 */
	public function change_client_path($album_id,$client_path,$user_id,$check_error = false){
		
		$check = false;
		$user_id = intval($user_id);
		$album_id = intval($album_id);
		$check_error = (bool) $check_error;
		$client_path = trim($client_path);
		if($album_id > 0 && $user_id > 0 && strlen($client_path) > 0){
			
			if(substr($client_path, -1) != '/'){
				
				$client_path .= '/';
			}
			
			$check_exists = $this->check_exists($user_id,$client_path,true);					
			if(!$check_exists){
				
				$update_array = array();
				$str_where = 'id = '.$album_id.' and user_id ='.$user_id;
				$update_array['client_path'] = $client_path;
				$update_array['update_time'] = $this->current_datetime;				
				$check = $this->info_update($str_where,$update_array);				
			}elseif($check_error){
				
				$this->error_array['result'] = '文件目录已存在';
				$this->error_code_array['result'] = ERROR_DIR_EXISTS;
			}			
			
		}elseif($check_error){
			
			$this->error_array['result'] = '非法数据';
			$this->error_code_array['result'] = INVALID_DATA;
		}
		return $check;
	}
	
	/**
	 * 修改相册名称
	 * @param unknown $album_id
	 * @param unknown $album_name
	 * @param unknown $user_id
	 * @param string $check_error
	 * @return boolean
	 */
	public function change_album_name($album_id,$album_name,$user_id,$check_error = false){
	
		$check = false;
		$user_id = intval($user_id);
		$album_id = intval($album_id);
		$check_error = (bool) $check_error;
		$album_name = trim($album_name);
		if($album_id > 0 && $user_id > 0 && strlen($album_name) > 0){
				
			$update_array = array();
			$str_where = 'id = '.$album_id.' and user_id ='.$user_id;
			$update_array['album_name'] = $album_name;
			$update_array['update_time'] = $this->current_datetime;
			$check = $this->info_update($str_where,$update_array);
		}elseif($check_error){
				
			$this->error_array['result'] = '非法数据';
			$this->error_code_array['result'] = INVALID_DATA;
		}
		return $check;
	}
	
	/**
	 * 修改相册备注
	 * @param unknown $album_id
	 * @param unknown $album_note
	 * @param unknown $user_id
	 * @param string $check_error
	 * @return boolean
	 */
	public function change_album_note($album_id,$album_note,$user_id,$check_error = false){
	
		$check = false;
		$user_id = intval($user_id);
		$album_id = intval($album_id);
		$check_error = (bool) $check_error;
		$album_note = trim($album_note);
		if($album_id > 0 && $user_id > 0 && strlen($album_note) > 0){

			$update_array = array();
			$str_where = 'id = '.$album_id.' and user_id ='.$user_id;
			$update_array['album_note'] = $album_note;
			$update_array['update_time'] = $this->current_datetime;
			$check = $this->info_update($str_where,$update_array);
			
		}elseif($check_error){
				
			$this->error_array['result'] = '非法数据';
			$this->error_code_array['result'] = INVALID_DATA;
		}
		return $check;
	}
	
	/**
	 * 修改用户相册相关信息
	 * @param unknown $album_id
	 * @param unknown $user_id
	 * @param unknown $change_name
	 * @param unknown $change_type 0:修改默认图片  1:修改本地路径  2:修改相册名称  3:修改相册备注
	 */
	public function client_change($album_id,$user_id,$change_name,$change_type){
		
		$check = true;		
		$user_id = intval($user_id);
		$album_id = intval($album_id);
		
		if($user_id > 0 && $album_id > 0 && $this->is_exists($album_id,$user_id,true,false)){
			
			$method_name = '';
			$check_method = $check_error = true;			
			$change_type = intval($change_type);
			
			switch($change_type){
				
				case 0://修改默认图片
						
					$method_name = 'change_default_photo';					
					break;
				case 1://修改本地路径
					
					$method_name = 'change_client_path';
					break;
				case 2://修改相册名称
				
					$method_name = 'change_album_name';
					break;
				case 3://修改相册备注
						
					$method_name = 'change_album_note';
					break;
				default :
					
					$check_method = false;					
					break;
			}
			
			if($check_method && method_exists($this,$method_name)){
				
				$check = $this->$method_name($album_id,$change_name,$user_id,$check_error);
			}elseif(!$check_method){
				
				$this->error_array['result'] = '非法数据';
				$this->error_code_array['result'] = INVALID_DATA;
			}else{
				
				$this->error_array['result'] = '方法不存在';
				$this->error_code_array['result'] = METHOD_NOT_EXISTS;
			}
		}else{
				
			$this->error_array['result'] = '非法数据';
			$this->error_code_array['result'] = INVALID_DATA;
		}
		return $check;
	}
	
	/**
	 * 创建目录
	 *
	 **/
	protected function mk_dir($dir,$mode,$error_status = false){
	
		$check = false;
		$error_status = (bool) $error_status;
		if(is_dir($dir) || @mkdir($dir,$mode)){
	
			$check = true;
		}
	
		if($error_status && !$check){
	
			$this->error_array['result'] = '目录不存在，且创建目录失败';
			$this->error_code_array['result'] = ERROR_MK_DIR;
		}
	
		return $check;
	}
	
	/**
	 * 创建相册目录
	 * @param unknown $user_id
	 * @param string $error_status
	 * @return string
	 */
	public function mkdir_server_path($user_id,$error_status = false){
	
		$server_path = '';
		$user_id = intval($user_id);
		if($user_id > 0){
				
			$upload_base_dir = $this->upload_base_dir;
			if(substr($upload_base_dir,-1) != '/'){
	
				$upload_base_dir .= '/';
				$this->upload_base_dir = $upload_base_dir;
			}
			$mode = $this->mode;
			$check = $this->mk_dir(substr($this->upload_base_dir,0,-1),$mode,$error_status);
			if($check){
	
				$user_id_str = strval($user_id);
				$dir = $this->upload_base_dir.$user_id_str;
				$check = $this->mk_dir($dir,$mode,$error_status);
				if($check){
	
					$start = $user_id%10;
					$dir .= '/'.substr(md5($user_id),$start,6);
					$check = $this->mk_dir($dir,$mode,$error_status);
					if($check){
	
						$server_path = $dir.'/';
						$fh = @fopen($server_path.'index.html','ab');
						@fclose($fh);
					}
				}
			}
				
		}elseif($error_status){
				
			$this->error_array['result'] = '非法数据';
			$this->error_code_array['result'] = INVALID_DATA;
		}
		return $server_path;
	}
	
	/**
	 * 创建相册小图目录
	 * @param unknown $user_id
	 * @param string $error_status
	 * @return string
	 */
	public function mkdir_small_server_path($user_id,$error_status = false){
	
		$small_server_path = '';
		$server_path = $this->mkdir_server_path($user_id,$error_status);
		if(strlen($server_path) > 1){
				
			if(substr($server_path,-1) != '/'){
					
				$server_path .= '/';
	
			}
			$dir = $server_path.'small';
			$check = $this->mk_dir($dir,$this->mode,$error_status);
			if($check){
					
				$small_server_path = $dir.'/';
				$fh = @fopen($small_server_path.'index.html','ab');
				@fclose($fh);
			}
		}
		return $small_server_path;
	}
	
	/**
	 * 同步相册目录
	 * @param unknown $user_id
	 * @param unknown $album_id
	 * @param unknown $client_path
	 * @return boolean|multitype:
	 */
	public function client_synchro($user_id,$album_id,$client_path){
				
		$info_array = array();
		$user_id = intval($user_id);
		$this->error_array = array();
		$this->error_code_array = array();		
		$client_path = strip_tags($client_path);
		$client_path = trim($client_path);
		if($user_id > 0 && strlen($client_path) > 0){
		
			$server_path = '';			
			$error_status = true;			
			$album_id = intval($album_id);
			if(substr($client_path,-1) != '/'){
					
				$client_path .= '/';
			}
						
			if($album_id <= 0){
		
				$album_id = $this->get_id($user_id,$client_path);				
				if($album_id == 0){
										
					$server_path = $this->mkdir_server_path($user_id,$error_status);
					if(count($this->error_code_array) > 0 || count($this->error_array) > 0){
							
						return $info_array;
					}
					$add_info = array();
					$client_path_array = explode('/',$client_path);
					$album_name = $client_path_array[count($client_path_array) -2];
					
					$add_info['user_id'] = $user_id;
					$add_info['client_path'] = addslashes($client_path);
					$add_info['album_name'] = addslashes($album_name);
					$add_info['server_path'] = addslashes($server_path);
					$add_info['default_photo_id'] = 0;
					$add_info['default_photo_path'] = '';
					$add_info['is_delete'] = 0;
					$add_info['status'] = 1;
					$add_info['album_note'] = '';
					$add_info['total_count'] = 0;
					$add_info['unfinished_total_count'] = 0;
					$add_info['add_time'] = $this->current_datetime;
					$add_info['update_time'] = $this->current_datetime;
					$result = $this->info_add($add_info);
					$album_id = $result === false ? 0 : intval($result);
					if($album_id <= 0){
		
						$this->error_array['result'] = '非法数据';
						$this->error_code_array['result'] = INVALID_DATA;
						return $info_array;
					}					
				}		
			}
			if($album_id > 0){
		
				$field_str = 'id as album_id,user_id,album_name,client_path,server_path,default_photo_id,default_photo_path,album_note,total_count,unfinished_total_count';
				$info_array = $this->get_info($album_id,$field_str);
				if(!check_array_valid($info_array) || $user_id != $info_array['user_id'] || $client_path != stripslashes($info_array['client_path'])){
						
					$this->error_array['result'] = '非法数据';
					$this->error_code_array['result'] = INVALID_DATA;
					return array();
				}
				$server_path = trim($info_array['server_path']);
				unset($info_array['server_path']);
				if($server_path == ''){
		
					$server_path = $this->mkdir_server_path($user_id,$error_status);
					if(count($this->error_code_array) > 0 || count($this->error_array) > 0){
					
						return array();
					}
										
					$update_array = array();
					$str_where = ' id = '.$album_id;
					$update_array['server_path'] = addslashes($server_path);
					$update_array['update_time'] = $this->current_datetime;		
					
					$check = $this->info_update($str_where,$update_array);
					if(!$check){
						
						$this->error_array['result'] = '同步失败';
						$this->error_code_array['result'] = ERROR_SYNC_FAILED;						
						return array();
					}					
				}		
			}
		}else{
				
			$this->error_array['result'] = '非法数据';
			$this->error_code_array['result'] = INVALID_DATA;
		}
		
		return $info_array;
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
	 * 获取id值
	 * @param unknown $user_id
	 * @param unknown $client_path
	 * @return number
	 */
	public function get_id($user_id,$client_path){
		
		$id = 0;
		$user_id = intval($user_id);
		$client_path = trim($client_path);
		if($user_id > 0 && strlen($client_path) > 0){
			
			$str_where = 'user_id = '.$user_id.' and client_path = "'.addslashes($client_path).'" ';
			$result = $this->field('id')->where($str_where)->find();
			if(check_array_valid($result)){
				
				$id = intval($result['id']);
			}
		}
		return $id;
	}	
	
	public function get_upload_base_dir(){
	
		return $this->upload_base_dir;
	}
	
	/**
	 * 更新默认图片
	 * @param unknown $album_array
	 * @return Ambigous <boolean, \Think\false>
	 */
	public function update_default_path($album_array = array()){
		
		$check = false;
		if(check_array_valid($album_array)){
			
			$album_str = implode(',',$album_array);
			$table = $this->dbName.'.'.$this->trueTableName;
			
			$sql = 'update '.$table.' ua,'.$this->album_photo_view_table.' ap	set ua.`default_photo_id` = ap.photo_id,ua.`default_photo_path` = ap.small_photo_path where ap.album_id = ua.id and ap.is_delete = 0 and ap.status = 1 and ap.small_photo_path != "" and ap.album_id in ('.$album_str.')';			
			$this->execute($sql);
						
			$sql = 'update '.$table.' set default_photo_id = 0,default_photo_path = "" where (id in ('.$album_str.')) and (id not in (select album_id from '.$this->album_photo_view_table.' where is_delete = 0 and status = 1 and small_photo_path != "" and album_id in ('.$album_str.')) ';				
			$check = $this->execute($sql);
				
		}
		return $check;		
	}
}
?>