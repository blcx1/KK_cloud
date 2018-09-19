<?php
namespace Home\Model;
use Think\Model;

abstract class ContactsBaseModel extends \Home\Model\DefaultModel implements \Home\Model\ContactsInterfaceModel{
		
	protected $mimetype = '';
	protected $id_name='server_other_id';
	protected $check_is_delete = false;
	protected $check_add_time = false;
	protected $check_update_time = false;
	protected $check_md5_str = false;
	protected $mimetype_array = array();

	abstract public function contacts_list2current_list_relation();//联系列表与当前列表对应关系 id,base_id
	
	/**
	 * (non-PHPdoc)
	 * @see \Home\Model\DefaultModel::__destruct()
	 */
	public function __destruct(){
	
		parent::__destruct();
		$this->mimetype = '';
		$this->id_name = '';
		$this->check_is_delete = false;
		$this->check_add_time = false;
		$this->check_update_time = false;
		$this->check_md5_str = false;
		$this->mimetype_array = array();
	}
	
	/**
	 * 生成md5字符串
	 * @param unknown $value
	 * @param unknown $conversion_relation
	 * @return string
	 */
	public static function make_md5_str($value,$conversion_relation){
		
		$md5_str = '';
		$md5_string = '';		
		foreach($conversion_relation as $conversion_relation_key=>$conversion_relation_value){
				
			if($conversion_relation_key != 'data_id' && strpos($conversion_relation_key,'data_') !== false){
		
				$md5_string .= $conversion_relation_key.':'.(isset($value[$conversion_relation_value]) ? $value[$conversion_relation_value] : '');
			}
		}
		$md5_str = md5($md5_string);		
		return $md5_str;
	}
	
	public function info_del($where,$version_id = 0,$clear_cache = false){
		
		$check_is_delete = boolval($this->check_is_delete);
		
		if(!$check_is_delete){
			
			return parent::info_delete($where);
		}
		$check = false;		
		if(check_array_valid($where) || (is_string($where) && strlen(trim($where)) > 0)){
			
			if($clear_cache){
				
				$this->clear_cache($where);//清除对应的cache
			}
			
			$update_array = array();
			$update_array['is_delete'] = 1;
			$update_array['version_id'] = $version_id;
			$result = $this->where($where)->data($update_array)->save();			
			$check = $result === false ? false : true;
		}
		return $check;
	}
	
	/**
	 * 信息恢复
	 * @param unknown $where
	 * @param unknown $other_update_array
	 * @return boolean
	 */
	public function info_recover($where,$other_update_array = array(),$clear_cache = false){
		
		$check_is_delete = boolval($this->check_is_delete);
		if(!$check_is_delete){
				
			return false;
		}
		
		$check = false;
		if(check_array_valid($where) || (is_string($where) && strlen(trim($where)) > 0)){
			
			if($clear_cache){
				
				$this->clear_cache($where);//清除对应的cache
			}			
			$other_update_array = is_array($other_update_array) ? $other_update_array : array();
			$update_array = $other_update_array;
			$update_array['is_delete'] = 0;
			$result = $this->where($where)->data($update_array)->save();
			$check = $result === false ? false : true;
		}
		return $check;
	}
	
