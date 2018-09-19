<?php
/**
 * 云相册
 * @author kxd
 *
 */
namespace Home\Controller;
use Think\Controller;
class GalleryController extends \Home\Controller\DefaultController {
	
	protected $check_login_type = 'msg';
	protected $igore_action_name_array = array();
	protected $photo_object = null;
	protected $albumPhoto_object = null;
	protected $album_object = null;
	protected $albumPhoto_view_object =NULL;
	protected $page_size = 40;
	protected $error = array();//错误信息
	
	public function __destruct(){
		
		parent::__destruct();
		$this->page_size = 0;
		$this->photo_object = null;
		$this->albumPhoto_object = null;
		$this->album_object = null;
		$this->albumPhoto_view_object = null;
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

			$this->photo_object = new \Home\Model\UserPhotoModel();
			$this->albumPhoto_object = new \Home\Model\AlbumPhotoModel();
			$this->album_object = new \Home\Model\UserAlbumModel();
			$this->albumPhoto_view_object = new \Home\Model\AlbumPhotoViewModel();
		}
	}
	
	/**
	 * 通过album_id获取photo_id
	 */
	public function  get_photo_id(){
		
		$return_json = array('status'=>0,'total_count'=>0,'photo_id'=>'');
		
		if ($this->is_login){
			
			$user_id = $this->user_id;
			$id = filter_set_value($_POST,'aid',0,'int');
			$return_json['id'] =$id; 
			$str_where['user_id'] = $user_id;
			$str_where['album_id'] = $id;
			$str_where['is_delete'] = 0; 
			$result = $this->albumPhoto_view_object->where($str_where)->field('photo_id')->select(); 
			if ($result){  
				
				foreach($result as $key=>$val){
					
					$photo_id_arr[] = $val['photo_id'];
				}
				$return_json['photo_id'] = $photo_id_arr;
				$return_json['status'] = 1;				
			}
		}
		
		$this->check_login('json',$return_json);
	}
	
	/**
	 * 通过id获取photo信息
	 */
	public function  get_photo_info(){
		
		$return_json = array('status'=>0,'info'=>0);
		if ($this->is_login){
			
			$user_id = $this->user_id;
			$id = filter_set_value($_POST,'id',0,'int');
			$str_where['user_id'] = $user_id;
			$str_where['id'] = $id;
			$field='photo_name,photo_size,photo_width,photo_height,photo_type,add_time';
			$result = $this->photo_object->where($str_where)->field($field)->find();
			if ($result){
				
				$return_json['info'] = $result;
				$return_json['status'] = 1;	
			}
		}
		$this->check_login('json',$return_json);
	}	
	
	//下载相片
	public function down_photo(){
	
		$return_json = array('status'=>0,'total_count'=>0);		
		if ($this->is_login){
			
			$user_id = $this->user_id;
			$id_str = filter_set_value($_GET,'photo_id',0,'int');
			$where['v_ap.user_id'] = $user_id;
			$where['v_ap.photo_id'] = $id_str;
			$join_str=' as v_ap LEFT JOIN db_photo.tb_user_album as a on a.id = v_ap.album_id';
			$field_str = 'v_ap.photo_id,v_ap.photo_name,a.server_path,v_ap.photo_path';
			$result = $this->albumPhoto_view_object->join($join_str)->field($field_str)->where($where)->find($where);
			if (check_array_valid($result)){
				
				$photo_Name = $result["photo_name"];
				$photo_path = getFullUrl($result["server_path"].$result["photo_path"]);
				$this->download_pictures($photo_Name, $photo_path);
			}
		}
		exit;
	}
	
	//下载图片
	protected function download_pictures($Name,$photo_url){
		
		$fileTmp = pathinfo($photo_url);
		$fileExt = $fileTmp['extension'];
		$saveFileName = ($Name.'.'.$fileExt);
		$fp=fopen($photo_url,'r');
		//下载文件需要用到的头
		Header('Content-type: image/'.$fileExt);
// 		Header('Content-type: application/octet-stream');
		Header('Accept-Ranges: bytes');
// 		Header('Accept-Length:'.$file_size);
		Header('Content-Disposition: attachment; filename='.$saveFileName);
		$buffer=1024;
		$file_count=0;
		//向浏览器返回数据
		while(!feof($fp)){
			$file_con=fread($fp,$buffer);
			$file_count+=$buffer;
			echo $file_con;
		}
		fclose($fp);
		exit;
	}
	
	/**
	 *相册
	 */
    public function index(){
    	
    	$page_no = 1;
    	$header_array = array();
    	$footer_array = array();
    	$header_array['loader_css'][0] = '/css/home.css';
    	$header_array['loader_css'][1] = '/css/animation.css';
    	$header_array['loader_css'][2] = '/css/indexhome.css';
    	$header_array['loader_css'][3] = '/css/myindex.css';
    	$header_array['loader_css'][4] = '/css/gallery.css';
    	if($this->is_mobile){
    	
    		$header_array['loader_css'][5] = '/css/mobile.css';
    	}
    	$header_array['loader_js'][0] ='/js/gallery.js';
    	$header_array['title'] = L('Gallery');
    	$this->head_common($header_array);
    	$this->footer_common($footer_array);
    	
    	$user_id = $this->user_id;
    	$page_size = intval($this->page_size);
    	$page_size = $page_size > 0 ? $page_size : 30;    	
    	$list_array = $this->albumPhoto_view_object->get_album_photo_list($user_id,$page_no,$page_size,0);
 
		$this->assign('page_size',$page_size);
    	$this->assign('photo_data',$list_array['list']);
    	$this->assign('photo_data_count',$list_array['total_count']);
    	$this->display();    	
    }
    
    
    //相册列表（加到相册）
    public function get_album(){
    	
    	$return_json = array('status'=>0,'list'=>0,'total_count'=>0);
    	$user_id = $this->user_id;
    	$update_array = array();
    	if ($this->is_login){
    		
    		$where['user_id'] = $user_id;
    		$where['is_delete'] = 0;
    		$where['status'] = 1;    		
	    	$result = $this->album_object->getList('',$where,'');
	    	
	    	if(check_array_valid($result)){
	    	
				foreach($result as $key=>$val){
						
					$list[$key] = $val;
					$list[$key]['photo_path'] = getFullUrl($val['server_path'].$val['default_photo_path']);
				}
	    		
    			$return_json['list'] = $list;
    			$return_json['status'] = 1;
	    	}
    	}    	
    	
    	$this->check_login('json',$return_json);
    }    
    
    /**
     * 移动/复制 相册
     */
    public function move_photo(){
    	
    	$return_json = array('status'=>0,'result'=>'');    	
    	if ($this->is_login){
    		    	   		
    		$name = filter_set_value($_POST,'name','','string');
    		$name = $name == 'move'? $name : 'copy';
    		if($name == 'copy'){
    			 
    			$current_time = time();
    			$photo_copy_time = floatval(session('photo_copy_time'));
    		}
    		if($name == 'move' || ($name == 'copy' && $current_time - $photo_copy_time > 8)){
    			
	    		$user_id = $this->user_id;
	    		$dest_album_id = filter_set_value($_POST,'dest_album_id',0,'int');
	    		$source_album_id = filter_set_value($_POST,'source_album_id',0,'int');
	    		$photo_id = filter_set_value($_POST,'photo_id_array','','string');
	    		$photo_id = trim($photo_id);
	    		if(!empty($photo_id)){
	    		
	    			$photo_id_array = explode(',',$photo_id);
	    		}
	    		$album_object = $this->album_object;    		
    			
    			$method_name = 'client_'.$name;
    			$result = $album_object->$method_name($user_id,$dest_album_id,$source_album_id,$photo_id_array);
    			if($result){
    				
    				$return_json['status'] = 1;
    				if($name == 'copy'){
    					
    					session('photo_copy_time',$current_time);
    				}
    			}else{    				 
    				
    				$error_code_array = $album_object->get_error_code_array();
    				foreach($error_code_array as $error_code){
    			
    					$return_json['result'] = $this->get_lang_value($error_code);
    				}
    			}
    		}else{
    			
    			$return_json['result'] = $this->get_lang_value(ERROR_OPERATING_TOO_FAST);
    		}
    		
    	}
    	$this->check_login('json',$return_json);
    }    
    
   /**
    * 移入回收站相册分组
    */
    public function recy_album(){
    	
    	$return_json = array('status'=>0,'total_count'=>0);    	
    	if ($this->is_login){
    		
    		$user_id = $this->user_id;
    		$update_array = array();
    		$id = filter_set_value($_POST,'aid',0,'int');
    		$is_recy = filter_set_value($_POST,'is_recy',0,'int');
    		
    		$where['user_id'] = $user_id;
    		$where['album_id'] = $id;
    		$update_array['is_delete'] = $is_recy;
	    	$result = $this->albumPhoto_view_object->info_update($where,$update_array);
    		if($result){
    			
    			$return_json['status'] = 1;
    		}
    	}   	
    	
    	$this->check_login('json',$return_json);
    }
    
    /**
     * 删除相册分组（不可恢复）
     */
    public function  delete_album(){
    	
    	$return_json = array('status'=>0,'total_count'=>0);    	
    	if ($this->is_login){
    		
    		$user_id = $this->user_id;
    		$album_id_array = array();
    		$album_id_str = filter_set_value($_POST,'aid','','string');
    		$album_id_str = trim($album_id_str);
    		if(!empty($album_id_str)){
    		
    			$album_id_array = explode(',',$album_id_str);
    		}
    		
    		$result = $this->album_object->client_delete($album_id_array,$user_id);
    		if($result){
    			$return_json['status'] = 1;
    		}
    	}
    	
    	$this->check_login('json',$return_json);
    }
    
    /**
     * 删除某个相册下的相关相片（不可恢复）
     */
    public function  delete_photo(){
    	
    	$return_json = array('status'=>0);    	
    	if ($this->is_login){
    		
    		$user_id = $this->user_id;
    		$photo_id_array = array();
    		$album_id = filter_set_value($_POST,'album_id','','string');
    		$photo_id = filter_set_value($_POST,'photo_id_array','','string');
    		$photo_id = trim($photo_id);
    		if(!empty($photo_id)){
    
    			$photo_id_array = explode(',',$photo_id);
    		}
    		$return_json['photo_id_array'] = $photo_id_array;
    		$result = $this->photo_object->client_delete($user_id,$album_id,$photo_id_array);
    		if($result){
    			$return_json['status'] = 1;
    		}
    	}
    	$this->check_login('json',$return_json);
    }
    
    
    
    /**
     * 移入回收站photo相册
     */
    public function recy_photo(){
    	
    	$return_json = array('status'=>0,'total_count'=>0);    	
    	if ($this->is_login){
    		
    		$user_id = $this->user_id;
    		$update_array = $id_array = array();
    		$id_str = filter_set_value($_POST,'array_id','','string');
    		$aid = filter_set_value($_POST,'aid',0,'int');
    		$is_recy = filter_set_value($_POST,'is_recy',0,'int');
    		$id_where = substr($id_str,0,strlen($id_str)-1);
    		$id_array = explode(',',$id_where );
    		$where['user_id'] = $user_id;
    		$where['id'] = array('in',$id_where);
    		$update_array['is_delete'] = $is_recy;
    		$result = $this->photo_object->info_update($where,$update_array);
			
    		if($result){
    			
    			$return_json['status'] = 1;
    		}
    	}  	 
    	 
    	$this->check_login('json',$return_json);
    }
    
    
    
    
    /**
     * ajax 便签相册分类列表/回收站
     * is_recy 0 未删除 1 回收站
     * p 当前页码
     */
    public function ajax_note_album(){
    	
    	$return_json = array('status'=>0,'count'=>0,'list'=>array());    	
    	if ($this->is_login){
    		
    		$user_id = $this->user_id;
    		$is_recy = filter_set_value($_POST,'is_recy',0,'int');
    		$page_no = filter_set_value($_POST,'p',1,'int');
    		
	    	$page_size = intval($this->page_size);
	    	$page_no = $page_no > 0 ? $page_no : 1;
	    	$page_size = $page_size > 0 ? $page_size : 30;
	    	$list_array = $this->albumPhoto_view_object->get_album_photo_list($user_id,$page_no,$page_size,$is_recy);
    		$return_json['count'] = $list_array['total_count'];
    		$return_json['list'] = $list_array['list'];
    		$return_json['status'] = 1;
    	}
    	$this->check_login('json',$return_json);
    }
    
    /**
     *  ajax获取便签某相册中的相片列表
     *  is_recy 0 未删除 1 回收站
     * 	p 当前页码
     *  aid 
     *  分组id
     */
    public function get_photo_list(){
    	
    	$return_json = array('status'=>0,'list'=>array(),'count'=>0);
    	if($this->is_login){
    		
    		$user_id = $this->user_id;
    		$page_size = intval($this->page_size);
    		$album_id = filter_set_value($_POST,"aid",0,'int');
    		$page_no = filter_set_value($_POST,"p",1, 'int');
    		$is_recy = filter_set_value($_POST,'is_recy',0,'int');
    		$is_recy = $is_recy == 1 ? 1 : 0;
    		$order_by ="v_ap.add_time";
    		$order_way = 'desc';
    		$group_by = '';
    		$where['v_ap.user_id'] = $user_id;
    		$where['v_ap.is_delete'] = $is_recy;
    		$where['v_ap.album_id'] = $album_id;
    		$where['v_ap.status'] = 1;
    		$join_str=' as v_ap LEFT JOIN db_photo.tb_user_album as a on a.id = v_ap.album_id';
    		$field_str = 'v_ap.album_id,v_ap.photo_id,v_ap.photo_name,v_ap.photo_width ,v_ap.photo_height,v_ap.photo_type,v_ap.photo_tmp_path,v_ap.photo_path,v_ap.small_photo_path,a.server_path';
    		$result = $this->albumPhoto_view_object->get_page_list($page_no,$page_size,$order_by,$order_way,$group_by,$where,$join_str,$field_str);
	    	$photo_data_count =  $this->albumPhoto_view_object->get_list_count($group_by ,$where,$join_str ,'');
       		if(check_array_valid($result)){
       			
    			foreach ($result as $key=>$val){
    				$list[$key]=$val;
    				$list[$key]['small_photo_path']=getFullUrl($val['server_path'].$val['small_photo_path']);
    				$list[$key]['photo_path']=getFullUrl($val['server_path'].$val['photo_path']);
    			}
    		}
    		$return_json['count'] = $photo_data_count;
    		$return_json['list'] = $list;
    		$return_json['status'] = 1;
    	}
    	$this->check_login('json',$return_json);
    }

	/**
	 * 改名
	 */
	public function changeAlbumName(){

		$return_json = array('status'=>0,'result'=>'');
		if($this->is_login){
			
			$user_id = $this->user_id;
			$album_id = filter_set_value($_POST,'aid',0,'int');
			$album_name  = filter_set_value($_POST,'album_name','','string');
			$album_object = $this->album_object;
			$check = $album_object ->change_album_name($album_id,$album_name,$user_id,true);
			if($check){
				
				$return_json['status'] = 1;
			}else{

				$error_code_array = $album_object->get_error_code_array();
				foreach ($error_code_array as $error_code){

					$return_json['result'] = $this->get_lang_value($error_code);
				}
			}
		}
		$this->check_login('json',$return_json);
	}
      
}