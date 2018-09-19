<?php
/**
 * 通话记录
 *
 */
namespace Home\Model; 
use Home\Model\GroupModel;
class CallrecordModel extends \Home\Model\DefaultModel {
	
	protected $group_table = '';
	
	/**
	 * 初始化
	 */
	public function __construct($cache_object = null,$name='',$tablePrefix='',$connection='') {
		
		$db_prefix = C('DB_PREFIX');
		$this->dbName = 'db_callrecord';
		$this->tableName = $db_prefix.'callrecord';		
		$this->trueTableName = $this->tableName;
		$this->pk = 'id';
		parent::__construct($name,$tablePrefix,$connection);		
		$this->group_table= $this->dbName.'.'.$db_prefix.'group';
	}
	
	/**
	 * 释放
	 **/
	public function __destruct(){
	    		
		parent::__destruct();
		$this->group_table = '';
	}
	
	/**
	 * 获取某个用户某个组列表
	 * @param unknown $user_id
	 * @param unknown $gourp_id
	 * @param number $page_no
	 * @param number $page_size
	 * @param number $is_recy
	 * @return multitype:number multitype: |Ambigous <multitype:number multitype: , multitype:unknown Ambigous <multitype:, unknown> >
	 */
	public function get_gourp_call_record($user_id,$gourp_id,$page_no = 1,$page_size = 30,$is_recy = 0){
		
		$week_list = array();
		$return_array = array('total_count'=>0,'list'=>array(),'week_list'=>$week_list);		
		if($user_id <= 0){
	
			$this->error_array['result'] = '非法数据';
			$this->error_code_array['result'] = INVALID_DATA;
			return $return_array;
		}
		$is_recy = intval($is_recy) == 0 ? 0 : 1;
		$group_by = '';		
		$join_str = '';		
		$where['user_id'] = $user_id;
		$where['group_id'] = $gourp_id;
		$where['is_delete'] = $is_recy;
		$total_count = $this->get_list_count($group_by,$where,$join_str,'');
		if($total_count > 0){
			
			$order_by='client_add_time desc,id ';
			$order_way = 'desc';
			$field_str = 'id,client_add_time as date,type_value,duration';
			$result = $this->get_page_list($page_no,$page_size,$order_by,$order_way,$group_by,$where,$join_str,$field_str);
			if (check_array_valid($result)){
				
				$call_record_list_array = array();
				foreach ($result as $key=>$value){
					
					$date_time = strtotime($value['date']);
					$send_time = date('Y-m-d',$date_time);
					$week = date('w',$date_time);
					
					switch ($week){
						case 1:
							$str_week = '星期一';
						break;
						case 2:
							$str_week = '星期二';
							break;
						case 3:
							$str_week = '星期三';
							break;
						case 4:
							$str_week = '星期四';
							break;
						case 5:
							$str_week = '星期五';
							break;
						case 6:
							$str_week = '星期六';
							break;
						case 0:
							$str_week = '星期日';
							break;
					}
										
					$value['duration_str'] = self::get_duration_str($value['duration']);
					$value['type_value_str'] = self::get_type_value($value['type_value']);
					$value['date'] = date('H:i:s',$date_time);
					$week_list[$send_time]['week'] = $str_week;
					$call_record_list_array[$send_time][]= $value;			
				}
				$return_array = array('total_count'=>$total_count,'list'=>$call_record_list_array,'week_list'=>$week_list);	
			}
		}
		
		return $return_array;
	}
	
