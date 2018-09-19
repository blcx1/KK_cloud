var ajax_use = true;
var left_use_current_page = 1;//当前页有效短信组
var left_recy_current_page = 1;//当前页回收站短信组
var right_current_page = 0;//当前页有效短信
var left_use_total_count = 0;//有效短信组总个数
var left_recy_total_count = 0;//回收站短信组总个数
var right_total_count = 0;//某个分组短信总个数
var left_use_refresh_ajax = false;//有效短信组是否强制刷新
var left_recy_refresh_ajax = false;//回收站短信组是否强制刷新
var detail_group_id = 0;//当前详情的group_id
var detail_is_recy = -1;//当前详情右端选择
var check_search = false;//判别搜索
var search_name = "";//搜索的内容
var search_current_page = 0;//搜索当前页
var search_total_count = 0;//搜索总个数
var date_group_array_obejct = new Object();//日期对象
var ajax_process_param = new Object();

//左侧分组列表（短信/回收站）
function left_ajax(is_recy,check_first){
	
	var id_name;
	var current_page;	
	var next_page = 1;
	var total_count = 0;
	var check_ajax = false;
	if(is_recy == 0){
		
		id_name = "scroll_container_call_record1";
		if(check_first){
			
			left_use_total_count = 0;
			left_use_current_page = 0;			
		}else{
			
			total_count = left_use_total_count;
			current_page = left_use_current_page;
		}
			
	}else{
		
		id_name = "scroll_container_call_record2";
		if(check_first){
			
			left_recy_current_page = 0;
			left_recy_total_count = 0;			
		}else{
			
			total_count = left_recy_total_count;
			current_page = left_recy_current_page;
		}
		current_page = left_recy_current_page;			
	}
	if(!check_first){
		
		if(current_page*page_size < total_count){
			
			check_ajax = true;
			next_page = current_page + 1;
		}	
	}else{
		
		check_ajax = true;
	}
	
	if(check_ajax){
		
		var ajax_data = {'p':next_page,'is_recy':is_recy};
		ajax_group(is_recy,next_page,id_name,ajax_data);
	}	
}

function right_ajax(){
	
	var check_ajax = false;
	var next_page = 1;	
	if(right_current_page*page_size < right_total_count){
			
		check_ajax = true;
		next_page = right_current_page + 1;
	}
	if(check_ajax){
		
		var ajax_data = {'id':detail_group_id,'p':next_page,'is_recy':detail_is_recy};	
		ajax_call_record(next_page,ajax_data);
	}
}

//获取组的ajax 
function ajax_group(is_recy,next_page,id_name,ajax_data){
	
	$(".call_record_loading").show();
	$("#left_use_more").hide();
	$("#left_recy_more").hide();	
	$.ajax({
		url:base_module_url + '/CallRecord/getGroupList.html?is_ajax=1',
 		data:ajax_data,
 		type:'POST',
 		dataType:'JSON',
 		success:function(data){	
 			
			$(".call_record_loading").hide();			
 			redirct(data);
			if(data.status == 1){
				
				var value;
				var li_name;
				var group_id;	
				var total_count;				
				var ul_li_content = '';	
				var child_total_count_str = '';
				var id_name_str = '#'+id_name;
				var list_array = data.list;
				var id_name_child_str = id_name_str + " li";
				if(is_recy == 0){
					
					li_name = "full";
					left_use_refresh_ajax = false;						
					left_use_current_page = next_page;
					left_use_total_count = data.total_count;					
					left_use_more_display();
				}else{
					
					li_name = "locked";
					left_recy_refresh_ajax = false;
					left_recy_current_page = next_page;
					left_recy_total_count = data.total_count;
					left_recy_more_display();
				}			
				
				for(var val in list_array){
					
					value = list_array[val];
					group_id = value["group_id"];
					total_count = value["total_count"];
					$(id_name_child_str).each(function(){
						
						if($(this).attr("data-id") == group_id){
							
							$(this).remove();
						}
					})
					var type_html = '';
					if(value["type_value"]==1){
						
						type_html ='<em class="icon-incoming"></em>';
						
					}else if(value["type_value"]==2){
						type_html = '<em class="icon-exhaled"></em>';
						
					}
					child_total_count_str = '<font class="call_record_total_count_display" '+(total_count > 1 ? "" :('style="display:none;"'))+'> ( '+total_count+' )</font>';					
					ul_li_content +='<li id="thread-'+li_name+'-' + group_id + '" data-id="'+ group_id +'" class="call_record_list_item mobileInfo1" style="">'+
					'<div class="call_record-list-avatar stranger">'+
					'<img src="' + base_url + 'Public/images/10086.png" class="call_record_photo"/>'+
					'</div>'+
					'<div class="content">'+
					'<div class="content-call_record-title">'+
					'<span class="content-name display-name"><font class="call_record_contact_name">'+ value["name"] + '</font> '+
					'<font class="call_record_total_count">'+ total_count + '</font> '+ child_total_count_str+'</span>'+
					'</div>'+
					'<p class="content-p"> <font class="call_record_phone">'+value["phone"]+'</font>  '+value["type_value_str"]+' '+type_html+'</p>'+
					'<p class="content-date"><span>'+value["date"]+'</span></p>'+
					'</div>'+
					'<a class="checkbox item_checkbox  mobileInfo" data-gid="' + group_id + '" href="javascript:void(0);"><em></em></a>'+ 
					'</li>';
					
				} 			
				$(id_name_str).append(ul_li_content);
				select_display($(id_name_child_str),false);
			}			
 		}	
	})
}

