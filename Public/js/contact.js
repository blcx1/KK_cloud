var display_height;
var phone_height = 0;
var first_spell_height = 0;
var total_count = 0;
var total_height = 0;
var ajax_use = true;
var buttom_percent = 0.2;
var current_page = 1;
var is_recy = 0;
var list_type = "list";
var list_string = "";
var list_total_count = 0;
var list_current_page = 0;
var jsondata = new Object();
var recy_list_string = "";
var recy_list_total_count = 0;
var recy_list_current_page = 0;
var detail_id = 0;
var current_merge_type;
var use_contact_id = 0;
var merge_type_array = ["name","phone","email"];//合并类型
var merge_id_array = new Array();//合并的联系人id
var select_merge_list = new Array();
var current_merge_select = new Object();//当前选择联系人列表，调试用
var merge_complete_count = 0;//合并成功完成总个数
var merge_igonre_total_count = 0;//合并忽略总个数
var user_group_list = new Array();
var mime_type_object;
var mime_type_phone_array;
var mime_type_mail_array;
var mime_type_im_array;
var type_im_protocol_array;
var mime_type_postal_array;
var mime_type_organization_array;
var mime_type_nickname_array;
var mime_type_website_array;
var mime_type_relation_array;
var mime_type_sip_array;
var mime_type_event_array;
var focus_total_count = 0;
var ajax_submit = true;

function input_placeholder_process(object_input){
	
	var _this = object_input;
	var placeholder = _this.attr("placeholder");
	if(!is_undefined(placeholder) && (placeholder == _this.val() || _this.val().length <= 0)){
		
		_this.css({"color":"#b0b7bd","font-weight":"400"});
		_this.val(placeholder);				
	}	
}

function input_placeholder_init(){
	
	$(":text").each(function(){
		
		//input_placeholder_process($(this));
	});
}

function valid_mime_type(source_mime_type){
	
	var i = 0;
	var key;	
	var type_object = new Object();
	
	for(key in source_mime_type){
		
		if(i == 0){
			i++;
			continue;
		}
		type_object[key] = source_mime_type[key];		
	}
	return type_object;	
}

function get_mine_type_array(source_mime_type){
	
	var key;	
	var type_object = new Object();	
	for(key in source_mime_type){
		
		type_object[key] = source_mime_type[key];		
	}
	return type_object;	
}

//联系人相关对象初始化
function init_type(){
	
	mime_type_object = valid_mime_type(v_mime_type_object);
	delete mime_type_object["phone"];	
	delete mime_type_object["email"];	
	delete mime_type_object["photo"];
	delete mime_type_object["organization"];
	delete mime_type_object["nickname"];
	delete mime_type_object["group"];
	
    mime_type_phone_array = valid_mime_type(v_mime_type_phone_array);
    mime_type_mail_array = valid_mime_type(v_mime_type_mail_array);
    mime_type_im_array = valid_mime_type(v_mime_type_im_array);
    type_im_protocol_array = valid_mime_type(v_type_im_protocol_array);
    mime_type_postal_array = valid_mime_type(v_mime_type_postal_array);
	mime_type_organization_array = valid_mime_type(v_mime_type_organization_array);
	mime_type_nickname_array = valid_mime_type(v_mime_type_nickname_array);
	mime_type_website_array = valid_mime_type(v_mime_type_website_array);
	mime_type_relation_array = valid_mime_type(v_mime_type_relation_array);
	mime_type_sip_array = valid_mime_type(v_mime_type_sip_array);
	mime_type_event_array = valid_mime_type(v_mime_type_event_array);
}

//设置高度
function set_height(){
	
	total_height = 0;
	display_height = display_height > 0 ? display_height : $("#contactListFrame").height();
	if(total_count > 0){		
		
		phone_height = phone_height > 0 ? phone_height : $("#first_data").height();
		if(first_spell_height <= 0 && $("#first_spell_data").attr("class") != undefined){
			
			first_spell_height = $("#first_spell_data").height();
			total_height += first_spell_height * $("ul .contact-frist").length;
		}
		total_height += phone_height * $("ul .list_selection_item").length;		
	}
}

//获取spell列表
function get_first_spell_array(){
	var first_spell_array = new Array();
	if($("#contactListFrame ul .contact-frist").attr("class") != undefined){
		
		var i = 0;
		$("#contactListFrame ul .contact-frist p").each(function(){
			
			first_spell_array[i] = $(this).html();
			i++;
		});
	}
	return first_spell_array;
	
}

//处理按钮显示
function button_display(){
	
	if(is_recy == 1){
						
		$("#restore_contact,.restore_contact").show();
	}else{
		
		$("#restore_contact,.restore_contact").hide();
	}
}

//去重复数据
function no_repeat_first_spell(){
	
	$("#scroll_container_contact1 .contact-frist").each(function(){
						
		if($(this).next().attr("class") == undefined || $(this).next().hasClass("contact-frist") || total_count == 0){
			
			$(this).remove();
		}
	});
}

//异步获取列表
function ajax_contact_list(type,check_first){
	var name;
	var data_object;
	var next_page;
	var check_ajax = false;
	ajax_use = false;	
	if(check_first){
		next_page = 1;
		current_page = 0;
		check_ajax = true;
		total_count = 0;
	}else{
		if(current_page * page_size < total_count ){
		
			next_page = current_page + 1;
			check_ajax = true;
		}else{
			
			check_ajax = false;			
		}
	}
	
	if(check_ajax){
		if(type == "search"){
		
			name = "search";
			data_object = {'is_recy':is_recy,'p':next_page,'sk':$("#sk").val()};
		}else{
			
			name = "getList";
			data_object = {'is_recy':is_recy,'p':next_page};			
		}

		$.ajax({
				url:base_module_url+'/Contacts/'+ name +'.html?is_ajax=1',
				data:data_object,
				type:'POST',
				dataType:'JSON',
				success:function(data){	
													
					redirct(data);					
					if(data.status == 1){
						var i = 0;
						var id = 0;
						var str_html = "";
						var first_spell = "";
						var display_first_spell = false;
						var first_spell_array = new Array();
						var check_first_spell = true;
						var list_array = data.list;
						var id_array = new Array();
						ajax_use = true;
						current_page = next_page;
						total_count = data.total_count;											
						if(!check_first && current_page == 1){
							check_first = true;
						}else if(!check_first){
							first_spell_array = get_first_spell_array();
							str_html = $("#scroll_container_contact1").html();
							$("ul .list_selection_item").each(function(){
								
								id_array[i] = $(this).attr("data-id");
								i++;
							});							
						}
						for(var key in list_array){
							value = list_array[key];
							id = value['id'];
							if($.inArray(id,id_array) != -1){
								
								continue;
							}
							first_spell = value["first_spell"];
							display_first_spell = value["display"];
							if(display_first_spell){
								
								display_first_spell = $.inArray(first_spell,first_spell_array) == -1 ? true : false;
							}
							
							if(display_first_spell){
								
								str_html +='<li class="contactlist-index contact-frist"  '+(check_first_spell ? 'id="first_spell_data"' :'')+'> <p>'+first_spell+'</p> </li>';
								check_first_spell = false;
							}
							
							str_html +='<li data-id="'+id+'"  '+(check_first && key == 0 ? 'id="first_data"' :'')+' class="list_selection_item  ">'+
											'<div class="contactlist-container">'+
												'<p class="contact-nickname">'+value['display_name']+'</p>'+
												'<a class="checkbox item_checkbox  chkbox contact-information" href="javascript:void(&quot;contact/checkonefromlist&quot;);"><em></em></a>'+
											'</div>'+ 
										'</li>';							
						}
						
						$("#scroll_container_contact1").html(str_html);
						if(list_type == "list"){
							
							if(is_recy == 0){
								
								list_total_count = total_count;
								list_current_page = current_page;
								list_string = str_html;	
							}else{
								
								recy_list_total_count = total_count;
								recy_list_current_page = current_page;
								recy_list_string = str_html;
							}							
						}						
						display_process();
					}else{
						
						layer.msg(data.result,{icon:2,skin: 'layer-ext-moon'});
					}
				}
			});
	}	
}

//列表显示处理
function display_process(){
	
	var str_html;
	if(list_type == "search"){
							
		$("#contact_search").show();
		$("#contact_search_count").html(total_count);
		$("#contact_select_group_trigger").hide();
		$("#contact_select_group").hide();
		$("#contact_select_group_trigger .group-num").html(" ( "+0+" )");
	}else{
		
		$("#contact_search").hide();
		$("#contact_search_count").html(0);
		$("#contact_select_group").show();
		$("#contact_select_group_trigger").show();
		$("#contact_select_group_trigger .group-num").html(" ( "+total_count+" )");
	}
	str_html = $("#scroll_container_contact1").html();
	if(str_html.length > 0){
							
		$("#contactListFrame .no_data").hide();
	}else{
		
		$("#contactListFrame .no_data").show();
	}
	no_repeat_first_spell();
	set_height();
}

//搜索
function search(){
	
	var sk_current = trim($("#contact_search_input").val());
	if(sk_current.length >= 1 && sk_current != $("#contact_search_input").attr("placeholder")){
		
		var sk_history = $("#sk").val();
		if(!(list_type == "search" && sk_history == sk_current)){		
			
			list_type = "search";
			$("#sk").val(sk_current);
			$("#clear_search").show();
			$("#scroll_container_contact1").html("");
			ajax_contact_list("search",true);
		}			
	}else if(is_recy == 0 && list_total_count > 0 && list_string.length > 0){
		
		list_type = "list";
		total_count = list_total_count;
		current_page = list_current_page;
		$("#scroll_container_contact1").html(list_string);
		$("#clear_search").hide();		
		display_process();
	}else if(is_recy == 1 && recy_list_total_count > 0 && recy_list_string.length > 0){
			
		list_type = "list";
		total_count = recy_list_total_count;
		current_page = recy_list_current_page;
		$("#scroll_container_contact1").html(recy_list_string);
		$("#clear_search").hide();		
		display_process();
	}else{
		
		list_type = "list";
		$("#clear_search").hide();
		display_process();
		$("#scroll_container_contact1").html("");
		ajax_contact_list(list_type,true);
	}	
}

//详情显示
function detail_info_display(json_info){
	
	var key;	
	var job = "";
	var html_str="";
	var company = "";	
	var nickname = "";
	var info_list = {};
	var simple_info = {};
	var first_field_key;	
	var first_field = "";	
	var mime_type_attr = 0;
	var mime_type_array = [];
	var mime_type_attr_name;
	var other_field_key_element;	
	var base_info = json_info.base;
	var photo_info = json_info.photo;
	var nick_name_info = json_info.nickname;	
	var other_field_key_array = new Array();	
	var organization_info = json_info.organization;
	
	detail_id = base_info.id;
	$("#contact-padd2,#contact-detail-padd2,#contact_detail").show();
  	$("#contact-detail-padd2").parents("#contact_right").show();
    $("#contact-padd2 .cd-field:first").text(base_info.display_name);
    $(".default-hint,#edit-contact-padd,.contact-merge-panel,.create-contact-padd,#merge-panel1,#contact-panel,.import,.export").hide();
	if(nick_name_info.length > 0){
		
		nickname = nick_name_info[0].nick_name;		
	}
	if(organization_info.length > 0){
		
		simple_info = organization_info[0];
		company = simple_info.company;
		job = simple_info.job_description;
	}
	$("#contact-padd2 .cd-nickname").text(nickname);
	$(".cd-thefirst .cd-intro").text(company);
	$(".cd-thefirst .cd-group").text(job);
	$("#contact_detail_item_list dl").html("");
	for(key in json_info){
		
		if(key == "base" || key == "nickname" || key == "organization" || key == "photo" || key=="group"){
			
			continue;
		}
		html_str = "";
		first_field_key = key;
		other_field_key_array = new Array();		
		html_str += '<dt id="dt_'+key+'"><em class="contact-'+(key == "phone" ? 'phoneico' : key)+'"> ' + (key == "phone" ? ' ' : v_mime_type_object[key] + ": ") + ' </em></dt>';
        html_str += '<dd class="dd_'+key+'">';		
		switch(key){
			
			case "phone":
				
				mime_type_array = v_mime_type_phone_array;
				break;
			case "email":			
					
				mime_type_array = v_mime_type_mail_array;
				break;
			case "postal":
				
				first_field_key = "formatted_address";			
				mime_type_array = v_mime_type_postal_array;
				break;
			case "im":				
				
				first_field_key = "chat_account";
				mime_type_array = v_mime_type_im_array;
				break;
			case "event":				
				
				first_field_key = "start_date";
				mime_type_array = v_mime_type_event_array;
				break;
			case "relation":				
				
				first_field_key = "name";
				mime_type_array = v_mime_type_relation_array;
				break;
			case "website":			
				
				first_field_key = "url";
				mime_type_array = v_mime_type_website_array;
				break;
			case "group":
				
				first_field_key = "group_raw_id";
				mime_type_array = [];				
				break;
			case "note":				
				
				mime_type_array = [];				
				break;
			case "sip":
				
				first_field_key = "sip_address";
				mime_type_array = v_mime_type_sip_array;				
				break;			
		}
		
		info_list = json_info[key];
		var k = -1;
		for(k in info_list){
					
			simple_info = info_list[k];
			other_field_key_element = "";
			mime_type_attr = simple_info['type_name'];
			mime_type_attr_name = mime_type_array[mime_type_attr] != undefined ? mime_type_array[mime_type_attr] : "";
			for(var okey in other_field_key_array){
				
				var value = other_field_key_array[okey];
				other_field_key_element = '<p class="cd-field">'+simple_info[value]+'</p> ';
			}			
			html_str +=' <div class="cd-div"> <p class="cd-field">'+simple_info[first_field_key]+'</p> ' + other_field_key_element + ' <p class="cd-intro">'+mime_type_attr_name+'</p> </div>';
			if(key == "sip" || key == "note"){
				
				break;
			}
		}
		if(k == -1){
			
			html_str = "";		
		}else{
			
			html_str += "</dd>";	
		}
		
		$("#contact_detail_item_list dl").append(html_str);
	}
}

