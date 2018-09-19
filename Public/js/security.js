var is_click = true;
var timeTask ;
var timeCount=60;
var check_portrait = false;

function clear_time(){

	is_click = true;
	$('.verify-sendbtn').css("background", '').text(v_resend);
	$('.verify-sendbtn').css("color", '#000');
	timeCount =60;
	clearInterval(timeTask);
}

function change_time(){
	timeTask = setInterval(function(){
		timeCount--;
		if(timeCount<=0){
			is_click = true;
			$('.verify-sendbtn').css("background",'white').val(v_resend);
			$('.verify-sendbtn').removeAttr("disabled").css("color", '#000');
			timeCount =60;
			clearInterval(timeTask);

		}else{
			is_click = false;
			$('.verify-sendbtn').css("background", '#eaeaea').val(v_already_send+'（' + timeCount+ '）');
			$('.verify-sendbtn').attr({"disabled":true}).css("color", '#888');
		}
	},1000);
}

function ajax_file_upload(){

	if(check_portrait){

		$.ajaxFileUpload({
	        url: base_module_url+'/Index/changePortrait.html?is_ajax=1',
	        type: 'post',
	        secureuri: false, //一般设置为false
	        fileElementId: 'portrait', // 上传文件的id、name属性名
	        dataType: 'json', //返回值类型，一般设置为json、application/json
	     //   elementIds: elementIds, //传递参数到服务器
	        success: function(data){  
	    		switch (data.status){

					case 0:
						if(data.result!=''){
							$('#err_photo').css('display','block').text(data.result);
						}
						break;
					case 1:

						check_portrait = false;
						$("#preview").html('');
						$("#portrait").val('');
						$('.na-img-bg-area img').attr('src',data.portrait)
						msg = v_change_success;
						$('#set_head').hide();
						layer.alert(msg, {
							skin: 'layui-layer-molv' //样式类名
							,closeBtn: 0
						});
						$('.layui-layer-title').text(v_information);
						$('.layui-layer-btn0').text(v_determine);
						break;
				}
				if(navigator.userAgent.indexOf("MSIE") != -1){

					$('#portrait').bind("change",function(event) {

						upload_change(event,"ie");
					}).live("change",function(event) {

						upload_change(event,"notie");
					});
				}else{

					$('#portrait').replaceWith($("#portrait").clone(true));
				}

			}
		});
	}
}

function ReplaceAll(str, sptr, sptr1){

	while (str.indexOf(sptr) >= 0){
		str = str.replace(sptr, sptr1);
	}
	return str;
}

function upload_change(event,view_type){

	var tmppath = "";
	if(view_type == "ie"){

		$('#portrait').select();
		tmppath = document.selection.createRange().text;
		if(tmppath.length > 1){

			check_portrait = true;
			$("#preview").html('<img class="preview_portrait" src="'+ tmppath +'" width="90" height="90" />');
			return check_portrait;
		}

	}else{

		tmppath = URL.createObjectURL(event.target.files[0]);
	}
	if(tmppath.length > 1){

		var check_file_type = false;
		var file_type = event.target.files[0].type;
		var file_size = event.target.files[0].size;

		switch(file_type){

			case 'image/jpeg':
			case 'image/png':
			case 'image/gif':

				check_file_type = true;
				break;
			default:
				check_file_type = false;
				break;

		}
		if(check_file_type && max_file_size >= file_size){

			check_portrait = true;
			$("#preview").html('<img class="preview_portrait" src="'+ tmppath +'" width="90" height="90" />');

		}else{

			var error_msg = check_file_type ? v_e_portrait_file_type : v_e_portrait_file_max_size;

			$('#err_photo').css('display','block').text(error_msg);
		}
	}
}

