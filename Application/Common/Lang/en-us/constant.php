<?php
/**
 *常量编码对应的语言
 *英文
 **/
return array(
  
// 支付状态 
PS_UNPAYED => 'unpayed',
PS_PAYED => 'payed',
PS_PAYING => 'paying',
ORDER_COMFIRED => 'order comfired',
ORDER_CANCELED => 'order canceled',
ORDER_REFUNDED => 'order refunded',

// Error and other payment paid the corresponding value
ERROR_PAYMENT_NOT_EXISTS => 'payment does not exist, it may have been off the assembly line',
ERROR_PAYMENT_NOT_CONFIG => 'payment is not configured, or the configuration can not be used',
ERROR_PAYMENT_NOT_PAY_CODE => 'pay is not configured interface name',
ERROR_PAYMENT_NOT_FILE_EXISTS => 'does not correspond to the payment program, create payment files for further use',

// Purchase and payment error
ERROR_PRODUCT_NOT_EXISTS => 'products do not exist, or has the shelf',
ERROR_GOLD_NOT_ENOUGH => 'insufficient number of coins or insufficient funds',
ERROR_MULTI => 'multi-column error, with particular reference to the other columns',

// Database and operating anomalies
ERROR_MYSQL_CONNECT => 'Database connection failed',
ERROR_MYSQL_EXE_REPORT => 'Database error',

// Illegal access or illegal data or brute force
VERIFY_FAILED_ENOUGH => 'validation error many times',
INVALID_DATA => 'illegal data',
INVALID_OPERATE => 'illegal operation',

// Modify user information associated with the type of value
CHANGE_USER_NAME => 'Modify the user name',
CHANGE_NICK_NAME => 'Modify the nickname',
CHANGE_USER_EMAIL => 'Modify e-mail address',
CHANGE_USER_TEL => 'Modify your phone number',
CHANGE_PASSWORD => 'Change Password',

// Login = 'registration status
ERROR_NOT_LOGIN => 'not landing',
ERROR_EXISTS_LOGIN => 'logged over',
ERROR_LOGIN_FAILED => 'Login failed',
ERROR_NOT_REGISTER => 'Unregistered',
ERROR_REGISTER_FAILED => 'Registration failed',
LOGIN_SUCCESS => 'Login successful',
REGISTER_SUCCESS => 'successfully registered',
LOGOUT_SUCCESS => 'quit success',
LOGIN_EXPIRY => 'login has expired',
ERROR_THE_SAME_LOGIN => 'current account in another place Log',
ERROR_LOGIN_FAILED_ENOUGH => 'The current ip exception request too many times, pause the ip login',
ERROR_LOGOUT_FAILED => 'Logout failed',

// Username erroneous coding
ERROR_USER_EMPTY => 'Username can not be empty, enter, thank you',
ERROR_USER_INVALID => 'Username entered illegal characters',
ERROR_USER_EXISTS => 'user name has been registered',
ERROR_USER_INVALID_LEN => 'Username can not be less than the length of the bit and can not be larger than characters, please check input, thank you',
ERROR_USER_NOT_EXISTS => 'user does not exist',

// E-mail address coding error
ERROR_EMAIL_EMPTY => 'E-mail address can not be empty',
ERROR_EMAIL_INVALID => 'email address is invalid, please check input, thank you',
ERROR_EMAIL_EXISTS => 'The email address is already registered',
ERROR_EMAIL_INVALID_LEN => 'E-mail address can not be longer than characters',
ERROR_EMAIL_VERIFY => 'E-mail has been verified, is bound',
ERROR_EMAIL_NOT_VERIFY => 'mailbox not verified activated, verify mailbox',
ERROR_EMAIL_NOT_EXISTS => 'mailbox does not exist',

// Phone erroneous coding
ERROR_TEL_EMPTY => 'mobile phones can not be empty, enter, thank you',
ERROR_TEL_INVALID => 'phone number is not legal',
ERROR_TEL_EXISTS => 'phone number already exists',
ERROR_TEL_INVALID_LEN => 'E-mail address can not be longer than characters',
ERROR_TEL_VERIFY => 'phone number has been verified, is bound',
ERROR_TEL_NOT_VERIFY => 'phone number is not verified binding',

// Password erroneous coding
ERROR_PASSWORD_EMPTY => 'Passwords can not be empty, enter, thank you',
ERROR_PASSWORD_INVALID => 'password is not lawful',
ERROR_PASSWORD_INVALID_LEN => 'password length in place, case sensitive',
ERROR_PASSWORD_NOT_SAME => 'password is not the same',
ERROR_PASSWORD => 'old password is wrong',

// Coding error codes
ERROR_CAPTCHA_EMPTY => 'codes can not be empty',
ERROR_CAPTCHA_INVALID => 'not legal codes, please check and re-enter',
ERROR_USER_PASSWORD_NOT_MATCH => 'user name or password is incorrect, please check and re-enter, thank you',

// Modify nickname
ERROR_NICK_NAME_EMPTY => 'nickname can not be empty',
ERROR_NICK_NAME_INVALID => 'Nickname illegal character',
ERROR_NICK_NAME_INVALID_LEN => 'nickname length in place',
ERROR_DAY_INVALID => 'Date format is not lawful',

// Authentication Type
VERIFY_TYPE_EMAIL => 'E-mail verification',
VERIFY_TYPE_TEL => 'phone verification',
ERROR_VERIFY_TYPE_INVALID => 'authentication type is not lawful',
ERROR_NOT_MATCH_USER => 'did not match any user account',
VERIFY_TYPE_RESET => 'Reset authentication type',

// Send messages related
ERROR_TEMPLATE_NOT_EXISTS => 'Mail/SMS template does not exist',
ERROR_SEND_EMAIL_FAILED => 'Failed to send message',
ERROR_SEND_TEL_FAILED => 'Failed to send SMS',

// Other error coding
ERROR_UNKNOWN => 'Unknown error',
ERROR_BLACKLIST => 'This user is an administrator pulled into the blacklist, please contact the administrator',
ERROR_VERIFY_TYPE_NOT_EXISTS => 'type of validation does not exist',

// Encoding operation
ERROR_OPERATE_FAILED => 'Operation failed',
ERROR_ADD_FAILED => 'Failed to add',
ERROR_UPDATE_FAILED => 'update failed',
ERROR_DEL_FAILED => 'Delete failed',
ERROR_EDIT_FAILED => 'Edit failed',
ERROR_CONSERVE_FAILED => 'Failed to save',
ERROR_INSERT_FAILED => 'insert failed',
OPERATE_SUCCESS => 'successful operation',
ADD_SUCCESS => 'added successfully',
UPDATE_SUCCESS => 'Update success',
DEL_SUCCESS => 'deleted successfully',
EDIT_SUCCESS => 'edited successfully',
CONSERVE_SUCCESS => 'successfully saved',
INSERT_SUCCESS => 'insert success',
IS_PERFECT => 'need to improve information',
CHECK_LOGIN_CAPTCHA => 'Login required codes',
CHECK_REGISTER_CAPTCHA => 'requires registration codes',
ERROR_SESSID => 'session key is not lawful',

// File uploads
ERROR_MK_DIR => 'Failed to create directory',
ERROR_DIR_EXISTS => 'file directory already exists',
ERROR_DIR_NOT_EXISTS => 'file directory does not exist',
ERROR_SERVER_LIMIT_SIZE => 'file size exceeds server limits',
ERROR_FILE_MAX_SIZE => 'Upload file size is too long',
ERROR_FILE_LOAD_PART => 'file to load only a part of! ',
ERROR_FILE_LOAD_FAILED => 'file failed to load! ',
ERROR_FILE_TYPE => 'file type is not lawful',
ERROR_FILE_MOVE_FAILED => 'Failed to move file',
ERROR_METHOD_UPLOADED_FILE => 'uploading illegal',
ERROR_FINE_NOT_TMP_FILE => 'Missing a temporary folder',
ERROR_CANT_WRITE => 'Failed to write file',
ERROR_NOT_UPLOADED_FILE => 'No file was uploaded',
ERROR_FILE_NOT_EXISTS => 'File does not exist',
ERROR_FILE_PATH_NOT_EMPTY => 'file can not be empty',
ERROR_FILE_MK_FAILED => 'creation failed',
ERROR_FILE_NOT_READABLE => 'The file is unreadable',
ERROR_FILE_NOT_WRITEABLE => 'File not writable',
ERROR_FILE_EXISTS => 'file already exists',
ERROR_FILE_NOT_UPLOADED => 'Non-upload files',
ERROR_FILE_RENAME_FAILED => 'Failed to modify the file name',
ERROR_FILE_NAME_NOT_EMPTY => 'file name can not be empty',
ERROR_FILE_NAME_INVALID => 'file name is not valid',
ERROR_FILE_COPY_FAILED => 'File Copy failed',

// Events processing error
ERROR_DEVICE_EMPTY => 'equipment can not be empty, enter, thank you',
ERROR_DEVICE_INVALID => 'equipment is not lawful',
ERROR_DEVICE_EXISTS => 'device has been existence',
ERROR_DEVICE_INVALID_LEN => 'Device length illegal',
ERROR_DEVICE_NOT_EXISTS => 'device does not exist',
ERROR_ACTIVITY_EXPIRED => 'activity has expired',
ERROR_ACTIVITY_COMING_SOON => 'activities did not start',

// E-mail / SMS type
IS_TXI => 'Now Send',
NOT_IS_TXI => 'Do not send immediately',

//Synchronous Data
ERROR_SYNC_FAILED => 'Synchronization failed',
SYNC_SUCCESS => 'Synchronization success',
SYNC_ALREADY_VERSION => 'is already the latest version',
SYNC_EXCEPTION_VERSION => 'abnormal version',

// Procedure call error
METHOD_NOT_EXISTS => 'method does not exist',
ERROR_EXTENSION_LOADED_FAILED => 'expansion failed to load',
ERROR_CONNECT => 'connection failed',
ERROR_MAINTENANCE => 'Server Maintenance',
ERROR_OVER => 'The version of the application has expired and is no longer maintained, if the new version, please update to the latest version',
ERROR_OPERATING_TOO_FAST => 'operating too fast',
ERROR_SAME_OPERATING_TOO_FREQUENT => 'the same operation is too frequent',

// Find the phone return value encoding
ERROR_PARAMS => 'request parameter abnormal',
DEVICE_BANDED => 'equipment already bound',
ERROR_DEVICE_BAND_ERROR => 'binding equipment failure',
DEVICE_BAND_SUCCESS => 'Device Bind success',
ERROR_LOCATION_ADD_ERROR => 'Add location information error',
DEVICE_LOCATION_ERROR => 'Get final positioning equipment failure',
DEVICE_LOCATION_SUCCESS => 'Get Device final positioning success',
DEVICE_NOTFOUND => 'can not find the device or the device is not bound to the user',
);