//获取详情
function get_detail(id){
	
	var json_info = new Object();
	if(jsondata[id] == undefined){
		
		$.ajax({
			url:base_module_url+'/Contacts/getDetail.html?is_ajax=1',
			data:{'id':id},
			type:'POST',
			dataType:'JSON',
			success:function(data){	
												
				redirct(data);					
				if(data.status == 1){
					
					json_info = data.info;					
					if(json_info.base != undefined){
						
						jsondata[id] = json_info;
						detail_info_display(json_info);
					}else{
						
						layer.msg(v_no_data,{icon:2,skin: 'layer-ext-moon'});
					}
					
				}else{
					
					layer.msg(data.result,{icon:2,skin: 'layer-ext-moon'});
				}
			}
		});		
	}else{
		
		json_info = jsondata[id];
		detail_info_display(json_info);
	}
	if(is_recy == 0){
		
		$("#M-Gen-button-132").show();
	}else{
		
		$("#M-Gen-button-132").hide();
	}
	button_display();
}

//清空数据
function clearAll(){
	
	layer.confirm(v_tip_delete,
		{btn: [v_determine,v_cancel]},
		function(index){
			
			layer.close(index);
			$.ajax({
				url:base_module_url+'/Contacts/clearAll.html?is_ajax=1',
				data:{},
				type:'POST',
				dataType:'JSON',
				success:function(data){	
													
					redirct(data);	
					var icon = 1;
					var mesg_str = "";
					if(data.status == 1){				
						
						ajax_use = true;	
						jsondata = new Object();
						mesg_str = v_operation_success;
						recy_list_string = list_string = "";						
						detail_id = recy_list_current_page = list_current_page = current_page = total_count = recy_list_total_count = list_total_count = 0;											
						
						$("#scroll_container_contact1").html("");												
						display_process();       
						$("#merge-panel1 .global-input").find("input").val("");
						$('#contact-padd2,.default-hint,#contact_operate_bar').show();
						$('#edit-contact-padd,#create-contact-padd,#contact_select_mode,#contact_sel_bar').hide();
						$('#contact_selected_container').find('li').remove();
					}else{
						
						icon = 2;
						mesg_str = data.result;
					}
					layer.msg(mesg_str,{icon:icon,skin: 'layer-ext-moon'});
				}
			});
		},
		function(index){
			layer.close(index);
		}
	);
}

//选择html拼接
function get_select_html(contact_id,contact_name){
	
	var html_str = '<li id="contact_selected_'+contact_id+'">   <div class="ca-avator"></div>   <p>'+contact_name+'</p></li>';
	return html_str;
}

//选择条数计算后显示
function select_total_count(){
	
	if($('.contactlist-container .active1').length != undefined){
		
		$("#select_contact_count").text($('.contactlist-container .active1').length);
	}
}

//移动到回收站
function contact_move_recycle(){
	
	var i = 0;
	var contact_id;
	var delete_id = 0;	
	var delete_id_str = "";
	var delete_id_str_len = 0;
	var valid_total_count = 0;
	var delete_id_array = new Array();
	
	if($('#contact_select_mode').css("display") == "none"){
		
		delete_id_str = detail_id.toString() + ",";
		delete_id_array[i] = detail_id;
	}else{
		
		$("ul .list_selection_item").each(function(){
		
			if($(this).find(".active1").attr("class") != undefined){
				
				delete_id = $(this).attr("data-id");
				delete_id_str = delete_id_str + delete_id.toString() + ",";
				delete_id_array[i] = delete_id;
				i++;
			}		
		});
	}
	
	delete_id_str_len = delete_id_str.length;
	delete_id_str = delete_id_str.substr(0,delete_id_str_len - 1);
	if(delete_id_str_len > 1){
		
		$.ajax({
			url:base_module_url+'/Contacts/recycle.html?is_ajax=1',
			data:{'id':delete_id_str},
			type:'POST',
			dataType:'JSON',
			success:function(data){	
												
				redirct(data);					
				if(data.status == 1){					
										
					$("ul .list_selection_item").each(function(){
						
						contact_id = $(this).attr("data-id");
						if($.inArray(contact_id,delete_id_array) != -1){
							
							$(this).remove();						
						}						
					});
					total_count = list_total_count = list_total_count - delete_id_array.length;
					no_repeat_first_spell();
					valid_total_count = $("ul .list_selection_item").length;
					recy_list_string = "";
					recy_list_total_count = 0;
					recy_list_current_page = 0;
					
					if(page_size > valid_total_count && total_count > 0){
						
						if(total_count > valid_total_count){
							
							list_string = "";
							$("#scroll_container_contact1").html("");
							ajax_contact_list("list",true);
						}						
					}else{
						
						if(total_count == 0){
						
							$("#contactListFrame .no_data").show();
						}else if(total_count > valid_total_count){
							
							current_page = list_current_page = Math.floor(valid_total_count/page_size);
							ajax_contact_list("list",false);
						}			
					}
					$("#contact_select_group_trigger .group-num").html(" ( "+total_count+" )");
					list_string = $("#scroll_container_contact1").html();
					$("#M-Gen-dialog-123,#global_popup,#contact_select_mode,#contact_sel_bar,#contact-padd2").hide();
					$(".default-hint,#contact_operate_bar").show();
					$(".global-list .checkbox").css("display","")
					$("#edit-contact-padd").hide();
					set_height();			
					layer.msg(v_succ_delete,{icon:1,skin: 'layer-ext-moon'});					
				}else{
					
					layer.msg(data.result,{icon:2,skin: 'layer-ext-moon'});
				}
			}
		});		
	}else{
		
		layer.msg(v_sel_delete,{icon:2,skin: 'layer-ext-moon'});
	}	
}

//移出回收站
function contact_recover(){
	
	var i = 0;
	var contact_id;
	var recover_id = 0;	
	var recover_id_str = "";
	var recover_id_str_len = 0;
	var valid_total_count = 0;
	var recover_id_array = new Array();
	
	if($('#contact_select_mode').css("display") == "none"){
		
		recover_id_str = detail_id.toString() + ",";
		recover_id_array[i] = detail_id;
	}else{
		
		$("ul .list_selection_item").each(function(){
		
			if($(this).find(".active1").attr("class") != undefined){
				
				recover_id = $(this).attr("data-id");
				recover_id_str = recover_id_str + recover_id.toString() + ",";
				recover_id_array[i] = recover_id;
				i++;
			}		
		});
	}
	
	recover_id_str_len = recover_id_str.length;
	recover_id_str = recover_id_str.substr(0,recover_id_str_len - 1);
	if(recover_id_str_len > 1){
		
		$.ajax({
			url:base_module_url+'/Contacts/recover.html?is_ajax=1',
			data:{'id':recover_id_str},
			type:'POST',
			dataType:'JSON',
			success:function(data){	
												
				redirct(data);					
				if(data.status == 1){
					if($(document).width()<1024){
						$(".mdl-m-m-r").hide();
						$(".mdl-m-m-l").show();
					}
										
					$("ul .list_selection_item").each(function(){
						
						contact_id = $(this).attr("data-id");
						if($.inArray(contact_id,recover_id_array) != -1){
							
							$(this).remove();									
						}						
					});
					total_count = recy_list_total_count = recy_list_total_count - recover_id_array.length;
					no_repeat_first_spell();
					valid_total_count = $("ul .list_selection_item").length;
					list_string = "";
					list_total_count = 0;
					list_current_page = 0;
					if(page_size > valid_total_count && total_count > 0){
						
						if(total_count > valid_total_count){
							
							recy_list_string = "";
							$("#scroll_container_contact1").html("");
							ajax_contact_list("list",true);
						}					
					}else{
											
						if(total_count == 0){
						
							$("#contactListFrame .no_data").show();
						}else if(total_count > valid_total_count){
							
							current_page = recy_list_current_page = Math.floor(valid_total_count/page_size);
							ajax_contact_list("list",false);
						}					
					}
					$("#contact_select_group_trigger .group-num").html(" ( "+total_count+" )");
					recy_list_string = $("#scroll_container_contact1").html();
					$("#M-Gen-dialog-123,#global_popup,#contact_select_mode,#contact_sel_bar,#contact-padd2").hide();
					$(".default-hint,#contact_operate_bar").show();
					$(".global-list .checkbox").css("display","")
					$("#edit-contact-padd").hide();
					set_height();			
					layer.msg(v_succ_restore,{icon:1,skin: 'layer-ext-moon'});					
				}else{
					
					layer.msg(data.result,{icon:2,skin: 'layer-ext-moon'});
				}
			}
		});		
	}else{
		
		layer.msg(v_sel_data,{icon:2,skin: 'layer-ext-moon'});
	}	
}

//彻底删除数据
function contact_delete(){
	
	var i = 0;
	var contact_id;
	var delete_id = 0;	
	var delete_id_str = "";
	var delete_id_str_len = 0;
	var valid_total_count = 0;
	var delete_id_array = new Array();
	
	if($('#contact_select_mode').css("display") == "none"){
		
		delete_id_str = detail_id.toString() + ",";
		delete_id_array[i] = detail_id;
	}else{
		
		$("ul .list_selection_item").each(function(){
		
			if($(this).find(".active1").attr("class") != undefined){
				
				delete_id = $(this).attr("data-id");
				delete_id_str = delete_id_str + delete_id.toString() + ",";
				delete_id_array[i] = delete_id;
				i++;
			}		
		});
	}
	
	delete_id_str_len = delete_id_str.length;
	delete_id_str = delete_id_str.substr(0,delete_id_str_len - 1);
	if(delete_id_str_len > 1){
		
		$.ajax({
			url:base_module_url+'/Contacts/delete.html?is_ajax=1',
			data:{'id':delete_id_str},
			type:'POST',
			dataType:'JSON',
			success:function(data){	
												
				redirct(data);					
				if(data.status == 1){					
										
					$("ul .list_selection_item").each(function(){
						
						contact_id = $(this).attr("data-id");
						if($.inArray(contact_id,delete_id_array) != -1){
							
							$(this).remove();							
							delete jsondata[contact_id];
						}						
					});
					total_count = recy_list_total_count = recy_list_total_count - delete_id_array.length;
					no_repeat_first_spell();
					valid_total_count = $("ul .list_selection_item").length;
					
					if(page_size > valid_total_count && total_count > 0){
						
						if(total_count > valid_total_count){
							
							recy_list_string = "";
							$("#scroll_container_contact1").html("");
							ajax_contact_list("list",true);
						}						
					}else{
											
						if(total_count == 0){
						
							$("#contactListFrame .no_data").show();
						}else if(total_count > valid_total_count){
							
							current_page = recy_list_current_page = Math.floor(valid_total_count/page_size);
							ajax_contact_list("list",false);
						}					
					}
					$("#contact_select_group_trigger .group-num").html(" ( "+total_count+" )");
					recy_list_string = $("#scroll_container_contact1").html();
					$("#M-Gen-dialog-123,#global_popup,#contact_select_mode,#contact_sel_bar,#contact-padd2").hide();
					$(".default-hint,#contact_operate_bar").show();
					$(".global-list .checkbox").css("display","")
					$("#edit-contact-padd").hide();					
					set_height();			
					layer.msg(v_succ_delete,{icon:1,skin: 'layer-ext-moon'});					
				}else{
					
					layer.msg(data.result,{icon:2,skin: 'layer-ext-moon'});
				}
			}
		});		
	}else{
		
		layer.msg(v_sel_delete,{icon:2,skin: 'layer-ext-moon'});
	}	
}

