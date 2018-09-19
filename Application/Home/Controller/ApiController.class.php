<?php
namespace Home\Controller;
use Think\Log;
use Think\Controller;
class ApiController extends Controller {

	public $is_bug = true;
	protected $is_spider = false;//是否为搜索引擎
	protected $log_path = '';//日志路径
	protected $log_type = '';//日志级别 
	protected $log_mess = '';//日志信息
	protected $request_data = array();//请求值
	protected $json_return_array = array();
	protected $cache_object = null;//缓存对象
	protected $is_verfiy_out = true;
	protected $cache_prefix = '';//模块缓存前缀
	protected $cache_expire = 86400;//缓存时间
	
	/**
	 * 架构函数 取得模板对象实例
	 * @access public
	 */
	public function __construct() {
		
		if(APP_DEBUG){
			
			parent::__construct();
		}else{
			
			$this->_initialize();
		}		
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \Think\Controller::__destruct()
	 */
	public function __destruct(){
		
		parent::__destruct();
		$this->is_bug = false;
		$this->log_path = '';
		$this->log_type = '';
		$this->log_mess = '';
		$this->request_data = array();		
		$this->cache_object = null;
		$this->is_verfiy_out = false;
		$this->cache_expire = 0;
	}
	
	/**
	 * 初始化数据
	 */
	public final function _initialize(){
		
		header('Content-type: text/html; charset=utf-8');
		
		//初始化公用输出值
		$this->json_return_array = array();
		$this->json_return_array['status'] = 0;
		$this->json_return_array['data'] = array();		
		
		//搜索引擎爬取当成页面不存在处理
		$check_is_spider = is_spider();
		if($check_is_spider){
				
			$this->_empty();
			exit;
		}
		
		//是否为日志调试模式
		if($this->is_bug){
			
			$this->log_type = Log::INFO;
			$this->log_path = LOG_PATH.'client_api_log.txt';
		}		
		$this->request();
		$this->init();
		$this->is_verfiy_out = true;
	}
	
	/**
	 * 其他初始化
	 */
	protected function init(){		
	}
	
	/**
	 * 请求处理
	 */
	protected final function request(){
			
		if(IS_POST){
			
			$this->request_data = $_POST;
		}
	}	
	
	/**
	 * 输出处理
	 */
	protected final function jsonOut(){
		
		$is_bug = boolval($this->is_bug);
		$request_data = $this->request_data;		
		$json_return_array = $this->json_return_array;		
		$json_out = json_encode($json_return_array);
		if($is_bug){
			
			$this->log_mess .= PHP_EOL.'request data:'.PHP_EOL.'Array(';
			if(check_array_valid($request_data)){
				
				foreach($request_data as $key =>$value){
				
					$str_value = '';
					if(is_object($value) || is_array($value)){
							
						$str_value = @serialize($value);
					}else{
							
						$str_value = strval($value);
					}
					$this->log_mess .= PHP_EOL.'["'.$key.'"]=>'.$str_value.','.PHP_EOL;
				}
			}			
			$this->log_mess .= PHP_EOL.')'.PHP_EOL;
			$this->log_mess .= PHP_EOL.'last out:'.PHP_EOL.$json_out.PHP_EOL.PHP_EOL;
			Log::write($this->log_mess,$this->log_type,'',$this->log_path);
			
			$this->log_path = '';
			$this->log_type = '';
			$this->log_mess = '';
		}
		$this->cache_object = null;
		$this->request_data = array();
		echo $json_out;
		exit;
	}
	
	/*
	 * 空方法调用
	 */
	public final function _empty($method = '',$args = array()){
		
		$this->json_return_array['status'] = INVALID_OPERATE;
		$this->jsonOut();
	}
	
	/**
	 * 清除日志
	 */
	public function clearLog(){
	
		$check = false;
		if($this->is_bug){
	
			$filename = $this->log_path;
			if(file_exists($filename)){
	
				$log_path = realpath($filename);
				$fh = @fopen($log_path,'w+');
				if($fh){
	
					flock($fh, LOCK_EX);
					fwrite($fh,'',0);
					flock($fh, LOCK_UN);
					fclose($fh);
					$check = true;
				}
			}
		}
		$str = 'It is failed';
		if($check){
	
			$str = 'It is ok';
		}
		echo $str;
		exit;
	}
	
	/**
	 * redis缓存连接
	 * @return Ambigous <NULL, unknown, mixed, \Think\mixed, object>
	 */
	protected function redisCacheConnect(){
		 
		$cache_object = $this->cache_object;
		if(is_null($cache_object) || !is_object($cache_object)){
	
			$check_cache = C('IS_REDIS_CACHE') ? true : false;
			if(!$check_cache){
					
				return $cache_object;
			}
			$cache_expire = intval($this->cache_expire);
			$cache_expire = $cache_expire > 0 ? $cache_expire : 0;
			if($cache_expire <= 0){
	
				$cache_expire = intval(C('REDIS_CACHE_EXPIRE'));
				$cache_expire = $cache_expire > 0 ? $cache_expire : 86400;
			}
			if($cache_expire != $this->cache_expire){
					
				$this->cache_expire = $cache_expire;
			}
			$redis_cache_prefix = C('REDIS_CACHE_PREFIX');
			$server_name = getServerName('localhost');
			$server_name_array = explode('.',$server_name);
			if(check_array_valid($server_name_array)){
	
				$redis_cache_prefix = $server_name_array[0];
			}
			$option_array = array(
					'type'=>'Redis',
					'host'=> C('REDIS_CACHE_HOST'),
					'port'=> C('REDIS_CACHE_PORT'),
					'timeout'=>false,
					'persistent'=>false,
					'expire'=>$cache_expire,
					'prefix'=> $redis_cache_prefix,
					'length'=>0);
			$cache_object = S($option_array);
			if(!is_null($cache_object)){
					
				$this->cache_object = $cache_object;
			}
		}
		return $cache_object;
	}
	
	/**
	 * 判定合法性
	 */
	public function verify(){
		
		$is_verfiy_out = boolval($this->is_verfiy_out);
		$this->json_return_array['status'] = INVALID_DATA;
		$request_data = $this->request_data;
		$token = filter_set_value($request_data,'token','','string');
		$stoken = filter_set_value($request_data,'stoken','','string');
		$cache_object = $this->redisCacheConnect();
		$token_object = new \Home\Model\TokenModel($cache_object);		
		$check = $token_object->check_valid($token,$stoken);
		if($check){
			
			$this->json_return_array['status'] = 0;
		}
		if($is_verfiy_out || !$check){
			
			$this->jsonOut();
		}else{
			
			return $token_object;
		}
	}
	
	/**
	 * 获取用户基本信息
	 */
	public function getUserBaseInfo(){
		
		$this->is_verfiy_out = false;
		$token_object = $this->verify();
		if(is_object($token_object)){
			
			$request_data = $this->request_data;
			$stoken = filter_set_value($request_data,'stoken','','string');
			$data_array = $token_object->get_user_info($stoken);
			$this->json_return_array['data'] = $data_array;
		}else{
			
			$this->_empty();
		}
		$this->jsonOut();
	}
}