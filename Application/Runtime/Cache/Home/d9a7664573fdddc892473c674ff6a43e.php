<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html lang="en">
<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1,maximum-scale=1,user-scalable=no">
		<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
		<meta name="HandheldFriendly" content="true">
		<meta name="apple-mobile-web-app-capable" content="yes">
		<meta name="apple-mobile-web-app-status-bar-style" content="black">
		<meta name="format-detection" content="telephone=no">
		<meta http-equiv="x-dns-prefetch-control" content="on" />
		<link rel="dns-prefetch" href="//<?php echo getServerName();?>"/>
		<link rel="dns-prefetch" href="//download.kenxinda.com"/>
		<link rel="dns-prefetch" href="//s3-ap-southeast-1.amazonaws.com"/>
		<title><?php echo ($title); ?></title>
		<?php if(is_array($loader_css_array)): $i = 0; $__LIST__ = $loader_css_array;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><link type="text/css" rel="stylesheet" href="/cloud/Public<?php echo ($vo); ?>" /><?php endforeach; endif; else: echo "" ;endif; ?>
		<!--[if lte IE 8]>
		<![endif]-->
		<!--[if IE 8]>
		<link type="text/css" rel="stylesheet" href="/cloud/Public/css/ie8.css">
		<![endif]-->
		<script type="text/javascript">
		  var is_mobile = false;
 		  var base_url = "<?php echo getBaseURL();?>";
 		  var base_module_url = base_url + "<?php echo (MODULE_NAME); ?>";
 		</script>
		<script type="text/javascript" src="/cloud/Public/js/jquery-1.8.3.min.js"></script>
		<script type="text/javascript" src="/cloud/Public/lib/layer/layer.js"></script>
		<script type="text/javascript" src="/cloud/Public/js/var/<?php echo ($lang_iso); ?>/common.js"></script>
		<script type="text/javascript" src="/cloud/Public/js/common.js"></script>		
		<?php if(is_array($loader_js_array)): $i = 0; $__LIST__ = $loader_js_array;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$volist): $mod = ($i % 2 );++$i;?><script type="text/javascript" src="/cloud/Public<?php echo ($volist); ?>"></script><?php endforeach; endif; else: echo "" ;endif; ?> 
</head>
<body lang="<?php echo ($lang_iso); ?>" langloaded="micloud--lang--<?php echo ($lang_iso); ?>">
<script type="text/javascript">
	page_size = <?php echo ($page_size); ?>;
	total_count = <?php echo ($total_count); ?>;
	list_total_count = total_count;
	list_current_page = current_page;	
</script>
<script type="text/javascript" src="/cloud/Public/js/left_menu.js"></script>
<div class="v-nav-ul-container" id="g_slider_menu" style="display:block;left:-139px;"> <!-- left:-139px;-->
	<div class="slider-nav-bg"></div>
	<ul class="v-home-ul slider-nav clearfix">	
		<?php if(is_array($left_suspend_plugin_list)): $i = 0; $__LIST__ = $left_suspend_plugin_list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><li <?php if($vo["name"] == $control_name): ?>class="highlight"<?php endif; ?>>
				<a href="<?php echo ($vo["url"]); ?>">
		            <span class="animation-area vhu-item1">
			              <em class="ico-area v-ico-<?php echo ($vo["name"]); ?>"></em>
			              <em class="ico-lock" style="display:none"></em>
			              <img src="/cloud/Public/images/v-home-ico-<?php echo ($vo["name"]); ?>.png" width="60" class="for-noanimation">
						
		            </span>
					<span class="vhu-con g_<?php echo ($vo["name"]); ?>"><?php echo ($vo["display_name"]); ?></span>
				</a>
			</li><?php endforeach; endif; else: echo "" ;endif; ?> 
		
	</ul>
</div>
<div class="global-loading loading-animation" id="init_loading" style="display: none;"></div>
<div class="layout" style="" id="kcloud_main_frame">
	<div class="global-head clearfix" id="g_new_navbar" style="display: block;">
		<a href="javascript:void(0);" class="current-nav" id="g_switch_menu">
			<em class="menu-more-ico" style="display: inline-block;"></em>
			<span id="g_mod_name" style="display: inline;"><?php echo (L("Contact")); ?></span>
		</a>
		<script type="text/javascript" src="/cloud/Public/js/right_user_info.js"></script>