function object_to_array(list_value){
	
	var i = 0;
	var key;
	var arr = new Array();
	for(key in list_value){
		
		arr[i] = list_value[key];
		i++;
	}object_to_array

	return arr;

}

function merge_html_block(merge_type,list_value,key){
	
	var k;
	var i;
	var list_row;	
	var html_block;	
	var list_phone_item;
	var list_array = object_to_array(list_value);
	var list_len = list_array.length;
	var check_color = key % 2 == 1 ? true : false;

	html_block = '<div class="cmcc-list clearfix" merge-type="'+merge_type+'" '+(check_color ? ' style="background-color:#f2f2f2" ':'')+'><div class="fl-l">';
	for(k in list_array){
		
		list_row = list_array[k];

		list_phone_item = list_row.phone_array;
		html_block += '<div class="cm-contact-item" data-id="'+list_row['id']+'"><div class="contactlist-container"><div class="contactlist-avatar"></div>'+												
					  '<p class="contact-nickname contact-red-mark">'+list_row['display_name']+'</p>';
		if(merge_type == 'email'){
			
			html_block += '<p class="contact-email">'+list_row['email']+'</p>';
		}			  
		for(i in list_phone_item){
			
			html_block += '<p class="contact-num">'+list_phone_item[i]+'</p>';
		}
		if(list_len >= 2){
			
			html_block += '<em data-type="contact" id="merge-contact-'+key+'-'+k+'" g-index="'+key+'" c-index="0" class="cmcc-check-area"></em>';	
		}
		html_block += '</div></div>';
	}	
	
	html_block += '</div><div class="fl-r-block"><div class="fl-r cmcc-check-area"  ><em data-type="group" id="merge-group-'+key+'" g-index="'+key+'"></em></div></div></div>';	
	
	return html_block;	
}

//获取合并列表
function get_merge_list(){
	
	$.ajax({
		url:base_module_url+'/Contacts/getMergeList.html?is_ajax=1',
		data:{},
		type:'POST',
		dataType:'JSON',
		success:function(data){	
											
			redirct(data);					
			if(data.status == 1){

				var key;
				var k_group = 0;
				var list_value;
				var merge_type;
				var html_str = "";
				var list = data.list;				
				var name_array = list.name;
				var phone_array = list.phone;
				var email_array = list.email;
				var merge_list_count = list.total_count;
				$("#possible_num").text(merge_list_count);
				merge_type = "name";

				for(key in name_array){

					list_value = name_array[key];

					html_str += merge_html_block(merge_type,list_value,k_group);
					k_group++;
				}
				merge_type = "phone";
				for(key in phone_array){

					list_value = phone_array[key];
					html_str += merge_html_block(merge_type,list_value,k_group);
					k_group++;
				}
				merge_type = "email";
				for(key in email_array){
					
					list_value = email_array[key];
					html_str += merge_html_block(merge_type,list_value,k_group);
					k_group++;
				}				
				if(merge_list_count < 1){
					
					html_str = '<p class="no_data merge_no_data">'+ v_no_data +'</p>';
					$(".cm-contact-container .cmcc-list,.merge-contact,.ignore-contact").hide();					
				}else{
					$(".cm-contact-container .cmcc-list,.merge-contact,.ignore-contact").show();
				}
				$(".cmcc-data-container").html(html_str);
				$("#contact-panel").show();
				$(".contact-merge-panel").hide();
				if(merge_list_count > 1){
					
					$(".cm-contact-container .cmcc-list,.merge-contact,.ignore-contact").show();
					$(".cmcc-list .cm-contact-item em").addClass("cmcc-check-area");
				}				
			}else{
				
				layer.msg(data.result,{icon:2,skin: 'layer-ext-moon'});
			}
		}
	});
}

//合并数据
function merge_data(type,use_id,merge_id_array){
	
	var delete_len = merge_id_array.length - 1;
	console.log(delete_len)
	console.log(merge_type_array)
	console.log(type)
	if($.inArray(type,merge_type_array) == -1 || use_id <= 0 || delete_len <= 0){
		
		layer.msg(v_operation_faild,{icon:2,skin: 'layer-ext-moon'});
	}else{
		
		$.ajax({
			url:base_module_url+'/Contacts/merge.html?is_ajax=1',
			data:{"id":use_id,"type":type,"merge_id_array":merge_id_array},
			type:'POST',
			dataType:'JSON',
			success:function(data){	
												
				redirct(data);					
				if(data.status == 1){
					
					var contact_id;
					total_count = 0;
					current_page = 0;
					list_string = "";
					list_total_count = 0;
					list_current_page = 0;

					recy_list_string = "";
					recy_list_total_count = 0;
					recy_list_current_page = 0;
					
					$("ul .list_selection_item").each(function(){
						
						contact_id = $(this).attr("data-id");

						if($.inArray(contact_id,merge_id_array) != -1){
							
							if(use_id != contact_id){
								
								$(this).remove();	
							}													
							delete jsondata[contact_id];
						}						
					});	
					merge_complete_count++;					
					layer.msg(v_operation_success,{icon:1,skin: 'layer-ext-moon'});
				}else{
					
					layer.msg(data.result,{icon:2,skin: 'layer-ext-moon'});
				}
				next_merge_display();
			}
		});
	}
}

//合并联系人改变操作相关改变
function change_merge_contact(merge_total_count,change_count){
	var cca_length=$(".fl-r-block .cmcc-check-area").length
	merge_total_count += change_count;
	$("#already_merge_count").text(merge_total_count);
	if(merge_total_count > 0){
		
		if(!$(".merge-contact").hasClass("default")){
			$(".merge-contact").removeClass("disabled");
			$(".merge-contact").addClass("default");
		}				
	}else{
		
		if($(".merge-contact").hasClass("default")){
			
			$(".merge-contact").addClass("disabled");
			$(".merge-contact").removeClass("default");
		}
	}
	if($(".cmcc-check-first .checkbox").hasClass("active1")){
		
		$(".cmcc-check-first .checkbox").removeClass("active1");
	}
	if(merge_total_count ==cca_length){
		$(".cmcc-check-first .checkbox").addClass("active1");
	}else{
		$(".cmcc-check-first .checkbox").removeClass("active1");
	}
}

//联系人姓名拼接显示
function contact_display_name_show(){
	
	var regular_char = /^[a-z\s]+$/i;
	var prefix = trim($("#ceditor_prefix").val());
	var familyName = trim($("#ceditor_familyName").val());
	var middleName = trim($("#ceditor_middleName").val());
	var givenName = trim($("#ceditor_givenName").val());
	var suffix = trim($("#ceditor_suffix").val());
	prefix = prefix == $("#ceditor_prefix").attr("placeholder") ? '': prefix;
	familyName = familyName == $("#ceditor_familyName").attr("placeholder") ? '': familyName;
	middleName = middleName == $("#ceditor_middleName").attr("placeholder") ? '': middleName;
	givenName = givenName == $("#ceditor_givenName").attr("placeholder") ? '': givenName;
	suffix = suffix == $("#ceditor_suffix").attr("placeholder") ? '': suffix;
	var family_name_check = regular_char.test(familyName);
	var middle_name_check = regular_char.test(middleName);
	var given_name_check = regular_char.test(givenName);	 
	
	if(regular_char.test(prefix) && family_name_check){		
		
		prefix += " ";
	}	
	if(family_name_check && middle_name_check){
		
		familyName += " ";
	}	
	if(middle_name_check && given_name_check){
		
		middleName += " ";
	}	
	if(given_name_check && regular_char.test(suffix)){
		
		givenName += ", ";
	}
	
	$("#ceditor_displayName").val(prefix + familyName + middleName + givenName + suffix);
}

//提交单个类型处理
function mime_type_process(mime_type,element_name){
	
	var i;
	var val;	
	var attr_id;	
	var placeholder;
	var value_object = new Object();
	var attr_array = new Array();
	var value_array = new Array();
	var value_already_array = new Array();
	if($("#"+element_name+" .mime_type").length > 0){			
		
		i = 0;
		$("#"+element_name+" .mime_type").each(function(){
			
			val = trim($(this).parent().next("dd").find(".ceditor_input").val());
			placeholder = $(this).parent().next("dd").find(".ceditor_input").attr("placeholder");
			if(val.length <= 0 || val == placeholder || in_array(val,value_already_array) || (mime_type == "phone" && !is_phone(val)) || (mime_type == "email" && !is_mail(val))){
				
				return true;
			}
			
			attr_id = parseInt($(this).val());
			attr_array[i] = attr_id;
			value_array[i] = val;
			i++;			
		});
	}
	
	value_object["attr_array"] = attr_array;
	value_object["value_array"] = value_array;
	
	return value_object;
}

//提交处理
function submit_process(display_name,type){
	
	var mime_type;
	var value_object;	
	var element_name;
	var data_object = new Object();
	var igore_array = ["base","nickname","note","organization","photo","sip"];
	type = type == "add" ? "add" : "edit";
	if(type == "edit"){
		
		data_object["id"] = detail_id;
	}
	data_object['display_name'] = display_name;
	data_object["prefix"] = trim($("#ceditor_prefix").val());
	data_object["prefix"] = data_object["prefix"] != $("#ceditor_prefix").attr("placeholder") ? data_object["prefix"] : "";
	data_object["family_name"] = trim($("#ceditor_familyName").val());
	data_object["family_name"] = data_object["family_name"] != $("#ceditor_familyName").attr("placeholder") ? data_object["family_name"] : "";
	data_object["middle_name"] = trim($("#ceditor_middleName").val());
	data_object["middle_name"] = data_object["middle_name"] != $("#ceditor_middleName").attr("placeholder") ? data_object["middle_name"] : "";
	data_object["given_name"] = trim($("#ceditor_givenName").val());
	data_object["given_name"] = data_object["given_name"] != $("#ceditor_givenName").attr("placeholder") ? data_object["given_name"] : "";
	data_object["suffix"] = trim($("#ceditor_suffix").val());
	data_object["suffix"] = data_object["suffix"] != $("#ceditor_suffix").attr("placeholder") ? data_object["suffix"] : "";
	data_object["nick_name"] = trim($("#ceditor_nick_name").val());
	data_object["nick_name"] = data_object["nick_name"] != $("#ceditor_nick_name").attr("placeholder") ? data_object["nick_name"] : "";
	data_object["company"] = trim($("#ceditor_company").val());
	data_object["company"] = data_object["company"] != $("#ceditor_company").attr("placeholder") ? data_object["company"] : "";
	data_object["job_description"] = trim($("#ceditor_title").val());
	data_object["job_description"] = data_object["job_description"] != $("#ceditor_title").attr("placeholder") ? data_object["job_description"] : "";
	data_object["note"] = "";
	data_object["sip_address"] = "";
	if($("#ceditor_cato_note .ceditor_input").val() != undefined){
		
		data_object["note"] = trim($("#ceditor_cato_note .ceditor_input").val());
		data_object["note"] = data_object["note"] != $("#ceditor_cato_note .ceditor_input").attr("placeholder") ? data_object["note"] : "";
	}
	
	if($("#ceditor_cato_sipPhones .ceditor_input").val() != undefined){
		
		data_object["sip_address"] = trim($("#ceditor_cato_sipPhones .ceditor_input").val());	
		data_object["sip_address"] = data_object["sip_address"] != $("#ceditor_cato_sipPhones .ceditor_input").attr("placeholder") ? data_object["sip_address"] : "";
	}
	
	for(mime_type in v_mime_type_object){
		
		if(in_array(mime_type,igore_array)){
			
			continue;
		}	
		
		switch(mime_type){
			
			case "phone":		
				
				element_name = "ceditor_cato_phoneNumbers";				
				break;
			case "email":
						
				element_name = "ceditor_cato_emails";			
				break;
			case "im":			
				
				element_name = "ceditor_cato_ims";		
				break;
			case "event":			
				
				element_name = "ceditor_cato_events";		
				break;
			case "website":		
				
				element_name = "ceditor_cato_urls";
				break;
			case "postal":				
				
				element_name = "ceditor_cato_addresses";
				break;		
			case "relation":			
							
				element_name = "ceditor_cato_relations";	
				break;
			
			case "group":
					
				element_name = "ceditor_cato_groups";				
				break;			
		}
		value_object = new Object();
		value_object = mime_type_process(mime_type,element_name);
		data_object[mime_type] = value_object["value_array"];
		data_object[mime_type+"_arr"] = value_object["attr_array"];
	}
	
	if(type == "add"){
			
		contact_add(data_object);
	}else{
		
		contact_edit(data_object);
	}
}

