<?php
/**
 * Token模型
 * @author kivenpc pcttcnc2007@126.com
 * @date 2016-09-13
 *
 */
namespace Home\Model;
class TokenModel{
	
	protected $table = '';
	protected $is_cache = false;
	protected $current_time = 0;
	protected $cache_prefix = 'token_';//模块缓存前缀
	protected $expire_time = 900;//有效期秒数
	protected $cache_object = null;
	protected $db_cache = null;
	protected $info_array = array();
	protected static $confounded_array = array('3'=>'1','7'=>'0','13'=>'3','9'=>'2');//混淆数组
	
	/**
	 * 初始化
	 */
	public function __construct($cache_object = null) {
		 
		if(!is_null($cache_object) && is_object($cache_object)){
			
			$this->is_cache = true;
			$this->cache_object = $cache_object;
		}else{
						
			$this->is_cache = false;						
			$this->current_time = time();			
			$db_name = 'db_cloud';
			$db_prifix = C('DB_PREFIX');
			$this->table = $db_name.'.'.$db_prifix.'token';			
			$this->db_cache = M();
		}
	}
	
	/**
	 * 获取stoken
	 * @param unknown $token
	 * @return string
	 */
	public static function get_stoken($token){
	
		$stoken = '';
		$token = trim($token);
		if(is_Md5($token)){
	
			$stoken = md5(md5(strval(strtotime()).$token).strval(rand(1000000,9999999)));
		}
		return $stoken;
	}
	
	/**
	 * 混淆stoken写法
	 * @param unknown $stoken
	 * @return string
	 */
	public static function get_confounded_stoken($stoken){
		
		$confounded_stoken = '';
		$stoken = trim($stoken);
		if(is_Md5($stoken)){
			
			$start = 0;
			$stoken_array = array();
			$confounded_array = self::$confounded_array;
			foreach($confounded_array as $key=>$value){
				
				$len = intval($key);
				$value = intval($value);
				$stoken_array[$value] = substr($stoken,$start,$len);
				$start += $len;
			}			
			ksort($stoken_array);
			$confounded_stoken = implode('',$stoken_array);			
		}
		return $confounded_stoken;
	}
	
	/**
	 * 混淆stoken
	 * @param unknown $confounded_stoken
	 * @return string
	 */
	public static function get_confounded_stoken_recover($confounded_stoken){
		
		$stoken_recover = '';
		$confounded_stoken = trim($confounded_stoken);
		if(is_Md5($confounded_stoken)){
			
			$i = 0;
			$start = 0;
			$confounded_stoken_array = array();
			$confounded_recover_array = array();			
			$confounded_stoken_recover_array = array();
			$confounded_array = self::$confounded_array;			
			foreach($confounded_array as $key=>$value){
				
				$len = intval($key); 
				$value = intval($value);
				$confounded_stoken_array[$value] = $len;
				$confounded_recover_array[$value] = $i;
				$i++;
			}
			ksort($confounded_stoken_array);			
			foreach($confounded_stoken_array as $key=>$value){
				
				$confounded_stoken_recover_array[$confounded_recover_array[$key]] = substr($confounded_stoken,$start,$value);				
				$start += $value;
			}			
			ksort($confounded_stoken_recover_array);
			$stoken_recover = implode('',$confounded_stoken_recover_array);
		}
		return $stoken_recover;
	}
	
	/**
	 * 释放
	 **/
	public function __destruct(){
		
		$this->clear_expire();
		$this->table = '';
		$this->cache_prefix = '';
		$this->expire_time = 0;
		$this->cache_object = null;
		$this->db_cache = null;
		$this->info_array = array();
		self::$confounded_array = array();
	}
	
	/**
	 * 过期处理
	 */
	public function clear_expire(){
		
		if(!$this->is_cache){
			
			$sql = 'delete from '.$this->table.' where `expire` <= '.$this->current_time;
			$this->db_cache->execute($sql,false);	
		}
	}
	
	/**
	 * 判别合法性
	 * @param unknown $token
	 * @param unknown $stoken
	 * @return boolean
	 */
	public function check_valid($token,$stoken){
		
		$check = false;
		$token = trim($token);
		$stoken = trim($stoken);
		if(is_Md5($token) && is_Md5($stoken)){
			
			$info_array = $this->get_info($stoken);
			if(check_array_valid($info_array)){
				
				$source_token = isset($info_array['token']) ? trim($info_array['token']) : '';				
				$check = $source_token == $token ? true : false;
			}
		}
		return $check;
	}	
	
	/**
	 * 获取stoken的值
	 * @param unknown $stoken
	 * @return Ambigous <multitype:, unknown>
	 */
	public function get($stoken){
		
		$info_array = array();
		$is_cache = boolval($this->is_cache);
		$token_info = '';
		if($is_cache){
			
			$cache_name = $this->cache_prefix.$stoken;
			$token_info = $this->cache_object->get($cache_name);
		}else{
			
			$sql = 'select `stoken_data` from '.$this->table.' where `stoken` = "'.$stoken.'" limit 1 ';
			$result = $this->db_cache->query($sql);			
			if(check_array_valid($result)){
				
				$token_info = $result[0]['stoken_data'];
			}			 
		}
		if(!empty($token_info)){
			
			$token_info = trim($token_info);
			$result = @json_decode(base64_decode(urldecode($token_info)),true);		
			if(check_array_valid($result)){
				
				$info_array = $result;
			}
		}
		return $info_array;
	}
	
	/**
	 * 设置stoken的值
	 * @param unknown $stoken
	 * @param unknown $token_value
	 * @return boolean
	 */
	public function set($stoken,$token_value = array()){
		
		$check = false;
		$stoken = trim($stoken);
		if(is_Md5($stoken) && is_array($token_value)){
						
			$is_cache = boolval($this->is_cache);
			$expire_time = intval($this->expire_time);
			if($expire_time <= 0){
					
				$expire_time = 900;
			}
			$token_info = urlencode(base64_encode(json_encode($token_value)));
			if($is_cache){
					
				$cache_name = $this->cache_prefix.$stoken;
				$result = $this->cache_object->set($cache_name,$token_info,$expire_time);
				$check = $result ? true : false;
			}else{
				
				$expire_time += $this->current_time;
				$sql = 'insert into '.$this->table.' (`stoken`,`expire`,`stoken_data`) values ("'.$stoken.'",'.$expire_time.',"'.$token_info.'") ON 
						duplicate key update `expire` = '.$expire_time.',`stoken_data` = "'.$token_info.'"';
				
				$result = $this->db_cache->execute($sql,false);
				$check = $result === false ? false : true;
			}				
		}
				
		return $check;
	}
	
	/**
	 * 获取相关信息
	 * @param unknown $stoken
	 * @return multitype:
	 */
	public function get_info($stoken){
			
		$info_array = array();
		$stoken = trim($stoken);
		$info_arr = $this->info_array;		
		if(isset($info_arr[$stoken]) && is_array($info_arr[$stoken])){
		 	
			$info_array = $info_arr[$stoken];
		}else{
			
			$info_array = $this->get($stoken);			
		}
			 
		return $info_array;
	}
	
	/**
	 * 获取用户信息
	 * @param unknown $stoken
	 * @return Ambigous <multitype:, unknown>
	 */
	public function get_user_info($stoken){
		
		$user_info = array();
		$stoken = trim($stoken);
		$info_array = $this->get_info($stoken);
		if(isset($info_array['user_info']) && is_array($info_array['user_info'])){

			$user_info = $info_array['user_info'];
		}
		return $user_info;
	}
}
?>