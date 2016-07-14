<?php if (!defined('THINK_PATH')) exit();?><style>

.t_border {
	cellspacing: 0px, cellpadding:0px, border-collapse:collapse
}

.thead {
	text-align: center;
	background: #abcdef;
}

.client{
   cursor:pointer
}

</style>
<!--  
<br/>
<div style="background:#fedcba">
&nbsp;&nbsp;&nbsp;
	<select >
			<option>支行汇总排序</option>	
			<option>结算量排序</option>	
			<option>结算量增幅排序</option>	
			<option>结售汇量排序</option>	
			<option>结售汇增幅排序</option>	
			<option>贸易融资量排序</option>	
			<option>贸易融资量增幅排序</option>	
	
	</select>

</div>
<br/>

-->
<table id='tb_client_report' class='t_border'   border=1>
	<tr>
		<td  class='thead'>一级行号</td>
		<td  class='thead'>二级行号</td>
		<td  class='thead'>行名</td>
		<td  class='thead'>客户名称</td>
		<td  class='thead' width=30>结算笔数</td>
		<td  class='thead'>结算金额(美元)</td>
		<td  class='thead' width=30>去年同期</td>
		<td  class='thead'>去年同期</td>
		<td  class='thead'>同比</td>
		<td  class='thead' width=30 >结售汇<br/>笔数</td>
		<td  class='thead'>结售汇金额(美元)</td>
		<td  class='thead' width=30>去年同期</td>
		<td  class='thead'>去年同期</td>
		<td  class='thead'>同比</td>
		<td  class='thead' width=30>融资笔数</td>
		<td  class='thead'>融资金额(美元)</td>
		<td  class='thead' width=30>去年同期</td>
		<td  class='thead'>去年同期</td>
		<td  class='thead'>同比</td>
	</tr>
	<?php if(is_array($client_yw)): $i = 0; $__LIST__ = $client_yw;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><tr>
		<td align="center"><?php echo ($vo["upbranch"]); ?></td>
		<td align="center"><?php echo ($vo["branch"]); ?></td>
		<td align="center" ><?php echo (str_replace('本部','',$vo["br_name"])); ?></td>
		<td align="center" id='<?php echo ($vo["custno"]); ?>' class='client'  name='<?php echo ($vo["name"]); ?>' ><?php echo ($vo["name"]); ?></td>
		<td align="center"><?php echo ($vo["js_times"]); ?></td>
		<td align="right"><?php echo (number_format($vo["js_jiner"],2)); ?></td>
		<td align="center"><?php echo ($vo["js_times_compare"]); ?></td>
		<td align="right"><?php echo (number_format($vo["js_jiner_compare"],2)); ?></td>
		<?php if(($vo["js_jiner_tongbi"] < 0)): ?><td align="right" style="color:red"><?php echo (number_format($vo["js_jiner_tongbi"],2)); ?></td>
		<?php else: ?> <td align="right" ><?php echo (number_format($vo["js_jiner_tongbi"],2)); ?></td><?php endif; ?>
		<td align="center"><?php echo ($vo["jsh_times"]); ?></td>
		<td align="right"><?php echo (number_format($vo["jsh_jiner"],2)); ?></td>
		<td align="center"><?php echo ($vo["jsh_times_compare"]); ?></td>
		<td align="right"><?php echo (number_format($vo["jsh_jiner_compare"],2)); ?></td>
		<?php if(($vo["jsh_jiner_tongbi"] < 0)): ?><td align="right" style="color:red"><?php echo (number_format($vo["jsh_jiner_tongbi"],2)); ?></td>
		<?php else: ?> <td align="right" ><?php echo (number_format($vo["jsh_jiner_tongbi"],2)); ?></td><?php endif; ?>
		
		
		<td align="center"><?php echo ($vo["tf_times"]); ?></td>
		<td align="right"><?php echo (number_format($vo["tf_jiner"],2)); ?></td>
		<td align="center"><?php echo ($vo["tf_times_compare"]); ?></td>
		<td align="right"><?php echo (number_format($vo["tf_jiner_compare"],2)); ?></td>
		<?php if(($vo["tf_jiner_tongbi"] < 0)): ?><td align="right" style="color:red"><?php echo (number_format($vo["tf_jiner_tongbi"],2)); ?></td>
		<?php else: ?> <td align="right" ><?php echo (number_format($vo["tf_jiner_tongbi"],2)); ?></td><?php endif; ?>
	</tr><?php endforeach; endif; else: echo "" ;endif; ?>
<tr>
<td colspan=4 align="center">合计</td>
<td align="center"><?php echo ($total["js_times_total"]); ?></td>
<td align="right"><?php echo (number_format($total["js_jiner_total"],2)); ?></td>
<td></td>
<td></td>
<td></td>
<td align="center"><?php echo ($total["jsh_times_total"]); ?></td>
<td align="right"><?php echo (number_format($total["jsh_jiner_total"],2)); ?></td>
<td></td>
<td></td>
<td></td>
<td align="center"><?php echo ($total["tf_times_total"]); ?></td>
<td align="right"><?php echo (number_format($total["tf_jiner_total"],2)); ?></td>
<td></td>
<td></td>
<td></td>
</tr>
</table>



<script>
$(function(){
	
	$("#tb_client_report td").bind('click', function(evgl) {
		var evg = evgl.srcElement ? evgl.srcElement : evgl.target;
		var c_id=evg.id;
		var url='/jrrc_web/home/report2016/show_Client_Report_Month';
		if(c_id!=''){	
			var start=$("#start").val();
			var end = $("#end").val();
			//var name=document.getElementById(c_id);
			//alert(evg.name);
			
			url=url+"/client_id/"+c_id;
			url=url+"/start/"+start;
			url=url+"/end/"+end;
			url=url+"/type/01";
			window.open(url,evg.id,"width=1200px,height=800px,top=50,left=60");
			//$.post(url,{"c_id":c_id,"type":'01',"start":start,"end":end,},function(msg){
			//	$("#div_report").html(msg);
			//},"html");
		}

	});
	
	
$("#tb_client_report").css({
	"width" : "100%",
	"border" : "0px solid gray",
	"cellspacing" : "0",
	"cellpadding" : "0",
	'border-collapse' : 'collapse'
});
$("#tb_client_report td").css({
	"border" : "1px solid gray",
	"cellspacing" : "0",
	"cellpadding" : "0",
	'border-collapse' : 'collapse',
	'height' : '15px',
	'font-size' : '12px'
});


$("#tb_client_report td").bind('mousemove', function(evgl) {
	var evg = evgl.srcElement ? evgl.srcElement : evgl.target;
	evg.parentNode.style.background = "#cbadef";
});
$("#tb_client_report td").bind('mouseout', function(evgl) {
	var evg = evgl.srcElement ? evgl.srcElement : evgl.target;
	evg.parentNode.style.background = "white";
});

});

</script>