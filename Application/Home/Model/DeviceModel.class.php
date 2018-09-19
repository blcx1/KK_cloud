<?php
/**
 * Created by PhpStorm.
 * User: inmyfree
 * Date: 2016/6/20
 * Time: 17:23
 */

namespace Home\Model;
class DeviceModel extends  \Home\Model\DefaultModel{

    /**
     * 初始化
     * @param string $name
     * @param string $tablePrefix
     * @param string $connection
     */
    public function __construct($name='',$tablePrefix='',$connection='') {

        $this->dbName = 'db_findmyphone';
        $this->tableName = C('DB_PREFIX').'device';
        $this->trueTableName = $this->tableName;
        $this->pk = 'id';
        parent::__construct($name,$tablePrefix,$connection);
    }

    /**
     * 通过md5值获取设备信息
     * @param unknown $md5_str
     * @return multitype:
     */
    public function findDevice($md5_str)
    {
        $device_info = array();    	
        $md5_str = trim($md5_str);
    	if(is_Md5($md5_str)){
    		
    		$field_str = 'id,name,ieme,expand_key,mac';
    		$where = 'md5_str = "'.$md5_str.'" ';
    		 		
    		$result = $this->field($field_str)->where($where)->find();
    		if(check_array_valid($result)){
    			
    			$device_info = $result;
    		}
    	}
    	
        return $device_info;
    }
    
    /**
     * 生成md5值
     * @param unknown $name
     * @param unknown $ieme
     * @param unknown $mac
     * @return string
     */
    public static function make_md5($name,$ieme,$mac){
    	
    	$md5_str = '';
    	$name = trim($name);
    	$ieme = trim($ieme);
    	$mac = trim($mac);
    	if(!($name == '' && $ieme == '' && $mac == '')){
    		
    		$md5_str = md5('n:'.$name.'&i:'.$ieme.'&m:'.$mac);
    	}    	
    	return $md5_str;
    }
}