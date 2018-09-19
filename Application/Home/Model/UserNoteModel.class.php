<?php

namespace Home\Model;
use Think\Model;

class UserNoteModel  extends \Home\Model\DefaultModel {
	
	protected $current_time;
	protected $current_datetime;
	protected $current_date;
	protected $upload_base_dir = '';
	protected $upload_base_url = '';
	protected static $user_note_base_url = '';
	protected $path = '';
	protected $tmp_path = '';
	protected $mode = '0755';
	protected $max_size = 20000000;//最大限制
	protected $error_array = array();//错误信息
	protected $error_code_array = array();//错误编码
	protected $allow_file_types = '|GIF|JPG|PNG|BMP|SWF|DOC|XLS|PPT|MID|WAV|ZIP|RAR|PDF|CHM|RM|TXT|';
	
	/**
	 * 初始化
	 */
	public function __construct($name='',$tablePrefix='',$connection='') {
		
		$this->dbName = 'db_note';
		$this->tableName = C('DB_PREFIX').'user_note';
		$this->trueTableName = $this->tableName;
		$this->pk = 'id';
		parent::__construct($name,$tablePrefix,$connection);
		$this->current_time = time();
		$this->current_datetime = date('Y-m-d H:i:s',$this->current_time);
		$this->current_date = date('Y-m-d',$this->current_time);
		$this->upload_base_url = self::$user_note_base_url;
	}
	
	/**
	 * 获取信息
	 */
	public function get_user_noteinfo($id,$user_id){
		
		$field_str = '';
		$extend_where = ' and user_id = '.$user_id;
		$result = $this->get_info($id,$field_str,$extend_where);
		if(check_array_valid($result)){
										
			$info_array = $result;			
			$info_array['note_content'] = self::content_display_process($result['note_content']);
		}
		return $info_array;
	}	
	
	public static function content_display_process($content){
		
		$content_d = '';
		if(strlen($content) > 0){
			
			$igore_special_array = array('[*&*&]','[&#&#]','[@$@$]','[#*#*]','[&$&$]');			
			$content = strval(base64_decode(urldecode(trim(stripslashes($content)))));
			//$content = str_replace('\n','<br />',str_replace($igore_special_array,"",$content));
			$content = str_replace('\n','<br />',$content);
			$content_d = json_decode($content,true);
		}
		return $content_d;
	}
	
	/**
	 * 判别是否存在
	 */
	public function check_exists($id,$user_id){
		
		$check = false;
		$id = intval($id);
		$user_id = intval($user_id);
		if($id > 0 && $user_id > 0){
			
			$str_where = ' id = '.$id.' and user_id = '.$user_id.' and is_delete = 0 ';
			$total_count = $this->where($str_where)->count();
			$check = $total_count > 0 ? true : false;
			
		}
		return $check;
	}
	
	/**
	 * 释放
	 **/
	public function __destruct(){
	    		
		$this->current_time = 0;
		$this->current_datetime = '';
		$this->current_date = '';
		$this->error_code_array = array();
		$this->error_array = array();
		$this->upload_base_dir = '';
		$this->path = '';
		$this->tmp_path = '';
		$this->max_size = 0;
		$this->mode = '';
		$this->allow_file_types = '';
	}
	
