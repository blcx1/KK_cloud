<?php

namespace Home\Model;
use Think\Model;

class UserNameMaxRandModel extends Model {
	
	//初始化
	public function __construct($name='',$tablePrefix='',$connection='') {
		
		$this->dbName = 'db_user';
		$this->tableName = C('DB_PREFIX').'user_name_max_rand';
		$this->trueTableName = $this->tableName;
		parent::__construct($name,$tablePrefix,$connection);	
	}
}
?>