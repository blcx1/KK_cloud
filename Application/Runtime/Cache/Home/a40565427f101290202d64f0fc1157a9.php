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
<div class="layout" id="new_home_frame" style=" position: absolute; top: 0px; z-index: 2;">
	<div class="global-head show clearfix">
		<div class="account-area" style="display:none">
			<p class="aa-equipment" style="visibility: visible; display: inline-block;">
				<span id="g_your_connect"></span>
				<span id="g_device_num"></span>
				<span id="g_device_count"><?php echo ($device_total_count); echo (L("Platform")); echo (L("Connected")); ?></span></p>
			<script type="text/javascript" src="/cloud/Public/js/language.js"></script>
<div class="global-language">
	<form action="" method="get" name="lang_form" id="lang_form">
		<p class="gl-lang-container" id="check_lang">
			<em class="ico-lang-bg"></em>
			<select name="l"  onchange="javascript:$('#lang_form').submit();" id="g_current_lang">
				<?php if(is_array($iso_lang_list)): $i = 0; $__LIST__ = $iso_lang_list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><option value="<?php echo ($key); ?>" <?php if( $vo["name"] == $lang_name ): ?>selected="selected"<?php endif; ?>><?php echo ($vo["name"]); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
			 </select>
		</p>
	</form>
</div>

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
	</div>
	<div class="v-ul-container" style="display:none">
		<ul class="v-home-ul home-animation clearfix">
			<?php if(is_array($left_suspend_plugin_list)): $i = 0; $__LIST__ = $left_suspend_plugin_list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><li>
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
</div>
<div id="popup" style="display:none;">
	<iframe name="hidden_message_frame_1" style="display: block;"></iframe>
	<div class="dialog clearfix web-dialog animated3 pulse" id="dialog_0" style="width: 480px; z-index: 1000; top: 243px; left: 435.5px;">
		<div class="m-p-head clearfix dialog_header">
			<div class="m-p-head-ct">
				<?php echo ($device_total_count); echo (L("Platform")); echo (L("Connected")); ?>
			</div>
			<a href="javascript:void(0)" id="g_add_device" class="head-btn hb-add-device"><span class="btn-ct"><?php echo (L("Add")); ?></span></a>
			<a href="javascript:void(0)" id="close_device" class="head-btn hb-close"><?php echo (L("Close")); ?></a>
		</div>
		
		<div class="m-p-foot v4-btn-area tl-c dialog_footer">
		</div>
	</div>
	<div class="dialog clearfix web-dialog animated3 pulse" id="dialog_11" style="width: 400px; z-index: 1011; top: 241.5px; left: 475.5px;display:none;" >
		<div class="m-p-head clearfix dialog_header">
			<div class="m-p-head-ct">
				<?php echo (L("Adddevice")); ?>
			</div>
			<a href="javascript:void(0)" id="g_add_back" class="head-btn hb-close"><span class="btn-ct"><?php echo (L("Back")); ?></span></a>
		</div>
		<div class="m-p-foot v4-btn-area tl-c dialog_footer">
		</div>
	</div>
	<div class="gray_box web-gray" id="__mask2__" style="z-index: 109; width: 100%; height: 100%;"></div>
</div>
<div style="display: none;">
<script type="text/javascript">var cnzz_protocol = (("https:" == document.location.protocol) ? " https://" : " http://");document.write(unescape("%3Cspan id='cnzz_stat_icon_1261146263'%3E%3C/span%3E%3Cscript src='" + cnzz_protocol + "s4.cnzz.com/z_stat.php%3Fid%3D1261146263%26show%3Dpic1' type='text/javascript'%3E%3C/script%3E"));</script>
</div>
</body>
</html>