	/**
	 * 获取便签分页数据
	 * @param $user_id 用户id
	 * @param $type_id 类型
	 * @param $start 开始位置
	 * @param $len 获取的个数
	 * @param $check_status 是否限制status为有效值
	 * @return array
	 *
	 */
	public function get_pagelist($user_id,$type_id,$page_no = 1,$page_size = 30,$is_recy = 0){
	
		$return_array = array('total_count'=>0,'list'=>array());
		if($user_id <= 0){
	
			$this->error_array['result'] = '非法数据';
			$this->error_code_array['result'] = INVALID_DATA;
			return $return_array;
		}
		$is_recy = intval($is_recy) == 0 ? 0 : 1;
		$page_no = intval($page_no) > 0 ? $page_no : 1;
		$type_id = intval(type_id) > 0 ? type_id : 1;
		$group_by = '';
		$join_str = '';
		$where['user_id'] = $user_id;
		$where['type_id'] = $type_id;
		$where['is_delete'] = $is_recy;
		$total_count = $this->get_list_count($group_by,$where,$join_str,'');		
		if($total_count > 0){
			
			$return_array['total_count'] = $total_count;
		
			$order_by='';
			$order_way = '';
			$field_str = '';
			$result = $this->get_page_list($page_no,$page_size,$order_by,$order_way,$group_by,$where,$join_str,$field_str);
			if (check_array_valid($result)){
								
				$list = array();				
				foreach($result as $key=>$value){					
					
					$list[$key]['service_id'] = intval($value['id']);
					$list[$key]['user_id'] = intval($value['user_id']);
					$list[$key]['path'] = stripslashes($value['path']);
					$list[$key]['data'] = self::content_display_process($value['note_content']);		
					$list[$key]['update_time'] = $value['update_time'];					
				}
				$return_array['list'] = $list;	
			}
		}
		return $return_array;
	}
	
	/**
	 * 获取便签数据
	 * @param $user_id 用户id
	 * @param $type_id 类型
	 * @param $start 开始位置
	 * @param $len 获取的个数
	 * @param $check_status 是否限制status为有效值
	 * @return array
	 * 
	 */
	public function client_get_synchro($user_id,$type_id,$start = 0,$len = 500,$check_status = false,$is_delete = 0){
		
		$info_array = array('total_count'=>0,'list'=>array());
		$user_id = intval($user_id);
		$this->error_array = array();
		$this->error_code_array = array();
		if($user_id > 0){
			
			$type_id = intval($type_id);
			$type_id = $type_id > 0 ? $type_id : 1;
			$start = intval($start);
			$len = intval($len);
			$start = $start > 0 ? $start : 0;
			$len = $len > 0 ? $len : 500;
			$check_status = (bool) $check_status;
			$str_where = 'user_id = '.$user_id.' and is_delete = '.$is_delete.' and type_id = '.$type_id.' ';
			if($check_status){
				$str_where .= 'and status = 1 ';
			}
			$result = $this->where($str_where)->count();
			
			
			$total_count = intval($result);
			if($total_count > 0){
				
				$info_array['total_count'] = $total_count;
				$result = $this->field('id,user_id,type_id,path,note_content,update_time')->where($str_where)->order('update_time desc')->limit($start,$len)->select();
				if(check_array_valid($result)){
				
					$k = 0;
					$list = array();					
					foreach($result as $key=>$value){
													
						$list[$key]['service_id'] = intval($value['id']);
						$list[$key]['user_id'] = intval($value['user_id']);
						$list[$key]['path'] = stripslashes($value['path']);
						$list[$key]['data'] = self::content_display_process($value['note_content']);
						$list[$key]['update_time'] = $value['update_time'];
						$k++;
					}
					$info_array['list'] = $list;
				}
			}	
		}else{
			
			$this->error_array['result'] = '非法数据';
			$this->error_code_array['result'] = INVALID_DATA;
		}
		return $info_array;
	}
	
