$(document).ready(function(){		
		
	$('#submit_button').click(function(){
		
		 var check_submit = true;
		 var re_pwd = regular_password;
		 var pwd = $("input[name='new_password']").val();
		 var pwdRepeat = $("input[name='confirm_password']").val();
		 var next_pwd_element = $('#form-item-pwd').next();
		 var next_confirm_pwd_element = $('#form-item-pwdRepeat').next();
		 var i_error_str = "<i class='i-error'></i>";
		 
		//验证密码
		 if(re_pwd.test(pwd)==false){
			 
			 check_submit = false;
			 next_pwd_element.show();
			 next_pwd_element.find('span').html(i_error_str + v_password_tips);
		 }else{
			 
			 next_pwd_element.hide();
			 next_pwd_element.find('span').html("");	
		 }
		 //再次确认密码
		 
		 if(pwdRepeat != pwd){

			 check_submit = false;
			 next_confirm_pwd_element.show();
			 next_confirm_pwd_element.find('span').html(i_error_str + v_e_password_not_same);
		 }else{
			 
			 next_confirm_pwd_element.hide();
			 next_confirm_pwd_element.find('span').html("");
		 }
		 
		 if(check_submit){
			 
			$('#getpass').submit();
		 }		 
	});
});