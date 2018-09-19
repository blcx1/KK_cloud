<?php
/**
 * Created by PhpStorm.
 * User: inmyfree
 * Date: 2016/6/20
 * Time: 17:23
 */

namespace Home\Model;
class UserDeviceModel extends  \Home\Model\DefaultModel{
	
	protected $device_table = '';
	
    /**
     * 初始化
     * @param string $name
     * @param string $tablePrefix
     * @param string $connection
     */
    public function __construct($name='',$tablePrefix='',$connection='') {
		
    	$db_prefix = C('DB_PREFIX');
        $this->dbName = 'db_findmyphone';
        $this->tableName = $db_prefix.'user_device';
        $this->trueTableName = $this->tableName;
        $this->pk = 'id';
        parent::__construct($name,$tablePrefix,$connection);
        $this->device_table = $this->dbName.'.'.$db_prefix.'device';        
    }
    
    public function __destruct(){
    	
    	parent::__destruct();
    	$this->device_table = '';
    }

    /**
     * 获取绑定的手机列表
     * @return array
     */
    public function get_phone_list($user_id,$page_no,$page_size)
    {
        $list_array = array('total_count'=>0,'list' =>array());
        $user_id = intval($user_id);
        $this->error_array = array();
        $this->error_code_array = array();

        if($user_id > 0){
        	
        	$group_by = '';        	
        	$where = ' ud.user_id = '.$user_id;
        	$field_str = 'ud.id, ud.chanleid,ud.deviceid,d.name,d.mac';
            $join_str = ' as ud inner join '.$this->device_table.' as d on d.id  = ud.deviceid '; 
            $total_count = $this->get_list_count($group_by,$where,$join_str,'');
            if($total_count > 0){
            	
            	$order_by = 'ud.id';
            	$order_way = 'desc';
            	$list_array['total_count'] = $total_count;
            	$list = $this->get_page_list($page_no,$page_size,$order_by,$order_way,$group_by,$where,$join_str,$field_str);
            	
            	$list_array['list'] = $list;
            }                      
        }else{
        	
        	$this->error_array['result'] = '未登陆';
        	$this->error_code_array['result'] = ERROR_NOT_LOGIN;
        }
        return $list_array;
    }

    /**
     * 注册绑定手机
     * @param $user_id
     * @param $name
     * @param $ieme
     * @param $mac
     * @param $chanleid
     * @return array|mixed
     */
    public function register_phone($user_id,$name,$ieme,$mac,$chanleid)
    {
    	$mac = trim($mac);
    	$name = trim($name);
    	$ieme = trim($ieme);
        $list_array = array();
        $user_id = intval($user_id);
                
        $this->error_array = array();
        $this->error_code_array = array();

        if($user_id > 0){
        	        	            
            $md5_str = \Home\Model\DeviceModel::make_md5($name,$ieme,$mac);
            if($md5_str == ''){
            	
            	$this->error_array['result'] = '非法数据';
            	$this->error_code_array['result'] = INVALID_DATA;
            	return $list_array;
            }

            $deviceModel = new \Home\Model\DeviceModel();
            $device = $deviceModel->findDevice($md5_str);
            
            $this->error_array['result'] = '设备绑定失败';
            $this->error_code_array['result'] = ERROR_DEVICE_BAND_ERROR;

            if (check_array_valid($device)){//查找到设备
            	
            	$device_id = $device['id'];
                $user_device = $this->find_user_device($device_id);
                
                if($user_device){//设备已经绑定
                	
                    if($user_device['user_id'] == $user_id && $user_device['chanleid'] == $chanleid){//已经绑定
                    	
                        $list_array['deviceid'] = $device_id;
                        $this->error_array['result'] = '设备已绑定';
                        $this->error_code_array['result'] = DEVICE_BANDED;
                    }else{//更新绑定用户和推送id
                    	
                        $user_device['user_id']  = $user_id;
                        $user_device['chanleid'] = $chanleid;
                        $user_device['md5_str'] = self::make_md5($user_id, $device_id, $chanleid);
                        
                        if($this->save($user_device)){
                        	
                            $list_array['deviceid'] = $device_id;
                            $this->error_array['result'] = '设备绑定成功';
                            $this->error_code_array['result'] = DEVICE_BAND_SUCCESS;
                        }
                    }
                }else{//有设备，但设备未绑定用户
                	
                    $user_device_data = array(
                        'user_id'    => $user_id,
                        'chanleid'   => $chanleid,
                        'deviceid'   =>  $device_id,
                        'createtime' => $this->current_datetime,
                    );

                    if ($this->add($user_device_data)){
                    	
                        $list_array['deviceid'] = $device['id'];
                        $this->error_array['result'] = '设备绑定成功';
                        $this->error_code_array['result'] = DEVICE_BAND_SUCCESS;
                    }
                }
            }else{

            	$current_datetime = $this->current_datetime;
                $data = array(
                    'name'       => $name,
                    'ieme'       => $ieme,
                    'mac'       => $mac,
                    'createtime' => $current_datetime,
                	'md5_str'   => $md5_str
                );
                $newdid = $deviceModel->info_add($data);
                if($newdid > 0){
                	
                    $user_device_data = array(
                    		
                        'user_id'   => $user_id,
                        'chanleid'       => $chanleid,
                        'deviceid'       => $newdid,
                        'createtime' => $current_datetime,
                    	'md5_str'=>self::make_md5($user_id,$newdid, $chanleid)
                    );
                    
                    if ($this->info_add($user_device_data)){
                    	
                        $list_array['deviceid'] = $newdid;
                        $this->error_array['result'] = '设备绑定成功';
                        $this->error_code_array['result'] = DEVICE_BAND_SUCCESS;
                    }
                }
            }
        }else{
        	
        	$this->error_array['result'] = '未登陆';
        	$this->error_code_array['result'] = ERROR_NOT_LOGIN;
        }
        return $list_array;
    }
    
