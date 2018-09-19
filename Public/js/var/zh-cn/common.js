//一般提示
var v_login = "登录";
var v_resend = "重发发送";
var v_basics = "基础知识";
var v_already_send = "已发送";
var v_login_account = "登录帐号";
var v_register_account = "注册账号";
var v_change_success = "修改成功";
var v_delete_account = "删除账号";
var v_verify_code = "验　证　码";
var v_secret_set = "密保设置";

//错误信息
var v_e_not_login = "未登录";
var v_e_send_failed = "发送失败";
var v_e_change_failed = "修改失败";
var v_e_input_email = "邮箱地址格式输入错误！";
var v_e_verify_code = "验证码输入有误";
var v_e_input_password = "密码输入有误";
var v_e_input_username = "用户名输入有误";
var v_e_password_not_same = "两次密码不一致！";
var v_e_input_verify_code = "验证码输入错误!";
var v_e_input_phone = "电话号码格式输入错误！";

//提示信息
var v_operating_fast = "您的操作频率过快，请稍后再试。";
var v_username_tips = "用户名由中文、字母、数字、\-\_的组合，4-20个字符";
var v_password_tips = "密码使用字母、数字和符号两种及以上的组合，6-18个字符";
var v_e_inout_phone = "手机号错误";//Cell phone number error
var v_girl = "女";
var v_boy = "男";
var v_secrecy = "保密";
var v_determine = "确定";
var v_information = "信息";
var v_e_portrait_file_type = "图片格式只支持jpg,png,gif。";
var v_e_portrait_file_max_size = "图片大小不超过2M。";
var v_moverecycle = '移入回收站'; 
var v_removecompletely = '彻底删除';
var v_is_recycle = '确定要移入回收站？';
var v_is_delete   = '删除后不可恢复，确定要删除？';
var v_move_recycle = '请选择要移入回收站的数据。';
var v_sel_delete = '请选择要删除的数据。';//删除后数据不可恢复,确定要删除吗？
var v_sel_messages = '删除选中的记录?';
var v_call_delete = '确定要删除这条记录吗？';
var v_succ_delete = '删除成功。';
var v_succ_restore= '恢复数据成功。';
var v_succ_recycle = '移入回收站成功。';
var v_ope_failed = '操作失败';
var v_sel_data = '请选择要恢复的数据。';
var v_search_contents = '请输入搜索的内容。';
var v_tip_delete = '清空所有数据，清空后数据不可恢复，确定要清空吗？';
var v_no_data = '没有相应的数据';
var v_remove_completely = '彻底删除';//取消
var v_cancel = '取消';
var v_device_name = "设备名";
var v_clear_success = "清除成功";
var v_longitude = "经度";
var v_latitude = "纬度";
var v_address = "地址";
var v_success = "成功";
var v_unsuccess = "未成功";
var v_target = "目标";
var v_status = "状态";
var v_add_time = "添加时间";
var v_exec_time = "执行时间";
var v_loading = "正在加载中......";
var v_operation_success = "操作成功";
var v_cmd_faild = "操作失败，设备可能离线中，请确认，谢谢.";
var v_operation_faild = "操作失败";
var v_delete = "删除";
var v_effective = "有效";
var v_invalid = "无效";
var v_full_name = "姓名";
var v_phone = "电话";
var v_email = "邮箱";
var v_company = "公司";
var v_prefix = "前缀";
var v_middle_name = "中间名";
var v_family_name = "姓氏";
var v_given_name = "名字";
var v_suffix = "名称后缀";
var v_nick_name = "昵称";
var v_position = "职位";
var v_im = "即时通讯";
var v_date = "日期";
var v_more_select = "添加更多项";
var v_add_portrait = "添加头像";
var v_delete_portrait = "删除头像";
var v_invalid_data = "非法数据";
var v_add_success = "添加成功";
var v_e_empty_contact_name = "联系人姓名不能为空!";

//联系人相关 
var v_mime_type_object = {"base":"基础信息","phone":"号码","email":"邮箱","im":"聊天工具","postal":"邮政地址","photo":"头像",
                          "organization":"公司","nickname":"昵称","group":"群组","note":"备注","website":"站点",
						  "relation":"关系","event":"事件","sip":"互联网通话"};
//电话号码类型						  
var v_mime_type_phone_array = new Array();
v_mime_type_phone_array[0] = "自定义";
v_mime_type_phone_array[1] = "家庭";
v_mime_type_phone_array[2] = "手机";
v_mime_type_phone_array[3] = "工作";
v_mime_type_phone_array[4] = "工作传真";
v_mime_type_phone_array[5] = "家庭传真";
v_mime_type_phone_array[6] = "寻呼";
v_mime_type_phone_array[7] = "其他";
v_mime_type_phone_array[8] = "回拨号";
v_mime_type_phone_array[9] = "车号";
v_mime_type_phone_array[10] = "公司主机";
v_mime_type_phone_array[11] = "ISDN";
v_mime_type_phone_array[12] = "主号";
v_mime_type_phone_array[13] = "其他传真";
v_mime_type_phone_array[14] = "无线";
v_mime_type_phone_array[15] = "电传";
v_mime_type_phone_array[16] = "电传打印机等";
v_mime_type_phone_array[17] = "工作手机";
v_mime_type_phone_array[18] = "工作寻呼";
v_mime_type_phone_array[19] = "分机";
v_mime_type_phone_array[20] = "彩信";

