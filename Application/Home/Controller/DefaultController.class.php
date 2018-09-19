<?php
namespace Home\Controller;
use Think\Controller;
abstract class DefaultController extends Controller {
	
	protected $is_mobile = false;
	protected $lang_array = array();//语言数组
	protected $crypt_type = 'des2';//加密里类型
	protected $crypt_key = 'cskxdtest';	
	protected $user_id = 0;//用户id
	protected $session_id = '';//用户登录session值
	protected $user_info = array();//用户相关信息
	protected $is_maintenance = false;//是否开启维护
	protected $is_over = false;//是否已经不再维护了
	protected $cache_object = null;//缓存对象
	protected $cache_expire = 86400;
	protected $is_login = false;//判断是否已经登录
	protected $is_spider = false;//是否为搜索引擎
	protected $is_perfect = false;//是否完善资料
	protected $is_ajax = false;//判别是否异步
	protected $check_login_type = 'msg';//登录检查类型	
	protected $igore_action_name_array = array('login','register');	

	/**
	 * 初始化数据
	 */
	public final function _initialize(){
		
		header('Content-type: text/html; charset=utf-8');		
		if(!defined('LANG_SET')){
		
			define('LANG_SET',C('DEFAULT_LANG',null,'zh-cn'));
		}		
		$is_mobile = is_mobile();
		if(!$is_mobile){
				
			$is_mobile = is_client_mobile();
		}
		$this->is_mobile = $is_mobile;
		$module_name = strtolower(MODULE_NAME);
		if(($is_mobile && $module_name == 'home') || (!$is_mobile && $module_name == 'mobile') ){
			
			$base_url = getBaseURL();
			if($is_mobile && $module_name == 'home'){
				
				$redirect_module_name = 'Mobile';
			}else{
				
				$redirect_module_name = 'Home';
			}
			$url = $base_url.$redirect_module_name.'/'.CONTROLLER_NAME.'/'.ACTION_NAME.'.html';
			$get_array = $_GET;
			if(check_array_valid($get_array)){
				
				$url .= '?';
				foreach($get_array as $get_key=>$get_value){
					
					$url .= $get_key.'='.$get_value.'&';
				} 
				substr($url,0,-1);
			}
			redirect($url,0,'');
			exit;
		}
		$this->maintenance();//维护处理
		$this->user_id = 0;
		$this->session_id = '';
		$this->user_info = array();
		$this->is_login = false;
		$is_spider = session('is_spider');		
		if($is_spider == 1){
	
			$this->is_spider = true;
		}else{
			
			if(is_null($is_spider)){
					
				$check_is_spider = is_spider();//搜索引擎不生成session				
				$this->is_spider = $check_is_spider ? true : false;				
				session('is_spider',($this->is_spider ? 1 : 0));
			}else{
				
				$this->is_spider = false;				
			}
		}		
		if(!$this->is_spider){
						
			$user_id = session('user_id');
			$user_id = intval($user_id);
			if($user_id > 0){
					
				$session_id = strval(session('session_id'));
				$session_id = !empty($session_id) && is_Md5($session_id) ? $session_id : '';
				if(empty($session_id)){
			
					$user_id = 0;
				}else{
			
					$user_info = session('user_info');
					$user_info = check_array_valid($user_info) ? $user_info : array();
					
					$this->is_login = true;
					$this->user_id = $user_id;
					$this->session_id = $session_id;
					$this->user_info = $user_info;				
				}
				$this->is_perfect = intval(session('is_perfect')) == 1 ? true : false;					
			}
			if($user_id <= 0){
					
				session('user_id',0);
				session('session_id','');
				session('user_info',array());
				session('is_perfect',0);
			}			
		}
		$file_name = LANG_PATH.LANG_SET.'/constant.php';
		if(is_file($file_name)){
			
			$lang_array = include $file_name;
			if(check_array_valid($lang_array)){
				
				$this->lang_array = $lang_array;
			}
		}		
		$this->is_ajax = IS_AJAX || (isset($_GET['is_ajax']) && intval($_GET['is_ajax']) == 1) ? true : false;		
		$this->init();
	}
	
	/*
	 * 首页不访问
	 */
	abstract public function index();
	