	/**
	 * 获取通讯记录列表（分组取一组中的最新值）
	 * @param unknown $user_id
	 * @param number $is_recy
	 * @param number $page_no
	 * @param number $page_size
	 * @return multitype:|multitype:multitype:string number
	 */
	public function get_call_record_group($user_id,$is_recy = 0,$page_no = 1,$page_size = 150,$extend_where = array()){
		$return_array = array('total_count'=>0,'list'=>array());		
		if($user_id <= 0){
			$this->error_array['result'] = '非法数据';
			$this->error_code_array['result'] = INVALID_DATA;
			return $return_array;
		}
		$is_recy = intval($is_recy) == 1 ? 1 : 0;
		$group_by = '';
		$field_str = 'distinct s.group_id';
		$join_str = ' as s inner join '.$this->group_table.' as g on (g.id = s.group_id and g.user_id = s.user_id)';		
		$where = array();
		$where['s.user_id'] = $user_id;		
		$where['s.is_delete'] = $is_recy;
		if(check_array_valid($extend_where)){
			$where = array_merge($where,$extend_where);
		}
		$total_count = $this->get_list_count($group_by,$where,$join_str,$field_str);
		if($total_count > 0){
			$return_array['total_count'] = $total_count;
			$group_by = 's.group_id';
			$order_by = 's.client_add_time desc,s.id';
			$order_way = 'desc';
			$field_str = 's.group_id,s.client_add_time as date,s.type_value,s.duration,g.phone,g.name,count(*) as total_count';
			$result = $this->get_page_list($page_no,$page_size,$order_by,$order_way,$group_by,$where,$join_str,$field_str);			
			if(check_array_valid($result)){
				$list_array = array();
				foreach ($result as $key=>$value){
					$value['type_value_str'] = self::get_type_value($value['type_value']);
					$value['duration_str'] = self::get_duration_str($value['duration']);
					$list_array[$key] = $value;
				}
				$return_array['list'] = $list_array;
			}			
		}
		return $return_array;		
	}
	
	/**
	 * 时长转化为字符串
	 * @param unknown $duration
	 * @return string
	 */
	public static function get_duration_str($duration){
		
		$duration_str = '';
		$duration = intval($duration);
		$sec = $minute = $hour = 0;
		if($duration > 0){
			
			$sec = $duration%60;
			$tmp_duration = $duration - $sec;
			if($tmp_duration > 0){
				
				$minute = $tmp_duration%3600;
				$tmp_duration -= $minute*60;
				if($tmp_duration > 0){
					
					$hour = $tmp_duration/3600;
				}
			}		
		}
		$duration_str = strval($hour).' '.L('Hour').' '.strval($minute).' '.L('Minute').' '.strval($sec).' '.L('Sec');
		return $duration_str;
	}
	
	/**
	 * 获取电话呼入的类型名称
	 * @param unknown $type
	 * @return string
	 */
	public static function get_type_value($type){
		
		$type_value = '';
		$type = intval($type);
		switch($type){
			
			case 1:
				
				$type_value = L('Incoming');
				break;
			case 2:
				
				$type_value = L('CallOut');
				break;
			case 3:
			default:
				
				$type_value = L('Missed');
				break;
		}
		return $type_value;
	}

	/**
	 * 获取通讯记录数据列表
	 */
	public function client_get_call($user_id,$is_recy = 1,$page_no = 1,$page_size = 150){
		
		$call_record_list_array = array('total_count'=>0,'list'=>array());
		$user_id = intval($user_id);
		if($user_id <= 0){
				
			$this->error_array['result'] = '非法数据';
			$this->error_code_array['result'] = INVALID_DATA;
			return $call_record_list_array;
		}
		$is_recy = intval($is_recy) == 1 ? 1 : 0;
		$group_by = '';
		$order_by = 's.id';
		$order_way = 'desc';
	
		$join_str = ' as s left join '.$this->group_table.' as g on g.id = s.group_id';
		$field_str = 's.type_value,s.duration,s.client_add_time,s.md5_content,g.phone,g.name,g.update_time';
		$where['s.is_delete'] = $is_recy;
		$where['s.user_id'] = $user_id;
		$total_count = $this->get_list_count($group_by,$where,$join_str,'s.id');
		if ($total_count > 0){
				
			$list = array();
			$data = $this->get_page_list($page_no,$page_size,$order_by,$order_way,$group_by,$where,$join_str,$field_str);
			foreach ($data as $key=>$value){
	
				$list[$key] = $value;
				$list[$key]['client_add_time'] = floatval(strtotime($value['client_add_time']));
			}
			$call_record_list_array = array(
					'total_count'=>$total_count,
					'list'=>$list
			);
		}
		return $call_record_list_array;
	}
	
