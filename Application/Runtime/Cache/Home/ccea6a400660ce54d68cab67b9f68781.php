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
<div class="wrapper">
    <div class="wrap">
        <div class="layout" id="layout">
            <!--表单输入登录-->
            <div class="mainbox" id="login-main">
                <div><a class="ercode" id="qrcode-trigger" href="javascript:void(0)" style="display:none;"></a></div>
                <!-- header s -->
                <div class="lgnheader">
                    <div class="header_tit t_c">
                        <em id="custom_display_1" class="klogo">
                            <img alt="" src="/cloud/Public/images/logo.png" style='width: 61px;'>
                        </em>
                        <h4 class="header_tit_txt" id="login-title"><?php echo (L("Accountlogin")); ?></h4>
                      </div>
                </div>
                <!-- header e -->
                <div>
                    <div class="login_area">
                        <form action="<?php echo get_login_url(false);?>" method="POST" id="login-main-form">
                            <div class="loginbox c_b">
                                <!-- 输入框 -->
                                <div class="lgn_inputbg c_b">
                                    <!--验证用户名-->
                                 
                                    <label id="region-code" class="labelbox login_user c_b" for="">
                                        <input class="item_account"  autocomplete="off"  type="text" name="user_account" value="<?php echo ($user_account); ?>" id="username" placeholder="<?php echo (L("Username")); ?> / <?php echo (L("Email")); ?>" >
                                    </label>
                                    <label class="labelbox pwd_panel c_b">
                                       
                                        <input placeholder="<?php echo (L("Password")); ?>" autocomplete="new-password" value=""  name="password" id="pwd"  type="password">
                                    </label>
                                </div>
                                <?php if($check_verify): ?><div class="lgncode c_b" id="captcha">
	                                	<div class="inputbg inputcode dis_box">
											<label style="width: 225px;" class="labelbox labelbox-captcha" for="">
												<input style="width: 225px;" id="code-captcha" class="code" type="text" name="verify_code" placeholder="<?php echo (L("Picturecode")); ?>">
											</label>
											<img alt="<?php echo (L("Picturecode")); ?>" style="float:right;" title="<?php echo (L("Changepicture")); ?>" src="<?php echo getBaseURL();?>Home/Index/verify/v/login_verify/88592264" class="icode_image code-image chkcode_img" id="verifyimg">
										</div>
	                                </div><?php endif; ?>
                                <!-- 错误信息 -->
                                <div class="err_tip" id="error-outcon" <?php if($error_array["result"] != ""): ?>style='display: block;'<?php endif; ?>>
                                    <div class="dis_box"><em class="icon_error"></em><span class="error-con"><?php echo ($error_array["result"]); ?></span></div>
                                </div>
                                <!-- 登录频繁 -->
                                <div id="error-forbidden" class="err_forbidden"><?php echo (L("OperatingTooFast")); ?></div>
                                <div class="btns_bg">
                                    <input class="btnadpt btn_orange" id="login-button" type="button" value="<?php echo (L("Landimmediately")); ?>">
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <!-- 其他登录方式 e -->
                <div class="n_links_area" id="custom_display_64">
                    <a class="outer-link" href="<?php echo get_register_url(false);?>"><?php echo (L("Registeredaccount")); ?></a><span>|</span>
                    <a class="outer-link" href="<?php echo getBaseURL();?>Home/Index/forgetpassword.html"><?php echo (L("Forgepassword")); ?>？</a>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="n-footer">
  <div class="nf-link-area clearfix">
  <ul class="lang-select-list">    
	    <?php if(is_array($iso_lang_list)): $i = 0; $__LIST__ = $iso_lang_list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><li><a class="lang-select-li <?php if( $vo["name"] == $lang_name ): ?>current<?php endif; ?>" href="?l=<?php echo ($key); ?>" data-lang="<?php echo ($key); ?>"><?php echo ($vo["name"]); ?></a>|</li><?php endforeach; endif; else: echo "" ;endif; ?>
		<li><a class="a_critical" onclick="javascript:void(0);" target="_blank"><em><?php echo (L("FAQ")); ?></em></a></li>    
  </ul>
  </div>
  <p class="nf-intro"><span><?php echo (L("Copyright")); ?><!-- <a class="beianlink beian-record-link" target="_blank" href="http://www.beian.gov.cn/portal/registerSystemInfo?recordcode="><span><img src="/cloud/Public/Images/ghs.png"></span></a> --></span></p>
</div>

<div style="display: none;">
<script type="text/javascript">var cnzz_protocol = (("https:" == document.location.protocol) ? " https://" : " http://");document.write(unescape("%3Cspan id='cnzz_stat_icon_1261146263'%3E%3C/span%3E%3Cscript src='" + cnzz_protocol + "s4.cnzz.com/z_stat.php%3Fid%3D1261146263%26show%3Dpic1' type='text/javascript'%3E%3C/script%3E"));</script>
</div>
</body>
</html>