//获取短信的ajax
function ajax_call_record(next_page,ajax_data){
	
	$(".content_loading").show();
	$("#more_content").hide();
	$.ajax({
		
 		url:base_module_url+'/CallRecord/getCallRecordList.html?is_ajax=1',
 		data:ajax_data,
 		type:'POST',
 		dataType:'JSON',
 		success:function(data){	
				
				redirct(data);
				if(data.status == 1){
					
					var id;
					var value;	
					var tmp_date;
					var html = "";
					var tmp_value;	
					var list_array = data.list;
					var week_list_array = data.week_list;				
					var tmp_date_array = new Array();
					right_current_page = next_page; 					
					right_total_count = data.total_count;
					right_more_display();
					if(right_current_page == 1){
						
						date_group_array_obejct = new Object();
					}
					if($('#call_record_detail_select_frame').css("display")=="none"){
						
						$('#mes-details-avator,#mes-details,#call_recordDetailFrame,#call_record_detail_full_frame').show();
						$('.default-hint,.callrecord-detail,#call_record_detail_editor_frame,#call_record_detail_full_frame1').hide();
					}else{
						
						$('.mobileInfo').removeClass('active1');
						$('#call_record_detail_select_frame,#call_record_detail_full_frame,#call_recordDetailFrame').hide();
						$('.default-hint').show();
					}
					$(".checked").removeClass("checked");
					$(".active1").removeClass("active1");
				    $('.all_sel').show();		
					for(var key in list_array){
						
						tmp_date = key;	
						value = list_array[key];
						week_value = week_list_array[key];
						
						if($("#"+key).attr("id") == undefined){
							
							html += '<div class="mes-details-time" id="'+key+'">'+
										'<span>'+key+' &nbsp; '+ week_value['week'] +'</span>'+
								   '</div>';
						}
						
						if(date_group_array_obejct[tmp_date] == undefined){
									
							tmp_date_array = new Array();							
						}else{
							
							tmp_date_array = date_group_array_obejct[tmp_date];
						}								
						for(var val in value){
						
							tmp_value = value[val];												
							id = tmp_value['id'];							
							if($.inArray(id,tmp_date_array) == -1){
								
								tmp_date_array[tmp_date_array.length] = id;
							}
							
							var type_html = '';
							if(tmp_value["type_value"]==1){
								
								type_html ='<em class="icon-incoming"></em>';
								
							}else if(tmp_value["type_value"]==2){
								
								type_html = '<em class="icon-exhaled"></em>';								
							}
							date_group_array_obejct[tmp_date] = tmp_date_array;							
							html += '<div id="call_record_content-'+tmp_value['id']+'"  class="call-history-item call_list call_record_content">'+
								  	'<input type="checkbox" class="check" value="'+id+'" style="margin-top: 2px;display: none;float: left;margin-right:7px;">'+
									'<div class="call-history-content" style=" width: 90%; float: left;">'+
										   '<div class="number">'+tmp_value['type_value_str']+
											  ' <s>|</s>'+type_html+
										  ' </div>'+
										   '<div class="time">'+tmp_value['date']+'&nbsp;  '+tmp_value['duration_str']+'</div>'+
									  ' </div>'+
								   '</div>';
						}
					}
					$('#call_record_full_detail_content').append(html);
				}
	    }     			
 	});
}

