var device_id = 0;
var mac_value;
var device_name;			
var last_element;
var current_element;
var last_device_id = 0;
var default_device_id = 0;//当前设备
var device_total_count = 0;//设备总数
var device_current_page = 1;//当前设备页数
var location_log_total_count = 0;//定位日志总数
var location_log_current_page = 0;//当前定位日志页数
var cmd_log_total_count = 0;//指令日志总数
var cmd_log_current_page = 0;//当前指令日志页数
var log_type = 0;//0为定位日志 1为指令日志
var refresh_int;
var refresh_m_time = 10*1000;//毫秒数
var default_refresh_count = 20;//刷新的次数
var current_refresh_count = 0;//当前刷新的次数
var regular_lockpassword = /^[0-9]{4}$/;

//设备改变切换最后位置地图
function change_map(){
	
	$("#map_frame").attr("src",base_module_url + "/Phone/map.html?id="+default_device_id);
}

//定位定时刷新地图
function map_setinterval_refresh(){
	
	if(current_refresh_count <= default_refresh_count){
		
		refresh_int = window.setInterval("map_refresh()",refresh_m_time);
	}
}

//地图刷新
function map_refresh(){
	
	if(current_refresh_count <= default_refresh_count){
		
		current_refresh_count += 1;
		$("#map_frame").attr("src",base_module_url + "/Phone/map.html?id="+ default_device_id +"&tmp=" + (new Date()).valueOf().toString());
	}else{
		
		map_clear_refresh();
	}	
}

//清除刷新
function map_clear_refresh(){
	
	if(refresh_int != undefined && refresh_int > 0){
		
		refresh_int = window.clearInterval(refresh_int);
		current_refresh_count = 0;
	}	
}

//获取设备列表
function ajax_device_list(check_first){
	
	var next_page = 1;
	var check_ajax = false;
	if(check_first){
		
		check_ajax = true;
		device_current_page = 0;
		next_page = 1;
	}else if(device_current_page * page_size < device_total_count){
		
		check_ajax = true;
		next_page = device_current_page + 1;
	}
	$(".select-device").append('<li id="device_loading"> '+ v_loading +' </li>');
	if(check_ajax){
		
		$.ajax({
			url:base_module_url+'/Phone/getDeviceList.html?is_ajax=1',
			data:{"id":default_device_id,"p":next_page},
			type:'POST',
			dataType:'JSON',
			success:function(data){	
												
				redirct(data);
				$("#device_loading").remove();		
				if(data.status == 1){
					
					var device_id_tmp;
					var check_selected = false;
					var str_html = "";
					var list_array = data.list;
					
					device_current_page = next_page;
					device_total_count = data.total_count;					
					display_more("");
					
					for(var key in list_array){
						
						value = list_array[key];
						device_id_tmp = value['deviceid'];
						check_selected = device_id_tmp == default_device_id ? true : false;
						str_html += '<li deviceid="'+ device_id_tmp +'" '+(check_selected ? ' class="current" ': '')+'>' +
										'<a href="javascript:void(0);" class="radiobox '+(check_selected ? 'clicked-radiobox' : '')+'"> <em></em></a>'+
										'<div class="sd-device">' +
											'<p class="sd-name"><span class="device_name_label">'+ v_device_name +': </span><span class="device_name_value">'+ value['name'] +'</span></p>'+
											'<p class="sd-num"><span class="mac_label">Mac: </span><span class="mac_value">'+ value['mac'] +'</span></p>' +							
										'</div>' +						
								    '</li>';						
					}
					if(check_first){
						
						$(".select-device").html(str_html);
					}else{
						
						$(".select-device").append(str_html);
					}
					str_html = $(".select-device").html();
					if(str_html.length > 0){
						
						$("#M-Gen-dialog-114 .no_data").hide();
					}else{
						
						$("#M-Gen-dialog-114 .no_data").show();
					}
				}else{
					
					layer.msg(data.result,{icon:2,skin: 'layer-ext-moon'});
				}
			}
		});
	}
}

