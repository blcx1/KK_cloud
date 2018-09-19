<?php
/**
 *常量编码文件
 *@author kivenpc pcttcnc2007@126.com
 *@date 2015-11-13
 **/
/**
  *跟客户端交互的接口对应的编码常量
  *date 2015-05-04
  *autor:kiven pcttcnc2007@126.com
  */
  
/**
  *
  * 编号 0 -- 449 购物支付预留
  * 支付状态不用于api状态编码显示，及意义显示（即0 -- 5）
  *
  **/
  
// 支付状态 
define('PS_UNPAYED',0); // 未付款
define('PS_PAYED',1); // 已支付
define('PS_PAYING',2); //支付失败（付款中）
define('ORDER_COMFIRED',3); //订单支付确认
define('ORDER_CANCELED',4); //取消订单
define('ORDER_REFUNDED',5);//订单退款

//支付出错及其他支付对应值
define('ERROR_PAYMENT_NOT_EXISTS',100);//支付方式不存在,可能已经下线了
define('ERROR_PAYMENT_NOT_CONFIG',101);//支付方式未配置，或者配置不能使用
define('ERROR_PAYMENT_NOT_PAY_CODE',102);//未配置支付接口名称
define('ERROR_PAYMENT_NOT_FILE_EXISTS',103);//没有对应的支付程序，请建立支付文件程序再使用

//购买及支付报错
define('ERROR_PRODUCT_NOT_EXISTS',200);//产品不存在，或者已经下架
define('ERROR_GOLD_NOT_ENOUGH',201);//金币数量不足或者金额不足
define('ERROR_MULTI',202);//多列错误，具体参考其他列

//数据库及操作异常
define('ERROR_MYSQL_CONNECT',450);//数据库连接失败
define('ERROR_MYSQL_EXE_REPORT',451);//数据库报错

//非法访问，或者非法数据或者暴力破解
define('VERIFY_FAILED_ENOUGH',998);//验证错误过多次
define('INVALID_DATA',999);//非法数据
define('INVALID_OPERATE',1000);//非法操作

//第三方登陆接口 1001 --- 1015
define('LOGIN_API_QQ',1001);//腾讯qq登陆方式
define('LOGIN_API_WEIBO',1002);//新浪微博登陆方式
define('LOGIN_API_WEIXIN',1003);//微信登陆方式
define('LOGIN_API_UUID',1004);//用户设备uuid登陆
define('LOGIN_API_QQZONE',1005);//用户采用qq空间登陆
define('LOGIN_API_TENCENT',1006);//腾讯微博登录
define('LOGIN_CLIENT',1007);//客户端登录
define('LOGIN_WEB',1008);//网站登录

//注册类型 1016 --- 1020
define('REGISTER_CLIENT_TEL',1016);//客户端手机号码注册
define('REGISTER_CLIENT_MAIL',1017);//客户端email注册
define('REGISTER_WEB_TEL',1018);//网站手机号码注册
define('REGISTER_WEB_MAIL',1019);//网站email注册

//修改用户相关信息，关联类型值
define('CHANGE_USER_NAME',1021);//修改用户名
define('CHANGE_NICK_NAME',1022);//修改昵称
define('CHANGE_USER_EMAIL',1023);//修改邮箱地址
define('CHANGE_USER_TEL',1024);//修改手机号码
define('CHANGE_PASSWORD',1025);//修改密码

//登陆,注册状态
define('ERROR_NOT_LOGIN',1031);//未登陆
define('ERROR_EXISTS_LOGIN',1032);//已登录过
define('ERROR_LOGIN_FAILED',1033);//登录失败
define('ERROR_NOT_REGISTER',1034);//未注册
define('ERROR_REGISTER_FAILED',1035);//注册失败
define('LOGIN_SUCCESS',1036);//登录成功
define('REGISTER_SUCCESS',1037);//注册成功
define('LOGOUT_SUCCESS',1038);//退出成功，登出成功
define('LOGIN_EXPIRY',1039);//登陆已经过期
define('ERROR_THE_SAME_LOGIN',1040);//当前账号在另外个地方登录
define('ERROR_LOGIN_FAILED_ENOUGH',1041);//当前该ip异常请求次数过多，暂停该ip登录
define('ERROR_LOGOUT_FAILED',1042);//登出失败,退出失败

//用户名出错编码
define('ERROR_USER_EMPTY',1050);//用户名不能为空，请输入，谢谢
define('ERROR_USER_INVALID',1051);//用户名输入不合法字符
define('ERROR_USER_EXISTS',1052);//用户名已被注册
define('ERROR_USER_INVALID_LEN',1053);//用户名长度不能小于2位且不能大于60个字符，请核对后输入，谢谢
define('ERROR_USER_NOT_EXISTS',1054);//用户不存在

