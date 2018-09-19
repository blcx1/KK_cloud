<?php
/**
 * 便签
 * @author kxd
 *
 */
namespace Home\Controller;
use Think\Controller;
class NoteController extends \Home\Controller\DefaultController {
	
	protected $check_login_type = 'msg';
	protected $igore_action_name_array = array();
	protected $note_model_object = null;
	protected $page_size = 30;
	
	public function __destruct(){
		parent::__destruct();
		$this->page_size = 0;
		$this->note_model_object = null;
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
	
			$this->note_model_object = new \Home\Model\UserNoteModel();
		}
	}
	/**
	 * 便签
	 */
    public function index(){
        
    	$user_id = $this->user_id;
    	$page_size = intval($this->page_size);
    	$page_size = $page_size > 0 ? $page_size : 30;
    	$header_array = array();
    	$footer_array = array();
    	$header_array['loader_css'][0] = '/css/home.css';
    	$header_array['loader_css'][1] = '/css/indexhome.css';
    	$header_array['loader_css'][2] = '/css/animation.css';
    	$header_array['loader_css'][3] = '/css/note.css';
    	if($this->is_mobile){
    	
    		$header_array['loader_css'][4] = '/css/mobile.css';
    	}
    	$header_array['loader_js'][0] ='/js/note.js';
    	$header_array['title'] = L('Note');
    	$this->head_common($header_array);
    	$this->footer_common($footer_array);
    	
    	$type_id = 1;
    	$note_data = array();
    	$recovery_note = array();    	
    	$note_model_object= $this->note_model_object;
    	$result = $note_model_object->get_pagelist($user_id,$type_id,1,$page_size,0);
    	$list_array = $result['list'];
        
        //正则去掉标记字符
        $igore_array = array('[*&*&]','[&#&#]','[@$@$]','[#*#*]','[&$&$]','[*&$#@&*%$@%%*#%$]');
         if (check_array_valid($list_array)){
             foreach($list_array as $k=>$v){
                 $content = str_replace($igore_array,"",$list_array[$k]['data']['content']);
                 $list_array[$k]['data']['content']=$content;
             }
         }
        
    	$note_data_total_count = $result['total_count'];
    	 if (check_array_valid($list_array)){
    	 	
	    	foreach ($list_array as $key=>$val){
	    		
	    		$note_data[$key] = $val;
	    	}
    	 }
    	 
    	 $recovery_result = $note_model_object->get_pagelist($user_id,$type_id,1,$page_size,1);
    	 $list_array = $recovery_result['list'];
    	 $recovery_note_total_count = $recovery_result['total_count'];
        if (check_array_valid($list_array)){
                   foreach ($list_array as $key=>$val){
                           $recovery_note[$key] = $val;
                   }
        }

    /*    foreach($note_data as $k=>$v){
            $str=$note_data[$k]['data']['content'];
            $str=str_replace('<br />', '', $str);
            if(preg_match('/\/KXDnotes/',$str)){
                    $arr=explode('/KXDnotes',$str);
                    for($i=0;$i<count($arr);$i++){
                            $arr[$i]=preg_replace('/\/KXD.*\.mp3/','<br /><img width="30" src="'.__ROOT__.'/Public/images/music.png"/><br />',$arr[$i]);
                            $arr[$i]=preg_replace('/\/KXD.*\.mp4|\/KXD.*\.avi|\/KXD.*\.mpg/','<br /><img width="30" src="'.__ROOT__.'/Public/images/video.png"/><br />',$arr[$i]);
                            $arr[$i]=preg_replace('/\/KXD.*\.png|\/KXD.*\.jpg|\/KXD.*\.gif/','<br /><img width="30" src="'.__ROOT__.'/Public/images/pic.png"/><br />',$arr[$i]);
                    }
                    $str=implode('',$arr);
            }
            $note_data[$k]['data']['content']=$str;
        }*/
        
        
         $this->assign('page_size',$page_size);
         $this->assign('recovery_note_total_count',$recovery_note_total_count);
         $this->assign('note_data_total_count',$note_data_total_count);
    	 $this->assign('recovery_note',$recovery_note);
    	 $this->assign('note_data',$note_data);
    	 $this->display('note');
    }
    
    
    /**
     * Ajax获取便签列表
     */

    public function getnoteList(){
    		
    	$return_json = array('status'=>0,'total_count'=>0,'list'=>array());
    	if($this->is_login){
    
    		$page_size = intval($this->page_size);
    		$page_size = $page_size > 0 ? $page_size : 30;
    		$type_id = filter_set_value($_POST,'type_id',1,'int');
    		$page_no = filter_set_value($_POST,'p',1,'int');
    		$is_recy = filter_set_value($_POST,'is_recy',0,'int');
    		$is_recy = $is_recy == 1 ? 1 : 0;
    		$return_json = $this->note_model_object->get_pagelist($this->user_id,$type_id,$page_no,$page_size,$is_recy);
                
    		$return_json['status'] = 1;
    	}

    	$this->check_login('json',$return_json);
    }
    
    /**
     * 获取便签详情信息
     */
    
    public function get_note_info(){
    	
    	$return_json = array('status'=>0,'info'=>array());    	 
    	if ($this->is_login){
    		
	    	$id = filter_set_value($_POST, 'id',0,'int');	    	
	    	$return_json['info'] = $this->note_model_object->get_user_noteinfo($id,$this->user_id);
    		$return_json['status'] = 1;
    	}
    	$this->check_login('json',$return_json);    	 
    }
   
    /**
     * 删除回收站（不可恢复）
     */
    public function delete_note(){
    	
    	$return_json = array('status'=>0);    	
    	if ($this->is_login){
    		
    		$id = filter_set_value($_POST,'id','','string');
    		$type_id = filter_set_value($_POST,'type_id',1,'int');    		
    		$check = $this->note_model_object->delete_process($id,$this->user_id,$type_id);
    		if ($check){
    			
    			$return_json['status'] = 1;
    		}
    	}
    	$return_json['id'] = $id ;
    	$this->check_login('json',$return_json);
    }
    
    /**
     * 移入回收站/恢复数据
     */
    public function up_recy_note(){
    	
    	$return_json = array('status'=>0);    	
    	if ($this->is_login){
    		
	    	$id = filter_set_value($_POST,'id',0,'int');
	    	$recy_id = filter_set_value($_POST,'recy_id',1,'int');	    	
	    	$check = $this->note_model_object->recy_note($id,$this->user_id,1,$recy_id);
	    	if ($check){
	    		
		    	$return_json['status'] = 1;
	    	}
    	}
    	$this->check_login('json',$return_json);
    }
}