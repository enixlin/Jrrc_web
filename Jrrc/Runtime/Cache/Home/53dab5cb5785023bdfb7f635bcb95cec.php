<?php if (!defined('THINK_PATH')) exit();?><!doctype html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<title>欢迎使用国际业务信息查询</title>

  <link rel="stylesheet"
	href="/Jrrc_web/Public/jquery/jquery_ui/jquery_ui.css" />
<script src='/Jrrc_web/Public/jquery/jquery.js'></script>
<script src='/Jrrc_web/Public/jquery/jquery_ui/jquery_ui.min.js'></script>    

 


</head>
<body onkeydown="doKey();">
	<div id='formdiv' class='text_center border'>
		<form action="" method='post'>
			<span><img class='logosiza_big' id='bg'
				src="/Jrrc_web/Public/res/Logo_orange.png" alt="" /></span> <br /> <br />
			<span>用户：<select id='s_name' name='name'
				class='textbox_length_middle text_center'></select></span> <br /> <br />
			<span>密码：<input type="password" name='password'
				id='tb_password' class="textbox_length_middle text_center" /></span> <br />
			<br /> <span>
			<input
				class='buttonsize_middel  fontsize_midel' type="button" value='修改密码'
				id="btn_changepw" />
				
				<input class='buttonsize_middel  fontsize_midel' type="button" value='用户登录'
				id="btn_submit" />
				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 
				<input class='buttonsize_middel  fontsize_midel' type="button" value='查询登录'
				id="btn_unit" />
				</span>
		</form>
	</div>
</body>
</html>

<script>
	$(function() {
		$("#formdiv").css({"width":"420px","border":"3px double orange","textAlign":"center","margin":"10px auto auto auto","padding":"15px"});
		$("#bg").css({"width":"400px"});
		//$("#s_name").button();
	$("#tb_password").button();
	
	
	
		
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
		$("#btn_unit").button();
		$("#btn_guest").button();
		$("#btn_changepw").button();
		//$("#s_name").spinner();
	});
	
	
	function doKey() {	
		if(event.keyCode == 13){
			var postdata = $("form").serialize();
			$.post("/Jrrc_web/Home/Login/login/", postdata, function(message) {
				if (message == 'false') {
					alert("登录失败，请检查【户名】和【密码】！");
				}
				if (message == 'true') {
					//登录成功，页面跳转到主页
					window.location.href = "/Jrrc_web/Home/Main/Index";

				}
			});
		}
	}


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
				if (message == 'false') {
					alert("登录失败，请检查【户名】和【密码】！");
				}
				if (message == 'true') {
					//登录成功，页面跳转到主页
					window.location.href = "/Jrrc_web/Home/Main/Index";

				}
			});
		});
		
		$("#btn_changepw").bind('click', function() {
			window.location.href='/Jrrc_web/Home/Login/changepw';
		});
	

		$("#btn_unit").bind('click', function() {
			
			$.post("/Jrrc_web/Home/Login/login/", {"name":"guest","password":"1"}, function(message) {
				if (message == 'false') {
					alert("登录失败，请检查【户名】和【密码】！");
				}
				if (message == 'true') {
					//登录成功，页面跳转到主页
					window.location.href = "/Jrrc_web/Home/Main/Index";

				}
			});
		});

	});
</script>