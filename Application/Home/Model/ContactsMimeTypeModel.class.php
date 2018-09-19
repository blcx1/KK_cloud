<?php

namespace Home\Model;
use Think\Model;

class ContactsMimeTypeModel extends \Home\Model\DefaultModel {
	
	public function __construct($cache_object = null,$name='',$tablePrefix='',$connection=''){
		
		$db_prefix = C('DB_PREFIX');
		$this->dbName = 'db_contacts';
		$this->tableName = $db_prefix.'mimetype';
		$this->trueTableName = $this->tableName;
		$this->pk = 'id';
		parent::__construct($cache_object,$name,$tablePrefix,$connection);
	}
	
	/*
	 * 释放
	*/
	public function __destruct(){
	
		parent::__destruct();
	}
	
	
}
?>