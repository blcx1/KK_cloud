var page_size = 30;//一页个数
var date_timezone = 8;//东八区
var charset = "utf-8";

//用户相关正则表达式
var regular_phone = /^[0-9]{3,}[\s0-9]*$/;
var regular_user_name = /^[^-_][\u4E00-\u9FA5a-zA-Z0-9_-]+$/;
var regular_password = /^[a-z0-9]{6,18}$/i;
var regular_email = /^[a-z0-9]+[a-z0-9+_\-\.]+@[a-z0-9]+[a-z0-9+_\-\.]+[a-z]{2,6}$/i;

//头像相关
var max_file_size = 2*1024*1024;//2M
var portrait = "http://download.kenxinda.com/userserver/Public/Upload/avatar/1.png";//默认头像

//更新验证码
function refresh_verify_code(object,verify_name){
	
	var img_src = base_module_url + '/Index/verify/';
	if(verify_name != ""){
		
		img_src += 'v/'+verify_name+'/';
	}
	img_src += (new Date()).valueOf();
	object.attr("src",img_src);
}

//未登录跳转到登录页
function redirct_login(){
	
	window.location.href = base_module_url + "/Index/login.html";
}

//ajax 返回json后发现未登录跳转
function redirct(result){
	
	if(result.is_login == 0){
		
		layer.msg(v_e_not_login,{icon: 0,skin: 'layer-ext-moon'});
		if(result.url == undefined){
			
			redirct_login();
		}else{
			
			window.location.href = result.url;
		}		
	}
}

//服务器时间转化成本地时间
function get_server2local_time(server_date){
	
	var year;
	var month;//js从0开始取 
	var date; 
	var hour; 
	var minutes; 
	var second;
	var local_time_str = "";
	var date_object = new Date();
	var reg_object = new RegExp("-","g");
	server_date = server_date.replace(reg_object,"/");
	var server_date_object = new Date(server_date);
	var local_time = server_date_object.getTime() - (date_timezone*60 + date_object.getTimezoneOffset())*60*1000;
	date_object.setTime(local_time);
	year = date_object.getFullYear();
	month = date_object.getMonth()+1;
	date = date_object.getDate();
	hour = date_object.getHours();
	minutes = date_object.getMinutes();
	second = date_object.getSeconds();
	local_time_str = year.toString() + "-";
	if(month < 10){
		
		local_time_str += "0" + month.toString() + "-";
	}else{
		
		local_time_str += month.toString() + "-";
	}
	if(date < 10){
		
		local_time_str += "0" + date.toString() + " ";
	}else{
		
		local_time_str += date.toString() + " ";
	}
	if(hour < 10){
		
		local_time_str += "0" + hour.toString() + ":";
	}else{
		
		local_time_str += hour.toString() + ":";
	}
	if(minutes < 10){
		
		local_time_str += "0" + minutes.toString() + ":";
	}else{
		
		local_time_str += minutes.toString() + ":";
	}
	if(second < 10){
		
		local_time_str += "0" + second.toString();
	}else{
		
		local_time_str += second.toString();
	}
	
	return local_time_str;
}

//判断是否定义
function is_undefined(variable){
	
	return typeof variable == 'undefined' ? true : false;
}

//判断某个元素是否在某个数组中
function in_array(needle, haystack){
	
	if(typeof needle == 'string' || typeof needle == 'number') {
		
		for(var i in haystack) {
			
			if(haystack[i] == needle) {
				
				return true;
			}
		}
	}
	return false;
}

//去掉两边的空格
function trim(str) {
	
	return (str + '').replace(/(\s+)$/g, '').replace(/^\s+/g, '');
}

//计算带中文字符的长度
function mb_strlen(str) {
	
	var len = 0;
	for(var i = 0; i < str.length; i++) {
		
		len += str.charCodeAt(i) < 0 || str.charCodeAt(i) > 255 ? (charset == 'utf-8' ? 3 : 2) : 1;
	}
	return len;
}

//判别是否为邮箱地址
function is_mail(mail){
	
	var check = false;
	mail = trim(mail);
	if(mail.length > 0 && mail.length <= 60){
		
		if(regular_email.test(mail)){
			
			check = true;
		}
	}
	return check;
}

//判别是否为手机地址
function is_phone(phone){
	
	var check = false;
	phone = trim(phone);
	if(phone.length > 0 && phone.length <= 16){
		
		if(regular_phone.test(phone)){
			
			check = true;
		}
	}
	return check;
}

