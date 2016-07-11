<?php if (!defined('THINK_PATH')) exit();?><form action="">
	<table width='100%' id='phone_condition' cellpadding='0'
		cellspacing='0'>
		<tr>
			<td colspan='4' align='center' width='100%'
				style="background-image: url('/Jrrc_web/Public/res/tab_bg.gif'); height: 25px; color: orange">
				<strong> 通信电话查询 </strong>
			</td>
		</tr>

		<tr>
			<td align="right">单位</td>
			<td><select name="company" id='department'>
					<option value="">&nbsp;</option>
					<?php if(is_array($departments)): $i = 0; $__LIST__ = $departments;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><option  value=<?php echo ($vo["department"]); ?>><?php echo ($vo["department"]); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
			</select></td>

			<td align="right">部门</td>
			<td><select name="department"  id='sub_department'>
				
			</select></td>
		</tr>
		<tr>
			<td align="right">职务</td>
			<td><select name="position" id="position">
					<option value="">&nbsp;</option>
					<?php if(is_array($positions)): $i = 0; $__LIST__ = $positions;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><option value=<?php echo ($vo["position"]); ?>><?php echo ($vo["position"]); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
			</select></td>

			<td align="right">姓名</td>
			<td><input type="text" name='name' id='name'/></td>
		</tr>
		<tr>

			<td colspan='4' align="right" height="35px">
			<input type="reset"  value='清除内容' id='btn_reset'/>
			<input type="button"
				id='submit' value="查询" width='100px' /></td>
		</tr>
	</table>
</form>


<div id="div_userlist" align="center">
</br></br>
<img  src="/jrrc_web/public/res/bg/phonebook1.png" alt="" />
<img  src="/jrrc_web/public/res/bg/phonebook2.png" alt="" />
<img  src="/jrrc_web/public/res/bg/phonebook3.png" alt="" />

</div>
<div id="div_modify" type='hidden'>

</div>