function group_empty_next_page(ul_name,is_recy){
	
	if($(ul_name).find("li").attr('data-id') == undefined){
								
		if(check_search){

			ajax_search(false);
		}else{
			
			left_ajax(is_recy,false);
		}
	}
}

//ajax 处理分组
function ajax_group_process(process_type,is_recy,id_name,ul_name,data_id_array){

	var msg_str = "";
	$.ajax({
				url:base_module_url+'/CallRecord/'+process_type+'.html?is_ajax=1',
				data:{'gid':data_id_array},
				type:'POST',
				dataType:'JSON',
				success:function(data){	
										
					redirct(data);
					var icon = 1;
					if(data.status == 1){
												
						var tmp_msg;
						var id_success_list;
						var id_success_page = 0;
						var id_success_list_count = 0;
						left_use_refresh_ajax = true;
						left_recy_refresh_ajax = true;
						id_success_list = data.id_success_list;
						id_success_list_count = id_success_list.length;
						id_success_page = Math.ceil(id_success_list_count/page_size);
						if(check_search){
									
							search_total_count -= id_success_list_count;
							search_current_page -= id_success_page;
						}
						switch(process_type){
			
							case "recycle":
								
								if(!check_search){
									
									left_use_current_page -= id_success_page;
									left_use_total_count -= id_success_list_count;
								}
								msg_str = v_succ_recycle;
								break;
							case "delete":
							case "recover":
								
								if(!check_search){
									
									left_recy_current_page -= id_success_page;
									left_recy_total_count -= id_success_list_count;															
								}
								msg_str = process_type == "delete" ? v_succ_delete :v_succ_restore;
								break;			
						}
						if(id_success_list_count > 0){
							
							for(var key in id_success_list){
								
								$(id_name).each(function(){
											
									if($(this).attr('data-id') == id_success_list[key]){										
										
										$(this).remove();
									}
								})
							}	
							
							group_empty_next_page(ul_name,is_recy);
						}
						
					}else{
						
						icon = 2;
						tmp_msg = data.result;
						msg_str = tmp_msg.length > 0 ? tmp_msg : v_ope_failed;
					}	
					layer.msg(msg_str,{icon: icon,skin: 'layer-ext-moon'});
				}	
			})
}

//短信组彻底删除 ,移入回收站,还原数据
function group_process(process_type){
	
	var i = 0;
	var is_recy;
	var id_name;
	var ul_name;
	var data_id;
	var msg_str = "";	
	var data_id_array = new Array();
	if($("#scroll_container_call_record1").css("display") == "block"){
		
		is_recy = 0;
		ul_name = "#scroll_container_call_record1";
		
	}else{
		
		is_recy = 1;
		ul_name = "#scroll_container_call_record2";
	}
	id_name = ul_name + " li";
	$(id_name).each(function(){
		
		data_id = $(this).find(".active1").parent("li").attr('data-id');
		if(data_id != undefined){
			
			data_id_array[i] = data_id;
			i++;
		}			
	})
	
	if(process_type != 'recover'){
		
		if(is_recy == 0){
			
			process_type = "recycle";
		}else{
			
			process_type = "delete";
		}
	}
	if(i > 0){		
		
		if(process_type == "recover"){
			
			ajax_group_process(process_type,is_recy,id_name,ul_name,data_id_array);
		}else{
			
			switch(process_type){
			
			case "recycle":
				
				msg_str = v_is_recycle;
				break;
			case "delete":
				
				msg_str = v_is_delete;
				break;				
			}	
			ajax_process_param = new Object();
			ajax_process_param['process_type'] = process_type;
			ajax_process_param['is_recy'] = is_recy;
			ajax_process_param['id_name'] = id_name;
			ajax_process_param['ul_name'] = ul_name;
			ajax_process_param['data_id_array'] = data_id_array;		
			layer.confirm(msg_str,
				{btn: [v_determine,v_cancel]},
				function(index){
					layer.close(index);
					ajax_group_process(ajax_process_param['process_type'],ajax_process_param['is_recy'],ajax_process_param['id_name'],ajax_process_param['ul_name'],ajax_process_param['data_id_array']);
				},
				function(index){
					layer.close(index)
				}
			);	
		}
	}else{
		
		switch(process_type){
			
			case "recycle":
				
				msg_str = v_move_recycle;
				break;
			case "delete":
				
				msg_str = v_sel_delete;
				break;
			case "recover":
				
				msg_str = v_sel_data;
				break;			
		}		
		layer.msg(msg_str,{icon:0,skin: 'layer-ext-moon'});
	}
	
}