	/**
	 * 便签同步处理
	 * 
	 */
	public function client_synchro_process($client_id,$service_id,$user_id,$type_id,$is_force_update,$data,$path = '',$check_file = false){
		
		$check = false;
		$info_array = array();
		$client_id = intval($client_id);
		$this->error_array = array();
		$this->error_code_array = array();
		if($client_id <= 0){
			
			$this->error_array['result'] = '非法数据';
			$this->error_code_array['result'] = INVALID_DATA;
			return $info_array;
		}
		
		$service_id = intval($service_id);
		$user_id = intval($user_id);
		$type_id = intval($type_id);
		$is_force_update = intval($is_force_update);
		$data = check_array_valid($data) ? $data : array();
		$check_exists = $this->check_exists($service_id,$user_id);
		$path = trim($path);
		$check_file = (bool) $check_file;
		if(!$check_exists){
			
			$add_array = array();
			$add_array['user_id'] = $user_id;
			$add_array['type_id'] = $type_id;
			$add_array['is_delete'] = 0;
			$add_array['status'] = strlen($path) > 0 ? 1 : ($check_file ? 0 :1);
			$add_array['path'] = addslashes($path);
			$add_array['note_content'] = addslashes(urlencode(base64_encode(json_encode($data))));
			$add_array['add_time'] = addslashes($this->current_datetime);
			$add_array['update_time'] = $add_array['add_time'];
			
			$result = $this->data($add_array)-> add();
			if($result){
				
				$check = true;
				$service_id = intval($result);
			}else{
				
				$this->error_array['result'] = '同步失败';
				$this->error_code_array['result'] = ERROR_SYNC_FAILED;
			}
			
		}else{
			
			$update_array = array();
			if($is_force_update === 1){
				
				$update_array['type_id'] = $type_id;
				$update_array['status'] = strlen($path) > 0 ? 1 : ($check_file ? 0 :1);
				$update_array['path'] = addslashes($path);
			}
			if(check_array_valid($data) || $is_force_update === 1){
				
				$update_array['note_content'] = addslashes(urlencode(base64_encode(json_encode($data))));
			}
			
			$update_array['update_time'] = addslashes($this->current_datetime);
			$result = $this->where(' id = '.$service_id.' and user_id = '.$user_id.' and is_delete = 0 ')->data($update_array)->save();
		    if($result !== false){
		    	
		    	$check = true;		    	
		    }else{
		    	
		    	$this->error_array['result'] = '同步失败';
				$this->error_code_array['result'] = ERROR_SYNC_FAILED;
		    }
		}
		
		if($check && $service_id > 0){
			
			$result = $this->field('id,type_id,update_time')->where(' id = '.$service_id.' and user_id = '.$user_id.' and is_delete = 0 ')->find();
			if(check_array_valid($result)){			
				
				$info_array['client_id'] = $client_id;
				$info_array['service_id'] = intval($result['id']);
				$info_array['type_id'] = intval($result['type_id']);				
				$info_array['user_id'] = $user_id;				
				$info_array['update_time'] = $result['update_time'];
			}
		}
		
		return $info_array;
	}
	
	/**
	 * 获取用户的目录名称
	 * @param unknown $user_id
	 */
	public static function get_user_dir_name($user_id){
		
		$dir_name = '';
		$user_id = intval($user_id);
		if($user_id > 0){
			
			$dir_name = strval($user_id).'/';
			$md5_id = md5(strval($user_id));
			$prefix = substr(md5($md5_id),3,2);
			$suffix = substr(md5(($md5_id.$prefix)),5,3);
			$dir_name .= $prefix.$suffix.'/';
			
		}
		return $dir_name;
	}
	
	/**
	 * 获取用户的目录路径
	 * @param $user_id
	 * @return string
	 */
	public static function get_user_dir_path($user_id){
		
		$dir_name = '';
		$user_id = intval($user_id);
		if($user_id > 0){
				
			$dir_name = BASE_DIR.'Public/Upload/usernote/'.self::get_user_dir_name($user_id);				
		}
		return $dir_name;
	}
	
	/**
	 * 获取用户的目录的url
	 * @param $user_id
	 * @return string
	 */
	public static function get_user_url($user_id){
		
		$url_path = '';
		$user_id = intval($user_id);
		if($user_id > 0){
			
			$url_path = self::$user_note_base_url.self::get_user_dir_name($user_id);
		}
		return $url_path;
	}

