<?php if (!defined('THINK_PATH')) exit();?><!doctype html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<title>欢迎使用国际业务办公信息网</title>
<link rel="stylesheet" href="/Jrrc_web/Public/my.css" />
<link rel="stylesheet"
	href="/Jrrc_web/Public/jquery/jquery_ui/jquery-ui.css" />
<script src='/Jrrc_web/Public/jquery/jquery.js'></script>
<script src='/Jrrc_web/Public/jquery/jquery_ui/jquery_ui.min.js'></script>
</head>
<body>
	<div id='formdiv' class='text_center border'>
		<form action="" method='post'>
			<span><img class='logosiza_big'
				src="/Jrrc_web/Public/res/Logo_orange.png" alt="" /></span> <br /> <br />
			<span>用户：<select id='s_name' name='name'
				class='textbox_length_middle text_center'></select></span> <br /> <br />
			<span>密码：<input type="password" name='password'
				id='tb_password' class="textbox_length_middle text_center" /></span> <br />
			<br /> <span><input class='buttonsize_middel  fontsize_midel'
				type="button" value="游客通道" id='btn_guest' />
				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <input
				class='buttonsize_middel  fontsize_midel' type="button" value='用户登录'
				id="btn_submit" /></span>
		</form>
	</div>
</body>
</html>

<script>
	$(function() {
		$("#btn_reset").button({
			icons : {
				primary : "ui-icon-gear",
				secondary : "ui-icon-triangle-1-s"
			}
		});
		$("#btn_reset").button("option", "icons", {
			primary : "ui-icon-gear",
			secondary : "ui-icon-triangle-1-s"
		});
		$("#btn_submit").button();
		$("#btn_guest").button();
		//$("#s_name").spinner();
	});

	$(function() {
		$.post('/Jrrc_web/Home/Login/getAllUser/', function(message) {
			var oJson = eval("(" + message + ")");
			var Str = "<option>请选择你的用户名</option>";
			for (var n = 0; n < oJson.length; n++) {
				Str = Str + "<option class='text_center'>" + oJson[n].name
						+ "</option>";
			}
			$("#s_name").html(Str);
		});
	});

	$(function() {
		$("#btn_submit").bind('click', function() {
			var postdata = $("form").serialize();
			$.post("/Jrrc_web/Home/Login/login/", postdata, function(message) {
				if (message == 'False') {
					alert("登录失败，请检查【户名】和【密码】！");
				}
				if (message == 'True') {
					//登录成功，页面跳转到主页
					window.location.href = "/Jrrc_web/Home/main";

				}
			});
		});
		
		$("#btn_guest").bind('click', function() {
			window.location.href = "/Jrrc_web/Home/Login/guestLogin";
		});

	});
</script>