//联系人添加修改处理
function contacts_submit(type){	
	
	var display_name = trim($("#ceditor_displayName").val());	
	if(display_name.length < 1 || display_name == $("#ceditor_displayName").attr("placeholder")){
		
		layer.msg(v_e_empty_contact_name,{icon:0,skin: 'layer-ext-moon'});
		return false;
	}
	
	if(!ajax_submit){
		
		ajax_submit = true;
		return false;
	}	
	
	var id = 0;
	type = type == "add" ? "add" : "edit";	
	if(type == "edit"){
		
		id = detail_id;
	}
	
	$.ajax({
		url:base_module_url+'/Contacts/checkValid.html?is_ajax=1',
		data:{"id":id,"display_name":display_name},
		type:'POST',
		dataType:'JSON',
		success:function(data){	
											
			redirct(data);			
			if(data.status == 1){					
									
				submit_process(display_name,type);				
			}else{
				
				layer.msg(data.result,{icon:2,skin: 'layer-ext-moon'});
			}
		}
	});
	
}

//添加联系人
function contact_add(data_object){
	
	$.ajax({
		url:base_module_url+'/Contacts/add.html?is_ajax=1',
		data:data_object,
		type:'POST',
		dataType:'JSON',
		success:function(data){	
											
			redirct(data);			
			if(data.status == 1){					
									
				detail_id = data.id;						
				$("#merge-panel1,#contact_detail").hide();				
				$("#contact-detail-padd2,#contact-padd2").show();
				layer.msg(v_add_success,{icon:1,skin: 'layer-ext-moon'});
				is_recy = 0;
				get_detail(detail_id);
				
				ajax_contact_list("list",true);
				
			}else{
				
				layer.msg(data.result,{icon:2,skin: 'layer-ext-moon'});
			}
		}
	});
}

//修改联系人
function contact_edit(data_object){
	
	$.ajax({
		url:base_module_url+'/Contacts/edit.html?is_ajax=1',
		data:data_object,
		type:'POST',
		dataType:'JSON',
		success:function(data){	
											
			redirct(data);			
			if(data.status == 1){					
				
				$("#edit-contact-padd").hide();
				$("#contact-padd2").show();
				delete jsondata[detail_id];
				layer.msg(v_change_success,{icon:1,skin: 'layer-ext-moon'});
				is_recy = 0;
				get_detail(detail_id);				
				ajax_contact_list("list",true);
			}else{
				
				layer.msg(data.result,{icon:2,skin: 'layer-ext-moon'});
			}
		}
	});
}

//合并前单个html展示
function single_merge_html(row_contact,key_index,is_check){
	
	var str_html = "";
	var merge_type = row_contact["merge_type"];
	var phone_array = row_contact["phone_array"];
	str_html = '<div class="left-contact-item msc-data-item '+(is_check ? 'checked' :'')+'" array-index="'+row_contact["id"]+'">'+
					'<div class="contactlist-avatar"></div>'+
					'<div class="msc-di-con">'+
						'<span class="title">'+v_full_name+'</span>'+
						'<p class="msc-data-mes ">'+row_contact["name"]+'</p>'+
					'</div>';
	if(merge_type == "email"){
		
		str_html +=	'<div class="msc-di-con">'+
						'<span class="title">'+v_email+'</span>'+
						'<p class="msc-data-mes ">'+row_contact["email"]+'</p>'+
					'</div>';
	}
	if(phone_array.length > 0){
		
		str_html +=	'<div class="msc-di-con"><span class="title">'+v_phone+'</span>';
		for(var key in phone_array){
			
			str_html +=	'<p class="msc-data-mes ">'+phone_array[key]+'</p>';
		}						
		str_html +=	'</div>';
	}	
	str_html += '<em class="msc-index-num '+(is_check ? '' :'two-status')+'">'+key_index+'</em></div>';
	return str_html;
}

//合并后html展示
function mult_merge_html(mult_merge_list){
	
	var str_html = "";
	var merge_type = mult_merge_list["merge_type"];
	var phone_array = mult_merge_list["phone_array"];
	str_html = '<div class="msc-data-item">'+
					'<div class="contactlist-avatar"></div>'+				
					'<div class="msc-di-con">'+
						'<span class="title">'+v_full_name+'</span>'+
						'<p class="msc-dic-container ">'+mult_merge_list["name"]+'</p>'+
					'</div>';
	if(merge_type == "email"){
		
		str_html +=	'<div class="msc-di-con">'+
						'<span class="title">'+v_email+'</span>'+
						'<p class="msc-dic-container">'+mult_merge_list["email"]+'</p>'+
					'</div>';
	}
	if(phone_array.length > 0){
		
		str_html +=	'<div class="msc-di-con"><span class="title">'+v_phone+'</span>';
		for(var key in phone_array){
			
			str_html +=	'<p class="msc-dic-container">'+phone_array[key]+'</p>';
		}						
		str_html +=	'</div>';
	}					
	str_html +=	'</div>';
	
	return str_html;
}

//完成合并处理
function merge_complete_display(){
	
	ajax_contact_list("list",true);
	$(".dialog-contact,#__mask1__,#contact-panel").hide();
	$(".merge-contacts,#merge-panel1").show();
	if($(".merge-contact").hasClass("default")){
		
		$(".merge-contact").removeClass("default");		
	}
	$(".cm-contact-container .active2").each(function(){
		
		$(this).removeClass("active2");
	});
	$(".merge-complete .merge-count").text(merge_complete_count);	
}

//合并后展示
function merge_display(){
	
	if(select_merge_list.length > 0){
				
		var id;
		var v = 0;
		var q = 0;
		var is_check;
		var key_index;
		var phone_array;
		var row_contact;
		var str_html = "";
		var mult_merge_list_len = 0;
		var mult_merge_phone_array;
		var mult_merge_list = new Object();		
		
		for(key_index in select_merge_list){
			
			v = 0;
			is_check = true;
			current_merge_select = select_merge_list[key_index];					
			for(var key in current_merge_select){
				
				phone_array = new Array();
				row_contact = current_merge_select[key];						
				str_html += single_merge_html(row_contact,v+1,is_check);
				id = row_contact["id"];
				phone_array = row_contact["phone_array"];
				merge_id_array[v] = id;
				if(is_check){
					
					is_check = false;
					use_contact_id = id;
					mult_merge_list["name"] = row_contact["name"];
					current_merge_type = row_contact["merge_type"];
					mult_merge_list["merge_type"] = current_merge_type;
					if(current_merge_type == "email"){
						
						mult_merge_list["email"] = row_contact["email"];
					}
					mult_merge_list["phone_array"] = phone_array;
				}else{							
					
					mult_merge_phone_array = new Array();
					mult_merge_phone_array = mult_merge_list["phone_array"];
					mult_merge_list_len = mult_merge_phone_array.length;
					for(q in phone_array){
						
						if($.inArray(phone_array[q],mult_merge_phone_array) == -1){
							
							mult_merge_phone_array[mult_merge_list_len] = phone_array[q];
							mult_merge_list_len++;
						}								
					}							
				}						
				v++;
			}
			break;
		}
		$(".msc-title .msc-index-num").text(1);
		$(".current_merge").text(merge_igonre_total_count + merge_complete_count + 1);
		$("#repeat_contacts").text(merge_id_array.length);
		$(".msc-data-container").html(str_html);
		str_html = mult_merge_html(mult_merge_list);
		$(".multi_merge").html(str_html);
		$(".dialog-contact").show();
		$("#__mask1__").show();
	}else{
		
		merge_complete_display();
	}
}

//下一个合并
function next_merge_display(){
			
	if(select_merge_list.length > 0){
	
		for(var key in select_merge_list){
			
			select_merge_list.splice(key,1);
			break;
		}				
	}
	merge_display();
}

//显示mime-type 属性列表
function show_mime_type_attr(object_mime_type,mime_type){

	/*if($(".mime_type_attr").length > 0){
		alert(2);
		$(".mime_type_attr").hide();
	}
	if(object_mime_type.find(".mime_type_attr").attr("class") != undefined){
		alert(3);
		object_mime_type.find(".mime_type_attr").show();
		return false;
	}*/
	if(object_mime_type.find(".mime_type_attr").length > 0){
		if(object_mime_type.find(".mime_type_attr").css("display")=="block"){
			object_mime_type.find(".mime_type_attr").hide();
		}else{
			$(".mime_type_attr").hide();
			object_mime_type.find(".mime_type_attr").show();
		}
	}else{
		$(".mime_type_attr").hide();
		var str_html = "";
		var check_show = true;
		var mime_type_tmp_array;
		switch(mime_type){

			case "phone":

				mime_type_tmp_array = get_mine_type_array(mime_type_phone_array);
				break;
			case "email":

				mime_type_tmp_array = get_mine_type_array(mime_type_mail_array);
				break;
			case "im":

				mime_type_tmp_array = get_mine_type_array(mime_type_im_array);
				break;
			case "event":

				mime_type_tmp_array = get_mine_type_array(mime_type_event_array);
				break;
			case "website":

				mime_type_tmp_array = get_mine_type_array(mime_type_website_array);
				break;
			case "postal":

				mime_type_tmp_array = get_mine_type_array(mime_type_postal_array);
				break;
			case "relation":

				mime_type_tmp_array = get_mine_type_array(mime_type_relation_array);
				break;
			case "sip":

				check_show = false;
				break;
			case "note":

				check_show = false;
				break;
			case "group":

				mime_type_tmp_array = get_mine_type_array(user_group_list);
				break;
		}
		if(check_show){
			var key;
			str_html +='<div class="mime_type_attr"><div class="dialog-menu animated2 fadeInUp menu fadeInUp-menu "><ul>';
			for(key in mime_type_tmp_array){

				str_html +='<li data-id="'+key+'" class="mime_type_item"><span class="li-font">'+mime_type_tmp_array[key]+'</span></li>';
			}
			str_html +='</ul><em class="menu-corner right"></em></div></div>';
			object_mime_type.append(str_html);
			object_mime_type.find(".mime_type_attr").show();
		}
	}
	/*var str_html = "";
	var check_show = true;
	var mime_type_tmp_array;
	switch(mime_type){
		
		case "phone":			
			
			mime_type_tmp_array = get_mine_type_array(mime_type_phone_array);
			break;
		case "email":							
			
			mime_type_tmp_array = get_mine_type_array(mime_type_mail_array);
			break;
		case "im":				
			
			mime_type_tmp_array = get_mine_type_array(mime_type_im_array);
			break;
		case "event":					
			
			mime_type_tmp_array = get_mine_type_array(mime_type_event_array);			
			break;
		case "website":					
			
			mime_type_tmp_array = get_mine_type_array(mime_type_website_array);
			break;
		case "postal":						
			
			mime_type_tmp_array = get_mine_type_array(mime_type_postal_array);
			break;		
		case "relation":					
			
			mime_type_tmp_array = get_mine_type_array(mime_type_relation_array);
			break;
		case "sip":
		
			check_show = false;		
			break;
		case "note":
		
			check_show = false;	
			break;	
        case "group":				
			
			mime_type_tmp_array = get_mine_type_array(user_group_list);			
			break;			
	}
	if(check_show){
		var key;
		str_html +='<div class="mime_type_attr"><div class="dialog-menu animated2 fadeInUp menu fadeInUp-menu "><ul>';
		for(key in mime_type_tmp_array){
			
			str_html +='<li data-id="'+key+'" class="mime_type_item"><span class="li-font">'+mime_type_tmp_array[key]+'</span></li>';
		}
		str_html +='</ul><em class="menu-corner right"></em></div></div>';
		object_mime_type.append(str_html);
		object_mime_type.find(".mime_type_attr").show();
	}*/
}

