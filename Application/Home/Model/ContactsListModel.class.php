<?php
namespace Home\Model;
use Think\Model;

class ContactsListModel extends \Home\Model\DefaultModel {
	
	protected $cache_prefix = 'contactslist';
	protected $field_array = array();
	protected $ignore_mimetype_prefix = 'vnd.android.cursor.item/';
	protected $contacts_base_info_table = '';
	protected $contacts_version_relation_table = '';
	
	public function __construct($cache_object = null,$name='',$tablePrefix='',$connection=''){
		
		$db_prefix = C('DB_PREFIX');
		$this->dbName = 'db_contacts';
		$this->tableName = $db_prefix.'contacts_list';
		$this->trueTableName = $this->tableName;
		$this->pk = 'id';
		parent::__construct($cache_object,$name,$tablePrefix,$connection);
		$db_prefix_common = $this->dbName.'.'.$db_prefix;
		$this->contacts_base_info_table = $db_prefix_common.'contacts_base_info';
		$this->contacts_version_relation_table = $db_prefix_common.'contacts_version_relation';
	}
	
	public function __destruct(){
		
		parent::__destruct();
		$this->field_array = array();
		$this->ignore_mimetype_prefix = '';
		$this->contacts_base_info_table = '';
		$this->contacts_version_relation_table = '';
	}
	
	/**
	 * 获取数据库字段
	 * @return Ambigous <unknown, multitype:>|Ambigous <multitype:, unknown>
	 */
	public function get_field_array(){
		
		$field_array = $this->field_array;
		if(!check_array_valid($field_array)){
			
			$check_cache = false;
			$cache_object = $this->cache_object;
			if($cache_object != null && is_object($cache_object)){
			
				$check_cache = true;
				$cache_name = $this->cache_prefix.'-get_field_array';
				$cache_value = $cache_object->get($cache_name);
				if($cache_value){
						
					$result = @unserialize(base64_decode(urldecode($cache_value)));
					if(check_array_valid($result)){
							
						$field_array = $result;
						$this->field_array = $field_array;						
						return $field_array;
					}
				}
			}

			$sql = 'SHOW COLUMNS FROM '.$this->dbName.'.'.$this->trueTableName;
			$result = $this->query($sql);
			if(check_array_valid($result)){
				
				foreach($result as $value){
					
					$field_array[] = $value['Field'];
				}				
				if($check_cache){
				
					$cache_value = @urlencode(base64_encode(serialize($field_array)));
					$cache_object->set($cache_name,$cache_value);
				}
			}
		}
				
		return $field_array;
	}
	
	/**
	 * 判别类型合法性并返回类型
	 * @param unknown $mime_type
	 * @return string
	 */
	public static function valid_mime_type($mime_type){
		
		$mime_type = trim($mime_type);
		if(!empty($mime_type)){
				
			$mime_type = strtolower($mime_type);
			if(strpos($mime_type,'email') !== false){
		
				$mime_type = 'email';
			}elseif(strpos($mime_type,'phone') !== false){
		
				$mime_type = 'phone';
			}elseif(strpos($mime_type,'sip') !== false){
		
				$mime_type = 'sip';
			}elseif(strpos($mime_type,'ims') !== false){
		
				$mime_type = 'ims';
			}elseif(strpos($mime_type,'photo') !== false){
		
				$mime_type = 'photo';
			}elseif(strpos($mime_type,'nickname') !== false){
		
				$mime_type = 'nickname';
			}elseif(strpos($mime_type,'organization') !== false){
					
				$mime_type = 'organization';
			}elseif(strpos($mime_type,'postal') !== false){
					
				$mime_type = 'postal';
			}elseif(strpos($mime_type,'name') !== false){
					
				$mime_type = 'name';
			}elseif(strpos($mime_type,'identify') !== false || strpos($mime_type,'relation') !== false){
					
				$mime_type = 'relation';
			}elseif(strpos($mime_type,'group') !== false){
					
				$mime_type = 'group';
			}elseif(strpos($mime_type,'website') !== false){
					
				$mime_type = 'website';
			}elseif(strpos($mime_type,'note') !== false){
					
				$mime_type = 'note';
			}elseif(strpos($mime_type,'im') !== false){
					
				$mime_type = 'im';
			}else{
		
				$mime_type = '';
			}
		}
		
		return $mime_type;
	}
	
