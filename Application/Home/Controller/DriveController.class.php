<?php
/**
 * 云盘
 * @author kxd
 *
 */
namespace Home\Controller;
use Think\Controller;
class DriveController extends \Home\Controller\DefaultController {
	
	protected $check_login_type = 'msg';
	protected $igore_action_name_array = array();
	
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
		}
	}
	
	/**
	 * 云盘
	 */
    public function index(){
    	  	
        $this->display('drive');
    }      
}