<div class="vh-avator-area">
		<div id="g_userinfo" class="gl-lang-container">
			<div class="account-avator">
				<img src="<?php echo ($user_info["portrait"]); ?>" id="g_useravator">
				<div class="account-avator-bg"></div>
			</div>
			<span class="account-name" id="g_username" ><?php echo ($user_info["nick_name"]); ?> / <?php echo (L("Account")); ?></span>
			<em class="ico-lang-down"></em>
		</div>
		<ul class="gl-lang-ul gl-lang-ul-account" id="lang_ul2" style="display:none">			
			<li><a href="<?php echo getBaseURL();?>Home/Index/security.html" target="_blank" class="gl-link" id="g_my_account"><?php echo (L("Account")); ?></a></li>			
			<li><a href="<?php echo getBaseURL();?>Home/Index/logout.html" class="gl-link" id="g_logout"><?php echo (L("Logout")); ?></a></li>
		</ul>
	</div>
	</div>
	<div class="mdl js_frame animated fadeInLeft" id="kcloud_window" >
		<div class="mdl-m-m">
			<div class="mdl-m-m-l" id="kcloud_left_frame">
				<div id="contact_left" class="contact-list-padd">
					<div class="global-search k-search search-hascon clearfix">
						<i></i>
						<em id="contact_search_cancel" style="display:none"></em>
						<input type="text" placeholder="<?php echo (L("Search")); ?>" id="contact_search_input" autocomplete="off" name='contact_search_input'/>
						<div id="clear_search"><img src="/cloud/Public/images/color-close.png" alt="photo" /></div>
						<input type="hidden" id="sk" name="sk" value="" />
					</div>
					<div class="contact-group-select" id="contact_select_group">
						<a onclick="return false" id="contact_select_group_trigger" class="toolmenu">
							<span class="group-name" style="margin-right:3px"><?php echo (L("Allcontacts")); ?></span>
							<span class="group-num">( <?php echo ($total_count); ?> )</span> <!-- <em></em>   -->
						</a>
						<em class="yel-tooltips">
					       <span class="yt-area">
					       		<em class="yt-corner"></em>
						       <span class="yt-con">
							       <b><?php echo (L("NotSamecontactsTips")); ?></b>
							       1、<?php echo (L("Cloudcontacts")); ?><br />
							       2、<?php echo (L("Prompt")); ?><br />
							       3、<?php echo (L("Automatically")); ?><br />
						       </span>
						   </span>
						</em>
						<!--
						 <div style="position: relative;">
							<div class="dialog-menu animated2 fadeInDown switch-group multiselectionmenu" id="M-Gen-multiselectionmenu-102" style="width: 218px; left: 0px; position: absolute; z-index: 5;display:none;">
								<ul>
									<li data-index="0" class="menu-item selected"><a href="javascript:void(&quot;contact/groupswitch&quot;)" class="checkbox checked"><em></em></a><span class="li-font"><span class="group-name">所有联络人&nbsp;</span><span class="group-num">(133)</span></span></li>
								</ul>
								<em class="menu-corner up"></em>
							</div>						
						</div>
						 -->
					</div>
					<div class="contact-group-select" id="contact_search" style="display:none">
						<span><?php echo (L("find")); ?><span id="contact_search_count"> 0 </span><?php echo (L("Contacts")); ?></span>
						<em class="yel-tooltips">
					       <span class="yt-area">
					       		<em class="yt-corner"></em>
						       <span class="yt-con">
							       <b><?php echo (L("NotSamecontactsTips")); ?></b>
							       1、<?php echo (L("Cloudcontacts")); ?><br />
							       2、<?php echo (L("Prompt")); ?><br />
							       3、<?php echo (L("Automatically")); ?><br />
						       </span>
						   </span>
						</em>
					</div>
					<div class="contact-merge-enter" style="display:none;z-index:4;">
						<?php echo (L("Duplicate")); ?>
						<a href="javascript:void(0)"><?php echo (L("Mergecontact")); ?></a>
						<em class="sn-close"></em>
					</div>
					<div class="frame-area" id="contact_frame_resizeable">
						<div class="left-corner"></div>
						<div class="right-corner"></div>
						<div class="left-bottom-corner"></div>
						<div class="right-bottom-corner"></div>
						<div class="contact-list-div" id="contactListFrame" style="height:100%">							
							<ul id="scroll_container_contact1" class="global-list contact-list" style="position: relative;">
							<?php if($total_count > 0): if(is_array($list_array)): $k = 0; $__LIST__ = $list_array;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($k % 2 );++$k; if($vo["display"] == true): ?><li class="contactlist-index contact-frist"  <?php if($check_first_spell == 1): ?>id="first_spell_data"<?php endif; ?>> <p><?php echo ($vo["first_spell"]); ?></p> </li>
									<?php if($check_first_spell == 1): $check_first_spell = '0'; endif; endif; ?>								
								<li data-id="<?php echo ($vo["id"]); ?>"  <?php if($k == 1): ?>id="first_data"<?php endif; ?> class="list_selection_item  ">
									<div class="contactlist-container">
										<p class="contact-nickname"><?php echo ($vo["display_name"]); ?></p>
										<a class="checkbox item_checkbox  chkbox contact-information" href="javascript:void(&quot;contact/checkonefromlist&quot;);"><em></em></a>
									</div> 
								</li><?php endforeach; endif; else: echo "" ;endif; endif; ?>	
							</ul>							
							<p class="no_data" style="<?php if($total_count > 0): ?>display:none;<?php else: ?>display:block;<?php endif; ?>" ><?php echo (L("NoData")); ?></p>							
							<ul id="scroll_container_contact2" class="global-list contact-list" style="position: relative; display: none;">
								<li class="item-space-list" style="height:0px;border:0 none"></li>
								<li class="contact-empty-tip">
									<div>
										<?php echo (L("Informationblank")); ?>
									</div>
									<div class="contact-empty-desc">
										<a href="#contact/editor"><?php echo (L("Newcontacts")); ?></a> <?php echo (L("Or")); ?>
										<a href="#home/how"><?php echo (L("Synchronize")); ?></a>
									</div></li>
							</ul>
						</div>
					</div>
					<div class="button-area clearfix" id="contact_operate_bar">
					
						<div class="icobtn-add button fl-l" style="margin-right:10px" id="merge_contact">
							<span class="btn-ct" ><?php echo (L("Mergecontact")); ?></span>
							<div class="btn-tips">
								<div class="tip">
									<?php echo (L("Mergecontact")); ?>
								</div>
								<em></em>
							</div>
							<span class="ico-red-point" style="display:none;"></span>
						</div>
						<div class="icobtn-more button fl-l" id="contact_more_operate">
							<span class="btn-ct"><?php echo (L("Moreoperation")); ?></span>
							<div class="btn-tips">
								<div class="tip">
									<?php echo (L("Moreoperation")); ?>
								</div>
								<em></em>
							</div>
						</div>						
						<div class="icobtn-add button fl-r" id="create_contact">
							<span class="btn-ct"><?php echo (L("Newcontacts")); ?></span>
							<div class="btn-tips">
								<div class="tip">
									<?php echo (L("Newcontacts")); ?>
								</div>
								<em></em>
							</div>
						</div>
					</div>
					<div class="button-area clearfix select-all" id="contact_sel_bar" style="display:none">
						<div id="all_button">
							<div class="fl-r link-button" id="contact_sel_all_button">
								<?php echo (L("Select")); ?>
							</div>
						</div>
					</div>

				</div>
			</div>
			<div class="mdl-m-m-r" id="kcloud_right_frame" >
				<div id="contact_right" class="contact-empty" style="display:block;">
		<!-- <?php echo (L("Importcontacts")); ?> -->
				<div id="contact_right" class="import" style="display:none;">
					<div style="height: 100%;" id="import-contact-panel" class="contact-import-panel m-p m-p-sh panel"> 
						<div class="m-p-head clearfix"> 
							<div class="m-p-head-ct"> 
								<h4 class="contact-title"><?php echo (L("Importcontacts")); ?></h4> 
							</div> 
						</div> 
						<div class="m-p-frame panel_body"> 
							<div class="contact-import"> 
								<p class="ptips1"><?php echo (L("Importtips")); ?></p> 
								<form enctype="multipart/form-data" method="post" id="import_contact_form" target="import_contact_frame" action=""> 
									<div class="button-area contact-import-button clearfix"> 
										<div class="button">
											<span class="btn-ct"><?php echo (L("Selectfile")); ?></span>
											<input type="file" class="transparent-file" id="file" name="file" />
										</div> 
									</div> 
									<input style="display:none" id="serviceToken" name="serviceToken" /> 
								</form> 
								<iframe style="height:0;width:0;border:0px;" id="import_contact_frame" name="import_contact_frame"></iframe> 
								<p class="filename nowrap" id="contact_file_name" style="display:none;white-space:normal;"></p> 
								<div class="button-area clearfix"> 
									<div class="button default" id="import_contact_button" style="display:none">
										<span class="btn-ct"><?php echo (L("Startimporting")); ?></span>
									</div> 
								</div> 
							</div> 
						</div> 
					</div>
				</div>
				<div id="contact_right" class="export" style="display:none;">
					<div style="height: 100%;" id="export_contact_panel" class="contact-import-panel m-p m-p-sh panel"> 
						<div class="m-p-head clearfix"> 
							 <div class="m-p-head-ct"> 
								<h4 class="contact-title"> <?php echo (L("Exportcontacts")); ?></h4> 
							 </div> 
						</div> 
						<div class="m-p-frame panel_body contact-import"> 
							<p class="ptips1"><?php echo (L("Exporttip")); ?></p> 
							 <div class="button-area clearfix"> 
								<a class="button default" href="javascript:void(0);" id="export_contact_button"><span class="btn-ct"><?php echo (L("Startexporting")); ?></span></a> 
							 </div> 
						</div> 
						<iframe id="export_contact_frame" name="export_contact_frame" style="display:none"></iframe> 
					</div>
				</div>
					<div class="m-p m-p-sh panel" id="edit-contact-padd" style="height: 100%;display:none;">
						<div class="m-p-head clearfix">
							<div class="m-p-head-ct">
								<h4 class="contact-title"><?php echo (L("Editcontact")); ?></h4>
							</div>
						</div>
						<div class="m-p-frame panel_body" id='edit'>
						</div>
						<div class="m-p-foot button-area clearfix tl-r">
							<div class="fl-l button" id="M-Gen-button-114">
								<span class=" btn-ct"><?php echo (L("Cancel")); ?></span>
							</div>
							<div class="default fl-l button" id="M-Gen-button-115">
								<span class=" btn-ct"><?php echo (L("Determine")); ?></span>
							</div>
							<div class="icobtn-del fl-r button" id="M-Gen-button-116">
								<span class=" btn-ct"><?php echo (L("Delete")); ?></span>
								<div class="btn-tips">
									<div class="tip">
										<?php echo (L("Delete")); ?>
									</div>
									<em></em>
								</div>
							</div>
						</div>
					</div>
					<div class="default-hint" >
						<span class="hint-content"><?php echo (L("Clickdetails")); ?></span>
					</div>
					<div style="height: 100%;display:none;!important" class="contact-merge-panel" >
						<div class="m-p-head clearfix">
							<div class="m-p-head-ct">
								<h4 class="contact-title"><?php echo (L("MergeContacts")); ?></h4>
							</div>
						</div>
						<ul class="cm-index clearfix">
							<li class="fl-l current"><span class="cm-i-con"><?php echo (L("Duplicatecontacts")); ?></span><em class="cm-i-ico"></em></li>							
							<li class="fl-l last"><span class="cm-i-con"><?php echo (L("Mergeposscontacts")); ?></span><em class="cm-i-ico"></em></li>
						</ul>
						<div style="padding-top:140px;" class="tl-c">
							<div class="button" id="button-conatact">
								<span class="btn-ct"><?php echo (L("Lookduplicatecontacts")); ?></span>
							</div>
							<p class="cm-tip"><?php echo (L("Looktip")); ?></p>
						</div>
					</div>
					<div style="height: 100%;width: 97%;position: relative;left: 16px;display:none;" id="contact-panel">
						<div class="m-p-head clearfix">
							<div class="m-p-head-ct">
								<h4 class="contact-title"><?php echo (L("MergeContacts")); ?></h4>
							</div>
						</div>
						<ul class="cm-index clearfix">
							<li class="fl-l current"><span class="cm-i-con"><?php echo (L("Duplicatecontacts")); ?></span><em class="cm-i-ico"></em></li>							
							<li class="fl-l last current"><span class="cm-i-con"><?php echo (L("Mergeposscontacts")); ?></span><em class="cm-i-ico"></em></li>
						</ul>
						<div style="padding-top: 140px; display: none;" class="tl-c">
							<div class="button" id="button-contact">
								<span class="btn-ct"><?php echo (L("Lookduplicatecontacts")); ?></span>
							</div>
							<p class="cm-tip"><?php echo (L("Looktip")); ?></p>
						</div>
						<div class="cm-contact-container" style=" height: 75%;">
							<div class="cmcc-list clearfix first">
								<p class="fl-l cmcc-title"><?php echo (L("Possibledupcontacts")); ?> （<span id="possible_num">0</span><?php echo (L("Group")); ?>）</p>
								<div class="cmcc-check-area cmcc-check-first">
									<em class="checkbox" data-type="all"></em>
								</div>
							</div>
							<div class="cmcc-data-container">								
							</div>
						</div>
						<div class="button-area clearfix">
							<div class="button fl-r ignore-contact">
								<span class="btn-ct"><?php echo (L("Ignorecontact")); ?></span>
							</div>
							<div class="button fl-r disabled merge-contact">
								<span class="btn-ct"><?php echo (L("MergeP")); ?><span id="already_merge_count">0</span><?php echo (L("MergeContactP")); ?></span>
							</div>
						</div>
					</div>
					<div style="height: 100%;display:none;" id="merge-panel1">
						<div class="m-p-head clearfix  merge-contacts" style="display:none;">
							<div class="m-p-head-ct">
								<h4 class="contact-title"><?php echo (L("MergeContacts")); ?></h4>
								<div style="border:solid 1px #f2f2f2;width:112%;"></div>
							</div>
							<div class="merge-complete">
								<p style="top:240px;"><?php echo (L("AlreadyMergeP")); ?><span class="merge-count">0</span><?php echo (L("MergeContactP")); ?></p>
							</div>
						</div>
						<div class="m-p m-p-sh panel" id="create-contact-padd" style="height: 100%;display:none;">
							<div class="m-p-head clearfix" id="m-p-head-rt">
								<div class="m-p-head-ct" >
									<?php echo (L("Newcontacts")); ?>
								</div>
							</div>
							<div class="m-p-frame panel_body" id='newbuild'>
							</div>
							<div class="m-p-foot button-area clearfix tl-l">
								<div id="M-Gen-button-10011" class="button">
									<span class=" btn-ct"><?php echo (L("Cancel")); ?></span>
								</div>
								<div class="default button" id="M-Gen-button-101">
									<span class=" btn-ct"><?php echo (L("NewBuild")); ?></span>
								</div>
							</div>
						</div>
					</div>
					<div class="m-p m-p-sh panel" id="contact_select_mode" style="width: auto; height: 100%;display:none;">
						<div class="m-p-head clearfix">
							<div class="m-p-head-ct">
								<?php echo (L("Currentlyselected")); ?>
								<span id="select_contact_count">1</span><?php echo (L("Contacts")); ?>
							</div>
						</div>
						<div class="m-p-frame panel_body">
							<div class="contact-avatorlist-container" id="contact_selectbox" style="overflow:auto">
								<ul class="contact-avatorlist clearfix" id="contact_selected_container">
									<li id="contact_selected" style="display:none">
										<div class="ca-avator">
										</div> <p> </p> </li>
									<li id="contact_selected1" style="display:none">
										<div class="ca-avator">
										</div> <p> </p> </li>
									<li id="contact_selected2" style="display:none">
										<div class="ca-avator">
										</div> <p> </p> </li>
								</ul>
							</div>
						</div>
						<div class="m-p-foot button-area clearfix tl-r">
							<div class="fl-l button" id="M-Gen-button-122">
								<span class=" btn-ct"><?php echo (L("Cancel")); ?></span>
							</div>
						<!-- <div class="icobtn-group button" id="M-Gen-button-123" >
								<span class="btn-ct toolmenu" id="M-Gen-toolmenu-125" style="height: 27px;background-position: -239px -196px;"><?php echo (L("Group")); ?></span>
								<div class="btn-tips">
									<div class="tip">
										<?php echo (L("Group")); ?>
									</div>
									<em></em>
								</div>
								<div style="position: absolute;">
									<div class="dialog-menu animated2 fadeInDown multiselectionmenu" id="M-Gen-multiselectionmenu-109" style="width: 150px;display:none; left: -47.5px; position: absolute; bottom: 42px;">
										<ul>
											<li data-index="0" class="menu-item  ">
												<a href="javascript:void(0)" class="checkbox ">
													<em></em>
												</a>
												<span class="li-font">11111111111111111</span>
											</li>
										</ul>
										<div class="add-group menu-addgroup tl-l" id="M-Gen--108">
											<span class=" btn-ct" style="height: 46px;line-height: 46px;"><?php echo (L("NewBuild")); echo (L("Group")); ?></span>
										</div>
										<em class="menu-corner bottom"></em>
									</div>
								</div>
							</div>-->
							
							<div class="icobtn-del button" id="M-Gen-button-124" style=" margin-left: -4px;">
								<span class=" btn-ct" style="height:30px;   background-position: -150px -49px;"><?php echo (L("Delete")); ?></span>
								<div class="btn-tips">
									<div class="tip">
										<?php echo (L("Delete")); ?>
									</div>
									<em></em>
								</div>
							</div>
							<div class="icobtn-add button fl-l"  id="restore_contact">
								<span class="btn-ct"><?php echo (L("Resumecontact")); ?></span>
								<div class="btn-tips">
									<div class="tip">
										<?php echo (L("Resumecontact")); ?>
									</div>
									<em></em>
								</div>
							</div>
						</div>
					</div>					
					<div class="m-p m-p-sh panel" id="contact-detail-padd2" style="width: auto; height: 100%;">
						<div  id="contact-padd2" style="width: auto; height: 100%;display:none">
							<div class="m-p-frame panel_body">
								<div id="contact_detail">
									<dl class="contact-details cd-thefirst clearfix">
										<dt style="overflow:visible" class="contact-edit-avator">
											<a class="contact-avatars" href="javascript:void(&quot;contact/avata&quot;)"> </a>
										<div class="avatars-area">
											<span class="aa-func-link delete-link" style="display:none"><?php echo (L("Delete")); ?></span>
											<span class="aa-func-link change-link"><?php echo (L("AddPortrait")); ?></span>
										</div>
										</dt>
										<dd>
											<div class="cd-div">
												<p class="cd-field"> </p>
												<p class="cd-nickname"> </p>
												<p class="cd-intro"> </p>
												<p class="cd-group" title=""> </p>
											</div>
										</dd>
									</dl>
									<div id="contact_detail_item_list" class="contact-details-contaniner" style="overflow:auto;">
										<dl class="contact-details contact-tosms clearfix">											
										</dl>
									</div>
								</div>
							</div>
							<div class="m-p-foot button-area clearfix tl-r" style="position: relative;">
								<div class="icobtn-edit fl-l button" id="M-Gen-button-132">
									<span class=" btn-ct" style=" background-position: -97px -44px;height: 31px;    margin: 0 10px;"><?php echo (L("Edit")); ?></span>
									<div class="btn-tips">
										<div class="tip">
											<?php echo (L("Edit")); ?>
										</div>
										<em></em>
									</div>
								</div>
								<div class="fl-r icobtn-del button" id="M-Gen-button-133">
									<span class=" btn-ct" style=" height: 26px;  background-position: -152px -46Fpx;    margin: 0 10px;"><?php echo (L("Delete")); ?></span>
									<div class="btn-tips">
										<div class="tip">
											<?php echo (L("Delete")); ?>
										</div>
										<em></em>
									</div>
								</div>
								
								<div class="icobtn-add button fl-l restore_contact">
									<span class="btn-ct"><?php echo (L("Resumecontact")); ?></span>
									<div class="btn-tips">
										<div class="tip">
											<?php echo (L("Resumecontact")); ?>
										</div>
										<em></em>
									</div>
								</div>								
							</div>
						</div>
					</div>
					<!--</div>-->
					<div class="frame-container js_frame" id="kcloud_iframe_container" style="display: none; margin: 0px auto; width: 736.842px; height: 500px;"></div>
				</div>

				<div  id="global_popup" style="display:none;" >
					<div id="group-build" >
						<div class="inform-container" id="global_error_area">
							<div class="inform-inner" style="display: none;">
								<span class="inform-span notice_content"></span>
								<a href="#" onclick="location.reload();return false" style="display:none"><?php echo (L("Retry")); ?></a></div>
						</div>
						<div id="gloading" class="" style="z-index: 9990; display: none;"></div>
						<div class="m-p animated3 pulse clearfix dialog" zindex="110" id="M-Gen-dialog-106" style="margin-left: -204px; left: 50%; top: 290.5px; z-index: 110;">
							<!-- <div class="m-p-head clearfix" id="m-head" >
								<div class="m-p-head-ct">
									<?php echo (L("NewBuild")); echo (L("Group")); ?>
								</div>
								<a href="javascript:void(0)" class="close_btn"></a>
							</div>
							<div class="m-p-frame dialog_body">
								<div class="m-p-promot dialog-input-area clearfix">
									<input type="text" placeholder="<?php echo (L("Group")); ?>" class="m-p-pm-input dialog-input" />
								</div>
							</div>
							<div class="m-p-foot button-area clearfix tl-r">
								<div id="M-Gen-button-1041" class="button">
									<span class=" btn-ct"><?php echo (L("Cancel")); ?></span>
								</div>
								<div class="default button" id="M-Gen-button-1051">
									<span class=" btn-ct"><?php echo (L("Determine")); ?></span>
								</div>
							</div> -->
						</div>
					</div>
					<div class="m-p animated3 pulse clearfix dialog dialog-prompt" zindex="130" id="M-Gen-dialog-138" style="width: 400px;display:none; margin-left: -200px; left: 50%; top: 357px; z-index: 130;">
						<div class="m-p-head clearfix">
							<div class="m-p-head-ct">
								<?php echo (L("Tips")); ?>
							</div>
							<a href="javascript:void(0)" class="close_btn"></a>
						</div>
						<div class="m-p-frame dialog_body">
							<div class="dialog-tips">
								<?php echo (L("Groupnamenotempty")); ?>
							</div>
						</div>
						<div class="m-p-foot button-area clearfix tl-r">
							<div class="default button" id="M-Gen-button-137">
								<span class=" btn-ct"><?php echo (L("Determine")); ?></span>
							</div>
						</div>
					</div>
					<div class="gray_box" id="__mask__" ></div>
				</div>
				<div class="gray_box" id="__mask1__"></div>
				<div class="m-p animated3 pulse clearfix dialog" zindex="120" id="M-Gen-dialog-123" style="width: 400px; margin-left: -200px; left: 50%; top: 357px; z-index: 120;display:none">
					<div class="m-p-head clearfix">
						<div class="m-p-head-ct">
							<?php echo (L("Tips")); ?>
						</div>
						<a href="javascript:void(0)" class="close_btn"></a>
					</div>
					<div class="m-p-frame dialog_body">
						<div class="dialog-tips">
							<?php echo (L("Deletecontact")); ?>
						</div>
					</div>
					<div class="m-p-foot button-area clearfix tl-r">
						<div id="M-Gen-button-121" class="button">
							<span class=" btn-ct"><?php echo (L("Cancel")); ?></span>
						</div>
						<div class="default button" id="M-Gen-button-1221">
							<span class=" btn-ct"><?php echo (L("Determine")); ?></span>
						</div>
					</div>
				</div>
				<div class="m-p animated3 pulse clearfix dialog  pulse-dialog" zindex="150" id="M-Gen-dialog-123" style="width: 400px;display:none; margin-left: -200px; left: 50%; top: 379px; z-index: 150;">
					<div class="m-p-head clearfix">
						<div class="m-p-head-ct">
							<?php echo (L("Tips")); ?>
						</div>
						<a href="javascript:void(0)" class="close_btn"></a>
					</div>
					<div class="m-p-frame dialog_body">
						<div class="dialog-tips">
							<?php echo (L("Phonetips")); ?>
						</div>
					</div>
					<div class="m-p-foot button-area clearfix tl-r">
						<div class="default button" id="M-Gen-button-122">
							<span class=" btn-ct"><?php echo (L("Determine")); ?></span>
						</div>
					</div>
				</div>
				<div id="type_selector_0" class="dialog-menu func-menu animated2 fadeInUp">
					<ul id="type_selector_content_0">
						<li ctype="list"> <span class="li-font js_item"> <?php echo (L("List")); ?> </span> </li>
						<li ctype="recycle"> <span class="li-font js_item"> <?php echo (L("Recycle")); ?> </span> </li>
						<li ctype="clearAll"> <span class="li-font js_item"> <?php echo (L("Emptyall")); ?> </span> </li>
						<!--						
						<li ctype="fliter"> <span class="li-font js_item"> <?php echo (L("Phonecontacts")); ?> <a class="switch switch-close js_switch" href="javascript:void(0);"><?php echo (L("Switch")); ?></a> </span> </li>
						<li ctype="import"> <span class="li-font js_item"> <?php echo (L("Importcontacts")); ?> </span> </li>
						<li ctype="export"> <span class="li-font js_item"> <?php echo (L("Exportcontacts")); ?> </span> </li> 
						-->
					</ul>
					<em class="menu-corner"></em>
				</div>
				<div class="dialog clearfix dialog-contact" zindex="110" id="M-Gen-dialog-102">
					<div class="m-p-head clearfix m-p-head-contact">
						<div class="m-p-head-ct">
							<?php echo (L("Mergecontact")); ?> （<span class="current_merge">1</span>/<span class="merge_total_count">1</span>）
						</div>
						<a href="javascript:void(0)" class="close_btn"></a>
					</div>
					<div class="m-p-frame dialog_body">
						<div class="merge-same-contact clearfix" style="padding:20px 20px 0 20px;">
							<div class="fl-l">
								<p class="msc-title"><span id="repeat_contacts">0</span><?php echo (L("RepeatContactPart")); ?> <span> (<?php echo (L("MergeContactPart")); ?><em class="msc-index-num">1</em><?php echo (L("Prevail")); ?>)：</span></p>
								<div class="pos-r has-scroll">
									<div class="msc-data-container">
										
									</div>
								</div>
							</div>
							<div class="fl-r msc-merge-detail">
								<p class="msc-title"><?php echo (L("Merged")); ?>：</p>
								<div class="multi_merge">
								</div>
							</div>
						</div>
					</div>
					<div class="m-p-foot button-area clearfix tl-r">
						<div id="M-Gen-button-100" class="button">
							<span class=" btn-ct skip-btn-ct"><?php echo (L("Skipgroup")); ?></span>
						</div>
						<div class="default button">
							<span class=" btn-ct merge-btn-ct"><?php echo (L("Mergegroup")); ?></span>
						</div>
					</div>
				</div>
<div style="display: none;">
<script type="text/javascript">var cnzz_protocol = (("https:" == document.location.protocol) ? " https://" : " http://");document.write(unescape("%3Cspan id='cnzz_stat_icon_1261146263'%3E%3C/span%3E%3Cscript src='" + cnzz_protocol + "s4.cnzz.com/z_stat.php%3Fid%3D1261146263%26show%3Dpic1' type='text/javascript'%3E%3C/script%3E"));</script>
</div>
</body>
</html>