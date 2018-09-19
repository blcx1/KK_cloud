<?php
/**
 * 通话记录
 *
 */
namespace Home\Controller;
use Think\Controller;
class CallRecordController extends \Home\Controller\DefaultController {
	
	protected $page_size = 30;
	protected $check_login_type = 'msg';
	protected $igore_action_name_array = array();
	protected $call_record_model_object = null;
	
	public function __destruct(){
	
		parent::__destruct();
		$this->page_size = 0;
		$this->call_record_model_object = null;
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

		if($this->is_login){
				
			$cache_object = $this->redisCacheConnect();
			$this->call_record_model_object = new \Home\Model\CallrecordModel($cache_object);
		}
	}
	
	/**
	 * 通话记录组列表
	 */
	public function index(){
		 
		$page_no = 1;
		$page_size = intval($this->page_size);
        $page_size = $page_size > 0 ? $page_size : 30;
		$header_array = array();
		$footer_array = array();
		$header_array['loader_css'][0] = '/css/home.css';
		$header_array['loader_css'][1] = '/css/indexhome.css';
		$header_array['loader_css'][2] = '/css/animation.css';
		$header_array['loader_css'][3] = '/css/call_record.css';
		if($this->is_mobile){
			
			$header_array['loader_css'][4] = '/css/mobile.css';
			$header_array['loader_css'][5] = '/css/m.callRecord.css';
		}		
		$header_array['loader_js'][0] ='/js/call_record.js';
		$header_array['title'] = L('CallRecord');
		$this->head_common($header_array);
		$this->footer_common($footer_array);		 
		$user_id = $this->user_id;
	
		$call_record_model_object = $this->call_record_model_object;
		$use_call_record_result = $call_record_model_object->get_call_record_group($user_id,0,$page_no,$page_size);
		$recy_call_record_result = $call_record_model_object->get_call_record_group($user_id,1,$page_no,$page_size);
		$this->assign('page_size',$page_size);
		$this->assign('use_call_record_total_count',$use_call_record_result['total_count']);
		$this->assign('recy_call_record_total_count',$recy_call_record_result['total_count']);
		$this->assign('use_call_record_list',$use_call_record_result['list']);
		$this->assign('recy_call_record_list',$recy_call_record_result['list']);


		$this->display();
	}
	
	/**
	 * 获取分组列表
	 */
	public function getGroupList(){
		 
		$return_json = array('status'=>0,'total_count'=>0,'list'=>array());
		if($this->is_login){
			 
			$page_size = intval($this->page_size);
    	    $page_size = $page_size > 0 ? $page_size : 30; 	
			$page_no = filter_set_value($_POST,'p',1,'int');
			$is_recy = filter_set_value($_POST,'is_recy',0,'int');
			$is_recy = $is_recy == 1 ? 1 : 0;
	
			$return_json = $this->call_record_model_object->get_call_record_group($this->user_id,$is_recy,$page_no,$page_size);
			$return_json['status'] = 1;
		}
	
		$this->check_login('json',$return_json);
	}
	
	/**
	 * 获取某个用户某个分组列表
	 */
	public function getCallRecordList(){
		 
		$return_json = array('status'=>0,'total_count'=>0,'list'=>array());
		if($this->is_login){
	
	        $page_size = intval($this->page_size);
    		$page_size = $page_size > 0 ? $page_size : 30;	
			$gourp_id = filter_set_value($_POST,'id',0,'int');
			$page_no = filter_set_value($_POST,'p',1,'int');
			$is_recy = filter_set_value($_POST,'is_recy',0,'int');
			$is_recy = $is_recy == 1 ? 1 : 0;
			$return_json = $this->call_record_model_object->get_gourp_call_record($this->user_id,$gourp_id,$page_no,$page_size,$is_recy);
			$return_json['status'] = 1;
		}
		$this->check_login('json',$return_json);
	}
	