	/**
	 * 采用contacts 列表方式添加
	 * @param \Home\Model\ContactsVersionModel $version_object
	 * @param \Home\Model\ContactsVersionRelationModel $version_relation_object
	 * @param \Home\Model\ContactsListModel $contacts_list_object
	 * @param unknown $user_id
	 * @param unknown $version_auto_id
	 * @param unknown $last_version_id
	 * @param unknown $add_list
	 * @param unknown $add_success_list
	 * @param unknown $failed_list
	 * @param string $check_update_version_id
	 * @param unknown $update_success_list
	 * @return Ambigous <multitype:Ambigous <number, unknown> boolean Ambigous <multitype:, unknown> , multitype:unknown number boolean string Ambigous <number, string, unknown> >
	 */
	public function specical_add(\Home\Model\ContactsVersionModel $version_object,\Home\Model\ContactsVersionRelationModel $version_relation_object,\Home\Model\ContactsListModel $contacts_list_object,$user_id,$version_auto_id,$last_version_id,$add_list,$add_success_list,$failed_list,$check_update_version_id = true,$update_success_list = array()){

		$user_id = intval($user_id);
		$version_auto_id = intval($version_auto_id);
		$last_version_id = $last_version_id >= 0 ? $last_version_id : 0;
		$failed_list = is_array($failed_list) ? $failed_list : array();
		$add_success_list = is_array($add_success_list) ? $add_success_list : array();
		$update_success_list = is_array($update_success_list) ? $update_success_list : array();
		$check_update_version_id = boolval($check_update_version_id);
		$return_array = array('last_version_id'=>$last_version_id,'check_update_version_id'=>$check_update_version_id,
				              'add_success_list'=>$add_success_list,'failed_list'=>$failed_list,'update_success_list'=>$update_success_list);		
		if($user_id > 0 && check_array_valid($add_list) && $version_auto_id > 0){

			$conversion_relation = $this->contacts_list2current_list_relation();		
			$add_list = $this->contacts_list_conversion_current_list($add_list,true);
			if(check_array_valid($add_list)){
								
				$sign_type = 'add';
				$md5_str_array = array();
				$id_name = $this->id_name;
				$mimetype = $this->mimetype;
				if($this->check_md5_str){
					
					$str_where = 'user_id = '.$user_id;
					$result = $this->field('id,md5_str')->where($str_where)->select();
					if(check_array_valid($result)){
						
						foreach($result as $key=>$value){
							
							$md5_str_array[$value['md5_str']] = intval($value['id']);
						}						
					}					
				}
				$check_is_delete = $this->check_is_delete;
				$check_add_time = $this->check_add_time;
				$check_update_time = $this->check_update_time;		
				$tmp_row_source = array();
				$tmp_row_source['sign_type'] = $sign_type;
				$tmp_row_source['mime_type'] = $mimetype;
				foreach($add_list as $key=>$value){
					
					$tmp_row = $tmp_row_source;					
					$data_id = isset($value['data_id']) ? intval($value['data_id']) : 0;
					$contacts_id = isset($value['contacts_id']) ? intval($value['contacts_id']) : 0;					
					$tmp_row['data_id'] = $data_id;
					$tmp_row['contacts_id'] = $contacts_id;					
					if($id_name == 'server_id'){
						
						$server_id = 0;		
					}else{
						
						$server_id = isset($value['base_id']) ? intval($value['base_id']) : 0;										
					}
					$tmp_row['server_id'] = $server_id;
					$tmp_row['server_other_id'] = 0;
					if($data_id <= 0 || $contacts_id <= 0 || $server_id < 0){			
						
						$tmp_row['failed_type'] = INVALID_DATA;
						$failed_list[] = $tmp_row;
						continue;
					}
					
					if(isset($value[$id_name])){
						
						unset($value[$id_name]);
					}
					if(isset($value['data_id'])){
						
						unset($value['data_id']);
					}
					if(isset($value['contacts_id'])){
					
						unset($value['contacts_id']);
					}
					
					if($check_update_version_id){
						
						$last_version_id += 1;
						$update_check = $version_object->update_version_id($version_auto_id,$user_id,$last_version_id);
						if($update_check){
							
							$check_update_version_id = false;							
						}else{
							
							$last_version_id -= 1;
						}
					}
										
					$value['user_id'] = $user_id;
					$value['version_id'] = $last_version_id;
					if($check_is_delete){
						
						$value['is_delete'] = isset($value['is_delete']) && intval($value['is_delete']) == 1 ? 1 : 0;
					}
					if($check_add_time){
						
						$value['add_time'] = $this->current_datetime;
					}
					if($check_update_time){
						
						$value['update_time'] = $this->current_datetime;
					}
					$id = 0;
					$md5_str = '';
					if($this->check_md5_str){						

						$md5_str = self::make_md5_str($value,$conversion_relation);
						$value['md5_str'] = $md5_str;						
						if(isset($md5_str_array[$md5_str])){
							
							$id = $md5_str_array[$md5_str];
							$value['id'] = $id;
						}						
					}	
					if($id > 0){				
						
						$sign_type = 'upd';
						$str_where = 'id = '.$id;
						$this->info_update($str_where,$value);
					}else{
						
						$sign_type = 'add';
						$id = $this->info_add($value);
						if($id > 0 && $this->check_md5_str){
								
							$md5_str_array[$md5_str] = $id;
						}
					}			
					
					if($id > 0){
												
						if($id_name == 'server_id'){
							
							$update_array = array();
							$update_array['server_id'] = $id;
							$where = ' user_id = '.$user_id.' and contacts_id = '.$contacts_id;
							$contacts_list_object->info_update($where,$update_array);
						}
						$tmp_row[$id_name] = $id;
						$tmp_row['version_id'] = $last_version_id;
						$add_info = array();
						$add_info['contacts_version_id'] = $version_auto_id;
						$add_info['server_id'] = $tmp_row['server_id'];
						$add_info['server_other_id'] = $tmp_row['server_other_id'];
						$add_info['mime_type'] = $mimetype;						
						$add_info['sign_type'] = $sign_type;
						$add_info['version_id'] = $last_version_id;
						$version_relation_object->relation_add($add_info);
						if($sign_type == 'add'){
							
							$add_success_list[] = $tmp_row;
						}else{
							
							$update_success_list[] = $tmp_row;
						}					
					}else{
						
						$tmp_row['failed_type'] = ERROR_ADD_FAILED;
						$failed_list[] = $tmp_row;
					}
				}
								
				$return_array = array('last_version_id'=>$last_version_id,'check_update_version_id'=>$check_update_version_id,
				                      'add_success_list'=>$add_success_list,'failed_list'=>$failed_list,'update_success_list'=>$update_success_list);		
				
			}			
		}
		
		return $return_array;
	}
	