//获取下一个信息 
function get_next_info(mime_type){
	
	var next_id;
	var last_element_id;
	var element_name = "";	
	var next_info = new Object();
	var mime_type_tmp_array = new Object();
	
	switch(mime_type){
		
		case "phone":		
			
			element_name = "ceditor_cato_phoneNumbers";
			mime_type_tmp_array = get_mine_type_array(mime_type_phone_array);
			break;
		case "email":
							
			element_name = "ceditor_cato_emails";	
			mime_type_tmp_array = get_mine_type_array(mime_type_mail_array);
			break;
		case "im":			
			
			element_name = "ceditor_cato_ims";
			mime_type_tmp_array = get_mine_type_array(mime_type_im_array);
			break;
		case "event":			
						
			element_name = "ceditor_cato_events";
			mime_type_tmp_array = get_mine_type_array(mime_type_event_array);			
			break;
		case "website":			
			
			element_name = "ceditor_cato_urls";
			mime_type_tmp_array = get_mine_type_array(mime_type_website_array);
			break;
		case "postal":
						
			element_name = "ceditor_cato_addresses";
			mime_type_tmp_array = get_mine_type_array(mime_type_postal_array);
			break;		
		case "relation":		
			
			element_name = "ceditor_cato_relations";
			mime_type_tmp_array = get_mine_type_array(mime_type_relation_array);
			break;
		case "sip":
		
			element_name = "ceditor_cato_sipPhones";
			if($("#"+element_name).css("display") == "block"){
				
				next_info = false;
			}else{
				
				mime_type_tmp_array = get_mine_type_array(mime_type_sip_array);
			}			
			break;
		case "note":
		
			element_name = "ceditor_cato_note";
			if($("#"+element_name).css("display") == "block"){
				
				next_info = false;
			}else{
				
				mime_type_tmp_array[0] = mime_type_object["note"];
			}		
			break;	
        case "group":
				
			element_name = "ceditor_cato_groups";
			mime_type_tmp_array = get_mine_type_array(user_group_list);			
			break;			
	}
	if(typeof next_info == "object"){
		
		last_element_id = $("#"+element_name).find(".ceditor_item_type input:last").val();
		last_element_id = last_element_id == undefined ? -1 : last_element_id;
		last_element_id = parseInt(last_element_id);
		for(next_id in mime_type_tmp_array){
			
			next_id = parseInt(next_id);
			if(next_id > last_element_id){
				
				break;
			}
		}
		if(mime_type != "relation" || (mime_type == "relation" && next_id > last_element_id)){
			
			next_info["index"] = next_id;
			if(mime_type == "group"){
				
				next_info["name"] = mime_type_object[mime_type];
			}else{
				
				next_info["name"] = mime_type_tmp_array[next_id];
			}
		}else{
			
			next_info = false;
		}
				
	}
	return next_info;
}

//下一个dt元素
function next_element_dt(mime_type){
	
	var str_html = "";
	var next_info = get_next_info(mime_type);
	if(next_info){
		
		str_html = '<dt cato="'+mime_type+'" class="ceditor_item_type ceditor_selector"><span class="attr_name">'+ next_info["name"] +'</span><input type="hidden" class="mime_type" name="'+mime_type+'_type[]" value="'+ next_info["index"] +'"/></dt>';
	}	
	return str_html;
}

//删除更多选项里面的选项
function delete_more_select(mime_type){
	
	var element_li = $("#M-Gen-menu-102 li");
	var li_len = element_li.length
	if(li_len > 0){
		
		element_li.each(function(){
							
			if($(this).attr("data-index") == mime_type){
				
				if(li_len == 1){
					
					element_li.parent().parent().parent().parent().hide();
				}
				$(this).remove();				
				return false;
			}							
		});
	}	
}

//更多选项
function get_more_select_element(){
	
	var key;
	var str_html = "<ul>";
	for(key in mime_type_object){
		
		str_html +='<li data-index="'+key+'" class="menu-item  "><span class="li-font">'+mime_type_object[key]+'</span></li>';				
	}
	str_html += "</ul>"
	return str_html;	
}

//联系人信息公共元素
function info_common_element(type){
	
	var str_html = "";
	var html_name = type =="new" ? "newbuild" : "edit";
	var empty_html_name = type =="new" ? "edit" : "newbuild";
	
	init_type();
	str_html +='<div class="contact-edit-container contact_editor" id="contact_editor" style="overflow:auto">';
	str_html +='<div class="contact-edit-avator"><a href="javascript:void(0);" id="contact_edit_avator" class="contact-avatars">'+
			   '<input id="ceditor_photo" type="hidden" /><input id="ceditor_thumbnail" type="hidden" /></a>'+
			   '<div class="avatars-area " style="visibility: hidden;"><span class="aa-func-link delete-link" style="display:none">'+ 
			   v_delete_portrait +'</span><span class="aa-func-link change-link">'+ v_add_portrait +'</span></div></div>';//头像
			   
	str_html +='<dl class="hasno-dt edit-contact clearfix edit-contact-dt1 "><div id="ceditor_name" style="display: block;"><dt>'+v_full_name+'</dt>'+
			   '<dd class="clearfix"><div class="edit-contact-handle-area" id="ceditor_base_more"><em class="ico-edit-contact-down  edit-contact-down-area">'+
			   '</em></div><div class="global-input"><input id="ceditor_displayName" name="display_name" placeholder="'+v_full_name+'" maxlength="40" type="text" />'+
			   '<span id="transmark" style="display: none; width: 0px; height: 0px;"></span></div></dd></div><div style="display: none;" id="ceditor_multName1">'+
			   '<dt>'+v_prefix+'</dt><dd class="clearfix"><div class="edit-contact-handle-area" id="ceditor_base_less"><em class="ico-edit-contact-down up"></em>'+
			   '</div><div class="global-input"><input id="ceditor_prefix" name="prefix" placeholder="'+v_prefix+'" maxlength="40" type="text" /></div></dd>'+
			   '<dt>'+v_family_name+'</dt><dd class="hasno-btn clearfix"><div class="global-input"><input id="ceditor_familyName" name="family_name" placeholder="'+
			   v_family_name+'" maxlength="40" type="text" /></div></dd><dt>'+v_middle_name+'</dt><dd class="hasno-btn clearfix"><div class="global-input">'+
			   '<input id="ceditor_middleName" name="middle_name" placeholder="'+v_middle_name+'" maxlength="40" type="text" /></div></dd><dt>'+v_given_name+'</dt>'+
			   '<dd class="hasno-btn clearfix"><div class="global-input"><input id="ceditor_givenName" name="given_name" placeholder="'+v_given_name+
			   '" maxlength="40" type="text" /></div></dd><dt>'+v_suffix+'</dt><dd class="hasno-btn clearfix"><div class="global-input">'+
			   '<input id="ceditor_suffix" name="suffix" placeholder="'+v_suffix+'" maxlength="40" type="text" /></div></dd><dt>'+v_nick_name+'</dt>'+
			   '<dd class="hasno-btn clearfix"><div class="global-input"><input id="ceditor_nick_name" name="nick_name" placeholder="'+v_nick_name+'" maxlength="40" type="text">'+
			   '</div></dd></div><dt>'+v_company+'</dt><dd class="hasno-btn clearfix"><div class="global-input"><input id="ceditor_company" cato="organizations" name="company"  placeholder="'+
			   v_company+'" maxlength="60" type="text" /></div></dd><dt>'+v_position+'</dt><dd class="hasno-btn clearfix"><div class="global-input">'+
			   '<input id="ceditor_title" cato="organizations" name="job_description" placeholder="'+v_position+'" maxlength="60" type="text"></div></dd></dl>';//基本信息		   

	str_html +='<dl class="edit-contact clearfix ceditor_cato_phoneNumbers" id="ceditor_cato_phoneNumbers"></dl>';//手机号码
	str_html +='<dl class="edit-contact clearfix ceditor_cato_emails" id="ceditor_cato_emails"></dl>';//邮箱
	str_html +='<dl class="edit-contact clearfix ceditor_cato_ims" id="ceditor_cato_ims" style="display:none"></dl>';//即时通讯
	str_html +='<dl class="edit-contact clearfix ceditor_cato_addresses" id="ceditor_cato_addresses" style="display:none"></dl>';//通讯地址
	str_html +='<dl class="edit-contact clearfix ceditor_cato_urls" id="ceditor_cato_urls" style="display:none"></dl>';//url
	str_html +='<dl class="edit-contact clearfix ceditor_cato_sipPhones" id="ceditor_cato_sipPhones" style="display:none"></dl>';//互联网通话
	str_html +='<dl class="edit-contact clearfix ceditor_cato_events" id="ceditor_cato_events" style="display:none"></dl>';//事件
	str_html +='<dl class="edit-contact clearfix ceditor_cato_relations" id="ceditor_cato_relations" style="display:none"></dl>';//关系
	str_html +='<dl class="hasno-dt edit-contact remark clearfix hide ceditor_cato_note" id="ceditor_cato_note" style="display:none"></dl>';//备注
	str_html +='<dl class="edit-contact clearfix ec-group" id="ceditor_cato_groups" style="display:none"></dl>';//群组
	str_html +='<div class="button-area clearfix" style=""><a href="javascript:void(0);" id="ceditor_addmore" class="ba-addmore button ceditor_selector_commmon toolmenu">'+
			   '<span class="ceditor_selector_commmon"> <i></i>'+v_more_select+'</span></a><div style="position: relative;">'+
			   '<div class="dialog-menu animated2 fadeInUp menu fadeInUp-menu" id="M-Gen-menu-102" style="width: 50%; left: 112px; position: absolute; bottom: 48px;display:none;">';
	str_html += get_more_select_element();	
	str_html +='<em class="menu-corner bottom"></em></div></div></div></div>';
	$("#"+empty_html_name).html("");
	$("#"+html_name).html(str_html);
	add_element("phone");
	add_element("email");
}

//添加元素 昵称，公司，备注，头像，sip
function add_element(mime_type){		
	
	var str_html = next_element_dt(mime_type);
	if(str_html.length < 1){
		
		return str_html;
	}
	var next_id;
	var last_element_id;
	var max_length = 40;
	var input_name = "";
	var element_name = "";
	var placeholder_name = "";
	var extend_element_html = "";
	str_html +='<dd class="clearfix"><div class="edit-contact-handle-area"><em class="ico-edit-contact-del ceditor_del"></em></div><div class="global-input">';
	input_name = mime_type + "[]";
	placeholder_name = v_mime_type_object[mime_type];
	switch(mime_type){
		
		case "phone":		
			
			element_name = "ceditor_cato_phoneNumbers";				
			break;
		case "email":
		
			max_length = 60;			
			element_name = "ceditor_cato_emails";			
			break;
		case "im":			
			
			element_name = "ceditor_cato_ims";		
			break;
		case "event":			
			
			placeholder_name = v_date;
			element_name = "ceditor_cato_events";
			str_html +='<input type="text" class="ceditor_input ceditor_item event_date" name="'+input_name+'" value="" placeholder="'+placeholder_name+'" readonly="readonly" onclick="WdatePicker({lang:\'zh-cn\',isShowWeek:true,dateFmt:\'yyyy-MM-dd\'})" />';
			break;
		case "website":			
			
			max_length = 1024;
			element_name = "ceditor_cato_urls";
			break;
		case "postal":
			
			max_length = 255;
			element_name = "ceditor_cato_addresses";
			break;		
		case "relation":			
			
			input_name = "relation";			
			element_name = "ceditor_cato_relations";	
			break;
		case "sip":
			
			input_name = "sip";
			element_name = "ceditor_cato_sipPhones";
			break;
		case "note":
			
			max_length = 255;
			input_name = "note";			
			element_name = "ceditor_cato_note";
			break;	
        case "group":
				
			element_name = "ceditor_cato_groups";
			str_html +='<select name="'+input_name+'" class="ceditor_cato_select">';
			last_element_id = $("#"+element_name).find(".ceditor_item_type input:last").val();
			last_element_id = last_element_id == undefined ? -1 : 0;
			last_element_id = parseInt(last_element_id);
			for(next_id in user_group_list){
				
				next_id = parseInt(next_id);
				if(next_id > last_element_id){
					
					str_html += '<option value="'+next_id+'">'+user_group_list[next_id]+'</option>';
				}
			}
			str_html +='</select>';
			break;			
	}
	if(mime_type != "group" && mime_type != "event"){
		
		str_html +='<input type="text" class="ceditor_input ceditor_item" name="'+input_name+'" value="" placeholder="'+placeholder_name+'" maxlength="'+max_length+'" />';
	}
	
	str_html += extend_element_html + '</div></dd>';
	$("#"+ element_name).append(str_html);
	$("#"+ element_name).show();	
}

//删除元素
function remove_element(delete_element_object){
	
	var element_dd = delete_element_object.parent().parent();
	element_dd.prev("dt").remove();
	element_dd.remove();
}