//邮件类型
var v_mime_type_mail_array = new Array();
v_mime_type_mail_array[0] = "自定义";
v_mime_type_mail_array[1] = "私人";
v_mime_type_mail_array[2] = "工作";
v_mime_type_mail_array[3] = "其他";
v_mime_type_mail_array[4] = "手机";

//聊天工具类型
var v_mime_type_im_array = new Array();
v_mime_type_im_array[0] = "自定义";
v_mime_type_im_array[1] = "私人";
v_mime_type_im_array[2] = "工作";
v_mime_type_im_array[3] = "其他";

//聊天协议类型
var v_type_im_protocol_array = new Array();
v_type_im_protocol_array[-1] = "自定义";
v_type_im_protocol_array[0] = "AIM";
v_type_im_protocol_array[1] = "MSN";
v_type_im_protocol_array[2] = "YAHOO";
v_type_im_protocol_array[3] = "SKYPE";
v_type_im_protocol_array[4] = "QQ";
v_type_im_protocol_array[5] = "GOOGLE TALK";
v_type_im_protocol_array[6] = "ICQ";
v_type_im_protocol_array[7] = "JABBER";
v_type_im_protocol_array[8] = "NETMEETING";

//邮政地址类型
var v_mime_type_postal_array = new Array();
v_mime_type_postal_array[1] = "家庭";
v_mime_type_postal_array[2] = "工作";
v_mime_type_postal_array[3] = "其他";

//组织/团体地址
var v_mime_type_organization_array = new Array();
v_mime_type_organization_array[0] = "自定义";
v_mime_type_organization_array[1] = "工作";
v_mime_type_organization_array[2] = "其他";

//昵称类型
var v_mime_type_nickname_array = new Array();
v_mime_type_nickname_array[0] = "自定义";
v_mime_type_nickname_array[1] = "默认";
v_mime_type_nickname_array[2] = "其他";
v_mime_type_nickname_array[3] = "(女子)婚前姓";
v_mime_type_nickname_array[4] = "简称";

//网站类型
var v_mime_type_website_array = new Array();
v_mime_type_website_array[0] = "自定义";
v_mime_type_website_array[1] = "主页";
v_mime_type_website_array[2] = "博客";
v_mime_type_website_array[3] = "简历";
v_mime_type_website_array[4] = "家庭主页";
v_mime_type_website_array[5] = "工作主页";
v_mime_type_website_array[6] = "FTP地址";
v_mime_type_website_array[7] = "其他";

//关系类型						  
var v_mime_type_relation_array = new Array();
v_mime_type_relation_array[0] = "自定义";
v_mime_type_relation_array[1] = "助理";
v_mime_type_relation_array[2] = "兄弟";
v_mime_type_relation_array[3] = "儿子";
v_mime_type_relation_array[4] = "合作伙伴";
v_mime_type_relation_array[5] = "父亲";
v_mime_type_relation_array[6] = "朋友";
v_mime_type_relation_array[7] = "经理";
v_mime_type_relation_array[8] = "母亲";
v_mime_type_relation_array[9] = "前辈";
v_mime_type_relation_array[10] = "伙伴";
v_mime_type_relation_array[11] = "推荐人";
v_mime_type_relation_array[12] = "亲属";
v_mime_type_relation_array[13] = "姐妹";
v_mime_type_relation_array[14] = "配偶";

//事件类型
var v_mime_type_event_array = new Array();
v_mime_type_event_array[0] = "自定义";
v_mime_type_event_array[1] = "周年纪念日";
v_mime_type_event_array[2] = "其他";
v_mime_type_event_array[3] = "生日";

//sip类型
var v_mime_type_sip_array = new Array();
v_mime_type_sip_array[0] = "自定义";
v_mime_type_sip_array[1] = "家庭";
v_mime_type_sip_array[2] = "工作";
v_mime_type_sip_array[3] = "其他";


var v_down_album = "下载相册";//Download album
var v_recy_album = "还原相册";//Photo album
var v_delete_album = "删除相册";//Delete album
var v_choice_tips = "最多只能选择10张图片！";//Up to 10 pictures can be selected
var v_move_tips = "确定移入回收站？";//Sure to move to the recycle bin?
var v_restore_tips = "确定还原数据？";//Determine restore data?
var v_delete_tips = "确定删除相册（不可恢复）？";//Sure delete album (not restored)?
var v_download_success = "下载成功";//Download success
var v_download_failed = "下载失败";//Download failed
var v_choice = "选择";//Choice确定下载图片
var v_down_tips = "确定下载图片?";//Determine download pictures
var v_Photos = "张";

var v_Logout = '退出';//Logout
var v_No_data = '没有数据';//Logout