	/**
	 * 采用contacts 列表方式更新
	 * @param \Home\Model\ContactsVersionModel $version_object
	 * @param \Home\Model\ContactsVersionRelationModel $version_relation_object
	 * @param unknown $user_id
	 * @param unknown $version_auto_id
	 * @param unknown $last_version_id
	 * @param unknown $update_list
	 * @param unknown $update_success_list
	 * @param unknown $failed_list
	 * @param string $check_update_version_id
	 * @return Ambigous <multitype:Ambigous <number, unknown> boolean Ambigous <multitype:, unknown> , multitype:number boolean string Ambigous <number, string> >
	 */
	public function specical_update(\Home\Model\ContactsVersionModel $version_object,\Home\Model\ContactsVersionRelationModel $version_relation_object,$user_id,$version_auto_id,$last_version_id,$update_list,$update_success_list,$failed_list,$check_update_version_id = true){
		
		$user_id = intval($user_id);
		$version_auto_id = intval($version_auto_id);
		$last_version_id = $last_version_id >= 0 ? $last_version_id : 0;
		$failed_list = is_array($failed_list) ? $failed_list : array();
		$update_success_list = is_array($update_success_list) ? $update_success_list : array();
		$check_update_version_id = boolval($check_update_version_id);
		$return_array = array('last_version_id'=>$last_version_id,'check_update_version_id'=>$check_update_version_id,
			              	  'update_success_list'=>$update_success_list,'failed_list'=>$failed_list);
		if($user_id > 0 && check_array_valid($update_list) && $version_auto_id > 0){
				
			$conversion_relation = $this->contacts_list2current_list_relation();
			$update_list = $this->contacts_list_conversion_current_list($update_list,true);
			if(check_array_valid($update_list)){
		
				$sign_type = 'upd';
				$id_name = $this->id_name;
				$mimetype = $this->mimetype;
				$check_update_time = $this->check_update_time;
				$tmp_row_source = array();
				$tmp_row_source['sign_type'] = $sign_type;
				$tmp_row_source['mime_type'] = $mimetype;
				foreach($update_list as $key=>$value){
					
					$tmp_row = $tmp_row_source;								
					$data_id = isset($value['data_id']) ? intval($value['data_id']) : 0;
					$contacts_id = isset($value['contacts_id']) ? intval($value['contacts_id']) : 0;
						
					$tmp_row['data_id'] = $data_id;
					$tmp_row['contacts_id'] = $contacts_id;
					if($id_name == 'server_id'){
							
						$server_id = isset($value['id']) ? intval($value['id']) : 0;
						$server_other_id = 0;						
					}else{
		
						$server_id = isset($value['base_id']) ? intval($value['base_id']) : 0;
						$server_other_id = isset($value['id']) ? intval($value['id']) : 0;
					}
					$tmp_row['server_id'] = $server_id;
					$tmp_row['server_other_id'] = $server_other_id;
					if($data_id <= 0 || $contacts_id <= 0 || $server_id <= 0 || ($id_name != 'server_id' && $server_other_id <= 0)){
		
						$tmp_row['failed_type'] = INVALID_DATA;
						$failed_list[] = $tmp_row;
						continue;
					}
						
					if(isset($value[$id_name])){
		
						unset($value[$id_name]);
					}
					if(isset($value['data_id'])){
							
						unset($value['data_id']);
					}
					if(isset($value['contacts_id'])){
							
						unset($value['contacts_id']);
					}
					
					if($check_update_version_id){
		
						$last_version_id += 1;
						$update_check = $version_object->update_version_id($version_auto_id,$user_id,$last_version_id);
						if($update_check){
								
							$check_update_version_id = false;
						}else{
								
							$last_version_id -= 1;
						}
					}
					
					if($this->check_md5_str){
					
						$md5_str = self::make_md5_str($value,$conversion_relation);
						$value['md5_str'] = $md5_str;						
					}	
					
					$value['user_id'] = $user_id;
					$value['version_id'] = $last_version_id;				
					if($check_update_time){
		
						$value['update_time'] = $this->current_datetime;
					}
					$where = array();					
					$where['id'] = $id_name == 'server_id' ? $server_id : $server_other_id;
					$check = $this->info_update($where,$value);				
					if($check){
						
						$add_info = array();
						$add_info['contacts_version_id'] = $version_auto_id;
						$add_info['server_id'] = $server_id;
						$add_info['server_other_id'] = $server_other_id;
						$add_info['mime_type'] = $mimetype;
						$add_info['sign_type'] = $sign_type;
						$add_info['version_id'] = $last_version_id;
						$version_relation_object->relation_add($add_info);
						$tmp_row['version_id'] = $last_version_id;
						$update_success_list[] = $tmp_row;
					}else{
						
						$tmp_row['failed_type'] = ERROR_ADD_FAILED;
						$failed_list[] = $tmp_row;
					}
				}
		
				$return_array = array('last_version_id'=>$last_version_id,'check_update_version_id'=>$check_update_version_id,
			              	          'update_success_list'=>$update_success_list,'failed_list'=>$failed_list);
		
			}
		}
		
		return $return_array;
	}
	
