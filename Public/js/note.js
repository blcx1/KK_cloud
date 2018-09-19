var recy_note_page = 1;//当前页回收站短信组
var note_list_page = 1;//当前页有效短信
var note_data_total_count = 0;//正常总个数
var recovery_note_total_count = 0;//回收站总个数
var ajax_use = true;
var note_nodata_html = '<div id="album_nodata" class="the-container" style="display:black" >'+
							      '<div class="gl-right clearfix gallery_right_time" id="gallery_right_time" style="">'+
								      '<div class="m-p m-p-sh img-frame-box total-img-area gallery-total-padd panel" style="height: 100%;" id="gallery_detail_time">'+
									      '<div id="gallery_detail_time" class="imglist-empty">'+
									      	'<p>'+v_No_data+'</p>'+
									     '</div>'+
								     '</div>'+
							     '</div>'+
							  '</div>';
//if($("list_note>js_draggable").length<=0){
//	$("list_note").html(note_nodata_html);
//	
//}

//将便签移入回收站/恢复数据
function recycle_note(name,id,recy_id){
	
	 var is_success = true ;
	 $.ajax({
  		url:base_module_url +'/Note/up_recy_note.html?is_ajax=1',
  		type:'Post',
  		data:{'id':id,"recy_id":recy_id},
  		dataType:'Json',
  		success:function(data){
  			redirct(data);
  			var msg_str = "";
  			var status = data.status;
  			if(name == "note_delete-btn"){
  				
  				switch(status){
					
					case 1:
						is_success = true;
						msg_str = v_operation_success;
						layer.msg(msg_str,{icon: 1,skin: 'layer-ext-moon'});
						$('#'+id).remove();
						break;
					case 0:
					default:
						is_success = false;
						msg_str = v_operation_faild;
						layer.msg(msg_str,{icon: 2,skin: 'layer-ext-moon'});
						break;
  				}
  				if($("#list_note>.js_draggable").length<=0){
  	  				$("#list_note").html(note_nodata_html);
  	  				
  	  			}
  			}else if(name == "info_in_delete"){
  				switch(status){
					
					case 1:
						is_success = true;
						msg_str = v_operation_success;
						layer.msg(msg_str,{icon: 1,skin: 'layer-ext-moon'});
						$('#'+id).remove();
						$('.layer,.js_note_detail').hide();
						break;
					case 0:
					default:
						is_success = false;
						msg_str = v_operation_faild;
						layer.msg(msg_str,{icon: 2,skin: 'layer-ext-moon'});
						break;
  				}
  				
  				if($("#list_note>.js_draggable").length<=0){
  	  				$("#list_note").html(note_nodata_html);
  	  				
  	  			} 				
  			}  			
  		}
  	});
}
//将回收站删除（不可恢复）
function delete_note(id,recy_id){
	
	 $.ajax({
  		url:base_module_url +'/Note/delete_note.html?is_ajax=1',
  		type:'Post',
  		data:{'id':id,"type_id":1},
  		dataType:'Json',
  		success:function(data){
			
            redirct(data);
  			var msg_str = "";
  			var status = data.status;
  				switch(status){
					
					case 1:
						is_success = true;
						msg_str = v_operation_success;
						layer.msg(msg_str,{icon: 1,skin: 'layer-ext-moon'});
						$('#'+id).remove();
						$('.layer,.js_note_detail').hide();
						break;
					case 0:
					default:
						is_success = false;
						msg_str = v_operation_faild;
						layer.msg(msg_str,{icon: 2,skin: 'layer-ext-moon'});
						break;
  				}
  				if($("#reco_list_note>.js_draggable").length<=0){
  	  				$("#reco_list_note").html(note_nodata_html);
  	  				
  	  			}
  		}
  	});
}
//加载便签及回收站列表
function list_note(name,is_delete,page,type_id,check_firest){

	var html = "";
	var check_ajax = false;
	if(page == 1){

		check_ajax = true;
	}else{

		if(is_delete == 0){

			if((note_list_page - 1)* page_size <note_data_total_count){
				check_ajax = true;
			}
		}else{

			if((recy_note_page - 1)*page_size<recovery_note_total_count){
				check_ajax = true;
			}
		}
	}
	
	if(check_ajax){
		$.ajax({
			url:base_module_url +'/Note/getnoteList.html?is_ajax=1',
			type:'Post',
			data:{"is_recy":is_delete,"p":page,"type_id":type_id},
			dataType:'Json',
			success:function(data){

				redirct(data);
				var list = data.list;
				for(var val in list){
                                        list[val]["data"]["content"]=list[val]["data"]["content"].replace(/\[\*\&\*\&\]|\[\&\#\&\#\]|\[\@\$\@\$\]|\[\#\*\#\*\]|\[\&\$\&\$\]|\[\*\&\$\#\@\&\*\%\$\@\%\%\*\#\%\$\]/g,"");
				/*	list[val]["data"]["content"]=list[val]["data"]["content"].replace(/<br \/>/g,'');
                                        if(list[val]["data"]["content"].indexOf('/KXDnotes')>=0){
                                            var arr=list[val]["data"]["content"].split('/KXDnotes');
                                            for(var i=0;i<arr.length;i++){
                                                arr[i]=arr[i].replace(/\/KXD.*\.mp3/g,'<br /><img width="30" src="'+base_url_img+'music.png"/><br/>');
                                                arr[i]=arr[i].replace(/\/KXD.*\.mp4|\/KXD.*\.avi|\/KXD.*\.mpg/g,'<br /><img width="30" src="'+base_url_img+'video.png"/><br />');
                                                arr[i]=arr[i].replace(/\/KXD.*\.png|\/KXD.*\.jpg|\/KXD.*\.gif/g,'<br /><img width="30" src="'+base_url_img+'pic.png"/><br />');
                                            }
                                            list[val]["data"]["content"]=arr.join('');
                                        }*/

                                        html += '<div class="js_note_brief note-brief js_normal_note js_note_color0 js_draggable" data-note-id="'+list[val]["service_id"]+'" id="'+list[val]["service_id"]+'" draggable="true" id="js_draggable">'+
						'<div class="note-brief-hd">'+
						'<span class="modify-date js_modify_date" title="'+list[val]["data"]["title"]+'">'+list[val]["data"]["last_update_time"]+'</span>'+
						'<div class="js_delete_btn delete-btn note_delete-btn"  title="'+v_delete+'" ></div>'+
						'</div>'+
						'<div class="js_snippet js_note_brief_bd note-brief-bd">'+
						list[val]["data"]["content"]+
						'</div>'+
						'<div class="js_drop_zone"></div>'+

						'</div>';
				}

				if(name=="info_list"){
					if(data.total_count<=0){
						
	  	  				$("#list_note").html(note_nodata_html);
	  	  				
	  	  			}else{
		  	  			if(page==1){
							$("#list_note").html(html);
						}else{
	
							$("#list_note").append(html);
						}
	  	  			}
				}else if(name=="recovery_list"){
					if(data.total_count<=0){
						$("#reco_list_note").html(note_nodata_html);
	  	  			}else{
	  	  				if(page==1){
	  	  					$("#reco_list_note").html(html);
	  	  				}else{
	  	  					$("#reco_list_note").append(html);
	  	  				}
	  	  			}
					
				}
				ajax_use = true;
			},
		});

	}

}

$(function(){

	/*//便签下拉加载更多
	 $(".home-bd").scroll(function(){

	 if($("#list_note").scrollTop()>=$(".home-bd").height()-$("#list_note").height()){
	 note_list_page = note_list_page +1;
	 list_note("info_list",0,note_list_page,1);
	 }
	 })
	 //回收站下拉加载更多
	 $(".trash-bd").scroll(function(){
	 if($("#reco_list_note").scrollTop()>=$(".trash-bd").height()-$("#reco_list_note").height()){

	 recy_note_page = recy_note_page +1;
	 list_note("recovery_list",1,recy_note_page,1);
	 }
	 })*/

	//便签下拉加载更多
	var currrentWidth=$("#list_note").width();
	var currentNumbers=Math.floor(currrentWidth/230);
	var currentHeight=Math.ceil(page_size/currentNumbers);
	var temp=currentHeight*230*0.2;
	$(".home-bd").scroll(function(){
		
		if(ajax_use && $(".home-bd").scrollTop()+$("#js_home").height()+temp>=currentHeight*230*note_list_page){
			note_list_page = note_list_page +1;
			ajax_use = false;
			list_note("info_list",0,note_list_page,1);
		}
	});

	//回收站下拉加载更多
	var currrentWidth1=$("#reco_list_note").width();
	var currentNumbers1=Math.floor(currrentWidth1/230);
	var currentHeight1=Math.ceil(page_size/currentNumbers1);
	var temp1=currentHeight1*230*0.2;;
	$(".trash-bd").scroll(function(){
		if( ajax_use &&$(".trash-bd").scrollTop()+$(".js_trash").height()+temp1>=currentHeight1*230*recy_note_page ){
			ajax_use = false;
			recy_note_page = recy_note_page +1;
			list_note("recovery_list",1,recy_note_page,1);

		}
	});

//  $(document).on("click","#note_more",function(e){
//	note_list_page = note_list_page +1;
//	list_note("info_list",0,note_list_page,1);
//
//	})
//$(document).on("click","#recovery_more",function(e){
//	recy_note_page = recy_note_page +1;
//	list_note("recovery_list",1,recy_note_page,1);
//
//	})

    //导航栏切换
    $("#note-link").click(function () {

    	list_note("info_list",0,note_list_page,1);
    	$(this).addClass("js_selected");
    	$("#trash-link").removeClass("js_selected");
    	$("#info_in_delete").removeClass("delete_info").addClass("recovery_info");
        $(".home-bd,.add-note-btn,.js_home").show();
        $(".trash-bd,#recovery_note,#note-popup").hide();
    });
    $("#trash-link").click(function () {
    	$(this).addClass("js_selected");
    	$("#note-link").removeClass("js_selected");
    	list_note("recovery_list",1,recy_note_page,1);
		$("#info_in_delete").addClass("delete_info").removeClass("recovery_info");
        $(".trash,#recovery_note,.trash-bd,#note-popup").show();
        $(".home-bd,.add-note-btn,.js_home").hide();
    });

	//便签列表删除键
    $(document).on("click","#list_note .note_delete-btn",function(e){

    	 e.stopPropagation();
    	 var id = $(this).parents(".js_draggable").attr('id');
    	 layer.confirm(v_is_recycle,
 				{btn: [v_determine,v_cancel]},
 				function(index){
 					layer.close(index);
 					recycle_note("note_delete-btn",id,1);
 				},
 				function(index){
 					layer.close(index)
 				}
 			);
    	 $(".layui-layer-title").text(v_information);
    });

	//回收站列表删除键
    $(document).on("click","#reco_list_note .note_delete-btn",function(e){
    	e.stopPropagation();
    	var id = $(this).parents(".js_draggable").attr('id');
    	layer.confirm(v_is_delete,
    				{btn: [v_determine,v_cancel]},
    				function(index){
    					layer.close(index);
    					delete_note(id,1);
    				},
    				function(index){
    					layer.close(index)
    				}
    			);
    	$(".layui-layer-title").text(v_information);
    });
	 //回收站信息中删除键
	$(document).on("click",".delete_info",function(e){
		e.stopPropagation();
		var id = $('input[name="id"]').val();
		layer.confirm(v_is_delete,
				{btn: [v_determine,v_cancel]},
				function(index){
					layer.close(index);
					delete_note(id,1);
				},
				function(index){
					layer.close(index)
				}
			);
		$(".layui-layer-title").text(v_information);
   });
    //便签信息中删除键
    $(document).on("click",".recovery_info",function(e){

    	e.stopPropagation();
    	var id = $('input[name="id"]').val();
    	layer.confirm(v_is_recycle,
    				{btn: [v_determine,v_cancel]},
    				function(index){
    					layer.close(index);
    					 recycle_note("info_in_delete",id,1);     				},
    				function(index){
    					layer.close(index)
    				}
    			);
    	$(".layui-layer-title").text(v_information);
    });

    //便签信息中恢复数据
     $(document).on("click","#recovery_note",function(e){
    	 e.stopPropagation();
    	 var id = $('input[name="id"]').val();
    	 recycle_note("info_in_delete",id,0);
    });

    $(".js_mobile_return").click(function(){
        $(".layer,.js_note_detail").hide();
        if($("iframe").contents().find("body").text()==""){
            if(!$("#normal_folder").hasClass("normal_folder")) {
                $("#normal_folder").next().remove();
            }else{
                $("#note-popup").hide();
            }
        }
        $("iframe").contents().find("body").text("");
        $(".normal_folder").removeClass("normal_folder");

    });
    $("#normal_folder").click(function(){
        $("#note-popup").show();
    });

   /* if($(window).width()<=480){
        $("#g_username").text("");
        $(".vh-avator-area").css({"top":"14px","right":"0"});
        $("meta[name='viewport']").attr("content","width=360, user-scalable=no");
        $(".js_folder_detail").css("height","100%");
	}

    if(768<=$(window).width()<=1023){

        $("#g_username").text("");
        $(".vh-avator-area").css({"top":"14px","right":"0"});
        $("meta[name='viewport']").attr("content","width=360, user-scalable=no");
        $(".js_folder_detail").css("height","100%");
    }*/

    $(".user-info").click(function (e) {
        e.stopPropagation();
        $(".user-info .menu.js_hide").slideToggle();
    });
    $(document).click(function(){
        $(".user-info .menu.js_hide").slideUp();
    });

    //点击遮罩和关闭按钮关闭
    $(".layer,.close-btn").click(function (e) {
        e.stopPropagation();
        if($(this).hasClass("js_history_picker_close_btn")){
            return false;
        }
        if($('#normal_folder').hasClass('normal_folder')){
            if($('.js_note_detail').css("display")=="block"){
                $('.js_note_detail').hide();
                $("#note-popup .folder-detail-ctn").show();
                if($('.note-brief-bd-active').length<=0){
                    $("#note-popup .js_note_brief").eq(0).find('.js_snippet').text($("iframe").contents().find("body").text());
                    if($("iframe").contents().find("body").text()==""){
                        $("#note-popup .js_note_brief").eq(0).remove();
                    }
                }else{
                    $('.note-brief-bd-active').text($("iframe").contents().find("body").text());
                    var html = $("#note-popup .folder-detail-ctn").find(".js_note_briefs_ctn").html();
                    $('#normal_folder').find(".folder-brief-bd").html(html);
                }
                $('.js_snippet').removeClass('note-brief-bd-active');
                $("iframe").contents().find("body").text("");
                return false;
            }
            var html = $("#note-popup .folder-detail-ctn").find(".js_note_briefs_ctn").html();
            $('#normal_folder').find(".folder-brief-bd").html(html);
            $("#note-popup .folder-detail-ctn").hide();
            $(".layer").hide();
            $('#normal_folder').removeClass('normal_folder');
            $('.js_snippet').removeClass('note-brief-bd-active');
            $("iframe").contents().find("body").text("");
            return false;
        }
        $('.js_note_detail,.layer').hide();
        $("#note-popup .folder-detail-ctn").hide();
        if($('.note-brief-bd-active').length<=0){
            $('#normal_folder').next('.js_draggable').find('.js_snippet').html($("iframe").contents().find("body").html());
            if($("iframe").contents().find("body").text()==""){
                $('#normal_folder').next('.js_draggable').remove();
            }
        }else{
            $('.note-brief-bd-active').html($("iframe").contents().find("body").html());
           /* var html = $("#note-popup .folder-detail-ctn").find(".js_note_briefs_ctn").html();
            $('#normal_folder').find(".folder-brief-bd").html(html);*/
        }
        $('.js_snippet').removeClass('note-brief-bd-active');
        $("iframe").contents().find("body").text("");
        $('.note-detail-hd').css('border-top-color','#fff');
        $(".view-menu,.history-picker,.js_snippet input").hide();

    });

     //点击列表弹出详情信息
    $(document).on("click",".js_draggable",function(e){
    	 e.stopPropagation();
    	$('.layer,.js_note_detail').show();
    	var id = $(this).attr('data-note-id');
    	$.ajax({
    		url:base_module_url +'/Note/get_note_info.html?is_ajax=1',
    		type:'Post',
    		data:{'id':id},
    		dataType:'Json',
    		success:function(data){
    			
				redirct(data);
				var html;
				var info_array = data.info;
				if(data["state"]==0){
                                    alert(v_no_data);
    				//$('#state').text(v_effective);
    			}else{
                                //图片替换文件路径字符
                                function vmp(match,exc1,exc2,img){ 
                                    if(noteContent.indexOf(match)>=0){
                                        var arr=noteContent.split(match);
                                        for(var i=0;i<arr.length;i++){
                                            if(arr[i].indexOf('/KXDnotes')>=0 && arr[i].indexOf(exc1)==-1 && arr[i].indexOf(exc2)==-1){
                                                arr[i]='<img width="250" src="'+img+'"/>';
                                            }
                                        }
                                        noteContent=arr.join('');
                                    }
                                }
    			/*	$('#state').text(v_invalid);*/
					if(info_array["id"] != undefined){
						content = info_array.note_content;
						//$('#note_content').val(content["content"]);
                                                var noteContent=content["content"];
                                                noteContent=noteContent.replace(/\[\*\&\$\#\@\&\*\%\$\@\%\%\*\#\%\$\]/g,'');
                                                vmp('[@$@$]','[&#&#]','[*&*&]',base_url_img+'video.png');
                                                vmp('[&#&#]','[@$@$]','[*&*&]',base_url_img+'music.png');
                                                vmp('[*&*&]','[&#&#]','[@$@$]',base_url_img+'pic.png');
    
                                                noteContent=noteContent.replace(/\[\#\*\#\*\]/g,'<img width="15" src="'+base_url_img+'btn_check_off_emui.png" />');
                                                noteContent=noteContent.replace(/\[\&\$\&\$\]/g,'<img width="15" src="'+base_url_img+'btn_check_on_emui.png" />');
                                                
                                                
                                                $('#note_content').html(noteContent);
                                                $('#note_content').css('height',($('.note-detail').outerHeight()-210)+'px');
                                                
						//$('.note-detail-hd .modify-date').text(content["title"]);
						$('.note-detail-hd .modify-date').text(content["title"].substr(0,5));
						$('#add_time').text(content["create_time"]);
                                                $('#update_time').text(content["last_update_time"]);
						$('input[name="id"]').val(info_array["id"]);
						if(info_array["path"]!=""){
							var file_name ="";
							if(!content["file_name"]){
								file_name = info_array["id"]+".zip";
								
							}else{
								file_name = content["file_name"];
							}
						/*	html = '<img alt="zip" src="'+base_url_img+'zip.jpg" width=30>'+
							'<a style=" margin-left:20px;" href="'+ base_url+info_array["path"].substr(1)+'" target="view_window">'+ file_name+'</a>';
							$('#note_path').html(html);*/
							html ='<a style=" margin-left:20px;" href="'+ base_url+info_array["path"].substr(1)+'" target="view_window">'+ file_name+'</a>';
							$('#note_path').html(html);
						}
					}
    			}
    		}
    	});
    });

    //点击document 关闭
    $(document).click(function (){
        $('.js_snippet').removeClass('note-brief-bd-active');
        $(".checkbox,.view-menu,.history-picker").hide();
    });

    //输入框输入数据自动保存
    $("iframe").contents().find("body").keyup(function () {
        if ($("iframe").contents().find("body").html() != "") {
            setTimeout(function(){$(".note-detail-hd .js_sync_success").show()}, 2000);
            setTimeout(function(){$(".note-detail-hd .js_sync_success").hide()}, 200);
            if($('.note-brief-bd-active').length>0){
                $('.note-brief-bd-active').html($("iframe").contents().find("body").html());
            }else if($('#normal_folder').hasClass('normal_folder')){
                $("#note-popup .js_note_brief").eq(0).find('.js_snippet').text($("iframe").contents().find("body").html());
            }else{
                $('#normal_folder').next('.js_draggable').find('.js_snippet').text($("iframe").contents().find("body").text());
            }
        }
    });

    //颜色+查看历史
    $(".js_menu_toggle").click(function (e) {
        e.stopPropagation();
        $(".view-menu").toggle();
    });

    $(".js_view_history").click(function (e) {
        e.stopPropagation();
        $(".history-picker").show();
        $(".view-menu").hide();
        $(".js_history_picker").css("width","220px");
        //适应手机
       /* if($(window).width()<=480){
            $(".js_history_picker").css({"width":"100%","height":"187.5px"});
        }
        if(768<=$(window).width()<=1023){
            $(".js_history_picker").css({"width":"100%","height":"187.5px"});
        }
        if($(window).width()>=1024){
            $(".js_history_picker").css({"width":"220px","height":"100%"});
        }
        if (window.orientation === 90 || window.orientation === -90 ){
            $(".js_note_detail").css("width","50%");
            $(".history-picker").css({"width":"170px","height":"100%"});

           $(window).location.reload();
        }*/
    });
    $(".js_history_picker_close_btn").click(function(e){
        e.stopPropagation();
        $(".history-picker").hide();
        /*if (window.orientation === 90 || window.orientation === -90 ){
            $(".js_note_detail").css("width","80%");
        }*/
    });

    $(".note-detail-hd .js_note_color0").click(function () {
        $(this).parents(".note-detail-hd").css("border-top-color","#ffffff");
    });

    $(".note-detail-hd .js_note_color1").click(function () {
        $(this).parents(".note-detail-hd").css("border-top-color","#4790c7");
        $('.note-brief-bd-active').siblings(".note-brief-hd").css({"background":"#4790c7","color":"#ffffff"});
    });
    $(".note-detail-hd .js_note_color2").click(function () {
        $(this).parents(".note-detail-hd").css("border-top-color","#e7ad48");
        $('.note-brief-bd-active').siblings(".note-brief-hd").css({"background":"#e7ad48","color":"#ffffff"});
    });
    $(".note-detail-hd .js_note_color3").click(function () {
        $(this).parents(".note-detail-hd").css("border-top-color","#3baf86");
        $('.note-brief-bd-active').siblings(".note-brief-hd").css({"background":"#3baf86","color":"#ffffff"});
    });
    $(".note-detail-hd .js_note_color4").click(function () {
        $(this).parents(".note-detail-hd").css("border-top-color","#e4674c");
        $('.note-brief-bd-active').siblings(".note-brief-hd").css({"background":"#e4674c","color":"#ffffff"});
    });

   /* if (window.orientation === 90 || window.orientation === -90 ){


    }*/
    
});