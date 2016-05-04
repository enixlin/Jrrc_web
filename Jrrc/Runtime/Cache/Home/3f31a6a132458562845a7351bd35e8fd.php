<?php if (!defined('THINK_PATH')) exit();?><style>
#schedule_table {
					width:100%;
					height:450px;
					border-collapse:collapse;
}

#schedule_table td  {
					border:1px solid gray;
					vertical-align:top;
					
}

.thead 	{
					text-align:center;
					color:black;
					background:#abcdef;
					height:15px;
					width:14%;
					
}

.day_no_month{
					width:49%;
					height:15px;
					font-size:15px;
					background:#f1f1f1;
					color:gray;
					float:left;
}

.bg_no_month {
					width:100%;
					background:#f1f1f1;
				
}

.day_this_month{
					width:49%;
					height:15px;
					background:#f1f1f1;
					color:blue;
					float:left;
					font-size:15px;
}

.today_bg_this_month {
					width:100%;
					height:80px;
					background:#00FCBE;
					vertical-align:top
	}
.bg_this_month {
					width:100%;
					height:80px;
					background:white;
					vertical-align:top
	}

.td_bg_gray{
					background:#f1f1ff;
}

.table_title{
					text-align: center;
					vertical-align : middle;
}
.div_line{
					width:46%;
					float:left;
					font-size:11px;
					border:1px solid orange;
					margin:1px;
					
					overflow:auto;
					
					
}
.smallico{
					widthd:15px;
					height:15px
}

.div_folder{
					width:48%;
					height:15px;
					background:yellow;
					float:left;
					font-size:15px;
}

</style>


<table id='schedule_table' >

<!-- *************************** -->
<!--  -->
<!--输出表头  --> 
<!--  -->
<!-- *************************** -->
	<tr>
		<td colspan=1 class='table_title' height='35px'>记事日程</td>
		<td colspan=5 class='table_title'>
		
			年<select name="select_year" id="select_year">
				<option value="2012">2012</option>
				<option value="2013">2013</option>
				<option value="2014">2014</option>
				<option value="2015">2015</option>
				<option value="2016">2016</option>
				<option value="2017">2017</option>
				<option value="2018">2018</option>
				<option value="2019">2019</option>
				</select>
			月<select name="select_month" id="select_month">
				<option value="1">1</option>
				<option value="2">2</option>
				<option value="3">3</option>
				<option value="4">4</option>
				<option value="5">5</option>
				<option value="6">6</option>
				<option value="7">7</option>
				<option value="8">8</option>
				<option value="9">9</option>
				<option value="10">10</option>
				<option value="11">11</option>
				<option value="12">12</option>
				</select>
			
		</td>
		<td colspan='1'  class='table_title' id='add'>
		新增记事 <img src="/Jrrc_web/Public/res/ico/memo.ico" alt="" title='新增记事' style="width:20px;height:20px"/>
		</td>
	</tr> 
	
	<tr>
		<td class='thead'>星期一</td>
		<td class='thead'>星期二</td>
		<td class='thead'>星期三</td>
		<td class='thead'>星期四</td>
		<td class='thead'>星期五</td>
		<td class='thead'>星期六</td>
		<td class='thead'>星期日</td>
	</tr>
<!-- *************************** -->
<!--输出表头结束  -->
<!-- *************************** -->





<!-- *************************** -->

<!--输出表体  -->

