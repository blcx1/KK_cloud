//一般提示
var v_login = "登錄";
var v_resend = "重發發送";
var v_basics = "基礎知識";
var v_already_send = "已發送";
var v_login_account = "登錄帳號";
var v_register_account = "註冊賬號";
var v_change_success = "修改成功";
var v_delete_account = "刪除賬號";
var v_verify_code = "驗　證　碼";
var v_secret_set = "密保設置";

//錯誤信息
var v_e_not_login = "未登錄";
var v_e_send_failed = "發送失敗";
var v_e_change_failed = "修改失敗";
var v_e_input_email = "郵箱地址格式輸入錯誤！";
var v_e_verify_code = "驗證碼輸入有誤";
var v_e_input_password = "密碼輸入有誤";
var v_e_input_username = "用戶名輸入有誤";
var v_e_password_not_same = "兩次密碼不一致！";
var v_e_input_verify_code = "驗證碼輸入錯誤!";
var v_e_input_phone = "電話號碼格式輸入錯誤！";

//提示信息
var v_operating_fast = "您的操作頻率過快，請稍後再試。";
var v_username_tips = "用戶名由中文、字母、數字、\-\_的組合，4-20個字符";
var v_password_tips = "密碼使用字母、數字和符號兩種及以上的組合，6-18個字符";
var v_e_inout_phone = "手機號錯誤";
var v_girl = "女";
var v_boy = "男";
var v_secrecy = "保密";
var v_determine = "確定";
var v_information = "資訊";
var v_e_portrait_file_type = "圖片格式只支持jpg，png，gif。";
var v_e_portrait_file_max_size = "圖片大小不超過2M。";
var v_moverecycle = '移入回收站';
var v_removecompletely = '徹底删除 ';
var v_is_recycle = '確定要移入回收站?';
var v_is_delete   = '確定要删除？';
var v_move_recycle = '請選擇要移入回收站的數據。';
var v_sel_delete = '請選擇要删除的數據。';
var v_sel_messages = '删除選中的簡訊？';
var v_call_delete = '確定要删除這條記錄嗎？';
var v_succ_delete = '删除成功。';
var v_succ_restore= '恢復數據成功。 ';
var v_succ_recycle = '移入回收站成功。';
var v_ope_failed = '操作失敗 ';
var v_sel_data = '請選擇要恢復的數據。';
var v_search_contents = '請輸入蒐索的內容';
var v_tip_delete = '清空所有數據，清空後數據不可恢復，確定要清空嗎？';
var v_no_data = '沒有相應的數據';
var v_remove_completely = '徹底删除 ';
var v_cancel = '取消';
var v_device_name = '設備名';
var v_clear_success = "清除成功";
var v_longitude = "經度";
var v_latitude = "緯度";
var v_address = "地址";
var v_success = "成功";
var v_unsuccess = "未成功";
var v_target = "目標";
var v_status = "狀態";
var v_add_time = "添加時間";
var v_exec_time ="執行時間";
var v_loading = "正在加載中......";
var v_operation_success = "操作成功";
var v_cmd_faild = "操作失敗，設備可能離線中，請確認，謝謝.";
var v_operation_faild = "操作失敗";
var v_delete = "删除";
var v_effective = "有效";
var v_invalid = "無效";
var v_full_name = "姓名";
var v_phone = "電話";
var v_email = "郵箱";
var v_company = "公司";
var v_prefix = "首碼";
var v_middle_name = "中間名";
var v_family_name = "姓氏";
var v_given_name = "名字";
var v_suffix = "名稱尾碼";
var v_nick_name = "昵稱";
var v_position = "職位";
var v_im = "即時通訊";
var v_date = "日期";
var v_more_select = "添加更多項";
var v_add_portrait = "添加頭像";
var v_delete_portrait = "删除頭像";
var v_invalid_data = "非法數據";
var v_add_success = "添加成功";

//聯繫人相關
var v_mime_type_object = {"base":"基礎信息","phone":"號碼","email":"郵箱","im":"聊天工具","postal":"郵政地址","photo":"頭像",
                          "organization":"公司","nickname":"暱稱","group":"分組","note":"備註","website":"站點",
						  "relation":"關係","event":"事件","sip":"互聯網通話"};
//電話號碼類型
var v_mime_type_phone_array = new Array();
v_mime_type_phone_array[0] = "自定義";
v_mime_type_phone_array[1] = "家庭";
v_mime_type_phone_array[2] = "手機";
v_mime_type_phone_array[3] = "工作";
v_mime_type_phone_array[4] = "工作傳真";
v_mime_type_phone_array[5] = "家庭傳真";
v_mime_type_phone_array[6] = "尋呼";
v_mime_type_phone_array[7] = "其他";
v_mime_type_phone_array[8] = "回撥號";
v_mime_type_phone_array[9] = "車號";
v_mime_type_phone_array[10] = "公司主機";
v_mime_type_phone_array[11] = "ISDN";
v_mime_type_phone_array[12] = "主號";
v_mime_type_phone_array[13] = "其他傳真";
v_mime_type_phone_array[14] = "無線";
v_mime_type_phone_array[15] = "電傳";
v_mime_type_phone_array[16] = "電傳打印機等";
v_mime_type_phone_array[17] = "工作手機";
v_mime_type_phone_array[18] = "工作尋呼";
v_mime_type_phone_array[19] = "分機";
v_mime_type_phone_array[20] = "彩信";