//ajax call_record 处理
function ajax_call_record_process(process_type,li_name,data_id,group_id,data_id_array,ul_name,is_recy){
	
	var msg_str = "";
	$.ajax({
		url:base_module_url+'/CallRecord/'+process_type+'.html?is_ajax=1',
		data:{'id':data_id_array},
		type:'POST',
		dataType:'JSON',
		success:function(data){	
								
			redirct(data);
			var icon = 1;
			if(data.status == 1){
				
				var tmp_msg;
				var tmp_date;
				var date_array;
				var tmp_date_array;
				var id_success_list;
				var id_success_page = 0;
				var id_success_list_count = 0;
				left_use_refresh_ajax = true;
				left_recy_refresh_ajax = true;
				id_success_list = data.id_success_list;
				id_success_list_count = id_success_list.length;
				id_success_page = Math.ceil(id_success_list_count/page_size);
				
				right_current_page -= id_success_page;
				right_total_count -= id_success_list_count;						
				switch(process_type){
	
					case "recycle":								
						
						msg_str = v_succ_recycle;
						break;
					case "delete":
						
						msg_str = v_succ_delete;
						break;
					case "recover":								
						
						msg_str = v_succ_restore;
						break;			
				}
				if(id_success_list_count > 0){
					
					for(var key in id_success_list){
						
						data_id = id_success_list[key];								
						$("#call_record_content-"+data_id).remove();
						$(".mes-details-time").each(function(){
							
							tmp_date = $(this).attr("id");
							date_array = date_group_array_obejct[tmp_date];
							if(date_array != undefined){
								
								tmp_date_array = date_array;
								for(var date_key in date_array){
									
									if(date_array[date_key] == data_id){
										
										tmp_date_array.splice(date_key,1);
									}
								}
								date_group_array_obejct[tmp_date] = tmp_date_array;										
							}
							
						});
					}							
					$(".call_record_timeline_count .call_record_total_count").html(right_total_count);
					if(right_total_count > 0){
						
						var group_li_name = "#thread-"+li_name+"-"+group_id;
						var call_record_total_count_display_name = group_li_name+" .call_record_total_count_display";
						$(group_li_name+" .call_record_total_count").html(right_total_count);
						$(call_record_total_count_display_name).html("( "+ right_total_count +" )");
						if(right_total_count > 1){
							
							$(call_record_total_count_display_name).show();
						}else{
							
							$(call_record_total_count_display_name).hide();
						}
					}else{
											
						$("#thread-"+li_name+"-"+group_id).remove();						
						group_empty_next_page(ul_name,is_recy);
						
					}					
					
					$(".mes-details-time").each(function(){
							
						tmp_date = $(this).attr("id");								
						date_array = date_group_array_obejct[tmp_date];
						if(date_array != undefined){
							
							if(date_array.length == 0){
																	
								$("#"+tmp_date).remove();
							}
						}
					})
					
					if($("#call_record_detail_full_frame .call_record_content").find("input").val() == undefined){
														
						right_ajax();
					}
					
				}
				
			}else{
				
				icon = 2;
				tmp_msg = data.result;
				msg_str = tmp_msg.length > 0 ? tmp_msg : v_ope_failed;
			}	
			layer.msg(msg_str,{icon:icon,skin: 'layer-ext-moon'});
		}	
	})
}

