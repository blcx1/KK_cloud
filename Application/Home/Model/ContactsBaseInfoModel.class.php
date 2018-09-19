<?php
namespace Home\Model;
use Think\Model;

class ContactsBaseInfoModel extends \Home\Model\ContactsBaseModel{
	
	protected $version_relation_table = '';
	protected $id_name='server_id';
	protected $mimetype = 'name';//对应手机mimetype的缩写
	protected $mimetype_array = array('name');//对应手机mimetype的缩写组成的数组
	protected $is_change = false;
	protected $check_is_delete = true;
	protected $check_add_time = true;
	protected $check_update_time = true;
	protected $check_md5_str = true;
	protected $user_id = 0;//用户id
	protected $version_auto_id = 0;//版本自动id
	protected $lastest_version_id = 0;//最后版本id
	protected $source_version_id = 0;//原来版本id	
	protected $version_object = null;//版本对象
	protected $version_relation_object = null;//版本关联对象
	protected $model_object_array = array();//		
	protected $add_success_total_count = 0;//添加成功个数
	protected $update_total_count = 0;//更新个数
	protected $delete_total_count = 0;//删除个数
	protected $recover_total_count = 0;//覆盖个数
	protected $change_check = false;//修改状态
	protected $add_success_list = array();//添加成功的列表
	protected $update_sucess_list = array();//更新成功列表
	protected $delete_success_list = array();//删除成功列表
	protected $recover_success_list = array();//恢复成功列表
	protected $failed_list = array();//失败列表    
	protected $contacts_list_object = null;//跟客户端相似的联系列表对象
	protected $check_update_version_id = true;//判断是否更新版本ID
	protected $version_add_time = 0;//服务器版本添加时间
	
	
	/**
	 * 初始化
	 * @param unknown $user_id
	 * @param string $is_change
	 * @param string $cache_object
	 * @param string $name
	 * @param string $tablePrefix
	 * @param string $connection
	 */
	public function __construct($user_id,$is_change = false,$cache_object = null,$name='',$tablePrefix='',$connection=''){

		$db_prefix = C('DB_PREFIX');
		$this->dbName = 'db_contacts';
		$this->tableName = $db_prefix.'contacts_base_info';
		$this->trueTableName = $this->tableName;
		$this->pk = 'id';
		parent::__construct($cache_object,$name,$tablePrefix,$connection);
		$this->user_id = intval($user_id);	
		$this->is_change = boolval($is_change);
		$this->version_relation_table = $this->dbName.'.'.$db_prefix.'contacts_version_relation';
		$this->init();		
	}	
	
