<?php if (!defined('THINK_PATH')) exit();?><link rel="stylesheet" href="/Jrrc_web/Public/jquery/jquery_ui/jquery_ui.css" />
<script src='/Jrrc_web/Public/jquery/jquery.js'></script>
<script src='/Jrrc_web/Public/jquery/jquery_ui/jquery_ui.min.js'></script>

<style>

.t_border {
	cellspacing: 0px, cellpadding:0px, border-collapse:collapse
}

.thead {
	text-align: center;
	background: #abcdef;
}

</style>
&nbsp;
<img src='/jrrc_web/home/Report1/Unit_Barplot/unit/<?php echo ($br_id); ?>/start/<?php echo ($start); ?>/end/<?php echo ($end); ?>/type/<?php echo ($type); ?>'  alt=""  id='chart_month_list'/>
&nbsp;
<img src='/jrrc_web/home/Report1/Unit_Type_Pie/unit/<?php echo ($br_id); ?>/start/<?php echo ($start); ?>/end/<?php echo ($end); ?>/type/<?php echo ($type); ?>'  alt=""  id='chart_month_list'/>

<div id='div_client'  style='float:left;margin:10px;width:600px'>

<table id='tb_client_report' class='t_border'   border=1>
	<tr>

		<td  class='thead'>月份</td>
		<td  class='thead'>结算笔数</td>
		<td  class='thead'>结算金额(美元)</td>

	</tr>
	<?php if(is_array($result)): $i = 0; $__LIST__ = $result;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><tr>

		<td align="center"><?php echo ($vo["month"]); ?></td>
		<td align="center"><?php echo ($vo["times"]); ?></td>
		<td align="right"><?php echo (number_format($vo["jiner"],2)); ?></td>

	</tr><?php endforeach; endif; else: echo "" ;endif; ?>

</table>
</div>
<div id='div_type_report'  style='float:left;margin:10px'>
<table id='tb_type_report' class='t_border'   border=1>
	<tr>
	
		<td  class='thead'>业务种类</td>
		<td  class='thead'>结算笔数</td>
		<td  class='thead'>结算金额(美元)</td>

	</tr>
	<?php if(is_array($TypeResult)): $i = 0; $__LIST__ = $TypeResult;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><tr>
	
		<td align="center"><?php echo ($vo["yw_name"]); ?></td>
		<td align="center"><?php echo ($vo["timers"]); ?></td>
		<td align="right"><?php echo (number_format($vo["jiner"],2)); ?></td>

	</tr><?php endforeach; endif; else: echo "" ;endif; ?>

</table>
</div>

<script>
$(function(){
	
$("#tb_type_report").css({
	"width" : "550px",
	"border" : "0px solid gray",
	"cellspacing" : "0",
	"cellpadding" : "0",
	'border-collapse' : 'collapse'
});
$("#tb_type_report td").css({
	"border" : "1px solid gray",
	"cellspacing" : "0",
	"cellpadding" : "0",
	'border-collapse' : 'collapse',
	'height' : '15px',
	'font-size' : '12px'
});


$("#tb_type_report td").bind('mousemove', function(evgl) {
	var evg = evgl.srcElement ? evgl.srcElement : evgl.target;
	evg.parentNode.style.background = "#cbadef";
});
$("#tb_type_report td").bind('mouseout', function(evgl) {
	var evg = evgl.srcElement ? evgl.srcElement : evgl.target;
	evg.parentNode.style.background = "white";
});


$("#tb_client_report").css({
	"width" : "500px",
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