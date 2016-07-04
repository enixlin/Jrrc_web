<?php if (!defined('THINK_PATH')) exit();?><table id=article_list colspan='3' width='100%'>

	<tr>
		<td height='25px' align=center >发文时间</td>
		<td align=center >文件编号</td>
		<td  align=center>文件标题</td>
		<td  align=center>文件效力(0为有效，2为废止)</td>
	</tr>

	<?php if(is_array($list)): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><tr height=15px>
		
		<td height='20px'><?php echo (substr($vo["d_issue_date"],0,10)); ?></td>
		<td align="center"><?php echo ($vo["d_issue_num"]); ?></td>
		<td><a charset=utf8 href='/Jrrc_web/Home/Policy/findbytitle/title/<?php echo ($vo["d_title"]); ?>' target='_blank'><?php echo ($vo["d_title"]); ?></a></td>
		<td align="center"><?php echo ($vo["d_state"]); ?></td>
	</tr><?php endforeach; endif; else: echo "" ;endif; ?>
</table>

<<?php echo ($page); ?>>
<!--分页输出-->