<?php
/**
 *用户相片
 *date 2015-08-17
 *autor:kiven pcttcnc2007@126.com
 */

namespace Home\Model;
use Think\Model;
use \Org\Util\Image;

class UserPhotoModel extends Model {
	
	protected $default_order_by = 'id';
	protected $current_time;//日期时间戳
	protected $current_datetime;//日期时间按 Y-m-d H:i:s格式
	protected $current_date;//日期时间 按Y-m-d格式
	protected $error_code_array = array();//错误编码
	protected $error_array = array();//错误信息	
	protected $max_size = 6291456;//最大限制
	protected $allow_file_types = '|GIF|JPG|PNG|JPEG|BMP|WAV|SWF|MID|3GP|MP4|RM|FLV|WMV|ASF|ASX|RMVB|MPG|MPEG|MPE|MOV|M4V|AVI|MKV|F4V|';
	protected $upload_file_info = array();
	protected static $size_array = array();
	protected $aws_vendor_object = null;
	protected $upload_file_is_img = false;
	protected $upload_file_total_size = 0;
	protected static $default_photo = 'Public/Upload/avatar/albumphoto.png';
	protected $ignore_type_array = array('bmp'=>array('image/bmp'),'3gp'=>array('video/mp4','video/3gp','video/3gpp'),'mp4'=>array('video/mp4','video/x-flv'),
                                         'mpg4'=>array('video/mp4'),'wav'=>array('audio/wav'),'wmv'=>array('audio/x-ms-wmv'),'asx'=>array('video/x-ms-asf'),
										 'asf'=>array('video/x-ms-asf'),'rm'=>array('audio/x-pn-realaudio'),'rmvb'=>array('audio/x-pn-realaudio'),
										 'mpg'=>array('video/mpeg'),'mpeg'=>array('video/mpeg'),'mpe'=>array('video/x-mpeg'),'avi'=>array('video/x-msvideo'),
										 'm4v'=>array('video/x-m4v'),'mov'=>array('video/quicktime'),'flv'=>array('video/x-flv'),'f4v'=>array('video/mp4','video/x-f4v'),
                                         'mkv'=>array('video/x-matroska'));
	
	public function __construct($name='',$tablePrefix='',$connection='') {
		
		$this->dbName = 'db_photo';
		$this->tableName = C('DB_PREFIX').'user_photo';		
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
		
		$this->max_size = 0;
		$this->current_date = '';
		$this->current_time = 0;
		$this->current_datetime = '';
		$this->allow_file_types = '';
		$this->default_order_by = '';
		$this->error_array = array();
		$this->error_code_array = array();
		$this->upload_file_info = array();
		self::$size_array = array();
		$this->aws_vendor_object = null;
		$this->upload_file_total_size = 0;
		self::$default_photo = '';
		$this->ignore_type_array = array();
	}
	