    /**
     * 生成MD5
     * @param unknown $user_id
     * @param unknown $deviceid
     * @param unknown $chanleid
     * @return string
     */
    public static function make_md5($user_id,$deviceid,$chanleid){
    	
    	$md5_str = '';
    	if(!(empty($user_id) && empty($deviceid) && empty($chanleid)) && $deviceid > 0 && $user_id > 0){
    		
    		$md5_str = md5('u:'.$user_id.'d:'.$deviceid.'c:'.$chanleid);
    	}
    	return $md5_str;  	
    }
    
     /**
      * 返回设备详细信息
      * @return array
      */
    public function find_user_device($deviceid,$user_id = 0)
    {
       $user_device_info = array();      
       $deviceid = floatval($deviceid);
       if($deviceid > 0){
       	
       		$where = array('deviceid'=>array('eq',$deviceid));
       		if($user_id > 0){
       			
       			$where['user_id'] = $user_id;
       		}
       		$result = $this->where($where)->find();
       		if(check_array_valid($result)){
       			
       			$user_device_info = $result;
       		}
       }      
       return $user_device_info;
    }

    /**
     * 判断用户与设备是否绑定
     * @param $user_id
     * @param $deviceid
     * @return bool
     */
    public function is_user_device($user_id,$deviceid){
    	
    	$flag = false;
    	$user_id = intval($user_id);
    	$deviceid = floatval($deviceid);
    	
    	if($user_id > 0 && $deviceid > 0){
    		
    		$where = array(
    				'user_id'   => array('eq',$user_id),
    				'deviceid'  =>array('eq',$deviceid),
    		);
    		
    		$result = $this->where($where)->find();
    		if(check_array_valid($result)){
    			
    			$flag = true;
    		}
    	}
    	      
        return $flag;
    }
    
    /**
     * 获取某个用户的设备个数
     * @param unknown $user_id
     * @return number
     */
    public function get_user_device_total_count($user_id){
    	
    	$total_count = 0;
    	if($user_id > 0){
    		 
    		$group_by = '';
    		$where = ' ud.user_id = '.$user_id;
    		$field_str = 'ud.id, ud.chanleid,ud.deviceid,d.name,d.mac';
    		$join_str = ' as ud inner join '.$this->device_table.' as d on d.id  = ud.deviceid ';
    		$total_count = $this->get_list_count($group_by,$where,$join_str,'');    		
    	}
    	return $total_count;
    }
}