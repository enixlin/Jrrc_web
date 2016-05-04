<?php if (!defined('THINK_PATH')) exit();?>
<style>


</style>
<!-- 记事事表格 -->
<div id='div_schedule'>

</div>





<script>
	$(function() {
		
		var date=new Date();
		var year=date.getFullYear();
		var month=date.getMonth()+1;
		$.post('/Jrrc_web/Home/Schedule/createtable/uid/'+<?php echo ($uid); ?>+'/year/'+year+'/month/'+month,
				function(msg) {
					$("#div_schedule").html(msg);
			});

	});
	
</script>