	/**
	 * 放入回收站
	 * @param \Home\Model\ContactsVersionModel $version_object
	 * @param \Home\Model\ContactsVersionRelationModel $version_relation_object
	 * @param unknown $user_id
	 * @param unknown $version_auto_id
	 * @param unknown $last_version_id
	 * @param unknown $delete_list
	 * @param unknown $delete_success_list
	 * @param unknown $failed_list
	 * @param string $check_update_version_id
	 * @return Ambigous <multitype:Ambigous <number, unknown> boolean Ambigous <multitype:, unknown> , multitype:number boolean string Ambigous <number, string> >
	 */
	public function specical_delete(\Home\Model\ContactsVersionModel $version_object,\Home\Model\ContactsVersionRelationModel $version_relation_object,$user_id,$version_auto_id,$last_version_id,$delete_list,$delete_success_list,$failed_list,$check_update_version_id = true){
		
		$check_is_delete = $this->check_is_delete;
		if(!$check_is_delete){
			
			return $this->specical_force_delete($version_object, $version_relation_object, $user_id, $version_auto_id, $last_version_id, $delete_list, $delete_success_list, $failed_list,$check_update_version_id);
		}
		$user_id = intval($user_id);		
		$version_auto_id = intval($version_auto_id);
		$last_version_id = $last_version_id >= 0 ? $last_version_id : 0;
		$failed_list = is_array($failed_list) ? $failed_list : array();
		$delete_success_list = is_array($delete_success_list) ? $delete_success_list : array();
		$check_update_version_id = boolval($check_update_version_id);
		$return_array = array('last_version_id'=>$last_version_id,'check_update_version_id'=>$check_update_version_id,
				              'delete_success_list'=>$delete_success_list,'failed_list'=>$failed_list);
		if($user_id > 0 && check_array_valid($delete_list) && $version_auto_id > 0){
		
			$delete_list = $this->contacts_list_conversion_current_list($delete_list,true);
			if(check_array_valid($delete_list)){
		
				$sign_type = 'del';
				$id_name = $this->id_name;
				$mimetype = $this->mimetype;
				$tmp_row_source = array();
				$tmp_row_source['sign_type'] = $sign_type;
				$tmp_row_source['mime_type'] = $mimetype;
				foreach($delete_list as $key=>$value){
		            					
					$tmp_row = $tmp_row_source;					
					$data_id = isset($value['data_id']) ? intval($value['data_id']) : 0;
					$contacts_id = isset($value['contacts_id']) ? intval($value['contacts_id']) : 0;
		
					$tmp_row['data_id'] = $data_id;
					$tmp_row['contacts_id'] = $contacts_id;					
					if($id_name == 'server_id'){
							
						$server_id = isset($value['id']) ? intval($value['id']) : 0;
						$server_other_id = 0;
					}else{
					
						$server_id = isset($value['base_id']) ? intval($value['base_id']) : 0;
						$server_other_id = isset($value['id']) ? intval($value['id']) : 0;
					}
					$tmp_row['server_id'] = $server_id;
					$tmp_row['server_other_id'] = $server_other_id;
					if($data_id <= 0 || $contacts_id <= 0 || $server_id <= 0 || ($id_name != 'server_id' && $server_other_id <= 0)){
		
						$tmp_row['failed_type'] = INVALID_DATA;
						$failed_list[] = $tmp_row;
						continue;
					}
					
					if($check_update_version_id){
		
						$last_version_id += 1;
						$update_check = $version_object->update_version_id($version_auto_id,$user_id,$last_version_id);
						if($update_check){
		
							$check_update_version_id = false;
						}else{
		
							$last_version_id -= 1;
						}
					}
		
					$where = array();
					$where['user_id'] = $user_id;
					$where['id'] = $id_name == 'server_id' ? $server_id : $server_other_id;					
					$check = $this->info_del($where,$last_version_id);
					if($check){
		
						$add_info = array();
						$add_info['contacts_version_id'] = $version_auto_id;
						$add_info['server_id'] = $server_id;
						$add_info['server_other_id'] = $server_other_id;
						$add_info['mime_type'] = $mimetype;
						$add_info['sign_type'] = $sign_type;
						$add_info['version_id'] = $last_version_id;
						$version_relation_object->relation_add($add_info);
						$tmp_row['version_id'] = $last_version_id;
						$delete_success_list[] = $tmp_row;
					}else{
		
						$tmp_row['failed_type'] = ERROR_ADD_FAILED;
						$failed_list[] = $tmp_row;
					}
				}
		
				$return_array = array('last_version_id'=>$last_version_id,'check_update_version_id'=>$check_update_version_id,
						              'delete_success_list'=>$delete_success_list,'failed_list'=>$failed_list);
		
			}
		}
		
		return $return_array;
	}
	
