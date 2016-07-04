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
		<td class='thead width_brid'></td>
		<td class='thead width_brname'></td>
		<td class='thead ,width' colspan=4>国际结算量</td>
		<td class='thead ,width' colspan=4>结售汇量</td>
		<td class='thead ,width' colspan=3>有效对公客户数</td>
		<td class='thead ,width' colspan=3>储蓄存款日均增量</br>( <?php echo ($date_cxrj); ?>)</td>
		
	</tr>

	<tr>
		<td class='thead width_brid'>行号</td>
		<td class='thead width_brname'>经营单位名称</td>
		<td class='thead ,width'>任务</td>
		<td class='thead ,width'>实绩</td>
		<td class='thead ,width'>完成率(%)</td>
		<td class='thead ,width'>本季业绩</td>
		<td class='thead ,width'>任务</td>
		<td class='thead ,width'>实绩</td>
		<td class='thead ,width'>完成率(%)</td>
		<td class='thead ,width'>本季业绩</td>
		<td class='thead ,width'>任务</td>
		<td class='thead ,width'>实绩</td>
		<td class='thead ,width'>完成率(%)</td>
		<td class='thead ,width'>任务</td>
		<td class='thead ,width'>实绩</td>
		<td class='thead ,width'>完成率(%)</td>
		
		
	</tr>
	<?php if(is_array($result)): $i = 0; $__LIST__ = $result;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i; if($vo["p_id"] == '08100' ): ?><tr>
		<td align="center"><?php echo ($vo["br_id"]); ?></td>
		<td align="center" class='br'  id=<?php echo ($vo["br_id"]); ?>>
		<?php if($vo["br_name"] == 国际业务部): ?>代理联社业务<?php endif; ?> 
		<?php if($vo["br_name"] == 总行营业部本部): ?>总营<?php endif; ?> 
		<?php if($vo["br_name"] != 总行营业部本部 and $vo["br_name"] != 国际业务部): echo (str_replace('本部','',$vo["br_name"])); endif; ?>
		</td>
		<td align='center' class='font-aril'><?php echo (number_format($vo['js_task'])); ?></td>
		<td align='right' class='font-aril  br'   id=<?php echo ($vo["br_id"]); ?>><?php echo (number_format($vo['js_amount']/10000,2)); ?></td>	
		<td align='right' class='font-aril '   ><?php echo (number_format($vo['js_amount']/$vo['js_task']/100,2)); ?></td>
		<td align='right' class='font-aril  br'   id=<?php echo ($vo["br_id"]); ?>><?php echo (number_format($vo['js_amount_season']/10000,2)); ?></td>
		
		<td align='center' class='font-aril'><?php echo (number_format($vo['jsh_task'])); ?></td>
		<td align='right' class='font-aril'><?php echo (number_format($vo['jsh_amount']/10000,2)); ?></td>
		<td align='right' class='font-aril'><?php echo (number_format($vo['jsh_amount']/$vo['jsh_task']/100,2)); ?></td>
		<td align='right' class='font-aril'><?php echo (number_format($vo['jsh_amount_season']/10000,2)); ?></td>
		
		<td align='center' class='font-aril'><?php echo (number_format($vo['client_task'])); ?></td>
		<td align='right' class='font-aril'><?php echo (number_format($vo['client_amount'])); ?></td>
		<td align='right' class='font-aril'><?php echo (number_format($vo['client_amount']/$vo['client_task']*100,2)); ?></td>
		<td align='center' class='font-aril'><?php echo (number_format($vo['cxrj_task'])); ?></td>
		<td align='right' class='font-aril'><?php echo (number_format($vo['cxrj']/10000,2)); ?></td>
		<td align='right' class='font-aril'><?php echo (number_format($vo['cxrj']/$vo['cxrj_task']/100,2)); ?></td>
	</tr><?php endif; endforeach; endif; else: echo "" ;endif; ?>
	<tr class='sub_total'>
		<td colspan=2 align="center" class='sub_total'>支行合计</td>
		<td align="center"  class='font-aril sub_total'' ><?php echo (number_format($task_js_branch)); ?></td>
		<td align="center"  class='font-aril sub_total'' ><?php echo (number_format($amount_js_branch/10000,2)); ?></td>
		<td align="center"  class='font-aril sub_total'' ><?php echo (number_format($amount_js_branch/$task_js_branch/100,2)); ?></td>
		<td align="center"  class='font-aril sub_total'' ><?php echo (number_format($amount_js_branch_season/10000,2)); ?></td>
		
		<td align="center"  class='font-aril sub_total'' ><?php echo (number_format($task_jsh_branch)); ?></td>
		<td align="center"  class='font-aril sub_total'' ><?php echo (number_format($amount_jsh_branch/10000,2)); ?></td>
		<td align="center"  class='font-aril sub_total'' ><?php echo (number_format($amount_jsh_branch/$task_jsh_branch/100,2)); ?></td>
		<td align="center"  class='font-aril sub_total'' ><?php echo (number_format($amount_jsh_branch_season/10000,2)); ?></td>
		
		<td align="center"  class='font-aril sub_total'' ><?php echo (number_format($task_client_branch)); ?></td>
		<td align="center"  class='font-aril sub_total'' ><?php echo (number_format($amount_client_branch)); ?></td>
		<td align="center"  class='font-aril sub_total'' ><?php echo (number_format($amount_client_branch/$task_client_branch*100,2)); ?></td>	
		<td align="center"  class='font-aril sub_total'' ><?php echo (number_format($task_cxrj_branch)); ?></td>
		<td align="center"  class='font-aril sub_total'' ><?php echo (number_format($amount_cxrj_branch/10000,2)); ?></td>
		<td align="center"  class='font-aril sub_total'' ><?php echo (number_format($amount_cxrj_branch/$task_cxrj_branch/100,2)); ?></td>
		
	</tr>
	<?php if(is_array($result)): $i = 0; $__LIST__ = $result;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i; if($vo["p_id"] == '08101' ): ?><tr>
		<td align="center"><?php echo ($vo["br_id"]); ?></td>
		<td align="center" class='br'  id=<?php echo ($vo["br_id"]); ?> ><?php if($vo["br_name"] == 国际业务部): ?>代理联社业务<?php endif; ?> <?php if($vo["br_name"] == 总行营业部本部): ?>总营<?php endif; ?> <?php if($vo["br_name"] != 总行营业部本部 and $vo["br_name"] != 国际业务部): echo (str_replace('本部','',$vo["br_name"])); endif; ?></td>
		<td align='center' class='font-aril'><?php echo (number_format($vo['js_task'])); ?></td>
		<td align='right' class='font-aril  br'    id=<?php echo ($vo["br_id"]); ?> ><?php echo (number_format($vo['js_amount']/10000,2)); ?></td>
		<td align='right' class='font-aril'><?php echo (number_format($vo['js_amount']/$vo['js_task']/100,2)); ?></td>
		<td align='right' class='font-aril  br'    id=<?php echo ($vo["br_id"]); ?> ><?php echo (number_format($vo['js_amount_season']/10000,2)); ?></td>
		
		<td align='center' class='font-aril'><?php echo (number_format($vo['jsh_task'])); ?></td>
		<td align='right' class='font-aril'><?php echo (number_format($vo['jsh_amount']/10000,2)); ?></td>
		<td align='right' class='font-aril'><?php echo (number_format($vo['jsh_amount']/$vo['jsh_task']/100,2)); ?></td>
		<td align='right' class='font-aril'><?php echo (number_format($vo['jsh_amount_season']/10000,2)); ?></td>
		
		<td align='center' class='font-aril'><?php echo (number_format($vo['client_task'])); ?></td>
		<td align='right' class='font-aril'><?php echo (number_format($vo['client_amount'])); ?></td>
		<td align='right' class='font-aril'><?php echo (number_format($vo['client_amount']/$vo['client_task']*100,2)); ?></td>
		<td align='center' class='font-aril'><?php echo (number_format($vo['cxrj_task'])); ?></td>
		<td align='right' class='font-aril'><?php echo (number_format($vo['cxrj']/10000,2)); ?></td>
		<td align='right' class='font-aril'><?php echo (number_format($vo['cxrj']/$vo['cxrj_task']/100,2)); ?></td>
	</tr><?php endif; endforeach; endif; else: echo "" ;endif; ?>
	<tr class='sub_total'>
		<td colspan=2 align="center" class='sub_total'>部室合计</td>
		<td align="center"  class='font-aril sub_total'' ><?php echo (number_format($task_js_department)); ?></td>
		<td align="center"  class='font-aril sub_total'' ><?php echo (number_format($amount_js_department/10000,2)); ?></td>
		<td align="center"  class='font-aril sub_total'' ><?php echo (number_format($amount_js_department/$task_js_department/100,2)); ?></td>
		<td align="center"  class='font-aril sub_total'' ><?php echo (number_format($amount_js_department_season/10000,2)); ?></td>
		
		<td align="center"  class='font-aril sub_total'' ><?php echo (number_format($task_jsh_department)); ?></td>
		<td align="center"  class='font-aril sub_total'' ><?php echo (number_format($amount_jsh_department/10000,2)); ?></td>
		<td align="center"  class='font-aril sub_total'' ><?php echo (number_format($amount_jsh_department/$task_jsh_department/100,2)); ?></td>
		<td align="center"  class='font-aril sub_total'' ><?php echo (number_format($amount_jsh_department_season/10000,2)); ?></td>
		
		<td align="center"  class='font-aril sub_total'' ><?php echo (number_format($task_client_department)); ?></td>
		<td align="center"  class='font-aril sub_total'' ><?php echo (number_format($amount_client_department)); ?></td>
		<td align="center"  class='font-aril sub_total'' ><?php echo (number_format($amount_client_department/$task_client_department*100,2)); ?></td>	
		<td align="center"  class='font-aril sub_total'' ><?php echo (number_format($task_cxrj_department)); ?></td>
		<td align="center"  class='font-aril sub_total'' ><?php echo (number_format($amount_cxrj_department/10000,2)); ?></td>
		<td align="center"  class='font-aril sub_total'' ><?php echo (number_format($amount_cxrj_department/$task_cxrj_department/100,2)); ?></td>
	</tr>
	<tr>
		<td colspan=2 class='total'>全行合计</td>
		<td align="center" class='total font-aril'><?php echo (number_format($task_js_department+$task_js_branch)); ?></td>
		<td align="center" class='total font-aril'><?php echo (number_format($amount_js_department/10000+$amount_js_branch/10000,2)); ?></td>
		<td align="center" class='total font-aril'>0</td>
		<td align="center" class='total font-aril'><?php echo (number_format($amount_js_department_season/10000+$amount_js_branch_season/10000,2)); ?></td>
		
		<td align="center" class='total font-aril'><?php echo (number_format($task_jsh_department+$task_jsh_branch)); ?></td>
		<td align="center" class='total font-aril'><?php echo (number_format($amount_jsh_department/10000+$amount_jsh_branch/10000,2)); ?></td>
		<td align="center" class='total font-aril'>0</td>
		<td align="center" class='total font-aril'><?php echo (number_format($amount_jsh_department_season/10000+$amount_jsh_branch_season/10000,2)); ?></td>
		
		<td align="center" class='total font-aril'><?php echo (number_format($task_client_department+$task_client_branch)); ?></td>
		<td align="center" class='total font-aril'><?php echo (number_format($amount_client_department+$amount_client_branch)); ?></td>
		<td align="center" class='total font-aril'>0</td>
		<td align="center" class='total font-aril'><?php echo (number_format($task_cxrj_department+$task_cxrj_branch)); ?></td>
		<td align="center" class='total font-aril'><?php echo (number_format($amount_cxrj_department/10000+$amount_cxrj_branch/10000,2)); ?></td>
		<td align="center" class='total font-aril'>0</td>
	</tr>

</table>

<script>

$("#table_settlement td").bind('click', function(evgl) {

	var evg = evgl.srcElement ? evgl.srcElement : evgl.target;
	var br_id=evg.id;
	var url='';
	if(br_id!=''){
		if(br_id=='08128' || br_id=='08130' || br_id=='08132' || br_id=='08108'){
			url='/jrrc_web/home/report/mkunit_ywl_month';
		}else{
			url='/jrrc_web/home/report/mkbranch_ywl_month';
		}



		var start=$("#start").val();
		var end = $("#end").val();

		url=url+"/br_id/"+br_id;
			url=url+"/type/01";
			url=url+"/start/"+start;
			url=url+"/end/"+end;
			window.open(url,evg.id,"width=1200px,height=600px,top=50,left=60");

	}

});


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