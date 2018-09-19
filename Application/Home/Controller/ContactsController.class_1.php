<?php
/**
 * 联系人
 * @author kxd
 *
 */
namespace Home\Controller;
use Think\Controller;
use Home\Model\ContactsBaseInfoModel;
class ContactsController extends \Home\Controller\DefaultController {
	
	protected $page_size = 15;
	protected $check_login_type = 'msg';
	protected $igore_action_name_array = array();
	protected $contacts_base_info_object = null;
	
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
		if($this->is_login){
			
			$cache_object = $this->redisCacheConnect();
			$this->contacts_base_info_object = new \Home\Model\ContactsBaseInfoModel($this->user_id,false,$this->cache_object);
		}
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \Home\Controller\DefaultController::__destruct()
	 */
	public function __destruct(){
		
		parent::__destruct();
		$this->page_size = 0;
		$this->contacts_base_info_object = null;
	}
	
	/*
     * 通讯记录 
     */
	public function index(){
		
		$page_no = 1;
		$page_size = intval($this->page_size);
		$page_size = $page_size > 0 ? $page_size : 30;
		$header_array = array();
		$footer_array = array();
		
		$header_array['loader_css'][0] = '/css/home.css';
		$header_array['loader_css'][1] = '/css/animation.css';
		$header_array['loader_css'][2] = '/css/contact.css';
		if($this->is_mobile){
		
			$header_array['loader_css'][3] = '/css/mobile.css';
		}
		$header_array['loader_js'][0] ='/js/contact.js';
		$header_array['loader_js'][1] ='/lib/My97DatePicker/WdatePicker.js';
		$header_array['title'] = L('Contact');
		$this->head_common($header_array);
		$this->footer_common($footer_array);
		
		$result = $this->contacts_base_info_object->get_web_list(0,$page_no,$page_size,'');
		$this->assign('page_size',$page_size);
		$this->assign('list_array',$result['list']);
		$this->assign('check_first_spell',1);
		$this->assign('total_count',$result['total_count']);
    	$this->load_model_defined('lang',array());//语言模块参数定义
        $this->display('contact');	
	}
	
	/**
	 * 获取列表
	 */
	public function getList(){
		
		$return_json = array('status'=>0,'total_count'=>0,'list'=>array(),'result'=>'');
		if($this->is_login){
		
			$page_size = intval($this->page_size);
			$page_size = $page_size > 0 ? $page_size : 30;
			$page_no = filter_set_value($_POST,'p',1,'int');
			$is_recy = filter_set_value($_POST,'is_recy',0,'int');
						
			$result = $this->contacts_base_info_object->get_web_list($is_recy,$page_no,$page_size,'');
			$return_json['status'] = 1;
			$return_json['list'] = $result['list'];
			$return_json['total_count'] = $result['total_count'];
		}
		$this->check_login('json',$return_json);
	}
	