	/**
	 *  添加列表
	 * @param \Home\Model\ContactsVersionRelationModel $version_relation_object
	 * @param unknown $user_id
	 * @param unknown $add_list
	 * @param unknown $failed_list
	 * @return Ambigous <multitype:, unknown>
	 */
	public function add_list_process(\Home\Model\ContactsVersionRelationModel $version_relation_object,$user_id,$add_list,$failed_list){
				
		$user_id = intval($user_id);		
		$add_list = is_array($add_list) ? $add_list : array();
		$failed_list = is_array($failed_list) ? $failed_list : array();		
		$ignore_mimetype_prefix = $this->ignore_mimetype_prefix;
		$sign_type_array = \Home\Model\ContactsVersionRelationModel::sign_type_array();

		if($user_id > 0 && check_array_valid($add_list)){
					
			$tmp_array = array();
			$tmp_array['contacts_id'] = 0;
			$tmp_array['data_id'] = 0;
			$tmp_array['server_id'] = 0;
			$tmp_array['server_other_id'] = 0;
			$tmp_array['mime_type'] = '';
			$tmp_array['sign_type'] = '';			
			$field_array = $this->get_field_array();
			$this->clean_data($user_id);
			foreach($add_list as $value){
				
				if(is_object($value)){
					
					$value = (array) $value;
				}	
				
				$data_id = isset($value['data_id']) ? intval($value['data_id']) : 0; 
				$contacts_id = isset($value['contacts_id']) ? intval($value['contacts_id']) : 0;
				$mime_type = isset($value['mime_type']) ? str_replace($ignore_mimetype_prefix,'',$value['mime_type']) : '';
				$mime_type = self::valid_mime_type($mime_type);
				$server_id = isset($value['server_id']) ? intval($value['server_id']) : 0;
				$server_other_id = isset($value['server_other_id']) ? intval($value['server_other_id']) : 0;
								
				if($data_id < 0 || $server_id < 0 || $server_other_id < 0 || ($server_id == 0 && $server_other_id > 0) || 
				  ($server_id > 0 && $server_other_id == 0 && $mime_type !='name' && $value['sign_type'] != 'add') || $contacts_id < 0 || empty($mime_type) || 
				  !isset($value['sign_type']) || !in_array($value['sign_type'],$sign_type_array) || ($server_id > 0 && $server_other_id > 0
				   && $value['sign_type'] == 'add') || ($data_id == 0 && in_array($value['sign_type'],array('add','upd','del'))) || ($contacts_id == 0 && in_array($value['sign_type'],array('add','upd','del')))){
					
					$value['mime_type'] = $mime_type;
					$tmp_arr = $tmp_array;
					foreach($tmp_array as $tmp_key=>$tmp_value){
						
						if(isset($value[$tmp_key])){
								
							$tmp_arr[$tmp_key] = $value[$tmp_key];
						}
					}
					
					$tmp_arr['failed_type'] = INVALID_DATA;
					$failed_list[] = $tmp_arr;
					continue;
				}
								
				$info_array = array();
				$value['user_id'] = $user_id;
				$value['status'] = 0;
				$value['md5_str'] = '';
				$value['is_delete'] = isset($value['is_delete']) && intval($value['is_delete']) == 1 ? 1 : 0;
				$value['data_id'] = $data_id;
				$value['contacts_id'] = $contacts_id;
				$value['mime_type'] = $mime_type;
				$value['server_id'] = $server_id;
				$value['server_other_id'] = $server_other_id;
				foreach($field_array as $field_value){
					
					if(isset($value[$field_value])){
						
						$v = $value[$field_value];
						$v = $v == null || $v == 'null' ? '' : $v;
						$info_array[$field_value] = $v;
					}				
				}
				$this->info_add($info_array);
			}
			
			$table = $this->dbName.'.'.$this->trueTableName;
			$contacts_base_info_table = $this->contacts_base_info_table;
			
			//status 0 为非法，1为正常，2为删除失败  3为完成删除
			$sql = 'update '.$table.' set status = 1 where user_id = '.$user_id.' and server_id >= 0 and server_other_id = 0 and sign_type = "add" ';
			$this->execute($sql);
			$sql = 'update '.$table.' as cl,'.$contacts_base_info_table.' as cb set cl.status = 1 where cl.user_id = '.$user_id.' and 
					cl.server_id > 0 and cl.server_id = cb.id and cl.user_id = cb.user_id ';
			$this->execute($sql);
			$sql = 'update '.$table.' set status = 0 where data_1 = "" and mime_type != "photo" ';
			$this->execute($sql);
			$sql = 'update '.$table.' set status = 2  where user_id = '.$user_id.' and server_id > 0 and server_other_id > 0 and status = 1 
					and (sign_type in ("del","recover")) and (server_id in (select server_id from '.$table.' where user_id = '.$user_id.' and
				    server_id > 0 and server_other_id = 0 and status = 1 )) ';
			$this->execute($sql);		
		}	
		
		return $failed_list;
	}
	