$(function() {
	
	set_height();
	list_string = $("#scroll_container_contact1").html();
	input_placeholder_init();
	
	//滚动事件
	$("#contactListFrame").on("scroll",function(){
		
		if(ajax_use && $(this).scrollTop() + display_height > total_height*(1- buttom_percent)){
			
			ajax_contact_list(list_type,false);		
		}
	});
	
	$("#clear_search").click(function(){
		
		$("#contact_search_input").val("");
		search();
	});
	
	//搜索输入后
	$('#contact_search_input').keyup(function(){		
		
		search();	
	});	

   /* var currentHeight=$(window).height()-70+"px";
    if($("#kcloud_right_frame").css("display")=="none") {
		
        $("#kcloud_window").css("height", currentHeight);
    }
    if($(window).width()<=480) {
		
        $("#merge_contact").hide();
    }*/

	//通讯录清空，列表，回收站，导入导出等
	$('.js_item').click(function(){
		
		var ctype = $(this).parent().attr('ctype');
		switch(ctype){
			case 'list':				
			case 'recycle':
				
				var html_str = "";
				var check_cache = false;
				source_is_recy = is_recy;
				is_recy = ctype == 'list' ? 0 : 1;							
				list_type = "list";
				if(source_is_recy != is_recy){
					
					$(".default-hint").css("display","table");
					$(".default-hint .hint-content").show();
					$("#contact-detail-padd2,#create-contact-padd").hide();
				}
				if(is_recy == 0 && list_total_count > 0 && list_string.length > 0){		
					
					check_cache = true;
					total_count = list_total_count;
					current_page = list_current_page;
					html_str = list_string;
					
					$("#scroll_container_contact1").html(list_string);
					$("#clear_search").hide();		
					display_process();
				}else if(is_recy == 1 && recy_list_total_count > 0 && recy_list_string.length > 0){
								
					check_cache = true;
					total_count = recy_list_total_count;
					current_page = recy_list_current_page;
					html_str = recy_list_string;				
				}else{
										
					$("#scroll_container_contact1").html("");
					ajax_contact_list(list_type,true);
				}
									
				if(check_cache){
					
					$("#scroll_container_contact1").html(html_str);									
					$("#scroll_container_contact1 .item_checkbox").each(function(){
						
						$(this).css("display","");
					});
				}
				$("#clear_search").hide();
				display_process();
				break;
			case 'clearAll':
				
				clearAll();
				break;
			case 'export':
				
				$('.export').show();
				$('.import,.contact-merge-panel,#merge-panel1,#contact-padd2').hide();
				break;
			case 'import':
			default:
				
				$('.import').show();//
				$('.export,.contact-merge-panel,#merge-panel1,#contact-padd2').hide();
				break;				
		}

	});	
	
	$('.js_switch').click(function(e){
		
		 e.stopPropagation();
		$(this).toggleClass('switch-close');//fliter
		
	});

	//左侧点击联系人
    $(document).on("click","#scroll_container_contact1 .list_selection_item",function(e){

		$("#type_selector_0").hide();
        $(".list_selection_item_active").removeClass("list_selection_item_active");
        $("#contact_detail_item_list .contact-phoneico,#contact_detail_item_list .contact-details").show();
        //适用手机
        if($("#kcloud_right_frame").css("display")=="none"){
			
            $("#kcloud_right_frame").show();
            $("#kcloud_left_frame").hide();
        }
        e.stopPropagation();
		var html_str;
		var contact_name;		
		var contact_id = $(this).attr("data-id");
		var check_detail_info = false;
        $("#contact_detail_item_list .cd-div").remove();				
        if ($('#contact_select_mode').css("display") == "none") {
			
			check_detail_info = true;
			get_detail(contact_id);			
        }else if ($('.active1').length == 1) {
			
            if ($(this).find(".checkbox").hasClass('active1')) {
				
                $('.default-hint,#contact_operate_bar').show();
                $(this).find(".checkbox").removeClass('active1');
                $('.checkbox').css('display','')
                $('#contact_select_mode,#contact_sel_all_button').hide();
                $('#contact_selected_container').find('li').remove();
            } else {
				
                $(this).find(".checkbox").addClass('active1');
                $(this).siblings('li').find('.checkbox').css('display','block');				
			    contact_name = $(this).find(".contact-nickname").text();
				html_str = get_select_html(contact_id,contact_name);	               
                $('#contact_selected_container').append(html_str);
            }
			
        } else if ($(this).find(".checkbox").hasClass('active1')) {
			
            $(this).find(".checkbox").removeClass('active1');
            $(this).siblings('li').find('.checkbox').css('display','block');
           
            $('#contact_selected_container').find('li').each(function () {
				
                if (contact_id == $(this).attr('id').split("selected_")[1]) {
					
                    $(this).remove();
                }
            });
        } else {
			
            $(this).find(".checkbox").addClass('active1');
            $(this).siblings('li').find('.checkbox').css('display','block');			
			contact_name = $(this).find(".contact-nickname").text();
			html_str = get_select_html(contact_id,contact_name);          
            $('#contact_selected_container').append(html_str);
        }
		if(!check_detail_info){
			
			select_total_count();
			button_display();			
		}
			  
		$(this).addClass("list_selection_item_active");
    });

    /* 左侧联系人选中取消选中 */
    $(document).on("click","#scroll_container_contact1 .checkbox",function (e) {
		
        e.stopPropagation();
		var html_str;
        var _this = $(this);
		var contact_id;
		var contact_name;		
		var parent_li = _this.parents('li');
		contact_id = parent_li.attr('data-id');
		contact_name = parent_li.find('.contact-nickname').text();
		
        if ($('#contact_select_mode').css("display") == "none") {
			
            $('.default-hint,#contact_detail,.export,.import').hide();
            _this.addClass('active1');
            parent_li.siblings('li').find('.checkbox').css('display','block')
            $('#contact_selected_container').find('li').remove();
            $('#contact_select_mode,#contact_sel_all_button,#contact_sel_bar').show();
            $('#contact_operate_bar,#contact-detail-padd2,#create-contact-padd,#merge-panel1,.contact-merge-panel,#edit-contact-padd,#contact-panel,.contact_detail_right').hide();
            
			html_str = get_select_html(contact_id,contact_name);			
            $('#contact_selected_container').append(html_str);
        } else if ($('.active1').length == 1) {
			
            if (_this.hasClass('active1')) {
				
                $('.default-hint,#contact_operate_bar').show();
                _this.removeClass('active1');
                parent_li.siblings('li').find('.checkbox').css('display','')
                $('#contact_select_mode,#contact_sel_bar').hide();
                $('#contact_selected_container').find('li').remove();
            } else {
				
                _this.addClass('active1');
                parent_li.siblings('li').find('.checkbox').css('display','block');				
				html_str = get_select_html(contact_id,contact_name);
                $('#contact_selected_container').append(html_str);
            }
        } else if (_this.hasClass('active1')) {
			
            _this.removeClass('active1');
            parent_li.siblings('li').find('.checkbox').css('display','block')
            $('#contact_selected_container').find('li').each(function () {
				
                if (contact_id == $(this).attr('id').split("selected_")[1]) {
					
                    $(this).remove();
                }
            });
        } else {
		
            _this.addClass('active1');
            parent_li.siblings('li').find('.checkbox').css('display','block');            
			html_str = get_select_html(contact_id,contact_name);
            $('#contact_selected_container').append(html_str);
        }		
		select_total_count();
		button_display();
		
        //适用手机
        if($("#kcloud_right_frame").css("display")=="none"){
            $("#kcloud_right_frame").show();
            $("#kcloud_left_frame").hide();
        }
    });

    /* 左侧联系人全选 */
    $('#contact_sel_all_button').click(function () {
		
		var contact_id;
		var contact_name;
		var html_str = "";
        $('.checkbox').addClass('active1');
		$('#contact_selected_container').html("");
		$('ul .list_selection_item').each(function(){
			
			contact_id = $(this).attr("data-id");
			contact_name = $(this).find(".contact-nickname").text();
			html_str += get_select_html(contact_id,contact_name);			
		});
		$('#contact_selected_container').html(html_str);	
		select_total_count();
    });

    /* 取消 */
    $("#M-Gen-button-122").click(function () {
		
        $('.checkbox').removeClass('active1');
        $('.checkbox').css('display','');		
        $('.default-hint,#contact_operate_bar').show();
        $('#contact_select_mode,#contact_sel_bar,#restore_contact').hide();
        $('#contact_selected_container').find('li').remove();
    });

    //下方按钮
    $("#merge_contact").click(function () {
		
        $(".contact-merge-panel").show();
        $(".default-hint,.import,.export,#create-contact-padd2,#merge-panel,#contact-padd2,#create-contact-padd,#edit-contact-padd,#contact-panel,#merge-panel1,#contact-detail-padd2,.contact_detail_right,.contact_detail_right").hide();

        //适用手机
        if($("#kcloud_right_frame").css("display")=="none"){
            $("#kcloud_right_frame").show();
            $("#kcloud_left_frame").hide();
        }
    });
		
	//开始获取合并联系人按钮
    $("#button-conatact").click(function () {
		
		$("#already_merge_count").text("0");
		$(".cmcc-check-first .checkbox").removeClass("active1");
		if($(".merge-contact").hasClass("default")){
			
			$(".merge-contact").removeClass("default");		
		}
		get_merge_list();        
    });
	
	//忽略联系人按钮
    $(".ignore-contact").click(function () {
		
        $("#merge-panel1,.merge-contacts").show();
        $("#contact-panel").hide();
		$(".cmcc-check-first .checkbox").removeClass("active2");
    });


	//更多操作按钮
    $("#contact_more_operate").click(function (e) {
    	e.stopPropagation();
    	$("#contact-panel").hide();
        
    	if($("#type_selector_0").css('display')=='none'){
    		
    		$("#type_selector_0").show();
    		
    	}else{
    		
    		$("#type_selector_0").hide();
    	}
    } );
	
    /*新建联系人*/
    $("#create_contact").click(function(){
    	
		detail_id = 0;
		info_common_element("new");
        $(".default-hint,.export,.import,.merge-contacts,.contact-merge-panel,#contact-padd2,#edit-contact-padd,#contact-panel,#contact-detail-padd2,.contact_detail_right").hide();
        $("#merge-panel1,#create-contact-padd").show();
        $("#create-contact-padd .global-input").find("input").val("");

        if($("#contact_detail_item_list .cd-div").eq(0).find(".cd-field").text()==""){
			
            $("#contact_detail_item_list .contact-phoneico").hide();
        }
		
		input_placeholder_init();
        //适用手机
        if($("#kcloud_right_frame").css("display")=="none"){
            $("#kcloud_right_frame").show();
            $("#kcloud_left_frame").hide();
        }
    });
	
	//取消添加
	$(document).on("click","#M-Gen-button-10011",function(){
		
		$("#create-contact-padd").hide();
		$(".default-hint").css("display","table");
		$(".default-hint .hint-content").show();
		$("#contact_editor").html("");
	});
	
	//添加联系人相关
    $(document).on('click',"#ceditor_base_more",function(){
		
        $("#ceditor_multName1").show();
        $(".ico-edit-contact-down .up").css("background-position", "-225px -297px");
        $("#ceditor_name").hide();
    });

	//输入数据
	$(document).on("keyup",".ceditor_item",function(){

		var mime_type;
		var check_add = false;
		var mime_type_value = $(this).val();
		var dd_element = $(this).parent().parent("dd");
		var dt_element = dd_element.prev("dt");
		var current_id = dt_element.find("input").val();
		var dl_element = dt_element.parent("dl");		
		var last_id = dl_element.find(".ceditor_item_type input:last").val();
		if(last_id == current_id && dl_element.find(".ceditor_item:last").val().length > 0 && dl_element.find(".ceditor_item:last").val() != dl_element.find(".ceditor_item:last").attr("placeholder")){
			
			mime_type = dt_element.attr("cato");
			switch(mime_type){
			
				case "phone":
					
					if(is_phone(mime_type_value)){
						check_add = true;
					}
					break;
				case "email":
				
					if(is_mail(mime_type_value)){
						check_add = true;
					}
					break;
				default:
					
					check_add = true;
					break;				
			}
		}		
		
		if(check_add){
			
			check_add = mime_type == "note" || mime_type == "sip" ? false : true;
			if(check_add){
				
				add_element(mime_type);
				input_placeholder_init();
			}			
		}
	});
	
	//改变值
	$(document).on("change",".ceditor_cato_select",function(){
		
		var mime_type;
		var check_add = false;
		var mime_type_value = $(this).val();
		var dd_element = $(this).parent().parent("dd");
		var dt_element = dd_element.prev("dt");
		var current_id = dt_element.find("input").val();
		var dl_element = dt_element.parent("dl");		
		var last_id = dl_element.find(".ceditor_item_type input:last").val();
		
        if(last_id == current_id && dl_element.find(".ceditor_cato_select:last").val() == mime_type_value){
			
			mime_type = dt_element.attr("cato");
			add_element(mime_type);	
			input_placeholder_init();
		}
	});
	
	//改变值
	$(document).on("focus",".event_date",function(){

		focus_total_count++;
		if(focus_total_count == 2){

			var mime_type;
			var check_add = false;
			var mime_type_value = $(this).val();
			var dd_element = $(this).parent().parent("dd");
			var dt_element = dd_element.prev("dt");
			var current_id = dt_element.find("input").val();
			var dl_element = dt_element.parent("dl");		
			var last_id = dl_element.find(".ceditor_item_type input:last").val();
			focus_total_count = 0;
			$(this).css({"color":"#000","font-weight":"bold"});
			if(last_id == current_id && dl_element.find(".ceditor_item:last").val() == mime_type_value){
				
				mime_type = dt_element.attr("cato");
				add_element(mime_type);
				input_placeholder_init();
			}			
		}
	});
	
	//删除
	$(document).on("click",".ceditor_del",function(){
				
		if($(this).parent().parent().parent().find("dt").length > 1){
			
			remove_element($(this));
		}else{
			
			$(this).parent().siblings(".global-input").find(".ceditor_input").val("");
		}		
	});

	//光标焦点获取最前面
	$(document).on("click",".ceditor_item",function(){
		if($(this).val()==$(this).attr("placeholder")){
			$temp = $(this).val();
			$(this).val("").focus().val($temp);
		}

	});


	//删除input输入值
	$(document).on("keyup",".ceditor_input",function(){

		if($(this).parents("dl").find("dt").length > 1&&$(this).val()==""){
			var dd_element=$(this).parents("dd");
			var next_dt=$(this).parents("dd").next("dt");
			var next_dd=next_dt.next("dd");
			var next_input=next_dd.find(".ceditor_input");
			if(next_input.val()==""||next_input.val()==next_input.attr("placeholder")){
				next_dt.remove();
				next_dd.remove();
			}

		}
	});

	//点击选择属性显示
	$(document).on("click",".ceditor_selector .attr_name",function(e){
		e.stopPropagation();
		var _this = $(this).parent("dt");
		var mime_type = _this.attr("cato");
		show_mime_type_attr(_this,mime_type);				
	});

	//修改属性
	$(document).on("click",".mime_type_item",function(){
		
		var _this = $(this);
		var attr_id = _this.attr("data-id");
		var attr_name = _this.children("span").text();
		var mime_type_attr_element = _this.parent().parent().parent();
		
		mime_type_attr_element.siblings(".attr_name").text(attr_name);
		mime_type_attr_element.siblings("input").val(attr_id);
		mime_type_attr_element.hide();
	});
	
	//编辑联系人相关
    $(document).on('click',"#ceditor_base_less",function () {
        $("#ceditor_multName1").css("display", "none");
        $("#ceditor_name").show();
    });

	//编辑联系人头像鼠标事件
    $(".contact-edit-avator").mouseover(function () {
        $(".avatars-area").css("visibility", "visible")
    });
	//编辑联系人头像鼠标事件
    $(".contact-edit-avator").mouseout(function () {
        $(".avatars-area").css("visibility", "hidden")
    });

    $(document).click(function () {
        
        $(".merge-complete a,#type_selector_0,#M-Gen-menu-102,.mime_type_attr").hide();
    });
	
    //联系人名字的变化
	$(document).on("change","#ceditor_displayName",function (){
		
		var myReg;
		var name_len;
		var display_name_array;
		var base_element = $("#contact_editor").find(".global-input");
		var display_name = trim(base_element.find("#ceditor_displayName").val());		
		if(display_name != ""){
			
			myReg = /^[\u4e00-\u9fa5]+$/;   //判断汉字的正则表达式
			if (myReg.test(display_name)){    //输入的是汉字
				
				name_len = display_name.length;
				if(name_len == 1 || name_len >= 5){
					
					base_element.find("#ceditor_givenName").val(display_name);					
				}else{
					
					display_name_array = display_name.split("");
					switch(name_len){
						
						case 2:
							
							base_element.find("#ceditor_familyName").val(display_name_array[0]);
							base_element.find("#ceditor_givenName").val(display_name_array[1]);
							break;
						case 3:
							
							base_element.find("#ceditor_familyName").val(display_name_array[0]);
							base_element.find("#ceditor_givenName").val(display_name_array[1] + display_name_array[2]);
							break;
						case 4:
							base_element.find("#ceditor_familyName").val(display_name_array[0] + display_name_array[1]);
							base_element.find("#ceditor_middleName").val(display_name_array[2]);
							base_element.find("#ceditor_givenName").val(display_name_array[3]);
							break;						
					}
				}							
			}else{
			
				base_element.find("#ceditor_givenName").val(display_name);
			}
		}
	});
	
    //姓发生变化
    $(document).on('change',"#ceditor_prefix,#ceditor_familyName,#ceditor_middleName,#ceditor_givenName,#ceditor_suffix",function (){
       
    	contact_display_name_show();
    });
	
    //确定键是否为数字
    $(document).on('click','.pulse-dialog .btn-ct,.close_btn',function (){
	
        $(".pulse-dialog").hide();
    });
	
	//新建联系人信息
	$("#M-Gen-button-101").click(function(){
		
		contacts_submit("add");
	});
	
	//编辑联系人的删除键
    $("#M-Gen-button-133,#M-Gen-button-124,#M-Gen-button-116").click(function () {
		
		var dialog_text = is_recy == 0 ? v_is_recycle : v_is_delete;			
		$("#M-Gen-dialog-123 .dialog-tips").text(dialog_text);
			
        $("#M-Gen-dialog-123,#__mask1__").show();
        $(".pulse-dialog,.contact_detail_right").hide();
        //适应手机
        if($("#kcloud_left_frame").css("display")=="none"){
            $(".dialog").css({"width":"auto","margin-left":"-150px","top":"160px"});
        }
    });

    $(".close_btn").click(function () {
        $("#M-Gen-dialog-123,#__mask1__").hide();
    });

    $(".btn-ct").click(function () {
        $("#M-Gen-dialog-123,#__mask1__").hide();
    });
	
	//取消编辑及新建
    $("#M-Gen-button-114").click(function () {
        $("#edit-contact-padd").hide();
        $("#contact-padd2,#contact_detail").show();
    });
	
    //编辑联系人键是否为数字
    $(document).on('click','.pulse-dialog .btn-ct,.close_btn',function (){
        $(".pulse-dialog").hide();
    });

    //已有联系人进行编辑
    $(document).on('click','.icobtn-edit',function (e){

        e.stopPropagation();		
		var key;
		var i = 0;		
		var base_info;
		var attr_id;
		var attr_name;
		var mime_value;
		var dt_element;
		var element_name;
		var current_mime_array;
		var current_mime_object;
		var current_contact_object;		
		var company_info = new Object();
		var nickname_info = new Object();		
		var note_info = new Object();		
		var sip_info = new Object();
		var mime_type_tmp_array = new Array();
		
    	info_common_element("edit");
        $("#edit-contact-padd").show();
        $(".contact_detail_right,#contact_detail,#contact-detail-padd2").hide();
		$("#contact-padd2").hide();
		current_contact_object = jsondata[detail_id];
		base_info = current_contact_object.base;		
		$("#ceditor_displayName").val(base_info.display_name);
		$("#ceditor_prefix").val(base_info.prefix);
		$("#ceditor_familyName").val(base_info.family_name);
		$("#ceditor_middleName").val(base_info.middle_name);
		$("#ceditor_givenName").val(base_info.given_name);
		$("#ceditor_suffix").val(base_info.suffix);
		
		if(current_contact_object.organization.length > 0){
			
			company_info = current_contact_object.organization[0];
			$("#ceditor_company").val(company_info.company);
			$("#ceditor_title").val(company_info.job_description);
		}
		if(current_contact_object.nickname.length > 0){
			
			nickname_info = current_contact_object.nickname[0];		
			$("#ceditor_nick_name").val(nickname_info.nick_name);
		}		
		
		for(key in current_contact_object){
			
			if(key == "base" || key == "organization" || key == "nickname" || key == "note" || key == "sip" || key=="group"){
				
				continue;
			}
			current_mime_array = current_contact_object[key];
			if(current_mime_array.length > 0){
				
				i = 0;
				if(!(key == "phone" || key == "email")){
					
					delete_more_select(key);
				}				
				for(i in current_mime_array){
					
					if(!(i == 0 && (key == "phone" || key == "email"))){
						
						add_element(key);
					}					
					current_mime_object = current_mime_array[i];
					attr_id = parseInt(current_mime_object["type_name"]);
					
					switch(key){	
						
						case "phone":						
							
							mime_value = current_mime_object["phone"];
							mime_type_tmp_array = v_mime_type_phone_array;
							element_name = "ceditor_cato_phoneNumbers";				
							break;
						case "email":						
									
							mime_value = current_mime_object["email"];
							mime_type_tmp_array = v_mime_type_mail_array;
							element_name = "ceditor_cato_emails";			
							break;
						case "im":			
							
							mime_value = current_mime_object["chat_account"];
							mime_type_tmp_array = v_mime_type_im_array;
							element_name = "ceditor_cato_ims";		
							break;
						case "event":						
							
							mime_value = current_mime_object["start_date"];
							mime_type_tmp_array = v_mime_type_event_array;
							element_name = "ceditor_cato_events";							
							break;
						case "website":			
							
							mime_value = current_mime_object["url"];
							mime_type_tmp_array = v_mime_type_website_array;
							element_name = "ceditor_cato_urls";
							break;
						case "postal":
							
							mime_value = current_mime_object["formatted_address"];
							mime_type_tmp_array = v_mime_type_postal_array;
							element_name = "ceditor_cato_addresses";
							break;		
						case "relation":			
							
							mime_value = current_mime_object["name"];
							mime_type_tmp_array = v_mime_type_relation_array;	
							element_name = "ceditor_cato_relations";	
							break;						
						case "group":
								
							mime_value = current_mime_object["name"];
							mime_type_tmp_array = user_group_list;
							element_name = "ceditor_cato_groups";							
							break;			
	
					}
					if(typeof mime_type_tmp_array[attr_id] != "undefined"){
						
						attr_name = mime_type_tmp_array[attr_id];
					}else{
						
						attr_name = mime_type_tmp_array[0];
					}
					dt_element = $("#"+element_name+ " dt:last");
					dt_element.find("input").val(attr_id);
					dt_element.find(".attr_name").text(attr_name);					
					$("#"+element_name+ " dd:last").find(".ceditor_input").val(mime_value);
				}
				add_element(key);
			}			
		}
		
		if(current_contact_object.sip.length > 0){
			
			sip_info = current_contact_object.sip[0];
			add_element("sip");
			$("#ceditor_cato_sipPhones .ceditor_input").val(sip_info.sip_address);
			delete_more_select("sip");			
		}
		if(current_contact_object.note.length > 0){
			
			note_info = current_contact_object.note[0];
			add_element("note");						
			$("#ceditor_cato_note .ceditor_input").val(note_info.note);
			delete_more_select("note");
		}
		input_placeholder_init();
    });

	//编辑联系人确认键
    $("#edit-contact-padd .default").click(function(){		
        
        contacts_submit("edit");
    });
	
	//打开姓名下拉获取更多下拉信息
    $("#ceditor_base_more-area").click(function () {
		
        $(".ico-edit-contact-down .up").css("background-position", "-225px -297px")
        $("#ceditor_multName").show();
        $(".edit-contact-area").hide();
    });
	
	//关闭姓名下拉信息
    $("#ceditor_base_less-area").click(function () {
        $("#ceditor_multName").css("display", "none");
        $(".edit-contact-area").show();
    });
	
	//显示更多选项列表
    $(document).on('click',"#ceditor_addmore",function(e){
		
    	e.stopPropagation();		
        if($("#M-Gen-menu-102").css("display")=="block"){
            $("#M-Gen-menu-102").hide();
        }else{
            $("#M-Gen-menu-102").show();
        }       
        $("#type_selector_0").hide();
      
    });

	//添加选项
	$(document).on('click',".menu-item",function(e){
		
		var dl_element_name = "";
		var dl_element_html = "";
		var more_element_html = "";
    	var mime_type = $(this).attr("data-index");
		switch(mime_type){
			
			case "website":
				
				dl_element_name = "ceditor_cato_urls";
				break;
			case "postal":
				
				dl_element_name = "ceditor_cato_addresses";
				break;
			case "sip":
			
				dl_element_name = "ceditor_cato_sipPhones";
				break;
			case "note":
			
				dl_element_name = "ceditor_cato_note";
				break;
			default:
				
				dl_element_name = "ceditor_cato_"+ mime_type +"s";
				break;
		}
		
		dl_element_html = $("#"+dl_element_name).prop("outerHTML");		
		$("#"+dl_element_name).remove();		
		$("#contact_editor").append(dl_element_html);		
		add_element(mime_type);
		$(this).remove();
		if($(".menu-item").length <= 0){
			
			$("#ceditor_addmore").parent().remove();				
		}else{
			
			more_element_html = $("#ceditor_addmore").parent().prop("outerHTML");
			$("#ceditor_addmore").parent().remove();
			$("#contact_editor").append(more_element_html);
		}
		input_placeholder_init();
    });
	
    $("#M-Gen-button-137").click(function () {
		
        $('#M-Gen-dialog-138').hide();
        $("#global_popup,#group-build,#M-Gen-dialog-106").show();
    		
    });
	
	//电话号码合法性判别
	$(document).on("blur","#ceditor_cato_phoneNumbers .ceditor_input",function(e){
		var phone = $(this).val();
		if(phone.length > 0 && is_phone(phone) == false){		
						
			ajax_submit = false;
			layer.msg(v_e_input_phone,{icon:0,skin: 'layer-ext-moon'});
			$(this).val("");
		}else{
			
			ajax_submit = true;
		}
	});
	
	//邮件地址合法性判别
	$(document).on("blur","#ceditor_cato_emails .ceditor_input",function(){
		
		var email = $(this).val();
		if(email.length > 0 && is_mail(email) == false){
			
			ajax_submit = false;
			layer.msg(v_e_input_email,{icon:0,skin: 'layer-ext-moon'});
			$(this).val("");
		}else{
			
			ajax_submit = true;
		}
	});
	
	//关闭
    $(".close_btn").click(function () {
        $("#global_popup,#M-Gen-dialog-138").hide();
    });
	
	//联系人删除或者移入回收站
    $("#M-Gen-button-1221").click(function(){
		if(is_recy == 0){
			
			contact_move_recycle();      
		}else{
			
			contact_delete();
		}		  
    });
	
	//还原联系人
	$("#restore_contact,.restore_contact").click(function(){
		
		contact_recover();
	});
	
    //合并全选
    $(".cmcc-list .checkbox").click(function(){
		
		var _this = $(this);
        var merge_total_count = 0;
        if(_this.hasClass("active1")){
						
            _this.removeClass("active1");
            $(".cmcc-list .cmcc-check-area").removeClass("active2");
			$("#already_merge_count").text(merge_total_count);
            $(".merge-contact").removeClass("default");
        }
        else{
			
			_this.addClass("active1");
			merge_total_count = $("#possible_num").text();            
            $(".cmcc-list .cmcc-check-area").addClass("active2");
            $('.cm-contact-item em').addClass("active2 cmcc-check-area");
            $(".merge-contact").addClass("default");
            $(".merge-contact").removeClass("disabled");			
            $("#already_merge_count").text(merge_total_count);
        }
    });
	
	//合并左边点击选择或者取消选择
	$(document).on("click",".cmcc-list .cm-contact-item",function(){
		
		var _this = $(this);	
		if(_this.find("em").attr("id") != undefined){
			
			var change_count = 0;
			var group_total_count = 0;
			var group_select_count = 0;
			var parent_element = _this.parent();
			var merge_total_count = parseInt($("#already_merge_count").text());
			_this.find("em").removeClass("cmcc-check-area_hover");
			group_total_count = parent_element.find(".cm-contact-item").length;
			if(_this.find("em").hasClass("active2")){
				
				_this.find("em").removeClass("active2");
				group_select_count = parent_element.find(".active2").length;				
				if(group_select_count == 1){
					
					change_count = -1;
				}
				if(group_total_count == group_select_count + 1){
					
					_this.parent().siblings(".fl-r-block").children(".fl-r").removeClass("active2");
				}
			}else{
				
				_this.find("em").addClass("active2");
				group_select_count = parent_element.find(".active2").length;				
				if(group_select_count == 2){
					
					change_count = 1;
				}
				if(group_select_count == group_total_count){
					
					_this.parent().siblings(".fl-r-block").children(".fl-r").removeClass("cmcc-check-area_hover");
					_this.parent().siblings(".fl-r-block").children(".fl-r").addClass("active2");
				}
			}
			
			change_merge_contact(merge_total_count,change_count);
		}		
	});
	
	//合并右边点击选择或者取消选择
    $(document).on("click",".cmcc-list .fl-r-block",function(){
		
		var _this = $(this);
		var change_count = 0;		
		var item_element = _this.siblings(".fl-l").children(".cm-contact-item");
		var merge_total_count = parseInt($("#already_merge_count").text());
		if(_this.find(".fl-r").hasClass("active2")){
			
			change_count = -1;
			_this.find(".fl-r").removeClass("active2");
			item_element.find(".active2").each(function(){
				
				$(this).removeClass("active2");
			});
		}else{
			
			var group_select_count = 0;
			var group_total_count = item_element.length;
			_this.find(".fl-r").addClass("active2");
			if(group_total_count > 2){
				
				group_select_count = item_element.find(".active2").length;
				if(group_select_count < 2){
					
					change_count = 1;
				}
				item_element.find("em").each(function(){
					
					if(!$(this).hasClass("active2")){
						
						$(this).addClass("active2");
					}					
				});
			}else{
				
				change_count = 1;
			}			
		}
		change_merge_contact(merge_total_count,change_count);	
	});

	//合并联系人边框变色
    $(document).on("click",".left-contact-item",function(){
		
		var nick_name = "";
		$(".left-contact-item").each(function(){
			
			if($(this).hasClass("checked")){
				
				$(this).removeClass("checked");
				$(this).find(".msc-index-num").addClass("two-status");
			}
		});
		$(this).addClass("checked");
		$(this).find(".msc-index-num").removeClass("two-status");
		use_contact_id = $(this).attr("array-index");
		$(".msc-title .msc-index-num").text($(this).children(".msc-index-num").text());
		$(this).find(".msc-data-mes").each(function(){
			
			nick_name = $(this).text();
			return false;
		});
		
		$(".multi_merge .msc-dic-container").each(function(){
			
			$(this).text(nick_name);
			return false;
		});		
    });

    //合并重复联系人
    $(document).on("click",".merge-contact",function(){
		
		select_merge_list = new Array();		
		merge_id_array = new Array();
		select_merge_list = new Array();
		current_merge_select = new Object();
		merge_complete_count = 0;
		merge_igonre_total_count = 0;		
        if($(this).hasClass("default")){		
			
			var i = 0;
			var k = 0;		
			var phone_array;
			var check_valid;			
			var tmp_select_id;
			var tmp_merge_type;	
			var tmp_select_row;			
			var tmp_select_group_list;					
			
			$(".cmcc-data-container .cmcc-list").each(function(){
				
				check_valid = false;				
				tmp_select_group_list = new Object();							
				if($(this).children(".fl-r-block").children(".fl-r").hasClass("active2")){
					
					check_valid = true;
					tmp_merge_type = $(this).attr("merge-type");
					$(this).children(".fl-l").children(".cm-contact-item").each(function(){						
												
						phone_array = new Array();
						tmp_select_row = new Object();
						tmp_select_id = $(this).attr("data-id");
						tmp_select_row["id"] = tmp_select_id;
						tmp_select_row["merge_type"] = tmp_merge_type;
						tmp_select_row["name"] = $(this).find(".contact-nickname").text();
						if(tmp_merge_type == "email"){
							
							tmp_select_row["email"] = $(this).find(".contact-email").text();
						}											
						if($(this).find(".contact-num").length > 0){
							
							k = 0;
							$(this).find(".contact-num").each(function(){
								
								phone_array[k] = $(this).text();
								k++;
							});							
						}
						tmp_select_row["phone_array"] = phone_array;
						tmp_select_group_list[tmp_select_id] = tmp_select_row;
					});				
				}else{
					
					if($(this).find("em").length > 0){
						
						var j = 0;
						check_valid = false;
						tmp_merge_type = $(this).attr("merge-type");
						$(this).children(".fl-l").children(".cm-contact-item").each(function(){							
							
							if($(this).find(".active2").attr("id") != undefined){
								
								phone_array = new Array();
								tmp_select_row = new Object();
								tmp_select_id = $(this).attr("data-id");
								tmp_select_row["id"] = tmp_select_id;
								tmp_select_row["merge_type"] = tmp_merge_type;
								tmp_select_row["name"] = $(this).find(".contact-nickname").text();
								if(tmp_merge_type == "email"){
									
									tmp_select_row["email"] = $(this).find(".contact-email").text();
								}											
								if($(this).find(".contact-num").length > 0){
									
									k = 0;
									$(this).find(".contact-num").each(function(){
										
										phone_array[k] = $(this).text();
										k++;
									});							
								}
								tmp_select_row["phone_array"] = phone_array;
								tmp_select_group_list[tmp_select_id] = tmp_select_row;
								j++;
							}
						});
						if(j > 1){
							
							check_valid = true;
						}
					}
				}
				if(check_valid){
					
					select_merge_list[i] = tmp_select_group_list;
					i++;
				}
			});
            $(".merge_total_count").text(i);
			merge_display();
        }
    });

    //忽略以上联系人
    $(".ignore-contact").click(function () {
		
		$(".merge-complete .merge-count").text("0");
        $("#merge-panel1").show();
        $("#contact-panel").hide();
        $(".merge-contacts").show();

    });
	
    //合并弹窗中跳过本组
    $(".m-p-foot .skip-btn-ct").click(function(e){		
        
        e.stopPropagation();
		merge_igonre_total_count++;
		next_merge_display();	
    });

    //关闭合并联系人按钮
    $(".m-p-head-contact .close_btn").click(function(){
		
		merge_complete_display();	   
    });

    //合并本组
    $(document).on("click",".merge-btn-ct",function(e){
		
        e.stopPropagation();		
		merge_data(current_merge_type,use_contact_id,merge_id_array);		
    });

    $(".merge-complete a").click(function(){
		
        $("#merge-panel1").hide();
        $(".default-hint").show();
    });

    if (window.orientation === 90 || window.orientation === -90 ){
		
        $("#g_slider_menu").css({"background":"#000","opacity":".6"});
        $(".slider-nav-bg").css("background","none");
    }
	
	$(document).on("mouseover",".cm-contact-item",function(){		
		
		var _this = $(this);
		var left_element = _this.find("em");
		var right_element = _this.parent().siblings(".fl-r-block").children(".fl-r");
		if(left_element.attr("id") != undefined){
			
			if(!left_element.hasClass("active2") && !left_element.hasClass("cmcc-check-area_hover")){
			
				left_element.addClass("cmcc-check-area_hover");							
			}else if(left_element.hasClass("active2") && left_element.hasClass("cmcc-check-area_hover")){
				
				left_element.removeClass("cmcc-check-area_hover");
			}			
		}
		if(!right_element.hasClass("active2") && !right_element.hasClass("cmcc-check-area_hover")){
					
			right_element.addClass("cmcc-check-area_hover");
		}else if(right_element.hasClass("active2") && right_element.hasClass("cmcc-check-area_hover")){
				
			right_element.removeClass("cmcc-check-area_hover");
		}						
	});
	
	$(document).on("mouseout",".cm-contact-item",function(){
		
		var _this = $(this);
		var left_element = _this.find("em");
		var right_element = _this.parent().siblings(".fl-r-block").children(".fl-r");
		if(left_element.attr("id") != undefined){
			
			if(left_element.hasClass("cmcc-check-area_hover")){
				
				left_element.removeClass("cmcc-check-area_hover");
			}								
		}
		
		if(right_element.hasClass("cmcc-check-area_hover")){
					
			right_element.removeClass("cmcc-check-area_hover");
		}
	});	

	$(document).on("click",":text",function(){
		var _this = $(this);
		var placeholder = _this.attr("placeholder");
		if(!is_undefined(placeholder) && placeholder == _this.val()){
			
			_this.val("");
			_this.css({"color":"#000","font-weight":"bold"});
		}
	});
	
	$(document).on("blur",":text",function(){
		
		//input_placeholder_process($(this));
	});
	
	//$("#contact_search_input").blur();

});



