<?php
/**
 *  为xunsearch服务添加数据
 */
namespace Home\Controller;
use Think\Controller;
require_once 'ThinkPHP/Library/xunsearch/php/lib/XS.php';
class XSController extends Controller {

    public function addContactsBaseInfo(){
        $xs=new \XS('contacts_base_info');
        $contacts=new \Home\Model\ContactsBaseInfoModel('*');
        $size=10000;
        $count=$contacts->count();
        $page_tatal=ceil($count/$size);
        $xs->index->clean();
        $doc=new \XSDocument;
        for($i=0;$i<$page_tatal;$i++){
            $page=$i*$size;
            $contactsInfo=$contacts->field('id,first_spell,display_name,user_id,given_name,family_name,prefix,suffix,middle_name,is_delete')->limit("$page,$size")->select();
            foreach($contactsInfo as $val){
                $doc->setFields($val);
                $xs->index->add($doc);
            }
        }      
        $xs->index->flushIndex();
    }
    
    public function search(){
            
        $xs=new \XS('contacts_base_info');
        $page_no=1;
        $page_size=10;
        $page_no=($page_no-1)*$page_size;
        $docs=$xs->search->setLimit($page_size,$page_no)->search('user_id:10131 is_delete:0 a');
        if($docs){
            foreach($docs as $key=>$val){
                $data[$key]['id']=$val->id;
                $data[$key]['user_id']=$val->user_id;
                $data[$key]['display_name']=$val->display_name;
                $data[$key]['first_spell']=$val->first_spell;
                $data[$key]['given_name']=$val->given_name;
                $data[$key]['family_name']=$val->family_name;
                $data[$key]['is_delete']=$val->is_delete;
            }
            echo '<pre>';print_r($data);echo '</pre>';
        }
    }
}
