<?php
/**
 * Created by PhpStorm.
 * User: inmyfree
 * Date: 2016/6/20
 * Time: 17:23
 */

namespace Home\Model;
class PushRecordModel extends  \Home\Model\DefaultModel{

    /**
     * 初始化
     * @param string $name
     * @param string $tablePrefix
     * @param string $connection
     */
    public function __construct($name='',$tablePrefix='',$connection='') {
		
		$db_prefix = C('DB_PREFIX');
        $this->dbName = 'db_findmyphone';
        $this->tableName = $db_prefix.'push_record';
        $this->trueTableName = $this->tableName;
        $this->pk = 'id';
        parent::__construct($name,$tablePrefix,$connection);
    }
}