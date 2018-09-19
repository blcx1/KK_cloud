<?php
/**
 * Created by PhpStorm.
 * User: inmyfree
 * Date: 2016/6/20
 * Time: 17:23
 */

namespace Home\Model;
class LocationModel extends  \Home\Model\DefaultModel{
	
	private $add_limit_time = 25;//相同数据限制时间插入（秒数）
	private $hour_add_limit_count = 60;//一个小时内重复的次数超过限定数不插入
	
    /**
     * 初始化
     * @param string $name
     * @param string $tablePrefix
     * @param string $connection
     */
    public function __construct($name='',$tablePrefix='',$connection='') {
		
    	$db_prefix = C('DB_PREFIX');
        $this->dbName = 'db_findmyphone';
        $this->tableName = $db_prefix.'location';
        $this->trueTableName = $this->tableName;
        $this->pk = 'id';
        parent::__construct($name,$tablePrefix,$connection);        
    }

    /**
     * @return array
     */
    public function add_location($deviceid,$ip,$longitude,$latitude,$cname,$ename,$countryflag)
    {
    	$result = array();
        $list_array = array('status'=>0);        
        $result['list_array'] = $list_array;
        $this->error_array = array();
        $this->error_code_array = array();
        if($deviceid <= 0){
        	
        	return $result;
        }
        
		$device_object = new \Home\Model\DeviceModel();
		$check_exists = $device_object->check_exists($deviceid,false);
        if($check_exists){
        	
        	$countryflag = intval($countryflag);
        	$countryflag = $countryflag == 1 ? 1 : 0;
        	$md5_str = self::make_md5($deviceid,$ip,$countryflag,$longitude,$latitude,$cname,$ename);
        	if($this->valid_add($deviceid,$md5_str)){
        		
	        	$location_data = array(
	        			'ip' => $ip,
	        			'deviceid' => $deviceid,
	        			'longitude' => $longitude,
	        			'latitude'  => $latitude,
	        			'countryflag' => $countryflag,
	        			'cname' => $cname,
	        			'ename'  => $ename,
	        			'createtime' => $this->current_datetime,
	        			'md5_str'=>$md5_str
	        	);
	        	$rawId = $this->info_add($location_data);        	
	        	if($rawId){
	        	
	        		$result['location_data'] = $location_data;
	        		$list_array['status'] = 1;
	        	}
	        	
	        	$result['list_array'] = $list_array;
        	}
        }                    
        
        return $result;
    }

    /**
     * @return array
     */
    public function find_device_location($user_id,$deviceid)
    {
    	$result = array('status'=>DEVICE_LOCATION_ERROR);
        $userdeviceModel = new UserDeviceModel();
        if($userdeviceModel->is_user_device($user_id,$deviceid)){
        	
        	$where = array();
            $where['deviceid'] = $deviceid;
            $data = $this->where($where)->order('id desc')->field('deviceid,ip,longitude,countryflag,latitude,cname,ename,createtime')->find();
            if (check_array_valid($data)){
            	
                $data['countryflag'] = intval($data['countryflag']);
                $data['status'] = DEVICE_LOCATION_SUCCESS;                
                $result = $data;
            }
        }
        return $result;
    }
    
    /**
     * 生成md5值
     * @param unknown $deviceid
     * @param unknown $ip
     * @param unknown $countryflag
     * @param unknown $longitude
     * @param unknown $latitude
     * @param unknown $cname
     * @param unknown $ename
     * @return string
     */
    public static function make_md5($deviceid,$ip,$countryflag,$longitude,$latitude,$cname,$ename){
    	    	
    	$md5_str = '';
    	$ip = trim($ip);
    	$cname = trim($cname);
    	$ename = trim($ename);
    	$latitude = trim($latitude);
    	$longitude = trim($longitude);
    	$deviceid = floatval($deviceid);
    	$countryflag = intval($countryflag);
    	if($deviceid > 0 && $countryflag >= 0){
    		
    		$md5_str = md5('d:'.$deviceid.'ip:'.$ip.'c:'.$countryflag.'l:'.$longitude.'&la:'.$latitude.'&cn:'.$cname.'&en:'.$ename);
    	}
    	    	
    	return $md5_str;
    }
    
    /**
     * 插入合法性判断
     * @param unknown $deviceid
     * @param unknown $md5_str
     * @return boolean
     */
    public function valid_add($deviceid,$md5_str){
    	
    	$check = true;
    	$md5_str = trim($md5_str);
    	$deviceid = floatval($deviceid);
    	if($deviceid > 0 && is_Md5($md5_str)){
    		
    		$str_where = 'deviceid = '.$deviceid.' and md5_str ="'.$md5_str.'"';
    		$result = $this->field('createtime')->where($str_where)->order('id desc')->find();    		
    		if(check_array_valid($result)){    			
    			
    			$add_time = strtotime($result['createtime']);    			
    			$add_time = $add_time === false ? 0 : $add_time;
    			$add_limit_time = $this->add_limit_time >= 1 ? $this->add_limit_time : 60;
    			if(($add_time + $add_limit_time) > $this->current_time){
    				
    				$check = false;
    				$this->error_array['result'] = '操作过快';
    				$this->error_code_array['result'] = ERROR_OPERATING_TOO_FAST;
    			}else{
    				
    				$str_where = 'deviceid = '.$deviceid.' ';
    				$result = $this->field('md5_str')->where($str_where)->order('id desc')->find();
    				if(check_array_valid($result) && $result['md5_str'] == $md5_str){
    					
    					$str_where = 'deviceid = '.$deviceid.' and md5_str ="'.$md5_str.'" and createtime >='.
    					date('Y-d-m H:i:s',($this->current_time - 3600));
    					    					
    					$hour_add_limit_count = $this->hour_add_limit_count > 1 ? $this->hour_add_limit_count : 25;
    					$total_count = $this->where($str_where)->count();
    					if($total_count > $hour_add_limit_count){
    						
    						$check = false;
    						$this->error_array['result'] = '相同操作过于频繁';
    						$this->error_code_array['result'] = ERROR_SAME_OPERATING_TOO_FREQUENT;
    					}    					
    				}
    			}
    		}
    	}else{
    		
    		$check = false;
    		$this->error_array['result'] = '非法数据';
    		$this->error_code_array['result'] = INVALID_DATA;
    	}
    	return $check;
    }
    

    /**
     * 获取定位列表
     * @param unknown $deviceid
     * @param number $page_no
     * @param number $page_size
     * @return multitype:number multitype:
     */
    public function get_location_list($deviceid,$page_no = 1,$page_size = 30){
    	
    	$list_array = array('total_count'=>0,'list'=>array());
    	$deviceid = floatval($deviceid);
    	if($deviceid > 0){
    		
    		$group_by = '';
    		$join_str = '';
    		$field_str = 'countryflag,longitude,latitude,cname,ename,createtime';
    		$where = array('deviceid'=>$deviceid);
    		$total_count = $this->get_list_count($group_by = '',$where,$join_str,'');
    		if($total_count > 0){
    			
    			$order_by = 'id';
    			$order_way = 'desc';
    			$list = $this->get_page_list($page_no,$page_size,$order_by,$order_way,$group_by,$where,$join_str,$field_str);
    			
    			$list_array['list'] = $list;
    			$list_array['total_count'] = $total_count;
    		}
    	}
    	return $list_array;
    }
}