//邮箱地址出错编码
define('ERROR_EMAIL_EMPTY',1055);//邮箱地址不能为空
define('ERROR_EMAIL_INVALID',1056);//邮箱地址不合法，请核对后输入，谢谢
define('ERROR_EMAIL_EXISTS',1057);//该邮箱地址已被注册
define('ERROR_EMAIL_INVALID_LEN',1058);//邮箱地址长度不能超过60个字符
define('ERROR_EMAIL_VERIFY',1059);//邮箱已经验证，已绑定
define('ERROR_EMAIL_NOT_VERIFY',1060);//邮箱没有验证激活，请验证邮箱
define('ERROR_EMAIL_NOT_EXISTS',1061);//邮箱不存在

//手机出错编码
define('ERROR_TEL_EMPTY',1062);//手机不能为空，请输入，谢谢
define('ERROR_TEL_INVALID',1063);//手机号码不合法
define('ERROR_TEL_EXISTS',1064);//手机号码已存在
define('ERROR_TEL_INVALID_LEN',1065);//邮箱地址长度不能超过60个字符
define('ERROR_TEL_VERIFY',1066);//手机号码已经验证，已绑定
define('ERROR_TEL_NOT_VERIFY',1067);//手机号码没有验证绑定（不能用于重置密码）

//密码出错编码
define('ERROR_PASSWORD_EMPTY',1068);//密码不能为空，请输入，谢谢
define('ERROR_PASSWORD_INVALID',1069);//密码不合法
define('ERROR_PASSWORD_INVALID_LEN',1070);//密码长度在6到18位，字母区分大小写
define('ERROR_PASSWORD_NOT_SAME',1071);//密码输入不一样
define('ERROR_PASSWORD',1072);//原密码错误

//验证码出错编码
define('ERROR_CAPTCHA_EMPTY',1073);//验证码不能为空
define('ERROR_CAPTCHA_INVALID',1074);//验证码不合法，请核对后重新输入
define('ERROR_USER_PASSWORD_NOT_MATCH',1075);//用户名或密码错误，请核对后重新输入，谢谢

//昵称修改
define('ERROR_NICK_NAME_EMPTY',1076);//昵称不能为空
define('ERROR_NICK_NAME_INVALID',1077);//昵称不合法字符
define('ERROR_NICK_NAME_INVALID_LEN',1078);//昵称长度在2到60位

define('ERROR_DAY_INVALID',1079);//日期格式不合法

//验证类型
define('VERIFY_TYPE_EMAIL',1080);//邮箱验证
define('VERIFY_TYPE_TEL',1081);//手机验证
define('ERROR_VERIFY_TYPE_INVALID',1082);//验证类型不合法
define('ERROR_NOT_MATCH_USER',1083);//没有匹配的用户账号
define('VERIFY_TYPE_RESET',1084);//重置验证类型

//邮件发送相关
define('ERROR_TEMPLATE_NOT_EXISTS',1090);//邮件/短信模板不存在
define('ERROR_SEND_EMAIL_FAILED',1091);//邮件发送失败
define('ERROR_SEND_TEL_FAILED',1092);//手机短信发送失败

//其他出错编码
define('ERROR_UNKNOWN',1100);//未知错误
define('ERROR_BLACKLIST',1101);//该用户被管理员拉入黑名单，请联系管理员
define('ERROR_VERIFY_TYPE_NOT_EXISTS',1102);//验证的类型不存在

//操作编码
define('ERROR_OPERATE_FAILED',1200);//操作失败
define('ERROR_ADD_FAILED',1201);//添加失败
define('ERROR_UPDATE_FAILED',1202);//更新失败
define('ERROR_DEL_FAILED',1203);//删除失败
define('ERROR_EDIT_FAILED',1204);//编辑失败
define('ERROR_CONSERVE_FAILED',1205);//保存失败
define('ERROR_INSERT_FAILED',1206);//插入失败
define('OPERATE_SUCCESS',1207);//操作成功
define('ADD_SUCCESS',1208);//添加成功
define('UPDATE_SUCCESS',1209);//更新成功
define('DEL_SUCCESS',1210);//删除成功
define('EDIT_SUCCESS',1211);//编辑成功
define('CONSERVE_SUCCESS',1212);//保存成功
define('INSERT_SUCCESS',1213);//插入成功

//
define('IS_PERFECT',1214);//需要完善资料
define('CHECK_LOGIN_CAPTCHA',1215);//登录需要验证码
define('CHECK_REGISTER_CAPTCHA',1216);//注册需要验证码
define('ERROR_SESSID',1217);//session key不合法

