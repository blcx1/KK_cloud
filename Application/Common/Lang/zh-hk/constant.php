<?php
/**
 *常量编码对应的语言
 *繁体
 **/
return array(

//支付狀態
PS_UNPAYED => '未付款',
PS_PAYED => '已支付',
PS_PAYING => '付款中',
ORDER_COMFIRED => '訂單支付確認',
ORDER_CANCELED => '取消訂單',
ORDER_REFUNDED => '訂單退款',

//支付出錯及其他支付對應值
ERROR_PAYMENT_NOT_EXISTS => '支付方式不存在,可能已經下線了',
ERROR_PAYMENT_NOT_CONFIG => '支付方式未配寘,或者配寘不能使用',
ERROR_PAYMENT_NOT_PAY_CODE => '未配寘支付介面名稱',
ERROR_PAYMENT_NOT_FILE_EXISTS => '沒有對應的支付程式,請建立支付檔案程式再使用',

//購買及支付報錯
ERROR_PRODUCT_NOT_EXISTS => '產品不存在,或者已經下架',
ERROR_GOLD_NOT_ENOUGH => '金幣數量不足或者金額不足',
ERROR_MULTI => '多列錯誤,具體參攷其他列',

//資料庫及操作异常
ERROR_MYSQL_CONNECT => '資料庫連接失敗',
ERROR_MYSQL_EXE_REPORT => '資料庫報錯',

//非法訪問,或者非法數據或者暴力破解
VERIFY_FAILED_ENOUGH => '驗證錯誤過多次',
INVALID_DATA => '非法數據',
INVALID_OPERATE => '非法操作',

//修改用戶相關資訊,關聯類型值
CHANGE_USER_NAME => '修改用戶名',
CHANGE_NICK_NAME => '修改昵稱',
CHANGE_USER_EMAIL => '修改郵箱地址',
CHANGE_USER_TEL => '修改手機號碼',
CHANGE_PASSWORD => '修改密碼',

//登入注册狀態
ERROR_NOT_LOGIN => '未登陸',
ERROR_EXISTS_LOGIN => '已登入過',
ERROR_LOGIN_FAILED => '登入失敗',
ERROR_NOT_REGISTER => '未注册',
ERROR_REGISTER_FAILED => '注册失敗',
LOGIN_SUCCESS => '登入成功',
REGISTER_SUCCESS => '注册成功',
LOGOUT_SUCCESS => '退出成功',
LOGIN_EXPIRY => '登入已經過期',
ERROR_THE_SAME_LOGIN => '當前帳號在另外個地方登入',
ERROR_LOGIN_FAILED_ENOUGH => '當前該ip异常請求次數過多,暫停該ip登入',
ERROR_LOGOUT_FAILED => '登出失敗', 

//用戶名出錯編碼
ERROR_USER_EMPTY => '用戶名不能為空,請輸入,謝謝',
ERROR_USER_INVALID => '用戶名輸入不合法字元',
ERROR_USER_EXISTS => '用戶名已被注册',
ERROR_USER_INVALID_LEN => '用戶名長度不能小於比特且不能大於個字元,請核對後輸入,謝謝',
ERROR_USER_NOT_EXISTS => '用戶不存在',

//郵箱地址出錯編碼
ERROR_EMAIL_EMPTY => '郵箱地址不能為空',
ERROR_EMAIL_INVALID => '郵箱地址不合法,請核對後輸入,謝謝',
ERROR_EMAIL_EXISTS => '該郵箱地址已被注册',
ERROR_EMAIL_INVALID_LEN => '郵箱地址長度不能超過個字元',
ERROR_EMAIL_VERIFY => '郵箱已經驗證,已綁定',
ERROR_EMAIL_NOT_VERIFY => '郵箱沒有驗證啟動,請驗證郵箱',
ERROR_EMAIL_NOT_EXISTS => '郵箱不存在',

//手機出錯編碼
ERROR_TEL_EMPTY => '手機不能為空,請輸入,謝謝',
ERROR_TEL_INVALID => '手機號碼不合法',
ERROR_TEL_EXISTS => '手機號碼已存在',
ERROR_TEL_INVALID_LEN => '郵箱地址長度不能超過個字元',
ERROR_TEL_VERIFY => '手機號碼已經驗證,已綁定',
ERROR_TEL_NOT_VERIFY => '手機號碼沒有驗證綁定',

//密碼出錯編碼
ERROR_PASSWORD_EMPTY => '密碼不能為空,請輸入,謝謝',
ERROR_PASSWORD_INVALID => '密碼不合法',
ERROR_PASSWORD_INVALID_LEN => '密碼長度在到位,字母區分大小寫',
ERROR_PASSWORD_NOT_SAME => '密碼輸入不一樣',
ERROR_PASSWORD => '原密碼錯誤',

//驗證碼出錯編碼
ERROR_CAPTCHA_EMPTY => '驗證碼不能為空',
ERROR_CAPTCHA_INVALID => '驗證碼不合法,請核對後重新輸入',
ERROR_USER_PASSWORD_NOT_MATCH => '用戶名或密碼錯誤,請核對後重新輸入,謝謝',

//昵稱修改
ERROR_NICK_NAME_EMPTY => '昵稱不能為空',
ERROR_NICK_NAME_INVALID => '昵稱不合法字元',
ERROR_NICK_NAME_INVALID_LEN => '昵稱長度在到位',
ERROR_DAY_INVALID => '日期格式不合法',

//驗證類型
VERIFY_TYPE_EMAIL => '郵箱驗證',
VERIFY_TYPE_TEL => '手機驗證',
ERROR_VERIFY_TYPE_INVALID => '驗證類型不合法',
ERROR_NOT_MATCH_USER => '沒有匹配的用戶帳號',
VERIFY_TYPE_RESET => '重置驗證類型',

//郵件發送相關
ERROR_TEMPLATE_NOT_EXISTS => '郵件/簡訊範本不存在',
ERROR_SEND_EMAIL_FAILED => '郵件發送失敗',
ERROR_SEND_TEL_FAILED => '手機短信發送失敗',

//其他出錯編碼
ERROR_UNKNOWN => '未知錯誤',
ERROR_BLACKLIST => '該用戶被管理員拉入黑名單,請聯系管理員',
ERROR_VERIFY_TYPE_NOT_EXISTS => '驗證的類型不存在',

//操作編碼
ERROR_OPERATE_FAILED => '操作失敗',
ERROR_ADD_FAILED => '添加失敗',
ERROR_UPDATE_FAILED => '更新失敗',
ERROR_DEL_FAILED => '删除失敗',
ERROR_EDIT_FAILED => '編輯失敗',
ERROR_CONSERVE_FAILED => '保存失敗',
ERROR_INSERT_FAILED => '插入失敗',
OPERATE_SUCCESS => '操作成功',
ADD_SUCCESS => '添加成功',
UPDATE_SUCCESS => '更新成功',
DEL_SUCCESS => '删除成功',
EDIT_SUCCESS => '編輯成功',
CONSERVE_SUCCESS => '保存成功',
INSERT_SUCCESS => '插入成功',
IS_PERFECT => '需要完善資料',
CHECK_LOGIN_CAPTCHA => '登入需要驗證碼',
CHECK_REGISTER_CAPTCHA => '注册需要驗證碼',
ERROR_SESSID => 'session key不合法',

//文件上傳處理
ERROR_MK_DIR => '創建目錄失敗',
ERROR_DIR_EXISTS => '檔案目錄已存在',
ERROR_DIR_NOT_EXISTS => '檔案目錄不存在',
ERROR_SERVER_LIMIT_SIZE => '文件大小超過服務器限制',
ERROR_FILE_MAX_SIZE => '上傳文件太大',
ERROR_FILE_LOAD_PART => '檔案只加載了一部分！',
ERROR_FILE_LOAD_FAILED => '檔案加載失敗！',
ERROR_FILE_TYPE => '檔案類型不合法',
ERROR_FILE_MOVE_FAILED => '檔案移動失敗',
ERROR_METHOD_UPLOADED_FILE => '上傳管道不合法',
ERROR_FINE_NOT_TMP_FILE => '找不到臨時資料夾',
ERROR_CANT_WRITE => '檔案寫入失敗',
ERROR_NOT_UPLOADED_FILE => '沒有檔案被上傳',
ERROR_FILE_NOT_EXISTS => '檔案不存在',
ERROR_FILE_PATH_NOT_EMPTY => '檔案不能為空',
ERROR_FILE_MK_FAILED => '檔案創建失敗',
ERROR_FILE_NOT_READABLE => '檔案不可讀',
ERROR_FILE_NOT_WRITEABLE => '檔案不可寫',
ERROR_FILE_EXISTS => '檔案已存在',
ERROR_FILE_NOT_UPLOADED => '非上傳文件',
ERROR_FILE_RENAME_FAILED => '修改檔名失敗',
ERROR_FILE_NAME_NOT_EMPTY => '檔名不能為空',
ERROR_FILE_NAME_INVALID => '檔名不合法',
ERROR_FILE_COPY_FAILED => '檔案複製失敗', 

//活動處理出錯
ERROR_DEVICE_EMPTY => '設備不能為空,請輸入,謝謝',
ERROR_DEVICE_INVALID => '設備不合法',
ERROR_DEVICE_EXISTS => '設備已被存在',
ERROR_DEVICE_INVALID_LEN => '設備長度不合法',
ERROR_DEVICE_NOT_EXISTS => '設備不存在',
ERROR_ACTIVITY_EXPIRED => '活動已經過期',
ERROR_ACTIVITY_COMING_SOON => '活動未開始',

//郵件/短信發送類型
IS_TXI => '立即發送',
NOT_IS_TXI => '不立即發送',

//同步數據
ERROR_SYNC_FAILED => '同步失敗',
SYNC_SUCCESS => '同步成功',
SYNC_ALREADY_VERSION => '已經是最新版本',
SYNC_EXCEPTION_VERSION => '异常版本',

//程式調用錯誤
METHOD_NOT_EXISTS => '方法不存在',
ERROR_EXTENSION_LOADED_FAILED => '拓展加載失敗',
ERROR_CONNECT => '連接失敗',
ERROR_MAINTENANCE => '服務器維護中',
ERROR_OVER => '該版本應用已經過期,不再進行維護,若有新版本請更新到最新版本',
ERROR_OPERATING_TOO_FAST => '操作過快',
ERROR_SAME_OPERATING_TOO_FREQUENT => '相同操作過於頻繁',

//查找手機返回值編碼
ERROR_PARAMS => '請求參數异常',
DEVICE_BANDED => '設備已經綁定',
ERROR_DEVICE_BAND_ERROR => '設備綁定失敗',
DEVICE_BAND_SUCCESS => '設備綁定成功',
ERROR_LOCATION_ADD_ERROR => '添加定位資訊錯誤',
DEVICE_LOCATION_ERROR => '獲取設備最後定位失敗',
DEVICE_LOCATION_SUCCESS => '獲取設備最後定位成功',
DEVICE_NOTFOUND => '設備查找不到或者設備不是該用戶綁定的', 
);