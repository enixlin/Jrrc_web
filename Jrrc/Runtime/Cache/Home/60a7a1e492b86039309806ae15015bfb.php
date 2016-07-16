<?php if (!defined('THINK_PATH')) exit();?><style>

.br{
   cursor:pointer
}
.thead {
	text-align: center;
	background: #abcdef;
}

.total {
	background: #FFCB99
}

.sub_total {
	background: lightgreen
}
.width_brid {
	width: 50px
}

.width_brname {
	width: 120px
}

.font-aril {
	font-family: arial
}

.t_border {
	cellspacing: 0px, cellpadding:0px, border-collapse:collapse
}
</style>

<table border=1 width="100%" id='table_settlement'>

	<tr>

		<td class='thead ,width' colspan=1>行号</td>
		<td class='thead ,width' colspan=1>行名</td>
		<td class='thead ,width' colspan=1>客户名称</td>
		<td class='thead ,width' colspan=1>首笔结算业务日期</td>
	</tr>

	
	
<?php if(is_array($result)): $n = 0; $__LIST__ = $result;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($n % 2 );++$n;?><tr>

		<td align="center"><?php echo ($vo["upbranch"]); ?></td>
		<td align="center"><?php echo ($vo["br_name"]); ?></td>
		<td align="center"><?php echo ($vo["name"]); ?></td>
		<td align="center"><?php echo ($vo["yw_date"]); ?></td>
	</tr><?php endforeach; endif; else: echo "" ;endif; ?>
	


</table>

<script>




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
		'font-size' : '12px'
	});

	$("#table_settlement td").bind('mousemove', function(evgl) {
		var evg = evgl.srcElement ? evgl.srcElement : evgl.target;
		evg.parentNode.style.background = "#cbadef";
	});
	$("#table_settlement td").bind('mouseout', function(evgl) {
		var evg = evgl.srcElement ? evgl.srcElement : evgl.target;
		evg.parentNode.style.background = "white";
	});
</script>