<!-- *************************** -->
	
	<!--输出所有日期  -->
	<?php if(is_array($daylist)): $k1 = 0; $__LIST__ = $daylist;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($k1 % 2 );++$k1; if((($k1)-1)%7 == 0 ): ?><tr><?php endif; ?>
		
		<!--不是本月的日期  -->
		<?php if(substr($key,5,2) != $month): ?><td class='td_bg_gray'>
				<div class='bg_no_month'>
					<div class='day_no_month'><?php echo (substr($key,8,2)); ?></div>
						<!--输出用户的记事  -->
						<?php if(count($vo) < 5): if(is_array($vo)): $i = 0; $__LIST__ = $vo;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$sub): $mod = ($i % 2 );++$i;?><div class='div_line' title='【<?php echo ($sub["title"]); ?>】:<?php echo ($sub["content"]); ?>'  id='<?php echo ($sub["id"]); ?>'>
							<img src="/Jrrc_web/Public/res/ico/pin<?php echo ($sub["level"]); ?>.ico" alt="" class='smallico'/>
							<?php echo ($sub["title"]); ?>
							</div><?php endforeach; endif; else: echo "" ;endif; ?>
						<?php else: ?>
							<div id='folder' class='div_folder'> 展开...</div>
							<div id='div_marks' value='hide' style='display:none'>
							<?php if(is_array($vo)): $i = 0; $__LIST__ = $vo;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$sub): $mod = ($i % 2 );++$i;?><div class='div_line' title='【<?php echo ($sub["title"]); ?>】:<?php echo ($sub["content"]); ?>'  id='<?php echo ($sub["id"]); ?>'>
							<img src="/Jrrc_web/Public/res/ico/pin<?php echo ($sub["level"]); ?>.ico" alt="" class='smallico'/>
							<?php echo ($sub["title"]); ?>
							</div><?php endforeach; endif; else: echo "" ;endif; ?>
							</div><?php endif; ?>
				</div>
			</td><?php endif; ?>
		
		
		<!--本月的日期  -->
		<?php if(substr($key,5,2) == $month): ?><td>	
					<?php if($day == substr($key,8,2) and $month == substr($key,5,2)): ?><div class='today_bg_this_month'>
					<?php else: ?>
						<div class='bg_this_month'><?php endif; ?>
					<div class='day_this_month'><?php echo (substr($key,8,2)); ?></div>
						<!--输出用户的记事  -->
						<?php if(count($vo) < 5): if(is_array($vo)): $i = 0; $__LIST__ = $vo;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$sub): $mod = ($i % 2 );++$i;?><div class='div_line' title='【<?php echo ($sub["title"]); ?>】:<?php echo ($sub["content"]); ?>' id='<?php echo ($sub["id"]); ?>'>
							<img src="/Jrrc_web/Public/res/ico/pin<?php echo ($sub["level"]); ?>.ico" alt="" class='smallico'/>
							<?php echo ($sub["title"]); ?>
							</div><?php endforeach; endif; else: echo "" ;endif; ?>
						<?php else: ?>
							<div id='folder' class='div_folder'> 展开...</div>
							<div id='div_marks' value='hide'  style='display:none'>
							<?php if(is_array($vo)): $i = 0; $__LIST__ = $vo;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$sub): $mod = ($i % 2 );++$i;?><div class='div_line' title='【<?php echo ($sub["title"]); ?>】:<?php echo ($sub["content"]); ?>'   id='<?php echo ($sub["id"]); ?>'>
							<img src="/Jrrc_web/Public/res/ico/pin<?php echo ($sub["level"]); ?>.ico" alt="" class='smallico'/>
							<?php echo ($sub["title"]); ?>
							</div><?php endforeach; endif; else: echo "" ;endif; ?>
							</div><?php endif; ?>
				</div>
			</td><?php endif; ?>

		<?php if(($k1)%7 == 0): ?></tr><?php endif; endforeach; endif; else: echo "" ;endif; ?>

<!-- *************************** -->
<!--输出表体结束  -->
<!-- *************************** -->

</table>
<!-- 添加记事对话框 -->
<div id='div_input_schedule'></div>
<!-- 修改对话框-->
<div id='div_modify_schedule'></div>

<script>

$(function(){


	function add(){
		$.post('/Jrrc_web/Home/Schedule/showaddbox', function(msg) {
			$("#div_input_schedule").html(msg);
		});	
	}

	function modify(id){
		$.post('/Jrrc_web/Home/Schedule/showmodifybox', {
			'id' : id
		}, function(msg) {
			$("#div_modify_schedule").html(msg);
		});	
	}

	
	$("div").on('click',function(evgl){
		
		var evg=evgl.srcElement?evgl.sElement:evgl.target;

		if(evg.id=='folder'){
		$(evg).next().fadeToggle();
		evgl.stopPropagation();
		}		
	});

	$("td").bind('click',function(evgl){
		var evg=evgl.srcElement?evgl.sElement:evgl.target; 
		if(evg.id!='' && evg.id!='add' && evg.id!='select_month'){
			modify(evg.id);
		}else if(evg.id=='add'){
			add();
		}
	});
	
	//顯示當前的年月
	$("#select_year").val(<?php echo ($year); ?>);
	$("#select_month").val(<?php echo ($month); ?>);
	

	$("#select_year").change(function(){
		var year=$("#select_year").val();
		var month=$("#select_month").val();
		$.post('/Jrrc_web/Home/Schedule/createtable/uid/'+<?php echo ($uid); ?>+'/year/'+year+'/month/'+month,
				function(msg) {
					$("#div_schedule").html(msg);
			});
	});

	$("#select_month").change(function(){
		var year=$("#select_year").val();
		var month=$("#select_month").val();
		$.post('/Jrrc_web/Home/Schedule/createtable/uid/'+<?php echo ($uid); ?>+'/year/'+year+'/month/'+month,
				function(msg) {
					$("#div_schedule").html(msg);
			});
	});
	
	$("#add").on('mousemove',function(){
		$("#add").css({'background':'yellow'});
	});
	$("#add").on('mouseout',function(){
		$("#add").css({'background':'white'});
	});

	
	
});

</script>