//短信彻底删除 ,移入回收站,还原数据
function call_record_process(process_type){
	
	var i = 0;
	var id_name;
	var ul_name;
	var li_name;
	var data_id;
	var msg_str = "";
	var is_recy = detail_is_recy;
	var group_id = detail_group_id;
	var data_id_array = new Array();
	
	if(is_recy == 0){
				
                ul_name = "#scroll_container_call_record1";
		li_name = "full";		
	}else{
			
		ul_name = "#scroll_container_call_record2";	
		li_name = "locked";
	}
	id_name = "#call_record_detail_full_frame .call_record_content input";
	$(id_name).each(function(){
				
		if($(this).attr("checked") != undefined){
			
			data_id = $(this).val();			
			data_id_array[i] = data_id ;
			i++;
		}			
	})
	
	if(process_type != 'recover'){
		
		if(is_recy == 0){
			
			process_type = "recycle";
		}else{
			
			process_type = "delete";
		}
	}
	if(i > 0){
		
		switch(process_type){
			
			case "recycle":
				
				msg_str = v_is_recycle;
				break;
			case "delete":
				
				msg_str = v_is_delete;
				break;				
		}		
		
		if(process_type == "recover"){
			
			ajax_call_record_process(process_type,li_name,data_id,group_id,data_id_array,ul_name,is_recy);
		}else{
			
			ajax_process_param = new Object();
			ajax_process_param['process_type'] = process_type;
			ajax_process_param['li_name'] = li_name;
			ajax_process_param['data_id'] = data_id;
			ajax_process_param['group_id'] = group_id;
			ajax_process_param['data_id_array'] = data_id_array;	
			ajax_process_param['ul_name'] = ul_name;
			ajax_process_param['is_recy'] = is_recy;
			layer.confirm(msg_str,
				{btn: [v_determine,v_cancel]},
				function(index){
					layer.close(index);
					ajax_call_record_process(ajax_process_param['process_type'],ajax_process_param['li_name'],ajax_process_param['data_id'],ajax_process_param['group_id'],ajax_process_param['data_id_array'],ajax_process_param['ul_name'],ajax_process_param['is_recy']);
				},
				function(index){
					layer.close(index)
				}
			);			
		}
	}else{
		
		switch(process_type){
			
			case "recycle":
				
				msg_str = v_move_recycle;
				break;
			case "delete":
				
				msg_str = v_sel_delete;
				break;
			case "recover":
				
				msg_str = v_sel_data;
				break;			
		}		
		layer.msg(msg_str,{icon:0,skin: 'layer-ext-moon'});
	}
}

//搜索数据
function ajax_search(check_first){
	
	var is_recy;
	var id_name;
	var li_name;
	var next_page;
	var check_ajax = false;
	check_search = true;
	search_name = $("#call_record_search_input").val();
	if(search_name.length > 0){
		
		if($("#scroll_container_call_record1").css("display") == "block"){
		
			is_recy = 0;
			li_name = "full";
			id_name = "scroll_container_call_record1";
		}else{
			
			is_recy = 1;
			li_name = "locked";
			id_name = "scroll_container_call_record2";
		}
		if(check_first){
			
			next_page = 1;
			check_ajax = true;
			search_current_page = 0;
			search_total_count = 0;			
		}else{
			
			if(search_current_page*page_size < search_total_count){
			
				check_ajax = true;
				next_page = search_current_page + 1;
			}
		}
		if(check_ajax){
			
			$.ajax({
				url:base_module_url+'/CallRecord/search.html?is_ajax=1',
				data:{'p':next_page,'is_recy':is_recy,'sk':search_name},
				type:'POST',
				dataType:'JSON',
				success:function(data){	
					
					$(".call_record_loading").hide();			
					redirct(data);
					if(data.status == 1){
						
						var value;
						var group_id;	
						var total_count;				
						var ul_li_content = '';	
						var child_total_count_str = '';
						var id_name_str = '#'+id_name;
						var list_array = data.list;
						var id_name_child_str = id_name_str + " li";
						
						search_current_page = next_page;	
						search_total_count = data.total_count;
						if(is_recy == 1){
							
							left_recy_refresh_ajax = true;
						}else{
							
							left_use_refresh_ajax = true;
						}						
						if(is_recy == 0){
																	
							if(search_current_page*page_size < search_total_count){
								
								$(".call_record_more,#left_use_more").show();
							}else{
								
								$(".call_record_more,#left_use_more").hide();
							}
						}else{
														
							if(search_current_page*page_size < search_total_count){
								
								$(".call_record_more,#left_recy_more").show();
							}else{
								
								$(".call_record_more,#left_use_more").hide();	
							}
						}						
						for(var val in list_array){
							
							value = list_array[val];
							group_id = value["group_id"];
							total_count = value["total_count"];
							$(id_name_child_str).each(function(){
								
								if($(this).attr("data-id") == group_id){
									
									$(this).remove();
								}
							})	
							
							
							var type_html = '';
							if(value["type_value"]==1){
								
								type_html ='<em class="icon-incoming"></em>';
								
							}else if(value["type_value"]==2){
								type_html = '<em class="icon-exhaled"></em>';								
							}
							child_total_count_str = '<font class="call_record_total_count_display" '+(total_count > 1 ? "" :('style="display:none;"'))+'> ( '+total_count+' )</font>';					
							ul_li_content +='<li id="thread-'+li_name+'-' + group_id + '" data-id="'+ group_id +'" class="call_record_list_item mobileInfo1" style="">'+
							'<div class="call_record-list-avatar stranger">'+
							'<img src="' + base_url + 'Public/images/10086.png" class="call_record_photo"/>'+
							'</div>'+
							'<div class="content">'+
							'<div class="content-call_record-title">'+
							'<span class="content-name display-name"><font class="call_record_contact_name">'+ value["name"] + '</font> '+
							'<font class="call_record_total_count">'+ total_count + '</font> '+ child_total_count_str+'</span>'+
							'</div>'+
							'<p class="content-p"> <font class="call_record_phone">'+value["phone"]+'</font>  '+value["type_value_str"]+' '+type_html+'</p>'+
							'<p class="content-date"><span>'+value["date"]+'</span></p>'+
							'</div>'+
							'<a class="checkbox item_checkbox  mobileInfo" data-gid="' + group_id + '" href="javascript:void(0);"><em></em></a>'+ 
							'</li>';				
							
						} 	
						if(check_first){
							
							$(id_name_str).html(ul_li_content);
						}else{
							
							$(id_name_str).append(ul_li_content);
						}
						select_display($(id_name_child_str),false);
					}	
				}	
			})
		}
		
	}else{
	
		layer.msg(v_search_contents,{icon:0,skin:'layer-ext-moon'});
	}	
}

