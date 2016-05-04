<?php if (!defined('THINK_PATH')) exit();?><style>
.br {
	cursor: pointer
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
		<td class='thead ,width' colspan=2>#：对私</td>
		<td class='thead ,width' colspan=2>#：对公</td>
		<td class='thead ,width' colspan=2>结售汇量</td>
		<td class='thead ,width' colspan=1>有效对公</br>客户净增</td>
		<td class='thead ,width' colspan=3>外币存款日均增量</br>(
			<?php echo ($date_cxrj[0]['date']); ?>)
		</td>


	</tr>

	<tr>
		<td class='thead width_brid'>行号</td>
		<td class='thead width_brname'>经营单位名称</td>
		<td class='thead ,width'>任务</td>
		<td class='thead ,width'>实绩</td>
		<td class='thead ,width'>完成率(%)</td>
		<td class='thead ,width'>本季业绩</td>
		
		<td class='thead ,width'>实绩</td>
		<td class='thead ,width'>本季业绩</td>

		<td class='thead ,width'>实绩</td>
		<td class='thead ,width'>本季业绩</td>
		

		<td class='thead ,width'>实绩</td>
		<td class='thead ,width'>本季业绩</td>
		

		<td class='thead ,width'>实绩</td>
		
		<td class='thead ,width'>任务</td>
		<td class='thead ,width'>实绩</td>
		<td class='thead ,width'>完成率(%)</td>


	</tr>

	<?php if(is_array($report)): $n = 0; $__LIST__ = $report;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($n % 2 );++$n; if($vo['Head_office'] == '08100' ): ?><tr>
		<td align='center'><?php echo ($vo["br_id"]); ?></td>
		<td align='center' class='br' id=<?php echo ($vo["br_id"]); ?>><?php echo (str_replace('本部','',$vo["br_name"])); ?></td>

		<!-- 国际结算量总额-->
		<?php if($vo['task_amount_js'] == null ): ?><td>&nbsp;</td>
		<?php else: ?>
		<td align='right'><?php echo (number_format($vo["task_amount_js"])); ?></td><?php endif; ?>
		<td align='right'><?php echo (number_format($vo["amount_js"],2)); ?></td>
		<?php if($vo['js_complete_rate'] == null ): ?><td>&nbsp;</td>
		<?php else: ?>
		<td align='right'><?php echo (number_format($vo["js_complete_rate"],2)); ?></td><?php endif; ?>
		<td align='right'><?php echo (number_format($vo["season_amount_js"],2)); ?></td>

		<!-- 对私国际结算量 -->

		<td align='right'><?php echo (number_format($vo["amount_js_private"],2)); ?></td>
		<td align='right'><?php echo (number_format($vo["season_amount_js_private"],2)); ?></td>

		<!-- 对公国际结算量 -->

		<td align='right'><?php echo (number_format($vo["amount_js_company"],2)); ?></td>
		<td align='right'><?php echo (number_format($vo["season_amount_js_company"],2)); ?></td>


		<!-- 结售汇业务量 -->

		<td align='right'><?php echo (number_format($vo["amount_jsh"],2)); ?></td>
		<td align='right'><?php echo (number_format($vo["season_amount_jsh"],2)); ?></td>


		<!-- 有效对公结算客户数 -->

		<td align='center'><?php echo (number_format($vo["amount_client"])); ?></td>


		<!-- 外币储蓄存款日均增量 -->
		<?php if($vo['task_amount_cx'] == null ): ?><td>&nbsp;</td>
		<?php else: ?>
		<td align='right' ><?php echo (number_format($vo["task_amount_cx"])); ?></td><?php endif; ?>
		<td align='right'  class='br' id=<?php echo ($vo["br_id"]); ?>n    ><?php echo (number_format($vo["amount_cx"],2)); ?></td>
		<?php if($vo['cx_complete_rate'] == null ): ?><td>&nbsp;</td>
		<?php else: ?>
		<td align='right'><?php echo (number_format($vo["cx_complete_rate"],2)); ?></td><?php endif; ?>



	</tr><?php endif; endforeach; endif; else: echo "" ;endif; ?>




	<!--输出支行合计  -->
	<tr class='font-aril sub_total'>
		<td align="center" colspan="2" class='sub_total'>支行合计</td>

		<td align="right" class='sub_total'><?php echo (number_format($branch_sum['task_amount_js_sum'],2)); ?></td>
		<td align="right" class='sub_total'><?php echo (number_format($branch_sum['amount_js_sum'],2)); ?></td>
		<td align="right" class='sub_total'><?php echo (number_format($branch_sum['js_complete_rate_sum'],2)); ?></td>
		<td align="right" class='sub_total'><?php echo (number_format($branch_sum['amount_js_season_sum'],2)); ?></td>

	
		<td align="right" class='sub_total'><?php echo (number_format($branch_sum['amount_js_sum_private'],2)); ?></td>
		<td align="right" class='sub_total'><?php echo (number_format($branch_sum['amount_js_season_sum_private'],2)); ?></td>


		<td align="right" class='sub_total'><?php echo (number_format($branch_sum['amount_js_sum_company'],2)); ?></td>
		<td align="right" class='sub_total'><?php echo (number_format($branch_sum['amount_js_season_sum_company'],2)); ?></td>


		<td align="right" class='sub_total'><?php echo (number_format($branch_sum['amount_jsh_sum'],2)); ?></td>
		<td align="right" class='sub_total'><?php echo (number_format($branch_sum['amount_jsh_season_sum'],2)); ?></td>

		<td align="center" class='sub_total'><?php echo (number_format($branch_sum['amount_client_sum'])); ?></td>

		<td align="right" class='sub_total'><?php echo (number_format($branch_sum['task_amount_cx_sum'],2)); ?></td>
		<td align="right" class='sub_total'><?php echo (number_format($branch_sum['amount_cx_sum'],2)); ?></td>
		<td align="right" class='sub_total'><?php echo (number_format($branch_sum['cx_complete_rate_sum'],2)); ?></td>


	</tr>



	<?php if(is_array($report)): $n = 0; $__LIST__ = $report;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($n % 2 );++$n; if($vo['Head_office'] == '08101' ): ?><tr>
		<td align='center'><?php echo ($vo["br_id"]); ?></td>
		<td align="center" class='br' id=<?php echo ($vo["br_id"]); ?>><?php if($vo["br_name"] == 国际业务部): ?>代理联社业务 <?php else: echo (str_replace('本部','',$vo["br_name"])); endif; ?></td>



		<!-- 国际结算量总额 -->
		<?php if($vo['task_amount_js'] == null ): ?><td>&nbsp;</td>
		<?php else: ?>
		<td align='right'><?php echo (number_format($vo["task_amount_js"])); ?></td><?php endif; ?>
		<td align='right'><?php echo (number_format($vo["amount_js"],2)); ?></td>
		<?php if($vo['js_complete_rate'] == null ): ?><td>&nbsp;</td>
		<?php else: ?>
		<td align='right'><?php echo (number_format($vo["js_complete_rate"],2)); ?></td><?php endif; ?>
		<td align='right'><?php echo (number_format($vo["season_amount_js"],2)); ?></td>

		<!-- 对私国际结算量 -->

		<td align='right'><?php echo (number_format($vo["amount_js_private"],2)); ?></td>
		<td align='right'><?php echo (number_format($vo["season_amount_js_private"],2)); ?></td>

		<!-- 对公国际结算量 -->

		<td align='right'><?php echo (number_format($vo["amount_js_company"],2)); ?></td>
		<td align='right'><?php echo (number_format($vo["season_amount_js_company"],2)); ?></td>



		<!--结售汇量  -->

		<td align='right'><?php echo (number_format($vo["amount_jsh"],2)); ?></td>
		<td align='right'><?php echo (number_format($vo["season_amount_jsh"],2)); ?></td>

		<!--有效对公客户数  -->

		<td align='center'><?php echo (number_format($vo["amount_client"])); ?></td>


		<!--外币储蓄存款日均增量  -->
		<?php if($vo['task_amount_cx'] == null ): ?><td>&nbsp;</td>
		<?php else: ?>
		<td align='right'><?php echo (number_format($vo["task_amount_cx"])); ?></td><?php endif; ?>
		<td align='right'><?php echo (number_format($vo["amount_cx"],2)); ?></td>
		<?php if($vo['cx_complete_rate'] == null ): ?><td>&nbsp;</td>
		<?php else: ?>
		<td align='right'><?php echo (number_format($vo["cx_complete_rate"],2)); ?></td><?php endif; ?>



	</tr><?php endif; endforeach; endif; else: echo "" ;endif; ?>

	<!--输出部门小计  -->
	<tr class='font-aril sub_total'>
		<td align="center" colspan="2" class='sub_total'>部门小计</td>

		<td align="right" class='sub_total'><?php echo (number_format($department_sum['task_amount_js_sum'],2)); ?></td>
		<td align="right" class='sub_total'><?php echo (number_format($department_sum['amount_js_sum'],2)); ?></td>
		<td align="right" class='sub_total'><?php echo (number_format($department_sum['js_complete_rate_sum'],2)); ?></td>
		<td align="right" class='sub_total'><?php echo (number_format($department_sum['amount_js_season_sum'],2)); ?></td>


		<td align="right" class='sub_total'><?php echo (number_format($department_sum['amount_js_sum_private'],2)); ?></td>
		<td align="right" class='sub_total'><?php echo (number_format($department_sum['amount_js_season_sum_private'],2)); ?></td>


		<td align="right" class='sub_total'><?php echo (number_format($department_sum['amount_js_sum_company'],2)); ?></td>
		<td align="right" class='sub_total'><?php echo (number_format($department_sum['amount_js_season_sum_company'],2)); ?></td>


		<td align="right" class='sub_total'><?php echo (number_format($department_sum['amount_jsh_sum'],2)); ?></td>
		<td align="right" class='sub_total'><?php echo (number_format($department_sum['amount_jsh_season_sum'],2)); ?></td>


		<td align="center" class='sub_total'><?php echo (number_format($department_sum['amount_client_sum'])); ?></td>


		<td align="right" class='sub_total'><?php echo (number_format($department_sum['task_amount_cx_sum'],2)); ?></td>
		<td align="right" class='sub_total'><?php echo (number_format($department_sum['amount_cx_sum'],2)); ?></td>
		<td align="right" class='sub_total'><?php echo (number_format($department_sum['cx_complete_rate_sum'],2)); ?></td>


	</tr>


	<!--输出全行合计  -->
	<tr class='font-aril sub_total'>
		<td align="center" colspan="2" class='total'>全行合计</td>

		<td align="right" class='total'><?php echo (number_format($total['task_amount_js_sum'],2)); ?></td>
		<td align="right" class='total'><?php echo (number_format($total['amount_js_sum'],2)); ?></td>
		<td align="right" class='total'><?php echo (number_format($total['js_complete_rate_sum'],2)); ?></td>
		<td align="right" class='total'><?php echo (number_format($total['amount_js_season_sum'],2)); ?></td>


		<td align="right" class='total'><?php echo (number_format($total['amount_js_sum_private'],2)); ?></td>
		<td align="right" class='total'><?php echo (number_format($total['amount_js_season_sum_private'],2)); ?></td>


		<td align="right" class='total'><?php echo (number_format($total['amount_js_sum_company'],2)); ?></td>
		<td align="right" class='total'><?php echo (number_format($total['amount_js_season_sum_company'],2)); ?></td>


		<td align="right" class='total'><?php echo (number_format($total['amount_jsh_sum'],2)); ?></td>
		<td align="right" class='total'><?php echo (number_format($total['amount_jsh_season_sum'],2)); ?></td>


		<td align="center" class='total'><?php echo (number_format($total['amount_client_sum'])); ?></td>


		<td align="right" class='total'><?php echo (number_format($total['task_amount_cx_sum'],2)); ?></td>
		<td align="right" class='total'><?php echo (number_format($total['amount_cx_sum'],2)); ?></td>
		<td align="right" class='total'><?php echo (number_format($total['cx_complete_rate_sum'],2)); ?></td>


	</tr>

</table>



<script>
	$("#table_settlement td").bind(
			'click',
			function(evgl) {

				var evg = evgl.srcElement ? evgl.srcElement : evgl.target;
				var br_id = evg.id;
				var url = '';
				if (br_id != '' ) {
					if (br_id == '08128' || br_id == '08130'
							|| br_id == '08132' || br_id == '08108') {
						url = '/jrrc_web/home/report2016/show_Unit_Report_Month';
					} else {
						url = '/jrrc_web/home/report2016/show_Unit_Report_Month';
					}

					var start = $("#start").val();
					var end = $("#end").val();

					url = url + "/unit/" + br_id;
					url = url + "/start/" + start;
					url = url + "/end/" + end;
					url = url + "/type/01";
					window.open(url, evg.id,
							"width=1200px,height=600px,top=50,left=60");

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