	/**
	 * 从回收站移到正常里面
	 * @param \Home\Model\ContactsVersionModel $version_object
	 * @param \Home\Model\ContactsVersionRelationModel $version_relation_object
	 * @param unknown $user_id
	 * @param unknown $version_auto_id
	 * @param unknown $last_version_id
	 * @param unknown $recover_list
	 * @param unknown $recover_success_list
	 * @param unknown $failed_list
	 * @param string $check_update_version_id
	 * @return multitype:Ambigous <number, unknown> boolean Ambigous <multitype:, unknown> |Ambigous <multitype:Ambigous <number, unknown> boolean Ambigous <multitype:, unknown> , multitype:number boolean string Ambigous <number, string> >
	 */
	public function special_recover(\Home\Model\ContactsVersionModel $version_object,\Home\Model\ContactsVersionRelationModel $version_relation_object,$user_id,$version_auto_id,$last_version_id,$recover_list,$recover_success_list,$failed_list,$check_update_version_id = true){
			
		$user_id = intval($user_id);
		$version_auto_id = intval($version_auto_id);
		$last_version_id = $last_version_id >= 0 ? $last_version_id : 0;
		$failed_list = is_array($failed_list) ? $failed_list : array();
		$recover_success_list = is_array($recover_success_list) ? $recover_success_list : array();
		$check_update_version_id = boolval($check_update_version_id);
		$check_is_delete = $this->check_is_delete;
		$return_array = array('last_version_id'=>$last_version_id,'check_update_version_id'=>$check_update_version_id,
				              'recover_success_list'=>$recover_success_list,'failed_list'=>$failed_list);
				
		if(!$check_is_delete){
		
			return $return_array;
		}
		
		if($user_id > 0 && check_array_valid($recover_list) && $version_auto_id > 0){
		
			$recover_list = $this->contacts_list_conversion_current_list($recover_list,true);
			if(check_array_valid($recover_list)){
		
				$sign_type = 'recover';
				$id_name = $this->id_name;
				$mimetype = $this->mimetype;
				$tmp_row_source = array();
				$tmp_row_source['sign_type'] = $sign_type;
				$tmp_row_source['mime_type'] = $mimetype;
				foreach($recover_list as $key=>$value){
							
					$tmp_row = $tmp_row_source;					
					$data_id = isset($value['data_id']) ? intval($value['data_id']) : 0;
					$contacts_id = isset($value['contacts_id']) ? intval($value['contacts_id']) : 0;
		
					$tmp_row['data_id'] = $data_id;
					$tmp_row['contacts_id'] = $contacts_id;
					if($id_name == 'server_id'){
							
						$server_id = isset($value['id']) ? intval($value['id']) : 0;
						$server_other_id = 0;
					}else{
					
						$server_id = isset($value['base_id']) ? intval($value['base_id']) : 0;
						$server_other_id = isset($value['id']) ? intval($value['id']) : 0;
					}
					$tmp_row['server_id'] = $server_id;
					$tmp_row['server_other_id'] = $server_other_id;
					if($data_id <= 0 || $contacts_id <= 0 || $server_id <= 0 || ($id_name != 'server_id' && $server_other_id <= 0)){
						
						$tmp_row['failed_type'] = INVALID_DATA;
						$failed_list[] = $tmp_row;
						continue;
					}
		
					if(isset($value[$id_name])){
		
						unset($value[$id_name]);
					}
					if($check_update_version_id){
		
						$last_version_id += 1;
						$update_check = $version_object->update_version_id($version_auto_id,$user_id,$last_version_id);
						if($update_check){
		
							$check_update_version_id = false;
						}else{
		
							$last_version_id -= 1;
						}
					}
		
					$where = array();
					$where['user_id'] = $user_id;
					$where['id'] = $id_name == 'server_id' ? $server_id : $server_other_id;
					$other_update_array = array('version_id'=>$last_version_id);
					$check = $this->info_recover($where,$other_update_array);					
					if($check){
		
						$add_info = array();
						$add_info['contacts_version_id'] = $version_auto_id;
						$add_info['server_id'] = $server_id;
						$add_info['server_other_id'] = $server_other_id;
						$add_info['mime_type'] = $mimetype;
						$add_info['sign_type'] = $sign_type;
						$add_info['version_id'] = $last_version_id;
						$version_relation_object->relation_add($add_info);
						$tmp_row['version_id'] = $last_version_id;
						$recover_success_list[] = $tmp_row;
					}else{
		
						$tmp_row['failed_type'] = ERROR_ADD_FAILED;
						$failed_list[] = $tmp_row;
					}
				}
		
				$return_array = array('last_version_id'=>$last_version_id,'check_update_version_id'=>$check_update_version_id,
						              'recover_success_list'=>$recover_success_list,'failed_list'=>$failed_list);
		
			}
		}
		
		return $return_array;
	}
	