$(function(){

	//上传头像
	$('#sethead').click(function(){
		$('#set_head,.mod_tip_bd').show();

	});
	$('#portrait').bind("change",function(event) {

		if(navigator.userAgent.indexOf("MSIE") != -1){

			upload_change(event,"ie");
		}else{

			upload_change(event,"notie");
		}

	}).live("change",function(event) {

		upload_change(event,"notie");
	});

	$('.na-img-bg-area').css('background','rgba(0, 0, 0, 0) url("'+portrait+'") no-repeat scroll 0 0');

	//验证码点击刷新
	$("#verifyimg,#pass_Change").on("click",function(){
		
		refresh_verify_code($('#verifyimg'),"change_password_verify");		
	});
	$("#code_verifyimg,.email_change").on("click",function(){
		
		refresh_verify_code($('#code_verifyimg'),"change_email_verify");		
	});

	//修改邮件 -> 下一步
	$('#button_email,.button_email').click(function(){
		var bug = true;
		var pass = $("input[name='pass']").val();
		var new_email = $("input[name='new_email']").val();
		var icode = $("input[name='icode']").val();
		var re_pwd = regular_password;
		var re_email = regular_email;
		if(re_email.test(new_email)==false){
			bug = false;
			$('#err_eamil').css('display','block').text(v_e_input_email);
		}else{

			$('#err_eamil').css('display','none').text('');
		}

		if(re_pwd.test(pass)==false){
			bug = false;
			$('#err_pass').css('display','block').text(v_e_input_password);
		}else{
			$('#err_pass').css('display','none').text('');
		}
		if(icode.length!=4){
			bug = false;
			$('#err_icode').css('display','block').text(v_e_verify_code);
		}else{
			$('#err_icode').css('display','none').text('');
		}
		if(bug){

			$.ajax({

				url:base_module_url + '/Index/changeEmail.html?is_ajax=1',
				type:'post',
				data:{"email":new_email,"password":pass,"icode":icode},
				dataType:'json',
				success:function(result){
					redirct(result);
					if(result.verify_code!=''){
						bug = false;
						$('#err_icode').css('display','block').text(result.verify_code);
					}
					if(result.result!=''){
						bug = false;
						$('#err_pass').css('display','block').text(result.result);
					}
					if(bug){
						change_time();
						$('.mod_tip_bd').hide();
						$('.text_mail').text(new_email);
						$('.emailevrity').show();					
					}
					$("input[name='icode']").val('');
					refresh_verify_code($('#code_verifyimg'),"change_email_verify");					
				}
			})
		}
		$("#popUpdateemail").css("padding-bottom","21px");
	})

	// 修改邮箱  -> 邮件验证码提交
	$('#submit_code').click(function(){
		
		var verify_code= $("input[name='verify_code']").val();		
		$.ajax({
			url:base_module_url + '/Index/changeEmailVerify.html?is_ajax=1',
			type:'post',
			data:{'verify_code':verify_code},
			dataType:'json',
			success:function(result){
				var  status_r = result.status;
				switch (status_r){
					case 0:
						$('#err_set_email_code').css('display','block').text(v_e_change_failed);
						break;
					case 1:
						msg = v_change_success;
						$('.modal_container').hide();
						if($(window).width()<=480){

							$(".blockimportant").removeClass("hidewap");
						}
						$('.new_email,#Pass,.icode,.resendinput').val('');
						$('#err_set_email_code').css('display','none').text('');

						layer.alert(msg, {
							skin: 'layui-layer-molv' //样式类名
							,closeBtn: 0
						});
						break;
				}
			}
		});
	});

	//修改邮箱 ->重新获取验证码
	$('#setmail_verify-sendbtn').click(function(){
		$.ajax({
			url:base_module_url+"/Index/changeEmailSend.html?is_ajax=1",
			type:'get',
			dataType:'json',
			success:function(result){
				redirct(result);
				var  status_r = result.status;
				switch (status_r){
					case 0:
						$('#err_set_email_code').css('display','block').text(v_operating_fast);
						break;
					case 1:
						$('#err_set_email_code').css('display','none').text('');
						change_time();
						break;
					case 2:
						$('#err_set_email_code').css('display','block').text(v_e_send_failed);
						break;
					case 3:
						$('#err_set_email_code').css('display','block').text(v_e_not_login);
						break;
				}
			}
		});
	});

	//账户安全
	$(".account-security").click(function(){
		$(".security-level,.device_recommend").show();
		$(".uinfo").parents(".n-frame").hide();
		$(".n_sevice").parents(".n-frame").hide();
		$(".third_bindbox").parents(".n-frame").hide();
		$(".current").removeClass("current");
		$(this).addClass("current");		
	});

	//个人信息
	$(".personal-information").click(function(){
		$(".security-level,.device_recommend").hide();
		$(".uinfo").parents(".n-frame").show();
		$(".n_sevice").parents(".n-frame").hide();
		$(".third_bindbox").parents(".n-frame").hide();		
		$(".current").removeClass("current")
		$(this).addClass("current");
	});

	//授权
	$(".binding-authorization").click(function(){
		$(".security-level,.device_recommend").hide();
		$(".uinfo").parents(".n-frame").hide();
		$(".third_bindbox").parents(".n-frame").show();
		$(".n_sevice").parents(".n-frame").hide();
		$(".current").removeClass("current");
		$(this).addClass("current");		
	});

	//服务
	$(".cloud-service").click(function(){
		$(".security-level,.device_recommend").hide();
		$(".uinfo").parents(".n-frame").hide();
		$(".third_bindbox").parents(".n-frame").hide();
		$(".n_sevice").parents(".n-frame").show();
		$(".current").removeClass("current");
		$(this).addClass("current");
		//  $(".wrap").css({"position":"relative","top":"-118px"});
		$(".n_sevice").css({"position":"relative","top":"-10px"});
		$(".n_sevice").prev().css({"position":"relative","top":"-10px"});
	})

    //账号密码修改
	$("#btnUpdatePassword,#changePassword .wap-desc").click(function(e){
		e.stopPropagation();
		if($(this).hasClass("wap-desc")){
			$(this).parents(".blockimportant").addClass("hidewap");
			$(".btnCancel,.btn_mod_close span").css("display","inline-block");
		}
		$("#popUpdatePassword,.capt_box,.mod_tip_bd").show();
		$("#popUpdatePassword ").parents(".popup_mask").show();
		$(".newPass,.newPass2,.oldPass").css("border","1px solid #838383");
		$(".empty_pwd").hide();

	});


	//修改密码确认键
	$(".btnOK,.tip_btns_pass").click(function(){

		var bug = true;
		var oldPass = $("input[name='oldPass']").val();
		var new_pass = $("input[name='newPass']").val();
		var newPass2 = $("input[name='newPass2']").val();
		var pass_code = $("input[name='pass_code']").val();
		var re_pwd = regular_password;

		if(oldPass.length < 6){
			bug = false;
			$('#err_oldpass').css('display','block').text(v_e_input_password);
		}else{
			$('#err_oldpass').css('display','none').text('');
		}
		if(re_pwd.test(new_pass) == false){
			bug = false;
			$('#err_new_pass').css('display','block').text(v_password_tips);
		} else if(new_pass!=newPass2){
			bug = false;
			$('#err_new_pass').css('display','block').text(v_e_password_not_same);
		}else{
			$('#err_new_pass').css('display','none').text('');
		}
		if(pass_code.length!=4){
			bug = false;
			$('#err_pass_code').css('display','block').text(v_e_input_verify_code);
		}else{
			$('#err_pass_code').css('display','none').text('');
		}
		if(bug){
			var msg = "";
			$.ajax({
				type:'post',
				url:base_module_url+'/Index/changePassword.html?is_ajax=1',
				data:{'old_password':oldPass,'new_password':new_pass,'confirm_password':newPass2,'pass_code':pass_code},
				//返回数据根据结果进行相应的处理
				dataType:'json',
				success : function(result) {
					redirct(result);
					if(result.verify_code !='' ){
						bug = false;
						$('#err_pass_code').css('display','block').text(result.verify_code);
					}
					if(result.result !='' ){
						bug = false;
						$('#err_oldpass').css('display','block').text(result.result);

					}
					if(bug){
						$("#popUpdatePasswordSuccess").show();
						$("#popUpdatePasswordSuccess").parents(".popup_mask").show();
						$("#popUpdatePassword").hide();
						$("#popUpdatePassword").parents(".popup_mask").show();
						$('#popUpdatePasswordSuccess').show();
						var timeCount=3;
						var timeTask = setInterval(function(){
							timeCount--;
							if(timeCount==0){
								$('#popUpdatePasswordSuccess,.popup_mask').hide();
								$('.oldPass,.newPass,.newPass2,.verification-code').val('');
								$(".blockimportant").removeClass("hidewap");							
								clearInterval(timeTask);
							}else{
								$('#popUpdatePasswordSuccess .logoutCountdown').attr("disabled", true).text( timeCount);
							}
						},400);
					}
					$("input[name='pass_code']").val('');
					refresh_verify_code($('#verifyimg'),"change_password_verify");					
				}

			});
		}
	});

	$('#clear,#btn_close').click(function(){
		clear_time();
		$('#popUpdatePasswordSuccess,.popup_mask').hide();
		$('.oldPass,.newPass,.newPass2,.verification-code').val('');
	});

	//邮箱提示的点击事件
	$("#btnUpdateEmail,.btnBindMobile,.btnChangeMobile,#popManageTokenHome,#changeEmail .wap-desc").click(function(){
		$("#verify-mod-list,#verify-mode-list-email,.modal_tip,.modal_container,.mod_tip_bd").show();
		$("#verify-mod-sendTicketTip,#popManageTokenHome,#verify-mod-container,.emailevrity").hide();
		$("#verify-mode-list-email").siblings().hide();
		if($(this).hasClass("wap-desc")){
			$(this).parents(".blockimportant").addClass("hidewap");
			$(".btnCancel,#button_email").hide();
		}
	});

	//radio
	$("#verify-mode-list-email").click(function(){
		$(this).addClass("now");
	});

	//点击resendinput1事件
	$(".resendinput1").focus(function(){
		$(".verify-error-con").parents(".err_tip").hide();
	});

	//点击resendinput事件
	$(".resendinput").focus(function(){
		$(".verify-error-con").parents(".err_tip").hide();
		$(".verify-error-con").text("");
		$(".resendinput").css("border", "1px solid #e8e8e8");
	});

	//取消与关闭
	$(".btn_mod_close,.btn_back,.btn_tip_btnCancel").click(function(){
		$("#popUpdatePassword,.modal_tip,.modal_container,#popManageTokenHome,#verify-mod-containe,.empty_capt_none,.wng_pwd,.wng_capt,#err_pass,#set_head").hide();
		$("#popUpdatePassword").parents(".popup_mask").hide();
		$("#popManageTokenHome").parents(".popup_mask").hide();
		$(".layereditinfo").parents(".popup_mask").hide();
		$("#popSwitchRegion").parents(".mod_wrap,.popup_mask").hide();
		$("#popSwitchRegion").find(".mod_tip_bd").eq(0).show();
		$("#popSwitchRegion").find(".mod_tip_bd").eq(1).hide();
		$("#verify-mode-list-email").removeClass("now");
		$(".active").removeClass("active");
		$(".verify-error-con").text("");
		clear_time();
		$(".resendinput,.oldPass,.newPass,.newPass2,.verification-code,.new_email,.pass,.icode").val("");
		$(".verify-error-con").parents(".err_tip").hide();
		$(".resendinput,.verification-code").css("border", "1px solid #e8e8e8");
		$(".grpNewPass input").css("border", "1px solid #666");
		$(".hidewap").removeClass("hidewap");
		$(".layeruploadface").parents(".popup_mask").hide();
		$("#err_phone，#button_email").hide();
		
	});

	//修改密码关闭和
	$("#popUpdatePasswordSuccess .btn_mod_close,.btnReturn").click(function(){
		
		$("#popUpdatePasswordSuccess").hide();
		$("#popUpdatePasswordSuccess").parents(".popup_mask").hide();
		window.location.href= base_module_url + "/Index/login.html";
	});

	//开启功能
	$("#btnManageToken").click(function(){
		$("#popManageTokenHome").show();
		$("#popManageTokenHome").parents(".popup_mask").show();
	});

	//编辑功能
	$("#editInfo,#editInfoWap").click(function(){
		$(".layereditinfo").parents(".mod_wrap,.popup_mask").show();
		$(".layereditinfo,.mod_acc_tip,.mod_tip_bd").show();
		$(".layereditinfo .btn_commom").css({"background":"#4185da","border":"1px solid #4185da"});
		$("#changePassword").parents(".blockimportant").addClass("hidewap");
		$(".btnCancel").css("display","inline-block");
	});

	//个人信息页面修改点击事件
	$("#switchRegion").click(function(){
		
		$("#popSwitchRegion").parents(".mod_wrap,.popup_mask").show();
		$("#continueSwitch").css({"background":"#4185da","border":"1px solid #4185da"});
	});

	//继续切换点击事件
	$("#continueSwitch").click(function(){
		$("#popSwitchRegion").find(".mod_tip_bd").eq(0).hide();
		$("#popSwitchRegion").find(".mod_tip_bd").eq(1).show()
		$("#popSwitchRegion").parents(".mod_wrap,.popup_mask").show();
		$("#doRegionSwitch").css({"background":"#4185da","border":"1px solid #4185da"});
	});

	//确定
	$("#doRegionSwitch").click(function(){
		$(this).parents(".mod_wrap,.popup_mask").hide()
		$("#region").text($(".change_region_box tt").text())
	});

	//切换各地账户的点击事件
	$(".listtit").click(function(e){
		e.stopPropagation();
		$(this).siblings().find(".country-container").toggle();
	});

	//各地值的内容切换
	$(".listtit").siblings().find(".list li").click(function(){
		$(".listtit tt").text($(this).find(".record-country").text()) ;
	});

	//点击基础知识事件
	$(document).on("click",".faqBasis1",function(){
		$(".faqList-faqBasis,.faqBasis").show();
		$(".faqBasis").siblings().hide();
		$(".faqList").hide();
		var html='&nbsp;&nbsp;&gt;<span class="m_func">'+v_basics+'</span>'
		$(".left_name").append(html);
		$(".left_name .m_func").eq(0).css("color","#333");
	});

	//点击登录账号事件
	$(".faqLogin1 .faq-question,.faqLogin .faq-question").click(function(){
		var i,j;
		var len=$(this).index();
		$(".faqList-faqBasis,.faqLogin").show();
		$(".faqList").hide();
		$(".faqLogin").siblings().hide();
		if($(".left_name .m_func").length<=1){
			var html='&nbsp;&nbsp;&gt;<span class="m_func">'+v_login_account+'</span>'
			$(".left_name").append(html);
		}
		$(".left_name .m_func").eq(0).css("color","#333");
		$(".faqLogin .faq_reson_detail .faq-question").eq(len).addClass("current");
		$(".faqLogin .faq_reson_detail .faq-question ").eq(len).siblings().removeClass("current");
		//之前的内容隐藏
		for(i=0;i<=len-1;i++){
			$(".faqLogin dd").eq(i).hide();
		}
		//之后的内容隐藏
		for(j=len+1;j<$(".faqLogin1 a").length;j++){
			$(".faqLogin dd").eq(j).hide();
		}
		$(".faqLogin dd").eq(len).show();
	});

	//点击注册帐号事件
	$(".faqRegisterBind1 .faq-question,.faqRegisterBind .faq_reson_detail").click(function(){
		var i,j;
		var len=$(this).index();
		$(".faqList-faqBasis,.faqRegisterBind").show();
		$(".faqList").hide();
		$(".faqRegisterBind").siblings().hide();
		if($(".left_name .m_func").length<=1){
			var html='&nbsp;&nbsp;&gt;<span class="m_func">'+v_register_account+'</span>'
			$(".left_name").append(html);
		}
		$(".left_name .m_func").eq(0).css("color","#333");
		$(".faqRegisterBind .faq_reson_detail").eq(len).find(".faq-question").addClass("current");
		$(".faqRegisterBind .faq_reson_detail").eq(len).siblings().find(".faq-question").removeClass("current");
		//之前的内容隐藏
		for(i=0;i<=len-1;i++){
			$(".faqRegisterBind dd").eq(i).hide();
		}
		//之后的内容隐藏
		for(j=len+1;j<$(".faqRegisterBind1 a").length;j++){
			$(".faqRegisterBind dd").eq(j).hide();
		}
		$(".faqRegisterBind dd").eq(len).show();
	});

	//点击密保设置事件
	$(".faqSecret1 .faq-question,.faqSecret .faq_reson_detail").click(function(){
		var i,j;
		var len=$(this).index();
		$(".faqList-faqBasis,.faqSecret").show();
		$(".faqList").hide();
		$(".faqSecret").siblings().hide();
		if($(".left_name .m_func").length<=1){
			var html='&nbsp;&nbsp;&gt;<span class="m_func">'+v_secret_set+'</span>'
			$(".left_name").append(html);
		}
		$(".left_name .m_func").eq(0).css("color","#333");
		$(".faqSecret .faq_reson_detail").eq(len).find(".faq-question").addClass("current");
		$(".faqSecret .faq_reson_detail").eq(len).siblings().find(".faq-question").removeClass("current");
		//之前的内容隐藏
		for(i=0;i<=len-1;i++){
			$(".faqSecret dd").eq(i).hide();
		}
		//之后的内容隐藏
		for(j=len+1;j<$(".faqSecret a").length;j++){
			$(".faqSecret dd").eq(j).hide();
		}
		$(".faqSecret dd").eq(len).show();
	});

	//点击删除账号事件
	$(".faqDelAccount1 .faq-question,.faqDelAccount .faq_reson_detail").click(function(){
		var i,j;
		var len=$(this).index();
		$(".faqList-faqBasis,.faqDelAccount").show();
		$(".faqList").hide();
		$(".faqDelAccount").siblings().hide();
		if($(".left_name .m_func").length<=1){
			var html='&nbsp;&nbsp;&gt;<span class="m_func">'+v_delete_account+'</span>'
			$(".left_name").append(html);
		}
		$(".left_name .m_func").eq(0).css("color","#333");
		$(".faqDelAccount .faq_reson_detail").eq(len).find(".faq-question").addClass("current");
		$(".faqDelAccount .faq_reson_detail").eq(len).siblings().find(".faq-question").removeClass("current");
		//之前的内容隐藏
		for(i=0;i<=len-1;i++){
			$(".faqDelAccount dd").eq(i).hide();
		}
		//之后的内容隐藏
		for(j=len+1;j<$(".faqSecret a").length;j++){
			$(".faqDelAccount dd").eq(j).hide();
		}
		$(".faqDelAccount dd").eq(len).show();
	});

	//点击云服务事件
	$(".faqServicePhone1 .faq-question,.faqServicePhone .faq_reson_detail").click(function(){
		var i,j;
		var len=$(this).index();
		$(".faqList-faqBasis,.faqServicePhone").show();
		$(".faqList").hide();
		$(".faqServicePhone").siblings().hide();
		if($(".left_name .m_func").length<=1){
			var html='&nbsp;&nbsp;&gt;<span class="m_func">'+v_delete_account+'</span>'
			$(".left_name").append(html);
		}
		$(".left_name .m_func").eq(0).css("color","#333");
		$(".faqServicePhone .faq_reson_detail").eq(len).find(".faq-question").addClass("current");
		$(".faqServicePhone .faq_reson_detail").eq(len).siblings().find(".faq-question").removeClass("current");
		//之前的内容隐藏
		for(i=0;i<=len-1;i++){
			$(".faqServicePhone dd").eq(i).hide();
		}
		//之后的内容隐藏
		for(j=len+1;j<$(".faqSecret a").length;j++){
			$(".faqServicePhone dd").eq(j).hide();
		}
		$(".faqServicePhone dd").eq(len).show();
	});

	//第三方绑定事件
	$(".faqSns1 .faq-question,.faqSns .faq_reson_detail").click(function(){
		var i,j;
		var len=$(this).index();
		$(".faqList-faqBasis,.faqSns").show();
		$(".faqList").hide();
		$(".faqSns").siblings().hide();
		if($(".left_name .m_func").length<=1){
			var html='&nbsp;&nbsp;&gt;<span class="m_func">'+v_delete_account+'</span>'
			$(".left_name").append(html);
		}
		$(".left_name .m_func").eq(0).css("color","#333");
		$(".faqSns .faq_reson_detail").eq(len).find(".faq-question").addClass("current");
		$(".faqSns .faq_reson_detail").eq(len).siblings().find(".faq-question").removeClass("current");
		//之前的内容隐藏
		for(i=0;i<=len-1;i++){
			$(".faqSns dd").eq(i).hide();
		}
		//之后的内容隐藏
		for(j=len+1;j<$(".faqSecret a").length;j++){
			$(".faqSns dd").eq(j).hide();
		}
		$(".faqSns dd").eq(len).show();
	});

	//注册账号的链接点击事件
	$(".faqRegisterBind .action_link,.faqServicePhone .action_link_active1").click(function(){
		$(".faqList-faqBasis,.faqSecret").show();
		$(".faqList").hide();
		$(".faqSecret").siblings().hide();
		$(".left_name .m_func").eq(1).text(v_secret_set)
		$(".left_name .m_func").eq(0).css("color","#333");
		$(".faqSecret .faq_reson_detail").eq(0).find(".faq-question").addClass("current");
		$(".faqSecret .faq_reson_detail").eq(0).siblings().find(".faq-question").removeClass("current");
		$(".faqSecret dd").eq(0).show();
		$(".faqSecret dd").eq(0).siblings().hide();
		$(".faqSecret dt").show();
	});

	//密保设置链接点击事件
	$(".faqSecret .action_link_active").click(function(){
		$(".faqList-faqBasis,.faqRegisterBind").show();
		$(".faqList").hide();
		$(".faqRegisterBind").siblings().hide();
		$(".left_name .m_func").eq(1).text(v_register_account)
		$(".left_name .m_func").eq(0).css("color","#333");
		$(".faqRegisterBind .faq_reson_detail").eq(0).find(".faq-question").addClass("current");
		$(".faqRegisterBind .faq_reson_detail").eq(0).siblings().find(".faq-question").removeClass("current");
		$(".faqRegisterBind dd").eq(0).show();
		$(".faqRegisterBind dd").eq(0).siblings().hide();
		$(".faqRegisterBind dt").show();
	});

	$(".faqSecret .action_link_active1").click(function(){
		$(".faqList-faqBasis,.faqRegisterBind").show();
		$(".faqList").hide();
		$(".faqRegisterBind").siblings().hide();
		$(".left_name .m_func").eq(1).text(v_register_account)
		$(".left_name .m_func").eq(0).css("color","#333");
		$(".faqRegisterBind .faq_reson_detail").eq(2).find(".faq-question").addClass("current");
		$(".faqRegisterBind .faq_reson_detail").eq(2).siblings().find(".faq-question").removeClass("current");
		$(".faqRegisterBind dd").eq(2).show();
		$(".faqRegisterBind dd").eq(2).siblings().hide();
		$(".faqRegisterBind dt").show();
	});

	//申请人工审核链接点击事件
	$(".faqSecret .action_link_active2").click(function(){
		$(".top,.suc_content,.footer").hide();
		$(".wrapper,#main_container .suc_content,.n-footer").show();
	});

	//修改基础资料验证
	$('#set_userinfo,.set_userinfo').click(function(){
		var bug = true;
		var username = $("input[name='username']").val();
		var nickname = $("input[name='nickname']").val();
		var uptime = $("input[name='uptime']").val();
		var in_sex = $("input[name='sex']");
		var address = $("input[name='address']").val();
		var phone = $("input[name='phone']").val();
		var re_userName = regular_user_name;
		var re_phone = regular_phone;
		if(re_userName.test(username)==false){
			$bug = false;
			$('#err_username').css({'display':'block'}).text(v_username_tips);
		}else{
			$('#err_username').css('display','none').text('');
		}
		if(re_phone.test(phone)==false){
			$bug = false;
			$('#err_phone').css('display','block').text(v_e_inout_phone);
		}else{
			$('#err_phone').css('display','none').text('');
		}

		for(var i=0;i<in_sex.length;i++){
			if(in_sex[i].checked){
				sex = in_sex[i].value;
			}
		}
		if(bug){
			$.ajax({

				url:base_module_url + '/Index/editInfo.html?is_ajax=1',
				type:'post',
				data:{'user_name':username,'nick_name':nickname,'birthday':uptime,'gander':sex,'address':address,'tel':phone},
				dataType:'json',
				success:function(data){
					if(data.user_name !=''){
						$('#user_name_val').text(data.user_name);
					}
					if(data.nick_name !=''){
						$('#nick_name_val').text(data.nick_name);
					}					
					switch (data.gander){
						case '0':
							$('#gender_val').text(v_girl);
							break;
						case '1':
							$('#gender_val').text(v_boy);
							break;
						case '2':
							$('#gender_val').text(v_secrecy);
							break;
					}
					if(data.birthday !=''){

						$('#birthday_val').text(data.birthday);
					}
					if(data.tel !=''){

						$('#phone_val').text(data.tel);
					}
					if(data.address !=''){
						$('#address_val').text(data.address);
					}

					var  status = data.status;
					switch (status){
						case 0:
							var error_array = data.error_array;
							if(error_array.birthday!=''){
								$('#err_uptime').css('display','block').text(error_array.birthday);
							}
							if(error_array.nick_name!=''){
								$('#err_nickname').css('display','block').text(error_array.nick_name);
							}
							if(error_array.tel!=''){
								$('#err_phone').css('display','block').text(error_array.tel);
							}
							if(error_array.user_name!=''){
								$('#err_username').css('display','block').text(error_array.user_name);
							}
							break;
						case 1:
							$('#err_uptime,#err_nickname,#err_phone,#err_username').css('display','none').text('');
							msg = v_change_success;
							$('.popup_mask').hide();
							layer.alert(msg, {
								skin: 'layui-layer-molv' //样式类名
								,closeBtn: 0
							});
							break;
					}
				}
			})
		}
		if($(window).width()<=480){
			$(".layereditinfo").hide();
			$("#changePassword").parents(".blockimportant").removeClass("hidewap");
		}
	})


	//适应手机
	if($(window).width()<=480) {

		$(".device-detail-area-active .wap-desc").text("");		
		$(".logout_wap_btnadpt .btnadpt").text(v_Logout)
	}

	$(".device-detail-area-active .font-img-item:first").click(function(){
		$(".title_security_wap,.device-detail-area,.dis_none_pc,.n-account-area-box,.personal-information .logout_wap,.logout_wap_active,.title-line").hide();
		$(".personal-information").show();
		$(".framedatabox .fdata").eq(0).find("a").hide();
	});
	$(".layui-layer-btn0").click(function(){
		$(".layui-layer-shade").hide();
	})
	
  if(window.orientation==90||window.orientation==-90){
	  $(".uinfo").css("font-sze","12px");
	  $(".fdata").css("line-height","38px");
	  $("#err_set_email_code").css("margin-left","20%");
  }
});