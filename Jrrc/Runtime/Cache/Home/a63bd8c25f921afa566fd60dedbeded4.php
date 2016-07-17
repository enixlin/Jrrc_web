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




<table id='tb_ywls'   border=1>
	<tr>
		<td  class='thead'>业务编号</td>
		<td  class='thead'>客户编号</td>
		<td  class='thead'>客户名称</td>
		<td  class='thead'>业务类型</td>
		<td  class='thead'>业务日期</td>
		<td  class='thead'>币种</td>
		<td  class='thead'>金额</td>
		

	</tr>
	<?php if(is_array($ywls)): $i = 0; $__LIST__ = $ywls;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><tr>
		<td align='center'><?php echo ($vo["ywbh"]); ?></td>
		<td align='center'><?php echo ($vo["custno"]); ?></td>
		<td align='center'><?php echo ($vo["name"]); ?></td>
		<td align='center'><?php echo ($vo["yw_type"]); ?></td>
		<td align='center'><?php echo ($vo["yw_date"]); ?></td>
		<td align='center'><?php echo ($vo["cny"]); ?></td>
		<td align='center'><?php echo ($vo["txamt"]); ?></td>
	</tr><?php endforeach; endif; else: echo "" ;endif; ?>
	

</table>



<script>

$("#tb_ywls").css({
	"width" : "100%",
	"border" : "0px solid gray",
	"cellspacing" : "0",
	"cellpadding" : "0",
	'border-collapse' : 'collapse'
});
$("#tb_ywls td").css({
	"border" : "1px solid gray",
	"cellspacing" : "0",
	"cellpadding" : "0",
	'border-collapse' : 'collapse',
	'height' : '15px',
	'font-size' : '12px'
});

$("#tb_ywls td").bind('mousemove', function(evgl) {
	var evg = evgl.srcElement ? evgl.srcElement : evgl.target;
	evg.parentNode.style.background = "#cbadef";
});
$("#tb_ywls td").bind('mouseout', function(evgl) {
	var evg = evgl.srcElement ? evgl.srcElement : evgl.target;
	evg.parentNode.style.background = "white";
});



</script>