	/**
	 * 添加通讯记录接口
	 * @param unknown $user_id 用户id
	 * @param unknown $data 
	 * @return multitype:number multitype: |multitype:number multitype:string
	 */
	public  function client_add_callrecord($user_id,$data = array()){ 
		
		$add_count = 0;
		$update_count = 0;		
		$errorMd5 = array();
		$datalist = array(
				'add_count' => $add_count,
				'update_count'=>$update_count,
				'errorMd5' =>$errorMd5
		);
		
		$user_id = intval($user_id);		
		if($user_id <= 0){
			
			$this->error_array['result'] = '非法数据';
			$this->error_code_array['result'] = INVALID_DATA;
			return $datalist;
		}
		
		$groupModel = new GroupModel($this->cache_object);		
		$data = is_array($data) ? $data : (is_object($data) ? (array)$data : array());		
		if(check_array_valid($data)){
				
			$current_datetime = $this->current_datetime;
			$groupData = $groupModel->get_group($user_id);
			$md5_list = $this->get_md5_list($user_id);
			foreach ($data as $value){
					
				if(!is_array($value) && !is_object($value)){
						
					continue;
				}
				$value = (array) $value;
				if(!check_array_valid($value)){
						
					continue;
				}
				if(!isset($value['client_add_time']) || floatval($value['client_add_time']) == 0){
						
					continue;
				}
				
				$phone = isset($value['phone']) && is_numeric($value['phone']) ? $value['phone'] : '';
				if (!is_numeric($phone) || $phone < 3){
						
					continue;
				}
				
				$md5_content = isset($value['md5_content']) ? trim($value['md5_content']) : '';
				if(!is_Md5($md5_content)){
						
					continue;
				}
				
				$duration = isset($value['duration']) ? floatval($value['duration']) : 0;
				if($duration < 0){
						
					continue;
				}
				$name = isset($value['name']) ? trim($value['name']) : '';
				$client_add_time =  date('Y-m-d H:i:s',floatval($value['client_add_time']));
				$type_value = isset($value['type_value']) ? intval($value['type_value']) : 0;
				if (!in_array($type_value,array(1,2,3))){
						
					$type_value = 1;
				}
							
				$group_id = isset($groupData[$phone]) ? $groupData[$phone] : 0;				
				if ($group_id <= 0){
					
					$groupList = array();
					$groupList['user_id'] = $user_id;
					$groupList['phone'] = $phone;
					$groupList['name'] = $name;
					$groupList['total_count'] = 0;
					$groupList['use_total_count'] = 0;
					$groupList['recy_total_count'] = 0;
					$groupList['add_time'] = $current_datetime;
					$groupList['update_time'] = $current_datetime;
					$group_id = $groupModel->info_add($groupList);
					
					if ($group_id <= 0){
							
						$errorMd5[] = $md5_content;
						continue;
					}
					$groupData[$phone] = $group_id;
				}else{
						
					$where = array();
					$update_array = array();
					$where['id'] = $group_id;
					$where['user_id'] = $user_id;
					$update_array['name'] = $name;					
					$groupModel->info_update($where,$update_array);
				}			
				
				$list = array();
				$list['user_id']=$user_id;
				$list['group_id']= $group_id;
				$list['is_delete']= 0;
				$list['duration']= $duration;
				$list['type_value'] = $type_value;
				$list['md5_content'] = $md5_content;
				$list['client_add_time']= $client_add_time;
		
				if (isset($md5_list[$md5_content])){
						
					$where = array();
					$where['id'] = $md5_list[$md5_content];
					$flg = $this->info_update($list,$where);

					$flg = $flg === false ? false : true;
					if($flg){
							
						$update_count += 1;
					}
				}else {
						
					$flg = $this->info_add($list);
					if($flg){
							
						$add_count += 1;
						$md5_list[$md5_content] = $flg;
					}
				}
				if (!$flg){
						
					$errorMd5[] = $md5_content;
				}
			}
		}
		
		$check = count($errorMd5) > 0 ? false : true;
		$groupModel->process($user_id,$check);
		$datalist = array(	
			'add_count' => $add_count,				
			'update_count' => $update_count,
			'errorMd5' => $errorMd5,
		);		 
		return $datalist;
	}
	