	/**
	 * 彻底删除
	 * @param \Home\Model\ContactsVersionModel $version_object
	 * @param \Home\Model\ContactsVersionRelationModel $version_relation_object
	 * @param unknown $user_id
	 * @param unknown $version_auto_id
	 * @param unknown $last_version_id
	 * @param unknown $delete_list
	 * @param unknown $delete_success_list
	 * @param unknown $failed_list
	 * @param string $check_update_version_id
	 * @return Ambigous <multitype:Ambigous <number, unknown> boolean Ambigous <multitype:, unknown> , multitype:number boolean string Ambigous <number, string> >
	 */
	public function specical_force_delete(\Home\Model\ContactsVersionModel $version_object,\Home\Model\ContactsVersionRelationModel $version_relation_object,$user_id,$version_auto_id,$last_version_id,$delete_list,$delete_success_list,$failed_list,$check_update_version_id = true){
		
		$user_id = intval($user_id);
		$version_auto_id = intval($version_auto_id);
		$last_version_id = $last_version_id >= 0 ? $last_version_id : 0;
		$failed_list = is_array($failed_list) ? $failed_list : array();
		$delete_success_list = is_array($delete_success_list) ? $delete_success_list : array();
		$check_update_version_id = boolval($check_update_version_id);
		$return_array = array('last_version_id'=>$last_version_id,'check_update_version_id'=>$check_update_version_id,
				              'delete_success_list'=>$delete_success_list,'failed_list'=>$failed_list);
		if($user_id > 0 && check_array_valid($delete_list) && $version_auto_id > 0){
		
			$delete_list = $this->contacts_list_conversion_current_list($delete_list,true);
			if(check_array_valid($delete_list)){
		
				$sign_type = 'del';
				$id_name = $this->id_name;
				$mimetype = $this->mimetype;				
				$tmp_row_source = array();
				$tmp_row_source['sign_type'] = $sign_type;
				$tmp_row_source['mime_type'] = $mimetype;
				foreach($delete_list as $key=>$value){
		
					$tmp_row = $tmp_row_source;					
					$data_id = isset($value['data_id']) ? intval($value['data_id']) : 0;
					$contacts_id = isset($value['contacts_id']) ? intval($value['contacts_id']) : 0;
		
					$tmp_row['data_id'] = $data_id;
					$tmp_row['contacts_id'] = $contacts_id;					
					if($id_name == 'server_id'){
							
						$server_id = isset($value['id']) ? intval($value['id']) : 0;
						$server_other_id = 0;
					}else{
							
						$server_id = isset($value['base_id']) ? intval($value['base_id']) : 0;
						$server_other_id = isset($value['id']) ? intval($value['id']) : 0;
					}
					$tmp_row['server_id'] = $server_id;
					$tmp_row['server_other_id'] = $server_other_id;
					if($data_id <= 0 || $contacts_id <= 0 || $server_id <= 0 || ($id_name != 'server_id' && $server_other_id <= 0)){
		
						$tmp_row['failed_type'] = INVALID_DATA;
						$failed_list[] = $tmp_row;
						continue;
					}
									
					if($check_update_version_id){
		
						$last_version_id += 1;
						$update_check = $version_object->update_version_id($version_auto_id,$user_id,$last_version_id);
						if($update_check){
		
							$check_update_version_id = false;
						}else{
		
							$last_version_id -= 1;
						}
					}
										
					$where = array();
					$where['user_id'] = $user_id;
					$where['id'] = $id_name == 'server_id' ? $server_id : $server_other_id;
					
					$check = $this->info_force_delete($where);					
					if($check){
		
						$add_info = array();
						$add_info['contacts_version_id'] = $version_auto_id;
						$add_info['server_id'] = $server_id;
						$add_info['server_other_id'] = $server_other_id;
						$add_info['mime_type'] = $mimetype;
						$add_info['sign_type'] = $sign_type;
						$add_info['version_id'] = $last_version_id;
						$version_relation_object->relation_add($add_info);
						$delete_success_list[] = $tmp_row;
					}else{
		
						$tmp_row['failed_type'] = ERROR_ADD_FAILED;
						$failed_list[] = $tmp_row;
					}
				}
		
				$return_array = array('last_version_id'=>$last_version_id,'check_update_version_id'=>$check_update_version_id,
						              'delete_success_list'=>$delete_success_list,'failed_list'=>$failed_list);
		
			}
		}
		
		return $return_array;
	}
	