	/**
	 * 删除最后处理
	 * @param unknown $user_id
	 * @param unknown $delete_success_list
	 * @param unknown $failed_list
	 * @return multitype:
	 */
	public function last_process($user_id,$version_auto_id,$delete_success_list,$recover_success_list,$failed_list){
		
		$user_id = intval($user_id);
		$version_auto_id = intval($version_auto_id);
		$failed_list = is_array($failed_list) ? $failed_list : array();
		$delete_success_list = is_array($delete_success_list) ? $delete_success_list : array();
		$return_array = array('failed_list'=>$failed_list,'delete_success_list'=>$delete_success_list,'recover_success_list'=>$recover_success_list);
		if($user_id > 0 && $version_auto_id > 0){
			
			//status 0 为非法，1为正常，2为删除失败  3为完成删除
			$table = $this->dbName.'.'.$this->trueTableName;
			$contacts_version_relation_table = $this->contacts_version_relation_table;
			
			$sql = 'update '.$table.' set status = 0 where server_id = 0 and  status = 1 and sign_type = "add"';
			$this->execute($sql);
			$sql = 'update '.$table.' cl,'.$contacts_version_relation_table.' as cv set cl.status = 3,cl.version_id = cl.version_id where cv.contacts_version_id = '.$version_auto_id.' and cl.status = 2 and 
					cv.server_id = cl.server_id and cv.server_other_id = cl.server_other_id and cv.mime_type = cl.mime_type ';
			
			$this->execute($sql);
						
			$where_common = ' user_id = '.$user_id;
			$field_str = 'contacts_id,data_id,server_id,server_other_id,mime_type,sign_type';			
			$where = $where_common.' and status = 3 ';
			$result = $this->getList('data_id','asc','',$where,'',$field_str);
			if(check_array_valid($result)){
					
				foreach($result as $value){
					
					$sign_type = $value['sign_type'];
					if($sign_type == 'del'){
						
						$delete_success_list[] = $value;
					}else{
						
						$recover_success_list[] = $value;
					}
				}
			}
			
			$where = $where_common.' and status in (0,2) ';			
			$result = $this->getList('data_id','asc','',$where,'',$field_str);
			if(check_array_valid($result)){
			
				foreach($result as $value){
						
					$value['failed_type'] = INVALID_DATA;
					$failed_list[] = $value;
				}
			}
			$return_array = array('failed_list'=>$failed_list,'delete_success_list'=>$delete_success_list,'recover_success_list'=>$recover_success_list);
		}		
		return $return_array;
	}
	
	/**
	 * 获取某个用户提交上来的列表
	 * @param unknown $user_id
	 * @param string $extend_str_where
	 * @return Ambigous <multitype:, \Home\Model\<multitype:,, unknown>
	 */
	public function get_contacts_list($user_id,$extend_str_where = ''){
		
		$contacts_list = array();
		$user_id = intval($user_id);
		if($user_id > 0){
			
			$field_str = 'contacts_id,data_id,server_id,server_other_id,mime_type,sign_type,data_1,
					      data_2,data_3,data_4,data_5,data_6,data_7,data_8,data_9,data_10,data_11,data_12,data_13,data_14,data_15';
			$str_where = ' user_id = '.$user_id.' and status = 1 '.$extend_str_where;
			$result = $this->getList('data_id','asc','',$str_where,'',$field_str);
			if(check_array_valid($result)){
			
				$contacts_list = $result;
			}
		}		
		return $contacts_list;
	}
	
	/**
	 * 清除数据
	 * @param unknown $user_id
	 * @return boolean
	 */
	public function clean_data($user_id){
		
		$check = false;
		$user_id = intval($user_id);
		if($user_id > 0){
			
			$str_where = 'user_id = '.$user_id;			
			$result = $this->where($str_where)->delete();
			$check = $result === false ? false : true;
		}
		return $check;
	}
}
?>