//清空所有数据
function clearAll(){
	
	layer.confirm(v_tip_delete,
		{btn: [v_determine,v_cancel]},
		function(index){
			
			layer.close(index);
			$.ajax({
				url:base_module_url+'/CallRecord/clearAll.html?is_ajax=1',
				data:{},
				type:'POST',
				dataType:'JSON',
				success:function(data){	
								
					redirct(data);
					if(data.status == 1){
						
						var call_record_html;
						ajax_use = true;
						left_use_current_page = 0;
						left_recy_current_page = 0;
						right_current_page = 0;
						left_use_total_count = 0;
						left_recy_total_count = 0;
						right_total_count = 0;
						left_use_refresh_ajax = true;
						left_recy_refresh_ajax = true;
						detail_group_id = 0;
						detail_is_recy = -1;
						date_group_array_obejct = new Object();
						call_record_html = '<span class="hint-content">'+v_no_data+'</span>';					
						$("#scroll_container_call_record1,#scroll_container_call_record2").html(call_record_html);
						$(".call_record_more").hide();
						$('#call_record_detail_full_frame').hide();
						$('.default-hint').show();					
					}		
				}	
			})
		},
		function(index){
			layer.close(index)
			}
	);
}

function call_record_tab_change(name,object){
	
	var del_name = "";
	check_search = false;
	if(left_use_refresh_ajax){
			
		$('#scroll_container_call_record1').html("");
	}
	if(left_recy_refresh_ajax){
			
		$('#scroll_container_call_record2').html("");
	}	
	
	$("#scroll_container_call_record1").toggle();
	$("#scroll_container_call_record2").toggle();
	object.removeClass("another-corner");	
	object.addClass("current-status");
	
	ajax_use = true;		
	$('.all_select').show();
	$('.checkbox').removeClass('active1').css('display','');
	$('#scroll_container_call_record1 li').css('background','');
	$('#scroll_container_call_record2 li').css('background','');	
	if(name == 'call_record_tab_full'){
		
		del_name = v_moverecycle;
		$(".btn-recover").hide();
		$("#left_recy_more").hide();
		$("#scroll_container_call_record1").show();
		$("#scroll_container_call_record2").hide();
		$("#call_record_tab_locked").removeClass("current-status");	
		$("#call_record_tab_locked").addClass("another-corner");
	}else{
		
		del_name = v_remove_completely;
		$("#left_use_more").hide();
		$(".btn-recover").show();
		$("#scroll_container_call_record1").hide();
		$("#scroll_container_call_record2").show();
		$("#call_record_tab_full").removeClass("current-status");	
		$("#call_record_tab_full").addClass("another-corner");
	}
	$(".btn-delete").html(del_name);
}

function left_use_more_display(){
	
	if(left_use_current_page*page_size < left_use_total_count){
		
		
		$(".call_record_more,#left_use_more").show();
	}else{
		
		$(".call_record_more,#left_use_more").hide();		
	}
}

function left_recy_more_display(){
	
	if(left_recy_current_page*page_size < left_recy_total_count){
			
		$(".call_record_more,#left_recy_more").show();
	}else{
		
		$(".call_record_more,#left_recy_more").hide();						
	}
}

