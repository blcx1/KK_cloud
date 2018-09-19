<?php 
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2014 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
namespace Think\Session\Driver;
/**
 * 数据库方式Session驱动
 *    CREATE TABLE think_session (
 *      session_id varchar(255) NOT NULL,
 *      session_expire int(11) NOT NULL,
 *      session_data blob,
 *      UNIQUE KEY `session_id` (`session_id`)
 *    );
 */
class Db {

    /**
     * Session有效时间
     */
   protected $lifeTime      = ''; 

    /**
     * session保存的数据库名
     */
   protected $sessionTable  = '';

    /**
     * 数据库句柄
     */
   protected $hander  = array(); 
	
   protected $db_type = '';
   
    /**
     * 打开Session 
     * @access public 
     * @param string $savePath 
     * @param mixed $sessName  
     */
    public function open($savePath, $sessName) { 
	
		$this->lifeTime = C('SESSION_EXPIRE')?C('SESSION_EXPIRE'):ini_get('session.gc_maxlifetime');
		$this->sessionTable  =   C('SESSION_TABLE')?C('SESSION_TABLE'):C("DB_PREFIX")."session";

		$this->db_type = $db_type = C('DB_TYPE');

		//分布式数据库
		$host = explode(',',C('DB_HOST'));
		$port = explode(',',C('DB_PORT'));
		$name = explode(',',C('DB_NAME'));
		$user = explode(',',C('DB_USER'));
		$pwd  = explode(',',C('DB_PWD'));
		
		
		if(1 == C('DB_DEPLOY_TYPE')){
		   //读写分离
		   if(C('DB_RW_SEPARATE')){
			    $w = floor(mt_rand(0,C('DB_MASTER_NUM')-1));
			    if(is_numeric(C('DB_SLAVE_NO'))){//指定服务器读
				   $r = C('DB_SLAVE_NO');
			    }else{
				   $r = floor(mt_rand(C('DB_MASTER_NUM'),count($host)-1));
			    }			   
				
				$config_w['hostname'] = $host[$w];
				$config_w['username'] = isset($user[$w])?$user[$w]:$user[0];
				$config_w['password'] = isset($pwd[$w])?$pwd[$w]:$pwd[0];
				$config_w['database'] = isset($name[$w])?$name[$w]:$name[0];
				$config_w['hostport'] = isset($port[$w])?$port[$w]:$port[0];
				
				$config_r['hostname'] = $host[$r];
				$config_r['username'] = isset($user[$r])?$user[$r]:$user[0];
				$config_r['password'] = isset($pwd[$r])?$pwd[$r]:$pwd[0];
				$config_r['database'] = isset($name[$r])?$name[$r]:$name[0];
				$config_r['hostport'] = isset($port[$r])?$port[$r]:$port[0];
				
			    if($db_type === 'mysqli'){
				   
					$hander = new \mysqli($config_w['hostname'],$config_w['username'],$config_w['password'],$config_w['database'],$config_w['hostport']);
					if(mysqli_connect_errno()){
				
						return false; 
					}
					$hander->query("SET NAMES ust8'");
					if($hander->server_version >'5.0.1'){
						
						$hander->query("SET sql_mode=''");
					}
					$this->hander[0] = $hander;
					$hander = new \mysqli($config_r['hostname'],$config_r['username'],$config_r['password'],$config_r['database'],$config_r['hostport']);
					if(mysqli_connect_errno()){
				
						return false; 
					}
					$hander->query("SET NAMES ust8'");
					if($hander->server_version >'5.0.1'){
						
						$hander->query("SET sql_mode=''");
					}
			    }else{
				   
				   //主数据库链接
				   $hander = mysql_connect($config_w['hostname'].':'.$config_w['hostport'],$config_w['username'],$config_w['password']);
				   $dbSel = mysql_select_db($config_w['database'],$hander);
				   if(!$hander || !$dbSel)
					   return false;
				   $this->hander[0] = $hander;
				   
				   //从数据库链接
				   $hander = mysql_connect($config_r['hostname'].':'.$config_r['hostport'],$config_r['username'],$config_r['password']);
				   $dbSel = mysql_select_db($config_r['database'],$hander);
				   if(!$hander || !$dbSel)
					   return false;
				   
			    }
				$this->hander[1] = $hander;
				return true;
			   
		   }
		}
		//从数据库链接
		$r = floor(mt_rand(0,count($host)-1));
		
		$config_r['hostname'] = $host[$r];
		$config_r['username'] = isset($user[$r])?$user[$r]:$user[0];
		$config_r['password'] = isset($pwd[$r])?$pwd[$r]:$pwd[0];
		$config_r['database'] = isset($name[$r])?$name[$r]:$name[0];
		$config_r['hostport'] = isset($port[$r])?$port[$r]:$port[0];
		
		if($db_type === 'mysqli'){	
			
			$hander = new \mysqli($config_r['hostname'],$config_r['username'],$config_r['password'],$config_r['database'],$config_r['hostport']);
			if(mysqli_connect_errno()){
				
				return false; 
			}
			$hander->query("SET NAMES ust8'");
			if($hander->server_version >'5.0.1'){
				
				$hander->query("SET sql_mode=''");
			}			
		}else{
			
			$hander = mysql_connect($config_r['hostname'].':'.$config_r['hostport'],$config_r['username'],$config_r['password']);
			$dbSel = mysql_select_db($config_r['database'],$hander);
			if(!$hander || !$dbSel) 
				return false; 
		}		
		$this->hander = $hander; 
		return true; 
    } 