	/**
	 * 删除通讯记录接口
	 * @param unknown $user_id
	 * @param unknown $id
	 */
	public function client_delete_call($user_id,$md5_array=array()){
	
		$deletecount = 0;
		$errorMd5 = array();
		$deleteList = array(	
				'deletecount' => $deletecount,
				'md5' => $errorMd5
		);
		$user_id = intval($user_id);
		if ($user_id <= 0){
				
			$this->error_array['result'] = '非法数据';
			$this->error_code_array['result'] = INVALID_DATA;
			return $deleteList;
		}
		if (is_array($md5_array) || is_object($md5_array)){
				
			$md5_array = (array)$md5_array;
			if (check_array_valid($md5_array)){				
				
				$valid_md5_array = array();								
				foreach ($md5_array as $md5){
						
					if (!is_Md5($md5)){
	
						continue;
					}					
					$valid_md5_array[] = $md5;					
				}
				
				if(check_array_valid($valid_md5_array)){
					
					$where = array();
					$where['user_id'] = $user_id;
					$where['md5_content'] = array('in',$valid_md5_array);
					$result = $this->where($where)->count();
					$total_count = intval($result);
					if($total_count > 0){
						
						$flg = $this->info_force_delete($where);
						if($flg){
								
							$deletecount = $total_count;
						}else{
							
							$errorMd5 = $valid_md5_array;
						}
					}					
				}				
			}
		}
		if ($deletecount > 0 ){
	
			$groupModel = new \Home\Model\GroupModel($this->cache_object);
			$groupModel->process($user_id,true);
		}
		$deleteList = array(
				'deletecount' => $deletecount,
				'md5' => $errorMd5
		);
		
		return $deleteList;
	}
	
	/**
	 * 公共处理
	 * @param unknown $method_name
	 * @param unknown $user_id
	 * @param unknown $group_id_array
	 * @param unknown $id_array
	 * @param unknown $status_value
	 * @param unknown $extend_where
	 * @return multitype:|Ambigous <multitype:, \Home\Model\multitype:number, multitype:number >
	 */
	protected function common_call_record_process($method_name,$user_id,$group_id_array,$id_array,$status_value = -1,$extend_where = array()){
		
		$success_list = array();
		$user_id = intval($user_id);
		$method_name = trim($method_name);
		$group_id_array = check_array_valid($group_id_array) ? $group_id_array : array();
		$id_array = check_array_valid($id_array) ? $id_array : array();
		$group_id_array_count = count($group_id_array);
		$id_array_count = count($id_array);
		if ($user_id <= 0 || ($group_id_array_count <= 0 && $id_array_count <= 0)){
				
			$this->error_array['result'] = '非法数据';
			$this->error_code_array['result'] = INVALID_DATA;
			return $success_list;
		}elseif(!method_exists($this,$method_name)){
			
			$this->error_array['result'] = '方法不存在';
			$this->error_code_array['result'] = METHOD_NOT_EXISTS;
			return $success_list;
		}
		$valid_id_array = array();
		$tmp_id_array = $group_id_array_count > 0 ? $group_id_array : $id_array;
		$valid_id_array = self::get_valid_id_array($tmp_id_array);
		if(count($valid_id_array) > 0){
				
			$where = array();			
			$id_key = $group_id_array_count > 0 ? 'group_id' : 'id';
			$where['user_id'] = $user_id;
			$where[$id_key] = array('in',$valid_id_array);
			if(check_array_valid($extend_where)){
				
				$where = array_merge($where,$extend_where);
			}
			if($method_name == 'change_name'){
				
				$where['is_delete'] = $status_value == 0 ? 1 : 0;
				$check = $this->change_name('is_delete',$status_value,$where);
			}else{
								
				$check = $this->$method_name($where);
			}
			
			if($check){
		
				$success_list = $valid_id_array;
			}
		}
		
		if (count($success_list) > 0){
		
			$groupModel = new \Home\Model\UserGroupModel($this->cache_object);
			$groupModel->process($user_id,true);
		}
		return $success_list;
	}
	
	/**
	 * 移动到回收站
	 * @param unknown $user_id
	 * @param unknown $group_id_array
	 * @param unknown $id_array
	 * @return multitype:
	 */
	public function recycle_call_record($user_id,$group_id_array,$id_array){
		
		$status_value = 1;
		$method_name = 'change_name';		
		return $this->common_call_record_process($method_name,$user_id,$group_id_array,$id_array,$status_value);
	}
	
	/**
	 * 恢复数据
	 * @param unknown $user_id
	 * @param unknown $group_id_array
	 * @param unknown $id_array
	 * @return multitype:
	 */
	public function recover_call_record($user_id,$group_id_array,$id_array){
		
		$status_value = 0;
		$method_name = 'change_name';		
		return $this->common_call_record_process($method_name,$user_id,$group_id_array,$id_array,$status_value);
	}
	
	/**
	 * 彻底删除
	 * @param unknown $user_id
	 * @param unknown $group_id_array
	 * @param unknown $id_array
	 * @return multitype:
	 */
	public function delete_call_record($user_id,$group_id_array,$id_array){
		
		$status_value = -1;
		$extend_where = array();
		$extend_where['is_delete'] = 1;
		$method_name = 'info_force_delete';					
		return $this->common_call_record_process($method_name,$user_id,$group_id_array,$id_array,$status_value,$extend_where);
	}
	
