// General prompt
var v_login = "Login";
var v_resend = "Resend";
var v_basics = "Basics";
var v_already_send = "Already Send";
var v_login_account = "Login Account";
var v_register_account = "Register Account";
var v_change_success = "Modified successfully";
var v_delete_account = "Delete Account";
var v_verify_code = "Verification Code";
var v_secret_set = "Secret security settings";

// Error message
var v_e_not_login = "Not Signed";
var v_e_send_failed = "Send failed";
var v_e_change_failed = "Modify failed";
var v_e_input_email = "The Email Address is not in the correct format!";
var v_e_verify_code = "Verification code entered incorrectly";
var v_e_input_password = "Password Entered incorrectly";
var v_e_input_username = "User Name Entered incorrectly";
var v_e_password_not_same = "Two Passwords Do Not Match!";
var v_e_input_verify_code = "Verification Code Invalid!";
var v_e_input_phone = "Telephone number format input error!";

// prompt information
var v_operating_fast = "Your operating frequency too fast, please try again later.";
var v_username_tips = "User name consists of 4-20 characters, letters, numbers, \ - \ _";
var v_password_tips = "Password uses letters, numbers and symbols of two or more combinations, 6-18 characters";
var v_e_inout_phone = "Cell phone number error";//手机号错误
var v_girl = "Girl";//女
var v_boy = "Boy";
var v_secrecy = "Secrecy";
var v_determine = "Determine";
var v_information = "information";
var v_e_portrait_file_type = "Picture format only supports PNG,JPG,gif.";
var v_e_portrait_file_max_size = "The picture size is less than 2M.";
var v_moverecycle = 'Move to recycle bin'; //移入回收站
var v_removecompletely = 'Remove completely';//彻底删除
var v_is_recycle = 'Are you sure you want to move to the recycle bin?';//确定要移入回收站？
var v_is_delete   = 'Are you sure you want to delete?';//确定要删除？
var v_move_recycle = 'Please select the data you want to move to the recycle bin.';//请选择要移入回收站的数据。
var v_sel_delete = 'Please select the data you want to delete.';//请选择要删除的数据。
var v_sel_messages = 'Delete selected messages?';//删除选中的短信?
var v_call_delete = 'Are you sure you want to delete this log?';//确定要删除这条通话记录吗？
var v_succ_delete = 'Delete success';//删除成功。
var v_succ_restore= 'Restore data success';//恢复数据成功。
var v_succ_recycle = 'Successful move to recycle bin.';//移入回收站成功。
var v_ope_failed = 'operation failed';//操作失败
var v_sel_data = 'Please select the data you want to restore';//请选择要恢复的数据。
var v_search_contents = 'Please enter the contents of the search.';//请输入搜索的内容。
var v_tip_delete = 'Clear all data, empty the data can not be restored, to be sure to empty it?';//清空所有数据,清空后数据不可恢复，确定要清空吗？
var v_no_data = 'No corresponding data';//没有相应的数据
var v_remove_completely = 'Remove completely';//彻底删除
var v_cancel = 'Cancel';//取消
var v_device_name = 'device name';
var v_clear_success = "Clear success";
var v_longitude = "longitude";
var v_latitude = "latitude";
var v_address = "Address";
var v_success = "Successful";
var v_unsuccess = "Unsuccessful";
var v_target = "Target";
var v_status = "Status";
var v_add_time = "add time";
var v_exec_time = "execution time";
var v_loading = "Loading in ......";
var v_operation_success = "Successful operation";
var v_cmd_faild = "The operation failed, the device may be offline, please confirm, thank you.";
var v_operation_faild = "operation failed";//操作失败
var v_delete = "delete";//删除
var v_effective = "effective";//有效
var v_invalid = "invalid";//无效
var v_full_name = "Full Name";
var v_phone = "Phone";
var v_email = "Email";
var v_company = "Company";
var v_prefix = "Prefix";
var v_middle_name = "Middle Name";
var v_family_name = "Family Name";
var v_given_name = "Given Name";
var v_suffix = "Suffix";
var v_nick_name = "Nick Name";
var v_position = "Position";
var v_im = "Im";
var v_date = "Date";
var v_more_select = "Add More Items";
var v_add_portrait = "Add Portrait";
var v_delete_portrait = "Delete Portrait";
var v_invalid_data = "Invalid Data";
var v_add_success = "Add successfully";

// Contact-related
var v_mime_type_object = {"base": "basics", "phone": "phone", "email": "email", "im": "chat", "postal": "post", "photo": "photo",
                          "Organization": "company", "nickname": "nickname", "group": "group", "note": "note", "website": "site",
						  "Relation": "relationship", "event": "event", "sip": "SIP"};