<script>
$(function(){
	$("#phone_condition ").css({
		"border" : "1px solid gray",
		"CellSpacing" : "0",
		"CellPadding" : "0"
	});
	$("#phone_condition td").css({
		"border" : "1px solid white",
		"padding":"2px"
	});


	$("#submit").button();
	$("#btn_reset").button();
	$("#department").bind('change',function(){
		getSub_department($("#department").text());
		submit();
	});
	
	$("#sub_department").bind('change',function(){
		submit();
	});
	
	$("#position").bind('change',function(){
		submit();
	});

	$("#name").bind('input propertychange',function(){
		submit();
	});
	
	//关闭对话框
	function d_close(){
		$("#modifyform").html('');
		$("#div_modify").dialog('close');
	}
	
	//获取部门机构数据
	function getSub_department(){
		var department=$("#department").val();
		$("#sub_department").empty();
		$.post('/Jrrc_web/Home/PhoneBook/getsubdepartment',{"department":department},function(msg){
			var json=eval(msg);
			var html="<option></option>";
			for(var n=0;n<json.length;n++){
				html=html+"<option value="+json[n].sub_department+">"+json[n].sub_department+"</option>";
			}
			$("#sub_department").append(html);
		});
	}
	
	//查询电话记录
	function submit(){
		var Condition = $("form").serialize();
		if($("#department").val()=="" && $("#name").val()=="" && $("#position").val()==""  && $("#name").val()==''){
			return;
		}

		$.post('/Jrrc_web/Home/PhoneBook/search', Condition, function(msg) {
			//设置电话列表大小
			$("#div_userlist").css({
				"width":"100%",
				"height":"350px",
				"overflow":"auto",	
				"padding":"1px",
			});
			//将返来的数据生成一个list对象
			var list = eval(msg);
			//构建表格html
			//var table="";
			var table="<table id='userlist' border='1px solid gray' width=100%>";
			table=table+"<tr> <td colspan='9' width=100% style='background-image:url(/Jrrc_web/Public/res/tab.gif) ' align=center> 用户通信录</td></tr>"
			table=table+"<tr>";
			table=table+"<td align=center width='150px'>公司/单位</td>";
			table=table+"<td align=center>部门</td>";
			table=table+"<td align=center width='100px'>姓名</td>";
			table=table+"<td align=center>职务</td>";
			table=table+"<td align=center width='150px'>手机号码</td>";
			table=table+"<td align=center width='150px'>座机</td>";
			table=table+"<td align=center>电邮</td>";
			table=table+"<td align=center>地址</td>";
			table=table+"<td align=center>操作</td>";
			table=table+"</tr>";
			
			var tr="";
			//如果返回为空就只输出表头
			if(msg=="null"){
				table=table+"</table>";
				$("#div_userlist").html("");
				$("#div_userlist").append(table);
			}else{
			//输出表格内容
			for(var n=0;n<list.length;n++){
				tr=tr+"<tr id="+list[n].id+">";
				tr=tr+"<td align=center>"+list[n].department+"</td>";
				tr=tr+"<td align=center>"+list[n].sub_department+"</td>";
				tr=tr+"<td align=center>"+list[n].name+"</td>";
				tr=tr+"<td align=center>"+list[n].position+"</td>";
				tr=tr+"<td align=center>"+list[n].cell_phone+"</td>";
				tr=tr+"<td align=center>"+list[n].office_phone+"</td>";
				tr=tr+"<td align=center>"+list[n].email+"</td>";
				tr=tr+"<td align=center>"+list[n].address+"</td>";
				tr=tr+"<td align=center><input type=button value=修改 id=modify"+list[n].id+"/></td>";
				tr=tr+"</tr>";
			}
			
			table=table+tr+"</table>";
			//清空原来的表格内容
			$("#div_userlist").html("");
			//将新表格追加到DIV中
			$("#div_userlist").append(table);
			//当鼠标停在行上高亮
			$("#userlist input").bind('mousemove',function(evgl){
				var evg=evgl.srcElement?evgl.sElement:evgl.target; 
				evg.parentNode.parentNode.style.background="#cbadef";
			});
			//当鼠标离开就变来颜色
			$("#userlist input").bind('mouseout',function(evgl){
				var evg=evgl.srcElement?evgl.srcElement:evgl.target; 
				evg.parentNode.parentNode.style.background="white";
			});
			$("#userlist td").bind('mousemove',function(evgl){
				var evg=evgl.srcElement?evgl.srcElement:evgl.target; 
				evg.parentNode.style.background="#cbadef";
			});
			$("#userlist td").bind('mouseout',function(evgl){
				var evg=evgl.srcElement?evgl.srcElement:evgl.target; 
				evg.parentNode.style.background="white";
			});
			
			$("#userlist input").button();
			//修改按键的单击事件处理
			$("#userlist input").bind('click',function(evgl){
				//定义单击事件的事件源，定位表格行的ID
				var evg=evgl.srcElement?evgl.srcElement:evgl.target; 
				var data=evg.parentNode.parentNode.id;
				//后台发送请求，以ID查询电话记录
				$.post('/Jrrc_web/Home/PhoneBook/search',{'id':data},function(msg){
					//返回数据生成表格对象
					var json=eval(msg);
					//构建表格HTML
					var htmlstring="<form action='#' method='post' id='modifyform'>";
					 htmlstring=htmlstring+"id : "+json[0].id+"<input name='id' type=text value="+json[0].id+" style=display:none /></br>";
					 htmlstring=htmlstring+"姓名 <input name='name' type=text value="+json[0].name+" /></br>";
					 htmlstring=htmlstring+"单位 <input name='department' type=text value="+json[0].department+" /></br>";
					 htmlstring=htmlstring+"部门 <input name='sub_department' type=text value="+json[0].sub_department+" /></br>";
					 htmlstring=htmlstring+"职务 <input name='position' type=text value="+json[0].position+" /></br>";
					 htmlstring=htmlstring+"手机 <input name='cell_phone' type=text value="+json[0].cell_phone+" /></br>";
					 htmlstring=htmlstring+"座机 <input name='office_phone' type=text value="+json[0].office_phone+" /></br>";
					 htmlstring=htmlstring+"电邮 <input  name='email' type=text value="+json[0].email+" /></br>";
					 htmlstring=htmlstring+"地址 <input  name='address' type=text value="+json[0].address+" />";
					 htmlstring=htmlstring+"<input  type='button' value='保存修改' id='btn_save_modify'/></form>";
					//填充表格
					$("#div_modify").html(htmlstring);
					
					//关闭修改对话框的同时清空数据
					$("#div_modify").on('dialogclose',function(){
						$("#modifyform").html('');
					});
					
					
					//保存修改
					$("#btn_save_modify").bind('click',function(){
						var condition =$("#modifyform").serialize();
						$.post('/Jrrc_web/Home/PhoneBook/update',condition,function(msg){
							if('1'==msg){
								//返回成功，
								alert("保存成功");
								condition=null;
								d_close();
								submit();
							}else{
								alert('修改失败');
							}
						});
					});
					//显示修改记录的对话框
					$("#div_modify").dialog({'title':"修改记录",modal:true});
				});
			});
			
		
			$("#userlist").css({
				"border" : "0px solid gray",
					"cellSpacing" : "0",
					"cellPadding" : "0"	,
					"border-collapse": "collapse"
			});
			$("#userlist td").css({
				"border" : "1px solid gray",
					"CellSpacing" : "0",
					"Padding" : "0"
			});
			}
			
			
		});
		$Condition=null;
		
	}

	$("#submit").bind('click', function() {
		if($("#department").val()!="" || $("#name").val()!="" || $("#position").val()!="" ){
			submit();
		}else{
			alert("没有设定查询条件");
		}
	});

});
</script>