//异步获取定位日志
function ajax_location_log(check_first){
	
	var next_page = 1;
	var check_ajax = false;
	if(check_first){
		
		check_ajax = true;
		location_log_current_page = 0;
		next_page = 1;
		$(".location_log_list ul").html("");
		$(".location_log_list .no_data").hide();
	}else if(location_log_current_page * page_size < location_log_total_count){
		
		check_ajax = true;
		next_page = location_log_current_page + 1;
	}
	$(".location_log_list ul").append('<li id="location_log_loading"> '+ v_loading +' </li>');
	if(check_ajax){
		
		$.ajax({
			url:base_module_url+'/Phone/getLocationLog.html?is_ajax=1',
			data:{"id":default_device_id,"p":next_page},
			type:'POST',
			dataType:'JSON',
			success:function(data){	
												
				redirct(data);
				$("#location_log_loading").remove();
				if(data.status == 1){
					
					var create_time;
					var str_html = "";
					var local_name = "";
					var list_array = data.list;
					
					location_log_current_page = next_page;
					location_log_total_count = data.total_count;
					display_more("log");
					
					for(var key in list_array){
						
						value = list_array[key];						
						create_time = get_server2local_time(value['createtime']);
						local_name = value['countryflag'] == 1 ? value['cname'] : value['ename'];  
						str_html += '<li>'+
										'<p class="pm-p">'+local_name+'</p>'+
										'<p class="pm-p-little" data-index="0">'+
										'<span>'+create_time+' </span> '+ 
										'<span class="pm-span-phonenumber"> ( '+v_longitude+': '+value['longitude']+','+v_latitude+': '+value['latitude']+' )</span>'+
										'</p>'+
									'</li>';											
					}
					
					$(".location_log_list ul").append(str_html);
					str_html = $(".location_log_list ul").html();
					if(str_html.length > 0){
						
						$(".location_log_list .no_data").hide();
					}else{
						
						$(".location_log_list .no_data").show();
					}
				}else{
					
					layer.msg(data.result,{icon:2,skin: 'layer-ext-moon'});
				}			
			}	
		});
	}
	
}

//异步获取指令日志
function ajax_cmd_log(check_first){
	
	var next_page = 1;
	var check_ajax = false;
	if(check_first){
		
		check_ajax = true;
		cmd_log_current_page = 0;
		next_page = 1;
		$(".cmd_log_list ul").html("");
		$(".cmd_log_list .no_data").hide();
	}else if(cmd_log_current_page * page_size < cmd_log_total_count){
		
		check_ajax = true;
		next_page = cmd_log_current_page + 1;
	}
	$(".cmd_log_list ul").append('<li id="cmd_log_loading"> '+ v_loading +' </li>');
	if(check_ajax){
	
		$.ajax({
				url:base_module_url+'/Phone/getCmdLog.html?is_ajax=1',
				data:{"id":default_device_id},
				type:'POST',
				dataType:'JSON',
				success:function(data){	
													
					redirct(data);
					$("#cmd_log_loading").remove();
					if(data.status == 1){
												
						var cmd_name;
						var exec_time;
						var exec_status;
						var create_time;						
						var exec_status_str;
						var str_html = "";						
						var list_array = data.list;
						
						cmd_log_current_page = next_page;
						cmd_log_total_count = data.total_count;
						display_more("log");
						
						for(var key in list_array){
						
							value = list_array[key];
							exec_status = value['status'];
							create_time = get_server2local_time(value['createtime']);
							if(exec_status == 1){
								
								exec_status_str = v_success;
								exec_time = '<span class="pm-span-phonenumber"> '+v_exec_time+': '+ get_server2local_time(value['executiontime']) +'</span>'
							}else{								
		
								exec_status_str = v_unsuccess;
								exec_time = "";								
							}
							
							str_html += '<li>'+
										'<p class="pm-p">'+value['cmd_name']+' ( '+ exec_status_str +' )</p>'+
										'<p class="pm-p">'+v_target+': '+value['device_name']+' (Mac: '+ value['mac'] +')</p>'+
										'<p class="pm-p-little" data-index="0">'+
										'<span>'+v_add_time+': '+create_time+' </span> '+ exec_time + 									
										'</p>'+
									'</li>';						
						}
						$(".cmd_log_list ul").append(str_html);
						str_html = $(".cmd_log_list ul").html();						
						if(str_html.length > 0){
							
							$(".cmd_log_list .no_data").hide();
						}else{
							
							$(".cmd_log_list .no_data").show();
						}
					}else{
						
						layer.msg(data.result,{icon:2,skin: 'layer-ext-moon'});
					}			
				}	
			});
	}
}

