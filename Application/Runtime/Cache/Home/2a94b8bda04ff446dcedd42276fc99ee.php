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
<div class="login-container animated fadeInDown" style="" id="kcloud_login_frame">
	<div class="layout-login">
		<div class="ll-book-container">
			<div class="ll-book-top">
				<div class="ll-logo-area">
					<img width="120" height="120" src="/cloud/Public/images/logo.png" alt="<?php echo (L("CloudServices")); ?>" />
					<p class="logo-con">
						<span class="m-logo-con"><?php echo (L("CloudServices")); ?></span></p>
					<p class="logo-intro" id="logo-intro"><?php echo (L("HomeCloudSummary")); ?></p>									
				</div>					 
				<div id="logoutTipByLocked" class="timeout-tip tl-c" style="display:none"></div>
				<div class="form-container" style="height:150px" id="form_container">
					<div class="loginButton"><a id="loginLink1" href="<?php echo get_login_url(false);?>" ><?php echo (L("Landimmediately")); ?></a></div>					
					<div class="registerButton"><a id="register" href="<?php echo get_register_url(false);?>" ><?php echo (L("Registeredaccount")); ?></a></div>				
				</div>
			</div>
		</div>
	</div>
	<div class="lang-area">
		<a class="k-learn-more" href="#" target="_blank" id="cloud_learn_more">
			<em class="klm-ico"></em>
			<span><?php echo (L("LearnMoreCloud")); ?></span></a>
	</div>
	<div class="copy-rights">
		<?php if($iso_lang_list_count=count($iso_lang_list)): endif; ?>		
		<?php if(is_array($iso_lang_list)): $k = 0; $__LIST__ = $iso_lang_list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($k % 2 );++$k; if(count($iso_lang_list)): endif; ?>
		    <a target="_self" class="lang-select-li"  <?php if($vo["name"] == $lang_name): ?>id="current"<?php endif; ?>  href="?l=<?php echo ($key); ?>" data-lang="<?php echo ($key); ?>"><?php echo ($vo["name"]); ?></a>
		    <?php if($k != $iso_lang_list_count): ?>|<?php endif; endforeach; endif; else: echo "" ;endif; ?>		
		<br>
		<br>
		<a target="_blank" href="#" id="user_privacy"><?php echo (L("Privacypolicy")); ?></a>|
		<a target="_blank" href="#" id="user_agreement"><?php echo (L("Useragreement")); ?></a>| &nbsp; &nbsp;
		<span id="rights_reserved"><?php echo (L("Copyright")); ?></span></div>
</div>
<div style="display: none;">
<script type="text/javascript">var cnzz_protocol = (("https:" == document.location.protocol) ? " https://" : " http://");document.write(unescape("%3Cspan id='cnzz_stat_icon_1261146263'%3E%3C/span%3E%3Cscript src='" + cnzz_protocol + "s4.cnzz.com/z_stat.php%3Fid%3D1261146263%26show%3Dpic1' type='text/javascript'%3E%3C/script%3E"));</script>
</div>
</body>
</html>