    /**
     * 关闭Session 
     * @access public 
     */
   public function close() {
	   
	   $check = false;
	   $this->gc($this->lifeTime);
	   $db_type = $this->db_type;
       if(is_array($this->hander)){
		   
           if($db_type === 'mysqli'){
				
				$check = $this->hander[0]->close() && $this->hander[1]->close();
		   }else{
			   
			   $check = mysql_close($this->hander[0]) && mysql_close($this->hander[1]);
		   }
           
       }else{
		   
		   if($db_type === 'mysqli'){
				
				$check = $this->hander->close();
		   }else{
			   
			   $check = mysql_close($this->hander);
		   }
	   }
      
       return $check; 
   } 

    /**
     * 读取Session 
     * @access public 
     * @param string $sessID 
     */
   public function read($sessID) { 
	   
	   $data_string = '';
	   $row = array('data'=>'');
	   $db_type = $this->db_type;   
       $hander = is_array($this->hander)?$this->hander[1]:$this->hander;
	   $sql = "SELECT session_data AS data FROM ".$this->sessionTable." WHERE session_id = '$sessID'   AND session_expire >".time();
	   
	   if($db_type === 'mysqli'){
			
			$res = $hander->query($sql);
			
			if($res){
				
				if(method_exists($res,'fetch_assoc')){
					
					$row = $res->fetch_assoc();
					
				}else{
					$row = $res->fetch_array(MYSQLI_ASSOC);					
				}				
								
			}
	   }else{
		   
		   $res = mysql_query($sql,$hander); 
		   if($res) {
			   
			   $row = mysql_fetch_assoc($res);			   
		   }
	   }
       $data_string = isset($row['data']) ? $row['data'] : '';
       return $data_string; 
   } 

    /**
     * 写入Session 
     * @access public 
     * @param string $sessID 
     * @param String $sessData  
     */
   public function write($sessID,$sessData) { 
		
	   $check = false;
	   $db_type = $this->db_type;
       $hander = is_array($this->hander)?$this->hander[0]:$this->hander;
       $expire = time() + $this->lifeTime; 
	   $sql = "REPLACE INTO  ".$this->sessionTable." (  session_id, session_expire, session_data)  VALUES( '$sessID', '$expire',  '$sessData')";
       if($db_type === 'mysqli'){
			
			$hander->query($sql);
			$affected_rows = $hander->affected_rows;
	   }else{
		   
		   mysql_query($sql,$hander); 
		   $affected_rows = mysql_affected_rows($hander);
	   }
       $check = $affected_rows ? true : false;
	   
       return $check; 
   } 

    /**
     * 删除Session 
     * @access public 
     * @param string $sessID 
     */
   public function destroy($sessID) { 
	   
	   $check = false;
	   $db_type = $this->db_type;
       $hander = is_array($this->hander)?$this->hander[0]:$this->hander;
	   $sql = "DELETE FROM ".$this->sessionTable." WHERE session_id = '$sessID'";
	   if($db_type === 'mysqli'){
			
			$hander->query($sql);
			$affected_rows = $hander->affected_rows;
	   }else{
		   
		   mysql_query($sql,$hander); 
		   $affected_rows = mysql_affected_rows($hander);
	   }
       $check = $affected_rows ? true : false;
       
       return $check; 
   } 

    /**
     * Session 垃圾回收
     * @access public 
     * @param string $sessMaxLifeTime 
     */
   public function gc($sessMaxLifeTime) { 
		
	   $db_type = $this->db_type;
       $hander = is_array($this->hander)?$this->hander[0]:$this->hander;
	   $sql = "DELETE FROM ".$this->sessionTable." WHERE session_expire < ".time();
	   
	   if($db_type === 'mysqli'){
			
			$hander->query($sql);
			$affected_rows = $hander->affected_rows;
	   }else{
		   
		   mysql_query($sql,$hander); 
		   $affected_rows = mysql_affected_rows($hander);
	   }
       
       return $affected_rows; 
   } 

}