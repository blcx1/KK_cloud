<?php
/**
 *常量编码对应的语言
 *中文
 **/
return array(
  
// 支付状态 
PS_UNPAYED => '未付款',
PS_PAYED => '已支付',
PS_PAYING => '付款中',
ORDER_COMFIRED => '订单支付确认',
ORDER_CANCELED => '取消订单',
ORDER_REFUNDED => '订单退款',

//支付出错及其他支付对应值
ERROR_PAYMENT_NOT_EXISTS => '支付方式不存在，可能已经下线了',
ERROR_PAYMENT_NOT_CONFIG => '支付方式未配置，或者配置不能使用',
ERROR_PAYMENT_NOT_PAY_CODE => '未配置支付接口名称',
ERROR_PAYMENT_NOT_FILE_EXISTS => '没有对应的支付程序，请建立支付文件程序再使用',

//购买及支付报错
ERROR_PRODUCT_NOT_EXISTS => '产品不存在，或者已经下架',
ERROR_GOLD_NOT_ENOUGH => '金币数量不足或者金额不足',
ERROR_MULTI => '多列错误，具体参考其他列',

//数据库及操作异常
ERROR_MYSQL_CONNECT => '数据库连接失败',
ERROR_MYSQL_EXE_REPORT => '数据库报错',

//非法访问，或者非法数据或者暴力破解
VERIFY_FAILED_ENOUGH => '验证错误过多次',
INVALID_DATA => '非法数据',
INVALID_OPERATE => '非法操作',

//修改用户相关信息，关联类型值
CHANGE_USER_NAME => '修改用户名',
CHANGE_NICK_NAME => '修改昵称',
CHANGE_USER_EMAIL => '修改邮箱地址',
CHANGE_USER_TEL => '修改手机号码',
CHANGE_PASSWORD => '修改密码',

//登陆='注册状态
ERROR_NOT_LOGIN => '未登陆',
ERROR_EXISTS_LOGIN => '已登录过',
ERROR_LOGIN_FAILED => '登录失败',
ERROR_NOT_REGISTER => '未注册',
ERROR_REGISTER_FAILED => '注册失败',
LOGIN_SUCCESS => '登录成功',
REGISTER_SUCCESS => '注册成功',
LOGOUT_SUCCESS => '退出成功',
LOGIN_EXPIRY => '登陆已经过期',
ERROR_THE_SAME_LOGIN => '当前账号在另外个地方登录',
ERROR_LOGIN_FAILED_ENOUGH => '当前该ip异常请求次数过多，暂停该ip登录',
ERROR_LOGOUT_FAILED => '登出失败',

//用户名出错编码
ERROR_USER_EMPTY => '用户名不能为空，请输入，谢谢',
ERROR_USER_INVALID => '用户名输入不合法字符',
ERROR_USER_EXISTS => '用户名已被注册',
ERROR_USER_INVALID_LEN => '用户名长度不能小于位且不能大于个字符，请核对后输入，谢谢',
ERROR_USER_NOT_EXISTS => '用户不存在',

//邮箱地址出错编码
ERROR_EMAIL_EMPTY => '邮箱地址不能为空',
ERROR_EMAIL_INVALID => '邮箱地址不合法，请核对后输入，谢谢',
ERROR_EMAIL_EXISTS => '该邮箱地址已被注册',
ERROR_EMAIL_INVALID_LEN => '邮箱地址长度不能超过个字符',
ERROR_EMAIL_VERIFY => '邮箱已经验证，已绑定',
ERROR_EMAIL_NOT_VERIFY => '邮箱没有验证激活，请验证邮箱',
ERROR_EMAIL_NOT_EXISTS => '邮箱不存在',

//手机出错编码
ERROR_TEL_EMPTY => '手机不能为空，请输入，谢谢',
ERROR_TEL_INVALID => '手机号码不合法',
ERROR_TEL_EXISTS => '手机号码已存在',
ERROR_TEL_INVALID_LEN => '邮箱地址长度不能超过个字符',
ERROR_TEL_VERIFY => '手机号码已经验证，已绑定',
ERROR_TEL_NOT_VERIFY => '手机号码没有验证绑定',

//密码出错编码
ERROR_PASSWORD_EMPTY => '密码不能为空，请输入，谢谢',
ERROR_PASSWORD_INVALID => '密码不合法',
ERROR_PASSWORD_INVALID_LEN => '密码长度在到位，字母区分大小写',
ERROR_PASSWORD_NOT_SAME => '密码输入不一样',
ERROR_PASSWORD => '原密码错误',

//验证码出错编码
ERROR_CAPTCHA_EMPTY => '验证码不能为空',
ERROR_CAPTCHA_INVALID => '验证码不合法，请核对后重新输入',
ERROR_USER_PASSWORD_NOT_MATCH => '用户名或密码错误，请核对后重新输入，谢谢',

//昵称修改
ERROR_NICK_NAME_EMPTY => '昵称不能为空',
ERROR_NICK_NAME_INVALID => '昵称不合法字符',
ERROR_NICK_NAME_INVALID_LEN => '昵称长度在到位',
ERROR_DAY_INVALID => '日期格式不合法',

//验证类型
VERIFY_TYPE_EMAIL => '邮箱验证',
VERIFY_TYPE_TEL => '手机验证',
ERROR_VERIFY_TYPE_INVALID => '验证类型不合法',
ERROR_NOT_MATCH_USER => '没有匹配的用户账号',
VERIFY_TYPE_RESET => '重置验证类型',

//邮件发送相关
ERROR_TEMPLATE_NOT_EXISTS => '邮件/短信模板不存在',
ERROR_SEND_EMAIL_FAILED => '邮件发送失败',
ERROR_SEND_TEL_FAILED => '手机短信发送失败',

//其他出错编码
ERROR_UNKNOWN => '未知错误',
ERROR_BLACKLIST => '该用户被管理员拉入黑名单，请联系管理员',
ERROR_VERIFY_TYPE_NOT_EXISTS => '验证的类型不存在',

//操作编码
ERROR_OPERATE_FAILED => '操作失败',
ERROR_ADD_FAILED => '添加失败',
ERROR_UPDATE_FAILED => '更新失败',
ERROR_DEL_FAILED => '删除失败',
ERROR_EDIT_FAILED => '编辑失败',
ERROR_CONSERVE_FAILED => '保存失败',
ERROR_INSERT_FAILED => '插入失败',
OPERATE_SUCCESS => '操作成功',
ADD_SUCCESS => '添加成功',
UPDATE_SUCCESS => '更新成功',
DEL_SUCCESS => '删除成功',
EDIT_SUCCESS => '编辑成功',
CONSERVE_SUCCESS => '保存成功',
INSERT_SUCCESS => '插入成功',
IS_PERFECT => '需要完善资料',
CHECK_LOGIN_CAPTCHA => '登录需要验证码',
CHECK_REGISTER_CAPTCHA => '注册需要验证码',
ERROR_SESSID => 'session key不合法',

//文件上传处理
ERROR_MK_DIR => '创建目录失败',
ERROR_DIR_EXISTS => '文件目录已存在',
ERROR_DIR_NOT_EXISTS => '文件目录不存在',
ERROR_SERVER_LIMIT_SIZE => '文件大小超过服务器限制',
ERROR_FILE_MAX_SIZE => '上传文件太大',
ERROR_FILE_LOAD_PART => '文件只加载了一部分！',
ERROR_FILE_LOAD_FAILED => '文件加载失败！',
ERROR_FILE_TYPE => '文件类型不合法',
ERROR_FILE_MOVE_FAILED => '文件移动失败',
ERROR_METHOD_UPLOADED_FILE => '上传方式不合法',
ERROR_FINE_NOT_TMP_FILE => '找不到临时文件夹',
ERROR_CANT_WRITE => '文件写入失败',
ERROR_NOT_UPLOADED_FILE => '没有文件被上传',
ERROR_FILE_NOT_EXISTS => '文件不存在',
ERROR_FILE_PATH_NOT_EMPTY => '文件不能为空',
ERROR_FILE_MK_FAILED => '文件创建失败',
ERROR_FILE_NOT_READABLE => '文件不可读',
ERROR_FILE_NOT_WRITEABLE => '文件不可写',
ERROR_FILE_EXISTS => '文件已存在',
ERROR_FILE_NOT_UPLOADED => '非上传文件',
ERROR_FILE_RENAME_FAILED => '修改文件名失败',
ERROR_FILE_NAME_NOT_EMPTY => '文件名不能为空',
ERROR_FILE_NAME_INVALID => '文件名不合法',
ERROR_FILE_COPY_FAILED => '文件复制失败',

//活动处理出错
ERROR_DEVICE_EMPTY => '设备不能为空，请输入，谢谢',
ERROR_DEVICE_INVALID => '设备不合法',
ERROR_DEVICE_EXISTS => '设备已被存在',
ERROR_DEVICE_INVALID_LEN => '设备长度不合法',
ERROR_DEVICE_NOT_EXISTS => '设备不存在',
ERROR_ACTIVITY_EXPIRED => '活动已经过期 ',
ERROR_ACTIVITY_COMING_SOON => '活动未开始',

//邮件/短信发送类型
IS_TXI => '立即发送',
NOT_IS_TXI => '不立即发送',

//同步数据
ERROR_SYNC_FAILED => '同步失败',
SYNC_SUCCESS => '同步成功',
SYNC_ALREADY_VERSION => '已经是最新版本',
SYNC_EXCEPTION_VERSION => '异常版本',

//程序调用错误
METHOD_NOT_EXISTS => '方法不存在',
ERROR_EXTENSION_LOADED_FAILED => '拓展加载失败',
ERROR_CONNECT => '连接失败',
ERROR_MAINTENANCE => '服务器维护中',
ERROR_OVER => '该版本应用已经过期，不再进行维护，若有新版本请更新到最新版本',
ERROR_OPERATING_TOO_FAST => '操作过快',
ERROR_SAME_OPERATING_TOO_FREQUENT => '相同操作过于频繁',

//查找手机返回值编码
ERROR_PARAMS => '请求参数异常',
DEVICE_BANDED => '设备已经绑定',
ERROR_DEVICE_BAND_ERROR => '设备绑定失败',
DEVICE_BAND_SUCCESS => '设备绑定成功',
ERROR_LOCATION_ADD_ERROR => '添加定位信息错误',
DEVICE_LOCATION_ERROR => '获取设备最后定位失败',
DEVICE_LOCATION_SUCCESS => '获取设备最后定位成功',
DEVICE_NOTFOUND => '设备查找不到或者设备不是该用户绑定的',
);