	/**
	 * 其他初始化
	 */
	protected function init(){
		
		if($this->is_ajax){
			
			$check_login_type = 'json';
		}else{
			
			$check_login_type = empty($this->check_login_type) ? 'msg' : $this->check_login_type;			
		}
		if(!$this->is_login || $check_login_type != 'json'){
			
			$return_json = array();
			$this->check_login($check_login_type,$return_json);
		}
	}
	
	/**
	 * 维护处理
	 */
	protected final function maintenance(){
		
		if($this->is_over || $this->is_maintenance){
			
			$this->display('Index/maintenance');
			exit;			
		}
	}
		
	/**
	 * 检查登录
	 * @param string $type
	 * @param unknown $return_json
	 * @return boolean
	 */
	protected function check_login($type = 'redirect',$return_json = array()){
		
		$check = $this->is_login;
		$type = trim($type);
		switch($type){
								
			case 'json':
				
				$is_login = $check ? 1 : 0;
				$url = !$check ? get_login_url($this->is_mobile) : '';
				$return_json = is_array($return_json) || is_object($return_json) ? $return_json : array();				
				if(is_array($return_json)){
					
					$return_json['is_login'] = $is_login;				
					$return_json['url'] = $url;
				}else{
					
					$return_json->is_login = $is_login;
					$return_json->url = $url;
				}
								
				echo json_encode($return_json);
				exit;
				break;
			case 'return':
			
				return $check;
				break;
			case 'msg':				
			case 'redirect':
			default:
				
				$igore_action_name_array = is_array($this->igore_action_name_array) ? $this->igore_action_name_array : array();
				$action_name = strtolower(ACTION_NAME);
				if(!$check && !in_array($action_name,$igore_action_name_array)){
					
					$url = get_login_url($this->is_mobile);					
					if($type == 'msg'){					
						
						$this->error(L('NotLoginMessTips'),$url,false);						
					}else{
						
						redirect($url,0,'');
					}					
				}
				break;				
		}
	}
	
	/*
	 * 释放
	 */
	public function __destruct(){
				
		$this->is_mobile = false;
		$this->lang_array = array();
		$this->crypt_type = '';
		$this->crypt_key = '';		
		$this->user_id = 0;
		$this->session_id = '';
		$this->is_over = false;
		$this->is_login = false;
		$this->is_spider = false;
		$this->is_perfect = false;
		$this->is_maintenance = false;
		$this->cache_expire = 0;
		$this->cache_object = null;		
		$this->user_info = array();
		$this->check_login_type = '';		
		$this->igore_action_name_array = array();		
	}
	
	/**
	 * 头部信息
	 * @param unknown $postion_name
	 */
	protected function head_common($header_array = array()){
		
		$loader_js_array = $loader_css_array = array();
		$header_array = check_array_valid($header_array) ? $header_array : array();
		if(isset($header_array['title'])){
			
			$title = $header_array['title'];
			unset($header_array['title']);
		}else{
			
			$title = L('DefaultMetaTitle');
		}
		
		if(isset($header_array['keywords'])){
			
			$keywords = $header_array['keywords'];
			unset($header_array['keywords']);
		}else{
			
			$keywords = L('DefaultMetaKeywords');
		}
		
		if(isset($header_array['description'])){
			
			$description = $header_array['description'];
			unset($header_array['description']);
		}else{
			
			$description = L('DefaultMetaDesc');
		}
		
		if(isset($header_array['loader_css'])){
			
			$loader_css_array = is_array($header_array['loader_css']) ? $header_array['loader_css'] : array('0'=>$header_array['loader_css']);
			unset($header_array['loader_css']);
		}
		
		if(isset($header_array['loader_js'])){
			$loader_js_array = is_array($header_array['loader_js']) ? $header_array['loader_js'] : array('0'=>$header_array['loader_js']);			
			unset($header_array['loader_js']);
		}
		
		$other_header_array = is_array($header_array) ? $header_array : array();
		$this->assign('title',$title);
		$this->assign('keywords',$keywords);
		$this->assign('description',$description);
		$this->assign('loader_js_array',$loader_js_array);
		$this->assign('loader_css_array',$loader_css_array);
		$this->assign('other_header_array',$other_header_array);
	}
	
