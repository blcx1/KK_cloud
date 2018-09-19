<?php

namespace Home\Model;
use Think\Model;

class ContactsPhotoModel extends \Home\Model\ContactsBaseModel {
	
	protected $mimetype = 'photo';
	protected $mimetype_array = array('photo');
	
	public function __construct($cache_object = null,$name='',$tablePrefix='',$connection=''){
		
		$db_prefix = C('DB_PREFIX');
		$this->dbName = 'db_contacts';
		$this->tableName = $db_prefix.'contacts_photo';
		$this->trueTableName = $this->tableName;
		$this->pk = 'id';
		parent::__construct($cache_object,$name,$tablePrefix,$connection);
	}

	/**
	 * (non-PHPdoc)
	 * @see \Home\Model\ContactsInterfaceModel::contacts_list2current_list_relation()
	 */
	public function contacts_list2current_list_relation(){
		
		$conversion_relation = array();
		$conversion_relation['server_other_id'] = 'id';
		$conversion_relation['server_id'] = 'base_id';
		$conversion_relation['data_14'] = 'client_photo_path';
		$conversion_relation['data_15'] = 'photo_path';		
		return $conversion_relation;
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
				$server_other_id_array = array();
				$id_name = $this->id_name;
				$mimetype = $this->mimetype;
				$str_where = 'user_id = '.$user_id;
				$result = $this->field('id,base_id')->where($str_where)->select();
				if(check_array_valid($result)){
	
					foreach($result as $key=>$value){
							
						$server_other_id_array[intval($value['base_id'])] = intval($value['id']);
					}
				}
	
				$tmp_row_source = array();
				$tmp_row_source['sign_type'] = $sign_type;
				$tmp_row_source['mime_type'] = $mimetype;
				foreach($add_list as $key=>$value){
	
					$tmp_row = $tmp_row_source;
					$data_id = isset($value['data_id']) ? intval($value['data_id']) : 0;
					$contacts_id = isset($value['contacts_id']) ? intval($value['contacts_id']) : 0;
					$tmp_row['data_id'] = $data_id;
					$tmp_row['contacts_id'] = $contacts_id;
					$server_id = isset($value['base_id']) ? intval($value['base_id']) : 0;
					$tmp_row['server_id'] = $server_id;
					$tmp_row['server_other_id'] = 0;
					if($data_id <= 0 || $contacts_id <= 0 || $server_id <= 0){
	
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
						
					$id = 0;
					if(isset($server_other_id_array[$server_id])){
	
						$id = $server_other_id_array[$server_id];
						$value['id'] = $id;
					}
					$md5_str = '';
					if($this->check_md5_str){
							
						$md5_str = self::make_md5_str($value,$conversion_relation);
						$value['md5_str'] = $md5_str;
					}
					if($id > 0){
	
						$sign_type = 'upd';
						$str_where = 'id = '.$id;
						$this->info_update($str_where,$value);
					}else{
	
						$sign_type = 'add';
						$id = $this->info_add($value);
						if($id > 0){
	
							$server_other_id_array[$server_id] = $id;
						}
					}
	
					if($id > 0){
	
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
}
?>