function right_more_display(){
	
	if(right_current_page*page_size < right_total_count){
						
		$("#more_content").show();
	}else{
		
		$("#more_content").hide();
		
	}
}

function select_display(object,check_item){
	
	if(ajax_use == false){
		
		$('.checkbox').show();
		object.css('background','#E2E2E2');
		if(check_item){
			
			object.find('.item_checkbox').toggleClass('active1');	
		}			
	}
}

function click_group(object,is_recy){
	
	select_display(object,true);
	if(ajax_use){
		
		var del_name = "";
		var current_group_id = object.attr("data-id");	
		var li_name = is_recy == 0 ? "full" : "locked";		
		var element_class_name = "#thread-"+li_name+"-" + current_group_id;		
		var call_record_phone = $(element_class_name + " .call_record_phone").html();
		var call_record_photo = $(element_class_name + " .call_record_photo").attr("src");
		var call_record_total_count = $(element_class_name + " .call_record_total_count").html();
		var call_record_contact_name = $(element_class_name + " .call_record_contact_name").html();
		
		if(is_recy == 0){
			
			del_name = v_moverecycle;
			$(".btn-call_record-recover").hide();
		}else{
			
			del_name = v_remove_completely;
			$(".btn-call_record-recover").show();
		}		
		$(".btn-call_record_delete").html(del_name);
		$("#call_record_displayname").html(call_record_contact_name);
		$("#mes-details-avator .call_record_phone").html(call_record_phone);
		$("#mes-details-avator .call_record_total_count").html(call_record_total_count);
		$("#call_record_detail_full_frame .call_record-list-avatar").attr("src",call_record_photo);
		$('#call_record_content,#call_record_detail_full_frame,#mes-details-avator,#call_record_detail_full_frame .call_record_timeline_count,#call_record_detail_full_frame .call_record_total_count').show();
		if(current_group_id == detail_group_id && is_recy == detail_is_recy){
						
			right_more_display();
		}else{
		
			detail_group_id = current_group_id;
			detail_is_recy = is_recy;
			right_current_page = 0;        
			right_total_count = 0;	
			$('#call_record_full_detail_content').html("");
			var ajax_data = {'id':detail_group_id,'p':1,'is_recy':detail_is_recy};
			ajax_call_record(1,ajax_data);
		}
	}	
}

