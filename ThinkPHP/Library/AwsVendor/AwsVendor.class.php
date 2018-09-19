<?php

namespace AwsVendor;

set_time_limit(0);
global $config_onload_array;
if(!defined('AWS_LOADED_VENDOR') && C('IS_AWS_URL')){
	
	$prefix_path = '/dev/shm/download/aws_sdk_php/';
	require_once $prefix_path.'vendor/autoload.php';
	$config_onload_array = include_once '/mnt/sdb1/webroot/config.php';
	define('AWS_LOADED_VENDOR',true);	
}
class AwsVendor {
	
	protected $is_valid_aws = false;
	protected $aws_upload_path = '';
	protected $aws_upload_url = '';
	protected $aws_base_path = '/Public/Upload/';
	protected $error_array = array();
	protected $error_code_array = array();
	protected $config_array = array();
	protected $s3Client = null;
	protected $concurrency = 4;
	protected $bucket = 'userserver.kenxinda.com';
	protected $default_acl = 'public-read';
	protected $acl_array = array('private','public-read','public-read-write',
	                             'authenticated-read','bucket-owner-read',
							     'bucket-owner-full-control','log-delivery-write');				
	
	//初始化文件
	public function __construct($bucket = '',$aws_base_path = '',$config_array = array(),$concurrency = 4){		
		global $config_onload_array;		
		
		$is_valid_aws = C('IS_AWS_URL') ? true : false;		
		$this->is_valid_aws = $is_valid_aws;		
		if($is_valid_aws){
			
			$bucket = trim($bucket);
			$concurrency = intval($concurrency);
			$aws_base_path = trim($aws_base_path);
			if($concurrency > 0){
					
				$this->concurrency = $concurrency;
			}
			if(strlen($aws_base_path) > 0){
					
				$this->aws_base_path = $aws_base_path;
			}
			if(!(is_array($config_array) && count($config_array) > 0) && is_array($config_onload_array) && count($config_onload_array) > 0){
					
				$config_array = $config_onload_array;
			}
			$this->config_array = $config_array;
			$this->s3Client = \Aws\S3\S3Client::factory($config_array);
			if(strlen($bucket) > 0 && \Aws\S3\S3Client::isValidBucketName($bucket)){
					
				$this->bucket = $bucket;
			}
			try {
					
				$bucket_list = $this->get_bucket_list();
				if(!in_array($this->bucket,$bucket_list)){
			
					$this->create_bucket();
				}
			} catch (\Aws\S3\Exception\S3Exception $e) {
					
				$this->error_code_array['result'] = 'AWS'.$e->getAwsErrorCode();// The AWS error code (e.g., )
				$this->error_array['result'] = $e->getMessage();// The bucket couldn't be created
			}
		}		
	}
	
	//释放
	public function __destruct(){

		$this->aws_upload_path = '';
		$this->aws_upload_url = '';
		$this->aws_base_path = '';
		$this->error_array = array();
		$this->error_code_array = array();
		$this->config_array = array();
		$this->s3Client = null;
		$this->concurrency = 0;
		$this->bucket = '';
		$this->default_acl = '';
		$this->acl_array = array();
	}
	
	/**
	 * 创建bucket
	 */
	public function create_bucket(){
		
		if($this->is_valid_aws){
			
			$bucket = $this->bucket;
			$s3Client = $this->s3Client;
			$config_array = $this->config_array;
			$result = $s3Client->createBucket(array(
					'Bucket' => $bucket,
					'LocationConstraint' => $config_array['region'],
			));				
			$result = $s3Client->waitUntil('BucketExists', array('Bucket' => $bucket));
		}			
	}
	
	/**
	 * 获取bucket列表
	 * @return multitype:unknown
	 */
	public function get_bucket_list(){
		
		$list_array = array();
		if($this->is_valid_aws){
			
			$s3Client = $this->s3Client;
			$result = $s3Client->listBuckets();
			$buckets_array = isset($result['Buckets']) ? (array) $result['Buckets'] : array();		
			if(count($result['Buckets']) > 0){
							
				foreach($buckets_array as $bucket){
					
					$list_array[] = $bucket['Name'];
				}
			}
		}
		return $list_array;
	}
	
	/**
	 * 设置属性
	 * @param unknown $name
	 * @param unknown $value
	 */
	public function set_name($name,$value){
		
		$name = trim($name);
		if(strlen($name) > 0){
			
			$this->$name = $value;
		}
	}
	
	/**
	 * 获取属性值
	 * @param unknown $name
	 * @return NULL
	 */
	public function get_name($name){
		
		$value = null;
		$name = trim($name);
		if(strlen($name) > 0 && isset($this->$name)){
			
			$value = $this->$name;
		}
		return $value;
	}
	
