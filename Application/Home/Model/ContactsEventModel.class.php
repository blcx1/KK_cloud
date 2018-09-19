<?php

namespace Home\Model;
use Think\Model;

class ContactsEventModel extends \Home\Model\ContactsBaseModel {
	
	protected $check_md5_str = true;
	protected $mimetype = 'event';
	protected $mimetype_array = array('event');
	
	public function __construct($cache_object = null,$name='',$tablePrefix='',$connection=''){
		
		$db_prefix = C('DB_PREFIX');
		$this->dbName = 'db_contacts';
		$this->tableName = $db_prefix.'contacts_event';
		$this->trueTableName = $this->tableName;
		$this->pk = 'id';
		parent::__construct($cache_object,$name,$tablePrefix,$connection);
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \Home\Model\ContactsInterfaceModel::contacts_list2current_list_relation()
	 */
	public function contacts_list2current_list_relation(){
		
		$conversion_relation = array();
		$conversion_relation['server_other_id'] = 'id';
		$conversion_relation['server_id'] = 'base_id';
		$conversion_relation['data_1'] = 'start_date';
		$conversion_relation['data_2'] = 'type_name';
		$conversion_relation['data_3'] = 'label';
		$conversion_relation['md5_str'] = 'md5_str';
		return $conversion_relation;
	}
	
}
?>