$(function(){
	//适应手机



	if($(window).width()<=480) {
		var currentHeight=$(window).height()-70+"px";
		if($("#kcloud_right_frame").css("display")=="none") {

			$("#kcloud_window").css("height", currentHeight);
		}
		$("#g_username").text("");
	}
	
	$("#call_record_search_input").keyup(function(event){
		
		if(event.keyCode == 13){
			
			ajax_search(true);
		}		
	});
	
	//搜索
	$("#call_record_search_btn").click(function(){
				
		ajax_search(true);
	});
	
	//点击短信获取列表
	$("#call_record_tab_full") .click(function(){
				
		call_record_tab_change("call_record_tab_full",$(this));		
		if(left_use_refresh_ajax){
			
			left_ajax(0,true);
		}else{
			
			left_use_more_display();
		}		
	});
	
	//点击回收站获取列表
	$("#call_record_tab_locked") .click(function(){
		
		call_record_tab_change("call_record_tab_locked",$(this));		
		if(left_recy_refresh_ajax){
			
			left_ajax(1,true);
		}else{
			
			left_recy_more_display();
		}		
	});
	
	//点击更多获取短信列表
	$("#left_use_more") .click(function(){
		
		if(check_search){
			
			ajax_search(false);
		}else{
			
			left_ajax(0,false);
		}
		
	});
	
	//点击更多获取回收站列表
	$("#left_recy_more") .click(function(){
		
		if(check_search){
			
			ajax_search(false);
		}else{
			
			left_ajax(1,false);
		}
	});
	
	//短信左侧li点击事件
	$(document).on('click','#scroll_container_call_record1 li',function(){
			
		click_group($(this),0);		
	});
	
	//短信左侧li点击事件
	$(document).on('click','#scroll_container_call_record2 li',function(){
		
		click_group($(this),1);
	});
	
	$('#more_content').click(function(){
    	
		right_ajax();
    });
		
	//短信左侧选择
	$('.btn-select').click(function(){
		
		ajax_use = false;
		$('#call_record_detail_full_frame').hide();
		$('.default-hint').show();
		
		$('.checkbox').show();
		if($("#scroll_container_call_record1").css("display") == "block"){
			
			id_name = 'scroll_container_call_record1';
		}else{
			
			id_name = 'scroll_container_call_record2';
		}
		
		$('#' + id_name + ' li').css('background','#E2E2E2');		
	});
	
	 //短信左侧全选
	$('.all_select').click(function(){
		
		var id_name;
		ajax_use = false;
		$('#call_record_detail_full_frame').hide();
		$('.default-hint').show();		
		$(this).hide();
		if($("#scroll_container_call_record1").css("display") == "block"){
			
			id_name = 'scroll_container_call_record1';
		}else{
			
			id_name = 'scroll_container_call_record2';
		}
		$('.checkbox').show();
		$('#' + id_name + ' li .checkbox').addClass('active1').show();
		$('#' + id_name + ' li').css('background','#E2E2E2');
	});
	
	//短信左侧取消选择
	$('.clear_clear').click(function(){
		
		ajax_use = true;		
		$('.all_select').show();
		$('.checkbox').removeClass('active1').css('display','');
		$('#scroll_container_call_record1 li').css('background','');		
		$('#scroll_container_call_record2 li').css('background','');		
	});
	
	//左侧删除/移入回收站
	$(".btn-delete").click(function(){
		
		group_process('');
	});
	
	//左侧还原数据
	$(".btn-recover").click(function(){
		
		group_process('recover');
	});
	
	$(".all_clear").click(function(){
		
		clearAll();
	});
	
	//右侧全选
	$(document).on('click','.all_sel',function(){
		$('.check').show();
		$('.check').attr("checked","checked");
		
		$(this).hide();
	});
	
	//右侧取消
	$(document).on('click','.call_record_clear',function(){
		
		$('.check').removeAttr("checked");
		$('.check').hide();
		$('.all_sel').show();		
		
	});
	
	//右侧选择
	$(document).on('click','.btn-sel',function(){
		
		$('.check').show();
	});
	
	//右侧删除/移入回收站
	$(".btn-call_record_delete").click(function(){
		
		call_record_process('');
	});
	
	//右侧还原
	$(".btn-call_record-recover").click(function(){
		
		call_record_process('recover');
	});	
    
	//取消
    $("#call_record_detail_select_frame .btn-ct").click(function(){

        $('.mobileInfo').removeClass('active1');
        $('.default-hint,#call_record_default_bar').show();
        $('#call_record_detail_select_frame,#call_record_sel_bar').hide();
        $(".checked").removeClass("checked");
    });
    $("#create_call_record").click(function(){
        $("#call_record_detail_editor_frame").show();
        $(".default-hint,.callrecord-detail,#mes-details,#call_record_detail_select_frame").hide();
        //适应手机
        if($("#kcloud_right_frame").css("display")=="none"){
            $("#kcloud_right_frame").show();
            $("#kcloud_left_frame").hide();
        }
    });

	//删除弹窗
    $(".icobtn-del").click(function(){
        $("#delete-popup,#call_record_detail_select_frame").show();
        $(".default-hint").hide();
    });

    //取消
    $(".close_btn,.animated3 .btn-ct").click(function(){
        $("#delete-popup").hide();
        $(".dialog_body .dialog-tips").text(v_sel_messages);
    });
    $(".default").click(function(){
        $("#delete-popup,#call_record_detail_select_frame,.clearbottom").hide();
        $(".default-hint").show();
        $(".active1").removeClass("active1");
        $(".checked").parents(".call-history-item").remove();
        $(".checked").removeClass("checked");
        $(".callrecord-list").removeClass("select-mode");
    });
    //通话记录删除功能
    $(".delete-button .btn-ct").click(function(){
		layer.msg('faf',{icon: 0,skin: 'layer-ext-moon'});
        $("#delete-popup").show();
        $(".dialog_body .dialog-tips").text(v_call_delete);
        //适应手机
        if($("#kcloud_left_frame").css("display")=="none"){
            $(".dialog").css({"width":"auto","margin-left":"-150px","top":"223px"});
        }
    });
    //点击出现相应联系人和电话
    $(".icon-small-sentmes").click(function(){
        $(".number").remove("s");
        $("#call_record_detail_full_frame1").show();
        $("#call_record_detail_full_frame").hide()
        $("#call_record_detail_full_frame1 #call_record_displayname").text($(this).parents(".small-button-area").prev(".content-name").text());
        $(this).parents(".call-history-content").next().find(".number s").remove();
        $("#call_record_detail_full_frame1 #call_record_displayname").next().text($(this).parents(".call-history-content").next().find(".number").text());
    });
});