//异步清除数据
function ajax_clearAll(log_type){
	
	layer.confirm(v_tip_delete,
		{btn: [v_determine,v_cancel]},
		function(index){
			
			layer.close(index);
			$.ajax({
				url:base_module_url+'/Phone/clearAll.html?is_ajax=1',
				data:{"id":default_device_id,"t":log_type},
				type:'POST',
				dataType:'JSON',
				success:function(data){	
													
					redirct(data);
					if(data.status == 1){
						
						var element_name_tmp = "";						
						element_name_tmp = log_type == 0 ? "location_log_list" : "cmd_log_list";
						$("."+ element_name_tmp +" ul").html("");
						$("."+ element_name_tmp +" .no_data").show();
						
						$("#log_display_more").hide();
						
						layer.msg(v_clear_success,{icon:1,skin: 'layer-ext-moon'});
					}else{
						
						layer.msg(data.result,{icon:2,skin: 'layer-ext-moon'});
					}		
				}	
			});
		},
		function(index){
			layer.close(index)
			}
	);
}

//异步发送指令
function ajax_cmd(cmd_type_id,content){

	$.ajax({
		url:base_module_url+'/Phone/cmdSend.html?is_ajax=1',
		data:{"id":default_device_id,"ctid":cmd_type_id,"content":content},
		type:'POST',
		dataType:'JSON',
		success:function(data){						
			redirct(data);
			var icon = 2;
			var msg_str = "";
			if(data.status == 1){
				
				if(data.push){
					
					icon = 1;
					msg_str = v_operation_success;
					if(cmd_type_id == 1){//定位指令发送成功
					
						map_setinterval_refresh();
					}
				}else{
					
					msg_str = v_cmd_faild;
				}				
			}else{
				
				icon = 2;
				msg_str = data.result;
			}		
			layer.msg(msg_str,{icon:icon,skin: 'layer-ext-moon'});
		}	
	});
}

//显示更多
function display_more(type){
		
	var total_count;
	var current_page;
	var display_more_name;
	var refresh_name;
	
	$(".loading").hide();	
	if(type == "log"){
		
		if(log_type == 0){
			
			total_count = location_log_total_count;
			current_page = location_log_current_page;					
		}else{
			
			total_count = cmd_log_total_count;
			current_page = cmd_log_current_page;			
		}
		
		refresh_name = "#refresh_log";
		display_more_name = "#log_display_more";		
	}else{
		
		total_count = device_total_count;
		current_page = device_current_page;
		refresh_name = "#refresh_device";
		display_more_name = "#device_display_more";		
	}
	
	$(refresh_name).removeClass("disabled");
	if(current_page*page_size < total_count){
		
		$(display_more_name).show();
	}else{		
		
		$(display_more_name).hide();
	}
}