//文件上传处理
define('ERROR_MK_DIR',1300);//创建目录失败
define('ERROR_DIR_EXISTS',1321);//文件目录已存在
define('ERROR_DIR_NOT_EXISTS',1322);//文件目录不存在
define('ERROR_SERVER_LIMIT_SIZE',1301);//文件大小超过服务器限制
define('ERROR_FILE_MAX_SIZE',1302);//上传文件太大或者上传文件的大小超过了 HTML 表单中 MAX_FILE_SIZE 选项指定的值
define('ERROR_FILE_LOAD_PART',1303);//文件只加载了一部分！
define('ERROR_FILE_LOAD_FAILED',1304);//文件加载失败！
define('ERROR_FILE_TYPE',1305);//文件类型不合法
define('ERROR_FILE_MOVE_FAILED',1306);//文件移动失败
define('ERROR_METHOD_UPLOADED_FILE',1307);//上传方式不合法
define('ERROR_FINE_NOT_TMP_FILE',1308);//找不到临时文件夹
define('ERROR_CANT_WRITE',1309);//文件写入失败
define('ERROR_NOT_UPLOADED_FILE',1310);//没有文件被上传
define('ERROR_FILE_NOT_EXISTS',1311);//文件不存在
define('ERROR_FILE_PATH_NOT_EMPTY',1312);//文件不能为空
define('ERROR_FILE_MK_FAILED',1313);//文件创建失败
define('ERROR_FILE_NOT_READABLE',1314);//文件不可读
define('ERROR_FILE_NOT_WRITEABLE',1315);//文件不可写
define('ERROR_FILE_EXISTS',1316);//文件已存在
define('ERROR_FILE_NOT_UPLOADED',1317);//非上传文件
define('ERROR_FILE_RENAME_FAILED',1318);//修改文件名失败
define('ERROR_FILE_NAME_NOT_EMPTY',1319);//文件名不能为空
define('ERROR_FILE_NAME_INVALID',1320);//文件名不合法
define('ERROR_FILE_COPY_FAILED',1323);//文件复制失败

//活动处理出错
define('ERROR_DEVICE_EMPTY',2000);//设备不能为空，请输入，谢谢
define('ERROR_DEVICE_INVALID',2001);//设备不合法
define('ERROR_DEVICE_EXISTS',2002);//设备已被存在
define('ERROR_DEVICE_INVALID_LEN',2003);//设备长度不合法
define('ERROR_DEVICE_NOT_EXISTS',2004);//设备不存在
define('ERROR_ACTIVITY_EXPIRED',2005);//活动已经过期 
define('ERROR_ACTIVITY_COMING_SOON',2006);//活动未开始

//邮件/短信发送类型
define('IS_TXI',2500);//立即发送
define('NOT_IS_TXI',2501);//不立即发送

//同步数据
define('ERROR_SYNC_FAILED',2600);//同步失败
define('SYNC_SUCCESS',2601);//同步成功
define('SYNC_ALREADY_VERSION',2602);//已经是最新版本
define('SYNC_EXCEPTION_VERSION',2603);//异常版本

//程序调用错误
define('METHOD_NOT_EXISTS',100000); //方法不存在
define('ERROR_EXTENSION_LOADED_FAILED',100001); //拓展加载失败
define('ERROR_CONNECT',100002);//连接失败
define('ERROR_MAINTENANCE',100003);//服务器维护中
define('ERROR_OVER',100004);//该版本应用已经过期，不再进行维护，若有新版本请更新到最新版本
define('ERROR_OPERATING_TOO_FAST',100005);//操作过快
define('ERROR_SAME_OPERATING_TOO_FREQUENT',100006);//相同操作过于频繁

//配置api接口编码及域名相关
define('API_APPSTORE',200000);//appstore.kenxinda.com 应用商店
define('API_INDIA_APPSTORE',200001);//india.appstore.kenxinda.com 应用商店 印度
define('API_EUROPE_APPSTORE',200002);//europe.appstore.kenxinda.com 应用商店 欧洲
define('API_THEMESTORE',200003);//themestore.kenxinda.com 主题商店
define('API_USER',200004);//userserver.kenxinda.com 用户登录及相关
define('API_USER_ALBUM',200005);//userserver.kenxinda.com 云相册
define('API_USER_NOTE',200006);//userserver.kenxinda.com 便签
define('API_MODEL_INTRODUCED',200007);//modelintroduced 模型介绍
define('API_OPERATION',200008);//操作日志记录

//查找手机返回值编码
define('ERROR_PARAMS',2700);//请求参数异常
define('DEVICE_BANDED',2701);//设备已经绑定
define('ERROR_DEVICE_BAND_ERROR',2702);//设备绑定失败
define('DEVICE_BAND_SUCCESS',2703);//设备绑定成功
define('ERROR_LOCATION_ADD_ERROR',2704);//添加定位信息错误
define('DEVICE_LOCATION_ERROR',2705);//获取设备最后定位失败
define('DEVICE_LOCATION_SUCCESS',2706);//获取设备最后定位成功
define('DEVICE_NOTFOUND',2707);//设备查找不到或者设备不是该用户绑定的