	/**
	 * 联系列表转化当前列表
	 * @param unknown $list_array
	 * @param boolean $check_same true:只取相同关联值,false:所有值
	 * @return Ambigous <unknown, multitype:unknown >
	 */
	public function contacts_list_conversion_current_list($list_array,$check_same = false){
		
		$conversion_list = array();
		if(check_array_valid($list_array)){
			
			$check_same = boolval($check_same);
			$conversion_relation = $this->contacts_list2current_list_relation();
			if(check_array_valid($conversion_relation)){
				
				$tmp_arr = array();
				$tmp_arr['data_id'] = 0;
				$tmp_arr['contacts_id'] = 0;
				foreach($list_array as $key=>$value){
					
					if($check_same){
						
						$tmp_array = $tmp_arr;
						if(isset($value['data_id'])){
							
							$tmp_array['data_id'] = intval($value['data_id']);
						}
												
						if(isset($value['contacts_id'])){
							
							$tmp_array['contacts_id'] = intval($value['contacts_id']);
						}
						
						foreach($conversion_relation as $left_value=>$right_value){
							
							$tmp_array[$right_value] = isset($value[$left_value]) ? $value[$left_value] : '';							
						}						
						$conversion_list[$key] = $tmp_array;						
					}else{
						
						/* 
						if(isset($value['mime_type'])){
						
							unset($value['mime_type']);
						} 
						*/
						
						foreach($conversion_relation as $left_value=>$right_value){
							
							if($left_value != $right_value){
								
								if(isset($value[$left_value])){
								
									$value[$right_value] = $value[$left_value];
									unset($value[$left_value]);
								}
							}							
						}
						$conversion_list[$key] = $value;
					}				
				}
				
			}else{
				
				$conversion_list = $check_same ? array() : $list_array;
			}
		}
		
		return $conversion_list;
	}
	