	/**
	 * 移动数据到回收站
	 * @param unknown $user_id
	 * @param unknown $id
	 */
	public function client_recy_call($user_id,$md5_array=array()){
	
		$status_value = 1;
		$list_count_name = 'recycount';
		return $this->change_delete_status($user_id,$list_count_name,$status_value,$md5_array);
	}
	
	/**
	 * 恢复回收站对应数据
	 * @param unknown $user_id
	 * @param unknown $id
	 */
	public function client_recover_call($user_id,$md5_array=array()){

		$status_value = 0;
		$list_count_name = 'recovercount';
		return $this->change_delete_status($user_id,$list_count_name,$status_value,$md5_array);
	}
	
	
	/**
	 * 改变delete的状态
	 * @param unknown $user_id
	 * @param unknown $list_count_name
	 * @param unknown $status_value
	 * @param unknown $md5_array
	 * @return multitype:multitype: number |multitype:number multitype:unknown
	 */
	public function change_delete_status($user_id,$list_count_name,$status_value,$md5_array = array()){
		
		$change_count = 0;
		$errorMd5 = array();
		$deleteList = array(
				$list_count_name => $change_count,
				'errorMd5'  => $errorMd5
		);
		$user_id = intval($user_id);
		if ($user_id <= 0){
	
			$this->error_array['result'] = '非法数据';
			$this->error_code_array['result'] = INVALID_DATA;
			return $deleteList;
		}
		$status_value = in_array($status_value,array(0,1)) ? $status_value : 0;
		if (is_array($md5_array) || is_object($md5_array)){
	
			$md5_array = (array)$md5_array;
			if (check_array_valid($md5_array)){
				
				$valid_md5_array = array();
				foreach ($md5_array as $md5){
	
					if (!is_Md5($md5)){
	
						continue;
					}
					$valid_md5_array[] = $md5;					
				}
				
				if(check_array_valid($valid_md5_array)){
						
					$where = array();
					$where['user_id'] = $user_id;
					$where['is_delete'] = $status_value == 0 ? 1 : 0;
					$where['md5_content'] = array('in',$valid_md5_array);
					$result = $this->where($where)->count();
					$total_count = intval($result);
					if($total_count > 0){
							
						$flg = $this->change_name('is_delete',$status_value,$where);
						if($flg){
				
							$change_count = $total_count;
						}else{
								
							$errorMd5 = $valid_md5_array;
						}
					}
				}
			}
		}
		$deleteList = array(
				$list_count_name => $change_count,
				'errorMd5'  => $errorMd5
		);
		if($change_count > 0){
				
			$groupModel = new \Home\Model\GroupModel($this->cache_object);
			$groupModel->process($user_id,true);
		}
		return $deleteList;
	}
	
	/**
	 * 获取用户的md5值与id的对应关系
	 * @param unknown $user_id
	 */
	public function get_md5_list($user_id){
	
		$md5_list = array();
		$user_id = intval($user_id);
		if($user_id > 0){
				
			$field_str = 'id,md5_content';
			$str_where = 'user_id = '.$user_id;
			$result = $this->field($field_str)->where($str_where)->select();
			if(check_array_valid($result)){
	
				foreach($result as $value){
						
					$md5_list[$value['md5_content']] = $value['id'];
				}
			}
		}
		return $md5_list;
	}
	
	/**
	 * 清空数据
	 * @param unknown $user_id
	 * @return boolean
	 */
	public function client_clearAll($user_id){
		$check = false;
		$user_id = intval($user_id);
		if ($user_id <= 0){
	
			$this->error_array['result'] = '非法数据';
			$this->error_code_array['result'] = INVALID_DATA;
			return $check;
		}
		$where = array();
		$where['user_id'] = $user_id;
		$check = $this->info_force_delete($where);
		if ($check){
				
			$where = array();
			$where['user_id'] = $user_id;
			$user_group_object = new \Home\Model\GroupModel($this->cache_object);
			$user_group_object->info_force_delete($where);

		}else {
	
			$this->error_array['result'] = '删除失败';
			$this->error_code_array['result'] = ERROR_DEL_FAILED;
		}
		return $check;
	}
}
?>