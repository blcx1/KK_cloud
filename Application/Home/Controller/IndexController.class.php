<?php
namespace Home\Controller;
use Think\Controller;
class IndexController extends \Home\Controller\DefaultController {
	
	protected $check_login_type = 'msg';
	protected static $login_log_object = null;	
	protected $igore_action_name_array = array('login','faqlist','register','index','logout','forgetpassword','verify','sendmail','resetpassword');
	
	/**
	 * 其他初始化
	 */
	protected function init(){	
		
		parent::init();
		if(!$this->is_ajax){
			
			if($this->is_login){
			
				$this->load_model_defined('user_info',array());//用户信息模块参数定义
			}
			$igore_action_name = array('login','register','logout','forgetPassword');
			$action_name = strtolower(ACTION_NAME);
			if(!in_array($action_name,$igore_action_name)){
			
				$this->load_model_defined('left_suspend_plugin',array());//左边悬浮插件模块参数定义
			}
		}
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \Home\Controller\DefaultController::__destruct()
	 */
	public function __destruct(){
		
		self::$login_log_object = null;
		parent::__destruct();
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \Home\Controller\DefaultController::index()
	 */
    public function index(){
        
    	$template_name = 'index';
    	$header_array = array();
    	$footer_array = array();
    	$device_total_count = 0;
    	$header_array['title'] = L('Home');
    	$header_array['loader_js'][0] ='/js/homenew.js';
    	$header_array['loader_css'][0] = '/css/home.css';
    	$header_array['loader_css'][1] = '/css/animation.css';
    	    	
    	$check_login = $this->check_login('return',array());
    	if(!$check_login){
    		    		
    		$header_array['loader_css'][2] = '/css/cloud.css';  		
    	}else{			
    		
    		$template_name = 'homenew';
    		$base_url = getBaseURL();    		
    		$left_suspend_plugin_list = array();   		
    		$left_suspend_plugin_list[0] = array(
    				'url'=>$base_url.MODULE_NAME.'/Contacts/index.html',
    				'name'=>'contact',
    				'display_name'=>L('Contact')
    		);
    		$left_suspend_plugin_list[1] = array(
    				'url'=>$base_url.MODULE_NAME.'/Sms/index.html',
    				'name'=>'sms',
    				'display_name'=>L('Sms')
    		);
    		$left_suspend_plugin_list[2] = array(
    				'url'=>$base_url.MODULE_NAME.'/Gallery/index.html',
    				'name'=>'gallery',
    				'display_name'=>L('Gallery')
    		);
    		$left_suspend_plugin_list[3] = array(
    				'url'=>$base_url.MODULE_NAME.'/Note/index.html',
    				'name'=>'note',
    				'display_name'=>L('Note')
    		);
    		$left_suspend_plugin_list[4] = array(
    				'url'=>$base_url.MODULE_NAME.'/Phone/index.html',
    				'name'=>'phone',
    				'display_name'=>L('Phone')
    		);    		
    		$left_suspend_plugin_list[5] = array(
    				'url'=>$base_url.MODULE_NAME.'/CallRecord/index.html',
    				'name'=>'callrecord',
    				'display_name'=>L('CallRecord')
    		);
    		$header_array['loader_css'][2] = '/css/homenew.css';    				
    		$this->assign('left_suspend_plugin_list',$left_suspend_plugin_list);
    		
    		$user_device_object = new \Home\Model\UserDeviceModel();
    		$device_total_count = $user_device_object->get_user_device_total_count($this->user_id);
    		
    	}   
    	if($this->is_mobile){
    	
    		$header_array['loader_css'][3] = '/css/mobile.css';
    	} 
    	$this->head_common($header_array);
    	$this->footer_common($footer_array);
    	$this->assign('device_total_count',$device_total_count);
    	$this->load_model_defined('lang',array());//语言模块参数定义    	
    	$this->display($template_name);
    }
    
    /**
     * 登录页面
     */
    public function login(){
        $refom_url=@$_GET['refom_url'];
        if(!empty($refom_url)){
            session('refom_url',$refom_url);
        }
        
    	token_process();
    	$check_login = $this->check_login('return',array());   
    	if(!$check_login){
    		$user_account = '';
    		$login_type = LOGIN_WEB;
    		$web_seed = session('web_seed'); 
    		$error_array = array('user_account'=>'','password'=>'','verify_code'=>'','result'=>'');   				
    	  	if(IS_POST){
   
    			$user_account = filter_set_value($_POST,'user_account','','string');   			
    			if(!empty($web_seed)){
    				    				
    				$verify_code = filter_set_value($_POST,'verify_code','','string');    				
    				$check = self::check_verify('login_verify',$verify_code,'login',$user_account,$login_type);
    				if($check){
    					
    					$api_id = '';    					
    					$password = filter_set_value($_POST,'password','','string');    					
    					$user_object = new \Home\Model\UserModel();
    					$check = strlen($user_account) > 1 && $user_object->is_password($password,false) ? true : false;
    					if($check){
    						
    						//用户中心登录
    						$action_type = 'POST';
    						$json_array = array();
    						$other_array = array();
    						    						
    						$json_array['scip'] = 1;
    						$json_array['ip'] = get_client_ip(0,true);
    						$json_array['user_id'] = 0;
    						$json_array['session_id'] = '';
    						$json_array['login_type'] = $login_type;
    						$json_array['user_name'] = $user_account;
    						$json_array['password'] = $password;
    						$json_array['api_id'] = $api_id;
    						$json_array['web_seed'] = $web_seed;
    						$json_array['seed_code'] = self::get_user_center_seed_code($web_seed,$login_type);
    						
    						$url = C('USER_CENTER_PREFIX').'Home/User/webLogin.html?l='.LANG_SET;    						
    						$json_object = $this->user_center_interactive($url,$json_array,$other_array,$action_type);    						  						
    						if(!is_null($json_object) && is_object($json_object)){
    							
    							$status = intval($json_object->status);    							
    							if($status == 0 || $status == IS_PERFECT || $status == LOGIN_SUCCESS){
    								
    								$user_info = array();
    								$this->is_login = true;
    								$this->user_id = intval($json_object->user_id);
    								$this->session_id = $json_object->session_id;
    								$user_info['user_id'] = $this->user_id;
    								$user_info['user_name'] = $json_object->user_name;
    								$user_info['nick_name'] = $json_object->user_nickname;
    								$user_info['portrait'] = isset($json_object->portrait_img) ? $json_object->portrait_img : '';			
    								$this->user_info = $user_info;
    								$this->is_perfect = $status == IS_PERFECT ? true : false;
    								
    								session('user_id',$this->user_id);
    								session('session_id',$this->session_id);
    								session('user_info',$this->user_info);
    								session('is_perfect',($this->is_perfect ? 1 : 0));	
    							
    								self::clear_login_count($user_account,$login_type);
                                                                if(!empty(session('refom_url'))){
                                                                    if(preg_match('/\?/',session('refom_url'))){
                                                                        $da="&";
                                                                    }else{
                                                                        $da="?";
                                                                    }
                                                                    $user_session_id=urlencode(base64_encode($this->user_id.','.$json_object->session_id));
                                                                    redirect(session('refom_url').$da.'user='.$user_session_id);
                                                                }
    								$this->login_success_redirect(false);
    							}else{    								
    			
    								$error_array['result'] = $this->get_lang_value($status);
    							}    							
    						}else{
    							
    							$error_array['result'] = L('NetworkBusy');
    						}   						
    					}else{
    						
    						if(strlen($user_account) <= 1){
    							
    							$error_array['user_account'] = L('UserAccoutInvalid');
    						}else{
    							
    							$error_array['password'] = L('PasswordInvalid');
    						}    						
    					}    					
    				}else{
    					
    					$error_array['result'] = L('CaptchaInvalid');
    				}    				
    			}else{
    				
    				$error_array['result'] = L('CaptchaInvalid');//验证码不合法
    			}

    			self::set_login_count($user_account,$login_type);    			
    		}
    		
    		$check_verify = self::check_verify_empty_valid($user_account,$login_type) ? false : true;
		    if(empty($web_seed)){
		    				 
		    	$web_seed = substr(uniqid(),0,8);
		    	session('web_seed',$web_seed);
		    }
		   
    		$header_array = array();
    		$header_array['title'] = L('LoginTitle');
    		$header_array['loader_css'][0] = '/css/login.css';
    		if($this->is_mobile){
    		
    			$header_array['loader_css'][1] = '/css/mobile.css';
    		}
    		$header_array['loader_js'][0]='/js/login.js';
    		$this->head_common($header_array);
    		$this->assign('user_account',$user_account);
    		$this->assign('error_array',$error_array);
    		$this->assign('check_verify',$check_verify);
    		$this->load_model_defined('lang',array());//语言模块参数定义
    		$this->display('login');
    	}else{
                if(!empty($refom_url)){
                    if(preg_match('/\?/',$refom_url)){
                        $da="&";
                    }else{
                        $da="?";
                    }
                    $user_session_id=urlencode(base64_encode(session('user_id').','.session('session_id')));
                    redirect(session('refom_url').$da.'user='.$user_session_id);
                }
                    
    		$this->login_success_redirect(true);
    	}  	    	
    }
    
    /**
     * 注册页面
     */
    public function register(){
    	
    	token_process();
   		$check_login = $this->check_login('return',array());
    	if(!$check_login){
    		
    		$email = '';
    		$user_name = '';
    		$web_seed = session('web_seed');
    		$error_array = array('user_name'=>'','password'=>'','email'=>'','verify_code'=>'','result'=>'');
    		if(IS_POST){
   				
    			$email = filter_set_value($_POST,'email','','string');
    			$user_name = filter_set_value($_POST,'regName','','string');   			
    			if(!empty($web_seed)){
    				    				
    				$verify_code = filter_set_value($_POST,'authcode','','string');    				
    				$check = self::check_verify('register_verify',$verify_code,'register');
    				if($check){
    					
    					$api_id = '';
    					$check_valid = true;
    					$user_object = new \Home\Model\UserModel();
    					$password = filter_set_value($_POST,'pwd','','string');
    					if(!$user_object->check_add_email($email,true,0)){
    							
    						$check_valid = false;    						
    					}
						if(!$user_object->check_add_user_name($user_name,true,0)){
							
							$check_valid = false;							
						}    					
    					if(!$user_object->is_password($password,true)){
    						
    						$check_valid = false;    						
    					}
    					
    					if($check_valid){
    						    						
    						//用户中心注册
    						$tel = '';
    						$action_type = 'POST';
    						$json_array = array();
    						$other_array = array();
    						$register_type = REGISTER_WEB_MAIL;
    						    						
    						$json_array['scip'] = 1;
    						$json_array['ip'] = get_client_ip(0,true);
    						$json_array['user_id'] = 0;
    						$json_array['session_id'] = '';
    						$json_array['register_type'] = $register_type;
    						$json_array['user_name'] = $user_name;
    						$json_array['tel'] = $tel;   						
    						$json_array['email'] = $email;
    						$json_array['password'] = $password;
    						$json_array['confirm_password'] = $password;    						
    						$json_array['web_seed'] = $web_seed;
    						$json_array['seed_code'] = self::get_user_center_seed_code($web_seed,$register_type);
    						
    						$url = C('USER_CENTER_PREFIX').'Home/User/webRegister.html?l='.LANG_SET;    						
    						$json_object = $this->user_center_interactive($url,$json_array,$other_array,$action_type);    						  						
    						if(!is_null($json_object) && is_object($json_object)){
    							
    							$status = intval($json_object->status);    							
    							if($status == 0 || $status == ERROR_EMAIL_NOT_VERIFY || $status == IS_PERFECT || $status == REGISTER_SUCCESS){
    								
    								$user_info = array();
    								$this->is_login = true;
    								$this->user_id = intval($json_object->user_id);
    								$this->session_id = $json_object->session_id;
    								$user_info['user_id'] = $this->user_id;
    								$user_info['user_name'] = $json_object->user_name;
    								$user_info['nick_name'] = $json_object->user_nickname;
    								$user_info['portrait'] = isset($json_object->portrait_img) ? $json_object->portrait_img : '';			
    								$this->user_info = $user_info;
    								$this->is_perfect = $status == IS_PERFECT ? true : false;
    								
    								session('user_id',$this->user_id);
    								session('session_id',$this->session_id);
    								session('user_info',$this->user_info);
    								session('email',$email);
    								session('is_perfect',($this->is_perfect ? 1 : 0));    							
    								if($status != ERROR_EMAIL_NOT_VERIFY){
    									
    									$this->login_success_redirect(false);
    								}else{
    									
    									$this->registerMail();
    									exit;
    								}
    								
    							}else{    								
    			
    								$error_array['result'] = $this->get_lang_value($status);
    							}    							
    						}else{
    							
    							$error_array['result'] = L('NetworkBusy');
    						}   						
    					}else{
    						
    						$error_code_array = $user_object->get_error_code_array();
    						foreach($error_code_array as $key=>$error_code){
    							
    							$error_array[$key] = $this->get_lang_value($error_code);
    						}
    					}  					
    				}else{
    					
    					$error_array['verify_code'] = L('CaptchaInvalid');
    				}    				
    			}else{
    				
    				$error_array['result'] = L('CaptchaInvalid');//验证码不合法

    			}

    		}
    		
    		if(empty($web_seed)){
    			 
    			$web_seed = substr(uniqid(),0,8);
    			session('web_seed',$web_seed);
    		}
    		$footer_array = array();
    		$header_array = array();
    		$header_array['title'] = L('Register');
    		
    		$header_array['loader_css'][0] = '/css/'.($this->is_mobile ? 'm.registerpwd.css' :'registerpwd.css');
    		$header_array['loader_css'][1] = '/css/reset.css';
    		$header_array['loader_css'][2] = '/css/layout.css';
    		$header_array['loader_css'][3] = '/css/unit.css';
    		$header_array['loader_css'][4] = '/css/widget.css';
    		if($this->is_mobile){
    		
    			$header_array['loader_css'][5] = '/css/mobile.css';
    		}
    		$header_array['loader_js'][0] = '/js/register.js';
    		$this->head_common($header_array);
    		$this->footer_common();
    		$this->load_model_defined('lang',array());//语言模块参数定义
    		$this->assign('user_name',$user_name);
    		$this->assign('email',$email);
    		$this->assign('error_array',$error_array);
    		$this->display('register');
    	}else{

			$this->login_success_redirect(true);
    	}  	
    }
    
    /**
     * 退出
     */
   	public function logout(){

   		$user_id = $this->user_id;
   		if($this->is_login){

   			$session_id = $this->session_id;
   			
   			$this->user_id = 0;
   			$this->session_id = '';
   			$this->user_info = array();
   			
   			session('user_id',$this->user_id);
   			session('session_id',$this->session_id);
   			session('user_info',$this->user_info);
   			session('[destroy]');
   			
   			//用户中心退出   
   			$action_type = 'POST';
   			$json_array = array();
   			$other_array = array();
   			$url = C('USER_CENTER_PREFIX').'Home/User/clientLogout.html?l='.LANG_SET;
   			   			
   			$json_array['scip'] = 1;
   			$json_array['ip'] = get_client_ip(0,true);
   			$json_array['user_id'] = $user_id;
   			$json_array['session_id'] = $session_id;
   			$json_array['session_id'] = $session_id;
   			$json_object = $this->user_center_interactive($url,$json_array,$other_array,$action_type);   			
   		}   				
   		
   		$check_url = false;
   		$http_referer = isset($_GET['back_url']) ? trim($_GET['back_url']) : '';
   		if(empty($http_referer)){
   			
   			$base_url = getBaseURL();   			
   			$http_referer = get_home_url($this->is_mobile);
   			if(empty($http_referer)){
   				
   				$http_referer = $base_url;
   			}
   		}else{
   			
   			$check_url = true; 			
   			if(strpos($http_referer,'?') > 0){
   				
   				$http_referer .= '&';
   			}else{
   				
   				$http_referer .= '?';
   			}   			
   			$http_referer .= 'user_id='.$user_id;
   			if(!$this->is_login){
   				
   				$http_referer .= '&is_expire=1';
   			}
   		}

   		if($check_url){
   			
   			$this->is_login = false;
   			redirect($http_referer,0,'');
   			exit;
   		}else{
   			
   			if($this->is_login){
   				
   				$this->is_login = false;
   				$this->success(L('LogoutSuccess'),$http_referer,false);
   			}else{
   				
   				$this->error(L('NotLogin'),$http_referer,false);
   			}
   		}
   	}
   	
   	/**
   	 * 用户中心
   	 */
   	public function userCenter(){
   		
   		$this->security();  		
   	}

    /**
     * 忘记密码
     */
    public function forgetPassword(){
    		    	
    	$check_login = $this->check_login('return',array());
    	if(!$check_login){
    		
    		$email = '';
    		$send_mail = false;
    		$check_next = false;
    		$verify_code_failed = 0;
    		$reset_send_mail = false;
    		$step = filter_set_value($_GET,'step',1,'int');    		
    		switch($step){
  			    				
    			case 2:	
    				
    				$user_id = session('user_id_f');
    				$email = session('email_f');
    				if($user_id > 0 && !empty($email)){
    					
    					$title = L('Forgepassword');
    					$check_next = true;
    					$error_array = array('verify_code'=>'');
    					break;
    				}else{
    					    					
    					$step = 1;
    				}    					
    			case 1:
    			default:
    					
    				$title = L('Forgepassword');
    				$web_seed = session('web_seed');
    				$error_array = array('email'=>'','verify_code'=>'');
    				break;    			
    		}
    			    	
    		if(IS_POST){
   				
    			if($step == 1){
    				
	    			$email = filter_set_value($_POST,'email','','string');    					
	    			if(!empty($web_seed)){
	    				    				
	    				$verify_code = filter_set_value($_POST,'icode','','string');    				
	    				$check = self::check_verify('forget_verify',$verify_code,'forgetpassword');
	    				if($check){
	    					
	    					$user_object = new \Home\Model\UserModel();
	    					$user_view_object = new \Home\Model\UserViewModel();
							$user_id = $user_view_object->web_user_forgot($user_object,$email,'',VERIFY_TYPE_EMAIL);
							$error_code_array = $user_view_object->get_error_code_array();
							$check = count($error_code_array) <= 0 ? true : false;
							if($check){
								
								session('user_id_f',$user_id);
								session('email_f',$email);
								$check_next = true;
								$send_mail = true;
							}else{
								
								foreach($error_code_array as $key=>$error_code){
										
									$error_array['email'] = $this->get_lang_value($error_code);
								}							
							}
	    				}else{
	    					
	    					$error_array['verify_code'] = L('CaptchaInvalid');
	    				}    				
	    			}else{
	    				
	    				$error_array['result'] = L('CaptchaInvalid');//验证码不合法
	    			}
    			}else{
    				 
    				$verify_code_failed = intval(session('verify_code_failed'));
    				$verify_code = filter_set_value($_POST,'verify_code','','string');
    				$s_verify_code = session('verify_code');
    				if(!empty($verify_code) && $s_verify_code == $verify_code ){
						
    					session('resetpass','Y');    					
    					session('web_seed',null);
    					session('verify_code',null);
    					session('verify_code_failed',null);
    					session('send_mail_time_f',null);
    					session('send_mail_count_f',null);
    					$url = getBaseURL().MODULE_NAME.'/Index/resetPassword.html';
    					redirect($url,0,'');
    				}elseif($verify_code_failed >= 3){
    					
    					$reset_send_mail = true;
    					session('verify_code_failed',1); 
    					$error_array['verify_code'] = L('CaptchaInvalid');//验证码不合法
    				}else{
    					
    					session('verify_code_failed',$verify_code_failed + 1);
    					$error_array['verify_code'] = L('CaptchaInvalid');//验证码不合法
    				}   				 
    			}     					
    		}
    		
	    	if($step == 1 && empty($web_seed)){
	    	
	    		$web_seed = substr(uniqid(),0,8);
	    		session('web_seed',$web_seed);
	    	}	    	
	    	$footer_array = array();
	    	$header_array = array();
	    	
	    	$header_array['loader_css'][0] = '/css/reset.css';
	    	$header_array['loader_css'][1] = '/css/layout.css';
	    	$header_array['loader_css'][2] = '/css/'.($this->is_mobile ? 'm.registerpwd.css' :'registerpwd.css');
	    	$header_array['loader_css'][3] = '/css/forgetpassword.css';
	    	if($this->is_mobile){
	    	
	    		$header_array['loader_css'][4] = '/css/mobile.css';
	    	}
	    	$header_array['title'] = $title;
	    	$this->head_common($header_array);
	    	$this->footer_common();
	    	$this->load_model_defined('lang',array());//语言模块参数定义
	    	$this->assign('email',$email);
	    	$this->assign('send_mail',$send_mail);	    	
	    	$this->assign('check_next',$check_next);
	    	$this->assign('reset_send_mail',$reset_send_mail);
	    	$this->assign('error_array',$error_array);
	    	$this->assign('click_count',intval(session('send_mail_count_f')));
	    	$this->display('forgetpassword');
    	}else{
    		
    		$this->login_success_redirect(true);
    	}
    }
    
    /**
     * 重置密码
     */
    public function resetPassword(){

    	$user_id = session('user_id_f');
    	$email = session('email_f');
    	if($user_id > 0 && !empty($email) && session('resetpass')== 'Y'){
    		   		
    		$error_array = array('result'=>'');
    		if(IS_POST){
    			
    			$new_password = filter_set_value($_POST,'new_password','','string');
    			$confirm_password = filter_set_value($_POST,'confirm_password','','string');
    			$user_object = new \Home\Model\UserModel();
    			$check = $user_object->webResetPassword($user_id,$new_password,$confirm_password);
    			if($check){
    				
    				session('user_id_f',null);
    				session('email_f',null);
    				session('resetpass',null);    				
    				$url = get_login_url($this->is_mobile);					
    				$this->success(L('ResetPasswordSuccess'),$url,false);
    				exit;
    			}else{
    				
    				$error_code_array = $user_object->get_error_code_array();
    				foreach($error_code_array as $key=>$error_code){
    					
    					$error_array['result'] = $this->get_lang_value($error_code);
    				}
    			}
    		}
    		$footer_array = array();
    		$header_array = array();
    		$header_array['loader_css'][0] = '/css/reset.css';
    		$header_array['loader_css'][1] = '/css/layout.css';
    		$header_array['loader_css'][2] = '/css/'.($this->is_mobile ? 'm.registerpwd.css' :'registerpwd.css');
    		if($this->is_mobile){
    		
    			$header_array['loader_css'][3] = '/css/mobile.css';
    		}
    		$header_array['loader_js'][0] ='/js/resetpassword.js';
    		$header_array['title'] = L('Resetpassword');
    		$this->head_common($header_array);
    		$this->footer_common();
    		$this->load_model_defined('lang',array());//语言模块参数定义
    		$this->assign('error_array',$error_array);
    		$this->display('resetpassword');
    	}else{
    		
    		$check_login = $this->check_login('return',array());
    		if($check_login){
    			
    			$this->login_success_redirect(true);
    		}else{    			
    			
    			$url = getBaseURL().MODULE_NAME.'/Index/forgetPassword.html';
    			$this->error(L('ResetPasswordFailed'),$url,false);
    		}    		
    	}
    }

    /**
     * 邮件发送
     */
    public function sendMail(){
    	    	
    	$status = 0;
    	$current_time = time();
    	$user_id = session('user_id_f');
    	$email = session('email_f');
    	$send_mail_time = intval(session('send_mail_time_f'));
    	$send_mail_count = intval(session('send_mail_count_f'));
    	if($user_id > 0 && $send_mail_count <= 8 && !empty($email) && ($send_mail_time + 50) < $current_time){
    	
    		$user_object = new \Home\Model\UserModel();
    		$user_view_object = new \Home\Model\UserViewModel();
    		$user_view_object->web_user_forgot($user_object,$email,'',VERIFY_TYPE_EMAIL);
    		$error_code_array = $user_view_object->get_error_code_array();
    		$check = count($error_code_array) <= 0 ? true : false;
    		if($check){
    			
    			session('send_mail_time_f',$current_time);    			
    		}
    		session('send_mail_count_f',$send_mail_count+1);
    		$status = $check ? 1 : 2;
    	}elseif($user_id <= 0 || $send_mail_count > 8 || empty($email)){
    		
    		$status = 2;
    	}
    	$json_array = array();
    	$json_array['status'] = $status;//0 操之过快 1 成功 2 失败
    	echo json_encode($json_array);
    	exit;   	
    }  
    
    /**
     * 账号安全
     */
    public function security(){
    	
    	$display_email = '';
    	$user_info = $this->user_info;
    	$user_info_view_object = new \Home\Model\UserInfoViewModel();
    	$user_info_view = $user_info_view_object->client_user_info($this->user_id);
    	if(check_array_valid($user_info_view)){
    		
    		$user_info['user_name'] = $user_info_view['user_name'];
    		$user_info['nick_name'] = $user_info_view['nick_name'];
    		$user_info['portrait'] = $user_info_view['portrait_img'];
    		session('user_info',$user_info);
    		$user_info['gander'] = $user_info_view['gander'];
    		$user_info['tel'] = $user_info_view['real_tel'];
    		$user_info['birthday'] = $user_info_view['birthday'];
    		$user_info['address'] = $user_info_view['address'];    		
    		$email = $user_info_view['real_email'];
    		$display_email = $user_info_view['email'];
    	}
    	$footer_array = array();
    	$header_array = array();
    	$header_array['title'] = L('Accountsecurity');
    	$header_array['loader_css'][0] = '/css/reset.css';
    	$header_array['loader_css'][1] = '/css/layout.css';
    	$header_array['loader_css'][2] = '/css/modacctip.css';
    	$header_array['loader_css'][3] = '/css/security.css';
    	if($this->is_mobile){
    	
    		$header_array['loader_css'][4] = '/css/mobile.css';
    	}
    	$header_array['loader_js'][0] ='/js/ajaxfileupload.js';
    	$header_array['loader_js'][1] ='/js/security.js';
    	$header_array['loader_js'][2] ='/lib/My97DatePicker/WdatePicker.js';
    	$this->head_common($header_array);
    	$this->footer_common();
    	$this->assign('user_info',$user_info);
    	$this->assign('email',$email);    	
    	$this->assign('display_email',$display_email);
    	$this->load_model_defined('lang',array());//语言模块参数定义
    	$this->display('security');
    }
    
    /**
     * 修改头像
     */
    public function changePortrait(){
    	    	
    	$return_json = array('status'=>0,'portrait'=>'','result'=>'');    
    	if($this->is_login){
    		
    		$user_info = $this->user_info;
    		$return_json['portrait']= $user_info['portrait'];
    		
    		$upload_name = 'portrait';
    		$user_object = new \Home\Model\UserModel();
    		$info_array = $user_object->change_portrait_img($this->user_id,$upload_name);    		
    		$check = $info_array['check'];
	    	if(!$check){
	    	
	    		$error_code_array = $user_object->get_error_code_array();
	    		foreach($error_code_array as $key=>$error_code){
	    			
	    			$return_json['result'] = $this->get_lang_value($error_code);
	    		}
	    	}else{
	    			    		
	    		$portrait = $info_array['portrait_img'];
                        
                        //复制头像文件到userserver目录2017-08-21
                        $avatar_file=substr($portrait,strrpos($portrait,'/Public/Upload/avatar')+1);
                        $userserver_file='../userserver/'.$avatar_file;
                        $userserver_dir=substr($userserver_file,0,strrpos($userserver_file,'/')+1);
                        if(!is_dir($userserver_dir)){
                            mkdir($userserver_dir,0755,true);
                        }
                        if(is_dir($userserver_dir)){
                            if(!file_exists($userserver_file)){
                                $ser_res=copy($avatar_file,$userserver_file);
                            }
                            $user_nev_file='../userserver'.substr($user_info['portrait'],strrpos($user_info['portrait'],'/Public/Upload/avatar'));
                            if(file_exists($user_nev_file)){
                                @unlink($user_nev_file);
                            }
                        }
                        /****************************/
                        
	    		$user_info['portrait'] = $portrait;
	    		session('user_info',$user_info);
	    		
	    		$return_json['status'] = 1;
	    		$return_json['portrait'] = $portrait;
	    	}

    	}
    	$this->check_login('json',$return_json);
    }
    
    /**
     * 修改用户信息
     */
    public function editInfo(){
    	
    	$error_array = array('user_name'=>'','nick_name'=>'','tel'=>'','birthday'=>'');    	
    	$return_json = array('status'=>0,'user_name'=>'','address'=>'','tel'=>'','nick_name'=>'','birthday'=>'','gander'=>0,'error_array'=>$error_array);
    	if($this->is_login){
    		
    		$status_array = array();
    		$error_code_array = array();
    		
    		$user_id = $this->user_id;
    		$user_info = $this->user_info;
    		
    		$gander = filter_set_value($_POST,'gander',0,'int');
    		$birthday = trim(filter_set_value($_POST,'birthday','0000-00-00','string'));
    		$address = trim(filter_set_value($_POST,'address','','string'));
    		$birthday = is_date($birthday) ? $birthday : date('Y-m-d');
    		$user_info_object = new \Home\Model\UserInfoModel();
    		$check_other = $user_info_object->user_other_perfect($user_id,$gander,$birthday,$address);
    		if(!$check_other){
    		
    			$error_code_array = $user_info_object->get_error_code_array();
    			foreach($error_code_array as $key=>$error_code){
    				
    				$error_array['birthday'] = $this->get_lang_value($error_code);
    			}    				 
    		}
    		
    		$other_array = array();
    		$user_name = trim(filter_set_value($_POST,'user_name','','string'));
    		$phone = trim(filter_set_value($_POST,'tel','','string'));
    		$nickname = trim(filter_set_value($_POST,'nick_name','','string'));
    		 
    		$user_object = new \Home\Model\UserModel();
    		$check_tel = $user_object->change_user_relate_info($user_id,$phone,CHANGE_USER_TEL,$other_array);
    		if(!$check_tel){
    		
    			$error_code_array = $user_object->get_error_code_array();
    			foreach($error_code_array as $key=>$error_code){
    			
    				$error_array['tel'] = $this->get_lang_value($error_code);
    			}    			 			 
    		}
    		$check_nick_name = $user_object->change_user_relate_info($user_id,$nickname,CHANGE_NICK_NAME,$other_array);
    		if(!$check_nick_name){
    		
    			$error_code_array = $user_object->get_error_code_array();
    			foreach($error_code_array as $key=>$error_code){
    				 
    				$error_array['nick_name'] = $this->get_lang_value($error_code);
    			}    				
    		}
    		if(empty($user_name) && $check_nick_name){
    		
    			$user_name = $nickname;
    		}
    		$check_user_name = $user_object->change_user_relate_info($user_id,$user_name,CHANGE_USER_NAME,$other_array);
    		if(!$check_user_name){
    		
    			$error_code_array = $user_object->get_error_code_array();
    			foreach($error_code_array as $key=>$error_code){
    					
    				$error_array['user_name'] = $this->get_lang_value($error_code);
    			}    					
    		}
    		$return_json['status'] = $check_other && $check_tel && $check_nick_name && $check_user_name ? 1 : 0;
    		$user_info_view_object = new \Home\Model\UserInfoViewModel();
    		$user_info_view = $user_info_view_object->client_user_info($user_id);		 
    		if(check_array_valid($user_info)){
    		  		
    			$user_info['user_name'] = $user_info_view['user_name'];
    			$user_info['nick_name'] = $user_info_view['nick_name'];
    			$user_info['portrait'] = $user_info_view['portrait_img'];
    			session('user_info',$user_info);
    			
    			$return_json['user_name'] = $user_info_view['user_name'];
    			$return_json['address'] = $user_info_view['address'];
    			$return_json['tel'] = $user_info_view['real_tel'];
    			$return_json['nick_name'] = $user_info_view['nick_name'];
    			$return_json['birthday'] = $user_info_view['birthday'];   			
    			$return_json['gander'] = $user_info_view['gander'];    			 			
    			$return_json['error_array'] = $error_array;
    			
    		}
    	}
    	$this->check_login('json',$return_json);
    }
	
    /**
     * 登录修改密码
     */
    public function changePassword(){
    	
    	$return_json = array('status'=>0,'verify_code'=>'','result'=>'');    	
    	if($this->is_login){
    		
    		$verify_code = filter_set_value($_POST,'pass_code','','string');
    		$check = self::check_verify('change_password_verify',$verify_code,'changepassword');
    		if($check){
    			
    			$other_array = array();    		
	    		$password = filter_set_value($_POST,'new_password','','string');
	    		$other_array['source_password'] = filter_set_value($_POST,'old_password','','string');
	    		$other_array['confirm_password'] = filter_set_value($_POST,'confirm_password','','string');
	    		$user_object = new \Home\Model\UserModel();
	    		$check = $user_object->change_password($this->user_id,$password,$other_array);
	    		if($check){
	    			
	    			$return_json['status'] = 1;
	    		}else{
	    			
	    			$error_code_array = $user_object->get_error_code_array();
	    			foreach($error_code_array as $key=>$error_code){
	    				
	    				$return_json['result'] = $this->get_lang_value($error_code);
	    			}
	    		}	    		
    		}else{
    		
    			$return_json['verify_code'] = L('CaptchaInvalid');
    		}
    	}
    	$this->check_login('json',$return_json);
    }
    
    /**
     * 登录修改邮箱地址
     */
    public function changeEmail(){
    	
    	$return_json = array('status'=>0,'password'=>'','verify_code'=>'','result'=>'');    
    	if($this->is_login){
    		
    		$verify_code = filter_set_value($_POST,'icode','','string');
    		$check = self::check_verify('change_email_verify',$verify_code,'changeemail');
    		if($check){
    			 
    			$other_array = array();
    			$email = filter_set_value($_POST,'email','','string');
    			$password = filter_set_value($_POST,'password','','string');
    			
    			$user_object = new \Home\Model\UserModel();
    			$check = $user_object->web_change_email($this->user_id,$password,$email);    			
    			if($check){
    				
    				session('email_change',$email);
    				$return_json['status'] = 1;
    			}else{
    		
    				$error_code_array = $user_object->get_error_code_array();
    				foreach($error_code_array as $key=>$error_code){
    					 
    					$return_json['result'] = $this->get_lang_value($error_code);
    				}
    			}
    		}else{
    		
    			$return_json['verify_code'] = L('CaptchaInvalid');
    		}
    	}
    	$this->check_login('json',$return_json);
    }
	
    /**
     * 登录修改邮箱验证
     */
    public function changeEmailVerify(){
    	
    	$return_json = array('status'=>0);
    	if($this->is_login){
    		
    		$email = session('email_change');
    		$verify_code = filter_set_value($_POST,'verify_code','','string');
    		$user_object = new \Home\Model\UserModel();
    		$check = $user_object->email_verify($this->user_id,$verify_code,'web_change');
    		if($check){
    			
    			session('email_change',null);
    			session('send_mail_time_change',null);
    			session('send_mail_count_change',null);
    			$return_json['status'] = 1;
    		}    		
    	}
    	$this->check_login('json',$return_json);//status 值：0验证失败， 1验证成功
    }
    /**
     * 登录修改邮箱邮件发送
     */
    public function changeEmailSend(){
    	
    	$status = 0;
    	$return_json = array('status'=>$status);
    	if($this->is_login){
    		    		
	    	$current_time = time();	    	
	    	$email = session('email_change');
	    	$send_mail_time = intval(session('send_mail_time_change'));
	    	$send_mail_count = intval(session('send_mail_count_change'));
	    	if($send_mail_count <= 8 && !empty($email) && ($send_mail_time + 50) < $current_time){
	    		 
	    		$user_object = new \Home\Model\UserModel();
	    		$check = $user_object->change_email($this->user_id,$email,'web');	    		
	    		if($check){
	    			 
	    			session('send_mail_time_change',$current_time);
	    		}
	    		session('send_mail_count_change',$send_mail_count+1);
	    		$status = $check ? 1 : 2;
	    	}elseif($send_mail_count > 8 || empty($email)){
	    	
	    		$status = 2;
	    	}
    	}else{
    		
    		$status = 3;
    	}
    
    	$return_json['status'] = $status;//0 操之过快 1 成功 2 失败 3未登录
    	$this->check_login('json',$return_json);
    }    
    
    /**
     * 校检验证码
     * @param unknown $verify_name
     * @param unknown $verify_code
     * @param unknown $verify_type
     * @param string $user_account
     * @param string $login_type
     * @return boolean
     */
    protected static function check_verify($verify_name,$verify_code,$verify_type,$user_account = '',$login_type = LOGIN_WEB){

    	$check = false;
    	$verify_name = empty($verify_name) ? 'verify' : $verify_name;
    	$s_verify = session($verify_name);
    	if(!empty($s_verify)){
    		
    		session($verify_name,null);
    		$verify_code = trim($verify_code);
    		if($verify_code != ''){
    			
    			$verify_code = strtolower($verify_code);
    			$check = md5($verify_code) == $s_verify ? true : false;
    		}    		
    	}else{
    		
    		if($verify_type == 'login'){
    			
    			$check = self::check_verify_empty_valid($user_account,$login_type);
    		}    		
    	}
    	
    	return $check;
    }
    
    /**
     * 获取登录日志模型对象
     * @return Ambigous <\Home\Model\loginLogModel, NULL>
     */
    public static function get_login_log_object(){
    	
    	$login_log_object = self::$login_log_object;
    	if(is_null($login_log_object)){
    		
    		$login_log_object = new \Home\Model\loginLogModel();
    		self::$login_log_object = $login_log_object;
    	}
    	    	  	
    	return $login_log_object;
    } 
    
    /**
     * 设置登录次数
     * @param unknown $user_account
     * @param unknown $login_type
     */
    protected static function set_login_count($user_account,$login_type){
    	
    	$login_count = self::get_login_cout($user_account,$login_type);
    	$login_count = $login_count > 12000 ? $login_count : ($login_count+1);
    	session('login_count',$login_count);
    	$login_log_object = self::get_login_log_object();
    	$login_log_object->set_login_count($user_account, $login_type);    	
    }
	
    /**
     * 获取登录次数
     * @param unknown $user_account
     * @param unknown $login_type
     * @return unknown
     */
    protected static function get_login_cout($user_account,$login_type){
    	
    	$login_count = intval(session('login_count')); 
    	$login_log_object = self::get_login_log_object();
    	$user_account_login_count = $login_log_object->get_login_count($user_account,$login_type);
    	$login_count = $login_count > $user_account_login_count ? $login_count : $user_account_login_count;
    	
    	return $login_count;
    }
    
    /**
     * 清空登录次数
     * @param unknown $user_account
     * @param unknown $login_type
     */
    protected static function clear_login_count($user_account,$login_type){
    	
    	session('login_count',null);
    	$login_log_object = self::get_login_log_object();
    	$login_log_object->clear_login_count($user_account,$login_type);    
    }
    
    /**
     * 判别是否免验证码
     * @param unknown $user_account
     * @param unknown $login_type
     * @return boolean
     */
    protected static function check_verify_empty_valid($user_account,$login_type){
    	    	
    	$check = true;
    	$verify_empty_valid_count = 3;//三次免验证码
    	$login_cout = self::get_login_cout($user_account,$login_type);
    	if($login_cout >= 3){
    		
    		$check = false;
    	}
    	return $check;
    }
    
    /**
     * 生产验证码
     */
    public function verify() {
    	
    	$web_seed = session('web_seed');
    	if(empty($web_seed)){
    		 
    		$web_seed = substr(uniqid(),0,8);
    		session('web_seed',$web_seed);
    	}
    	ob_end_clean();    	
    	$tty_file = '';
    	if(isset($_GET['type']) && $_GET['type'] != 'big'){
    		
    		$width = isset($_GET['width']) && $_GET['width'] > 0 ? $_GET['width'] : 48;
    		$height = isset($_GET['height']) && $_GET['height'] > 0 ? $_GET['height'] : 22;
    	}else{
    		
    		$width = 80;
    		$height = 46;
    		$file_size = 18;
    		$tty_array = array('arial.ttf','verdana.ttf','times.ttf','georgia.ttf');
    		$tty_file_name_key = array_rand($tty_array,1);    		
    		$tty_file_name = $tty_array[$tty_file_name_key];
    		$tty_file = BASE_DIR.'Public/font/ttf/'.$tty_file_name;    		
    	}   	
    	$verify_name = isset($_GET['v']) ? trim($_GET['v']) : 'verify';
    	$verify_name = !empty($verify_name) ? $verify_name : 'verify';
    	\Org\Util\Image::buildImageVerify (4,5,'png',$width,$height,$verify_name,$tty_file,$file_size);
    }
    
    /**
     * 注册邮件发送提示界面
     */   
    public function registerMail(){
    	    	
		$email = session('email');		
    	$email_url = get_mail_url($email);
    	$click_count = intval(session('send_mail_count'));
    	
    	$footer_array = array();
    	$header_array = array(); 
    	$header_array['title'] = L('Accountsecurity');
    	$header_array['loader_css'][0] = '/css/identity.css';
    	$header_array['loader_css'][1] = '/css/auth.css'; 
    	if($this->is_mobile){
    	
    		$header_array['loader_css'][2] = '/css/mobile.css';
    	}   	    	
    	$this->head_common($header_array);
    	$this->footer_common();
    	    	
    	$this->assign('email',$email);
		$this->assign('email_url',$email_url);
		$this->assign('click_count',$click_count);
    	$this->load_model_defined('lang',array());//语言模块参数定义
		$this->display('auth');
    }
    
    /**
     * 邮件验证
     */
    public function mailVerify(){
    	
    	$title = '';
    	$message = '';
    	$verify_type = filter_set_value($_GET,'type','add','string');
    	if($verify_type == 'add'){
    		
	    	$check_verify = false;
	    	$check_already_verify = false;
	    	$token = trim(filter_set_value($_GET,'t','','string'));
	    	$confounded_stoken = trim(filter_set_value($_GET,'ct','','string'));
	    	$verify_code = trim(filter_set_value($_GET,'verify','','string'));
	    	$uid = filter_set_value($_GET,'uid',0,'int');
	    	if($uid > 0 && is_Md5($token) && is_Md5($confounded_stoken) && strlen($verify_code) > 0){
	    		$user_object = new \Home\Model\UserModel();
	    		$result = $user_object->field('email,email_verify,is_email_verify,new_email,user_status')->where ('user_id = '.$uid.' and is_delete = 0')->find();
	
	    		if (check_array_valid($result)){
	    		
	    			if($result['is_email_verify'] == 0){
	    				
	    				$cache_object = $this->redisCacheConnect();
	    				$token_object = new \Home\Model\TokenModel($cache_object);
	    				$stoken = $token_object->get_confounded_stoken_recover($confounded_stoken);
	    				$token_value = $token_object->get($stoken);
	    				if(count($token_value) > 0){
	    						
	    					if($token_value['uid'] == $uid && $token_value['verify'] == $verify_code &&  $token_value['token'] == $token){
	    						$check_verify = true;
	    						$user_value['email_verify'] = '';
	    						$user_value['is_email_verify'] = 1;
	    						$user_value['new_email'] = '';
	    						$user_value['user_status'] = 1;
	    						$user_object->where ('user_id = '.$uid)->save($user_value);    						
	    					}
	    				}
	    			}else{
	    				
	    				$check_verify = true;
	    				$check_already_verify = true;
	    			}
	    			if($check_verify){
	    				
	    				session('send_mail_time',null);
	    				session('send_mail_count',null);
	    			}
	    		}    		
	    	} 
	    	
	    	$title = L('EmailActivation');
	    	if ($check_already_verify == true){
	    		
	    		$message = L('EmailActivationAlready');
	    			    	
	    	}elseif($check_verify){
	    	
	    		$message = L('EmailActivationSuccess');
	    	}else{
	    		
	    		$message = L('EmailActivationFailed');	    		
	    	}
    	}elseif($verify_type == 'change_recover'){
    		
    		$user_id = filter_set_value($_GET,'uid','','int');
    		$verify_code = filter_set_value($_GET,'verifycode','','string');
    		 
    		$user_object = new \Home\Model\UserModel();
    		$check = $user_object->email_verify($user_id,$verify_code,'change_recover');
    		$title = L('ChangeEmailRecover');
    		$message = $check ? L('ChangeEmailRecoverSuccess') : L('ChangeEmailRecoverSuccess');
    		$this->assign('title',$title);
    		$this->assign('message',$message);
    		$this->display('mailVerify');
    	}    	  	
    	
    	$this->assign('title',$title);
    	$this->assign('message',$message);
    	$this->display('mailVerify');
    }
    
    /**
     * 注册发送邮件
     */
    public function registerMailSend(){
    	
    	$status = 0;
    	$current_time = time();
    	$email = session('email');
    	$send_mail_time = intval(session('send_mail_time'));
    	$send_mail_count = intval(session('send_mail_count'));
    	if($this->user_id > 0 && $send_mail_count <= 3 && !empty($email) && ($send_mail_time + 50) < $current_time){
    	     
    		$cache_object = $this->redisCacheConnect();
    		$sid = get_sid();
    		$token = get_token($sid);
    		$token_object = new \Home\Model\TokenModel($cache_object);
    		$stoken = $token_object->get_stoken($token);
    		$confounded_stoken = $token_object->get_confounded_stoken($stoken);
    		$verify_code = \Home\Model\UserModel::get_email_verify(0,13);
    		$token_value = array();
    		$uid = $this->user_id;
    		$token_value['uid'] = $uid;//用户id
    		$token_value['token'] = $token;
    		$token_value['verify'] = $verify_code;
    		$token_object->set($stoken,$token_value);
    		$user_info = $this->user_info;
    		$user_name = $user_info["user_name"];
    		$user_object = new  \Home\Model\UserModel();
    	
    		$verify_url = getBaseURL().MODULE_NAME.'/Index/mailVerify.html?l='.LANG_SET.'&t='.$token.'&ct='.$confounded_stoken.'&verify='.$verify_code.'&uid='.$uid;
    		$check = $user_object->web_send_email($uid,$user_name,$email,'','add','','',NOT_IS_TXI,$verify_url);
    		if($check){
    			
    			session('send_mail_time',$current_time);    			
    		} 
    		session('send_mail_count',$send_mail_count+1);
    		$status = $check ? 1 : 2;
    	}elseif($this->user_id <= 0 || $send_mail_count > 3 || empty($email)){
    		
    		$status = 2;
    	}
    	$json_array = array();
    	$json_array['status'] = $status;//0 操之过快 1 成功 2 失败
    	echo json_encode($json_array);
    	exit;    	
    }    

    /**
     * 发送邮件找回密码
     */
    public function faqList(){
    	
    	$footer_array = array();
    	$header_array = array();
    	 	
    	$header_array['title'] = L('FAQ');
    	$header_array['loader_css'][0] = '/css/faq.css';
    	$header_array['loader_css'][1] = '/css/reset.css';
    	$header_array['loader_css'][2] = '/css/layout.css';
    	if($this->is_mobile){
    	
    		$header_array['loader_css'][3] = '/css/mobile.css';
    	}
    	$header_array['loader_js'][0] ='/js/security.js';
    	    		
    	$this->head_common($header_array);
    	$this->footer_common();
    	$this->load_model_defined('lang',array());//语言模块参数定义
    	$this->display('faqList');
    }    
    
    /**
     * 登录成功跳转
     * @param string $check_redirect 是否直接跳转
     */
	protected function login_success_redirect($check_redirect = false){
    
    	$token = session('token');
    	$redirect_url = session('redirect_url');
    	$http_referer = session('http_referer');
    	$is_perfect = $this->is_perfect;//完善资料 暂时不处理
    	if(is_Md5($token) && strlen($redirect_url) > 0){
    			
    		if(strpos($redirect_url,'?') > 0){
    
    			$redirect_url .= '&';
    		}else{
    				
    			$redirect_url .= '?';
    		}
    	 
    		$token_value = array();
    		$token_value['token'] = $token;
    		$token_value['user_info'] = $this->user_info;
    		$cache_object = $this->redisCacheConnect();
    		$stoken = \Home\Model\TokenModel::get_stoken($token);
    		
    		$token_object = new \Home\Model\TokenModel($cache_object);
    		$token_object->set($stoken,$token_value);
    		
    		$ctoken = \Home\Model\TokenModel::get_confounded_stoken($stoken);
    		$redirect_url .= 'ctoken='.$ctoken.'&referer='.urlencode($http_referer);

    		session('token',null);
    		session('http_referer',null);
    		session('redirect_url',null);
    		
    		redirect($redirect_url,0,'');
    	}else{
    			
    		$base_url = getBaseURL();
    		if(strtolower($base_url) != strtolower(substr($http_referer,0,strlen($base_url)))){
    				
    			$http_referer = '';
    		}
    		$url = empty($http_referer) ? get_home_url($this->is_mobile) : $http_referer;
    		if($check_redirect){
    			
    			redirect($url,0,'');
    		}else{
    			
    			$this->success(L('LoginSuccess'),$url,false);    			
    		}    		
    	}
    	exit;
    }
    
    /**
     * 生成用户中心种子
     * @param unknown $web_seed
     * @param unknown $type
     * @return string
     */
    protected static function get_user_center_seed_code($web_seed,$type){
    	
    	$type = strval(intval($type));
    	$web_seed = trim($web_seed);    	
    	$ip = get_client_ip(0,true);
    	$ip_md5 = md5($ip);
    	$seed_code = md5($type.substr($ip_md5,8,8).$web_seed.$ip);
    	
    	return $seed_code;
    }
    
    /**
     * 与用户中心交互方法
     * @param unknown $url
     * @param unknown $json_array
     * @param unknown $other_array
     * @param string $action_type
     * @param string $cacert_url
     * @param string $check_ssl
     * @param string $input_charset
     * @return NULL or json object
     */
    protected function user_center_interactive($url,$json_array,$other_array = array(),$action_type = 'POST',$cacert_url = '',$check_ssl = false,$input_charset = ''){
    	
    	$return_data = '';
    	$json_request_object = null;
    	$crypt_key = $this->crypt_key;
    	\Think\Crypt::init($this->crypt_type);
    	if(strtolower($action_type) == 'GET'){
    		
    		$return_data = getHttpResponseGET($url,$cacert_url,$check_ssl);
    	}else{
    		
    		$json_array_encode = json_encode($json_array);
    		$data_str = urlencode(base64_encode(\Think\Crypt::encrypt($json_array_encode,$crypt_key,0)));
    		$para = (array)$other_array;
    		$para['data'] = $data_str;    		
    		$return_data = getHttpResponsePOST($url,$cacert_url,$para,$input_charset,$check_ssl);
    	}  	
    	
    	$return_data = trim($return_data);
    	if(strlen($return_data) > 0){
    			
    		$request_data = base64_decode(urldecode($return_data));
    		if($request_data && strlen($request_data) > 0){
    				
    			$request_data = \Think\Crypt::decrypt($request_data,$crypt_key);    			
    			if($request_data && strlen($request_data) > 0){
    					
    				$json_request_object = @json_decode($request_data);
    			}
    		}
    	}
    		
    	return $json_request_object;   	
    }
}