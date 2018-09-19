<?php if (!defined('THINK_PATH')) exit();?><!doctype html>
<html lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta http-equiv="Cache-Control" content="max-age=300"/>
	<meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1,maximum-scale=1,user-scalable=no" />	
	<title>
		<?php if(isset($title) && !empty($title)): echo ($title); ?>
		<?php else: ?>
			<?php echo (L("TipsMessageTitle")); endif; ?>
	</title>
</head>
<body>

<style type="text/css">
	body {
		font-size:75%;
		font-family:Arial,Helvetica,sans-senif,SimSun,"宋体";
		color:#323232; 
		line-height:160%; 
		width:100%; 
		overflow-x:hidden; 
	}	
	#tips{
		width: 780px; 
		margin: 106px auto;
		border:1px solid #c3c3c3;
		border-radius:8px;
		background-color:#fff;
	}	
	#tips a{
		color:#0000ee;
		text-decoration: none;
		cursor:pointer!important;
	}
	#tips a:hover{	
		color:#2b89d9;
		text-decoration:underline; 
		outline:none;
		blr:expression(this.onFocus=this.blur());
	}
	h3{
		font-size:22px;
		font-weight:500;
		text-align: center;
		margin:2px;
		border-bottom: 1px solid rgb(204, 204, 204);
		padding:10px 0px;
		background-color:#fcfcfc;
	}	
	.system-message{		
		width:auto;
		height:260px;
	}
	.tips_img{	
		margin:15px;
		display:inline-block;
		float:left;
	}
	.tips_message{	
		font-size:16px;
		font-weight:400;
		margin-left:15px;
		float:left;
		display:inline-block;
		line-height:24px;
		
	}
	.message_main{		
		margin:15px auto;
		width:700px;
	}
	.error,.success{		
		font-size:20px;
		font-weight:500;
	}
	.jump{
		padding-top:8px;
	}

	@media (max-width:480px){
		#tips{
			width:94%;
			margin: 27% 0;
		}
		.tips_message{
			font-size:14px;
			width:53%;
			margin-top: -128px;
		}
		.error, .success{
			font-size:18px;
		}
		.message_main{
			width:100%;
			padding:118px 0px;
		}
		.tips_img{
			margin-top:-48px;
		}
		.system-message{
			height:100%;
		}
	}
	@media (min-width:768px) and (max-width:1023px){
		#tips{
			width:94%;
			margin: 27% 0;
		}
		.tips_img{
			margin-top:50px;
		}
		.message_main{
			margin:15px 150px;
		}
	}
	@media screen and (orientation : landscape) {
		body{
			margin:0;
		}
		#tips {
				width: 75%;
				margin:35px auto;
		}

	}
	@media (min-width:1024px){
		#tips {
			width: 780px;
			margin:106px auto;
		}
	}
</style>
<div class="content">
	<div class="contents">
		<div id="tips">
			<div class="system-message">
				<h3><?php echo (L("InformationPrompt")); ?></h3>
				<div class="message_main">
				<?php if(isset($message)): ?><div class="tips_img"><img src="/cloud/Public/images/success.jpg" width="94" alt="success pic" /></div>
					<div class="tips_message">
						<p class="success"><?php echo ($message); ?> </p>
				<?php else: ?>
					<div class="tips_img"><img src="/cloud/Public/images/error.jpg" width="95" alt="error pic" /></div>
					<div class="tips_message">
						<p class="error"><?php echo ($error); ?> </p><?php endif; ?>
						<p class="detail"></p>
						<p class="jump">
						<?php echo (L("Page")); ?><a id="href" href="<?php echo ($jumpUrl); ?>"><?php echo (L("Auto")); ?>  </a> , <?php echo (L("Time")); ?>  <b id="wait"><?php echo ($waitSecond); ?></b> <?php echo (L("Sec")); ?>
						</p>
						<p><?php echo (L("Jump")); ?><a href="<?php echo ($jumpUrl); ?>" title="click here"><?php echo (L("Click")); ?> </a> </p>
						<p><?php echo (L("Previous")); ?><a href="javascript:void(0);" onclick="javascript:history.go(-1);" title="Go back to the previous page"><?php echo (L("Click")); ?></a> </p>
					</div>
				</div>
			</div>			
		</div>
		<script type="text/javascript">
		(function(){
		var wait = document.getElementById('wait'),href = document.getElementById('href').href;
		var interval = setInterval(function(){
			var time = --wait.innerHTML;
			if(time <= 0) {
				location.href = href;
				clearInterval(interval);
			};
		}, 1000);
		})();
		</script>
    </div>
</div>
</body>
</html>