	/**
	 * 获取错误编码
	 * @return array
	 */	 
	public function get_error_code_array(){
		
		return (array)$this->error_code_array;
	}
	
	/**
	 * 获取错误信息
	 * @return array
	 */
	public function get_error_array(){
		
		return (array)$this->error_array;
	}
	
	/**
	 * 获取文件上传的路径
	 * @param unknown $prefix_path
	 * @param unknown $file_name
	 * @return string
	 */
	public function get_aws_file_path($prefix_path,$file_name){
		
		$file_path = '';		
		$prefix_path = trim($prefix_path);
		if(strlen($prefix_path) > 0 && substr($prefix_path,-1) == '/'){
			
			$prefix_path = substr($prefix_path,0,-1);
		}
		$file_path = $this->aws_base_path.$prefix_path.'/'.trim($file_name);		
		return $file_path;
	}
	
	/**
	 * 获取上传文件的url地址
	 * @return string
	 */
	public function get_aws_upload_file_url(){
		
		return $this->aws_upload_url;
	}
	
	/**
	 * 获取上传文件在aws的路径
	 * @return string
	 */
	public function get_aws_upload_path(){
		
		return $this->aws_upload_path;
	}
	
	/**
	 * 上传文件处理
	 * @param unknown $file_path
	 * @param unknown $acl
	 * @return boolean
	 */
	public function upload_process($file_path,$pathToFile,$acl){
				
		if(!$this->is_valid_aws){
			
			return true;
		}
		$check = false;
		$file_path = trim($file_path);
		$pathToFile = trim($pathToFile);
		if(count($this->error_code_array) > 0 || count($this->error_array) > 0 || strlen($file_path) <= 0 || strlen($pathToFile) <= 0){
			
			return $check;
		}
		$acl = trim($acl);
		$bucket = $this->bucket;
		$s3Client = $this->s3Client;
		$acl = strlen($acl) > 0 && in_array($acl,$this->acl_array) ? $acl : $this->default_acl;			
		$uploader = \Aws\S3\Model\MultipartUpload\UploadBuilder::newInstance()
		->setClient($s3Client)
		->setSource($file_path)
		->setBucket($bucket)
		->setOption('ACL', $acl)
		->setKey($pathToFile)
		->setConcurrency($this->concurrency)
		->build();
		try {
			
			$result = $uploader->upload();
			$check = true;			
			$this->aws_upload_path = isset($result['Key']) ? $result['Key'] : '';
			$this->aws_upload_url = isset($result['Location']) ? $result['Location'] : '';
		} catch (\Aws\Common\Exception\MultipartUploadException $e) {
			
			$uploader->abort();			
		}
	    return $check;
	}
	
	/**
	 * 获取文件的url地址
	 * @param unknown $url_path
	 * @return Ambigous <string, \Guzzle\Http\Url>
	 */
	public function get_plain_url($url_path){
		
		$plain_url = '';
		if($this->is_valid_aws){
			
			$url_path = trim($url_path);
			if(strlen($url_path) > 0){
					
				if(substr($url_path,0,3) === '/./'){
			
					$url_path = substr($url_path,3);
				}elseif(substr($url_path,0,2) === './'){
			
					$url_path = substr($url_path,2);
				}elseif(substr($url_path,0,1) === '/'){
			
					$url_path = substr($url_path,1);
				}
			}
			
			if(strlen($url_path) > 0){
					
				$bucket = $this->bucket;
				$s3Client = $this->s3Client;
				$plain_url = $s3Client->getObjectUrl($bucket, $url_path);
				$plain_url = str_replace('https://', 'http://', $plain_url);
			}
		}	
		return $plain_url;
	}
	
	/**
	 * 删除文件
	 * @param unknown $file_path
	 * @return boolean
	 */
	public function delete_process($file_path){
				
		if(!$this->is_valid_aws){
				
			return true;
		}
		$check = false;
		$file_path = trim($file_path);
		if(strlen($file_path) > 0 && substr($file_path,0,2) == './'){
			
			$file_path = substr($file_path,2);
		}
		if(count($this->error_code_array) > 0 || count($this->error_array) > 0 || strlen($file_path) <= 0){
			
			return $check;
		}
		
		$bucket = $this->bucket;
		$s3Client = $this->s3Client;
		$result = $s3Client->deleteObject(array(
				// Bucket is required
				'Bucket' => $bucket,
				// Key is required
				'Key' => $file_path,
				//'MFA' => 'string',
				//'VersionId' => 'string',
				//'RequestPayer' => 'string',
		));
		$check = true;
		return $check;
	}
    
}