	/**
	 * (non-PHPdoc)
	 * @see \Home\Model\DefaultModel::init()
	 */
	protected function init(){
		
		parent::init();
				
		$object_array = array();
		$object_array['nickname'] = new \Home\Model\ContactsNickNameModel($this->cache_object);	
		$object_array['phone'] = new \Home\Model\ContactsPhoneModel($this->cache_object);
		$object_array['email'] = new \Home\Model\ContactsEmailModel($this->cache_object);
		$object_array['im'] = new \Home\Model\ContactsChatAccountModel($this->cache_object);
		$object_array['postal'] = new \Home\Model\ContactsPostalModel($this->cache_object);
		$object_array['photo'] = new \Home\Model\ContactsPhotoModel($this->cache_object);		
		$object_array['organization'] = new \Home\Model\ContactsCompanyModel($this->cache_object);		
		$object_array['group'] = new \Home\Model\ContactsGroupRamModel($this->cache_object);
		$object_array['note'] = new \Home\Model\ContactsNoteModel($this->cache_object);
		$object_array['website'] = new \Home\Model\ContactsWebsiteModel($this->cache_object);
		$object_array['relation'] = new \Home\Model\ContactsRelationModel($this->cache_object);
		$object_array['event'] = new \Home\Model\ContactsEventModel($this->cache_object);
		$object_array['sip'] = new \Home\Model\ContactsSipModel($this->cache_object);
		
		$this->model_object_array = $object_array;
		$this->version_object = new \Home\Model\ContactsVersionModel($this->cache_object);
		$this->version_relation_object = new \Home\Model\ContactsVersionRelationModel($this->cache_object);
		
		$version_info = $this->version_object->get_version_info($this->user_id);
		$this->version_auto_id = intval($version_info['id']);
		$this->lastest_version_id = intval($version_info['version_id']);
		$this->version_add_time = $version_info['add_time'];
		if($this->is_change){						
			
			$this->add_success_total_count = 0;
			$this->update_total_count = 0;
			$this->delete_total_count = 0;
			$this->recover_total_count = 0;
						
			$this->change_check = false;
			$this->failed_list = array();			
			$this->add_success_list = array();
			$this->update_sucess_list = array();
			$this->delete_success_list = array();
			$this->recover_success_list = array();
			$this->check_update_version_id = true;
			$this->contacts_list_object = new \Home\Model\ContactsListModel($this->cache_object);
		}
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \Home\Model\ContactsInterfaceModel::contacts_list2current_list_relation()
	 */
	public function contacts_list2current_list_relation(){
		
		$conversion_relation = array();	
		$conversion_relation['server_id'] = 'id';		
		$conversion_relation['data_1'] = 'display_name';
		$conversion_relation['data_2'] = 'given_name';
		$conversion_relation['data_3'] = 'family_name';
		$conversion_relation['data_4'] = 'prefix';
		$conversion_relation['data_5'] = 'middle_name';
		$conversion_relation['data_6'] = 'suffix';
		$conversion_relation['data_7'] = 'phonetic_given_name';
		$conversion_relation['data_8'] = 'phonetic_middle_name';
		$conversion_relation['data_9'] = 'phonetic_family_name';
		$conversion_relation['md5_str'] = 'md5_str';
		return $conversion_relation;
	}
	
	/**
	 * 释放
	 **/
	public function __destruct(){
	
		parent::__destruct();
				
		$this->source_version_id = 0;
		$this->version_object = null;
		$this->version_relation_object = null;
		$this->model_object_array = array();				
		if($this->is_change){
			
			$this->add_success_total_count = 0;
			$this->update_total_count = 0;
			$this->delete_total_count = 0;
			$this->recover_total_count = 0;
			
			$this->change_check = false;
			$this->failed_list = array();
			$this->add_success_list = array();
			$this->update_sucess_list = array();
			$this->delete_success_list = array();
			$this->recover_success_list = array();
			$this->contacts_list_object = null;
			$this->check_update_version_id = false;
		}	
			
		$this->is_change = false;
		$this->version_add_time = 0;
	}
	
	/**
	 * 客户端获取列表
	 * @param unknown $is_recy
	 * @param unknown $page_no
	 * @param unknown $page_size
	 */
	public function client_get_list($is_recy,$page_no,$page_size){
		
		$user_id = intval($this->user_id);
		$lastest_version_id = $this->lastest_version_id;
		$list_array = array('lastest_version_id'=>$lastest_version_id,'version_add_time'=>$this->version_add_time,'total_count'=>0,'list'=>array());		
		if ($user_id <= 0){
		
			$this->error_array['result'] = '非法数据';
			$this->error_code_array['result'] = INVALID_DATA;
			return $list_array;
		}elseif($lastest_version_id <= 0){
			
			return $list_array;
		}
		
		$is_recy = intval($is_recy) == 1 ? 1 : 0;
		$group_by = '';
		$join_str = '';
		$field_str = '';				
		$where = array();
		$where['is_delete'] = $is_recy;
		$where['user_id'] = $user_id;		
		$total_count = $this->get_list_count($group_by,$where,$join_str,$field_str);
		if ($total_count > 0){
						
			$order_by = 'id';
			$order_way = 'desc';
			$field_str = 'id as server_id,display_name,given_name,family_name,prefix,middle_name,suffix,phonetic_given_name,
					      phonetic_middle_name,phonetic_family_name,version_id,md5_str';
			$list = array();
			$result = $this->get_page_list($page_no,$page_size,$order_by,$order_way,$group_by,$where,$join_str,$field_str);
		    if(check_array_valid($result)){		    	
		    	
		    	$id_array = array();
		    	$list_array['total_count'] = $total_count;
		    	foreach($result as $key=>$value){
		    				    		
		    		$list[$key] = $value;
		    		$id_array[] = $value['server_id'];   		
		    	}

		    	$field_str = '';
		    	$where = array();
		    	$list_name_array = array();
		    	$where['user_id'] = $user_id;
		    	$where['base_id'] = array('in',implode(',', $id_array));
		    	$model_object_array = $this->model_object_array;		    	
		    	foreach($model_object_array as $key=>$model_object){
		    		
		    		$list_name = $key.'_list';
		    		$list_name_array[] = $list_name;
		    		$$list_name = $this->get_other_list($model_object,$where,$field_str,array(),1);
		    	}
		    		    	
		    	foreach($list as $key=>$value){
		    		
		    		$server_id = $value['server_id'];
		    		$list_array['list'][$key] = $value;		    		
		    		foreach($list_name_array as $list_name){
		    			
		    			$list_name_arr = $$list_name;		    			
		    			$list_array['list'][$key][$list_name] = isset($list_name_arr[$server_id]) ? $list_name_arr[$server_id] : array();
		    		}		    			    		
		    	}
		    }			
		}
		
		return $list_array;
	}
	
	/**
	 * 手机模式客户端获取列表
	 * @param unknown $is_recy
	 * @param unknown $page_no
	 * @param unknown $page_size
	 * @return multitype:number multitype: |Ambigous <multitype:, unknown, multitype:number multitype: , string, Ambigous, \AwsVendor\Ambigous, mixed, multitype:Ambigous <\Home\Model\Ambigous, multitype:, unknown> >
	 */
	public function client_special_get_list($is_recy,$page_no,$page_size){
		
		$user_id = intval($this->user_id);
		$lastest_version_id = $this->lastest_version_id;
		$list_array = array('lastest_version_id'=>$lastest_version_id,'version_add_time'=>$this->version_add_time,'total_count'=>0,'list'=>array());		
		if ($user_id <= 0){
		
			$this->error_array['result'] = '非法数据';
			$this->error_code_array['result'] = INVALID_DATA;
			return $list_array;
		}elseif($lastest_version_id <= 0){
			
			return $list_array;
		}
		
		$is_recy = intval($is_recy) == 1 ? 1 : 0;
		$group_by = '';
		$join_str = '';
		$field_str = '';				
		$where = array();
		$where['is_delete'] = $is_recy;
		$where['user_id'] = $user_id;		
		$total_count = $this->get_list_count($group_by,$where,$join_str,$field_str);
		if ($total_count > 0){
						
			$order_by = 'id';
			$order_way = 'desc';
			$field_str = 'id,display_name,given_name,family_name,prefix,middle_name,suffix,phonetic_given_name,
					      phonetic_middle_name,phonetic_family_name,version_id,md5_str';
			$list = array();
			$result = $this->get_page_list($page_no,$page_size,$order_by,$order_way,$group_by,$where,$join_str,$field_str);
		    if(check_array_valid($result)){		    	
		    	
		    	$id_array = array();
		    	$list_array['total_count'] = $total_count;
		    	foreach($result as $key=>$value){
		    				    		
		    		$id_array[] = $value['id'];		    		
		    	}		    	
		    	$list = $this->current_list_conversion_contacts_list($result);
		    	
		    	$field_str = '';
		    	$where = array();
		    	$where['user_id'] = $user_id;
		    	$where['base_id'] = array('in',implode(',', $id_array));
		    	$model_object_array = $this->model_object_array;		    	
		    	foreach($model_object_array as $key=>$model_object){

		    		$list = $this->get_other_list($model_object,$where,$field_str,$list,0);
		    	}
		    			    		    	
		    	$list_array['list'] = $list;
		    }			
		}
		
		return $list_array;
	}
	
	/**
	 * 获取其他列表展现形式
	 * @param unknown $model_object
	 * @param unknown $where
	 * @param unknown $field_str
	 * @param unknown $list_array
	 * @param number $return_style 0为手机识别模式，1为直观识别模式
	 * @return unknown
	 */
	protected function get_other_list($model_object,$where,$field_str,$list_array = array(),$return_style = 0){
		
		$list_array = is_array($list_array) ? $list_array : array();
		if(is_object($model_object) && method_exists($model_object,'getList')){
			
			$join_str = '';
			$group_by = '';
			$order_by = 'id';
			$order_way = 'desc';			
			$result = $model_object->getList($order_by,$order_way,$group_by,$where,$join_str,$field_str);
			if(check_array_valid($result)){
				
				$return_style = $return_style == 1 ? 1 : 0;
				if($return_style == 1){
					
					foreach($result as $value){
							
						$id = $value['id'];
						$server_id = $value['base_id'];
						unset($value['id']);
						unset($value['base_id']);
						$value['server_id'] = $server_id;
						$value['server_other_id'] = $id;
						if(isset($value['photo_path'])){
							
							$value['photo_path'] = getFullUrl($value['photo_path']);
						}
						$list_array[$server_id][] = $value;
					}
				}else{					
					
					$mimetype = $model_object->get_value('mimetype');
					if($mimetype == 'photo'){
						
						$result_array = array();
						foreach($result as $key=> $value){
						
							if(isset($value['photo_path'])){
						
								$value['photo_path'] = getFullUrl($value['photo_path']);
							}
							$result_array[$key] = $value;
						}
						$result = $result_array;
					}
					
					$contacts_list = $model_object->current_list_conversion_contacts_list($result);
					foreach($contacts_list as $value){					
						
						unset($value['user_id']);
						$list_array[] = $value;
					}					
				}				
			}
		}
		
		return $list_array;
	}
	
	/**
	 * 改变同步处理
	 * @param unknown $change_list
	 * @param unknown $version_add_time
	 * @return multitype:boolean number multitype:
	 */
	public function client_change_list($change_list,$version_add_time){
		
		$user_id = intval($this->user_id);
		$return_list = array('check'=>$this->change_check,'lastest_version_id'=>$this->lastest_version_id,'version_add_time'=>$this->version_add_time,
				             'add_success_total_count'=>$this->add_success_total_count,'add_success_list'=>$this->add_success_list,
				             'update_total_count'=>$this->update_total_count,'update_sucess_list'=>$this->update_sucess_list,'delete_total_count'=>$this->delete_total_count,
				             'delete_success_list'=>$this->delete_success_list,'recover_total_count'=>$this->recover_total_count,
				             'recover_success_list'=>$this->recover_success_list,'failed_list'=>$this->failed_list);				
		if ($user_id <= 0 || !check_array_valid($change_list)){
		
			$this->error_array['result'] = '非法数据';
			$this->error_code_array['result'] = INVALID_DATA;
			return $return_list;
		}elseif($version_add_time != $this->version_add_time){
			
			$this->error_array['result'] = '异常版本';
			$this->error_code_array['result'] = SYNC_EXCEPTION_VERSION;
			return $return_list;
		}
		
		$this->change_check = true;
		$failed_list = array();
		$contacts_list_object = $this->contacts_list_object;
		$this->source_version_id = $this->lastest_version_id;		
		$failed_list = $contacts_list_object->add_list_process($this->version_relation_object,$user_id,$change_list,$failed_list);
				
		$this->failed_list = $failed_list;	
		
		$this->client_add();//添加处理		
		$this->client_update();//更新处理		
		$this->client_delete();//删除处理		
		$this->client_recover();//恢复处理		
		$this->update_time();//更新联系人更新时间		
		$result = $contacts_list_object->last_process($user_id,$this->version_auto_id,$this->delete_success_list,$this->recover_success_list,$this->failed_list);
		
		$this->failed_list = $result['failed_list'];
		$this->delete_success_list = $result['delete_success_list'];
		$this->recover_success_list = $result['recover_success_list'];
		$contacts_list_object->clean_data($user_id);
		$return_list = array('check'=>$this->change_check,'lastest_version_id'=>($this->check_update_version_id ? $this->source_version_id : $this->lastest_version_id),'version_add_time'=>$this->version_add_time,
				             'add_success_total_count'=>$this->add_success_total_count,'add_success_list'=>$this->add_success_list,
				             'update_total_count'=>$this->update_total_count,'update_sucess_list'=>$this->update_sucess_list,'delete_total_count'=>$this->delete_total_count,
				             'delete_success_list'=>$this->delete_success_list,'recover_total_count'=>$this->recover_total_count,
				             'recover_success_list'=>$this->recover_success_list,'failed_list'=>$this->failed_list);
		if($return_list['lastest_version_id'] != $this->source_version_id){
			
			$this->first_spell();//拼音处理
		}
		
		return $return_list;
	}
	
	/**
	 * 添加处理
	 * @return boolean
	 */
	public function client_add(){
		
		$user_id = intval($this->user_id);		
		if ($user_id <= 0){
		
			return false;
		}
		$version_auto_id = $this->version_auto_id;
		$version_object = $this->version_object;
		$model_object_array = $this->model_object_array;
		$version_relation_object = $this->version_relation_object;
		$contacts_list_object = $this->contacts_list_object;
		
		$extend_str_where_common = ' and server_other_id = 0 and sign_type = "add" ';
		$extend_str_where = $extend_str_where_common.' and server_id = 0 and mime_type = "'.$this->mimetype.'" ';
		$add_list = $contacts_list_object->get_contacts_list($user_id,$extend_str_where);
		$result = $this->specical_add($version_object,$version_relation_object,$contacts_list_object,$user_id,$version_auto_id,$this->lastest_version_id,$add_list,$this->add_success_list,$this->failed_list,$this->check_update_version_id,$this->update_sucess_list);
		$this->failed_list = $result['failed_list'];
		$this->lastest_version_id = $result['last_version_id'];
		$this->add_success_list = $result['add_success_list'];
		$this->add_success_total_count = count($result['add_success_list']);
		$this->check_update_version_id = $result['check_update_version_id'];		
		$this->update_sucess_list = $result['update_success_list'];
		$extend_str_where_common = ' and server_id > 0 '.$extend_str_where_common;
		foreach($model_object_array as $mime_type=>$model_object){
		
			$extend_str_where = $extend_str_where_common.' and mime_type = "'.$mime_type.'" ';
			$add_list = $contacts_list_object->get_contacts_list($user_id,$extend_str_where);
			$result = $model_object->specical_add($version_object,$version_relation_object,$contacts_list_object,$user_id,$version_auto_id,$this->lastest_version_id,$add_list,$this->add_success_list,$this->failed_list,$this->check_update_version_id,$this->update_sucess_list);
			$this->failed_list = $result['failed_list'];
			$this->lastest_version_id = $result['last_version_id'];
			$this->add_success_list = $result['add_success_list'];
			$this->check_update_version_id = $result['check_update_version_id'];
			$this->update_sucess_list = $result['update_success_list'];
		}
		return true;		
	}
	
	/**
	 * 更新处理
	 * @return boolean
	 */
	public function client_update(){
		
		$user_id = intval($this->user_id);		
		if ($user_id <= 0){
		
			return false;
		}
		$version_auto_id = $this->version_auto_id;
		$version_object = $this->version_object;
		$model_object_array = $this->model_object_array;
		$version_relation_object = $this->version_relation_object;
		$contacts_list_object = $this->contacts_list_object;
		
		$extend_str_where_common = ' and sign_type = "upd" ';
		$extend_str_where = ' and server_id > 0 and server_other_id = 0 '.$extend_str_where_common;
		$update_list = $contacts_list_object->get_contacts_list($user_id,$extend_str_where);		
		$result = $this->specical_update($version_object,$version_relation_object,$user_id,$version_auto_id,$this->lastest_version_id,$update_list,$this->update_sucess_list,$this->failed_list,$this->check_update_version_id);
		$this->failed_list = $result['failed_list'];
		$this->lastest_version_id = $result['last_version_id'];
		$this->update_sucess_list = $result['update_success_list'];
		$this->check_update_version_id = $result['check_update_version_id'];	
		
		$extend_str_where_common = ' and server_id > 0 and server_other_id > 0 '.$extend_str_where_common;
		foreach($model_object_array as $mime_type=>$model_object){
		
			$extend_str_where = $extend_str_where_common.' and mime_type = "'.$mime_type.'" ';
			$update_list = $contacts_list_object->get_contacts_list($user_id,$extend_str_where);
			$result = $model_object->specical_update($version_object,$version_relation_object,$user_id,$version_auto_id,$this->lastest_version_id,$update_list,$this->update_sucess_list,$this->failed_list,$this->check_update_version_id);
		
			$this->failed_list = $result['failed_list'];
			$this->lastest_version_id = $result['last_version_id'];
			$this->update_sucess_list = $result['update_success_list'];
			$this->check_update_version_id = $result['check_update_version_id'];	
		}
					
		$field_str = 'distinct server_id';
		$str_where ='contacts_version_id = '.$version_auto_id.' and version_id = '.$this->lastest_version_id .' and server_id > 0 
				     and sign_type = "upd" ';
		$this->update_total_count = $version_relation_object->get_list_count('',$str_where,'',$field_str);		
		return true;
	}
	
	/**
	 * 删除处理
	 * @return boolean
	 */
	public function client_delete(){
		
		$user_id = intval($this->user_id);		
		if ($user_id <= 0){
		
			return false;
		}
		$version_auto_id = $this->version_auto_id;
		$version_object = $this->version_object;
		$model_object_array = $this->model_object_array;
		$version_relation_object = $this->version_relation_object;
		
		$contacts_list_object = $this->contacts_list_object;
		$extend_str_where_common = ' and sign_type = "del" ';
		$extend_str_where = ' and server_id > 0 and server_other_id = 0 '.$extend_str_where_common;
		$delete_list = $contacts_list_object->get_contacts_list($user_id,$extend_str_where);
		$result = $this->specical_delete($version_object,$version_relation_object,$user_id,$version_auto_id,$this->lastest_version_id,$delete_list,$this->delete_success_list,$this->failed_list,$this->check_update_version_id);
				
		$this->failed_list = $result['failed_list'];
		$this->lastest_version_id = $result['last_version_id'];
		$this->delete_success_list = $result['delete_success_list'];
		$this->check_update_version_id = $result['check_update_version_id'];
	    $this->delete_total_count = count($this->delete_success_list);	    
	    
	    if($this->delete_total_count > 0){
	    	
	    	$server_id_del_array = array();
	    	$delete_success_list = $result['delete_success_list'];
	    	foreach($delete_success_list as $list_row){
	    		
	    		$server_id_del_array[] = $list_row['server_id'];
	    	}
	    	$del_str_where = ' user_id = '.$user_id.' and status = 1 and server_id > 0 and server_other_id > 0 '.$extend_str_where_common.' and server_id in ('.implode(',',$server_id_del_array).')';

	    	$contacts_list_object->where($del_str_where)->delete();
	    }
	    
		$extend_str_where_common = ' and server_id > 0 and server_other_id > 0 '.$extend_str_where_common;		
		foreach($model_object_array as $mime_type=>$model_object){
				
			$extend_str_where = $extend_str_where_common.' and mime_type = "'.$mime_type.'" ';
			$delete_list = $contacts_list_object->get_contacts_list($user_id,$extend_str_where);
			$result = $model_object->specical_delete($version_object,$version_relation_object,$user_id,$version_auto_id,$this->lastest_version_id,$delete_list,$this->delete_success_list,$this->failed_list,$this->check_update_version_id);
				
			$this->failed_list = $result['failed_list'];
			$this->lastest_version_id = $result['last_version_id'];
			$this->delete_success_list = $result['delete_success_list'];
			$this->check_update_version_id = $result['check_update_version_id'];
		}
		
		return true;
	}
	
	/**
	 * 恢复处理
	 * @return boolean
	 */
	public function client_recover(){
		
		$user_id = intval($this->user_id);		
		if ($user_id <= 0){
		
			return false;
		}
		$version_auto_id = $this->version_auto_id;
		$version_object = $this->version_object;
		$model_object_array = $this->model_object_array;
		$version_relation_object = $this->version_relation_object;
		$contacts_list_object = $this->contacts_list_object;
		
		$extend_str_where_common = ' and sign_type = "recover" ';
		$extend_str_where = ' and server_id > 0 and server_other_id = 0 '.$extend_str_where_common;
		$recover_list = $contacts_list_object->get_contacts_list($user_id,$extend_str_where);		
		$result = $this->special_recover($version_object,$version_relation_object,$user_id,$version_auto_id,$this->lastest_version_id,$recover_list,$this->recover_success_list,$this->failed_list,$this->check_update_version_id);
		
		$this->failed_list = $result['failed_list'];
		$this->lastest_version_id = $result['last_version_id'];
		$this->recover_success_list = $result['recover_success_list'];
		$this->check_update_version_id = $result['check_update_version_id'];
		$this->recover_total_count = count($this->recover_success_list);
		
		return true;
	}
	
	/**
	 * 清空某个用户的手机联系人信息
	 * @return boolean|unknown
	 */
	public function client_clearAll(){
	
		$check = false;
		$version_add_time = $this->version_add_time;
		$lastest_version_id = $this->lastest_version_id;
		$return_array = array('check'=>$check,'lastest_version_id'=>$lastest_version_id,'version_add_time'=>$version_add_time);
		$user_id = intval($this->user_id);
		if ($user_id <= 0){
	
			$this->error_array['result'] = '非法数据';
			$this->error_code_array['result'] = INVALID_DATA;
			return $return_array;
		}
	
		$where = array();
		$where['user_id'] = $user_id;
		$check = $this->info_force_delete($where);
		if ($check){
				
			$version_object = $this->version_object;
			$version_relation_object = $this->version_relation_object;
			$model_object_array = $this->model_object_array;
			foreach($model_object_array as $object_model){
					
				$object_model->info_force_delete($where);
			}
				
			$where = array();
			$where['contacts_version_id'] = $this->version_auto_id;
			$result = $version_relation_object->info_force_delete($where);
			if($result){
	
				$where = array();
				$update_array = array();
				$current_datetime = $this->current_datetime;
	
				$where['id'] = $this->version_auto_id;
				$update_array['version_id'] = 0;
				$update_array['add_time'] = $current_datetime;
				$update_array['update_time'] = $current_datetime;
				$check = $version_object->info_update($where,$update_array);
				if($check){
						
					$lastest_version_id = 0;
					$version_add_time = $this->current_time;
				}
			}
		}else{
				
			$this->error_array['result'] = '删除失败';
			$this->error_code_array['result'] = ERROR_DEL_FAILED;
		}
	
		$return_array = array('check'=>$check,'lastest_version_id'=>$lastest_version_id,'version_add_time'=>$version_add_time);
	
		return $return_array;
	}
	
	/**
	 * 彻底删除某用户数据
	 * @param unknown $server_id_array
	 * @param unknown $version_add_time
	 * @return multitype:unknown boolean number |multitype:unknown number boolean
	 */
	public function client_force_delete($server_id_array,$version_add_time){
		
		$check = false;		
		$lastest_version_id = $this->lastest_version_id;
		$return_array = array('check'=>$check,'lastest_version_id'=>$lastest_version_id,'version_add_time'=>$version_add_time);
		$user_id = intval($this->user_id);
		if($user_id <= 0){	

			$this->error_array['result'] = '非法数据';
			$this->error_code_array['result'] = INVALID_DATA;
			return $return_array;
		}elseif($version_add_time != $this->version_add_time){
				
			$this->error_array['result'] = '异常版本';
			$this->error_code_array['result'] = SYNC_EXCEPTION_VERSION;
			return $return_array;
		}
		
		$server_id_arr = check_array_valid($server_id_array) ? $server_id_array : array();
		$server_id_array = array();
		foreach($server_id_arr as $server_id){
			
			$server_id = intval($server_id);
			if($server_id > 0){
				
				$server_id_array[] = $server_id;
			}
		}
		
		if (!check_array_valid($server_id_array)){
		
			$this->error_array['result'] = '非法数据';
			$this->error_code_array['result'] = INVALID_DATA;
			return $return_array;
		}

		$server_id_str = implode(',',$server_id_array);
		$where = array();
		$where['id'] = array('in',$server_id_str);
		$where['user_id'] = $user_id;
		
		$result = $this->where($where)->getField('id',true);		
		if(!check_array_valid($result)){
			
			$this->error_array['result'] = '非法数据';
			$this->error_code_array['result'] = INVALID_DATA;
			return $return_array;		
		}
		
		$server_id_array = $result;
		$server_id_str = implode(',',$server_id_array);
		$where['id'] = array('in',$server_id_str);
		$check = $this->info_force_delete($where);
		
		if ($check){
						
			$where['base_id'] = $where['id'];
			unset($where['id']);
			$version_object = $this->version_object;
			$version_relation_object = $this->version_relation_object;
			$model_object_array = $this->model_object_array;
			foreach($model_object_array as $object_model){
					
				$object_model->info_force_delete($where);
			}
			
			$where = array();
			$version_auto_id = $this->version_auto_id;
			$where['contacts_version_id'] = $version_auto_id;	
			$where['server_id'] = array('in',$server_id_str);
			$result = $version_relation_object->info_force_delete($where);
			if($result){
								
				$where = array();
				$update_array = array();
								
				$lastest_version_id = $lastest_version_id + 1;
				$where['id'] = $this->version_auto_id;
				$update_array['version_id'] = $lastest_version_id;				
				$update_array['update_time'] = $this->current_datetime;
				$check = $version_object->info_update($where,$update_array);
				if($check){
					
					$mimetype = $this->mimetype;
					$table = $version_relation_object->get_value('dbName').'.'.$version_relation_object->get_value('trueTableName');					
					$sql = 'insert into '.$table.' (contacts_version_id,server_id,server_other_id,mime_type,sign_type,version_id) values ';
					foreach($server_id_array as $key=>$server_id){
							
						$sql .= $key == 0 ? '' : ',';
						$sql .= '('.$version_auto_id.','.$server_id.',0,"'.$mimetype.'","forcedel",'.$lastest_version_id.')';
			
					}
					$sql .= ';';						
					$this->execute($sql);
				}					
			}		
		}else{
			
			$this->error_array['result'] = '删除失败';
			$this->error_code_array['result'] = ERROR_DEL_FAILED;
		}
		
		$return_array = array('check'=>$check,'lastest_version_id'=>$lastest_version_id,'version_add_time'=>$version_add_time);
		
		return $return_array;
	}
	
	/**
	 * 简单恢复某用户数据
	 * @param unknown $server_id_array
	 * @param unknown $version_add_time
	 * @return multitype:unknown boolean number |multitype:unknown number boolean
	 */
	public function client_simple_recover($server_id_array,$version_add_time){
	
		$check = false;
		$lastest_version_id = $this->lastest_version_id;
		$return_array = array('check'=>$check,'lastest_version_id'=>$lastest_version_id,'version_add_time'=>$version_add_time);
		$user_id = intval($this->user_id);
		if($user_id <= 0){
	
			$this->error_array['result'] = '非法数据';
			$this->error_code_array['result'] = INVALID_DATA;
			return $return_array;
		}elseif($version_add_time != $this->version_add_time){
	
			$this->error_array['result'] = '异常版本';
			$this->error_code_array['result'] = SYNC_EXCEPTION_VERSION;
			return $return_array;
		}
	
		$server_id_arr = check_array_valid($server_id_array) ? $server_id_array : array();
		$server_id_array = array();
		foreach($server_id_arr as $server_id){
				
			$server_id = intval($server_id);
			if($server_id > 0){
	
				$server_id_array[] = $server_id;
			}
		}
	
		if (!check_array_valid($server_id_array)){
	
			$this->error_array['result'] = '非法数据';
			$this->error_code_array['result'] = INVALID_DATA;
			return $return_array;
		}
	
		$server_id_str = implode(',',$server_id_array);
		$where = array();
		$where['id'] = array('in',$server_id_str);
		$where['user_id'] = $user_id;
		$where['is_delete'] = 1;
		
		$result = $this->where($where)->getField('id',true);
		if(!check_array_valid($result)){
				
			$this->error_array['result'] = '非法数据';
			$this->error_code_array['result'] = INVALID_DATA;
			return $return_array;
		}
	
		$server_id_array = $result;
		$server_id_str = implode(',',$server_id_array);
		
		$where = array();
		$update_array = array();
		$version_object = $this->version_object;
		$version_auto_id = $this->version_auto_id;
		$lastest_version_id = $lastest_version_id + 1;
		
		$where['id'] = $version_auto_id;
		$update_array['version_id'] = $lastest_version_id;
		$update_array['update_time'] = $this->current_datetime;
		$check = $version_object->info_update($where,$update_array);
		if ($check){
			
			$where = array();			
			$where['id'] = array('in',$server_id_str);
			$where['user_id'] = $user_id;
			$other_update_array = array('version_id'=>$lastest_version_id);
			$check = $this->info_recover($where,$other_update_array);	
			
			if($check){
					
				$version_relation_object = $this->version_relation_object;
				$info_arr = array('contacts_version_id'=>$version_auto_id,'server_other_id'=>0,'mime_type'=>$this->mimetype,
						          'sign_type'=>'recover','version_id'=>$lastest_version_id);
				foreach($server_id_array as $server_id){
					
					$info_array = $info_arr;
					$info_array['server_id'] = $server_id;
					$version_relation_object->relation_add($info_array);
				}			
			}			
		}else{
				
			$lastest_version_id -= 1;
			$this->error_array['result'] = '更新失败';
			$this->error_code_array['result'] = ERROR_UPDATE_FAILED;
		}
	
		$return_array = array('check'=>$check,'lastest_version_id'=>$lastest_version_id,'version_add_time'=>$version_add_time);
	
		return $return_array;
	}
	
	/**
	 * 更新时间
	 */
	public function update_time(){
		
		$version_auto_id = $this->version_auto_id;//版本自动id	
		if($version_auto_id > 0){
			
			$sql = 'update '.$this->dbName.'.'.$this->trueTableName.' set update_time = "'.$this->current_datetime.'" where id in
				(select distinct server_id from '.$this->version_relation_table.' where contacts_version_id = '.$version_auto_id.' and server_other_id > 0 and version_id = '.$this->lastest_version_id.') ';
			
			$this->execute($sql);
		}		
	}
	
	/**
	 * 通过版本校准同步已经改变的列表
	 * @param unknown $version_id
	 * @param unknown $version_add_time
	 * @return multitype:multitype: NULL unknown
	 */
	public function client_version_sync($version_id,$version_add_time){
		
		$model_object_array = $this->model_object_array;
		$model_object_array[$this->mimetype] = $this;		
		$version_relation_object = $this->version_relation_object;
		$version_sync = array('lastest_version_id'=>$this->lastest_version_id,'version_add_time'=>$this->version_add_time,'list'=>array());
		if($version_add_time == $this->version_add_time){
			
			$sync_list = $version_relation_object->client_version_sync($this->user_id,$model_object_array,$this->version_auto_id,$this->lastest_version_id,$version_id);
			if(!check_array_valid($sync_list)){
					
				$this->error_array = $version_relation_object->get_error_array();
				$this->error_code_array = $version_relation_object->get_error_code_array();
			}
			$version_sync['lastest_version_id'] = $sync_list['lastest_version_id'];			
			$version_sync['list'] = $sync_list['list'];
		}else{
			
			$this->error_array['result'] = '异常版本';
			$this->error_code_array['result'] = SYNC_EXCEPTION_VERSION;
		}
			
		return $version_sync;	
	}
	
	/**
	 * 拼音首字母处理	
	 */
	public function first_spell(){
		
		$user_id = intval($this->user_id);
		$version_auto_id = $this->version_auto_id;
		$lastest_version_id = $this->lastest_version_id;
		$version_relation_object = $this->version_relation_object;
		$table = $this->dbName.'.'.$this->trueTableName;
		$version_relation_table = $version_relation_object->get_value('dbName').'.'.$version_relation_object->get_value('trueTableName');
		$sql = 'select cb.id,cb.display_name from '.$table.' as cb,'.$version_relation_table.' as cvr where cb.user_id = '.$user_id.' and
				cvr.server_id = cb.id and cvr.contacts_version_id = "'.$version_auto_id.'" and cvr.version_id = '.$lastest_version_id.
		       ' and cvr.server_other_id = 0 and cvr.mime_type = "'.$this->mimetype.'" and cvr.sign_type in ("add","upd") ';
	
		$result = $this->query($sql);		
		if(check_array_valid($result)){			
			
			$update_array = array();			
			foreach($result as $value){
				
				$first_spell = \Spell\CUtf8_PY::encode(trim($value['display_name']),'head');
				$where = array('id'=>$value['id']);
				$update_array = array('first_spell'=>strtoupper($first_spell));
				$this->info_update($where,$update_array);
			}
		}	
	}
	
	/**
	 * 获取列表
	 * @param number $is_recy
	 * @param number $page_no
	 * @param number $page_size
	 * @param string $extend_where
	 */
	public function get_web_list($is_recy = 0,$page_no = 1,$page_size = 30,$extend_where = ''){
		
		$list_array = array('total_count'=>0,'list'=>array());
		$user_id = intval($this->user_id);
		if($user_id > 0){
			
			$group_by = '';
			$join_str = '';			
			$is_recy = intval($is_recy);
			$is_recy = $is_recy == 1 ? 1 : 0;
			$where = ' user_id = '.$user_id.' and is_delete = '.$is_recy.' '.$extend_where;
			$total_count = $this->get_list_count($group_by,$where,$join_str,'');
			if($total_count > 0){
				
				$order_by = 'first_spell';
				$order_way = 'asc';
				$field_str = 'id,display_name,first_spell';
				$list_array['total_count'] = $total_count;				
				$result = $this->get_page_list($page_no,$page_size,$order_by,$order_way,$group_by,$where,$join_str,$field_str);
				if(check_array_valid($result)){
					
					$list = array();
					$tmp_first_spell_array = array();
					foreach($result as $key=>$value){
						
						$first_spell = trim($value['first_spell']);
						$first_spell = $first_spell == '' ? '#' : strtoupper($first_spell);
						$value['first_spell'] = $first_spell;
						if(!in_array($first_spell,$tmp_first_spell_array)){
							
							$value['display'] = true;
							$tmp_first_spell_array[] = $first_spell;
						}else{
							
							$value['display'] = false;
						}						
						$list[$key] = $value;		
					}
					$list_array['list'] = $list;
				}
			}
		}
		return $list_array;
	}
	
	/**
	 * 获取某个详情信息
	 * @param unknown $id
	 * @return multitype:
	 */
	public function get_detail_info($id){
		
		$detail_info = array();
		$id = intval($id);
		$user_id = intval($this->user_id);
		if($user_id > 0 && $id > 0){
			
			$extend_where = ' and user_id = '.$user_id;
			$field_str = 'id,display_name,given_name,family_name,prefix,middle_name,suffix,phonetic_given_name,
					      phonetic_middle_name,phonetic_family_name';
			$result = $this->get_info($id,$field_str,$extend_where);
			if(check_array_valid($result)){
				
				$detail_info['base'] = $result;
				
				$field_str = '';
				$where = array();				
				$where['user_id'] = $user_id;
				$where['base_id'] = $id;
				$model_object_array = $this->model_object_array;
				foreach($model_object_array as $model_name =>$model_object){				

					 $detail_info[$model_name] = $this->get_web_other_list($model_object,$where);					 			 
				}
			}
			
		}
		return $detail_info;
	}
	
	/**
	 * 网页版获取其他列表
	 * @param unknown $model_object
	 * @param unknown $where
	 * @return multitype:Ambigous <string, Ambigous, \AwsVendor\Ambigous, mixed>
	 */
	public function get_web_other_list($model_object,$where){
		
		$list_array = array();
		if(is_object($model_object) && method_exists($model_object,'getList')){
				
			$join_str = '';
			$group_by = '';
			$order_by = 'id';
			$order_way = 'asc';
			$field_str = '';
			$result = $model_object->getList($order_by,$order_way,$group_by,$where,$join_str,$field_str);
			if(check_array_valid($result)){			
						
				foreach($result as $key=>$value){						
					
					unset($value['user_id']);
					unset($value['base_id']);					
					unset($value['md5_str']);
					unset($value['version_id']);				
					if(isset($value['photo_path'])){
							
						$value['photo_path'] = getFullUrl($value['photo_path']);
					}
					$list_array[$key] = $value;
				}
			}				
		}
		
		return $list_array;
	}
	
	/**
	 * 标注为删除状态
	 * @param unknown $server_id_array
	 * @return boolean
	 */
	public function web_delete($server_id_array){
	
		$check = false;
		$user_id = intval($this->user_id);
		if($user_id <= 0){
	
			$this->error_array['result'] = '非法数据';
			$this->error_code_array['result'] = INVALID_DATA;
			return $check;
		}
	
		$server_id_arr = check_array_valid($server_id_array) ? $server_id_array : array();
		$server_id_array = array();
		foreach($server_id_arr as $server_id){
	
			$server_id = intval($server_id);
			if($server_id > 0){
	
				$server_id_array[] = $server_id;
			}
		}
	
		if (!check_array_valid($server_id_array)){
	
			$this->error_array['result'] = '非法数据';
			$this->error_code_array['result'] = INVALID_DATA;
			return $check;
		}
	
		$server_id_str = implode(',',$server_id_array);
		$where = array();
		$where['id'] = array('in',$server_id_str);
		$where['user_id'] = $user_id;
		$where['is_delete'] = 0;
	
		$result = $this->where($where)->getField('id',true);
		if(!check_array_valid($result)){
	
			$this->error_array['result'] = '非法数据';
			$this->error_code_array['result'] = INVALID_DATA;
			return $check;
		}
	
		$server_id_array = $result;
		$server_id_str = implode(',',$server_id_array);
	
		$where = array();
		$update_array = array();
		$lastest_version_id = $this->lastest_version_id;
		$version_object = $this->version_object;
		$version_auto_id = $this->version_auto_id;
		$lastest_version_id = $lastest_version_id + 1;
	
		$where['id'] = $version_auto_id;
		$update_array['version_id'] = $lastest_version_id;
		$update_array['update_time'] = $this->current_datetime;
		$check = $version_object->info_update($where,$update_array);
		if ($check){
	
			$where = array();
			$where['id'] = array('in',$server_id_str);
			$where['user_id'] = $user_id;
			$other_update_array = array('version_id'=>$lastest_version_id);
			$check = $this->info_del($where,$lastest_version_id);
			
			if($check){
					
				$version_relation_object = $this->version_relation_object;
				$info_arr = array('contacts_version_id'=>$version_auto_id,'server_other_id'=>0,'mime_type'=>$this->mimetype,
						          'sign_type'=>'del','version_id'=>$lastest_version_id);
				foreach($server_id_array as $server_id){
	
					$info_array = $info_arr;
					$info_array['server_id'] = $server_id;
					$version_relation_object->relation_add($info_array);
				}
			}
		}else{
	
			$lastest_version_id -= 1;
			$this->error_array['result'] = '更新失败';
			$this->error_code_array['result'] = ERROR_UPDATE_FAILED;
		}
	
		return $check;	
	}
	
	/**
	 * 恢复某用户数据
	 * @param unknown $server_id_array
	 * @param unknown $version_add_time
	 * @return multitype:unknown boolean number |multitype:unknown number boolean
	 */
	public function web_recover($server_id_array){
	
		$check = false;		
		$user_id = intval($this->user_id);
		if($user_id <= 0){
	
			$this->error_array['result'] = '非法数据';
			$this->error_code_array['result'] = INVALID_DATA;
			return $check;
		}
	
		$server_id_arr = check_array_valid($server_id_array) ? $server_id_array : array();
		$server_id_array = array();
		foreach($server_id_arr as $server_id){
	
			$server_id = intval($server_id);
			if($server_id > 0){
	
				$server_id_array[] = $server_id;
			}
		}
	
		if (!check_array_valid($server_id_array)){
	
			$this->error_array['result'] = '非法数据';
			$this->error_code_array['result'] = INVALID_DATA;
			return $check;
		}
	
		$server_id_str = implode(',',$server_id_array);
		$where = array();
		$where['id'] = array('in',$server_id_str);
		$where['user_id'] = $user_id;
		$where['is_delete'] = 1;
	
		$result = $this->where($where)->getField('id',true);
		if(!check_array_valid($result)){
	
			$this->error_array['result'] = '非法数据';
			$this->error_code_array['result'] = INVALID_DATA;
			return $check;
		}
	
		$server_id_array = $result;
		$server_id_str = implode(',',$server_id_array);
	
		$where = array();
		$update_array = array();
		$lastest_version_id = $this->lastest_version_id;
		$version_object = $this->version_object;
		$version_auto_id = $this->version_auto_id;
		$lastest_version_id = $lastest_version_id + 1;
	
		$where['id'] = $version_auto_id;
		$update_array['version_id'] = $lastest_version_id;
		$update_array['update_time'] = $this->current_datetime;
		$check = $version_object->info_update($where,$update_array);
		if ($check){
				
			$where = array();
			$where['id'] = array('in',$server_id_str);
			$where['user_id'] = $user_id;
			$other_update_array = array('version_id'=>$lastest_version_id);
			$check = $this->info_recover($where,$other_update_array);
				
			if($check){
					
				$version_relation_object = $this->version_relation_object;
				$info_arr = array('contacts_version_id'=>$version_auto_id,'server_other_id'=>0,'mime_type'=>$this->mimetype,
						'sign_type'=>'recover','version_id'=>$lastest_version_id);
				foreach($server_id_array as $server_id){
						
					$info_array = $info_arr;
					$info_array['server_id'] = $server_id;
					$version_relation_object->relation_add($info_array);
				}
			}
		}else{
	
			$lastest_version_id -= 1;
			$this->error_array['result'] = '更新失败';
			$this->error_code_array['result'] = ERROR_UPDATE_FAILED;
		}
	
		return $check;
	}
	
	/**
	 * 彻底删除
	 * @param unknown $server_id_array
	 * @return boolean|unknown
	 */
	public function web_fordel($server_id_array){
	
		$check = false;		
		$user_id = intval($this->user_id);
		if($user_id <= 0){
		
			$this->error_array['result'] = '非法数据';
			$this->error_code_array['result'] = INVALID_DATA;
			return $check;
		}
		
		$server_id_arr = check_array_valid($server_id_array) ? $server_id_array : array();
		$server_id_array = array();
		foreach($server_id_arr as $server_id){
				
			$server_id = intval($server_id);
			if($server_id > 0){
		
				$server_id_array[] = $server_id;
			}
		}
		
		if (!check_array_valid($server_id_array)){
		
			$this->error_array['result'] = '非法数据';
			$this->error_code_array['result'] = INVALID_DATA;
			return $check;
		}
		
		$server_id_str = implode(',',$server_id_array);
		$where = array();
		$where['id'] = array('in',$server_id_str);
		$where['user_id'] = $user_id;
		
		$result = $this->where($where)->getField('id',true);
		if(!check_array_valid($result)){
				
			$this->error_array['result'] = '非法数据';
			$this->error_code_array['result'] = INVALID_DATA;
			return $check;
		}
		
		$server_id_array = $result;
		$server_id_str = implode(',',$server_id_array);
		$where['id'] = array('in',$server_id_str);
		$check = $this->info_force_delete($where);
		
		if ($check){
		
			$where['base_id'] = $where['id'];
			unset($where['id']);
			$version_object = $this->version_object;
			$version_relation_object = $this->version_relation_object;
			$model_object_array = $this->model_object_array;
			foreach($model_object_array as $object_model){
					
				$object_model->info_force_delete($where);
			}
				
			$where = array();
			$lastest_version_id = $this->lastest_version_id;
			$version_auto_id = $this->version_auto_id;
			$where['contacts_version_id'] = $version_auto_id;
			$where['server_id'] = array('in',$server_id_str);
			$result = $version_relation_object->info_force_delete($where);
			if($result){
		
				$where = array();
				$update_array = array();
		
				$lastest_version_id = $lastest_version_id + 1;
				$where['id'] = $this->version_auto_id;
				$update_array['version_id'] = $lastest_version_id;
				$update_array['update_time'] = $this->current_datetime;
				$check = $version_object->info_update($where,$update_array);
				if($check){
						
					$mimetype = $this->mimetype;
					$table = $version_relation_object->get_value('dbName').'.'.$version_relation_object->get_value('trueTableName');
					$sql = 'insert into '.$table.' (contacts_version_id,server_id,server_other_id,mime_type,sign_type,version_id) values ';
					foreach($server_id_array as $key=>$server_id){
							
						$sql .= $key == 0 ? '' : ',';
						$sql .= '('.$version_auto_id.','.$server_id.',0,"'.$mimetype.'","forcedel",'.$lastest_version_id.')';
							
					}
					$sql .= ';';
					$this->execute($sql);
				}
			}
		}else{
				
			$this->error_array['result'] = '删除失败';
			$this->error_code_array['result'] = ERROR_DEL_FAILED;
		}
				
		return $check;
	}	
	
	/**
	 * 获取合并联系人列表
	 * @return multitype:|multitype:multitype:
	 */
	public function get_merge_list(){
		
		$merge_list = array();
		$user_id = intval($this->user_id);
		if($user_id <= 0){
		
			$this->error_array['result'] = '非法数据';
			$this->error_code_array['result'] = INVALID_DATA;
			return $merge_list;
		}
		$merge_list = array('total_count'=>0,'name'=>array(),'phone'=>array(),'email'=>array());
		
		$model_object_array = $this->model_object_array;
		$phone_object = $model_object_array['phone'];
		$mail_object = $model_object_array['email'];
		
		$base_info_table = $this->dbName.'.'.$this->trueTableName;
		$phone_table = $phone_object->get_value('dbName').'.'.$phone_object->get_value('trueTableName');
		$mail_table = $mail_object->get_value('dbName').'.'.$mail_object->get_value('trueTableName');
		
		unset($model_object_array);
		$phone_object = $mail_object = null;
		$extend_in_str = '';
		$server_id_array = array();	
		$display_name_array = array();
		$phone_array = array();
		$email_array = array();
			
		$sql = 'select b.id,b.display_name,p.phone from '.$base_info_table.' as b left join '.$phone_table.' as p on p.base_id = b.id where 
				b.user_id = '.$user_id.' and b.is_delete = 0 and b.display_name in (select display_name from (SELECT display_name , count( * ) AS total_count 
				FROM '.$base_info_table.' where user_id = '.$user_id.' and is_delete = 0 GROUP BY display_name) as m where total_count > 1)';

		$result = $this->query($sql);
		if(check_array_valid($result)){
			
			foreach($result as $value){
				
				$id = $value['id'];
				$display_name = $value['display_name'];
				$md5_name = md5($display_name);
				$rows_array = array('id'=>$id,'display_name'=>$display_name,'phone_array'=>array());
				if(!in_array($id,$server_id_array)){
					
					$server_id_array[] = $id;
					if($value['phone']){
						
						$rows_array['phone_array'][] = $value['phone'];
					}				
				}else{
					
					if($value['phone']){
						
						$phone_tmp_array = $display_name_array[$md5_name][$id]['phone_array'];
						$phone_tmp_array[] = $value['phone'];
						$rows_array['phone_array'] = $phone_tmp_array;
					}					
				}
				$display_name_array[$md5_name][$id] = $rows_array;				
			}

			$extend_in_str = ' and (base_id not in ('.implode(',',$server_id_array).'))';
		}
		
		$sql = 'select base_id,phone from '.$phone_table.' where user_id = '.$user_id.' '.$extend_in_str.' and phone 
				in (select phone from (select phone,count( * ) AS total_count from '.$phone_table.'
				where user_id = '.$user_id.' group by phone) as m where total_count > 1) ';
		
		$result = $this->query($sql);
		if(check_array_valid($result)){			
			
			foreach($result as $value){
				
				$id = $value['base_id'];				
				$phone = $value['phone'];
				$md5_name = md5($phone);
				$rows_array = array('id'=>$id,'display_name'=>'','phone_array'=>array());				
				$phone_array[$md5_name][$id] = $rows_array;
			}
			$phone_array_tmp = $phone_array;
			$phone_server_id_array = array();
			foreach($phone_array_tmp as $key=>$value){
				
				if(count($value) > 1){
					
					foreach($value as $base_id=>$v){
						
						if(!in_array($base_id,$phone_server_id_array)){
							
							$server_id_array[] = $base_id;
							$phone_server_id_array[] = $base_id;
						}						
					}
				}else{
					unset($phone_array[$key]);
				}
			}
			if(check_array_valid($phone_server_id_array)){
				
				$sql = 'select b.id,b.display_name,p.phone from '.$base_info_table.' as b left join '.$phone_table.' as p on p.base_id = b.id 
						where b.id in ('.implode(',',$phone_server_id_array).') ';
				
				$result = $this->query($sql);
				if(check_array_valid($result)){
										
					$rows_all_array = array();
					foreach($result as $value){
						
						$id = $value['id'];
						$phone = $value['phone'];
						if(!isset($rows_all_array[$id])){
							
							$tmp_array = $value;
							unset($tmp_array['phone']);
							$tmp_array['phone_array'][] = $phone;
							$rows_all_array[$id] = $tmp_array;
						}else{
							
							$rows_all_array[$id]['phone_array'][] = $phone;
						}				
					}
					$phone_array_tmp = $phone_array;
					foreach($phone_array_tmp as $key=>$value){
						
						foreach($value as $k =>$v){
							
							$phone_array[$key][$k] = $rows_all_array[$k];
						}
					}
				}				
			}
			
		}
		if(count($server_id_array) > 0){
			
			$extend_in_str = ' and (base_id not in ('.implode(',',$server_id_array).'))';
		}
		
		$sql = 'select base_id,email from '.$mail_table.' where user_id = '.$user_id.' '.$extend_in_str.' and email
				in (select email from (select email,count( * ) AS total_count from '.$mail_table.'
				where user_id = '.$user_id.' group by email) as m where total_count > 1) ';
		
		$result = $this->query($sql);
		if(check_array_valid($result)){
				
			foreach($result as $value){
		
				$id = $value['base_id'];
				$email = $value['email'];
				$md5_name = md5($email);
				$rows_array = array('id'=>$id,'display_name'=>'','email'=>$email,'phone_array'=>array());
				$email_array[$md5_name][$id] = $rows_array;
			}
			$email_array_tmp = $email_array;
			$email_server_id_array = array();
			foreach($email_array_tmp as $key=>$value){
		
				if(count($value) > 1){
						
					foreach($value as $base_id=>$v){
		
						if(!in_array($base_id,$email_server_id_array)){								
							
							$email_server_id_array[] = $base_id;
						}
					}
				}else{
					unset($email_array[$key]);
				}
			}
			if(check_array_valid($email_server_id_array)){
		
				$sql = 'select b.id,b.display_name,p.phone from '.$base_info_table.' as b left join '.$phone_table.' as p on p.base_id = b.id
						where b.id in ('.implode(',',$email_server_id_array).') ';
		
				$result = $this->query($sql);
				if(check_array_valid($result)){
											
					$rows_all_array = array();
					foreach($result as $value){
						
						$id = $value['id'];
						$phone = $value['phone'];
						if(!isset($rows_all_array[$id])){
								
							$tmp_array = $value;
							unset($tmp_array['phone']);
							$tmp_array['phone_array'][] = $phone;
							$rows_all_array[$id] = $tmp_array;
						}else{
								
							$rows_all_array[$id]['phone_array'][] = $phone;
						}
					}
					$email_array_tmp = $email_array;
					foreach($email_array_tmp as $key=>$value){
		
						foreach($value as $k =>$v){
								
							$email_array[$key][$k] = $rows_all_array[$k];
							$email_array[$key][$k]['email'] = $v['email'];
						}
					}
				}
			}				
		}
		$merge_list['name'] = $display_name_array;
		$merge_list['phone'] = $phone_array;
		$merge_list['email'] = $email_array;
		$merge_list['total_count'] = count($display_name_array) + count($phone_array) + count($email_array);		
		
		return $merge_list;
	}
	
	/**
	 * 合并联系人
	 * @param unknown $id
	 * @param unknown $merge_id_array
	 * @param unknown $merge_type
	 * @return boolean
	 */
	public function merge_contact($id,$merge_id_array,$merge_type){
		
		$check = false;
		$id = intval($id);
		$merge_type = trim($merge_type);
		$user_id = intval($this->user_id);
		$merge_type_array = array('name','phone','email');
		$merge_id_array = check_array_valid($merge_id_array) ? $merge_id_array : array();
		
		if($user_id <= 0 || $id <= 0 || count($merge_id_array) < 2 || !in_array($merge_type,$merge_type_array)){
			
			$this->error_array['result'] = '非法数据';
			$this->error_code_array['result'] = INVALID_DATA;
			return $check;
		}
		
		$tmp_merge_id_array = array();
		foreach($merge_id_array as $merge_id){
			
			$merge_id = intval($merge_id); 
			if($merge_id > 0 && !in_array($merge_id,$tmp_merge_id_array)){
				
				$tmp_merge_id_array[] = $merge_id;
			}
		}
		if(count($tmp_merge_id_array) < 2 || !in_array($id,$tmp_merge_id_array)){
			
			$this->error_array['result'] = '非法数据';
			$this->error_code_array['result'] = INVALID_DATA;
			return $check;
		}
		$merge_id_array = $tmp_merge_id_array;
		$base_info_table = $this->dbName.'.'.$this->trueTableName;
		$where = array();
		$where['user_id'] = $user_id;
		$where['is_delete'] = 0;
		$where['id'] = $id;
		$total_count = $this->get_list_count('',$where,'','');
		if($total_count <= 0){
			
			$this->error_array['result'] = '非法数据';
			$this->error_code_array['result'] = INVALID_DATA;
			return $check;
		}		
		$key = array_search($id,$merge_id_array);
		unset($where['id']);
		unset($merge_id_array[$key]);		
		$where['id'] = array('in',implode(',',$merge_id_array));
		$result = $this->getList('id','desc','',$where,'','id');
		if(check_array_valid($result)){				
			
			$where = array();
			$update_array = array();
			$version_auto_id = $this->version_auto_id;
			$where['id'] = $version_auto_id;
			$version_object = $this->version_object;
			$lastest_version_id = $this->lastest_version_id + 1;
			$update_array['version_id'] = $lastest_version_id;
			$update_array['update_time'] = $this->current_datetime;
			$check = $version_object->info_update($where,$update_array);
			if($check){
				
				$server_id = $id;
				$merge_id_array = array();
				foreach($result as $value){
				
					$merge_id_array[] = $value['id'];
				}
				$merge_id_array_str = implode(',',$merge_id_array);
				$model_object_array = $this->model_object_array;
				$version_relation_object = $this->version_relation_object;
				$version_relation_table = $version_relation_object->get_value('dbName').'.'.$version_relation_object->get_value('trueTableName');
				$tmp_sql_relation_insert = $sql_relation_insert = 'insert into '.$version_relation_table.' (contacts_version_id,server_id,server_other_id,mime_type,sign_type,version_id) values ';				
				$i = 0;
				foreach($model_object_array as $model_name =>$model_object){
					
					$table = $model_object->get_value('dbName').'.'.$model_object->get_value('trueTableName');
					if($model_name == 'photo'){
						
						$sql = 'delete from '.$table.' where base_id in ('.$merge_id_array_str.') ';
						$this->execute($sql);
						continue;
					}elseif($model_object->get_value('check_md5_str')){
						
						$sql = 'delete from '.$table.' where base_id in ('.$merge_id_array_str.') and (md5_str in (select * from (select md5_str from '.$table.' where base_id = '.$id.') as m))';
						$this->execute($sql);
					}
					
					if($model_name == $merge_type){
						
						switch($model_name){
							case 'phone':							
							case 'email':							
							default:
								
								$field_name = $model_name;
								break;
						}
						$sql = 'delete from '.$table.' where base_id in ('.$merge_id_array_str.') and ( '.$field_name.' in (select * from (select '.$field_name.' from '.$table.' where base_id = '.$id.') as m))';
						$this->execute($sql);
					}
						
					$sql = 'update '.$table.' set base_id = '.$id.',version_id = '.$lastest_version_id.' where base_id in ('.$merge_id_array_str.') ';
					$this->execute($sql);
					$sql = 'select id from '.$table.' where base_id = '.$id.' and version_id = '.$lastest_version_id;
					$result = $this->query($sql);
					if(check_array_valid($result)){
						
						$mimetype = $model_object->get_value('mimetype');
						foreach($result as $value){
							
							$server_other_id = $value['id'];
							$sql_relation_insert .= $i == 0 ? '' : ',';
							$sql_relation_insert .= '('.$version_auto_id.','.$server_id.',0,"'.$mimetype.'","add",'.$lastest_version_id.')';
							$i++;
						}
					}										
				}
				if($i > 0){
					
					$sql_relation_insert .= ';';					
					$this->execute($sql_relation_insert);
				}
				$where = array();
				$where['id'] = array('in',$merge_id_array_str);
				$check = $this->info_force_delete($where);
				if($check){
					
					$mimetype = $this->mimetype;
					$sql_relation_insert = $tmp_sql_relation_insert;
					foreach($merge_id_array as $key=>$merge_id){
							
						$sql_relation_insert .= $key == 0 ? '' : ',';
						$sql_relation_insert .= '('.$version_auto_id.','.$merge_id.',0,"'.$mimetype.'","forcedel",'.$lastest_version_id.')';
					}
					$sql_relation_insert .= ';';					
					$this->execute($sql_relation_insert);
					$sql = 'delete from '.$version_relation_table.' where contacts_version_id = '.$version_auto_id.' and
						    server_id in ('.$merge_id_array_str.') and version_id < '.$lastest_version_id;					
					$this->execute($sql);					
				}			
			}else{
				
				$this->error_array['result'] = '更新失败';
				$this->error_code_array['result'] = ERROR_UPDATE_FAILED;
			}			
		}else{
			
			$this->error_array['result'] = '非法数据';
			$this->error_code_array['result'] = INVALID_DATA;
		}
		return $check;
	}
	
	/**
	 * 单个联系人添加
	 * @param unknown $add_info
	 * @return boolean|number
	 */
	public function simple_add($add_info){
		
		$id = 0;
		$user_id = intval($this->user_id);
		if($user_id <= 0 || !check_array_valid($add_info) || !isset($add_info['base_info']) || !check_array_valid($add_info['base_info']) || !isset($add_info['base_info']['display_name'])){
				
			$this->error_array['result'] = '非法数据';
			$this->error_code_array['result'] = INVALID_DATA;
			return $id;
		}
		$base_info = $add_info['base_info'];
		if(strlen($base_info['display_name']) <= 0){
			
			$this->error_array['result'] = '非法数据';
			$this->error_code_array['result'] = INVALID_DATA;
			return $id;
		}
		
		$where = array();
		$update_array = array();
		$current_time = $this->current_datetime;
		$version_auto_id = $this->version_auto_id;
		$where['id'] = $version_auto_id;
		$version_object = $this->version_object;
		$lastest_version_id = $this->lastest_version_id + 1;
		$update_array['version_id'] = $lastest_version_id;
		$update_array['update_time'] = $current_time;
		$check = $version_object->info_update($where,$update_array);
		if($check){
			
			$base_info['phonetic_given_name'] = '';
			$base_info['phonetic_middle_name'] = '';
			$base_info['phonetic_family_name'] = '';		
			$conversion_relation = $this->contacts_list2current_list_relation();			
			$md5_str = self::make_md5_str($base_info, $conversion_relation);
			$str_where = ' user_id = '.$user_id.' and md5_str = "'.$base_info.'" ';
			$result = $this->field('id')->where($str_where)->find();
			$base_info_type = 'add';
			if(check_array_valid($result)){
				
				$id = $result['id'];
				$base_info_type = 'upd';
				$where = array('id'=>$id);
				$update_array = array();
				$update_array['is_delete'] = 0;
				$update_array['update_time'] = $current_time;
				$this->info_update($where,$update_array,false);
			}else{
				
				$base_info['md5_str'] = $md5_str;
				$base_info['user_id'] = $user_id;
				$base_info['is_delete'] = 0;
				$base_info['version_id'] = $lastest_version_id;
				$base_info['add_time'] = $current_time;
				$base_info['update_time'] = $current_time;
				$base_info['first_spell'] = strtoupper(\Spell\CUtf8_PY::encode($base_info['display_name'],'head'));
				$id = $this->info_add($base_info,false);
				$id = intval($id);
			}
						
			if($id > 0){
				
				$server_id = $id;
				$mimetype = $this->mimetype;
				$version_relation_object = $this->version_relation_object;				
				$version_relation_table = $version_relation_object->get_value('dbName').'.'.$version_relation_object->get_value('trueTableName');
				$sql_version_relation_common = 'insert into '.$version_relation_table.' (contacts_version_id,server_id,server_other_id,mime_type,sign_type,version_id) values ';
				$sql = $sql_version_relation_common.'('.$version_auto_id.','.$server_id.',0,"'.$mimetype.'","'.$base_info_type.'",'.$lastest_version_id.');';
				$this->execute($sql);
				$model_object_array = $this->model_object_array;
				foreach($model_object_array as $mode_name=>$model_object){
					
					$tmp_add_info_array = $add_info[$mode_name];
					if(count($tmp_add_info_array) > 0){
						
						$server_other_id = 0;
						$check_add = false;
						$mimetype = $model_object->get_value('mimetype');
						$check_md5_str = $model_object->get_value('check_md5_str');
						$model_object_class_name = '\\'.get_class($model_object);
						$sql_version_relation = $sql_version_relation_common;
						if($check_md5_str){
								
							$conversion_relation = $model_object->contacts_list2current_list_relation();
						}
						switch($mode_name){
						
							case 'nickname':
							case 'photo':
							case 'organization':
							case 'note':
							case 'sip':
							case 'group':									
								
								if($check_md5_str){									
									
									$tmp_add_info_array['md5_str'] = $model_object_class_name::make_md5_str($tmp_add_info_array,$conversion_relation);
								}
								
								$tmp_add_info_array['base_id'] = $server_id;
								$tmp_add_info_array['user_id'] = $user_id;
								$tmp_add_info_array['version_id'] = $lastest_version_id;
								$server_other_id = $model_object->info_add($tmp_add_info_array,false);
								if($server_other_id > 0){
									
									$check_add = true;
									$sql_version_relation .= '('.$version_auto_id.','.$server_id.','.$server_other_id.',"'.$mimetype.'","add",'.$lastest_version_id.')';
								}
																
								break;
							case 'phone':
							case 'email':
							case 'im':
							case 'postal':
							case 'website':
							case 'relation':
							case 'event':
								$i = 0;
								foreach($tmp_add_info_array as $tmp_add_info){
									
									if($check_md5_str){
											
										$tmp_add_info['md5_str'] = $model_object_class_name::make_md5_str($tmp_add_info,$conversion_relation);
									}
									
									$tmp_add_info['base_id'] = $server_id;
									$tmp_add_info['user_id'] = $user_id;
									$tmp_add_info['version_id'] = $lastest_version_id;
									$server_other_id = $model_object->info_add($tmp_add_info,false);
									if($server_other_id > 0){
											
										$check_add = true;
										if($i > 0){
											
											$sql_version_relation .= ',';
										}
										$sql_version_relation .= '('.$version_auto_id.','.$server_id.','.$server_other_id.',"'.$mimetype.'","add",'.$lastest_version_id.')';
										$i++;
									}
								}
								break;
						}
						if($check_add){
							
							$sql_version_relation .= ';';
							$this->execute($sql_version_relation);
						}						
					}									
				}
			}else{
				
				$this->error_array['result'] = '添加失败';
				$this->error_code_array['result'] = ERROR_ADD_FAILED;
			}
		}else{
			
			$this->error_array['result'] = '更新失败';
			$this->error_code_array['result'] = ERROR_UPDATE_FAILED;			
		}
				
		return $id;
	}
	
	/**
	 * 单个联系人编辑
	 * @param unknown $id
	 * @param unknown $update_info
	 * @return boolean
	 */
	public function simple_edit($id,$update_info){
		
		$check = false;
		$id = intval($id);
		$user_id = intval($this->user_id);		
		if($id <= 0 || $user_id <= 0 || !check_array_valid($update_info) || !isset($update_info['base_info']) || !check_array_valid($update_info['base_info']) || !isset($update_info['base_info']['display_name'])){
		
			$this->error_array['result'] = '非法数据';
			$this->error_code_array['result'] = INVALID_DATA;
			return $check;
		}
		$base_info = $update_info['base_info'];
		if(strlen($base_info['display_name']) <= 0){
				
			$this->error_array['result'] = '非法数据';
			$this->error_code_array['result'] = INVALID_DATA;
			return $check;
		}
		
		$str_where = ' id = '.$id.' and user_id = '.$user_id;
		$total_count = $this->where($str_where)->count();		
		if($total_count > 0){
					
			$where = array();
			$update_array = array();
			$current_time = $this->current_datetime;
			$version_auto_id = $this->version_auto_id;
			$where['id'] = $version_auto_id;
			$version_object = $this->version_object;
			$lastest_version_id = $this->lastest_version_id + 1;
			$update_array['version_id'] = $lastest_version_id;
			$update_array['update_time'] = $current_time;
			$check = $version_object->info_update($where,$update_array);
			if($check){
								
				$base_info['phonetic_given_name'] = '';
				$base_info['phonetic_middle_name'] = '';
				$base_info['phonetic_family_name'] = '';
				$conversion_relation = $this->contacts_list2current_list_relation();
				$base_info['md5_str'] = self::make_md5_str($base_info, $conversion_relation);
				$base_info['version_id'] = $lastest_version_id;
				$base_info['update_time'] = $current_time;
				$base_info['first_spell'] = strtoupper(\Spell\CUtf8_PY::encode($base_info['display_name'],'head'));
				$where = array();
				$where['id'] = $id;
				$check = $this->info_update($where,$base_info,false);				
				if($check){
			
					$server_id = $id;
					$mimetype = $this->mimetype;
					$version_relation_object = $this->version_relation_object;
					$version_relation_table = $version_relation_object->get_value('dbName').'.'.$version_relation_object->get_value('trueTableName');
					$sql_version_relation_common = 'insert into '.$version_relation_table.' (contacts_version_id,server_id,server_other_id,mime_type,sign_type,version_id) values ';
					$sql = $sql_version_relation_common.'('.$version_auto_id.','.$server_id.',0,"'.$mimetype.'","upd",'.$lastest_version_id.');';
					$this->execute($sql);
					$model_object_array = $this->model_object_array;
					$delete_server_other_id_array = array();
					foreach($model_object_array as $mode_name=>$model_object){
							
						$check_add = false;
						$check_update = false;
						$check_delete = false;					
						$server_other_id = 0;						
						$mimetype = $model_object->get_value('mimetype');
						$check_md5_str = $model_object->get_value('check_md5_str');
						$model_object_class_name = '\\'.get_class($model_object);
						$sql_version_relation = $sql_version_relation_common;
						if($check_md5_str){
						
							$conversion_relation = $model_object->contacts_list2current_list_relation();
						}												
						$tmp_update_info_array = $update_info[$mode_name];
						$str_where = ' base_id = '.$server_id.' and user_id = '.$user_id;
						$server_other_id_array = $model_object->where($str_where)->getField('id',true);
						if(check_array_valid($server_other_id_array)){
							
							if(count($tmp_update_info_array) > 0){
								
								switch($mode_name){
										
									case 'nickname':
									case 'photo':
									case 'organization':
									case 'note':
									case 'sip':
									case 'group':

										$server_other_id = $server_other_id_array[0];
										$str_tmp_where = ' id = '.$server_other_id;
										if($check_md5_str){
								
											$tmp_update_info_array['md5_str'] = $model_object_class_name::make_md5_str($tmp_update_info_array,$conversion_relation);
										}
											
										$tmp_update_info_array['base_id'] = $server_id;
										$tmp_update_info_array['user_id'] = $user_id;
										$tmp_update_info_array['version_id'] = $lastest_version_id;
										$check = $model_object->info_update($str_tmp_where,$tmp_update_info_array,false);										
										if($check){
								
											$check_update = true;
											$sql_version_relation .= '('.$version_auto_id.','.$server_id.','.$server_other_id.',"'.$mimetype.'","upd",'.$lastest_version_id.')';
										}
										
										break;
									case 'phone':
									case 'email':
									case 'im':
									case 'postal':
									case 'website':
									case 'relation':
									case 'event':
										$i = 0;
										foreach($tmp_update_info_array as $key=>$tmp_update_info){
								
											if($check_md5_str){
													
												$tmp_update_info['md5_str'] = $model_object_class_name::make_md5_str($tmp_update_info,$conversion_relation);
											}
								
											$tmp_update_info['base_id'] = $server_id;
											$tmp_update_info['user_id'] = $user_id;
											$tmp_update_info['version_id'] = $lastest_version_id;
											if(!isset($server_other_id_array[$key])){
												
												$server_other_id = $model_object->info_add($tmp_update_info,false);
												if($server_other_id > 0){
														
													$check_add = true;
													if($i > 0){
												
														$sql_version_relation .= ',';
													}
													$sql_version_relation .= '('.$version_auto_id.','.$server_id.','.$server_other_id.',"'.$mimetype.'","add",'.$lastest_version_id.')';
													$i++;
												}
											}else{
																								
												$server_other_id = $server_other_id_array[$key];
												unset($server_other_id_array[$key]);
												$str_tmp_where = ' id = '.$server_other_id;												
												$check = $model_object->info_update($str_tmp_where,$tmp_update_info,false);
												if($check){
												
													$check_update = true;
													$delete_server_other_id_array[] = $server_other_id;
													if($i > 0){
														
														$sql_version_relation .= ',';
													}
													$sql_version_relation .= '('.$version_auto_id.','.$server_id.','.$server_other_id.',"'.$mimetype.'","upd",'.$lastest_version_id.')';
													$i++;
												}
											}											
										}
										if(check_array_valid($server_other_id_array)){
											
											$str_tmp_where = ' id in ('.implode(',',$server_other_id_array).') ';											
											$check = $model_object->info_force_delete($str_tmp_where,false);
											if($check){
													
												$check_delete = true;
												foreach($server_other_id_array as $key=>$server_other_id){
											
													if($i > 0){
											
														$sql_version_relation .= ',';
													}
													$sql_version_relation .= '('.$version_auto_id.','.$server_id.','.$server_other_id.',"'.$mimetype.'","forcedel",'.$lastest_version_id.')';
												}
											}
										}
										break;
								}
								
							}else{
								
								$check = $model_object->info_force_delete($str_where,false);
								if($check){
									
									$check_delete = true;
									foreach($server_other_id_array as $key=>$server_other_id){
										
										if($key > 0){
										
											$sql_version_relation .= ',';
										}
										$sql_version_relation .= '('.$version_auto_id.','.$server_id.','.$server_other_id.',"'.$mimetype.'","forcedel",'.$lastest_version_id.')';										
									}
								}								
							}
						}else{
													
							if(count($tmp_update_info_array) > 0){
							
								switch($mode_name){
							
									case 'nickname':
									case 'photo':
									case 'organization':
									case 'note':
									case 'sip':
									case 'group':
							
										if($check_md5_str){
												
											$tmp_update_info_array['md5_str'] = $model_object_class_name::make_md5_str($tmp_update_info_array,$conversion_relation);
										}
							
										$tmp_update_info_array['base_id'] = $server_id;
										$tmp_update_info_array['user_id'] = $user_id;
										$tmp_update_info_array['version_id'] = $lastest_version_id;
										$server_other_id = $model_object->info_add($tmp_update_info_array,false);
										if($server_other_id > 0){
												
											$check_add = true;
											$sql_version_relation .= '('.$version_auto_id.','.$server_id.','.$server_other_id.',"'.$mimetype.'","add",'.$lastest_version_id.')';
										}
							
										break;
									case 'phone':
									case 'email':
									case 'im':
									case 'postal':
									case 'website':
									case 'relation':
									case 'event':
										$i = 0;
										foreach($tmp_update_info_array as $tmp_update_info){
												
											if($check_md5_str){
							
												$tmp_update_info['md5_str'] = $model_object_class_name::make_md5_str($tmp_update_info,$conversion_relation);
											}
												
											$tmp_update_info['base_id'] = $server_id;
											$tmp_update_info['user_id'] = $user_id;
											$tmp_update_info['version_id'] = $lastest_version_id;
											$server_other_id = $model_object->info_add($tmp_update_info,false);
											if($server_other_id > 0){
							
												$check_add = true;
												if($i > 0){
														
													$sql_version_relation .= ',';
												}
												$sql_version_relation .= '('.$version_auto_id.','.$server_id.','.$server_other_id.',"'.$mimetype.'","add",'.$lastest_version_id.')';
												$i++;
											}
										}
										break;
								}								
							}
						}
						
						if($check_add || $check_update || $check_delete){
								
							$check = true;
							$sql_version_relation .= ';';
							$this->execute($sql_version_relation);
						}						
					}
					if(check_array_valid($delete_server_other_id_array)){
						
						$sql = 'delete from '.$version_relation_table.' where contacts_version_id = '.$version_auto_id.' and server_id = '.$server_id.' and version_id < '.$lastest_version_id.' and (server_other_id in ('.
						       implode(',',$delete_server_other_id_array).')) and sign_type = "upd" ';
						
						$this->execute($sql);
					}
					if($check){
						
						$sql = 'select count(*) as total_count from '.$version_relation_table.' where contacts_version_id = '.$version_auto_id.' and server_id = '.$server_id.' and version_id ='.$lastest_version_id.
						       ' and sign_type = "forcedel" ';
						$result = $this->query($sql);
						if(check_array_valid($result)){
							
							$sql ='delete from '.$version_relation_table.' where contacts_version_id = '.$version_auto_id.' and server_id = '.$server_id.' and version_id < '.$lastest_version_id.
							      ' and sign_type != "add" and (server_other_id in (select server_other_id from '.$version_relation_table.' where contacts_version_id = '.$version_auto_id.
							      ' and server_id = '.$server_id.' and version_id ='.$lastest_version_id.' and sign_type = "forcedel" ) ';
							$this->execute($sql);
						}
					}
				}else{
			
					$this->error_array['result'] = '更新失败';
					$this->error_code_array['result'] = ERROR_UPDATE_FAILED;
				}
			}else{
			
				$this->error_array['result'] = '更新失败';
				$this->error_code_array['result'] = ERROR_UPDATE_FAILED;
			}
		}else{
			
			$this->error_array['result'] = '非法数据';
			$this->error_code_array['result'] = INVALID_DATA;
		}		
		return $check;
	}
	
	public function display_name_check_exists($id,$display_name){
		
		$check = false;
		$id = intval($id);
		$user_id = intval($this->user_id);
		$display_name = trim($display_name);
		if($user_id <= 0 || strlen($display_name) <= 0){
			
			$this->error_array['result'] = '非法数据';
			$this->error_code_array['result'] = INVALID_DATA;
			return $check;
		}
		
		$where = array('user_id'=>$user_id,'display_name'=>$display_name);
		if($id > 0){
			
			$where['id'] = array('neq',$id);
		}
		$result = $this->field('id')->where($where)->find();
		if(check_array_valid($result)){
			
			$check = true;
		}
		return $check;
	}
}
?>