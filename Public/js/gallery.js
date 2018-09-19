var base_url_img;
var photo_page=1;
var album_page=1;
var album_recy_page=1;
var photo_recy_page=1;
var photo_data_count= 0;
var photo_total_count = 0;
var album_total_count = 0;
var is_recy = 0;
var ajax_use=true;
var current_page  = 0;
var total_count = 0;
var list_type = "album";
var album_use = false;

//获取某相册中的相片列表
function page_photo_ajax(id,check_first){
	var check_ajax = false;
	var next_page = 1;
	if(check_first){
		check_ajax = true;
		current_page = 0;
		next_page = 1;
	}else{
		if(current_page*page_size < total_count){
			check_ajax = true;
			next_page = current_page + 1;
		}
	}
	if(check_ajax){
		$.ajax({
			url:base_module_url +'/Gallery/get_photo_list.html?is_ajax=1',
			type:'Post',
			data:{'aid':id,"p":next_page,"is_recy":is_recy},
			dataType:'Json',
			success:function(data){				
				redirct(data); //跳转
				var html ='';
				var list = data.list;
				current_page = next_page;
				total_count  = data.count;
				ajax_use = true;
				var icovedio = "";
				if(total_count<=0){
					 var re_html = $("#album_nodata").html();
					$("#gallery_list").html(re_html);
				}else{
					
					for(var i=0;i<=list.length-1;i++){
						if(list[i].photo_type=="video/3gpp"||list[i].photo_type=="video/mp4"){
							icovedio = '<em class="ico-vedio"></em>';
						}if(list[i].photo_type=="image/jpeg"){
							icovedio = '';
						}
						html += '<li class="image-item" id="'+list[i].photo_id+'" style="width:137px;height:137px"> '+
							'<a  href="javascript:void(0);" class="img-info" id="'+list[i].photo_id+'" style="width:137px;height:137px"> '+
							'<img class="img-data animated  fadeIn" video_width="'+list[i].photo_width+'" video_height="'+list[i].photo_height+'" photo-path="'+list[i].photo_path+'"  src="'+list[i].small_photo_path+'" style="opacity: 0; position: relative; top: -53.5px;"  width="137" height="244" />'+
							'<div class="img-checked-box"></div>'+icovedio+
							'</a> '+
							'<em class="gallery_checkbox checkbox" data-id="'+list[i].photo_id+'"></em>'+
							'</li>';
					}
					if(check_first) {
						$("#gallery_list").html(html);
					}else{
						$("#gallery_list").append(html);
					}
					
				}
			}
		});
	}
}
//获取分类相册列表/回收站
function page_album_ajax(check_first) {
	var name;
	var next_page = 1;
	var check_ajax = false;
	if(check_first){
		check_ajax = true;
		current_page = 0;
		next_page = 1;
	}else{
		if(current_page*page_size < total_count){
			next_page = current_page + 1;
			check_ajax = true;
		}
	}
	name = is_recy == 0 ? "album_list" : "album_recy";
	if (check_ajax) {
		$.ajax({
			url: base_module_url + '/Gallery/ajax_note_album.html?is_ajax=1',
			type: 'Post',
			data: {"p": next_page, "is_recy": is_recy},
			dataType: 'Json',
			success: function (data) {
				redirct(data); //跳转
				album_data_total_count = data.count;
				current_page = next_page;
				total_count  = data.count;
				ajax_use = true;
				var html = '';
				var v_recy = "";
				var list = data.list;
				var addclass = '';
				
				if(album_data_total_count<=0){
					if (name == 'album_list') {
						$("#album_nodata,.gallery_right_time").show();
						$("#album_list").hide();
					} else if (name == 'album_recy') {
						var re_html = $("#album_nodata").html();
						$("#album_recy").html(re_html);
						$(".gallery_right_time").show();
					}
				}else{
					$("#album_nodata").hide();
					$("#album_list").show();
					for (var i = 0; i <= list.length - 1; i++) {
						if (name == 'album_list') {
							v_recy = '<em title="'+v_operation_success+'" class="v-gal-down-ico down-photo" data-id="' + list[i].id + '">'+v_down_album+'</em>';
							addclass = " v-album-recy";
						} else if (name == 'album_recy') {
							v_recy = '<em title="'+v_recy_album+'" class="v-gal-recy-ico" data-id="' + list[i].id + '">'+v_recy_album+'</em>';
							addclass = " v-album-delete";
						}
						html += '<li class="image-item no-edit" id="' + list[i].id + '" data-canedit="false"> ' +
							'<a class="img-info" href="#"> <img src="' + list[i].photo_path + '" style="" class="img-data front-cover fadeIn"  width="120" height="212" />' +
							'<span class="album-func-area"> '+ v_recy +
							'<em title="'+v_delete_album+'" class="v-gal-del-ico ' + addclass + '" data-id="' + list[i].id + '">'+v_delete_album+'</em>' +
							'</span> </a>' +
							'<div class="album-name-area">' +
							'<div class="album-name-container">' +
							'<span class="album-name">' + list[i].album_name + '</span>' +
							'</div>' +
							'<input type="text" class="album-name-edit" id="aid" value="' + list[i].id + '" />' +
							'</div> <span class="album-num"><span class="album-num-1  photo_count" data-count="' + list[i].count + '">' + list[i].count + '</span><span>  '+v_Photos+'</span></span>' +
							'<div class="gallery-tip-box" style="display:none">' +
							'<em class="gallery-tip-triangle"></em>' +
							'</div> </li>';
					}
					if (name == 'album_list') {
						if (check_first) {
							$("#album_list").html(html);
						} else {
							$("#album_list").append(html);
						}
					} else if (name == 'album_recy') {
						if (check_first) {
							$("#album_recy").html(html);
						} else {
							$("#album_recy").append(html);
						}
					}
				}
				
				
				
			}
		});
	}
}
//分组移入回收站/还原数据
function recy_album(id,is_recy){
	 $.ajax({
	  		url:base_module_url +'/Gallery/recy_album.html?is_ajax=1',
	  		type:'Post',
	  		data:{'aid':id,"is_recy":is_recy},
	  		dataType:'Json',
	  		success:function(data){
			redirct(data); //跳转
	  			if(data.status==1){
	  				layer.msg(v_operation_success,{icon: 1,skin: 'layer-ext-moon'});
	  				if(is_recy==1){
	  					$("#album_list #"+id).remove();
	  					
	  					if($("#album_list>li").length<=0){

		  					$("#album_nodata").show();
		  					$("#album_list").hide();
		  				}
	  				}else if(is_recy==0){
	  					$("#album_recy #"+id).remove();
	  					if($("#album_recy>li").length<=0){
		  					var re_html = $("#album_nodata").html();
							$("#album_recy").html(re_html);
							$(".gallery_right_time").show();
		  				}
	  				}
	  			}else{
	  				layer.msg(v_operation_faild,{icon: 2,skin: 'layer-ext-moon'});
	  			}
	  		}
	  	});
}
//分组删除数据（不可恢复）
function delete_album(id){
	 $.ajax({
	  		url:base_module_url +'/Gallery/delete_album.html?is_ajax=1',
	  		type:'Post',
	  		data:{'aid':id},
	  		dataType:'Json',
	  		success:function(data){
			redirct(data); //跳转
	  			if(data.status==1){
	  				$("#album_recy #"+id).remove();
	  				if($("#album_recy>li").length<=0){
	  					var re_html = $("#album_nodata").html();
						$("#album_recy").html(re_html);
						$(".gallery_right_time").show();
	  				}

	  				layer.msg(v_operation_success,{icon: 1,skin: 'layer-ext-moon'});
	  			}else{
	  				layer.msg(v_operation_faild,{icon: 2,skin: 'layer-ext-moon'});
	  			}
	  		}
	  	});
}
//分组中photo相片移入回收站/还原数据
function recy_photo(array_id,is_recy,aid){
	 $.ajax({
	  		url:base_module_url+'/Gallery/recy_photo.html?is_ajax=1',
	  		type:'Post',
	  		data:{'array_id':array_id,"is_recy":is_recy,"aid":aid},
	  		dataType:'Json',
	  		success:function(data){
			redirct(data); //跳转
	  			if(data.status==1){
	 				var id = array_id.substr(0,array_id.length-1);
	  				var photo_id = id.split(",");
	  				var length = photo_id.length;
	  				for(var i=0;i<length;i++){
	  					$("#"+photo_id[i]).remove();
	  				}
	  				
	  				if(is_recy==1){
	  					var count = $("#album_list #"+aid).find(".photo_count").attr("data-count");
	  					if(count-length==0){
	  						$("#album_list #"+aid).remove();
	  					}else{
		  					$("#album_list #"+aid).find(".photo_count").attr("data-count",count-length).text(count-length);
	  					}

						$(".gal-toolbar").hide();
						$(".image-item").removeClass("selected");
						$("#detail_area").removeClass("selection-mode");
						$(".checked").removeClass("checked");

					}else if(is_recy==0){
	  					
	  					var count = $("#album_recy #"+aid).find(".photo_count").attr("data-count");
	  					if(count-length==0){
	  						$("#album_recy #"+aid).remove();
	  					}else{
		  					$("#album_recy #"+aid).find(".photo_count").attr("data-count",count-length).text(count-length);
	  					}
	  				}
	  				if($("#gallery_list>li").length<=0){
 						 var re_html = $("#album_nodata").html();
 						$("#gallery_list").html(re_html);
 						$(".gallery_right_time").show();
 						$(".gal-toolbar-area,.gal-toolbar-tip").hide();
						$(".gal-toolbar").hide();
 					}
	  				layer.msg(v_operation_success,{icon: 1,skin: 'layer-ext-moon'});
	  			}else{
	  				layer.msg(v_operation_faild,{icon: 2,skin: 'layer-ext-moon'});
	  			}
	  		}
	  	});
}
//删除某个相册下的相关相片（不可恢复）
function delete_photo(album_id,photo_id_array){
	 $.ajax({
	  		url:base_module_url +'/Gallery/delete_photo.html?is_ajax=1',
	  		type:'Post',
	  		data:{'album_id':album_id,'photo_id_array':photo_id_array},
	  		dataType:'Json',
	  		success:function(data){
	  			if(data.status==1){
					$(".gal-toolbar").hide();
					$(".image-item").removeClass("selected");
					$("#detail_area").removeClass("selection-mode");
					$(".checked").removeClass("checked");

	  				var id = photo_id_array.substr(0,photo_id_array.length-1);
	  				var photo_id = id.split(",");
	  				var length = photo_id.length;
	  				for(var i=0;i<length;i++){
	  					$("#"+photo_id[i]).remove();
	  				}
	  				var count = $("#album_recy #"+album_id).find(".photo_count").attr("data-count");
  					if(count-length==0){
  						$("#album_recy #"+album_id).remove();
  					}else{
  						$("#album_recy #"+album_id).find(".photo_count").attr("data-count",count-length).text(count-length);
  					}
  					if($("#gallery_list>li").length<=0){
						 var re_html = $("#album_nodata").html();
						$("#gallery_list").html(re_html);
						$(".gallery_right_time").show();
						$(".gal-toolbar-area,.gal-toolbar-tip").hide();
						 
					}
	  				layer.msg(v_operation_success,{icon: 1,skin: 'layer-ext-moon'});
	  			}else{
	  				layer.msg(v_operation_faild,{icon: 2,skin: 'layer-ext-moon'});
	  			}
	  		}
	  	});
}
function sleep(numberMillis) { 
	var now = new Date(); 
	var exitTime = now.getTime() + numberMillis; 
	while (true) { 
		now = new Date(); 
		if (now.getTime() > exitTime) 
		return; 
	} 
}
//相片移动/复制
function move_photo(name){
	var data_id ;
	var id = '';
	var i =1 ;
	$('#gallery_list .checked').each(function(){
		data_id = $(this).attr('data-id');
		id += data_id+",";
		i++;
	});
	var source_album_id = $(".photo_list").attr("data-id");
	var dest_album_id = $("#album_name").find(".current").attr("data-id");
	$.ajax({
		
		url:base_module_url +'/Gallery/move_photo.html?is_ajax=1',
		type:"post",
		data:{"name":name,"source_album_id":source_album_id,"dest_album_id":dest_album_id,"photo_id_array":id},
		dataType:'json',
		success:function(data){
			redirct(data); //跳转
			var status = data.status;
			var id_length = 0;
			if(status==1){
				var source_count = parseInt($("#album_list #"+source_album_id).find(".photo_count").attr("data-count"));
				var dest_count = parseInt($("#album_list #"+dest_album_id).find(".photo_count").attr("data-count"));
				$('.photo_list .checked').each(function(){
					id_length =id_length+1;
				});
				if(name=="move"){
					$('.photo_list .checked').each(function(){
						data_id = $(this).attr('data-id');
						$("#"+data_id).remove();
					});
					
					$(".gal-toolbar-area").hide();
  					if(source_count-id_length==0){
  						$("#album_list #"+source_album_id).remove();
  					}else{
	  					$("#album_list #"+source_album_id).find(".photo_count").attr("data-count",source_count-id_length).text(source_count-id_length);
	  					$("#album_list #"+dest_album_id).find(".photo_count").attr("data-count",dest_count+id_length).text(dest_count+id_length);
  					}
				}else if(name=="copy"){
  					$("#album_list #"+dest_album_id).find(".photo_count").attr("data-count",dest_count+id_length).text(dest_count+id_length);
				}
				$("#change_album_dialog,#__mask__").hide();
  				
				if($("#gallery_list>li").length<=0){
					 var re_html = $("#album_nodata").html();
					$("#gallery_list").html(re_html);
					$(".gallery_right_time").show();
					$(".gal-toolbar-area,.gal-toolbar-tip").hide();
					 
				}
				
				layer.msg(v_operation_success,{icon: 1,skin: 'layer-ext-moon'});
			}else{
  				layer.msg(data.result,{icon: 2,skin: 'layer-ext-moon'});
			}
		}
	})
}
//相片名称改变
function  change_album_name_ajax(id,initialnum,currentvalue){
	var name = initialnum;
	if(id > 0 && currentvalue.length > 0 && initialnum != currentvalue ){
		$.ajax({
			url:base_module_url +'/Gallery/changeAlbumName.html?is_ajax=1',
			type:'Post',
			data:{'aid':id,'album_name':currentvalue}   ,
			dataType:'Json',
			success:function(data) {
				redirct(data); //跳转
				var msg_str = "";
				var icon = 1;
				if (data.status == 1) {
					name = currentvalue;
					msg_str = v_success;
				}else{
					msg_str = v_ope_failed;
					icon = 0;
				}
				layer.msg(msg_str,{icon:icon,skin:'layer-ext-moon'});
				$(".currentImage .album-name").text(name);
				$(".currentImage .album-name-container").show();
				$('.currentImage').find("input").css("display","none");
				$(".currentImage").removeClass("currentImage");
			}
		});
	}else{
		$(".currentImage .album-name").text(name);
		$(".currentImage .album-name-container").show();
		$('.currentImage').find("input").css("display","none");
		$(".currentImage").removeClass("currentImage");
	}
}
//相片信息
function ajax_photo_info(id){
	$.ajax({
		url:base_module_url + '/Gallery/get_photo_info.html?is_ajax=1',
		data:{"id":id},
		type:"Post",
		dataType:"Json",
		success:function(data){
			var width = data.info.photo_width + " x " + data.info.photo_height + " px";
			$(".iMIge-exif-base-fileName").text(data.info.photo_name);//
			$(".size").text(data.info.photo_size);
			$(".width_heigth").text(width);
			$(".add_time").text(data.info.add_time);
		}
	})
}
	//播放Quicktime格式的视频，包括.mov .amr .3gp等    
	function pv_q(u, w, h){    
		var pv='';    
		pv += '<object width="'+w+'" height="'+h+'" classid="clsid:02BF25D5-8C17-4B23-BC80-D3488ABDDC6B" codebase="http://www.apple.com/qtactivex/qtplugin.cab">';    
		pv += '<param name="src" value="'+u+'">';    
		pv += '<param name="controller" value="true">';    
		pv += '<param name="type" value="video/quicktime">';    
		pv += '<param name="autoplay" value="true">';    
		pv += '<param name="target" value="myself">';    
		pv += '<param name="bgcolor" value="black">';    
		pv += '<param name="pluginspage" value="http://www.apple.com/quicktime/download/index.html">';    
		pv += '<embed src="'+u+'" width="'+w+'" height="'+h+'" controller="true" align="middle" bgcolor="black" target="myself" type="video/quicktime" pluginspage="http://www.apple.com/quicktime/download/index.html"></embed>';    
		pv += '</object>';    
		return pv;    
	}   
	
	
	function al_no_data(count){
		
		if(count<=0){
			
			album_use = true;
		}
		if(album_use == true){
			
			$("#album_nodata").show();
			$("#album_list").hide();
		}else{
			$("#album_nodata").hide();
			$("#album_list").show();
		}
	}