	/**
	 * 便签文件同步处理
	 * @param $client_id 客户端ID
	 * @param $service_id 服务器ID
	 * @param $user_id 用户ID
	 * @param $upload_type 上传类型 ;0:开始上传,1:为续传,2:结束上传,3：单个上传
	 * @param $user_file_string 文件拆分成的字符串
	 * @param $update_style 上传方式，0为文件拆分成字符串上传，1为文件拆分成一片片文件上传
	 * @param $source_file_name 上传文件名
	 * @param $is_file_exists_replace 上传文件存在替换，0为不替换替换，1为强制替换
	 * @param $file_size 已上传的大小
	 * @return array
	 */
	public function client_synchro_file($client_id,$service_id,$user_id,$upload_type = 0,$user_file_string = '',$update_style = 0,$source_file_name,$is_file_exists_replace = 0,$file_size){
		
		$check = false;
		$error_status = true;
		$info_array = array();		
		$client_id = intval($client_id);
		$this->error_array = array();
		$this->error_code_array = array();
		if($client_id <= 0){
				
			$this->error_array['result'] = '非法数据';
			$this->error_code_array['result'] = INVALID_DATA;
			return $info_array;
		}
		
		$this->upload_base_dir = BASE_DIR.'Public/Upload/';
		$check = $this->mk_dir($this->upload_base_dir,$this->mode,$error_status);
		if(!$check){
		
			return $info_array;
		}
		$this->upload_base_dir .= 'usernote/';
		$check = $this->mk_dir($this->upload_base_dir,$this->mode,$error_status);
		if(!$check){
		
			return $info_array;
		}
		
		$service_id = intval($service_id);
		$user_id = intval($user_id);		
		$check_exists = $this->check_exists($service_id,$user_id);
		if($check_exists){
			
			$this->upload_base_dir .= strval($user_id).'/';
			$this->upload_base_url .= strval($user_id).'/';
			$check = $this->mk_dir($this->upload_base_dir,$this->mode,$error_status);
			if(!$check){			
				
				return $info_array;
			}
			$fh = @fopen($this->upload_base_dir.'index.html','ab');			
			@fclose($fh);
			$this->upload_base_dir = self::get_user_dir_path($user_id);
			$check = $this->mk_dir($this->upload_base_dir,$this->mode,$error_status);
			if(!$check){
			
				return $info_array;
			}
			$this->upload_base_url = self::get_user_url($user_id);
			$upload_base_url = $this->upload_base_url;
			$upload_type = intval($upload_type);
			if($upload_type === 0 || $upload_type === 2 || $upload_type === 3){
				
				$check_mk_file = true;					
				$result = $this->field('tmp_path')->where(' id = '.$service_id.' and user_id = '.$user_id.' and is_delete = 0 ')->find();
				if(check_array_valid($result)){
						
					$tmp_path = trim(stripslashes($result['tmp_path']));
					$tmp_path_len = strlen($tmp_path);
					if($tmp_path_len > 0 && !file_exists($tmp_path) && $upload_type === 2){
						
						$this->error_array['result'] = '文件不存在';
						$this->error_code_array['result'] = ERROR_FILE_NOT_EXISTS;
						return $info_array;
					}elseif($tmp_path_len > 0 && file_exists($tmp_path)){
						
						if($upload_type === 2){
							
							$check_mk_file = false;
						}else{
							
							if(substr($tmp_path,0,2) == './'){
								
								$tmp_path = substr($tmp_path,2);
							}elseif(substr($tmp_path,0,1) == '/'){
								
								$tmp_path = substr($tmp_path,1);
							}
							$tmp_path = BASE_DIR.$tmp_path;
							if(file_exists($tmp_path)){
								
								@unlink($tmp_path);
							}							
						}
						
					}
				}else{
				
					$this->error_array['result'] = '数据库报错';
					$this->error_code_array['result'] = ERROR_MYSQL_EXE_REPORT;
					return $info_array;
				}				
				
				if($check_mk_file){
						
					$update_array = array();
					$file_name = date('YmdHis',$this->current_time).strval(rand(1111,9999)).'.part';
					$tmp_path = $this->upload_base_dir.$file_name;
					$fh = @fopen($tmp_path,'ab');
					@fclose($fh);
					if(!file_exists($tmp_path)){
						
						$this->error_array['result'] = '文件创建失败';
						$this->error_code_array['result'] = ERROR_FILE_MK_FAILED;
						return $info_array;
					}
					
					$update_array['tmp_path'] = addslashes($tmp_path);
					$result = $this->where(' id = '.$service_id.' and user_id = '.$user_id.' and is_delete = 0 ')->data($update_array)->save();
					if($result === false){
						 
						$this->error_array['result'] = '保存失败';
						$this->error_code_array['result'] = ERROR_CONSERVE_FAILED;
						return $info_array;
					}
				}
			}else{
				
				$result = $this->field('tmp_path')->where(' id = '.$service_id.' and user_id = '.$user_id.' and is_delete = 0 ')->find();
				if(check_array_valid($result)){
					
					$tmp_path = trim(stripslashes($result['tmp_path']));
					if(strlen($tmp_path) <= 0 || !file_exists($tmp_path)){
						
						if(strlen($tmp_path) <= 0){
							
							$this->error_array['result'] = '文件不能为空';
							$this->error_code_array['result'] = ERROR_FILE_PATH_NOT_EMPTY;
						}else{
							
							$this->error_array['result'] = '文件不存在';
							$this->error_code_array['result'] = ERROR_FILE_NOT_EXISTS;
						}
						return $info_array;
					}			
					
				}else{
					
					$this->error_array['result'] = '数据库报错';
					$this->error_code_array['result'] = ERROR_MYSQL_EXE_REPORT;
					return $info_array;
				}
			
			}
			$this->tmp_path = $tmp_path;
			$source_file_name = trim($source_file_name);			
			if(empty($source_file_name)){
				
				$this->error_array['result'] = '文件名不能为空';
				$this->error_code_array['result'] = ERROR_FILE_NAME_NOT_EMPTY;
				return $info_array;
			}elseif(strpos($source_file_name,'.zip') === false){
				
				$this->error_array['result'] = '文件类型不合法';
				$this->error_code_array['result'] = ERROR_FILE_TYPE;
				return $info_array;
			}elseif(strlen($source_file_name) == 4){
				
				$this->error_array['result'] = '文件名不合法';
				$this->error_code_array['result'] = ERROR_FILE_NAME_INVALID;
				return $info_array;
			}
			$source_file_name_array = explode('.',$source_file_name);
			$start_str = $source_file_name_array[0];
			$end_str = end($source_file_name_array);
			if($start_str == '' || $end_str != 'zip'){
				
				$this->error_array['result'] = '文件名不合法';
				$this->error_code_array['result'] = ERROR_FILE_NAME_INVALID;
				return $info_array;
			}
			$path = $this->upload_base_dir.$source_file_name;
			$is_file_exists_replace = $is_file_exists_replace === 1 ? 1 :0;			
			if($is_file_exists_replace === 0 && file_exists($path)){
					
				$this->error_array['result'] = '文件已存在';
				$this->error_code_array['result'] = ERROR_FILE_EXISTS;
				return $info_array;
			}else{
				
				deleteAwsFile($path);
				@unlink($path);
			}
			$this->path = $path;
			$update_style = intval($update_style);
			$update_style = $update_style === 1 ? 1 : 0;			
			if($update_style === 0){
				//var_dump($update_style);
				$check = $this->upload_file_string_process($upload_type,$user_file_string,$file_size);//上传文件处理
			}else{
				
				$check = $this->upload_file_process($upload_type,$file_size);//上传文件处理
			}
			if($check){
				
				//成功处理	
				$file_size = $upload_type === 2 || $upload_type === 3 ? filesize('.'.$this->path) : filesize($this->tmp_path);
				$file_size = $file_size ? $file_size : 0;
				$info_array['client_id'] = $client_id;
				$info_array['service_id'] = $service_id;
				$info_array['user_id'] = $user_id;
				$info_array['file_size'] = $file_size;
				$info_array['file_path'] = strval($upload_base_url.$source_file_name);
				if($upload_type === 2 || $upload_type === 3){
					
					$update_array = array();
					$update_array['path'] = addslashes($this->path);
					$update_array['tmp_path'] = '';
					$update_array['status'] = 1;
					$result = $this->where(' id = '.$service_id.' and user_id = '.$user_id.' and is_delete = 0 ')->data($update_array)->save();
					if($result === false){
						
						$info_array = array();
						$this->error_array['result'] = '保存失败';
						$this->error_code_array['result'] = ERROR_CONSERVE_FAILED;
					}else{
						
						$info_array['file_path'] = getFullUrl($this->path);
					}
				}
			}
		}else{
			
			$this->error_array['result'] = '非法数据';
			$this->error_code_array['result'] = INVALID_DATA;			
		}
		return $info_array;
	}
	
