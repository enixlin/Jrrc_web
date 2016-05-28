<?php if (!defined('THINK_PATH')) exit();?>
<!--添加用户  -->
<div align='left'>
<table cols='3'>
<tr >
<td colspan="1">最近一天：</td>
<td colspan="1"><?php echo ($LastDay); ?></td>
<td>&nbsp;</td>
</tr>
<tr >
<td colspan="1">国际结算业务量：</td>
<td colspan="1" align="right"><?php echo (number_format($js_amount,2)); ?>美元</td>
<td>&nbsp;&nbsp;共<?php echo ($js_count); ?>笔。</td>
</tr>
<tr >
<td colspan="1">贸易融资业务量：</td>
<td colspan="1"align="right"><?php echo (number_format($tf_amount,2)); ?>美元</td>
<td colspan="1">&nbsp;&nbsp;共<?php echo ($tf_count); ?>笔。</td>
</tr>


<tr >
<td colspan="1">贸易融资余额：</td>
<td colspan="1"align="right"><?php echo (number_format($tf_balance,2)); ?>美元</td>
</tr>


</table>

</div>
<br/>
<!--业务流水  -->
<div id="div_js_list" align="center" >

<table id="table_tf_remind_list" class='table'>
		<tr style="color: blue; align: center; width: 100%">
			<td class="align_center" colspan='8'>未来15天贸易融资业务到期提醒表</td>
		</tr>

		<tr>
			<td class='tableHeader align_center'>借据编号</td>
			<td class='tableHeader align_center'>客户名称</td>
			<td class='tableHeader align_center'>币种</td>
			<td class='tableHeader align_center'>融资金额</td>
			<td class='tableHeader align_center'>利率(%)</td>
			<td class='tableHeader align_center'>借款日</td>
			<td class='tableHeader align_center'>到期日</td>
			<td class='tableHeader align_center'>本金余额</td>
					
		</tr>

		<?php if(is_array($tf_next15day_detail)): $i = 0; $__LIST__ = $tf_next15day_detail;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><tr>
			<td class='tableTd align_center'><?php echo ($vo["contract_number"]); ?></td>
			<td class='tableTd align_center'><?php echo ($vo["client_name"]); ?></td>
			<td class='tableTd align_center'><?php echo ($vo["currency"]); ?></td>
			<td class='tableTd align_center'><?php echo (number_format($vo["tf_amount"],2)); ?></td>
			<td class='tableTd align_center'><?php echo ($vo["rate"]); ?></td>
			<td class='tableTd align_center'><?php echo ($vo["v_date"]); ?></td>
			<td class='tableTd align_center'><?php echo ($vo["r_date"]); ?></td>
			<td class='tableTd align_center'><?php echo (number_format($vo["remain_amount"],2)); ?></td>

			
		</tr><?php endforeach; endif; else: echo "" ;endif; ?>

	
	</table>




<br/>


<table id="table_tf_balance_list" class='table'>
		<tr style="color: blue; align: center; width: 100%">
			<td class="align_center" colspan='6'>贸易融资余额分户明细表</td>
		</tr>

		<tr>
			<td class='tableHeader align_center'>客户编号</td>
			<td class='tableHeader align_center'>客户名称</td>
			<td class='tableHeader align_center'>折美元金额</td>
			
		</tr>

		<?php if(is_array($tf_client_balance)): $i = 0; $__LIST__ = $tf_client_balance;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><tr>
			<td class='tableTd align_center'><?php echo ($vo["client_number"]); ?></td>
			<td class='tableTd align_center'><?php echo ($vo["client_name"]); ?></td>
			<td class='tableTd align_right'><?php echo (number_format($vo["sum"],2)); ?></td>
			
		</tr><?php endforeach; endif; else: echo "" ;endif; ?>

	
	</table>


<br/>



	<table id="table_user_list" class='table'>
		<tr style="color: blue; align: center; width: 100%">
			<td class="align_center" colspan='6'><?php echo ($LastDay); ?>业务流水列表</td>
		</tr>

		<tr>
			<td class='tableHeader align_center'>业务编号</td>
			<td class='tableHeader align_center'>客户名称</td>
			<td class='tableHeader align_center'>业务类型</td>
			<td class='tableHeader align_center'>币种</td>
			<td class='tableHeader align_center'>金额</td>
			<td class='tableHeader align_center'>折美元金额</td>
			
		</tr>

		<?php if(is_array($js_list)): $i = 0; $__LIST__ = $js_list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><tr>
			<td class='tableTd align_center'><?php echo ($vo["ywbh"]); ?></td>
			<td class='tableTd align_center'><?php echo ($vo["name"]); ?></td>
			<td class='tableTd align_center'><?php echo ($vo["yw_name"]); ?></td>
			<td class='tableTd align_center'><?php echo ($vo["cny"]); ?></td>
			<td class='tableTd align_right'><?php echo (number_format($vo["txamt"],2)); ?></td>
			<td class='tableTd align_right'><?php echo (number_format($vo["usdamt"],2)); ?></td>		
		</tr><?php endforeach; endif; else: echo "" ;endif; ?>

				<?php if(is_array($tf_list)): $i = 0; $__LIST__ = $tf_list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><tr>
			<td class='tableTd align_center'><?php echo ($vo["ywbh"]); ?></td>
			<td class='tableTd align_center'><?php echo ($vo["name"]); ?></td>
			<td class='tableTd align_center'><?php echo ($vo["yw_name"]); ?></td>
			<td class='tableTd align_center'><?php echo ($vo["cny"]); ?></td>
			<td class='tableTd align_right'><?php echo (number_format($vo["txamt"],2)); ?></td>
			<td class='tableTd align_right'><?php echo (number_format($vo["usdamt"],2)); ?></td>		
		</tr><?php endforeach; endif; else: echo "" ;endif; ?>
	</table>

</div>


<script>
$(function(){
	$("#table_user_list td").bind('mousemove', function(evgl) {
		var evg = evgl.srcElement ? evgl.srcElement : evgl.target;
		evg.parentNode.style.background = "#cbadef";
	});
	$("#table_user_list td").bind('mouseout', function(evgl) {
		var evg = evgl.srcElement ? evgl.srcElement : evgl.target;
		evg.parentNode.style.background = "white";
	});
	
	
	$("#table_tf_balance_list td").bind('mousemove', function(evgl) {
		var evg = evgl.srcElement ? evgl.srcElement : evgl.target;
		evg.parentNode.style.background = "#cbadef";
	});
	$("#table_tf_balance_list td").bind('mouseout', function(evgl) {
		var evg = evgl.srcElement ? evgl.srcElement : evgl.target;
		evg.parentNode.style.background = "white";
	});
	
	
	$("#table_tf_remind_list td").bind('mousemove', function(evgl) {
		var evg = evgl.srcElement ? evgl.srcElement : evgl.target;
		evg.parentNode.style.background = "#cbadef";
	});
	$("#table_tf_remind_list td").bind('mouseout', function(evgl) {
		var evg = evgl.srcElement ? evgl.srcElement : evgl.target;
		evg.parentNode.style.background = "white";
	});
	
	
});


</script>