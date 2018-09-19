<?php

namespace Home\Model;
use Think\Model;

class UserInfoViewModel extends Model {
		
	public function __construct($name='',$tablePrefix='',$connection='') {
		
		$this->dbName = 'db_user';
		$this->tableName = C('DB_PREFIX').'user_info_view';		
		$this->trueTableName = $this->tableName;
		parent::__construct($name,$tablePrefix,$connection);
		$this->pk = 'user_id';
	}
	
	//获取用户信息
	public function get_info($id,$field = ''){

		$info_array = array();
		$id = intval($id);
		if($id > 0){
			
			$id_name = $this->pk;
			if(!empty($field)){
				
				$this->field($field);
			}
			$result = $this->where ($id_name.' = '.$id)->find();
			if(check_array_valid($result)){
					
				$info_array = $result;
			}
		}
		
		return $info_array;
	}
	
	/**
	 * 获取用户中心信息
	 * @param unknown $user_id
	 * @return Ambigous <multitype:, \Think\mixed, boolean, NULL, mixed, unknown, string, object>
	 */
	public function client_user_info($user_id){
		
		$field = 'user_name,user_nickname as nick_name,email,user_tel as tel,sex as gander,birthday,address,portrait_img_url as portrait_img,login_prev_datetime,login_last_datetime,login_prev_ip,login_last_ip,add_time,update_time';
		$info_array = $this->get_info($user_id,$field);
	    if(check_array_valid($info_array)){
	    		    	
	    	$email = $info_array['email'];
	    	$info_array['real_email'] = $email;
	    	if(strlen($email) > 0){	    		
	    		
	    		$email_array = explode('@',$email);
	    		$prefix_email = $email_array[0];
	    		$prefix_email_len = strlen($prefix_email);
	    		if($prefix_email_len >= 3){
	    			
	    			$prefix_email_pro = substr($prefix_email,0,-2).'**';
	    		}elseif($prefix_email_len > 1){
	    			
	    			$prefix_email_pro = substr($prefix_email,0,-1).'*';
	    		}else{
	    			
	    			$prefix_email_pro = '*';
	    		}
	    		
	    		$info_array['email'] = $prefix_email_pro.substr($email,$prefix_email_len);
	    	}
	    	$star = '****';
	    	$tel = $info_array['tel'];
	    	$info_array['real_tel'] = $tel;	 
	    	$tel_len = strlen($tel);   	
	    	if($tel_len > 0){
	    		
	    		$info_array['tel'] = $tel_len > 7 ? (substr($tel,0,3).'****'.substr($tel,7)) : (substr($tel,0,3).substr($star,0,$tel_len-3));
	    	}	    	
	    	$info_array['portrait_img'] = getFullUrl($info_array['portrait_img']);				
	    }
	    
		return $info_array;
	}
}
?>