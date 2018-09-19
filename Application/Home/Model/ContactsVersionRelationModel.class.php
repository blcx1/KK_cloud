<?php

namespace Home\Model;
use Think\Model;

class ContactsVersionRelationModel extends \Home\Model\DefaultModel {
	
	protected $cache_prefix = 'contactsversionrelation';
	protected $mime_type_table = '';
	protected $mime_type_array = array();
	
	public function __construct($cache_object = null,$name='',$tablePrefix='',$connection=''){
		
		$db_prefix = C('DB_PREFIX');
		$this->dbName = 'db_contacts';
		$this->tableName = $db_prefix.'contacts_version_relation';
		$this->trueTableName = $this->tableName;
		$this->pk = 'id';
		parent::__construct($cache_object,$name,$tablePrefix,$connection);
		$this->mime_type_table = $this->dbName.'.'.$db_prefix.'mimetype';
	}
	
	/**
	 * 获取sign_type类型组成的数组
	 */
	public static function sign_type_array(){
		
		$sign_type_array = array('add','upd','del','recover','forcedel');
		return $sign_type_array;
	}
	
	/**
	 * 获取mime_type 组成的类型列表
	 * @return multitype:
	 */
	public function get_mime_type_array(){
		
		$mime_type_array = array();
		if(!check_array_valid($this->mime_type_array)){
			
			$check_cache = false;
			$cache_object = $this->cache_object;
			if($cache_object != null && is_object($cache_object)){
			
				$check_cache = true;
				$cache_name = $this->cache_prefix.'-get_mime_type_array';
				$cache_value = $cache_object->get($cache_name);
				if($cache_value){
						
					$result = @unserialize(base64_decode(urldecode($cache_value)));
					if(check_array_valid($result)){
							
						$mime_type_array = $result;
						$this->mime_type_array = $mime_type_array;						
						return $mime_type_array;
					}
				}
			}		
			$sql = 'select mimetype from '.$this->mime_type_table.' where is_delete = 0 order by id asc ';			
			$result = $this->query($sql);
			if(check_array_valid($result)){
				
				foreach($result as $key=>$value){
					
					$mime_type_array[] = $value['mimetype'];
				}
				if($check_cache){
						
					$cache_value = @urlencode(base64_encode(serialize($mime_type_array)));
					$cache_object->set($cache_name,$cache_value);
				}
			}
			
		}else{
			
			$mime_type_array = $this->mime_type_array;
		}
		return $mime_type_array;
	}

	/**
	 * 关联添加
	 * @param unknown $info_array
	 * @return Ambigous <boolean, \Home\Model\Ambigous, \Think\mixed, string, unknown>
	 */
	public function relation_add($info_array = array()){
		
		$check = false;		
		if(isset($info_array['contacts_version_id']) && isset($info_array['server_id']) && isset($info_array['server_other_id']) && 
		   isset($info_array['mime_type']) && isset($info_array['sign_type']) && isset($info_array['version_id'])){
			
			$sign_type_array = self::sign_type_array();
			$mime_type_array = $this->get_mime_type_array();
			
			if(intval($info_array['contacts_version_id']) > 0 && intval($info_array['server_id']) > 0 && intval($info_array['server_other_id']) >= 0
			   && in_array($info_array['mime_type'],$mime_type_array) && in_array($info_array['sign_type'],$sign_type_array) && intval($info_array['version_id']) > 0){
				
				$check = $this->info_add($info_array);
				
				if($check){
					
					$version_id = $info_array['version_id'];
					$sign_type = $info_array['sign_type'];
					
					$delete_array = $info_array;
					unset($delete_array['sign_type']);
					unset($delete_array['version_id']);
					$delete_array['version_id'] = array('lt',$version_id);
					if($sign_type != 'add'){				
												
						if($sign_type == 'recover'){
								
							$where = $delete_array;
							$where['sign_type'] = 'del';
							$result = $this->field('version_id')->where($where)->find();
							if(check_array_valid($result)){
								
								$delete_array['version_id'] = array('lt',$result['version_id']);
							}									
						}
						$delete_array['sign_type'] = array('in',array('upd','del','recover'));
 					}
 					$this->info_force_delete($delete_array); 					
				}				
			}			
		}		
		return $check;
	}
	
	/**
	 * 释放
	 **/
	public function __destruct(){
	
		parent::__destruct();
		$this->mime_type_table = '';
	    $this->mime_type_array = array();	    
	}
	
	/**
	 * 获取不同步的数据
	 * @param unknown $user_id
	 * @param unknown $model_object_array
	 * @param unknown $version_auto_id
	 * @param unknown $lastest_version_id
	 * @param unknown $version_id
	 */
	public function client_version_sync($user_id,$model_object_array,$version_auto_id,$lastest_version_id,$version_id){
		
		
		$user_id = intval($user_id);
		$version_id = intval($version_id);
		$version_auto_id = intval($version_auto_id);
		$lastest_version_id = intval($lastest_version_id);
		$sync_list = array('lastest_version_id'=>$lastest_version_id,'list'=>array());
		if ($user_id <= 0 || $version_auto_id <= 0 || $version_id < 0 || $lastest_version_id < $version_id || !check_array_valid($model_object_array)){
		
			$this->error_array['result'] = '非法数据';
			$this->error_code_array['result'] = INVALID_DATA;
			
			return $sync_list;
		}
		if($lastest_version_id == $version_id){
			
			$this->error_array['result'] = '已经是最新版本';
			$this->error_code_array['result'] = SYNC_ALREADY_VERSION;
			return $sync_list;
		}
				
		$use_mimytype_array = array();
		$str_where = ' contacts_version_id = '.$version_auto_id.' and version_id >'.$version_id.' and version_id <='.$lastest_version_id;		
		$result = $this->field('distinct mime_type')->where($str_where)->select();
		if(check_array_valid($result)){
			
			foreach($result as $value){
				
				$use_mimytype_array[] = $value['mime_type'];
			}
			
			$field_str = '';
			$group_by = '';
			$order_way = 'asc';
			$order_by = 'cvr.version_id';
			$join_str_common = ' as cvr left join ';
			$str_where_common_relation = ' cvr.contacts_version_id = '.$version_auto_id.' and cvr.version_id >'.$version_id.' and 
					                       cvr.version_id <='.$lastest_version_id;		
			
			foreach($model_object_array as $mime_type=>$model_object){
					
				if(!is_object($model_object)){
					
					break;
				}
				if(!in_array($mime_type,$use_mimytype_array)){
					
					continue;
				}
				
				$id_name = $model_object->get_value('id_name');				
				$table = $model_object->get_value('dbName').'.'.$model_object->get_value('trueTableName');
				$join_str = $join_str_common.' '.$table.' as m on m.id = cvr.'.$id_name.' ';
				$str_where = $str_where_common_relation.' and cvr.mime_type ="'.$mime_type.'" ';				
				$result = $this->getList($order_by,$order_way,$group_by,$str_where,$join_str,$field_str);
				
				if(check_array_valid($result)){
					
					if($mime_type == 'photo'){
						
						$tmp_arr = array();
						foreach($result as $key=>$value){
							
							$value['photo_path'] = getFullUrl($value['photo_path']);
							$tmp_arr[$key] = $value;
						}
						$result = $tmp_arr;
					}
					
					$result = $model_object->current_list_conversion_contacts_list($result,true);
					foreach($result as $value){
						
						$sync_list['list'][] = $value;
					}
				}				
			}
		}		
		return $sync_list;
	}
}
?>