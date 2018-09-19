<?php

namespace Home\Model;
use Think\Model;
use Home\Model\UserModel;
use Home\Model\UserViewModel;

class SendMessageModel extends Model {
	
	protected $current_time;
	protected $current_datetime;
	protected $current_date;
	protected $error_code_array = array();//错误编码
	protected $error_array = array();//错误信息
	
	/**
	 * 初始化
	 * @param string $name
	 * @param string $tablePrefix
	 * @param string $connection
	 */
	public function __construct($name='',$tablePrefix='',$connection='') {
		
		$this->dbName = 'db_user';
		$this->tableName = C('DB_PREFIX').'send_message';
		$this->trueTableName = $this->tableName;
		$this->pk = 'id';
		parent::__construct($name,$tablePrefix,$connection);
		$this->current_time = time();
		$this->current_datetime = date('Y-m-d H:i:s',$this->current_time);
		$this->current_date = date('Y-m-d',$this->current_time);
	}
	
	/**
	 * 获取信息
	 * @param unknown $id
	 * @return Ambigous <multitype:, \Think\mixed, boolean, NULL, mixed, unknown, string, object>
	 */
	public function get_info($id){
	
		$info_array = array();
		$id = intval($id);
		if($id > 0){
			$id_name = $this->pk;
				
			$result = $this->where ($id_name.' = '.$id)->find();
			if(check_array_valid($result)){
					
				$info_array = $result;
			}
		}
	
		return $info_array;
	}
	
	/**
	 * 单个发送
	 * @param unknown $id
	 * @param number $type
	 * @return boolean
	 */
	public function send_simple($id,$type = 0){
		
		$check = false;
		$id = intval($id);
		$type = intval($type) === 1 ? 1 : 0;
		$this->error_array = array();
		$this->error_code_array = array();
		if($id > 0){
			
			$str_where = 'id = '.$id.' and type = '.$type.' and is_delete = 0 and (is_send in(0,2))
		    and ((send_start_time = "0000-00-00 00:00:00" and send_end_time = "0000-00-00 00:00:00") or 
			(send_start_time <= "'.addslashes($this->current_datetime).'" and send_end_time >= "'.addslashes($this->current_datetime).'"))';
			$result = $this->field('id,user_id,user_name,tel,email,title,content,type,is_send')->where($str_where)->find();
			$check = $this->send_row($result);
			
		}
		return $check;
	}
	
	/**
	 * 通过一行数据进行发送
	 * @param unknown $result
	 * @return boolean
	 */
	public function send_row($result){
		
		$check = false;
		if(check_array_valid($result)){
		
			$id = intval($result['id']);
			$user_name = stripslashes($result['user_name']);
			$tel = stripslashes($result['tel']);
			$email = stripslashes($result['email']);
			$title = stripslashes($result['title']);
			$content = stripslashes($result['content']);
			$is_send = intval($result['is_send']);
			$type = intval($result['type']);
			
			$check = $type === 1 ? $this->send_SMS($user_name,$tel,$title,$content) : $this->send_mail($user_name,$email,$title,$content);
						
		    $error_message = '';
			$update_array = array();
			if(!$check){
					
				$is_send = $is_send === 0 ? 2 : 3;
				$error_array = $this->get_error_array();
				$error_message = check_array_valid($error_array) ? implode(';',$error_array):'未知错误';
				
			}else{
					
				$is_send = 1;
			}
			$update_array['is_send'] = $is_send;
			$update_array['error_message'] = $error_message;
			$update_array['send_time'] = addslashes($this->current_datetime);
			$check_update = $this->where('id = '.$id)->data($update_array)->save();
			if($check_update !== false){
				
				$check = true;
			}else{
				
				$this->error_code_array['result'] = ERROR_UPDATE_FAILED;
				$this->error_array['result'] = '更新失败';
			}
		}else{
			
			$this->error_code_array['result'] = INVALID_DATA;
			$this->error_array['result'] = '非法数据';
		}
		
		return $check;
	}
	
