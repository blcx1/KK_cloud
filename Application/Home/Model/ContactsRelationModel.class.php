<?php

namespace Home\Model;
use Think\Model;

class ContactsRelationModel extends \Home\Model\ContactsBaseModel {
	
	protected $check_md5_str = true;
	protected $mimetype = 'relation';
	protected $mimetype_array = array('relation','identify');
	
	public function __construct($cache_object = null,$name='',$tablePrefix='',$connection=''){
		
		$db_prefix = C('DB_PREFIX');
		$this->dbName = 'db_contacts';
		$this->tableName = $db_prefix.'contacts_relation';
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
		$conversion_relation['data_1'] = 'name';
		$conversion_relation['data_2'] = 'type_name';
		$conversion_relation['data_3'] = 'label';
		$conversion_relation['md5_str'] = 'md5_str';
		return $conversion_relation;
	}
}
?>