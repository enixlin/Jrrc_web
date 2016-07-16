//日期选框
$(".date").datepicker();
$(".date").css({width:'120px',background:'orange',align:'center'});


// 当鼠标停在借据列表记录上时高亮
$("td").mousemove(function(event) {
    /* Act on the event */
     if ($(event.target).parent().attr("id")=='tb_header') {
     }else{
        $(event.target).parent().css('backgroundColor', 'yellow');
     }   
});
$(".btn").mousemove(function(event) {
    /* Act on the event */
    $(event.target).parent().parent().css('backgroundColor', 'yellow');
});

// 当鼠标离开借据列表记录上时变回原着色    
$("td").mouseout(function(event) {
    /* Act on the event */
    if ($(event.target).parent().attr("id")=='tb_header') {
      // alert( $(event.target).parent().attr("id") );
        $("#tb_header").css('backgroundColor', 'abcdef');
    }else{
        $(event.target).parent().css('backgroundColor', 'white');
    }  
});

$(".btn").mouseout(function(event) {
    /* Act on the event */
    $(event.target).parent().parent().css('backgroundColor', 'white');
});



//自动校验填充借据查询中的日期
function validate(){
	if($("#cdate_s").val()==''){
		$("#cdate_s").val($("#cdate_e").val());
	};
	if($("#cdate_e").val()==''){
		$("#cdate_e").val($("#cdate_s").val());
	};
	if($("#rdate_s").val()==''){
		$("#rdate_s").val($("#rdate_e").val());
	};
	if($("#rdate_e").val()==''){
		$("#rdate_e").val($("#rdate_s").val());
	};
	if($("#cdate_e").val()<$("#cdate_s").val()){
		alert('借款日期区间选择有误，结束日期不能早于开始日期 ');
		$("#cdate_e").val("");
	};
	if($("#rdate_e").val()<$("#rdate_s").val()){
		alert('到期日期区间选择有误，结束日期不能早于开始日期 ');
		$("#rdate_e").val("");
	};	
	if($("#rdate_s").val()!='' && $("#rdate_s").val()<$("#cdate_s").val()){
		alert('日期区间选择有误，到期日期不能早于借款日期 ');
		$("#rdate_s").val("");
		$("#rdate_e").val("");
	};
}

//输出借据表
function makeloanlist(data){
	if(data==null){
		alert('没有该客户的借款记录');
		return;
	}
	
	var table_header='';
	var table_body='';
	
	table_header="<table>";
	table_header+="<tr id='tb_header'>";
	table_header+="<td class='text_center'>序号</td>";
	table_header+="<td class='long_td text_center'>客户</td>";
	table_header+="<td>借据编号</td>";
	table_header+="<td class='middle_td text_center'>业务种类</td>";
	table_header+="<td>币种</td>";
	table_header+="<td>金额</td>";
	table_header+="<td>利率</td>";
	table_header+="<td>借款日期</td>";
	table_header+="<td>到期日期</td>";
	table_header+="<td>余额</td>";
	table_header+="<td>备注</td>";
	table_header+="<td>后续操作</td>";
	table_header+="</tr>";
	
	for(var i=0;i<data.length;i++){
		table_body+="<tr id='tb_header'>";
		table_body+="<td class='text_center short_td'>"+i+"</td>";
		table_body+="<td class='text_center '>"+data[i].cname+"</td>";
		table_body+="<td>"+data[i].credit_code+"</td>";
		table_body+="<td class='middle_td text_center'>"+data[i].tname+"</td>";
		table_body+="<td>"+data[i].currency+"</td>";
		table_body+="<td>"+data[i].amount+"</td>";
		table_body+="<td>"+data[i].rate+"</td>";
		table_body+="<td>"+data[i].cdate+"</td>";
		table_body+="<td>"+data[i].rdate+"</td>";
		table_body+="<td>"+((data[i].amount)-(data[i].tf_return[0].sr))+"</td>";
		table_body+="<td>"+data[i].memo+"</td>";
		table_body+="<td id="+data[i].credit_code+" class='middle_td text_center'>";
		table_body+="<input type='button' id='btn_loan_modify'  value='修改' title='修改借据'>";
		table_body+="<input type='button' id='btn_loan_return'  value='还款' title='新增还款记录'>";
		table_body+="</td>";
		table_body+="</tr>";
	};
	return table_header+table_body;
}



/////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
//新增客户
$("#tf_addClient").dialog({ autoOpen: false,title:'增加客户'});
$("#tf_modifyClient").dialog({ autoOpen: false,title:'修改客户名称'});
//打开新增客户对话框
$("#btn_addclient").unbind('click');
$("#btn_addclient").click(function(event){
	$("#tf_addClient").dialog({ autoOpen: true,title:'增加客户'});
});
//保存新增客户
$("#btn_saveClient").unbind('click');
$("#btn_saveClient").click(function(event) {
    $.post("/jrrc/home/tf/addclient/", {tb_addclient:$("#tb_addclientname").val()},function(data){
            if (data=='客户已存在') {
                alert('客户已存在');
                return;
            }
            else{
            	$("#tf_addClient").dialog("close");
            	//刷新客户列表
                $.post('/jrrc/home/tf/refreshQuery', function(data, textStatus, xhr) {
                   $("#tf_client_select").html('');
                   $("#select_client_add").html('');     
                   var str1="<option value='%'>全部客户</option>";
                   var str='';
                   for(var i=0;i<data.length;i++){
                	   str+="<option value="+data[i].uid+">"+data[i].cname+"</option>";
                   }
                   $("#tf_client_select").html(str1+str);
                   $("#select_client_add").html(str); 
                   $("#tf_clientmodify").html(str);
                },'json');
            }
    }); 
});

//修改客户名称
$("#btn_modifyclient").unbind('click');
$("#btn_modifyclient").click(function(event){
	$("#tf_modifyClient").dialog({ autoOpen: true,title:'修改客户名称'});
});

/////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////












/////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
//新增借据
$("#tf_addloan").dialog({ autoOpen: false,title:'增加借据'});
//打开新增借据对话框
$("#btn_loan_add").unbind('click');
$("#btn_loan_add").click(function(event){
	$("#tf_addloan").dialog({width:680});
	$("#tf_addloan").dialog({ autoOpen: true,title:'增加借据'});
});
//保存借据
$("#btn_loan_save").unbind('click');
$("#btn_loan_save").click(function(event){
	$.post("/jrrc/home/tf/addloan/", $("#tf_loan_add_form").serialize() ,function(data){
        if (data=='借据编号已存在') {
            alert('借据已存在');
            return;
        }
        else{
        	$("#tf_addClient").dialog("close");
        	//刷新借据列表
        	$.post("/jrrc/home/tf/getloanlist/", $("#query_loan_form").serialize() ,function(data){
        		$("#loanlist").html(makeloanlist(data));
        	},'json'); 
        }
	}); 
});
/////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////







/////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
//隐藏借据查询条件
$("#btn_showloanquery").click(function(event){
	$("#querydiv").fadeToggle();
	if($("#btn_showloanquery").attr('value')=='隐藏查询条件'){
		$("#btn_showloanquery").attr('value',"显示查询条件");
	}else{
		$("#btn_showloanquery").attr('value',"隐藏查询条件");
	}
});

//查询借据
$("#btn_loan_search").unbind('click');
$("#btn_loan_search").click(function(){
	validate();//前端校验日期
	$.post("/jrrc/home/tf/getloanlist/", $("#query_loan_form").serialize() ,function(data){
		$("#loanlist").html(makeloanlist(data));

	},'json'); 
	
});
/////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////