	/**
	 * 获取信息
	 * @param unknown $id
	 * @param string $field_str
	 * @return Ambigous <multitype:, \Think\mixed, boolean, NULL, mixed, unknown, string, object>
	 */
	public function get_info($id,$field_str = ''){
	
		$info_array = array();
		$id = intval($id);
		if($id > 0){
				
			$field_str = trim($field_str);
			$str_where = $this->pk.' = '.$id.' ';
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
	 * @param unknown $photo_id
	 * @param unknown $album_id
	 * @return boolean
	 */
	public function check_exists($photo_id,$user_id = 0,$check_user = false,$is_all = false){
		
		$check = false;
		$photo_id = intval($photo_id);		
		if($photo_id > 0){
			
			$check_user = (bool)$check_user;
			if($check_user && $user_id <= 0){
				
				return $check;
			}
			$is_all = (bool) $is_all;
			$str_where = 'id = '.$photo_id.' ';
			if($check_user){
				
				$str_where .= ' and user_id = '.$user_id;
			}
			
			if(!$is_all){
				
				$str_where .= ' and is_delete = 0 and status = 1';
			}
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
	 * 客户端删除某个相册下的相关相片
	 * @param unknown $user_id
	 * @param unknown $album_id
	 * @param unknown $photo_id_array
	 * @return boolean
	 */
	public function client_delete($user_id,$album_id,$photo_id_array = array()){
		
		$check = false;		
		$user_id = intval($user_id);
		$album_id = intval($album_id);
		$this->error_array = array();
		$this->error_code_array = array();
		if(!check_array_valid($photo_id_array)){
			
			$photo_id = intval($photo_id_array);
			$photo_id_array = array();
			$photo_id_array['0'] = $photo_id;
		}
		
		$user_album_object = new \Home\Model\UserAlbumModel();		
		$extend_where = ' and user_id = '.$user_id;	
		$user_album_info = $user_album_object->get_info($album_id,'id,server_path',$extend_where);
		if($user_id > 0 && $album_id > 0 && count($photo_id_array) > 0 && check_array_valid($user_album_info)){
			
			$photo_id_tmp_array = array();
			foreach($photo_id_array as $key=>$value){
					
				$tmp_id = intval($value);
				if($tmp_id > 0 && !in_array($tmp_id,$photo_id_tmp_array)){
			
					$photo_id_tmp_array[] = $tmp_id;
				}
			}
			$photo_id_array = $photo_id_tmp_array;
			$len = count($photo_id_array);
			if($len > 0){				

				$str_where_base = 'user_id = '.$user_id.' and is_delete = 1 and album_id = '.$album_id;
				$str_where = $str_where_base.' and photo_id in ('.implode(',',$photo_id_array).')';
				$field_str = 'photo_id,photo_path,small_photo_path';
				$album_photo_view_object = new \Home\Model\AlbumPhotoViewModel();
				
				$list_array = $album_photo_view_object->getList('',$str_where,'',$field_str);				
				if(check_array_valid($list_array)){					
					
					$server_path = trim($user_album_info['server_path']);			
					$small_photo_path_array = $photo_path_array = $photo_id_array = $photo_id_tmp_array = array();
					
					foreach($list_array as $list_value){
						
						$photo_id = $list_value['photo_id'];
						$photo_id_array[] = $photo_id;
						$photo_path_array[$photo_id] = $server_path.trim($list_value['photo_path']);
						$small_photo_path_array[$photo_id] = $server_path.trim($list_value['small_photo_path']);						
					}
					
					$album_photo_object = new \Home\Model\AlbumPhotoModel();
					$check = $album_photo_object->client_delete($album_id,$photo_id_array,false);
					if($check){
						
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
							
							$photo_id_str = implode(',',$photo_id_array);
							$str_where = ' id in ('.$photo_id_str.')';
							$str_where_default_photo = 'user_id = '.$user_id.' and default_photo_id in ('.$photo_id_str.')';
							$check = $this->info_delete($str_where);	
							if($check){
								
								$str_where_default_photo_tmp = $str_where_default_photo.' and total_count > 1 ';
								$result = $user_album_object->getList('',$str_where_default_photo_tmp,'','id');
								if(check_array_valid($result)){
									
									foreach($result as $key=>$value){
										
										$album_id_array[] = $value['id'];
									}
									$user_album_object->update_default_path($album_id_array);									
								}else{	
																	
									$update_array = array();
									$update_array['default_photo_id'] = 0;
									$update_array['default_photo_path'] = '';
									$update_array['update_time'] = $this->current_datetime;
									$str_where_default_photo_tmp = $str_where_default_photo.' and total_count <= 1 ';
									$user_album_object->info_update($str_where_default_photo_tmp,$update_array);
								}																					
							}						
						}
						$str_where = $str_where_base;
						$total_count = $album_photo_view_object->get_list_count('',$str_where,'','');
						$unfinished_str_where = $str_where_base.' and status = 0 ';
						$unfinished_total_count = $album_photo_view_object->get_list_count('',$unfinished_str_where,'','');						
						$str_where = 'id = '.$album_id;
						$update_array = array();
						$update_array['total_count'] = $total_count;
						$update_array['unfinished_total_count'] = $unfinished_total_count;
						$update_array['update_time'] = $this->current_datetime;
						$user_album_object->info_update($str_where,$update_array);					
					}
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
	 * 获取相册列表
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
	 * 客户端上传
	 * @param unknown $user_id
	 * @param unknown $album_id
	 * @param unknown $photo_id
	 * @param unknown $client_path
	 * @param unknown $update_style 上传方式，0为直接一次上传文件，1为断点续传方式上传文件
	 * @param unknown $upload_type 上传类型 ;0:开始上传,1:为续传,2:结束上传
	 * @param unknown $width
	 * @param unknown $height
	 * @param unknown $file_name
	 * @param unknown $file_size 已上传的大小
	 * @return boolean|multitype:|Ambigous <string, multitype:Ambigous <number, \Home\Model\Ambigous, multitype:, \Think\mixed, boolean, NULL, mixed, unknown, string> Ambigous <\Home\Model\Ambigous, multitype:, \Think\mixed, boolean, NULL, mixed, unknown, string, object> >
	 */
	public function client_upload($user_id,$album_id,$photo_id,$client_path,$update_style,$upload_type,$width,$height,$file_name,$file_size){
		
		$check = false;		
		$list_array = array();		
		$user_id = intval($user_id);
		$album_id = intval($album_id);
		$this->error_array = array();
		$this->error_code_array = array();
		$client_path = strip_tags($client_path);
		$client_path = trim($client_path);	
		
		if($user_id > 0 && $album_id > 0 && strlen($client_path) > 0){
			
			$tmp_path = '';
			$upload_name = 'album_photo';
			$error_status = $check_user = true;
			$small_server_path = $server_path = '';			
			
			$photo_id = intval($photo_id);		
			if($photo_id > 0){
				
				$check_exists = $this->check_exists($photo_id,$user_id,true,true);
				if(!$check_exists){
					
					$this->error_array['result'] = '非法数据';
					$this->error_code_array['result'] = INVALID_DATA;
					return $check;
				}
				$field_str = 'photo_tmp_path,photo_path,small_photo_path';
				$photo_info = $this->get_info($photo_id,$field_str);
				$tmp_path = $photo_info['photo_tmp_path'];
			}
						
			$user_album_object = new \Home\Model\UserAlbumModel();
			$field_str = 'id as album_id,user_id,album_name,client_path,server_path,default_photo_id,default_photo_path,album_note,total_count,unfinished_total_count';
			$info_array = $user_album_object->get_info($album_id,$field_str);
			
			if(!check_array_valid($info_array) || $user_id != $info_array['user_id'] || $client_path != stripslashes(trim($info_array['client_path']))){
					
				$this->error_array['result'] = '非法数据';
				$this->error_code_array['result'] = INVALID_DATA;
				return $check;
			}
			$server_path = trim($info_array['server_path']);			
			if($server_path == ''){
			
				$server_path = $user_album_object->mkdir_server_path($user_id,$error_status);
				$small_server_path = $user_album_object->mkdir_small_server_path($user_id,$error_status);
				if(count($this->error_code_array) > 0 || count($this->error_array) > 0){
				
					return $check;
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
			if($small_server_path == ''){
				
				$small_server_path = $user_album_object->mkdir_small_server_path($user_id,$error_status);
				if(count($this->error_code_array) > 0 || count($this->error_array) > 0){
				
					return $check;
				}
			}
			unset($info_array['server_path']);
			$check = $this->upload_file_process($server_path,$upload_name,$upload_type,$update_style,$small_server_path,$tmp_path,$width,$height,$file_name,$file_size);
			if($check){
				
				$upload_file_info = $this->upload_file_info;
				if(check_array_valid($upload_file_info)){
					
					if($upload_file_info['photo_tmp_path'] == ''){
						
						unset($upload_file_info['photo_tmp_path']);
					}					
					if($upload_file_info['small_photo_path'] == ''){
						
						unset($upload_file_info['small_photo_path']);
					}
					if($upload_file_info['photo_path'] != '' ){
						
						$upload_file_info['photo_tmp_path'] = '';
						$upload_file_info['status'] = 1;
						$upload_file_info['photo_path'] = $this->save_path($user_album_object,$upload_file_info['photo_path'],$server_path);
					}else{
						
						$upload_file_info['status'] = 0;
					}
					$check_exists = false;					
					$album_photo_object = new \Home\Model\AlbumPhotoModel();
					$album_photo_view_object = new \Home\Model\AlbumPhotoViewModel();
					if($photo_id > 0){
																		
						$update_array = array();
						$str_where = 'id = '.$photo_id;
						$update_array = $upload_file_info;
						
						$update_array['update_time'] = $this->current_datetime;
						
						$check = $this->info_update($str_where,$update_array);
						$check_exists = $album_photo_object->check_exists($photo_id,$album_id);
											
						if($check){					
														
							$photo_path = trim($photo_info['photo_path']);
							if(strlen($photo_path) > 0){
								
								$delete_path = self::get_path($server_path,$photo_path);								
								deleteAwsFile($delete_path);
								if(file_exists($delete_path)){
								
									@unlink($delete_path);
								}
							}
							$photo_path = trim($photo_info['small_photo_path']);							
							if(strlen($photo_path) > 0){
								
								$delete_path = self::get_path($server_path,$photo_path);								
								deleteAwsFile($delete_path);
								if(file_exists($delete_path)){
								
									@unlink($delete_path);
								}
							}												
						}						
					}else{
						
						$add_array = $upload_file_info;
						$add_array['user_id'] = $user_id;
						$add_array['is_delete'] = 0;
						$add_array['add_time'] = $this->current_datetime;
						$add_array['update_time'] = $this->current_datetime;
						$result = $this->info_add($add_array);
						$photo_id = $result === false ? 0 : intval($result);
						$check = $photo_id > 0 ? true : false;						
					}
					
					if(!$check_exists && $photo_id > 0){
							
						$add_array = array();
						$add_array['photo_id'] = $photo_id;
						$add_array['album_id'] = $album_id;
						$result = $album_photo_object->info_add($add_array);
						$check = $result === false ? false : true;					
					}
					
					if($check){
						
						$update_array = array();
						$str_where = ' user_id = '.$user_id.' and is_delete = 0 and album_id = '.$album_id;
						$total_count = $album_photo_view_object->get_list_count('',$str_where,'','');
						$unfinished_str_where = $str_where.' and status = 0 ';
						$unfinished_total_count = $album_photo_view_object->get_list_count('',$unfinished_str_where,'','');
						$update_array['total_count'] = $total_count;
						$update_array['unfinished_total_count'] = $unfinished_total_count;
						$update_array['update_time'] = $this->current_datetime;
						if(isset($upload_file_info['small_photo_path']) || ($this->upload_file_is_img && $upload_file_info['photo_path'] != '')){
							
							$update_array['default_photo_id'] = $photo_id;
							$update_array['default_photo_path'] = isset($upload_file_info['small_photo_path']) ? $upload_file_info['small_photo_path'] : $upload_file_info['photo_path'];
							$info_array['default_photo_path'] = $update_array['default_photo_path'];
						}	
						$str_where = ' user_id = '.$user_id.' and id = '.$album_id;
						$check = $user_album_object->info_update($str_where,$update_array);
						if($check){
							
							$info_array['unfinished_total_count'] = $unfinished_total_count;
							$info_array['total_count'] = $total_count;
						}
					}
					
					if($check && $photo_id > 0){

						$field_str = 'id as photo_id,user_id,photo_name,photo_path,small_photo_path,photo_size,photo_width,photo_height,status,photo_key,photo_type';
						$photo_info = $this->get_info($photo_id,$field_str);
						$list_array['album_info'] = $info_array;
						$list_array['album_info']['default_photo_path'] = self::get_url($server_path,$info_array['default_photo_path'],true);
						$list_array['photo_info'] = $photo_info;
						$list_array['photo_info']['upload_size'] = $this->upload_file_total_size;
						$list_array['photo_info']['photo_path'] = self::get_url($server_path,$photo_info['photo_path'],false);
						$list_array['photo_info']['small_photo_path'] = self::get_url($server_path,$photo_info['small_photo_path'],false);
					}
				}
				
				if(!$check && (count($this->error_array) == 0 || count($this->error_code_array) == 0)){
					
					$this->error_array['result'] = '操作失败';
					$this->error_code_array['result'] = ERROR_OPERATE_FAILED;
				}				
			}
		}else{
			
			$this->error_array['result'] = '非法数据';
			$this->error_code_array['result'] = INVALID_DATA;
		}
		
		return $list_array;
	}
	
	/**
	 * 去掉相册目录后的路径
	 * @param \Home\Model\UserAlbumModel $user_album_object
	 * @param unknown $file_path
	 * @param string $server_path
	 * @return string
	 */
	public function save_path(\Home\Model\UserAlbumModel $user_album_object,$file_path,$server_path = ''){
		
		$save_path = '';
		$file_path = trim($file_path);
		
		if(strlen($file_path) > 0){
			
			$server_path = trim($server_path);
			if(strlen($server_path) <= 0){
				
				$server_path = $user_album_object->get_upload_base_dir();
			}
			$server_path = realpath($server_path);
			$server_path .= DIRECTORY_SEPARATOR;			
			$file_path_tmp = realpath($file_path);
			
			if(strpos($file_path_tmp,$server_path) !== false){
				
				$save_path = substr($file_path_tmp,strlen($server_path));
			}else{
				
				$save_path = $file_path;
			}
			
		}
		
		return $save_path;
	}
	
	 /**
	  * 相册下相片路径
	  * @param unknown $server_path
	  * @param unknown $photo_path
	  * @return string
	  */
	public static function get_path($server_path,$photo_path){
		
		$path = '';
		$server_path = trim($server_path);
		$photo_path = trim($photo_path);
		$file_path = $server_path.$photo_path;
		if(strlen($file_path) > 3){
				
			$path = $file_path;
		}
		return $path;
	}
	
	/**
	 * 获取链接地址
	 * @param unknown $server_path
	 * @param unknown $photo_path
	 * @param string $check_default
	 * @return string
	 */
	public static function get_url($server_path,$photo_path,$check_default = true){

		$url = '';
		$server_path = trim($server_path);
		$photo_path = trim($photo_path);
		$file_path = $server_path.$photo_path;
		if(strlen($file_path) < 3 || $file_path === $server_path){
			
			$check_default = (bool) $check_default;
			if($check_default){
				
				$file_path = self::$default_photo;
			}else{
				
				return $url;
			}			
		}
		$url = getFullUrl($file_path);
		return $url;
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
	 * 上传文件处理
	 * @param unknown $file_dir
	 * @param unknown $upload_name
	 * @param unknown $upload_type  上传类型 ;0:开始上传,1:为续传,2:结束上传
	 * @param unknown $update_style 上传方式，0为直接一次上传文件，1为断点续传方式上传文件
	 * @param unknown $small_server_path 小图片路径
	 * @param unknown $tmp_path
	 * @param unknown $width
	 * @param unknown $height
	 * @param unknown $file_name
	 * @param unknown $file_size
	 * 
	 */
	protected function upload_file_process($file_dir,$upload_name,$upload_type,$update_style,$small_server_path,$tmp_path,$width,$height,$file_name,$file_size){
	
		$check = false;
		$check_valid = $this->upload_file_common_process($upload_name, $file_dir);
		if(!$check_valid){
			
			return $check_valid;
		}	
		
		$check_upload = false;
		$photo_type_check = true;
		$user_file = $_FILES[$upload_name];
		$file_format = $filename = $file_path = '';
			
		$tmp_path = trim($tmp_path);
		$upload_type = intval($upload_type);
		$update_style = intval($update_style);
		$update_style = $update_style === 1 ? 1 : 0;	
		$ignore_type_array = $this->ignore_type_array;
		$check_file_format = $update_style === 0 ? true : ($upload_type === 2 ? true : false);
		if($update_style === 1){
			//断点续传
			if($upload_type === 0){
				
				if(file_exists($tmp_path)){
						
					@unlink($tmp_path);							
				}
				$tmp_path = $file_dir.date('YmdHis',$this->current_time).strval(rand(111111,999999)).'.part';
			}elseif(!file_exists($tmp_path)){
				
				$this->error_array['result'] = '文件不存在';
				$this->error_code_array['result'] = ERROR_FILE_NOT_EXISTS;
				return false;
			}
							
			$file_name = trim($file_name);
			if(strlen($file_name) <= 0){
				
				$this->error_array['result'] = '文件名不合法';
				$this->error_code_array['result'] = ERROR_FILE_NAME_INVALID;
				return false;
			}
			$file_name_array = explode('/',$file_name);
			$file_name_len = count($file_name_array);
			if($file_name_len > 1){
					
				$file_name = $file_name_array[$file_name_len - 1];
			}
			$file_name_array = explode('.',$file_name);							
			if(!check_array_valid($file_name_array)){
				
				$this->error_array['result'] = '文件名不合法';
				$this->error_code_array['result'] = ERROR_FILE_NAME_INVALID;
				return false;
			}
			$file_name_len = count($file_name_array);
			$file_format = $file_name_array[$file_name_len -1];
			$filename = $file_name_array[$file_name_len -2];
			if(strlen($filename) <= 0 || strlen($file_format) <= 0 || !preg_match('/^[\x{4e00}-\x{9fa5}a-zA-Z0-9]+[\x{4e00}-\x{9fa5}a-zA-Z0-9_-]*$/u', $filename)){
				
				$this->error_array['result'] = '文件名不合法';
				$this->error_code_array['result'] = ERROR_FILE_NAME_INVALID;
				return false;
			}
			
			$allow_file_types_array = explode('|',strtolower($this->allow_file_types));
			if(!in_array($file_format,$allow_file_types_array)){
			
				$this->error_array['result'] = '文件类型不合法';
				$this->error_code_array['result'] = ERROR_FILE_TYPE;
				return false;
			}
									
			$tmp_file_path = $file_dir.date('YmdHis',$this->current_time + 2).strval(rand(111111,999999)).'.part';
			if(!move_uploaded_file($user_file['tmp_name'],$tmp_file_path)){
					
				$check_upload = false;
				$this->error_array['result'] = '文件创建失败';
				$this->error_code_array['result'] = ERROR_FILE_MOVE_FAILED;
				return false;
			}
			if(!is_readable($tmp_file_path)){
			
				$check_upload = false;
				$this->error_array['result'] = '文件不可读';
				$this->error_code_array['result'] = ERROR_FILE_NOT_READABLE;
				return false;
			}
			
			$file_str = '';
			$fh_write = @fopen($tmp_path,'ab');
			if(!file_exists($tmp_path) && !is_writeable($tmp_path)){
					
				$check_upload = false;
				$this->error_array['result'] = '文件不可写';
				$this->error_code_array['result'] = ERROR_FILE_NOT_WRITEABLE;
				@fclose($fh_write);
				return false;
			}
			$current_file_size = filesize($tmp_path);
			$extend_file_size = 0;
			if($current_file_size < $file_size || $file_size < 0){
				
				$check_upload = false;
				$this->error_array['result'] = '文件加载失败！';
				$this->error_code_array['result'] = ERROR_FILE_LOAD_FAILED;
				@fclose($fh_write);
				return false;
			}else{
				
				$extend_file_size = $current_file_size - $file_size;							
			}
			$fh_read = @fopen($tmp_file_path,'rb');
			while(!@feof($fh_read)){
			
				$file_str = @fgets($fh_read);
				$file_str = strval($file_str);
				$file_str_len = strlen($file_str);
				if($extend_file_size >= $file_str_len){
					
					$extend_file_size -= $file_str_len;
					continue;
				}elseif($extend_file_size != 0){
					
					$file_str_len -= $extend_file_size;
					$file_str = substr($file_str,$extend_file_size,$file_str_len);						
				}
				@flock($fh_write, LOCK_EX) ;
				@fwrite($fh_write,$file_str,$file_str_len);
				@flock($fh_write, LOCK_UN);
			}
				
			@fclose($fh_read);
			@fclose($fh_write);
			@unlink($tmp_file_path);
			
			if($upload_type === 2){
				
				$file_path = $file_dir.date('YmdHis',$this->current_time).strval(rand(111111,999999)).'.'.$file_format;
				if(!rename($tmp_path,$file_path)){
				
					$this->error_array['result'] = '修改文件名失败';
					$this->error_code_array['result'] = ERROR_FILE_RENAME_FAILED;
					return false;
				}
				if(!isset($ignore_type_array[$file_format])){
					
					$photo_type_check = false;
					$file_format = self::check_file_type($file_path,$file_path, $this->allow_file_types);
					if(empty($file_format)){
						
						$this->error_array['result'] = '文件类型不合法';
						$this->error_code_array['result'] = ERROR_FILE_TYPE;
						@unlink($file_path);
						return false;
					}
				}
				
			}else{
				
				$this->upload_file_total_size = filesize($tmp_path);
				$upload_file_info = array();
				$upload_file_info['photo_name'] = $filename;
				$upload_file_info['photo_tmp_path'] = $tmp_path;
				$upload_file_info['photo_path'] = $file_path;
				$upload_file_info['small_photo_path'] = '';
				$upload_file_info['photo_size'] = self::file_size_str($this->upload_file_total_size);
				$upload_file_info['photo_width'] = 0;
				$upload_file_info['photo_height'] = 0;
				$upload_file_info['photo_key'] = md5_file($tmp_path);
				$upload_file_info['photo_type'] = $user_file['type'];
				$this->upload_file_info = $upload_file_info;
				
				return true;							
			}
									
		}
		if($check_file_format){		
			
			if(file_exists($tmp_path)){
				
				@unlink($tmp_path);
			}		
			
			if($update_style === 0){
				
				$file_name_array = explode('.',strval($user_file['name']));
				$file_format = end($file_name_array);							
				if(!isset($ignore_type_array[$file_format])){			
					
					$photo_type_check = false;
					$file_format = self::check_file_type($user_file['tmp_name'], $user_file['name'], $this->allow_file_types);
				}	
				if(strlen($file_format) > 0){
					
					$name_array = explode('.',$user_file['name']);
					unset($name_array[count($name_array) - 1]);
					$filename = implode('.',$name_array);									
					$file_path = $file_dir.date('YmdHis',$this->current_time).strval(rand(111111,999999)).'.'.$file_format;
					if(!move_uploaded_file($user_file['tmp_name'],$file_path)){
							
						$this->error_array['result'] = '文件创建失败';
						$this->error_code_array['result'] = ERROR_FILE_MOVE_FAILED;
						return false;
					}								
				}
			}
			
			if(strlen($file_format) > 0 && $photo_type_check){
					
				if(function_exists('mime_content_type')){
						
					$photo_type = mime_content_type($file_path);
				}else{
			
					$finfo = finfo_open(FILEINFO_MIME);
					$photo_type =  finfo_file($finfo, $file_path);
					finfo_close($finfo);
				}
				$tmp_ignore_type_array = $ignore_type_array[$file_format];							
				if(check_array_valid($tmp_ignore_type_array)){
					
					if(!in_array($photo_type,$tmp_ignore_type_array)){
						
						$file_format = '';
						@unlink($file_path);
					}
					
				}elseif($photo_type != $tmp_ignore_type_array){
					
					$file_format = '';
					@unlink($file_path);
				}						
				
			}
			
			if(strlen($file_format) > 0){						
										
				$width = $height = 0;
				$small_photo_path = '';
				$photo_array = array('gif','jpg','png','jpeg','bmp','swf');
				if(in_array($file_format,array('gif','jpg','png','jpeg','bmp'))){
					
					$this->upload_file_is_img = true;
					$small_photo_path = $this->small_photo_generation($small_server_path,$file_path);
				}else{
					
					$small_upload_name = 'small_'.$upload_name;
					$small_photo_path = $this->video_small_photo_generation($small_upload_name,$small_server_path);
					if(!empty($small_photo_path)){
						
						$this->upload_file_is_img = true;
					}elseif(count($this->error_array) > 0 || count($this->error_code_array) > 0){
						
						return false;
					}
				}
				if(in_array($file_format,$photo_array)){												
				
					$image_info_array = getimagesize($file_path);								
					if(check_array_valid($image_info_array)){
					
						$width = $image_info_array[0];
						$height = $image_info_array[1];
						$photo_type = $image_info_array['mime'];
					}
				}else{
													
					$width = $width > 0 ? $width : 1280;
					$height = $height > 0 ? $height : 720;
				}					
				
				$upload_file_info = array();
				$upload_file_info['photo_name'] = $filename;
				$upload_file_info['photo_tmp_path'] = '';
				$upload_file_info['photo_path'] = $file_path;
				$upload_file_info['small_photo_path'] = $small_photo_path;
				$upload_file_info['photo_size'] = self::file_size_str(filesize($file_path));
				$upload_file_info['photo_width'] = $width;
				$upload_file_info['photo_height'] = $height;
				$upload_file_info['photo_key'] = md5_file($file_path);
				$upload_file_info['photo_type'] = $photo_type;			
					
				$check = C('IS_AWS_URL') ? $this->aws_upload_process($file_path) : true;						
				if($check){					
													
					$this->upload_file_info = $upload_file_info;
				}
			   
				$check_upload = $check;
			}else{
			
				$this->error_array['result'] = '文件类型不合法';
				$this->error_code_array['result'] = ERROR_FILE_TYPE;
			}
		}
		$check = $check_upload;
		return $check;
	
	}
	
	/**
	 * 视频小图上传
	 * @param unknown $upload_name
	 * @param unknown $small_server_path
	 * @param number $max_width
	 * @param number $max_height
	 * @return string
	 */
	protected function video_small_photo_generation($upload_name,$small_server_path,$max_width = 300,$max_height = 300){
		
		$small_path = '';
		$check = $this->upload_file_common_process($upload_name,$small_server_path);
		if(!$check){
			
			return $small_path;
		}
		$user_file = $_FILES[$upload_name];
		$file_format = self::check_file_type($user_file['tmp_name'], $user_file['name'], $this->allow_file_types);
		if(!empty($file_format)){
			
			$file_path = $small_server_path.date('YmdHis',$this->current_time).strval(rand(111111,999999)).'.'.$file_format;
			if(!move_uploaded_file($user_file['tmp_name'],$file_path)){
			 		
			 	$this->error_array['result'] = '文件创建失败';
			 	$this->error_code_array['result'] = ERROR_FILE_MOVE_FAILED;
			 	return $small_path;
			}
			$small_path = $this->small_photo_generation($small_server_path,$file_path,$max_width,$max_height);			
		}else{
			
			$this->error_array['result'] = '文件类型不合法';
			$this->error_code_array['result'] = ERROR_FILE_TYPE;			
		}
		
		return $small_path;
	}
	
	/**
	 * 文件上传公共判别部分
	 * @param unknown $upload_name
	 * @param unknown $file_dir
	 * @return boolean
	 */
	protected function upload_file_common_process($upload_name,$file_dir){
		
		$check = false;
		if(count($this->error_code_array) > 0 || count($this->error_array) > 0 || !is_dir($file_dir) || strlen($upload_name) <= 0 ){
		
			return $check;
		}elseif(!isset($_FILES[$upload_name])){
		
			$this->error_array['result'] = '没有文件被上传';
			$this->error_code_array['result'] = ERROR_NOT_UPLOADED_FILE;
		
			return $check;
		}	
		$user_file = $_FILES[$upload_name];
		if($user_file['error'] > 0){
		
			switch($user_file['error']){
		
				case 1:
		
					$this->error_array['result'] = '文件大小超过服务器限制';
					$this->error_code_array['result'] = ERROR_SERVER_LIMIT_SIZE;
					break;
				case 2:
		
					$this->error_array['result'] = '上传文件太大或者上传文件的大小超过了 HTML 表单中 MAX_FILE_SIZE 选项指定的值';
					$this->error_code_array['result'] = ERROR_FILE_MAX_SIZE;
					break;
				case 3:
		
					$this->error_array['result'] = '文件只加载了一部分！';
					$this->error_code_array['result'] = ERROR_FILE_LOAD_PART;
					break;
				case 4:
		
					$this->error_array['result'] = '文件加载失败！';
					$this->error_code_array['result'] = ERROR_FILE_LOAD_FAILED;
					break;
				case 5:
		
					$this->error_array['result'] = '未知错误';
					$this->error_code_array['result'] = ERROR_UNKNOWN;
					break;
				case 6:
		
					$this->error_array['result'] = '找不到临时文件夹';
					$this->error_code_array['result'] = ERROR_FINE_NOT_TMP_FILE;
					break;
				case 7:
		
					$this->error_array['result'] = '文件写入失败';
					$this->error_code_array['result'] = ERROR_CANT_WRITE;
					break;
			}
		}else{
		
			if(is_uploaded_file($user_file['tmp_name'])){
		
				if($user_file['size'] > $this->max_size){
		
					$this->error_array['result'] = '文件大小超过服务器限制';
					$this->error_code_array['result'] = ERROR_SERVER_LIMIT_SIZE;
				}else{
					
					$check = true;
				}
			}else{
	
				$this->error_array['result'] = '非上传文件';
				$this->error_code_array['result'] = ERROR_FILE_NOT_UPLOADED;
			}
		}
		
		return $check;
		
	}
	
	/**
	 * 生成小图
	 * @param unknown $small_server_path
	 * @param unknown $file_path
	 * @param number $max_width
	 * @param number $max_height
	 * @return string
	 */
	public function small_photo_generation($small_server_path,$file_path,$max_width = 300,$max_height = 300){
		
		$small_photo_path = '';
		$small_server_path = trim($small_server_path);
		if(is_dir($small_server_path) && file_exists($file_path)){
			
			$file_path = realpath($file_path);
			$small_server_path = realpath($small_server_path);
			$file_path_array = explode(DIRECTORY_SEPARATOR,$file_path);
			$small_server_path_array = explode(DIRECTORY_SEPARATOR,$small_server_path);		
			$file_name = end($file_path_array);
			$thumbname = $small_server_path.'/'.$file_name;			
			$max_width = intval($max_width);
			$max_height = intval($max_height);
			$max_width = $max_width > 0 ? $max_width : 300;
			$max_height = $max_height > 0 ? $max_height : 300;
			$result = Image::thumb($file_path, $thumbname, '',$max_width,$max_height,true);
			$photo_path = $result === false ? '' : $result;
			if(!empty($photo_path)){
				
				if(C('IS_AWS_URL')){
					
					$check = $this->aws_upload_process($photo_path);
					if(!$check){
							
						return $small_photo_path;
					}
				}
				
				$small_photo_path = end($small_server_path_array).'/'.$file_name;
			}			
		}
		return $small_photo_path;
	}
	
	/**
	 * 亚马逊同步
	 * @param unknown $file_path
	 */
	public function aws_upload_process($file_path){
		
		$check = false;
		$acl = 'public-read';
		$aws_vendor_object = $this->aws_vendor_object;
		if($aws_vendor_object == null || !is_object($aws_vendor_object)){
			
			$aws_vendor_object = new \AwsVendor\AwsVendor(C('AWS_BUCKET'),C('AWS_BASE_PATH'));
		}
		if(file_exists($file_path)){

			$file_path = realpath($file_path);
			$path_array = explode(DIRECTORY_SEPARATOR,$file_path);
			$file_name = end($path_array);			
			$prefix_path = substr($file_path,0,-strlen($file_name));
			$aws_base_path = $aws_vendor_object->get_name('aws_base_path');
			
			$prefix_path_array = explode($aws_base_path,$prefix_path);
			$prefix_path = end($prefix_path_array);
			
			$pathToFile = $aws_vendor_object->get_aws_file_path($prefix_path,$file_name);
			$check = $aws_vendor_object->upload_process($file_path,$pathToFile,$acl);
			if(!$check){
				
				$error_array = $aws_vendor_object->get_error_array();
				$this->error_array['result'] = isset($error_array['result']) ? $error_array['result'] : '同步到亚马逊失败';
				$this->error_code_array['result'] = ERROR_SYNC_FAILED;
			}
		}else{
			
			$this->error_array['result'] = '文件不存在';
			$this->error_code_array['result'] = ERROR_FILE_NOT_EXISTS;
		}
		
		return $check;
	}
	
	/**
	 * 检查文件类型
	 *
	 * @access      public
	 * @param       string      filename            文件名
	 * @param       string      realname            真实文件名
	 * @param       string      limit_ext_types     允许的文件类型
	 * @return      string
	 */
	public static function check_file_type($filename, $realname = '', $limit_ext_types = ''){
	
		if($realname){
	
			$extname = strtolower(substr($realname, strrpos($realname, '.') + 1));
	
		}else{
	
			$extname = strtolower(substr($filename, strrpos($filename, '.') + 1));
		}
	
		if($limit_ext_types && stristr($limit_ext_types, '|' . $extname . '|') === false){
	
			return '';
		}
	
		$str = $format = '';
	
		$file = @fopen($filename, 'rb');
		if($file){
	
			$str = @fread($file, 0x400); // 读取前 1024 个字节
			@fclose($file);
	
		}else{
				
			if($extname == 'jpg' || $extname == 'jpeg' || $extname == 'gif' || $extname == 'png' || $extname == 'doc' ||
			   $extname == 'xls' || $extname == 'txt'  || $extname == 'zip' || $extname == 'rar' || $extname == 'ppt' ||
			   $extname == 'pdf' || $extname == 'rm'   || $extname == 'mid' || $extname == 'wav' || $extname == 'bmp' ||
			   $extname == 'swf' || $extname == 'chm'  || $extname == 'sql' || $extname == 'cert'|| $extname == 'pptx' ||
			   $extname == 'xlsx' || $extname == 'docx' || $extname == 'avi' || $extname == '3gp' || $extname == 'mp4' ||
			   $extname == 'flv' || $extname == 'wmv' || $extname == 'asf' || $extname == 'asx' || $extname == 'rmvb' || 
			   $extname == 'mpg' || $extname == 'mpeg' || $extname == 'mov' || $extname == 'm4v' || $extname == 'dat' ||
			   $extname == 'mkv' || $extname == 'vob' || $extname == 'f4v'){
	
			   $format = $extname;
			}
		}
	
		if($format == '' && strlen($str) >= 2 ){
	
			if(substr($str, 0, 4) == 'MThd' && $extname != 'txt'){
					
				$format = 'mid';
			}elseif (substr($str, 0, 4) == 'RIFF' && $extname == 'wav'){
					
				$format = 'wav';
			}elseif (substr($str ,0, 3) == "\xFF\xD8\xFF"){
					
				$format = 'jpg';
			}elseif(substr($str ,0, 4) == 'GIF8' && $extname != 'txt'){
					
				$format = 'gif';
			}elseif (substr($str ,0, 8) == "\x89\x50\x4E\x47\x0D\x0A\x1A\x0A"){
					
				$format = 'png';
			}elseif (substr($str ,0, 2) == 'BM' && $extname != 'txt'){
					
				$format = 'bmp';
			}elseif ((substr($str ,0, 3) == 'CWS' || substr($str ,0, 3) == 'FWS') && $extname != 'txt'){
					
				$format = 'swf';
			}elseif (substr($str ,0, 4) == "\xD0\xCF\x11\xE0"){
					
				// D0CF11E == DOCFILE == Microsoft Office Document
				if(substr($str,0x200,4) == "\xEC\xA5\xC1\x00" || $extname == 'doc'){
	
					$format = 'doc';
				}elseif(substr($str,0x200,2) == "\x09\x08" || $extname == 'xls'){
	
					$format = 'xls';
				}elseif(substr($str,0x200,4) == "\xFD\xFF\xFF\xFF" || $extname == 'ppt'){
	
					$format = 'ppt';
				}
			}elseif(substr($str ,0, 4) == "PK\x03\x04"){
					
				if(substr($str,0x200,4) == "\xEC\xA5\xC1\x00" || $extname == 'docx'){
	
					$format = 'docx';
				}elseif(substr($str,0x200,2) == "\x09\x08" || $extname == 'xlsx'){
	
					$format = 'xlsx';
				}elseif(substr($str,0x200,4) == "\xFD\xFF\xFF\xFF" || $extname == 'pptx'){
	
					$format = 'pptx';
				}else{
	
					$format = 'zip';
				}
			}elseif(substr($str ,0, 4) == 'Rar!' && $extname != 'txt'){
					
				$format = 'rar';
			}elseif(substr($str ,0, 4) == "\x25PDF"){
					
				$format = 'pdf';
			}elseif(substr($str ,0, 3) == "\x30\x82\x0A"){
					
				$format = 'cert';
			}elseif(substr($str ,0, 4) == 'ITSF' && $extname != 'txt'){
					
				$format = 'chm';
			}elseif (substr($str ,0, 4) == "\x2ERMF"){
					
				$format = 'rm';
			}elseif ($extname == 'sql'){
					
				$format = 'sql';
			}elseif ($extname == 'txt'){
					
				$format = 'txt';
			}elseif($limit_ext_types){
				
				if(stristr($limit_ext_types, '|' . substr($str, 0, 4) . '|') !== false){
					
					$format = substr($str, 0, 4);
				}elseif(stristr($limit_ext_types, '|' . substr($str, 0, 3) . '|') !== false){
					
					$format = substr($str, 0,3);
				}elseif(stristr($limit_ext_types, '|' . substr($str, 0, 2) . '|') !== false){
					
					$format = substr($str, 0,2);
				}				
			}
		}
	
		if($limit_ext_types && stristr($limit_ext_types, '|' . $format . '|') === false){
				
			$format = '';
		}
	
		return $format;
	}
	
	/**
	 * 格式化图片
	 * @param unknown $file_size
	 * @return string
	 */
	public static function file_size_str($file_size){
				
		$size_str = '0 Bit';		
		$size_array = array();
		$size_array = self::$size_array;
		
		if(!check_array_valid($size_array)){
			
			$size_array['Bit'] = 1;
			$min_size = 1024;
			$size_array['Kb'] = $min_size;
			$min_size *= 1024;
			$size_array['Mb'] = $min_size;
			$min_size *= 1024;
			$size_array['Gb'] = $min_size;
			$min_size *= 1024;			
			$size_array['Tb'] = $min_size;
			arsort($size_array);
			self::$size_array = $size_array;
		}
		
		$file_size = floatval($file_size);
		foreach($size_array as $key=>$value){
			
			if($file_size >= $value){
				
				$size_str = number_format($file_size/$value,2).' '.$key;
				break;
			}
		}				
		return $size_str;		
	}
	
	/**
	 * 轻量文件上传，采用md5校正
	 * @param unknown $user_id
	 * @param unknown $album_id
	 * @param unknown $md5_file
	 * @return multitype:
	 */
	public function client_md5_file_upload($user_id,$album_id,$client_path,$md5_file){
	
		$check = false;
		$list_array = array();
		$user_id = intval($user_id);
		$album_id = intval($album_id);
		$this->error_array = array();
		$this->error_code_array = array();
		$client_path = strip_tags($client_path);
		$client_path = trim($client_path);
		$md5_file = trim($md5_file);
		
		if($user_id > 0 && $album_id > 0 && strlen($client_path) > 0 && is_Md5($md5_file)){
			
			$error_status = true;
			$small_server_path = $server_path = '';
			$user_album_object = new \Home\Model\UserAlbumModel();
			$field_str = 'id as album_id,user_id,album_name,client_path,server_path,default_photo_id,default_photo_path,album_note,total_count,unfinished_total_count';
			$info_array = $user_album_object->get_info($album_id,$field_str);
			
			if(!check_array_valid($info_array) || $user_id != $info_array['user_id'] || $client_path != stripslashes(trim($info_array['client_path']))){
					
				$this->error_array['result'] = '非法数据';
				$this->error_code_array['result'] = INVALID_DATA;
				return $check;
			}
			$source_server_path = trim($info_array['server_path']);						
			$server_path = $user_album_object->mkdir_server_path($user_id,$error_status);
			$small_server_path = $user_album_object->mkdir_small_server_path($user_id,$error_status);
			if(count($this->error_code_array) > 0 || count($this->error_array) > 0){
			
				return $check;
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
			$small_server_path = $user_album_object->mkdir_small_server_path($user_id,$error_status);
			if(count($this->error_code_array) > 0 || count($this->error_array) > 0){
			
				return $check;
			}
			
			unset($info_array['server_path']);
			$str_where = 'user_id = '.$user_id.' and is_delete = 0 and status = 1 and photo_key = "'.addslashes($md5_file).'" ';
			$result = $this->field('id,user_id,photo_name,photo_tmp_path,photo_path,small_photo_path,photo_size,photo_width,photo_height,is_delete,status,photo_key,photo_type')->where($str_where)->find();
			if(check_array_valid($result)){
				
				$check = true;
				$photo_info = $result;
				$source_photo_path = $photo_info['photo_path'];
				$source_file_path = $source_server_path.$source_photo_path;
				if(!file_exists($source_file_path)){
				
					$this->error_array['result'] = '文件不存在';
					$this->error_code_array['result'] = ERROR_FILE_NOT_EXISTS;
					return array();
				}
				
				$photo_id = $photo_info['id'];									
				$album_photo_object = new \Home\Model\AlbumPhotoModel();
				$album_photo_view_object = new \Home\Model\AlbumPhotoViewModel();
				$str_where = 'album_id = '.$album_id.' and '.$str_where;
				$field_str = 'photo_id as id,user_id,photo_name,photo_tmp_path,photo_path,small_photo_path,photo_size,photo_width,photo_height,is_delete,status,photo_key,photo_type';
				$result = $album_photo_view_object->get_info($str_where,$field_str);				
				$check_exists = check_array_valid($result) ? true : false;
				if(!$check_exists){
						
					unset($photo_info['id']);
					$file_format = '';
					$source_photo_path_array = explode('.',$source_photo_path);
					if(is_array($source_photo_path_array)){
					
						$file_format = '.';
						$file_format .= end($source_photo_path_array);
					}				
					$photo_path = date('YmdHis',$this->current_time).strval(rand(111111,999999)).$file_format;
					$file_path = $server_path.$photo_path;
					if(copy($source_file_path,$file_path)){
						
						if(C('IS_AWS_URL')){
							
							$check = $this->aws_upload_process($file_path);
							if(!$check){
								
								return array();
							}
						}
					}else{
						
						$this->error_array['result'] = '文件复制失败';
						$this->error_code_array['result'] = ERROR_FILE_COPY_FAILED;
						return array();
					}
					$photo_info['photo_path'] = $photo_path;
					$photo_info['small_photo_path'] = $this->small_photo_generation($small_server_path,$file_path);
					$photo_info['add_time'] = $this->current_datetime;
					$photo_info['update_time'] = $this->current_datetime;					
					$add_array = array();
					$add_array = $photo_info;
					$result = $this->info_add($add_array);
					$photo_id = $result === false ? 0 : intval($result);
					if($photo_id > 0){
						
						$add_array = array();
						$add_array['photo_id'] = $photo_id;
						$add_array['album_id'] = $album_id;
						$result = $album_photo_object->info_add($add_array);
						$check = $result === false ? false : true;						
					}else{
						
						$check = false;
					}					
				}else{
					
					$photo_info = $result;
					unset($photo_info['id']);
				}
				
				if($check){					
					
					$update_array = array();
					
					$str_where = ' user_id = '.$user_id.' and is_delete = 0 and album_id = '.$album_id;
					$total_count = $album_photo_view_object->get_list_count('',$str_where,'','');
					$unfinished_str_where = $str_where.' and status = 0 ';
					$unfinished_total_count = $album_photo_view_object->get_list_count('',$unfinished_str_where,'','');
					$update_array['total_count'] = $total_count;
					$update_array['unfinished_total_count'] = $unfinished_total_count;
					$update_array['update_time'] = $this->current_datetime;
					if(!empty($photo_info['small_photo_path'])){
						
						$update_array['default_photo_id'] = $photo_id;
						$update_array['default_photo_path'] = $photo_info['small_photo_path'];
						
						$info_array['default_photo_id'] = $photo_id;
						$info_array['default_photo_path'] = $photo_info['small_photo_path'];
					}	
					$str_where = ' user_id = '.$user_id.' and id = '.$album_id;
					$check = $user_album_object->info_update($str_where,$update_array);
					if($check){
						
						$info_array['total_count'] = $total_count;
						$info_array['unfinished_total_count'] = $unfinished_total_count;
					}
				}
				
				if($check){
	
					$field_str = 'id as photo_id,user_id,photo_name,photo_path,small_photo_path,photo_size,photo_width,photo_height,status,photo_key,photo_type';
					$photo_info = $this->get_info($photo_id,$field_str);
					$list_array['album_info'] = $info_array;
					$list_array['album_info']['default_photo_path'] = self::get_url($server_path,$info_array['default_photo_path'],true);
					$list_array['photo_info'] = $photo_info;
					$list_array['photo_info']['upload_size'] = $this->upload_file_total_size;
					$list_array['photo_info']['photo_path'] = self::get_url($server_path,$photo_info['photo_path'],false);
					$list_array['photo_info']['small_photo_path'] = self::get_url($server_path,$photo_info['small_photo_path'],false);
				}
				
				if(!$check && (count($this->error_array) == 0 || count($this->error_code_array) == 0)){
						
					$this->error_array['result'] = '操作失败';
					$this->error_code_array['result'] = ERROR_OPERATE_FAILED;
				}
							
			}else{
				
				$this->error_array['result'] = '文件不存在';
				$this->error_code_array['result'] = ERROR_FILE_NOT_EXISTS;
			}
						
		}else{
			
			$this->error_array['result'] = '非法数据';
			$this->error_code_array['result'] = INVALID_DATA;
		}
		
		return $list_array;
	}
}
?>