$(function(){

	//验证码点击刷新
	$("#verifyimg,.pass-change-verifyCode").on("click",function(){
		
		refresh_verify_code($('#verifyimg'),"register_verify");				
	});
	
	$('.submit-step').click(function(){
		 //验证用户名
		 var check_submit = true;
		 var verify_code_len = 0;
		 var user_regName = $("input[name='regName']").val();
		 var email = $("input[name='email']").val();
		 var pwd = $("input[name='pwd']").val();
		 var pwdRepeat = $("input[name='pwdRepeat']").val();
		 var email = $("input[name='email']").val();
		 var authcode = $("input[name='authcode']").val();
		 var re_pwd = regular_password;
		 var re_regName = regular_user_name;
		 var re_email = regular_email;
		 var i_error_str = "<i class='i-error'></i>";
		 var i_status_str = "<i class='i-status'></i>";
		 var account_next_element = $('#form-item-account').next().find('span');
		 var password_next_element = $('#form-item-pwd').next().find('span');
		 var confirm_password_next_element = $('#form-item-pwdRepeat').next().find('span');
		 var email_next_element = $('#form-item-email').next().find('span');
		 var verify_code_next_element = $('#form-item-icode').next().next().next().find('span');
		 
		 if(re_regName.test(user_regName)==false){
		 
			 check_submit = false; 
			 account_next_element.html(i_error_str + v_username_tips);
		 }else{
		 
			 account_next_element.html(i_status_str);
		 }
		 //验证密码
		 if(re_pwd.test(pwd)==false){
		 
			 check_submit = false;
			 password_next_element.html(i_error_str + v_password_tips);
		 }else{
			 password_next_element.html(i_status_str);
		 }
		 //再次确认密码
		 if(pwdRepeat != pwd){
		 
			 check_submit = false;
			 confirm_password_next_element.html(i_error_str + v_e_password_not_same);
		 }else{
		 
			 confirm_password_next_element.html(i_status_str);
		 }
		 //邮箱验证
		 if(re_email.test(email) == false){
		 
			 check_submit = false;
			 email_next_element.html(i_error_str + v_e_input_email);
		 }else{
		 
			 email_next_element.html(i_status_str);
		 }
		 
		 //验证码验证
		 verify_code_len = authcode.length;
		 if(verify_code_len != 4){
		 
			 check_submit = false;
			 verify_code_next_element.html(i_error_str + v_e_verify_code);
		 }else{
			 verify_code_next_element.html(i_status_str);
		 }
		 if(check_submit){
		 
			 $("#register-form").submit();				 
		 }
	});


	/*if(!(/^[a-zA-Z]+$/).test($("#register-form label"))){

		$("#register-form label").css("margin-left","-40px");
	}
	if((/^[\u4e00-\u9fa5]+$/).test($("#register-form label"))){

		$("#register-form label").css("margin-left","0");
	}*/
});  