//郵件類型
var v_mime_type_mail_array = new Array();
v_mime_type_mail_array[0] = "自定義";
v_mime_type_mail_array[1] = "私人";
v_mime_type_mail_array[2] = "工作";
v_mime_type_mail_array[3] = "其他";
v_mime_type_mail_array[4] = "手機";

//聊天工具類型
var v_mime_type_im_array = new Array();
v_mime_type_im_array[0] = "自定義";
v_mime_type_im_array[1] = "私人";
v_mime_type_im_array[2] = "工作";
v_mime_type_im_array[3] = "其他";

//聊天協議類型
var v_type_im_protocol_array = new Array();
v_type_im_protocol_array[-1] = "自定義";
v_type_im_protocol_array[0] = "AIM";
v_type_im_protocol_array[1] = "MSN";
v_type_im_protocol_array[2] = "YAHOO";
v_type_im_protocol_array[3] = "SKYPE";
v_type_im_protocol_array[4] = "QQ";
v_type_im_protocol_array[5] = "GOOGLE TALK";
v_type_im_protocol_array[6] = "ICQ";
v_type_im_protocol_array[7] = "JABBER";
v_type_im_protocol_array[8] = "NETMEETING";

//郵政地址類型
var v_mime_type_postal_array = new Array();
v_mime_type_postal_array[1] = "家庭";
v_mime_type_postal_array[2] = "工作";
v_mime_type_postal_array[3] = "其他";

//組織/團體地址
var v_mime_type_organization_array = new Array();
v_mime_type_organization_array[0] = "自定義";
v_mime_type_organization_array[1] = "工作";
v_mime_type_organization_array[2] = "其他";

//暱稱類型
var v_mime_type_nickname_array = new Array();
v_mime_type_nickname_array[0] = "自定義";
v_mime_type_nickname_array[1] = "默認";
v_mime_type_nickname_array[2] = "其他";
v_mime_type_nickname_array[3] = "(女子)婚前姓";
v_mime_type_nickname_array[4] = "簡稱";

//網站類型
var v_mime_type_website_array = new Array();
v_mime_type_website_array[0] = "自定義";
v_mime_type_website_array[1] = "主頁";
v_mime_type_website_array[2] = "博客";
v_mime_type_website_array[3] = "簡歷";
v_mime_type_website_array[4] = "家庭主頁";
v_mime_type_website_array[5] = "工作主頁";
v_mime_type_website_array[6] = "FTP地址";
v_mime_type_website_array[7] = "其他";

//關係類型
var v_mime_type_relation_array = new Array();
v_mime_type_relation_array[0] = "自定義";
v_mime_type_relation_array[1] = "助理";
v_mime_type_relation_array[2] = "兄弟";
v_mime_type_relation_array[3] = "兒子";
v_mime_type_relation_array[4] = "合作夥伴";
v_mime_type_relation_array[5] = "父親";
v_mime_type_relation_array[6] = "朋友";
v_mime_type_relation_array[7] = "經理";
v_mime_type_relation_array[8] = "母親";
v_mime_type_relation_array[9] = "前輩";
v_mime_type_relation_array[10] = "夥伴";
v_mime_type_relation_array[11] = "推薦人";
v_mime_type_relation_array[12] = "親屬";
v_mime_type_relation_array[13] = "姐妹";
v_mime_type_relation_array[14] = "配偶";

//事件類型
var v_mime_type_event_array = new Array();
v_mime_type_event_array[0] = "自定義";
v_mime_type_event_array[1] = "週年紀念日";
v_mime_type_event_array[2] = "其他";
v_mime_type_event_array[3] = "生日";

//sip類型
var v_mime_type_sip_array = new Array();
v_mime_type_sip_array[0] = "自定義";
v_mime_type_sip_array[1] = "家庭";
v_mime_type_sip_array[2] = "工作";
v_mime_type_sip_array[3] = "其他";

var v_down_album = "下載相册";
var v_recy_album = "還原相册";
var v_delete_album = "删除相册";
var v_choice_tips = "最多只能選擇10張圖片！";
var v_move_tips = "確定移入回收站？";
var v_restore_tips = "確定還原數據？";
var v_delete_tips = "確定删除相册（不可恢復）？";
var v_download_success = "下載成功";
var v_download_failed = "下載失敗";
var v_choice = "選擇";
var v_down_tips = "確定下載圖片？";
var v_Photos = "張";

var v_Logout = '退出';
var v_No_data = '沒有數據';