// Phone number type
var v_mime_type_phone_array = new Array();
v_mime_type_phone_array [0] = "Custom";
v_mime_type_phone_array [1] = "Home";
v_mime_type_phone_array [2] = "Phone";
v_mime_type_phone_array [3] = "Work";
v_mime_type_phone_array [4] = "Work Fax";
v_mime_type_phone_array [5] = "Home Fax";
v_mime_type_phone_array [6] = "Paging";
v_mime_type_phone_array [7] = "Other";
v_mime_type_phone_array [8] = "Callback";
v_mime_type_phone_array [9] = "vehicle number";
v_mime_type_phone_array [10] = "Corporate Host";
v_mime_type_phone_array [11] = "ISDN";
v_mime_type_phone_array [12] = "main number";
v_mime_type_phone_array [13] = "Other Faxes";
v_mime_type_phone_array [14] = "Wireless";
v_mime_type_phone_array [15] = "telex";
v_mime_type_phone_array [16] = "teleprinter, etc.";
v_mime_type_phone_array [17] = "Work Mobile";
v_mime_type_phone_array [18] = "Work Paging";
v_mime_type_phone_array [19] = "Extensions";
v_mime_type_phone_array [20] = "MMS";

// The message type
var v_mime_type_mail_array = new Array();
v_mime_type_mail_array [0] = "Custom";
v_mime_type_mail_array [1] = "Private";
v_mime_type_mail_array [2] = "Work";
v_mime_type_mail_array [3] = "Other";
v_mime_type_mail_array [4] = "Phone";

// Chat tool type
var v_mime_type_im_array = new Array();
v_mime_type_im_array [0] = "Custom";
v_mime_type_im_array [1] = "Private";
v_mime_type_im_array [2] = "Work";
v_mime_type_im_array [3] = "Other";

// Chat protocol type
var v_type_im_protocol_array = new Array();
v_type_im_protocol_array [-1] = "Custom";
v_type_im_protocol_array [0] = "AIM";
v_type_im_protocol_array [1] = "MSN";
v_type_im_protocol_array [2] = "YAHOO";
v_type_im_protocol_array [3] = "SKYPE";
v_type_im_protocol_array [4] = "QQ";
v_type_im_protocol_array [5] = "GOOGLE TALK";
v_type_im_protocol_array [6] = "ICQ";
v_type_im_protocol_array [7] = "JABBER";
v_type_im_protocol_array [8] = "NETMEETING";

// Postal address type
var v_mime_type_postal_array = new Array();
v_mime_type_postal_array [1] = "Family";
v_mime_type_postal_array [2] = "Work";
v_mime_type_postal_array [3] = "Other";

// Organization / Community Address
var v_mime_type_organization_array = new Array();
v_mime_type_organization_array [0] = "Custom";
v_mime_type_organization_array [1] = "Work";
v_mime_type_organization_array [2] = "Other";

// Nickname type
var v_mime_type_nickname_array = new Array();
v_mime_type_nickname_array [0] = "Custom";
v_mime_type_nickname_array [1] = "default";
v_mime_type_nickname_array [2] = "Other";
v_mime_type_nickname_array [3] = "(woman) maiden name";
v_mime_type_nickname_array [4] = "Abbreviation";

// Site type
var v_mime_type_website_array = new Array();
v_mime_type_website_array [0] = "Custom";
v_mime_type_website_array [1] = "Home";
v_mime_type_website_array [2] = "Blog";
v_mime_type_website_array [3] = "Resume";
v_mime_type_website_array [4] = "Home Page";
v_mime_type_website_array [5] = "Work Home";
v_mime_type_website_array [6] = "FTP Address";
v_mime_type_website_array [7] = "Others";

// Relationship type
var v_mime_type_relation_array = new Array();
v_mime_type_relation_array [0] = "Custom";
v_mime_type_relation_array [1] = "Assistant";
v_mime_type_relation_array [2] = "Brother";
v_mime_type_relation_array [3] = "son";
v_mime_type_relation_array [4] = "Partner";
v_mime_type_relation_array [5] = "Father";
v_mime_type_relation_array [6] = "Friends";
v_mime_type_relation_array [7] = "Manager";
v_mime_type_relation_array [8] = "Mother";
v_mime_type_relation_array [9] = "predecessors";
v_mime_type_relation_array [10] = "Partner";
v_mime_type_relation_array [11] = "Referrer";
v_mime_type_relation_array [12] = "Relatives";
v_mime_type_relation_array [13] = "Sister";
v_mime_type_relation_array [14] = "spouse";

// The event type
var v_mime_type_event_array = new Array();
v_mime_type_event_array [0] = "Custom";
v_mime_type_event_array [1] = "Anniversary Day";
v_mime_type_event_array [2] = "Other";
v_mime_type_event_array [3] = "Birthday";

// sip type
var v_mime_type_sip_array = new Array();
v_mime_type_sip_array [0] = "Custom";
v_mime_type_sip_array [1] = "Family";
v_mime_type_sip_array [2] = "Work";
v_mime_type_sip_array [3] = "Other";

var v_down_album = "Download album";//下载相册
var v_recy_album = "Photo album";//还原相册
var v_delete_album = "Delete album";//删除相册
var v_choice_tips = "Up to 10 pictures can be selected";//最多只能选择10张图片！
var v_move_tips = "Sure to move to the recycle bin?";//确定移入回收站？
var v_restore_tips = "Determine restore data?";//确定还原数据？
var v_delete_tips = "Sure delete album (not restored)?";//确定删除相册（不可恢复）？
var v_download_success = "Download success";//下载成功
var v_download_failed = "Download failed";//下载失败
var v_choice = "Choice";//选择
var v_down_tips = "Determine download pictures?";//确定下载图片
var v_Photos = "Photos";//張

var v_Logout = 'Logout';//退出
var v_No_data = 'No Data';//没有数据