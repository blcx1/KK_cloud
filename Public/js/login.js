$(function(){
	if(!placeholderSupport()){   // 判断浏览器是否支持 placeholder
		$('[placeholder]').focus(function() {
			var input = $(this);
			if (input.val() == input.attr('placeholder')) {
				input.val('');
				input.removeClass('placeholder');
			}
		}).blur(function() {
			var input = $(this);
			if (input.val() == '' || input.val() == input.attr('placeholder')) {
				input.addClass('placeholder');
				input.val(input.attr('placeholder'));
			}
		}).blur();
	};
})
function placeholderSupport() {
	return 'placeholder' in document.createElement('input');
}

$(document).ready(function(){

	//验证码点击刷新
	$("#verifyimg").on("click",function(){
				
		refresh_verify_code($(this),"login_verify");
	});
	
	
	$('#login-button').bind('keyup', function(event) {
		if (event.keyCode == "13") {
			$('#login-button').click();
		}
	});
	
	$('#login-button').click(function(){
		var check_submit = true;
		var input_name = $("input[name='user_account']").val();
		var input_password = $("input[name='password']").val();
		var input_code = $("input[name='verify_code']").val();

		if(input_name.length<2 ){
			
			check_submit = false; 
			$('#error-outcon').show();
			$('.error-con').text(v_e_input_username);
		}
		if(input_password.length <= 2){
			
			check_submit = false;
			$('#error-outcon').show();
			$('.error-con').text(v_e_input_password)
		}
		if($('.lgncode').length>0){
			
			if(input_code.length != 4){
				
				check_submit = false;
				$('#error-outcon').show();
				$('.error-con').text(v_e_verify_code);
			}
		}

		if(check_submit){
			
			$('#login-main-form').submit();
		}

	});
        
        document.onkeydown=function(e){
            var e=e ? e : window.event;
            var keyCode = e.which ? e.which : e.keyCode;
            if(keyCode == 13){
                $('#login-button').click();
            }
        }
});