  	/**
  	 * 搜索
  	 */
	public function search(){
		
		$return_json = array('status'=>0,'total_count'=>0,'list'=>array(),'result'=>'');		
		if($this->is_login){
								
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
					
					$page_size = intval($this->page_size);
					$page_size = $page_size > 0 ? $page_size : 30;
					$page_no = filter_set_value($_POST,'p',1,'int');
					$is_recy = filter_set_value($_POST,'is_recy',0,'int');
					$page_no = $page_no > 0 ? $page_no : 1;
					$is_recy = $is_recy == 1 ? 1 : 0;
					
					$return_json['status'] = 1;
					$check_list = true;
					$check_sphinx = false;
					
					if(class_exists('\SphinxClient')){
						
						$check_sphinx = true;						
						$index_name = 'contacts_base_info';
						$sphinx_client = new \SphinxClient();
						$sphinx_client->setServer('127.0.0.1',9312);
						$sphinx_client->setConnectTimeout(3);
						$sphinx_client->setFilter('user_id',array($this->user_id),false);
						$sphinx_client->setFilter('is_delete',array($is_recy),false);
						$sphinx_client->setMaxQueryTime(2000);						
						/**
						 * SPH_MATCH_ALL 	Match all query words (default mode).
						 SPH_MATCH_ANY 	Match any of query words.
						 SPH_MATCH_PHRASE 	Match query as a phrase, requiring perfect match.
						 SPH_MATCH_BOOLEAN 	Match query as a boolean expression.
						 SPH_MATCH_EXTENDED 	Match query as an expression in Sphinx internal query language.
						 SPH_MATCH_FULLSCAN 	Enables fullscan.
						 SPH_MATCH_EXTENDED2 	The same as SPH_MATCH_EXTENDED plus ranking and quorum searching support.
						*/
						$sphinx_client->setMatchMode(SPH_MATCH_EXTENDED);
						/**
						 SPH_SORT_RELEVANCE 	Sort by relevance in descending order (best matches first).
						 SPH_SORT_ATTR_DESC 	Sort by an attribute in descending order (bigger attribute values first).
						 SPH_SORT_ATTR_ASC 	Sort by an attribute in ascending order (smaller attribute values first).
						 SPH_SORT_TIME_SEGMENTS 	Sort by time segments (last hour/day/week/month) in descending order, and then by relevance in descending order.
						 SPH_SORT_EXTENDED 	Sort by SQL-like combination of columns in ASC/DESC order.
						 SPH_SORT_EXPR 	Sort by an arithmetic expression.
						*/						
						$sphinx_client->setSortMode(SPH_SORT_RELEVANCE);
						$sphinx_client->SetLimits(($page_no -1)*$page_size,$page_size,500);
						$result = $sphinx_client->Query($search_name_valid_str,$index_name);
						//$error_array = $sphinx_client->getLastError();
						//$warn_array = $sphinx_client->getLastWarning();
						if($result){
							
							$return_json['total_count'] = $result['total'];
							$matches_list = $result['matches'];
							if(check_array_valid($matches_list)){
								
								$id_array = array();
								$id_array = array_keys($matches_list);								
								$extend_where = ' and id in ('.implode(',',$id_array).')';
							}else{
								
								$check_list = false;
							}
						}else{
							
							$check_list = false;
						}						
					}else{
						
						$extend_where = ' and display_name like "%'.$search_name_valid_str.'%"';
					}					
					if($check_list){
						
						$result = $this->contacts_base_info_object->get_web_list($is_recy,$page_no,$page_size,$extend_where);							
						$return_json['list'] = $result['list'];
						if($check_sphinx){
						
							$return_json['total_count'] = $result['total_count'];
						}
					}				
				}else{
					
					$return_json['result'] = $this->get_lang_value(INVALID_DATA);
				}
			}else{
				
				$return_json['result'] = $this->get_lang_value(INVALID_DATA);
			}			
		}
		$this->check_login('json',$return_json);
	}
	
	/**
	 * 获取详情
	 */
	public function getDetail(){
		
		$return_json = array('status'=>0,'info'=>array(),'result'=>'');
		if($this->is_login){
		
			$id = filter_set_value($_POST,'id',0,'int');				
			$result = $this->contacts_base_info_object->get_detail_info($id);
			if(check_array_valid($result)){
				
				$return_json['status'] = 1;
				$return_json['info'] = $result;
			}else{
				
				$return_json['result'] = $this->get_lang_value(INVALID_DATA);
			}
		}
		$this->check_login('json',$return_json);
	}
	
	/**
	 * 获取合并列表
	 */
	public function getMergeList(){
		
		$return_json = array('status'=>0,'list'=>array(),'result'=>'');
		if($this->is_login){
					
			$result = $this->contacts_base_info_object->get_merge_list();
			if(check_array_valid($result)){
		
				$return_json['status'] = 1;
				$return_json['list'] = $result;
			}else{
		
				$return_json['result'] = $this->get_lang_value(INVALID_DATA);
			}
		}
		$this->check_login('json',$return_json);
	}
	
	protected static function data_process($source,$name,$default_value,$value_type){
		
		$data = filter_set_value($source,$name,$default_value,$value_type);
		if($value_type == 'string'){
			
			$data = trim(strip_tags($data));
		}
		return $data;
	}
	
	/**
	 * 提交处理
	 * @param unknown $display_name
	 * @return multitype:multitype:NULL unknown
	 */
	protected function submit_process($display_name){
		
		$base_info = array();
		$submit_array = array();
		$mime_type_array = array('phone','email','im','postal','event','relation','website');		
		$base_info['display_name'] = $display_name;
		$base_info['given_name'] = self::data_process($_POST,'given_name','','string');
		$base_info['family_name'] = self::data_process($_POST,'family_name','','string');
		$base_info['prefix'] = self::data_process($_POST,'prefix','','string');
		$base_info['middle_name'] = self::data_process($_POST,'middle_name','','string');		
		$base_info['suffix'] = self::data_process($_POST,'suffix','','string');		
		$submit_array['base_info'] = $base_info;
		$submit_array['nickname'] = array();
		$submit_array['organization'] = array();
		$submit_array['photo'] = array();
		$submit_array['note'] = array();
		$submit_array['sip'] = array();
		$submit_array['group'] = array();
		foreach($mime_type_array as $mime_type){
			
			$submit_array[$mime_type] = array();
			$value_mime_type_array = self::data_process($_POST,$mime_type,array(),'array');
			if(count($value_mime_type_array) > 0){
								
				$value_mime_type_array_tmp = array();
				$value_mime_type_array_temp = array();
				$attr_mime_type_array = self::data_process($_POST,$mime_type.'_arr',array(),'array');
				foreach($value_mime_type_array as $key=>$value_mime_type){
					
					$check = false;
					$value_mime_type = strval($value_mime_type);
					$value_mime_type_len = strlen($value_mime_type);
					switch($mime_type){
					
						case 'phone':
							
							if($value_mime_type_len > 0 && preg_match("/^[0-9]{3,}[\s0-9]*$/", $value_mime_type)){
									
								$check = true;								
							}
							break;
						case 'email':					
							
							if($value_mime_type_len > 0 && $value_mime_type_len <= 60 && is_email($value_mime_type)){
							
								$check = true;								
							}
							break;
						case 'event':
							
							if($value_mime_type_len > 0 && is_date($value_mime_type)){
								
								$check = true;												
								$value_mime_type = date('Y-m-d',strtotime($value_mime_type));								
							}							
							break;
						case 'im':						
						case 'postal':						
						case 'relation':						
						case 'website':
								
							$check = $value_mime_type_len > 0 ? true : false;
							break;
					}
					if($check){
						
						$attr_mime_type = isset($attr_mime_type_array[$key]) ? intval($attr_mime_type_array[$key]) : 0;
						$val_attr = '<m>'.$value_mime_type.'<t>'.$attr_mime_type;
						if(!in_array($val_attr,$value_mime_type_array_temp)){
							
							$value_mime_type_array_tmp[$key] = $value_mime_type;							
							$attr_mime_type_array[$key] = $attr_mime_type;
							$value_mime_type_array_temp[] = $val_attr;							
						}elseif(isset($attr_mime_type_array[$key])){
							
							unset($attr_mime_type_array[$key]);
						}						
					}elseif(isset($attr_mime_type_array[$key])){
						
						unset($attr_mime_type_array[$key]);
					}
				}
				
				if(count($value_mime_type_array_tmp) > 0){
					
					$i = 0;
					foreach($value_mime_type_array_tmp as $key=>$value_mime_type){
						
						$row_array = array();
						$row_array['type_name'] = $attr_mime_type_array[$key];
						switch($mime_type){
								
							case 'phone':						
							case 'email':
								
								$row_array['label'] = '';
								$row_array[$mime_type] = $value_mime_type;
								break;
							case 'im':
									
								$row_array['label'] = '';
								$row_array['chat_account'] = $value_mime_type;
								$row_array['protocol'] = '';
								$row_array['custom_protocol'] = '';
								break;
							case 'postal':
									
								$row_array['label'] = '';
								$row_array['formatted_address'] = $value_mime_type;
								$row_array['street'] = '';
								$row_array['pobox'] = '';
								$row_array['neighborhood'] = '';
								$row_array['city'] = '';
								$row_array['region'] = '';
								$row_array['postcode'] = '';
								$row_array['country'] = '';
								break;
							case 'event':
									
								$row_array['label'] = '';
								$row_array['start_date'] = $value_mime_type;
								break;
							case 'relation':
									
								$row_array['label'] = '';
								$row_array['name'] = $value_mime_type;
								break;
							case 'website':
									
								$row_array['label'] = '';
								$row_array['url'] = $value_mime_type;
								break;							
						}
						
						$submit_array[$mime_type][$i] = $row_array;
						$i++;
					}					
				}
			}			
		}
		
		$note = self::data_process($_POST,'note','','string');
		$sip = self::data_process($_POST,'sip_address','','string');		
		$nick_name = self::data_process($_POST,'nick_name','','string');		
		$company = self::data_process($_POST,'company','','string');
		$job_description = self::data_process($_POST,'job_description','','string');
		$group_array = self::data_process($_POST,'group',array(),'array');
		if(strlen($note) > 0){
		
			$submit_array['note']['note'] = $note;
		}
		
		if(strlen($sip) > 0){
			
			$submit_array['sip']['label'] = '';
			$submit_array['sip']['sip_address'] = $sip;
			$submit_array['sip']['type_name'] = 1;
		}
		
		if(strlen($nick_name) > 0){
			
			$submit_array['nickname']['label'] = '';
			$submit_array['nickname']['nick_name'] = $nick_name;
			$submit_array['nickname']['type_name'] = 1;
		}
		
		if(strlen($company) > 0 || strlen($job_description) > 0){
			
			$submit_array['organization']['label'] = '';
			$submit_array['organization']['company'] = $company;
			$submit_array['organization']['title'] = $company;
			$submit_array['organization']['department'] = '';
			$submit_array['organization']['job_description'] = $job_description;
			$submit_array['organization']['symbol'] = 1;
			$submit_array['organization']['phonetic_name'] = '';			
			$submit_array['organization']['office_location'] = 1;
			$submit_array['organization']['phonetic_name_style'] = '';
			$submit_array['organization']['type_name'] = 1;
		}	
		
		if(check_array_valid($group_array)){
			
			$group_tmp_array = array();
			foreach($group_array as $group_id){
				
				$group_id = intval($group_id);
				if($group_id > 0 && !in_array($group_id,$group_tmp_array)){
					
					$group_tmp_array[] = $group_id;
				}
			}
			$submit_array['group'] = $group_tmp_array;
		}
		return $submit_array;
	}
	
	/**
	 * 添加
	 */
	public function add(){
	
		$return_json = array('status'=>0,'id'=>0,'result'=>'');
		if($this->is_login){
			
			$current_time = time();
			$contacts_add_time = floatval(session('contacts_add'));			
			if($current_time - $contacts_add_time > 8){
				
				$display_name = self::data_process($_POST,'display_name','','string');
				if(strlen($display_name)){
				
					$add_info = $this->submit_process($display_name);
					$contacts_base_info_object = $this->contacts_base_info_object;
					$id = $contacts_base_info_object->simple_add($add_info);
					$id = intval($id);					
					if($id > 0){
						
						session('contacts_add',$current_time);
						$return_json['id'] = $id;
						$return_json['status'] = 1;
					}else{
				
						$error_code_array = $contacts_base_info_object->get_error_code_array();
						foreach($error_code_array as $error_code){
								
							$return_json['result'] = $this->get_lang_value($error_code);
						}
					}
				}else{
				
					$return_json['result'] = L('ContactNameInvalid');
				}
			}else{
				
				$return_json['result'] = $this->get_lang_value(ERROR_OPERATING_TOO_FAST);
			}											
		}
		$this->check_login('json',$return_json);
	}
	
	/**
	 * 修改
	 */
	public function edit(){
		
		$return_json = array('status'=>0,'result'=>'');
		if($this->is_login){
		
			$display_name = self::data_process($_POST,'display_name','','string');
			if(strlen($display_name)){
				
				$update_info = $this->submit_process($display_name);
				$id = filter_set_value($_POST,'id',0,'int');
				$contacts_base_info_object = $this->contacts_base_info_object;
				$check = $contacts_base_info_object->simple_edit($id,$update_info);
				if($check){
					
					$return_json['status'] = 1;
				}else{
					
					$error_code_array = $contacts_base_info_object->get_error_code_array();
					foreach($error_code_array as $error_code){
							
						$return_json['result'] = $this->get_lang_value($error_code);
					}
				}
			}else{
				
				$return_json['result'] = L('ContactNameInvalid');
			}
		}
		$this->check_login('json',$return_json);
	}
	
	/**
	 * 公共处理
	 */
	protected function common_process(){		
		
		$return_json = array('status'=>0,'result'=>'');		
		if($this->is_login){
			
			$process_type = strtolower(ACTION_NAME);			
			$process_type_array = array('recover'=>'web_recover','recycle'=>'web_delete','delete'=>'web_fordel');			
			if(!isset($process_type_array[$process_type])){
					
				$return_json['result'] = $this->get_lang_value(INVALID_OPERATE);
			}else{
				
				$method_name = $process_type_array[$process_type];
				$contacts_base_info_object = $this->contacts_base_info_object;
				if(!method_exists($contacts_base_info_object,$method_name)){
					
					$return_json['result'] = $this->get_lang_value(METHOD_NOT_EXISTS);
				}else{
					
					$server_id_array = array();
					$id = trim(filter_set_value($_POST,'id','','string'));
					
					if(strpos($id,',') === false){
				
						$server_id_array[0] = $id;
					}else{
				
						$server_id_array = explode(',',$id);
					}
					$check = $contacts_base_info_object->$method_name($server_id_array);
					if($check){
				
						$return_json['status'] = 1;
					}else{
				
						$error_code_array = $contacts_base_info_object->get_error_code_array();
						foreach($error_code_array as $error_code){
								
							$return_json['result'] = $this->get_lang_value($error_code);
						}
					}
				}
			}
		
		}
		$this->check_login('json',$return_json);
	}
	
	/**
	 * 恢复
	 */
	public function recover(){
	
		$this->common_process();
	}
	
	/**
	 *移入回收站
	 */
	public function recycle(){
		
		$this->common_process();
	}	
	
	/**
	 * 强制删除
	 */
	public function delete(){
		
		$this->common_process();
	}	
	
	/**
	 * 合并
	 */
	public function merge(){
		
		$return_json = array('status'=>0,'result'=>'');
		if($this->is_login){
		
			$id = filter_set_value($_POST,'id',0,'int');
			$merge_type = filter_set_value($_POST,'type','','string');
			$merge_id_array = filter_set_value($_POST,'merge_id_array',array(),'array');
			$contacts_base_info_object = $this->contacts_base_info_object;
			$check = $contacts_base_info_object->merge_contact($id,$merge_id_array,$merge_type);
			if($check){
				
				$return_json['status'] = 1;
			}else{
				
				$error_code_array = $contacts_base_info_object->get_error_code_array();
				foreach($error_code_array as $error_code){
					
					$return_json['result'] = $this->get_lang_value($error_code);
				}				
			}			
		}
		$this->check_login('json',$return_json);
	}
	
	/**
	 * 清空
	 */
	public function clearAll(){
		
		$return_json = array('status'=>0,'result'=>'');
		if($this->is_login){

			$contacts_base_info_object = $this->contacts_base_info_object;
			$result = $this->contacts_base_info_object->client_clearAll();
			if($result['check']){
				
				$return_json['status'] = 1;
			}else{
				
				$error_code_array = $contacts_base_info_object->get_error_code_array();
				foreach($error_code_array as $key=>$error_code){
					
					$return_json['result'] = $this->get_lang_value($error_code);
				}				
			}				
		}
		$this->check_login('json',$return_json);
	}	
	
	/**
	 * 判别联系人姓名是否合法
	 */
	public function checkValid(){
		
		$return_json = array('status'=>0,'result'=>'');
		if($this->is_login){
			
			$id = filter_set_value($_POST,'id',0,'int');
			$display_name = self::data_process($_POST,'display_name','','string');
			$contacts_base_info_object = $this->contacts_base_info_object;
			$check = $contacts_base_info_object->display_name_check_exists($id,$display_name);
			if($check){
		
				$return_json['result'] = L('ContactNameExists');
			}else{
								
				$error_code_array = $contacts_base_info_object->get_error_code_array();
				if(check_array_valid($error_code_array)){
					
					foreach($error_code_array as $key=>$error_code){
					
						$return_json['result'] = $this->get_lang_value($error_code);
					}
				}else{
					
					$return_json['status'] = 1;
				}				
			}
		}
		$this->check_login('json',$return_json);
	}
	
	/**
	 * 导入
	 */
	public function import(){
		
	}
	
	/**
	 * 导出
	 */
	public function export(){
		
	}  
}