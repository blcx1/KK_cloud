<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2012 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
namespace Behavior;
/**
 * 语言检测 并自动加载语言包
 */
class CheckLangBehavior {

    // 行为扩展的执行入口必须是run
    public function run(&$params){
        // 检测语言
        $this->checkLanguage();
    }

    /**
     * 语言检查
     * 检查浏览器支持语言，并自动加载语言包
     * @access private
     * @return void
     */
    private function checkLanguage() {
        // 不开启语言包功能，仅仅加载框架语言文件直接返回
        if (!C('LANG_SWITCH_ON',null,false)){
            return;
        }
        if(!defined('LANG_SET')){
        	
	        $langSet = C('DEFAULT_LANG');
	        $varLang =  C('VAR_LANGUAGE',null,'l');
	        $langList = C('LANG_LIST',null,'zh-cn');
	        $is_not_cookie = false;
	        // 启用了语言包功能
	        // 根据是否启用自动侦测设置获取语言选择
	        if (C('LANG_AUTO_DETECT',null,true)){
	            if(isset($_GET[$varLang])){
	                $langSet = $_GET[$varLang];// url中设置了语言变量	                
	                if(!$is_not_cookie){
	                	
	                	cookie('think_language',$langSet,3600);
	                }	                
	            }elseif(cookie('think_language')){// 获取上次用户的选择
	                $langSet = cookie('think_language');
	            }elseif(isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])){// 自动侦测浏览器语言
	            	
	                preg_match('/^([a-z\d\-]+)/i', $_SERVER['HTTP_ACCEPT_LANGUAGE'], $matches);
	                $langSet = $matches[1];
	                if(!$is_not_cookie){
	                	
	               		 cookie('think_language',$langSet,3600);
	                }
	            }
	            
	            $check_use_default = false;            
	            if(false === stripos($langList.',zh-tw,zh-sg',$langSet)){
	            	
	            	$check_use_default = true;
	            }else{
	            	
	            	$lang_set_lower = str_replace('_','-',strtolower($langSet));	            	
	            	switch($lang_set_lower){
	            		
	            		case 'zh':
	            		case 'cn':
	            		case 'zh-cn'://中文(中华人民共和国)
	            			
	            			$langSet = 'zh-cn';
	            			break;
	            		case 'zh-tw'://中文(中国台湾)
	            		case 'zh-hk'://中文(中国香港特别行政区)	            			
	            		case 'zh-sg'://中文(新加坡)
	            			
	            			$langSet = 'zh-hk';
	            			break;
	            		case 'en':
	            		case 'us':
	            		case 'en-us':
	            			
	            			$langSet = 'en-us';
	            			break;
	            		default :
	            			
	            			$langList = strtolower($langList);
	            			$lang_list_array = explode(',',$langList);
	            			if(is_array($lang_list_array)){
	            				
	            				foreach($lang_list_array as $lang_name){
	            					
	            					if(strpos($lang_name,$lang_set_lower) !== false){
	            						
	            						$langSet = $lang_name;
	            						break;
	            					}
	            				}
	            			}else{
	            				
	            				$langSet = $langList;
	            			}	            			
	            	}	            	
	            }
	               
	            if($check_use_default) { // 非法语言参数
	            	
	                $langSet = C('DEFAULT_LANG');
	                if(empty($langSet)){
	                	
	                	$langSet = 'en-us';
	                }	                
	            }
	        }
	    
	        // 定义当前语言
	        define('LANG_SET',strtolower($langSet));	      
   		}elseif(!cookie('think_language')){
   		
   			cookie('think_language',LANG_SET,3600);
   		}
   		
   		$controller_name = strtolower(CONTROLLER_NAME);
   		
        // 读取框架语言包
        $file   =   THINK_PATH.'Lang/'.LANG_SET.'.php';
        if(LANG_SET != C('DEFAULT_LANG') && is_file($file))
            L(include $file);

        // 读取应用公共语言包
        $file   =  LANG_PATH.LANG_SET.'.php';
        if(is_file($file))
            L(include $file);
        
        // 读取当前控制器下对应的公共语言包
        $file   =  LANG_PATH.LANG_SET.'/'.$controller_name.'.php';
        if(is_file($file))
        	L(include $file);
        
        // 读取模块语言包
        $file   =   MODULE_PATH.'Lang/'.LANG_SET.'.php';
        if(is_file($file))
            L(include $file);

        // 读取当前控制器语言包
        $file   =   MODULE_PATH.'Lang/'.LANG_SET.'/'.$controller_name.'.php';
        if (is_file($file))
            L(include $file);
    }
}
