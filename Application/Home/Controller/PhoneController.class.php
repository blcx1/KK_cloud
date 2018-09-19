<?php
/**
 * 查找设备
 * @author kxd
 *
 */
namespace Home\Controller;
use Think\Controller;
use Home\Model\CommandModel;
use Home\Model\LocationModel;
use Home\Model\UserDeviceModel;
class PhoneController extends \Home\Controller\DefaultController {
	
	protected $page_size = 30;
	protected $check_login_type = 'msg';
	protected $igore_action_name_array = array();
	
	public function __destruct(){
		
		parent::__destruct();
		$this->page_size = 0;		
	}
	
	/**
	 * 其他初始化
	 */
	protected function init(){
		parent::init();
		if(!$this->is_ajax){
			if($this->is_login){
				$this->load_model_defined('user_info',array());//用户信息模块参数定义
			}
			$igore_action_name = array();
			$action_name = strtolower(ACTION_NAME);
			if(!in_array($action_name,$igore_action_name)){
				$this->load_model_defined('left_suspend_plugin',array());//左边悬浮插件模块参数定义
			}
			$this->load_model_defined('lang',array());//语言模块参数定义
		}
	}
	
	/**
	 * 查找设备
	 */
    public function index(){

    	$page_no = 1;
    	$page_size = intval($this->page_size);
    	$page_size = $page_size > 0 ? $page_size : 30;
    	$header_array = array();
    	$footer_array = array();
    	$device_info = array();
    	$default_device_id = 0;
    	$header_array['loader_css'][0] = '/css/home.css';
    	$header_array['loader_css'][1] = '/css/phone.css';  
    	if($this->is_mobile){
    	
    		$header_array['loader_css'][2] = '/css/mobile.css';    		
    	}  	
    	$header_array['loader_js'][0] ='/js/phone.js';    	
    	$header_array['title'] = L('Phone');
    	$this->head_common($header_array);
    	$this->footer_common($footer_array);
    	    
    	$userDeviceModel = new UserDeviceModel();
    	$result = $userDeviceModel->get_phone_list($this->user_id,$page_no,$page_size);
    	$list_array = $result['list'];
    	$total_count = $result['total_count'];   	
    	if($total_count > 0){
    		
    		$device_info = reset($list_array);    		
    		$default_device_id = $device_info['deviceid'];
    	} 
    	$this->assign('default_device_id',$default_device_id);
    	$this->assign('page_size',$page_size);
    	$this->assign('total_count',$total_count);
    	$this->assign('list_array',$list_array);
    	$this->assign('device_info',$device_info);
    	$this->display('index');
    }
    
    /**
     * 获取设备
     */
    public function getDeviceList(){
    	
    	$return_json = array('status'=>0,'total_count'=>0,'list'=>array(),'result'=>'');
    	if($this->is_login){
    		
    		$page_size = intval($this->page_size);
    		$page_size = $page_size > 0 ? $page_size : 30;
    		$page_no = filter_set_value($_POST,'p',1,'int');
    		
    		$userDeviceModel = new UserDeviceModel();
    		$return_json = $userDeviceModel->get_phone_list($this->user_id,$page_no,$page_size);
    		$return_json['status'] = 1;
    	}
    	$this->check_login('json',$return_json);
    }
    
    /**
     * 指令发送
     */    
    public function cmdSend(){

    	$return_json = array('status'=>0,'push'=>false,'result'=>'');
    	if($this->is_login){

    		$deviceid = filter_set_value($_POST,'id',0,'float');//设备id    		
    		$content = filter_set_value($_POST,'content','','string');//推送内容
    		$cmd_type_id = filter_set_value($_POST,'ctid',0,'int');//推送类型 

    		$user_devide_model = new UserDeviceModel();
    		$push_command_device = $user_devide_model->find_user_device($deviceid,$this->user_id);    		
    		if(check_array_valid($push_command_device)){//设备和执行指令设备用户匹配
    			    			
    			$command_model = new CommandModel();
    			$chanleid = $push_command_device['chanleid'];
    			$result =  $command_model->add_command($cmd_type_id,$deviceid,$chanleid,$deviceid,$chanleid,$content);
    			$data_array = $result['list_array'];
    			if($data_array['status'] == 1){
    				    			    
    				vendor('BaiduPush.sdk');
    				$command = $result['command_data'];
    				$pushObj = new \PushSDK(C('BAIDU_PUSH_APIKEY'),C('BAIDU_PUSH_SECRETKEY'));
    				if(isset($command['id']) && isset($command['pchanleid']) && $command['id'] && $command['pchanleid']){
    					
    					$message_array = array (
    							'command_id' => $command['id'],
    							'type_id'=> $command['cid'],
    					);
    					$message_opts = array(
    							'msg_type' => 0,// int 消息类型，1表示通知，0表示透传
    							'msg_expires' => 60,//int 过期时间，默认 3600 x 5 秒(5小时)，仅android有效。
    					);
    					$check_push =  $pushObj->pushMsgToSingleDevice($command['pchanleid'],$message_array,$message_opts);
    					if($check_push){
                                            //$file=dirname(__FILE__).'/../../Runtime/Logs/cmdsend.txt';		
                                            //file_put_contents($file,json_encode($message_array));  						
    						$return_json['status'] = 1;
    						$return_json['push'] = true;
    					}else{
    						
    						$return_json['status'] = 0;
    						$return_json['push'] = false;
    					}   					
    				}  
    				if($return_json['status'] == 0){
    					
    					$return_json['result'] = $this->get_lang_value(ERROR_OPERATE_FAILED);        					
    				}				
    			}else{
    				
    				$error_code_array = $command_model->get_error_code_array();
    				foreach($error_code_array as $error_code){
    					
    					$return_json['result'] = $this->get_lang_value($error_code);
    				}
    			}
    		}else{
    			
    			$return_json['result'] = $this->get_lang_value(DEVICE_NOTFOUND);    			
    		}
    	}
    	$this->check_login('json',$return_json);
    }
    