	/**
	 * 上传文件字符串处理
	 * @param $upload_type 上传类型 ;0:开始上传,1:为续传,2:结束上传,3：单个上传
	 * @param $file_size 已上传的大小
	 * @return boolean
	 */
	protected function upload_file_string_process($upload_type,$user_file_string,$file_size){
		
		$check = false;
		if(count($this->error_code_array) > 0 || count($this->error_array) > 0 ){
		
			return $check;
		}
		
		$path = $this->path;		
		$upload_type = intval($upload_type);
		$tmp_path = $this->tmp_path;
		$fh_write = @fopen($tmp_path,'ab');		
		if(!file_exists($tmp_path) && !is_writeable($tmp_path)){
				
			$check_upload = false;
			$this->error_array['result'] = '文件不可写';
			$this->error_code_array['result'] = ERROR_FILE_NOT_WRITEABLE;
			@fclose($fh_write);
			return false;
		}		
		$file_str = strval($user_file_string);
		if(strlen($file_str)> 0){
			
			$file_str = strval(base64_decode($file_str));
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
			$file_str_len = strlen($file_str);
			if($extend_file_size < $file_str_len){
				
				$file_str_len -= $extend_file_size;
				$file_str = substr($file_str,$extend_file_size,$file_str_len);
				@flock($fh_write, LOCK_EX) ;
				@fwrite($fh_write,$file_str,$file_str_len);
				@flock($fh_write, LOCK_UN);
			}			
			@fclose($fh_write);
		}
		
		$file_str = $user_file_string = '';
		if($upload_type === 2 || $upload_type === 3){
		
			$allow_file_types = $this->allow_file_types;
			
			if(!rename($this->tmp_path,$path)){
				
				$this->error_array['result'] = '修改文件名失败';
				$this->error_code_array['result'] = ERROR_FILE_RENAME_FAILED;				
				return false;
			}
			
			$file_path = $path;
			$path_array = explode('/',$path);
			$file_name = end($path_array);
			$prefix_path = substr($path,0,-strlen($file_name));
			
			$acl = 'public-read';
			$root_path = dirname(dirname(dirname(dirname(__FILE__))));
			$aws_vendor_object = new \AwsVendor\AwsVendor(C('AWS_BUCKET'),C('AWS_BASE_PATH'));			
			$tmp_aws_base_path = $aws_base_path = $aws_vendor_object->get_name('aws_base_path');
			if(substr($aws_base_path,0,1) === '/'){
			
				$tmp_aws_base_path = substr($aws_base_path,1);
			}
			$prefix_path_array = explode($aws_base_path,$prefix_path);			
			$prefix_path = end($prefix_path_array);
			
			$pathToFile = $aws_vendor_object->get_aws_file_path($prefix_path,$file_name);
			
			$this->path = $pathToFile;			
			$check = $aws_vendor_object->upload_process($file_path,$pathToFile,$acl);
			if(!$check){
					
				$error_array = $aws_vendor_object->get_error_array();
				$this->error_array['result'] = isset($error_array['result']) ? $error_array['result'] : '同步到亚马逊失败';
				$this->error_code_array['result'] = ERROR_SYNC_FAILED;
			}
			
			$check_upload = $check;
		
			$check = true;
		}else{
		
			$check = true;
		}
		return $check;
	}
	