	/**
	 * 公共ajax处理
	 * @param unknown $method_name
	 */
	protected function ajax_common_process($method_name){
	
		$return_json = array('status'=>0,'id_success_list'=>array(),'result'=>'');
		if($this->is_login){
	
			$method_name = trim($method_name);
			$call_record_model_object = $this->call_record_model_object;
			if(method_exists($call_record_model_object,$method_name)){
				 
				$group_id_array = filter_set_value($_POST,'gid',array(),'array');
				$id_array = filter_set_value($_POST,'id',array(),'array');
				$id_success_list = $call_record_model_object->$method_name($this->user_id,$group_id_array,$id_array);
				$return_json['id_success_list'] = $id_success_list;
				if(count($id_success_list) > 0){
					 
					$return_json['status'] = 1;
				}else{
					 
					$error_code_array = $call_record_model_object->get_error_code_array();
					foreach($error_code_array as $error_code){
	
						$return_json['result'] = $this->get_lang_value($error_code);
					}
				}
			}else{
				 
				$return_json['result'] = $this->get_lang_value(METHOD_NOT_EXISTS);
			}
		}
		$this->check_login('json',$return_json);
	}
	
	/**
	 * 移入回收站
	 */
	public function recycle(){
		 
		$method_name = 'recycle_call_record';
		$this->ajax_common_process($method_name);
	}
	
	/**
	 * 还原数据
	 */
	public function recover(){
		 
		$method_name = 'recover_call_record';
		$this->ajax_common_process($method_name);
	}
	
	/**
	 * 彻底删除
	 */
	public function delete(){
	
		$method_name = 'delete_call_record';
		$this->ajax_common_process($method_name);
	}

	/**
	 * 搜索
	 */
	public function search(){
	    	
		$return_json = array('status'=>0,'total_count'=>0,'list'=>array());
		if($this->is_login){
		    		 
			$page_size = intval($this->page_size);
			$page_size = $page_size > 0 ? $page_size : 30;
			$page_no = filter_set_value($_POST,'p',1,'int');
			$is_recy = filter_set_value($_POST,'is_recy',0,'int');
			$is_recy = $is_recy == 1 ? 1 : 0;
			$search_name = trim(filter_set_value($_POST,'sk','','string'));    		
			$search_name = str_replace('+',' ',$search_name);
			$search_name = strip_tags($search_name);
			$replace_array = array('*','%','=','"','\"','\'',"'");
			$search_name_valid_str = str_replace($replace_array,'',$search_name);
			if(strlen($search_name_valid_str) > 0){
		    		
				if(strpos($search_name_valid_str,' ')){
		    		
					$inputVal_array = explode(' ',$search_name_valid_str);
					$search_name_valid_str = '';    		
					foreach($inputVal_array as $value){
		    		
						if(strlen($value) > 0){
		    		
							$search_name_valid_str .= ' '.$value;
						}
					}    				
					$search_name_valid_str = trim($search_name_valid_str);   				  				
				}
		
				if(strlen($search_name_valid_str) > 0){
		    				    					
					$extend_where = array();
					$extend_where['g.phone'] = array('like','%'.$search_name_valid_str.'%');    				
					$return_json = $this->call_record_model_object->get_call_record_group($this->user_id,$is_recy,$page_no,$page_size,$extend_where);    				
				}
			}
		    		
			$return_json['status'] = 1;
		}
		$this->check_login('json',$return_json);
	}
    	
	/**
	 * 清空数据
	 */
	public function clearAll(){
		 
		$return_json = array('status'=>0,'result'=>'');
		if($this->is_login){
			 
			$call_record_model_object = $this->call_record_model_object;
			$check = $call_record_model_object->client_clearAll($this->user_id);
			if(!$check){
	
				$error_code_array = $call_record_model_object->get_error_code_array();
				foreach($error_code_array as $error_code){
	
					$return_json['result'] = $this->get_lang_value($error_code);
				}
			}else{
				 
				$return_json['status'] = 1;
			}
		}
		$this->check_login('json',$return_json);
	}	

}