    /**
     * 获取定位日志
     */
    public function getLocationLog(){
    	
    	$return_json = array('status'=>0,'total_count'=>0,'list'=>array(),'result'=>'');
    	if($this->is_login){
    		
    		$user_id = $this->user_id;
    		$page_size = intval($this->page_size);
    		$page_size = $page_size > 0 ? $page_size : 30;
    		$page_no = filter_set_value($_POST,'p',1,'int');
    		$deviceid = filter_set_value($_POST,'id',0,'float');//设备id
    		$userdeviceModel = new UserDeviceModel();
    		$check_exists = $userdeviceModel->is_user_device($user_id,$deviceid);
    		if($check_exists){
    			
    			$return_json['status'] = 1;
    			$location_object = new LocationModel();
    			$list_array = $location_object->get_location_list($deviceid,$page_no,$page_size);
    			$return_json['list']= $list_array['list'];
    			$return_json['total_count']= $list_array['total_count'];
    			
    		}else{
    			
    			$return_json['status'] = $this->get_lang_value(INVALID_DATA);
    		}
    		
    		
    	}
    	$this->check_login('json',$return_json);
    }
    
    /**
     * 获取指令日志
     */
    public function getCmdLog(){
    	
    	$return_json = array('status'=>0,'total_count'=>0,'list'=>array(),'result'=>'');
    	if($this->is_login){
    		 
    		$user_id = $this->user_id;
    		$page_size = intval($this->page_size);
    		$page_size = $page_size > 0 ? $page_size : 30;
    		$page_no = filter_set_value($_POST,'p',1,'int');
    		$deviceid = filter_set_value($_POST,'id',0,'float');//设备id
    		$userdeviceModel = new UserDeviceModel();
    		$check_exists = $userdeviceModel->is_user_device($user_id,$deviceid);
    		if($check_exists){
    			 
    			$return_json['status'] = 1;
    			$command_model = new CommandModel();
    			$list_array = $command_model->get_cmd_list($deviceid,$page_no,$page_size);
    			$return_json['list']= $list_array['list'];
    			$return_json['total_count']= $list_array['total_count'];
    		}else{
    			 
    			$return_json['status'] = $this->get_lang_value(INVALID_DATA);
    		}    		
    	}
    	$this->check_login('json',$return_json);
    }
        
    /**
     * 清空数据
     */
    public function clearAll(){
    	
    	$return_json = array('status'=>0,'result'=>'');
    	if($this->is_login){
    		 
    		$user_id = $this->user_id;
    		$deviceid = filter_set_value($_POST,'id',0,'float');//设备id   		
    		
    		$userdeviceModel = new UserDeviceModel();
    		$check_exists = $userdeviceModel->is_user_device($user_id,$deviceid);
    		if($check_exists){
    			
    			$where = array('deviceid'=>$deviceid);
    			$clear_type = filter_set_value($_POST,'t',0,'int');//清除的类型    			
    			 	
    			$clear_object = $clear_type == 0 ? new LocationModel() : new CommandModel();
    			$result = $clear_object->info_force_delete($where,true);
    			if($result){
    				
    				$return_json['status'] = 1;
    			}else{
    				
    				$return_json['result'] = $this->get_lang_value(ERROR_DEL_FAILED);
    			}    			
    		}else{
    			
    			$return_json['result'] = $this->get_lang_value(INVALID_DATA);
    		} 		
    	}
    	$this->check_login('json',$return_json);
    }
    
    /**
     * 地图
     */
    public function map(){
            	
    	$is_cn = 0;
    	$longitude = '';
    	$latitude = '';
    	$deviceid = filter_set_value($_GET,'id',0,'float');//设备id        
    	$location_object = new LocationModel();
    	$result = $location_object->find_device_location($this->user_id,$deviceid);
    	if(check_array_valid($result) && $result['status'] == DEVICE_LOCATION_SUCCESS){
    		
    		$longitude = $result['longitude'];
    		$latitude = $result['latitude'];
    		$is_cn = intval($result['countryflag']);
        }else{    
            $map_zoom = 1;
        }
    	
    	$check_baidu = $is_cn == 0 ? false : true;
    	$map_zoom = isset($map_zoom) ? $map_zoom :($check_baidu ? 20 : 16);
    	
    	$title = L('MapLocation');    	
    	$this->assign('title',$title);
    	$this->assign('map_zoom',$map_zoom);
    	$this->assign('longitude',$longitude);
    	$this->assign('latitude',$latitude);
    	$this->assign('check_baidu',$check_baidu);
    	$this->display('map');
    }
}