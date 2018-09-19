<?php
/**
 * Created by PhpStorm.
 * User: inmyfree
 * Date: 2016/6/20
 * Time: 17:23
 */

namespace Home\Model;
class CommandModel extends  \Home\Model\DefaultModel{
	
	private $device_table = '';
	private $add_limit_time = 60;//相同数据限制时间插入（秒数）
	private $hour_add_limit_count = 30;//一个小时内重复的次数超过限定数不插入
	
    /**
     * 初始化
     * @param string $name
     * @param string $tablePrefix
     * @param string $connection
     */
    public function __construct($name='',$tablePrefix='',$connection='') {
		
    	$db_prefix = C('DB_PREFIX');
        $this->dbName = 'db_findmyphone';
        $this->tableName = $db_prefix.'command';
        $this->trueTableName = $this->tableName;
        $this->pk = 'id';
        parent::__construct($name,$tablePrefix,$connection);
        $this->device_table = $this->dbName.'.'.$db_prefix.'device';
    }

    public function add_command($cid,$deviceid,$chanleid,$pdeviceid,$pchanleid,$content){

        $list_array = array();
        $this->error_array = array();
        $this->error_code_array = array();
        $cid = intval($cid);
        $deviceid = floatval($deviceid);
        $pdeviceid = floatval($pdeviceid);
        
        $time = $this->current_datetime;
        $command_data = array(
            'cid'           => $cid,
            'deviceid'     => $deviceid,
            'chanleid'     => $chanleid,
            'pdeviceid'    => $pdeviceid,
            'pchanleid'    => $pchanleid,
            'attempt'      => 0,
            'status'       => 0,
            'content'      => $content,           
            'createtime'  => $time,
        );
        $list_array['status'] = 0;
        $md5_str = self::make_md5($cid,$deviceid,$chanleid,$pdeviceid,$pchanleid,$content);
        if($md5_str == ''){
        	 
        	$this->error_array['result'] = '非法数据';
        	$this->error_code_array['result'] = INVALID_DATA;
        	
        	$result = array(
        			'list_array' => $list_array,
        			'command_data' =>$command_data
        	);
        	
        	return $result;
        }
        if($this->valid_add($cid,$deviceid,$pdeviceid,$md5_str)){
        	
	        $info_array  = $command_data;
	        $info_array['md5_str'] = $md5_str;
	        $rawId = $this->info_add($info_array);       
	        if($rawId){
	        	
	            $command_data['id'] = $rawId;
	            $list_array['status'] = 1;
	        }
	        $result = array(
	            'list_array' => $list_array,
	            'command_data' =>$command_data
	        );
        }
        return $result;
    }

    /**
     * @param $command_id
     * @param $cid
     * @return mixed
     */
    public function find_command($command_id,$cid,$deviceid = 0)
    {
        $where = array(
            'id'  => $command_id,
            'cid' => $cid,
        );
        if($deviceid > 0){
        	
        	$where['pdeviceid'] = $deviceid;
        	//$where['deviceid'] = array('exp','!= pdeviceid ');        	
        }
        return $this->where($where)->getField('chanleid');
    }

    public function change_command_status($command_id, $status)
    {
        $where = array('id'=>$command_id);
        $data['status'] = $status;
        $data['executiontime'] = $this->current_datetime;
       return $this->where($where)->save($data);
    }
    
    /**
     * 生成md5值
     * @param unknown $cid
     * @param unknown $deviceid
     * @param unknown $chanleid
     * @param unknown $pdeviceid
     * @param unknown $pchanleid
     * @param unknown $content
     * @return string
     */
    public static function make_md5($cid,$deviceid,$chanleid,$pdeviceid,$pchanleid,$content){
    
    	$md5_str = '';
    	$cid = intval($cid);
    	$chanleid = trim($chanleid);
    	$pchanleid = trim($pchanleid);    	
    	$content = trim($content);    	
    	$deviceid = floatval($deviceid);
    	$pdeviceid = floatval($pdeviceid);
    	
    	if($cid > 0 && $deviceid > 0 && $pdeviceid >= 0){
    
    		$md5_str = md5('c:'.$cid.'ch:'.$chanleid.'d:'.$deviceid.'pch:'.$pchanleid.'pd:'.$pdeviceid.'con:'.$content);
    	}
    
    	return $md5_str;
    }
    
    /**
     * 插入合法性判断
     * @param unknown $cid
     * @param unknown $deviceid
     * @param unknown $pdeviceid
     * @param unknown $md5_str
     * @return boolean
     */
    public function valid_add($cid,$deviceid,$pdeviceid,$md5_str){
    	 
    	$check = true;
    	$cid = intval($cid);
    	$md5_str = trim($md5_str);
    	$deviceid = floatval($deviceid);
    	$pdeviceid = floatval($pdeviceid);
    	if($cid > 0 && $deviceid > 0 && $pdeviceid > 0 && is_Md5($md5_str)){
    
    		$str_where = 'cid = '.$cid.' and deviceid = '.$deviceid.' and pdeviceid = '.$pdeviceid.' and md5_str ="'.$md5_str.'"';
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
    
    				$str_where = 'cid = '.$cid.' and deviceid = '.$deviceid.' and pdeviceid = '.$pdeviceid.' ';
    				$result = $this->field('md5_str')->where($str_where)->order('id desc')->find();
    				if(check_array_valid($result) && $result['md5_str'] == $md5_str){
    						
    					$str_where = 'cid = '.$cid.' and deviceid = '.$deviceid.' and pdeviceid = '.$pdeviceid.' and md5_str ="'.$md5_str.'"
    							      and createtime >='.date('Y-d-m H:i:s',($this->current_time - 3600));
    
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
     * 获取指令列表
     * @param unknown $deviceid
     * @param number $page_no
     * @param number $page_size
     * @return multitype:number multitype: unknown
     */
    public function get_cmd_list($deviceid,$page_no = 1,$page_size = 30){
    	
    	$list_array = array('total_count'=>0,'list'=>array());
    	$deviceid = floatval($deviceid);
    	if($deviceid > 0){
    	
    		$group_by = '';    		
    		$join_str = ' as c left join '.$this->device_table.' as d on d.id = c.pdeviceid ';
    		$field_str = 'c.cid,c.status,c.executiontime,c.createtime,d.name as device_name,d.mac';
    		$where = array('c.deviceid'=>$deviceid);
    		$total_count = $this->get_list_count($group_by = '',$where,$join_str,'');
    		if($total_count > 0){
    			 
    			$order_by = 'c.id';
    			$order_way = 'desc';
    			$result = $this->get_page_list($page_no,$page_size,$order_by,$order_way,$group_by,$where,$join_str,$field_str);
    			if(check_array_valid($result)){
    				
    				$list = $result;
    				$cmd_name_array = self::get_cmd_name_array();
    				foreach($result as $key=>$value){
    					
    					$cmd_type_id = $value['cid'];
    					$list[$key]['cmd_name'] = isset($cmd_name_array[$cmd_type_id]) ? $cmd_name_array[$cmd_type_id] : '';
    				}
    				$list_array['list'] = $list;
    			}
    			
    			$list_array['total_count'] = $total_count;
    		}    		
    	}
    	return $list_array;
    }
    
    public static function get_cmd_name_array(){
    	
    	$cmd_name_array = array();
    	$cmd_name_array[1] = L('Location');
    	$cmd_name_array[2] = L('Alarm');
    	$cmd_name_array[3] = L('LockScreen');
    	$cmd_name_array[4] = L('LockScreenTxt');    

    	return $cmd_name_array;
    }
}