<?php if (!defined('THINK_PATH')) exit();?><style>
.thead {
	text-align: center;
	background: #abcdef;
}

.total {
	background: green;
}

.width {
	width: 15%
}

.t_border {
	cellspacing: 0px, cellpadding:0px, border-collapse:collapse
}
</style>

<div id='div_condition'>
<form action=""  id="fm_condition">
报表类型：
<select name="report_type" id="report_type">
<option value="1">经营单位任务表</option>
<option value="2">对公客户业务量表</option>
<option value="3">全行业务量表</option>
 </select>
起始日：<input type="text"  name='start'   id='start'   value='20150101' />
结束日：<input type="text"  name='end'  id ='end' />
&nbsp;【日期格式】：20150101
<input type="button"  id='btn_submit' value='查询'/>
<input type="button"  id='btn_export' value='导出EXCEL'/>
</form>
</div>

<div id='div_report'>
	
</div>

	</br>

	<script>
	
	$(function(){
		


		$("#table_settlement").css({
			"width" : "100%",
			"border" : "0px solid gray",
			"cellspacing" : "0",
			"cellpadding" : "0",
			'border-collapse' : 'collapse'
		});
		$("#table_settlement td").css({
			"border" : "1px solid gray",
			"cellspacing" : "0",
			"cellpadding" : "0",
			'border-collapse' : 'collapse',
			'height' : '15px',
			'font-size' : '14px'
		});

		$("#table_settlement td").bind('mousemove', function(evgl) {
			var evg = evgl.srcElement ? evgl.srcElement : evgl.target;
			evg.parentNode.style.background = "#cbadef";
		});
		$("#table_settlement td").bind('mouseout', function(evgl) {
			var evg = evgl.srcElement ? evgl.srcElement : evgl.target;
			evg.parentNode.style.background = "white";
		});
		
		//当查询按钮按下的事件
		$("#btn_submit").bind('click',function(){
			
			//结束日期为空时，以本日日期填充
			if($("#end").val()==''){
				var date=new Date();
				var year=date.getFullYear();
				var month=date.getMonth();
				if(month<9){
					month=month+1;
					month="0"+month;
				}else{
					month=month+1;
				}
				
				var day=date.getDate();
				if(day<10){	
					day="0"+day;
				}else{
					//day=day+1;
				}
				$("#end").val(year+month+day);
			}
			
			var htm="<div width='100%' align='center'></br></br></br>正在查询，请稍候。。。<img align='center'  src='/jrrc_web/public/res/progress.gif'  /></div>";
			$("#div_report").html(htm);
			var url='';
			if($("#report_type").val()==1){
				url='/Jrrc_web/home/report/report';
			}
			if($("#report_type").val()==2){
				url='/Jrrc_web/home/report/mkclientywl';
			}
			if($("#report_type").val()==3){
				url='/Jrrc_web/home/report/mkallywl';
			}
			
			var data=$("#fm_condition").serialize();
			$.post(url,data,function(msg){
				$("#div_report").html(msg);
			});
		});
		
		//当导出EXCEL按钮按下的事件
		$("#btn_export").bind('click',function(){
			
			var url='';
			var url1='';
			if($("#report_type").val()==1){
				url='/Jrrc_web/home/report/unit_ywl_Excel';
				url1='/Jrrc_web/home/report/report';
			}
			if($("#report_type").val()==2){
				url='/Jrrc_web/home/report/client_ywl_Excel';
				url1='/Jrrc_web/home/report/mkclientywl';
			}
			if($("#report_type").val()==3){
				return;
			}
			
			var htm="<div width='100%' align='center'></br></br></br></br></br></br>正在查询，请稍候。。。<img align='center'  src='/jrrc_web/public/res/progress.gif'  /></div>";
			$("#div_report").html(htm);
			var data=$("#fm_condition").serialize();
			$.post(url1,data,function(msg){
				$("#div_report").html(msg);
				$.post(url,data,function(msg){
					if(msg!=''){
						var str="/jrrc_web/public/report/excel/"+msg;
						  window.location.href = str;
					}
				});
				
			});
			
		});
		
	});
	</script>