	/**
	 * 批量邮件发送
	 * @param number $is_send_type
	 * @param number $type
	 * @return boolean
	 */
	public function send_multi($is_send_type = 0,$type = 0){
		
		$check = false;
		$this->error_array = array();
		$this->error_code_array = array();
		$is_send_type_array = array(0,1,2);//0为对未发送进行发送；1为发送失败进行发送，2为发送所有未发送和发送失败
		$type = intval($type) === 1 ? 1 : 0;
		$is_send_type = intval($is_send_type);
		$is_send_type = in_array($is_send_type,$is_send_type_array) ? $is_send_type : 0;
		$str_where = 'type = '.$type.' and is_delete = 0 ';
		switch($is_send_type){
			case 0:
				
				$str_where .= ' and is_send = 0 ';
				break;
			case 1:
				
				$str_where .= ' and is_send = 2 ';
				break;
			case 2:
				
				$str_where .= ' and (is_send in(0,2)) ';
				break;
		}
		
		$str_where .= ' and ((send_start_time = "0000-00-00 00:00:00" and send_end_time = "0000-00-00 00:00:00") or
			(send_start_time <= "'.addslashes($this->current_datetime).'" and send_end_time >= "'.addslashes($this->current_datetime).'"))';
		$result = $this->field('id,user_id,user_name,tel,email,title,content,type,is_send')->where($str_where)->select();
		if(check_array_valid($result)){
			
			$check_prev = true;
			foreach($result as $value){
					
				$check_valid = $this->send_row($value);
				$check_prev = $check_prev && $check_valid;
			}
			$check = $check_prev;
		}
				
		return $check;
	}
	
	/**
	 * 邮件发送
	 * @param unknown $user_name
	 * @param unknown $email
	 * @param unknown $title
	 * @param unknown $content
	 * @return boolean
	 */
	public function send_mail($user_name,$email,$title,$content){
		
		$check = false;
		$user_name = trim($user_name);
		$email = trim($email);
		$title = trim($title);
		$content = trim($content);
		$email_len = strlen($email);
		if($email_len > 0 && is_email($email)){			
			
			$check = SendMail($email,$title,$content);
			
		}else{
			
			if($email_len <= 0){
				
				$this->error_code_array['result'] = ERROR_EMAIL_EMPTY;
				$this->error_array['result'] = '邮箱地址不能为空';
			}else{
				
				$this->error_code_array['result'] = ERROR_EMAIL_INVALID;
				$this->error_array['result'] = '邮箱地址不合法，请核对后输入，谢谢';
			}
			
		}
		
		return $check;
	}
	
	/**
	 * 发送短信
	 * @param unknown $user_name
	 * @param unknown $tel
	 * @param unknown $title
	 * @param unknown $content
	 * @return boolean
	 */
	public function send_SMS($user_name,$tel,$title,$content){
		
		$check = false;
		$user_name = trim($user_name);
		$tel = trim($tel);
		$title = trim($title);
		$content = trim($content);
		$tel_len = strlen($tel);
		if($tel_len > 0 && is_tel($tel)){
				
			//$check = SendMail($email,$title,$content);
			$check = true;
				
		}else{
				
			if($tel_len <= 0){
		
				$this->error_code_array['result'] = ERROR_TEL_EMPTY;
				$this->error_array['result'] = '手机号码不能为空';
			}else{
		
				$this->error_code_array['result'] = ERROR_TEL_INVALID;
				$this->error_array['result'] = '手机号码不合法，请核对后输入，谢谢';
			}
				
		}
		
		return $check;
	}
	
	/**
	 * 发送信息
	 * @param unknown $id
	 * @param number $type
	 * @return boolean
	 */
	public function sendMessage($id,$type = 0){
		 
		return $this->send_simple($id,$type);
	}
	
	/**
	 * 获取错误编码
	 * @return array
	 */
	public function get_error_code_array(){
	
		return (array)$this->error_code_array;
	}
	
	/**
	 * 获取错误信息
	 * @return array
	 */
	public function get_error_array(){
	
		return (array)$this->error_array;
	}
}
?>