	/**
	 * 当前列表转化联系列表	
	 * @param unknown $list_array
	 * @param string $check_same
	 * @return Ambigous <unknown, multitype:string >
	 */
    public function current_list_conversion_contacts_list($list_array,$check_same = false){
		
    	$conversion_list = array();
		if(check_array_valid($list_array)){
			
			$check_same = boolval($check_same);
			$conversion_relation = $this->contacts_list2current_list_relation();
			if(check_array_valid($conversion_relation)){
				
				$mimetype = $this->mimetype;
				$check_sign_type = boolval($check_sign_type);
				
				$tmp_value = array();
				$tmp_value['data_id'] = 0;
				$tmp_value['contacts_id'] = 0;
				$tmp_value['server_id'] = 0;
				$tmp_value['server_other_id'] = 0;
				$tmp_value['version_id'] = 1;
				$tmp_value['sign_type'] = '';
				$tmp_value['mime_type'] = $mimetype;
				$tmp_value['data_1'] = '';
				$tmp_value['data_2'] = '';
				$tmp_value['data_3'] = '';
				$tmp_value['data_4'] = '';
				$tmp_value['data_5'] = '';
				$tmp_value['data_6'] = '';
				$tmp_value['data_7'] = '';
				$tmp_value['data_8'] = '';
				$tmp_value['data_9'] = '';
				$tmp_value['data_10'] = '';
				$tmp_value['data_11'] = '';
				$tmp_value['data_12'] = '';
				$tmp_value['data_13'] = '';
				$tmp_value['data_14'] = '';
				$tmp_value['data_15'] = '';
				$tmp_value['md5_str'] = '';				
				foreach($list_array as $key=>$value){
					
					$tmp_array = $tmp_value;					
					foreach($conversion_relation as $left_value=>$right_value){
													
						if(isset($value[$right_value])){
						
							$tmp_array[$left_value] = $value[$right_value];
							if($right_value != $left_value){
								
								unset($value[$right_value]);
							}
						}		
						if(isset($value['sign_type'])){
							
							$tmp_array['sign_type'] = $value['sign_type'];
						}			
						$tmp_array['version_id'] = isset($value['version_id']) ? intval($value['version_id']) : 1;
					}
					if(!$check_same){
						
						$tmp_arr = $value;
						foreach($tmp_value as $key_tmp=>$tmp_value_v){
							
							$tmp_arr[$key_tmp] = $tmp_array[$key_tmp];
						}
						$conversion_list[$key] = $tmp_arr;
					}else{
						
						$conversion_list[$key] = $tmp_array;
					}									
				}
				
			}else{
				
				$conversion_list = $check_same ? array() : $list_array;
			}
		}
		
		return $conversion_list;
	}
}
?>