$(function(){
	
    if($(window).width()<=480){
        $(".vh-avator-area ").css("right","10px");
        $(".gl-lang-ul").css("z-index","100");
    }
	
	display_more("");
	
	//选择设备
	$(".select-device li").live("click",function(){
				
		last_element = $(".select-device").find(".current");
		last_device_id = last_element.attr("deviceid");
		current_element = $(this);
		device_id = current_element.attr("deviceid");
		if(last_device_id != device_id){
			
			last_element.removeClass("current");
			last_element.find(".radiobox").removeClass("clicked-radiobox");
			current_element.addClass("current");
			current_element.find(".radiobox").addClass("clicked-radiobox");
		}
		if(default_device_id != device_id){
		
			$("#M-Gen-button-113").removeClass("disabled");
		}else{
		
			$("#M-Gen-button-113").addClass("disabled");
		}					
	});
	
	//确定选择设备
	$("#M-Gen-button-113").click(function(){
				
		if(device_id > 0 && default_device_id != device_id){
			
			default_device_id = device_id;
			change_map();			
			mac_value = current_element.find(".mac_value").html();
			device_name = current_element.find(".device_name_value").html();					
			$(".find-phone-container .mac_value").html(mac_value);
			$(".find-phone-container .device_name_value").html(device_name);
			$("#M-Gen-dialog-105 .m-p-head-ct").html(" " + device_name +" (Mac: "+ mac_value +")");
			$(".find-phone-container .device_name_value").html(device_name);
			$('#M-Gen-dialog-114,.gray_box').hide();
			$("#M-Gen-button-113").addClass("disabled");
			map_clear_refresh();
		}	
	});
	
	//取消选择设备
	$("#M-Gen-button-112").click(function(){
		
		if(last_element != undefined){
			
			last_element.addClass("current");
			last_element.find(".radiobox").addClass("clicked-radiobox");
		}
		if(current_element != undefined){
			
			current_element.removeClass("current");
			current_element.find(".radiobox").removeClass("clicked-radiobox");
		}
		$(".device_display_more .loading").hide();
		$("#refresh_device").removeClass("disabled");
		$('#M-Gen-dialog-114,.gray_box').hide();
	});
	
	$("#device_display_more").click(function(){
		
		$(this).hide();
		$(".device_display_more .loading").show();
		ajax_device_list(false);		
	});
	
	$("#refresh_device").click(function(){
		
		if($(".device_display_more .loading").css("display") == "none"){
			
			$(this).addClass("disabled");
			$("#device_display_more").hide();
			$(".device_display_more .loading").show();
			ajax_device_list(true);
		}				
	});
    
	//打开日志
    $('#phone_view_log').click(function(){
		
		log_type = 0;		
		$(".location_log_label a").css("color","#ce290b");
		$(".cmd_log_label a").css("color","#000");
		$(".location_log_list").show();
		$(".cmd_log_list").hide();
        $('.phone-viewLog-padd,.gray_box').show();	
		
		if(default_device_id != $("#log_list").attr("data-id")){
			
			ajax_cmd_log(true);
			ajax_location_log(true);
			$("#log_list").attr("data-id",default_device_id);
		}		
    });
	
	$('.location_log_label').click(function(){
		
		log_type = 0;
		$(".location_log_label a").css("color","#ce290b");
		$(".cmd_log_label a").css("color","#000");
		$(".location_log_list").show();
		$(".cmd_log_list").hide();
        $('.phone-viewLog-padd,.gray_box').show();
		display_more("log");
    });
	
	$('.cmd_log_label').click(function(){
		
		log_type = 1;
		$(".location_log_label a").css("color","#000");
		$(".cmd_log_label a").css("color","#ce290b");
		$(".location_log_list").hide();
		$(".cmd_log_list").show();
        $('.phone-viewLog-padd,.gray_box').show();
		display_more("log");
    });
	
	$(".location_log_label,.cmd_log_label").mouseover(function(){
		
		$(this).find("a").css("color","#ce290b");
	});
	
	$(".location_log_label,.cmd_log_label").mouseout(function(){
		
		var element_name;		
		if(log_type == 0){
			
			element_name = "cmd_log_label";
		}else{
			element_name = "location_log_label";
		}
		$("." + element_name+" a").css("color","#000");		
	});
	
	$("#refresh_log").click(function(){
		
		if($(".log_display_more .loading").css("display") == "none"){
			
			$(this).addClass("disabled");
			$("#log_display_more").hide();
			$(".log_display_more .loading").show();
			if(log_type == 0){
				
				ajax_location_log(true);
			}else{				
				
				ajax_cmd_log(true);
			}			
		}		
	});
	
	$("#log_display_more").click(function(){
		
		$("#log_display_more").hide();
		$(".log_display_more .loading").show();
		if(log_type == 0){
			
			ajax_location_log(false);
		}else{				
			
			ajax_cmd_log(false);
		}
	});
	
	//取消
	$("#M-Gen-button-106 .btn-ct,#M-Gen-button-104 .btn-ct,#M-Gen-button-103 .btn-ct").click(function(){
		
		$("#M-Gen-dialog-105,#M-Gen-dialog-106,#M-Gen-dialog-109,.gray_box").hide();
	});	
	
	//关闭
    $('.close_btn').click(function(){		
		
		$('#M-Gen-dialog-111,#M-Gen-dialog-114,#M-Gen-dialog-106,#M-Gen-dialog-108,#M-Gen-dialog-109,.phone-viewLog-padd,.gray_box').hide();
		
    });

    $('.icobtn-voice').click(function(){

        $('#M-Gen-dialog-108,.gray_box').show();
    });

    //定位
    $('#M-Gen-button-104').click(function(){

        $('#M-Gen-dialog-109,.gray_box').show();
    });	
	
	//定位指令发送
	$("#M-Gen-button-107").click(function(){
		
		map_clear_refresh();
		ajax_cmd(1,"");
		$("#M-Gen-dialog-109,.gray_box").hide();
	});

    //锁定
 /*   $('#M-Gen-button-101').click(function(){
        $('#M-Gen-dialog-106,.gray_box').show();
    });*/
    
    //锁屏
    $('#M-Gen-button-101').click(function(){
        ajax_cmd(3,"");
    });
    //闹铃
    $('#M-Gen-button-102').click(function(){
        ajax_cmd(2,"");
    });
	
	//锁屏发送
/*	$("#M-Gen-button-118").click(function(){
		
		var passwd = $("#M-Gen-dialog-106 input").val();
		if(regular_lockpassword.test(passwd) == false){
			
			layer.msg(v_e_input_password,{icon:2,skin: 'layer-ext-moon'});
		}else{
			
			ajax_cmd(3,passwd);
			$("#M-Gen-dialog-106 input").val("");
			$("#M-Gen-button-118").addClass("disabled");
			$('#M-Gen-dialog-106,.gray_box').hide();
		}
	});
	
	//输入后
	$("#M-Gen-dialog-106 input").blur(function(){
		
		var passwd = $("#M-Gen-dialog-106 input").val();		
		if(regular_lockpassword.test(passwd)){
			
			$("#M-Gen-button-118").removeClass("disabled");
		}else{
			
			$("#M-Gen-button-118").addClass("disabled");
			layer.msg(v_e_input_password,{icon:2,skin: 'layer-ext-moon'});
		}
	});
	
	$("#M-Gen-dialog-106 input").keyup(function(){
		
		var passwd = $("#M-Gen-dialog-106 input").val();		
		if(regular_lockpassword.test(passwd)){
			
			$("#M-Gen-button-118").removeClass("disabled");
		}else{
			
			$("#M-Gen-button-118").addClass("disabled");			
		}
	});*/
	
    //全部设备	
    $('#M-Gen-button-103').click(function(){
		
		$('#M-Gen-dialog-114,.gray_box').show();
    });
	
	$("#clear_all").click(function(){
		
		if($(".location_log_list").css("display") == "none"){
			
			log_type = 1;
		}else{
			
			log_type = 0;
		}
        ajax_clearAll(log_type);
	});
	
    //清除
    $('.icobtn-clear').click(function(){

        $('#M-Gen-dialog-111,.gray_box').show();
    })

    if (window.orientation === 90 || window.orientation === -90 ){
        $("#g_slider_menu").css({"background":"#000","opacity":".6"});
        $(".slider-nav-bg").css("background","none");

    }
	$(".find-phone-name").click(function(){
		$("#M-Gen-dialog-114,.gray_box").show();
	})
});