$(document).ready(function(){

	al_no_data(total_count);
	
	
	var actionurl = $("#form_post").attr("action");
	//加到相册中的移动,
	$("#moveGalleryBtn").click(function(){
		move_photo("move");
	});
	//加到相册中的复制
	$("#copyGalleryBtn").click(function(){
		move_photo("copy");
	});
	//相册下载
	$(document).on("click",".down-photo",function(e){
		e.stopPropagation();
		var album_id = $(this).attr("data-id");
		var album_count = $(this).parents(".no-edit").find(".photo_count").attr("data-count");
		if(album_count<10){ 
			 $.ajax({
			  		url:base_module_url +'/Gallery/get_photo_id.html?is_ajax=1',
			  		type:'post',
			  		data:{'aid':album_id},
			  		dataType:'Json',
			  		success:function(data){
					redirct(data); //跳转
			  			var photo_id = data.photo_id; 
			  			var length = photo_id.length;
			  			if(length>0){
			  				layer.confirm(v_down_tips,
			  						{btn: [v_determine,v_cancel]},
									function(index){
										layer.close(index);
										var down_url;
										for (var i=0; i < length; i++) {											
											
											down_url = actionurl+"?photo_id="+photo_id[i];
											$("#open_url").attr("href",down_url);									
											document.getElementById("open_url").click();					
							  			}
										},
									function(index){
										layer.close(index)
									}
								);
			  				$(".layui-layer-title").text(v_information);
			  			}
			  		}
			  	});
		}else{
			layer.msg(v_choice_tips,{icon: 2,skin: 'layer-ext-moon'});
		}
	});
	//下载相片down_photo
	$(document).on("click","#download_photo",function(e){
		e.stopPropagation();


		var data_id ;
		var id = '';
		var i =1 ;
		var length = 0;
		var aid = $('#gallery_list').attr("data-id");  
			var length = $('#gallery_list .checked').length;
			if(length<=10){					
					layer.confirm(v_down_tips,
							{btn: [v_determine,v_cancel]},
							function(index){
								layer.close(index);							
								var down_url;
								$('#gallery_list .checked').each(function(){
									
									data_id = $(this).attr('data-id'); 								
									down_url = actionurl+"?photo_id="+data_id;
									
									$("#open_url").attr("href",down_url);									
									document.getElementById("open_url").click();									
								});			
								
								
								},
							function(index){
								layer.close(index)
							}
						);
					$(".layui-layer-title").text(v_information);

			}else{
				layer.msg(v_choice_tips,{icon: 2,skin: 'layer-ext-moon'});

			}

	});
	$(".gt-status").css({"display":"inline-block","color":"#fff","font-size":"14px","line-height":"32px"});
	$(".album-num").css({"color":"#98999b","display":"block","text-align":"center","line-height":"1.5","white-space":"nowrap","text-overflow":"ellipsis","overflow":"hidden"});
	$(".album-area .img-list li").css("text-align","center");
	//删除相片（不可恢复）
	$(document).on("click","#M-Gen-button-106",function(e){
		e.stopPropagation();
		var data_id ;
		var id = '';
		var i =0 ;
		var aid = $('#gallery_list').attr("data-id");
		$('#gallery_list .checked').each(function(){
			data_id = $(this).attr('data-id');
			id += data_id+",";
			i++;
		});
		layer.confirm(v_delete_tips,
				{btn: [v_determine,v_cancel]},
				function(index){
					layer.close(index);
					delete_photo(aid,id);
					},
				function(index){
					layer.close(index)
				}
			);
		$(".layui-layer-title").text(v_information);
	});
	//还原photo相片
	$(document).on("click","#M-Gen-button-105",function(e){
		e.stopPropagation();



		var data_id ;
		var id = '';
		var i =0 ;
		var aid = $('#gallery_list').attr("data-id");
		$('.checked').each(function(){
			data_id = $(this).attr('data-id');
			id += data_id+",";
			i++;
		})
		layer.confirm(v_restore_tips,
				{btn: [v_determine,v_cancel]},
				function(index){
					layer.close(index);
					recy_photo(id,0,aid);
					},
				function(index){
					layer.close(index)
				}
			);
		$(".layui-layer-title").text(v_information);
	});
	$("#add_album_dialog").hide();
	$("#add_album_dialog,#gallery_detail_1,#gallery_right_recycle").hide();
	$(".iMIge-bd").css("height","90%");
	//移入回收站photo相册
	$(document).on("click","#M-Gen-button-102",function(e){
		e.stopPropagation();
		var data_id ;
		var id = '';
		var i =0 ;
		var aid = $('#gallery_list').attr("data-id");
		$('.checked').each(function(){
			data_id = $(this).attr('data-id');
			id += data_id+",";
			i++;
		})
		layer.confirm(v_move_tips,
				{btn: [v_determine,v_cancel]},
				function(index){
					layer.close(index);
					recy_photo(id,1,aid);
					},
				function(index){
					layer.close(index)
				}
			);
		$(".layui-layer-title").text(v_information);
	});
	//相册分类回收站删除（不可恢复）
	$(document).on("click",".v-album-delete",function(e){
		e.stopPropagation();
		id = $(this).attr("data-id");
		layer.confirm(v_delete_tips,
				{btn: [v_determine,v_cancel]},
				function(index){
					layer.close(index);
					delete_album(id);
					},
				function(index){
					layer.close(index)
				}
			);
		$(".layui-layer-title").text(v_information);
	})
	//相册分类移入回收站
	$(document).on("click",".v-album-recy",function(e){
		e.stopPropagation();
		var id = $(this).attr("data-id");
		layer.confirm(v_move_tips,
				{btn: [v_determine,v_cancel]},
				function(index){
					layer.close(index);
					recy_album(id,1);
					},
				function(index){
					layer.close(index)
				}
			);
		$(".layui-layer-title").text(v_information);
	})
//相册分类还原数据
	$(document).on("click",".v-gal-recy-ico",function(e){
		e.stopPropagation();
		var id = $(this).attr("data-id");
		layer.confirm(v_restore_tips,
				{btn: [v_determine,v_cancel]},
				function(index){
					layer.close(index);
					recy_album(id,0);
					},
				function(index){
					layer.close(index)
				}
			);
		$(".layui-layer-title").text(v_information);
	})
	//相册下拉加载更多
	var currrentWidth=$("#detail_area").width();
	var currentNumbers=Math.floor(currrentWidth/150);
	var currentHeight=Math.ceil(page_size/currentNumbers);
	var temp=currentHeight*230*0.2;
	//相片加载更多
	var currrentWidth1=$("#detail_area").width();
	var currentNumbers1=Math.floor(currrentWidth/147);
	var currentHeight1=Math.ceil(page_size/currentNumbers1);
	var temp1=currentHeight1*147*0.2;
	$("#detail_area").scroll(function(){
		if(list_type != "photo"){
			if(is_recy == 0){
				if(ajax_use && $("#detail_area").scrollTop()+$("#detail_area").height()+temp>=currentHeight*230* album_page){
					ajax_use = false;
					page_album_ajax(false);
				}
			}else{
				if(ajax_use &&$("#detail_area").scrollTop()+$("#detail_area").height()+temp>=currentHeight*230*album_recy_page){
					ajax_use = false;
					page_album_ajax(false);
				}
			}
		}else{
			if(is_recy == 0){
				if(ajax_use && $("#detail_area").scrollTop()+$("#detail_area").height()+temp1>=currentHeight1*147* photo_page){
					var id = $("#gallery_list").attr("data-id");
					ajax_use = false;
					page_photo_ajax (id,false);
				}
			}else{
				if(ajax_use && $("#detail_area").scrollTop()+$("#detail_area").height()+temp1>=currentHeight1*147* photo_recy_page){
					var id = $("#gallery_list").attr("data-id");
					ajax_use = false;
					page_photo_ajax (id,false);
				}
			}
		}
	});
	//回收站切换
	$('#gallery_tab_recycle').click(function(){
		is_recy = 1;
		list_type = "album";
		page_album_ajax(true);
		$('#gallery_right_time,.fixed-area,.album-title-area,#my_pic').hide();
	    $('#gallery_right,.gallery_list').hide();
		$('#gallery_right_recycle').show();
	    $(this).addClass("current").siblings().removeClass('current');
		$(".ata-return").attr("data-name","photo_recy");
		$("#gallery_list").removeClass("photo_list").addClass("photo_recy_list");
	});
	//相册切换
	$('#gallery_tab_album').click(function(){
		list_type = "album";
		$('#gallery_right_time,#gallery_detail_1,.fixed-area').hide();
		$('#gallery_right,#album_frame,.album-title-area,#my_pic').show();
		$('#gallery_right_recycle,.gallery_list').hide();
		$(this).addClass("current").siblings().removeClass('current');
		$(".ata-return").attr("data-name","photo_re");
		$("#gallery_list").removeClass("photo_recy_list").addClass("photo_list");
		is_recy = 0;
		page_album_ajax(true);
	});
	//相册中，点击回收站相册各项的相关内容
	$(document).on("click","#album_recy .no-edit img",function(){
		var id = $(this).parent().parent().attr("id");
		list_type = "photo";
		var album_name = $(this).parents(".no-edit").find(".album-name").text();
		$("#gallery_title_album_name").text(album_name);
		$(".gallery-total-padd,.album-title-area,.fixed-area,.gallery_list,#gallery_list").show();
		$('#album_frame,#gallery_right_recycle').hide();
		$(".fixed-area").css("margin-top","0");
		$('#gallery_list').attr("data-id",id);
		page_photo_ajax(id,photo_recy_page,1);
	});
	//相册中，点击相册各项的相关内容
	$(document).on("click","#album_list .no-edit img",function(){
		
		var album_name = $(this).parents(".no-edit").find(".album-name").text();
		$("#gallery_title_album_name").text(album_name);
		var id = $(this).parent().parent().attr("id");
		list_type = "photo";
		$(".gallery-total-padd,.album-title-area,.fixed-area,.gallery_list").show();
		$('#album_frame').hide();
		$(".fixed-area").css("margin-top","0");
		$('#gallery_list').attr("data-id",id);
		page_photo_ajax(id,photo_page,0);
	});
	//选择与取消按钮
	$(document).on("click",".gl-check-group",function(){
		$(".img-list").removeClass("selected");
		var temp = $(this).parents(".the-month").next(".img-list")
		if (temp.find(".checkbox").hasClass("checked")) {
			if (temp.find(".checked").length <= temp.find(".image-item").length - 1) {
				$(this).text(v_cancel);
				temp.find(".checkbox").addClass("checked");
				temp.find(".image-item ").addClass("selected");
				$('.gal-toolbar').find('.checked_num').text($('.checked').length);
				$('.gallery-total-padd,.gallery-recycle').addClass('selection-mode');
			} else{
				if( $(".checked").length>temp.find(".checked").length){
					temp.find(".checked").parents(".image-item").removeClass("selected")
					$('.gallery-total-padd,.gallery-recycle').addClass('selection-mode');
					temp.find(".checked").removeClass("checked");
					$('.gal-toolbar').show();
					$('.gal-toolbar').find('.checked_num').text($(".checked").length-temp.find(".checked").length);
					$(this).text(v_choice);
				}else{
					$(".checked").parents(".image-item").removeClass("selected")
					$('.gallery-total-padd,.gallery-recycle').removeClass('selection-mode');
					$(".checked").removeClass("checked");
					$('.gal-toolbar').hide();
					$('.gal-toolbar').find('.checked_num').text(0);
					$(this).text(v_choice);
				}
			}
		} else {
			temp.find(".checkbox").addClass("checked");
			temp.find(".image-item ").addClass("selected");
			$(".gallery-total-padd,.gallery-recycle").addClass("selection-mode");
			$('.gal-toolbar').show();
			$(this).text(v_cancel);
			$('#gallery_right_time').siblings('.gal-toolbar').find('.checked_num').text($('.checked').length);
		}
	});
//checkbox的选择取消
	$(document).on("click",".checkbox",function(){
		$("#detail_area").addClass("selection-mode");
		if($(this).hasClass("checked")){
			if($(".checked").length<=1){
				$(".gal-toolbar").hide();
				$("#detail_area").removeClass("selection-mode");
				$(this).parents(".image-item").removeClass("selected");
				/*$(".gallery-total-padd,.gallery-recycle").removeClass("selection-mode");*/
				$('.gal-toolbar').find('.checked_num').text(0);
			}else {
				$('.gal-toolbar').find('.checked_num').text($('.checked').length-1);
				$(this).parents(".image-item").removeClass("selected");
			}
			$(this).removeClass("checked");
			$(this).parents(".img-list").prev(".the-month").find(".gl-check-group").text(v_choice);
		}else{
			$(this).addClass("checked");
			if($(this).parents(".img-list").hasClass("photo_recy_list")){
				$(".gal-toolbar-tip").show();
			}else{
				$(".gal-toolbar-area").show();
			}
			$(this).parents(".image-item").addClass("selected");
			$(".gallery-total-padd,.gallery-recycle").addClass("selection-mode");
			$('.gal-toolbar').find('.checked_num').text($('.checked').length)
			if($(this).parents(".img-list").find(".checked").length==$(this).parents(".img-list").find(".image-item").length){
				$(this).parents(".img-list").prev(".the-month").find(".gl-check-group").text(v_cancel);
			}
		}
	});
	//返回
	$("#gallery_back_button").click(function(){
		$(".gal-toolbar").hide();
		$(".image-item").removeClass("selected");
		$(".checked").removeClass("checked");
		$("#detail_area").removeClass("selection-mode");
	});
//加到相册
	$(document).on("click","#change_album_dialog .move-gallery-item",function(){
		 $(this).addClass('current').siblings().removeClass('current');
		$("#moveGalleryBtn,#copyGalleryBtn").addClass("default").removeClass("disabled")
	})
	//加到相册弹窗
	$(document).on("click","#M-Gen-button-100 ",function(){
		 $.ajax({
		  		url:base_module_url +'/Gallery/get_album.html?is_ajax=1',
		  		type:'Post',
		  		data:{},
		  		dataType:'Json',
		  		success:function(data){
				redirct(data); //跳转
		  			if(data.status==1){
		  				var list  = data.list;
		  				var html = '';
		  				for(var i=0;i<=list.length-1;i++){
		  					html += '<li class="move-gallery-item" data-id="'+list[i].id+'">'+
											'<div class="contactlist-container">'+
												'<div class="contactlist-avatar">'+
													'<img class="move_gallery_img" src="'+list[i].photo_path+'" />'+
												'</div>'+
												'<p class="contact-nickname">'+list[i].album_name+'</p>'+
											'</div>'+
										'</li>';
		  				}
		  				$("#album_name").html(html);
		  			}
		  		}
		  	});
		
		
		$('#change_album_dialog,.gray_box').show();
	});
	//加到相册中的新建相册
	$('#create_album_area').click(function(){
		$(this).hide();
		$('#create_album_content').show();
	});
	//加到相册中的取消按钮
	$(document).on("click",".create-album-lcance",function(){
		$('#create_album_content').hide();
		$('#create_album_area').show();
		$('#create_album_input').val('');
	});
	$(document).on("click","#clear_album,.cancel-delete,.delete-animated3 .close_btn",function(){
		$(".gal-toolbar").hide();
		$(".image-item").removeClass("selected");
		$("#detail_area").removeClass("selection-mode");
		$(".checked").removeClass("checked");
		$(".checked").show();
		$(".delete-animated3,.gray_box,#change_album_dialog").hide();
	});
//放大图片关闭
	$(document).on("click",".iMIge_close_el",function(){
		$('#M-Gen-lightbox-105,.gray_box,.iMIge').hide();//
		$('.picImg1').removeClass('picImg1');
		$("#gallery_left").show();
		if($(this).parents(".lightbox").find(".iMIge-del").hasClass("recy-iMIge-del")){
			$(".iMIge-del a").text(v_delete);
			$(".recy-iMIge-del").removeClass("recy-iMIge-del");
		}
		$(".iMIge-exif ").css("display","none");
		$(".iMIge-image-container").animate({left:"0px"},200);
	});

	//图片放大
	$(document).on('click','#gallery_list .animated',function(){
		if($(this).parents(".img-list").hasClass("photo_recy_list")){
			$(".lightbox").find(".iMIge-del").addClass("recy-iMIge-del");
			$(".recy-iMIge-del a").text(v_removecompletely);
		}
		$(".iMIge-del").attr("data-id",$(this).parents().attr("id"));
		$(".iMIge-down").attr("data-id",$(this).parents().attr("id"));
		$(".iMIge-info").attr("data-id",$(this).parents().attr("id"));
		var video_width = $(this).attr("video_width");
		var video_height = $(this).attr("video_height");
		if($("#detail_area").hasClass("selection-mode")){
			if($(".checked").length==1) {
				if ($(this).parents("a").next(".checkbox").hasClass("checked")) {
					$(".checked").parents(".image-item").removeClass("selected");
					$(".checked").removeClass("checked")
					$("#detail_area").removeClass("selection-mode");
					$('.gal-toolbar').hide();
				}
				else{
				 $(this).parents("a").next(".checkbox").addClass("checked");
				 $(this).parents(".image-item").addClass("selected");
				 }
				 }else if($(this).parents("a").next(".checkbox").hasClass("checked")){
				 $(this).parents(".image-item").removeClass("selected");
				 $(this).parents("a").next(".checkbox").removeClass("checked");
				 }else{
				 $(this).parents("a").next(".checkbox").addClass("checked");
				 $(this).parents(".image-item").addClass("selected");

				 }
			$(".gal-toolbar-inner .checked_num").text($(".checked").length);
		}else{
			$('.iMIge-next,.iMIge-prev').show();
			if(!$(this).parents("a").find("em").hasClass("ico-vedio")){
//				$('.iMIge-image-fg img').attr('src',$(this).attr('photo-path'));
				str_html = "";
				$(".fadeInDown").hide();
				$(".iMIge-image-fg").show();
				str_html ='<img  style="transform: rotate(0deg);" src = "'+$(this).attr('photo-path')+ '" />';
				$(".iMIge-image-container").css("width","320px");
			}else{
				$(".fadeInDown").show();
				$(".iMIge-image-container").css("width",video_width);
				$(".iMIge-image-container").css("heigth",video_height);
				str_html = pv_q($(this).attr('photo-path'),video_width,video_height);
			}
			$("#iMIge_image_container_up .iMIge-image-fg").html(str_html);
			$('#M-Gen-lightbox-105,.gray_box,.iMIge').show();
			$(this).parents(".image-item").addClass('picImg1');
			$(".lightbox").find(".iMIge-down,.iMIge-info,.iMIge-del").attr("data-id",$(".picImg1").attr("id"));
			//如果后面没有了就隐藏当前按钮
			var temp1 = $('.picImg1').parents('ul').nextAll('ul').eq(0).find('li').eq(0).find('img').attr("src");
			var temp2= $('.picImg1').next('li').find('img').attr("src");
			if(temp1==null&&temp2==null ){
					$('.iMIge-next').hide();
					return false;
			}
			//如果前面没有了就隐藏当前按钮
			var temp3 = $('.picImg1').parents('ul').prevAll('ul:last-child').find('li:last-child').find('img').attr("src");
			var temp4 = $('.picImg1').prev('li').find('img').attr("src");
			if(temp3==null&&temp4==null){
					$('.iMIge-prev').hide();
					return false;
			}
		}
	});

	$(document).on("click",".img-checked-box",function(){
		if($(".checked").length==1){
			if($(this).parents("a").next(".checkbox").hasClass("checked")){
				$(".checked").parents(".image-item").removeClass("selected");
				$(".checked").removeClass("checked")
				$("#detail_area").removeClass("selection-mode");
				$('.gal-toolbar').hide();
			}else{
				$(this).parents("a").next(".checkbox").addClass("checked");
				$(this).parents(".image-item").addClass("selected");
			}
		}else if($(this).parents("a").next(".checkbox").hasClass("checked")){
			$(this).parents(".image-item").removeClass("selected");
			$(this).parents("a").next(".checkbox").removeClass("checked");
		}else{
			$(this).parents("a").next(".checkbox").addClass("checked");
			$(this).parents(".image-item").addClass("selected");

		}
		$(".gal-toolbar-inner .checked_num").text($(".checked").length);
	});

	//下一张
	$(document).on("click",".iMIge-next",function(){
		$(".iMIge-prev").show();
		var temp = $('.picImg1').next('li').find('img').attr("src");
		$('.picImg1').next('li').addClass('picImg1');
		var data_id = $('.picImg1').next('li').attr("id");
		$(".lightbox").find(".iMIge-down,.iMIge-info,.iMIge-del").attr("data-id",data_id);
		if (temp == "" || temp == null) {
			var temp = $('.picImg1').parents('ul').nextAll('ul').eq(0).find('li').eq(0).find('img').attr("src");
			$('.picImg1').parents('ul').nextAll('ul').eq(0).find('li').eq(0).addClass('picImg1');
		}
		$('.picImg1').eq(0).removeClass('picImg1');
		if(!$('.picImg1').find("a").find("em").hasClass("ico-vedio")){
			str_html = "";
			$(".fadeInDown").hide();
			$(".iMIge-image-fg").show();
			str_html ='<img  style="transform: rotate(0deg);" src = "'+$('.picImg1').find("img").attr('photo-path')+ '" />';
			$(".iMIge-image-container").css("width","320px");
		}else{
			$(".fadeInDown").show();
			$(".iMIge-image-container").css("width","1000px");
			str_html = pv_q($('.picImg1').find("img").attr('photo-path'),1000,700);
		}
		$("#iMIge_image_container_up .iMIge-image-fg").html(str_html);
		//如果后面没有了就隐藏当前按钮
		var temp1 = $('.picImg1').parents('ul').nextAll('ul').eq(0).find('li').eq(0).find('img').attr("src");
		var temp2 = $('.picImg1').next('li').find('img').attr("src");
		if (temp1 == "" || temp1 == null) {
			if (temp2 == "" || temp2 == null) {
				$(this).hide();
				return false;
			}
		}
		ajax_photo_info(data_id);
	});
//上一张
	$(document).on("click",".iMIge-prev",function(){
		$('.iMIge-next').show();
		var temp = $('.picImg1').prev('li').find('img').attr("src");
		$('.picImg1').prev('li').addClass('picImg1');
		var data_id=$('.picImg1').attr("id")
		$(".lightbox").find(".iMIge-down,.iMIge-info,.iMIge-del").attr("data-id",data_id);
		if(temp==""||temp==null){
			var temp = $('.picImg1').parents('ul').prevAll('ul:last').find('li:last').find('img').attr("src");
			$('.picImg1').parents('ul').prevAll('ul:last').find('li:last').addClass('picImg1');
		}
		$('.picImg1:last').removeClass('picImg1');
		if(!$('.picImg1').find("a").find("em").hasClass("ico-vedio")){
			str_html = "";
			$(".fadeInDown").hide();
			$(".iMIge-image-fg").show();
			str_html ='<img  style="transform: rotate(0deg);" src = "'+$('.picImg1').find("img").attr('photo-path')+ '" />';
			$(".iMIge-image-container").css("width","320px");
		}else{
			$(".fadeInDown").show();
			$(".iMIge-image-container").css("width","1000px");
			str_html = pv_q($('.picImg1').find("img").attr('photo-path'),1000,700);
		}
		$("#iMIge_image_container_up .iMIge-image-fg").html(str_html);
		//如果前面没有了就隐藏当前按钮
		var temp1 = $('.picImg1').parents('ul').prevAll('ul:last-child').find('li:last-child').find('img').attr("src");
		var temp2 = $('.picImg1').prev('li').find('img').attr("src");
		if(temp1==""||temp1==null){
			if(temp2==""||temp2==null){
				$(this).hide();
				return false;
			}
		}
		ajax_photo_info(data_id);
	});
 $(document).on("click",".iMIge-down",function(){
		 var id = $(this).attr("data-id");
    	layer.confirm(v_down_tips,
			{btn: [v_determine,v_cancel]},
			function(index){
				layer.close(index);
					window.open(actionurl+"?photo_id="+id);
				},
			function(index){
				layer.close(index)
			}
		);
    	$(".layui-layer-title").text(v_information);
	 });
	//放大图片中删除（回收站）
	 $(document).on("click",".iMIge-del",function(){
		if($(this).hasClass("recy-iMIge-del")){
			//放大图片回收站中彻底删除
				 var aid = $(".photo_recy_list").attr("data-id");
				 var id = $(this).attr("data-id")+",";
				 layer.confirm(v_delete_tips,
							{btn: [v_determine,v_cancel]},
							function(index){
								layer.close(index);
								delete_photo(aid,id);
								$("#M-Gen-lightbox-105,#__mask__").hide();
								},
							function(index){
								layer.close(index)
							}
						);
					$(".layui-layer-title").text(v_information);
		}else{
			 var aid = $(".photo_list").attr("data-id");
			 var id = $(this).attr("data-id")+",";
				layer.confirm(v_move_tips,
						{btn: [v_determine,v_cancel]},
						function(index){
							layer.close(index);
								recy_photo(id,1,aid);
								$("#M-Gen-lightbox-105,#__mask__").hide();
							},
						function(index){
							layer.close(index);
						}
					);
				$(".layui-layer-title").text(v_information);
		}
	 });
//点击出现视频
	$("#gallery_list_1 .ico-vedio").parents(".image-item").click(function(){
	 $("#gallery_right .iMIge,.gray_box").show();
	 $(this).addClass("vedio-active");
	 });
	//播放视频
	$(".ico-vedio").click(function(){
		$("#player-progress-ALL_VIDEOS").css("background",$("#player-progress-bar-ALL_VIDEOS").css("background"))
		$(this).css("display","none")
		//调用视频
		$("#myvideodemo")[0].play();
	})
	$(".video").click(function(){
		$("#myvideodemo")[0].pause();
		$(".ico-vedio").show();
	})
	//出现相关信息
	$(".iMIge-info").click(function(){
		if($(".iMIge-exif ").css("display")=="block"){
			$(".iMIge-exif ").css("display","none");
			$(".iMIge-image-container").animate({left:"0px"},200);
		}else{
			$(".iMIge-exif ").css("display","block");
			$(".iMIge-image-container").animate({left:"-100px"},200);
		}
		var id = $(this).attr("data-id");
		ajax_photo_info(id);
	})
//相册中输入框内改变值
	$(document).on("click","#album_list .album-name-area",function(e){
		//e.stopPropagation();
			$(".currentImage").removeClass("currentImage");
			$(this).parents(".image-item").addClass("currentImage");
		$(".currentImage input").val($(".currentImage .album-name").text());
			$(".currentImage").find(".album-name-container").hide();
			$(".currentImage").find("input").css("display","inline-block");
		var value = $(".currentImage").find("input").val();
		$(".currentImage").find("input").val("").focus().val(value);
			$(".currentImage").siblings(".image-item ").find(".album-name-container").show();
			$(".currentImage").siblings(".image-item ").find("input").hide();
	});
	$(document).on("blur",".album-name-edit",function(){
		var initialnum=$(".currentImage .album-name").text();
		var id= $(".currentImage").attr("id");
		var currentvalue= $(".currentImage input").val();
		change_album_name_ajax(id,initialnum,currentvalue);
	});
	//全选
	$(document).on("click","#M-Gen-button-104,#M-Gen-button-108",function(){
		$('.gallery_checkbox').addClass('checked');
		$('.image-item').addClass('selected');
		$(".gt-status .checked_num").text($(".checked").length);
	});
	//全选取消
	$(document).on("click","#M-Gen-button-103,#M-Gen-button-107",function(){
		$("#detail_area").removeClass("selection-mode");
		$('.gallery_checkbox').removeClass('checked');
		$('.image-item').removeClass('selected');
		$('.gal-toolbar').hide();
		$(".gl-check-group").text(v_choice);
	});
//相册中的新建相册
	$('.album-add').click(function(){
		$('#add_album_dialog,.gray_box').show();
	});
//取消功能
	$(document).on("click","#M-Gen-button-101,.close_btn,#M-Gen-button-100 span",function(){
		$('#add_album_dialog,.gray_box,#change_album_dialog').hide();
	});
	//新建功能输入值发生变化
	$("#add_album_dialog input").keyup(function(){
		$("#album_list .image-item").each(function(){
			if($(this).find("input").val()==$("#add_album_dialog input").val()){
				$("#add_album_dialog #createAlbumBtn").removeClass("default").addClass("disabled");
				return false;
			} else{
				$("#add_album_dialog #createAlbumBtn").addClass("default").removeClass("disabled");
			}
		});
	});
	$(document).on("click","#createAlbumBtn",function(){
		$(".album-add").parents(".image-item").before(' <li class="image-item " data-canedit="true"> <a class="img-info" href="javascript:void(0);"> <span class="album-func-area"> <em title="'+v_down_album+'" class="v-gal-down-ico">'+v_down_album+'</em> <em title="'+v_delete_album+'" class="v-gal-del-ico" >'+v_delete_album+'</em> </span> </a>'+
			'<div class="album-name-area"> <div class="album-name-container"> <span class="album-name">'+ $(this).parents(".button-area").prev(".dialog_body").find("input").val()+'</span> </div>'+
			'<input type="text" class="album-name-edit" value="'+  $(this).parents(".button-area").prev(".dialog_body").find("input").val()+'" /> </div> <span class="album-num"> <span id="album-num-5992094103373537">0</span><span>'+v_Photos+'</span> <span class="album-sharedby-"></span> </span>'+
			'<div class="gallery-tip-box" style="display:none"> <p></p> <em class="gallery-tip-triangle"></em> </div> </li>')
		$(this).parents(".button-area").prev(".dialog_body").find("input").val(" ");
		$('#add_album_dialog,.gray_box').hide();
		$(this).removeClass("default").addClass("disabled");
//		$("#global_error_area").find(".notice_content").text("创建相册成功");
		$("#global_error_area .inform-inner").show()
		setTimeout(function(){$("#global_error_area .inform-inner").hide()}, 1000);
	})
	//照片中的年点击，出现相对应的弹窗
	$('.entry-to-month-pick').click(function(){
		$('.overlay-month-picker').show();
	});
	//年份关闭键
	$('.close-month-picker').click(function(){
		$('.overlay-month-picker').hide();
	});
//锚点定位
	$(".content-monthlist-item-month-pick").click(function(){
		var temp=$(this).attr("data-startdate").substring(0,6);
		$('#time_image_container .the-month').each(function(){
			var temp1=$(this).attr("data-day").substring(0,6);
			if(temp==temp1){
				var temp2 = $(this).parents('.time-mode-container').find('.the-month').eq(0).offset().top;
				var temp3=$(this).parents('#gallery_detail_time').prev(".fixed-area").offset().top;
				var len=temp2-temp3;
				$('#detail_area').animate({scrollTop:len},50);
				return false;
			}
		});
		$('.overlay-month-picker').hide();
	});
	//照片
	$(document).on("click","#gallery_tab_time",function(){
		$('#gallery_right_time,.album-title-area').show();
		$('#gallery_right,.album-title-area').hide();
		$('#gallery_right_recycle').hide();
		$(this).addClass("current").siblings().removeClass('current');
	});
	//移动相册li
	$("#album_name li").click(function(){
		$(this).siblings().removeClass('current');
		$(this).addClass('current');
	})
	//返回
	$('#gallery_back_button').click(function(){
		if($(this).attr("data-name")=="photo_re"){
			$('.album-title-area,.gallery_list,#gallery_detail_1,.fixed-area').hide();
			$('#album_frame').show();
		}else if($(this).attr("data-name")=="photo_recy"){
			$('.album-title-area,.gallery_list,#gallery_detail_1,.fixed-area').hide();
			$('#album_frame,#gallery_right_recycle').show();
		}
	});

	$(document).on("click","#moveGalleryBtn,#copyGalleryBtn",function(){
		$(".gal-toolbar").hide();
		$(".image-item").removeClass("selected");
		$("#detail_area").removeClass("selection-mode");
		$(".checked").removeClass("checked");
	});

});