	/**
	 * 底部信息
	 * @param unknown $footer_array
	 */
	protected function footer_common($footer_array = array()){	
		
		$footer_array = check_array_valid($footer_array) ? $footer_array : array();
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
	 * 空方法操作
	 */
	protected function _empty($method = '',$args = array()){
		
		$this->display('Index/404');
		exit;
	}
	
	/**
	 * 加载模块参数定义
	 * @param unknown $model_name
	 * @param unknown $argv_array
	 */
	protected final function load_model_defined($model_name,$argv_array = array()){
		
		$method_name = $model_name.'_model_defined';
		if(method_exists($this, $method_name)){
			
			$this->$method_name($argv_array);
		}
	}
	
	/**
	 * 语言模块参数定义
	 * @param unknown $argv_array
	 */
	protected function lang_model_defined($argv_array = array()){
		
		$cache_object = $this->redisCacheConnect();
		$lang_object = new \Home\Model\LanguageModel($cache_object);
			
		$lang_id = $lang_object->get_lang_id(LANG_SET);
		$lang_id = $lang_id > 0 ? $lang_id : 2;
		$lang_iso = $lang_object->get_lang_iso($lang_id);
		$iso_lang_list = $lang_object->get_value('iso_lang_list');
		$this->assign('lang_iso',$lang_iso);
		$this->assign('iso_lang_list',$iso_lang_list);
		$this->assign('lang_name',$iso_lang_list[$lang_iso]["name"]);
	}
	
	/**
	 * 用户信息模块参数定义
	 * @param unknown $argv_array
	 */
	protected function user_info_model_defined($argv_array = array()){
		
		$user_info = $this->user_info;
		$this->assign('user_info',$user_info);		
	}
	
	/**
	 * 左边悬浮插件模块参数定义
	 * @param unknown $argv_array
	 */
	protected function left_suspend_plugin_model_defined($argv_array = array()){
		
		$left_suspend_plugin_list = array();
		$control_name = strtolower(CONTROLLER_NAME);
		switch($control_name){
			
			case 'index':
				$control_name = 'home';
				break;
			case 'contacts':
				$control_name = 'contact';
				break;
			default:
				break;
			
		}
		
		$base_url = getBaseURL();
		$left_suspend_plugin_list[0] = array(
				'url'=>get_home_url($this->is_mobile),
				'name'=>'home',
				'display_name'=>L('Home')
		);
		$left_suspend_plugin_list[1] = array(
				'url'=>$base_url.MODULE_NAME.'/Contacts/index.html',
				'name'=>'contact',
				'display_name'=>L('Contact')
		);
		$left_suspend_plugin_list[2] = array(
				'url'=>$base_url.MODULE_NAME.'/Sms/index.html',
				'name'=>'sms',
				'display_name'=>L('Sms')
		);
		$left_suspend_plugin_list[3] = array(
				'url'=>$base_url.MODULE_NAME.'/Gallery/index.html',
				'name'=>'gallery',
				'display_name'=>L('Gallery')
		);
		$left_suspend_plugin_list[4] = array(
				'url'=>$base_url.MODULE_NAME.'/Note/index.html',
				'name'=>'note',
				'display_name'=>L('Note')
		);
		$left_suspend_plugin_list[5] = array(
				'url'=>$base_url.MODULE_NAME.'/Phone/index.html',
				'name'=>'phone',
				'display_name'=>L('Phone')
		);
		
		$left_suspend_plugin_list[6] = array(
    				'url'=>$base_url.MODULE_NAME.'/CallRecord/index.html',
    				'name'=>'callrecord',
    				'display_name'=>L('CallRecord')
    		);
		
		$this->assign('left_suspend_plugin_list',$left_suspend_plugin_list);
		$this->assign('control_name',$control_name);
	}
	
	/**
	 * 通过状态码获取对应语言
	 * @param unknown $status
	 * @return Ambigous <multitype:, number>
	 */
	protected final function get_lang_value($status){
		
		$status = intval($status);
		$value = isset($this->lang_array[$status]) ? $this->lang_array[$status] : $status;		
		return $value;
	}
}