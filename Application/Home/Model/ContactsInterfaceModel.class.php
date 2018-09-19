<?php
namespace Home\Model;

interface ContactsInterfaceModel{
	
	public function contacts_list2current_list_relation();//联系列表与当前列表对应关系
	
	public function contacts_list_conversion_current_list($list_array,$check_same = false);//联系列表转化当前列表
	
    public function current_list_conversion_contacts_list($list_array,$check_same = false);//当前列表转化联系列表	
}
?>