	/**
	 * 上传文件处理
	 * @param $upload_type 上传类型 ;0:开始上传,1:为续传,2:结束上传 ,3：单个上传
	 * @param $file_size 已上传的大小
	 * @return boolean
	 */
	protected function upload_file_process($upload_type,$file_size){
	
		$check = false;
		if(count($this->error_code_array) > 0 || count($this->error_array) > 0 ){
				
			return $check;
		}elseif(!isset($_FILES['user_file'])){

			$this->error_array['result'] = '没有文件被上传';
			$this->error_code_array['result'] = ERROR_NOT_UPLOADED_FILE;
			 
			return $check;
		}
		$check_upload = true;
	
		$user_file = $_FILES['user_file'];
		if($user_file['error'] > 0){
				
			$check_upload = false;
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
						
					$check_upload = false;
					$this->error_array['result'] = '文件大小超过服务器限制';
					$this->error_code_array['result'] = ERROR_SERVER_LIMIT_SIZE;
				}else{
										
					$path = $this->path;					
					//var_dump($user_file['tmp_name']);
					//var_dump($user_file['name']);
					$upload_type = intval($upload_type);
					$tmp_file_path = $this->upload_base_dir.date('YmdHis',$this->current_time).strval(rand(111111,999999)).'.part';					
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
					$tmp_path = $this->tmp_path;				
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
					
					if($upload_type === 2 || $upload_type === 3){
						
						$allow_file_types = $this->allow_file_types;						
						if(!rename($this->tmp_path,$path)){						
						
							$this->error_array['result'] = '修改文件名失败';
							$this->error_code_array['result'] = ERROR_FILE_RENAME_FAILED;
							return false;
						}
						
						$file_path = $path;
						$path_array = explode('/',$path);
						$file_name = end($path_array);
						$prefix_path = substr($path,0,-strlen($file_name));
							
						$acl = 'public-read';
						$root_path = dirname(dirname(dirname(dirname(__FILE__))));
						$aws_vendor_object = new \AwsVendor\AwsVendor(C('AWS_BUCKET'),C('AWS_BASE_PATH'));
						$tmp_aws_base_path = $aws_base_path = $aws_vendor_object->get_name('aws_base_path');
						if(substr($aws_base_path,0,1) === '/'){
								
							$tmp_aws_base_path = substr($aws_base_path,1);
						}
						$prefix_path_array = explode($aws_base_path,$prefix_path);
						$prefix_path = end($prefix_path_array);
							
						$pathToFile = $aws_vendor_object->get_aws_file_path($prefix_path,$file_name);
							
						$this->path = $pathToFile;
						$check = $aws_vendor_object->upload_process($file_path,$pathToFile,$acl);
						if(!$check){
								
							$error_array = $aws_vendor_object->get_error_array();
							$this->error_array['result'] = isset($error_array['result']) ? $error_array['result'] : '同步到亚马逊失败';
							$this->error_code_array['result'] = ERROR_SYNC_FAILED;
						}
										
						$check_upload = $check;
					}else{
						
						$check_upload = true;
					}				
									
				}
			}else{
	
				$check_upload = false;			
				$this->error_array['result'] = '非上传文件';
				$this->error_code_array['result'] = ERROR_FILE_NOT_UPLOADED;
			}
				
		}
		$check = $check_upload;
		return $check;
	
	}
	/**
	 * 移入回收站/恢复数据
	 * @param unknown $service_id
	 * @param unknown $user_id
	 * @param unknown $type_id
	 * $is_recy 1 移入回收 0 恢复
	 */
	public function recy_note($note_id,$user_id,$type_id=1,$is_recy= 1){
		$user_id = intval($user_id);
		$note_id = trim($note_id);
		$type_id = intval($type_id);
		$type_id = $type_id > 0 ? $type_id : 1;
		
		$str_where = 'user_id = '.$user_id.' and type_id = '.$type_id.' and id = '.$note_id;
		$result = $this->field('id,path,tmp_path')->where($str_where)->select();
		if (check_array_valid($result)){
			$data['is_delete'] = $is_recy;
			$check = $this->where($str_where)->save($data);
		}
	
		return $check;
	
	}
	
	
	/**
	 * 删除便签同时删除便签文件
	 * @param unknown $service_id
	 * @param unknown $user_id
	 */	 
	public function delete_process($service_id,$user_id,$type_id){
	
		$check = false;
		$user_id = intval($user_id);
		$service_id = trim($service_id);
		$type_id = intval($type_id);
		$type_id = $type_id > 0 ? $type_id : 1;
		if($user_id > 0 && strlen($service_id) > 0){
			
			$check_valid = false;
			$service_id_array = $service_id_array_tmp = array();
			$service_id_array_tmp = explode(',',$service_id);
			$str_where = 'user_id = '.$user_id.' and type_id = '.$type_id.' ';
			if(check_array_valid($service_id_array_tmp)){				
				
				foreach($service_id_array_tmp as $value){
					
					$id = intval($value);
					if($id > 0){
						
						$service_id_array[] = $id;											
					}					
				}
				
			}elseif(intval($service_id) > 0){
				
				$service_id_array[] = intval($service_id);
			}
			
			if(check_array_valid($service_id_array)){
				
				if(count($service_id_array)==1){
					
					$str_where .= ' and id = '.$service_id_array[0].' ';
				}else{
					
					$str_where .= ' and (id in ('.implode(',',$service_id_array).')) ';
				}
				
				$result = $this->field('id,path,tmp_path')->where($str_where)->select();
				
				if(check_array_valid($result)){					
					
					$check = $this->where($str_where)->delete();
					if($check){
						
						$aws_vendor_object = new \AwsVendor\AwsVendor(C('AWS_BUCKET'),C('AWS_BASE_PATH'));
						foreach($result as $value){						
							
							$path = trim($value['path']);
							$tmp_path = trim($value['tmp_path']);
							if(strlen($path) > 0){
									
								$aws_vendor_object->delete_process($path);
								if(file_exists($path)){
						
									@unlink($path);
								}
							}
						
							if(strlen($tmp_path) > 0 && file_exists($tmp_path)){
									
								@unlink($tmp_path);
							}
						}
					}else{
						
						$this->error_array['result'] = '删除失败';
						$this->error_code_array['result'] = ERROR_DEL_FAILED;
					}
										
				}else{
					
					$check = true;
				}				
			}						
		}else{
					
			$this->error_array['result'] = '非法数据';
			$this->error_code_array['result'] = INVALID_DATA;			
		}
		
		return $check;
	
	}	
	
	/**
	 * 清除用户便签同时删除便签文件
	 * @param unknown $service_id
	 * @param unknown $user_id
	 */
	public function clear_process($user_id,$type_id){
	
		$check = false;
		$user_id = intval($user_id);		
		$type_id = intval($type_id);
		$type_id = $type_id > 0 ? $type_id : 1;		
		if($user_id > 0){			
			$str_where = 'user_id = '.$user_id.' and type_id = '.$type_id.' ';	
			$result = $this->field('id,path,tmp_path')->where($str_where)->select();
			if(check_array_valid($result)){
				$check = $this->where($str_where)->delete();
				if($check){
					$aws_vendor_object = new \AwsVendor\AwsVendor(C('AWS_BUCKET'),C('AWS_BASE_PATH'));
					foreach($result as $value){			
						$path = trim($value['path']);
						$tmp_path = trim($value['tmp_path']);
						if(strlen($path) > 0){
							$aws_vendor_object->delete_process($path);
							if(file_exists($path)){
								@unlink($path);
							}
						}
						if(strlen($tmp_path) > 0 && file_exists($tmp_path)){
							@unlink($tmp_path);
						}
					}
				}else{
					$this->error_array['result'] = '删除失败';
					$this->error_code_array['result'] = ERROR_DEL_FAILED;
				}
			}else{
				$check = true;
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