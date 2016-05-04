<?php

namespace Home\Controller;
// require '../jrrc_web/Public/Classes/PHPExcel.php';
use Think\Controller;
use Org\Util\Date;
require ("../Jrrc_web/Public/Classes/PHPExcel.php");

class ReportController extends Controller {
	public function index() {
		//将用户ip\host\登录时间等数据写入数据库
		if(
				$_SERVER['REMOTE_ADDR']!='127.0.0.1'
				 &&
				$_SERVER['REMOTE_ADDR']!='109.0.25.16'
				&&
				$_SERVER['REMOTE_ADDR']!='109.0.25.6'
				){		
		$log=M('log');
		$data['ip']=$_SERVER['REMOTE_ADDR'];
		$data['host']=$_SERVER['REMOTE_HOST'];
		$data['time']=date('y-m-d h:i:s',time());
		
		$log->add($data);
		}
		
		
		$this->display ( 'index' );
	}
	public function report() {
		if(I('post.report_type') =="1"){
			$this->report_branch ( I ( 'post.start' ), I ( 'post.end' ) );
		}
		if(I('post.report_type')=="2"){
			$this->mkclientywl(I('post.start'),I('post.end'));
		}
	}
	
	public function showlog(){
		$log=M('log');
		$result=$log->select();
		foreach ($result as &$r){
			if($r['host']==''){
				$r['host']=mb_convert_encoding ( gethostbyaddr($r['ip']), "utf-8", "gbk, gb2312" );
				$log->save($r);
			}
		}
		unset($r);
		$this->assign('result',$result);
		$this->display('showlog');
	}
	
	
	
	public function clientsearch(){
		$model=M("struction");
		
	
		$this->display('clientsearch');
	}
	/**
	 * 全行业务量分类明细
	 */
	public function mk_all_ywl_type($start,$end){
		$Model = M ();
		$sql_type="
				select yw_name ,count(usdamt) as js_bishu,sum(usdamt) as js_amount
						FROM
						jrrc_ywls
						where 
							 type='01'
						and yw_date>=".$start."
						and yw_date<=".$end."
						GROUP BY yw_name
				";
	return	$result= $Model->query ( $sql_type );
		
	}
	
	/**
	 * 全行业务量分月明细
	 */
	public function mkallywl($start,$end){
		$Model = M ();
		$sql_all_ywl= "
					select LEFT(yw_date,6) as yw_month,count(usdamt) as js_bishu,sum(usdamt) as js_amount
							FROM
							jrrc_ywls
							where
				 	 				type='01'
								and yw_date>=".$start."
								and yw_date<=".$end."
							GROUP BY yw_month
				";
		
		
		$result= $Model->query ( $sql_all_ywl );
		$yw_type_result=$this->mk_all_ywl_type($start, $end);
		//dump(	$result= $Model->query ( $sql_client ));
		
		$this->assign('result',$result);
		$this->assign('yw_type_result',$yw_type_result);
		$this->assign('start',$start);
		$this->assign('end',$end);
		$this->assign('now',NOW_TIME);
		$this->display('all_month_list');
	}
	
	
	// ///////////////////////////////////////////////////////////////////////////////////////////////
	//
	// 输出支行维度的报表
	//
	// ///////////////////////////////////////////////////////////////////////////////////////////////
	public function report_branch($start, $end) {
		$type_js = '01'; // 结算量类型
		$type_jsh = "02"; // 结售汇量类型
		
		// 判断本日是在第几季度
	    $today=date('md');
	    $year=date('Y');
	   // echo $today;
	    $season_start='';
	    $season_end='';
	 	if($today>='0101' && $today<='0331'){
	 		$season_start=$year.'0101';
	 		$season_end=$year.'0331';
	 	}
	 	if($today>='0401' && $today<='0630'){
	 		$season_start=$year.'0401';
	 		$season_end=$year.'0630';
	 	}
	 	if($today>='0701' && $today<='0930'){
	 		$season_start=$year.'0701';
	 		$season_end=$year.'0930';
	 	}
	 	if($today>='1001' && $today<='1231'){
	 		$season_start=$year.'1001';
	 		$season_end=$year.'1231';
	 	}
		                
		// 按支行和事业部维度生成结算量数组
		$result_js = $this->mkywl_js ( $type_js, $start, $end );
		// 按支行和事业部维度生成本季度结算量数组
		$result_js_season = $this->mkywl_js ( $type_js, $season_start, $season_end );
		//dump( $result_js_season);
		// 按支行和事业部维度生成结售汇量数组
		$result_jsh = $this->mkywl_jsh ( $type_jsh, $start, $end );
		// 按支行和事业部维度生成本季度结售汇量数组
		$result_jsh_season = $this->mkywl_jsh ( $type_jsh, $season_start, $season_end );
		// 从机构信息表中生成所有一级经营单位（一级、直属支行加事业部）的组数
		$unit = $this->mkUnit ();
		// 按支行和事业部维度生成储蓄存款余额数组
		$result_cxrj=$this->mkcxrj();
		//取得经营销任务数据
		$result_task=$this->mktask($end);
		//取得所有客户
		$result_client=$this->clientcount($type_js,$start,$end);
		//存款余额增量日期
		 $date_cxrj=$result_cxrj[0][c_date];
		
		// dump($result_client);
		 
		// 历遍所有一级经营单 位
		foreach ( $unit as $key => &$u ) {
			$flag_js = 0;
			$flag_jsh = 0;
			$flag_js_season = 0;
			$flag_jsh_season = 0;
			$flag_cxrj= 0;
			$flag_client= 0;
			// 历遍所有结算量
			foreach ( $result_js as $r ) {
				// 如果行号与一级经营单位相同的，就将业务笔数和金额加入该数组中
				if ($u ['br_id'] == $r ['p_id']) {
					$u['js_bishu']=$r['bishu'];
					$u['js_amount']=$r['amount'];
					$flag_js = 1;
				}
			}
			// 如果所有结算量行号与一级经营单位都不相同的，就将业务笔数和金额置为0
			if ($flag_js == 0) {
				$u['js_bishu']=0;
				$u['js_amount']=0;
			}
			// 历遍所有本季结算量
			foreach ( $result_js_season as $r ) {
				// 如果行号与一级经营单位相同的，就将业务笔数和金额加入该数组中
				if ($u ['br_id'] == $r ['p_id']) {
					$u['js_bishu_season']=$r['bishu'];
					$u['js_amount_season']=$r['amount'];
					$flag_js_season = 1;
				}
			}
			// 如果所有结算量行号与一级经营单位都不相同的，就将业务笔数和金额置为0
			if ($flag_js_season == 0) {
				$u['js_bishu_season']=0;
				$u['js_amount_season']=0;
			}
			// 历遍所有结售汇量
			foreach ( $result_jsh as $r ) {
				// 如果行号与一级经营单位相同的，就将业务笔数和金额加入该数组中
				if ($u ['br_id'] == $r ['p_id']) {
					$u['jsh_bishu']=$r['bishu'];
					$u['jsh_amount']=$r['amount'];
					$flag_jsh = 1;
				}
			}
			// 如果所有结算量行号与一级经营单位都不相同的，就将业务笔数和金额置为0
			if ($flag_jsh == 0) {
				$u['jsh_bishu']=0;
				$u['jsh_amount']=0;
			}
			// 历遍所有本季结售汇量
			foreach ( $result_jsh_season as $r ) {
				// 如果行号与一级经营单位相同的，就将业务笔数和金额加入该数组中
				if ($u ['br_id'] == $r ['p_id']) {
					$u['jsh_bishu_season']=$r['bishu'];
					$u['jsh_amount_season']=$r['amount'];
					$flag_jsh_season = 1;
				}
			}
			// 如果所有结算量行号与一级经营单位都不相同的，就将业务笔数和金额置为0
			if ($flag_jsh_season == 0) {
				$u['jsh_bishu_season']=0;
				$u['jsh_amount_season']=0;
			}
			
			// 历遍所有客户量
			foreach ( $result_client as $r ) {
				// 如果行号与一级经营单位相同的，就将客户数加入该数组中
				if ($u ['br_id'] == $r ['p_id']) {
					$u['client_amount']=$r['clientcount'];
					$flag_client = 1;
				}
			}
			// 如果所有结算量行号与一级经营单位都不相同的，就将客户数置为0
			if ($flag_client== 0) {
				$u['client_amount']=0;
			}
			
			// 历遍所有存款余额数组
			foreach ( $result_cxrj as $r ) {
				// 如果行号与一级经营单位相同的，就将存款余额加入该数组中
				if ($u ['br_id'] == $r ['br_id']) {
					$u['cxrj']=$r['c_rj']-$r['b_rj'] ;
					$flag_cxrj= 1;
				}
			}
			// 如果所有结算量行号与一级经营单位都不相同的，就将存款余额置为0
			if ($flag_cxrj == 0) {
				$u['cxrj']=0;
			}
			
			// 历遍所有任务数组
			foreach ( $result_task as $r ) {
				// 如果行号与一级经营单位相同的，就将存款余额加入该数组中
					if ($u ['br_id'] == $r ['unit_id'] && $r['zb_type']==1) {
						$u['js_task']=$r['task'] ;
						
					}
					if ($u ['br_id'] == $r ['unit_id'] && $r['zb_type']==2) {
						$u['jsh_task']=$r['task'] ;
					
					}
					if ($u ['br_id'] == $r ['unit_id'] && $r['zb_type']==3) {
						$u['client_task']=$r['task'] ;
					
					}
					if ($u ['br_id'] == $r ['unit_id'] && $r['zb_type']==4) {
						$u['cxrj_task']=$r['task'] ;
					
					}
			
				
			}
			
			
		}
		//由于用&直接引用了UNIT数组，所以最后的$u要注销
		unset($u);
		
		//dump ($unit);
		//dump ($result_cxrj);
		// 生成支行和事部的业务任务小计
		$task_js_branch = 0;
		$task_js_department = 0;
		$task_jsh_branch = 0;
		$task_jsh_department = 0;
		$task_client_branch=0;
		$task_client_department=0;
		$task_cxrj_branch = 0;
		$task_cxrj_department = 0;
		// 生成支行和事部的业务金额小计
		$amount_js_branch = 0;
		$amount_js_department = 0;
		$amount_jsh_branch = 0;
		$amount_jsh_department = 0;
		
		$amount_js_branch_season = 0;
		$amount_js_department_season = 0;
		$amount_jsh_branch_season = 0;
		$amount_jsh_department_season = 0;
		
		$amount_client_branch=0;
		$amount_client_department=0;
		$amount_cxrj_branch = 0;
		$amount_cxrj_department = 0;
		// 生成支行和事部的业务量笔数小计
		$bishu_js_branch = 0;
		$bishu_js_department = 0;
		$bishu_jsh_branch = 0;
		$bishu_jsh_department = 0;
		$bishu_cxrj_branch = 0;
		$bishu_cxrj_department = 0;
		foreach ( $unit as $u ) {
			if ($u ['p_id'] == '08100') {
				$bishu_js_branch = $bishu_js_branch + $u ['js_bishu'];
				$amount_js_branch = $amount_js_branch + $u ['js_amount'];
				$amount_js_branch_season = $amount_js_branch_season + $u ['js_amount_season'];
				$bishu_jsh_branch = $bishu_jsh_branch + $u ['jsh_bishu'];
				$amount_jsh_branch = $amount_jsh_branch + $u ['jsh_amount'];
				$amount_jsh_branch_season = $amount_jsh_branch_season + $u ['jsh_amount_season'];
				$amount_client_branch = $amount_client_branch + $u ['client_amount'];
				$amount_cxrj_branch = $amount_cxrj_branch + $u ['cxrj'];
				$task_js_branch=$task_js_branch+$u['js_task'];
				$task_jsh_branch=$task_jsh_branch+$u['jsh_task'];
				$task_client_branch=$task_client_branch+$u['client_task'];
				$task_cxrj_branch=$task_cxrj_branch+$u['cxrj_task'];
			}
			if ($u ['p_id'] == '08101') {
				$bishu_js_department = $bishu_js_department +$u ['js_bishu'];;
				$amount_js_department = $amount_js_department + $u ['js_amount'];
				$amount_js_department_season = $amount_js_department_season + $u ['js_amount_season'];
				$bishu_jsh_department = $bishu_jsh_department + $u ['jsh_bishu'];
				$amount_jsh_department = $amount_jsh_department + $u ['jsh_amount'];
				$amount_jsh_department_season = $amount_jsh_department_season + $u ['jsh_amount_season'];
				$amount_client_department = $amount_client_department + $u ['client_amount'];
				$amount_cxrj_department = $amount_cxrj_department + $u ['cxrj'];
				$task_js_department=$task_js_department+$u['js_task'];
			}
		}
		
		// 向模板输出变量和渲染模板
		
		$this->assign('task_js_branch',$task_js_branch);
		$this->assign('task_js_department',$task_js_department);
		$this->assign('task_jsh_branch',$task_jsh_branch);
		$this->assign('task_jsh_department',$task_jsh_department);
		$this->assign('task_client_branch',$task_client_branch);
		$this->assign('task_cxrj_branch',$task_cxrj_branch);
		
		
		
		$this->assign ( 'result', $unit );
		$this->assign ( 'amount_js_branch', $amount_js_branch );
		$this->assign ( 'amount_js_branch_season', $amount_js_branch_season );
		$this->assign ( 'amount_js_department', $amount_js_department );
		$this->assign ( 'amount_js_department_season', $amount_js_department_season );
		$this->assign ( 'amount_jsh_branch', $amount_jsh_branch );
		$this->assign ( 'amount_jsh_branch_season', $amount_jsh_branch_season );
		$this->assign ( 'amount_jsh_department', $amount_jsh_department );
		$this->assign ( 'amount_jsh_department_season', $amount_jsh_department_season );
		$this->assign ( 'amount_client_department', $amount_client_department );
		$this->assign ( 'amount_client_branch', $amount_client_branch );
		$this->assign ( 'amount_jsh_department', $amount_jsh_department );
		$this->assign ( 'amount_cxrj_branch', $amount_cxrj_branch );
		$this->assign ( 'amount_cxrj_department', $amount_cxrj_department );
		$this->assign ( 'date_cxrj', $date_cxrj );
		
		$this->assign ( 'bishu_js_branch', $bishu_js_branch );
		$this->assign ( 'bishu_js_department', $bishu_js_department );
		$this->assign ( 'bishu_jsh_branch', $bishu_jsh_branch );
		$this->assign ( 'bishu_jsh_department', $bishu_jsh_department );
		
		$this->display ( 'report_unit' );
	}
	
	
	
	// ///////////////////////////////////////////////////////////////////////////////////////////////
	//
	//  支行报表EXCEL
	//
	// ///////////////////////////////////////////////////////////////////////////////////////////////
	public function unit_ywl_Excel($start, $end) {
		$type_js = '01'; // 结算量类型
		$type_jsh = "02"; // 结售汇量类型
		
		// 判断本日是在第几季度
		$today=date('Ymd');
		// echo $today;
		$season_start='';
		$season_end='';
		if($today>='20150101' && $today<='20150331'){
			$season_start='20150101';
			$season_end='20150331';
		}
		if($today>='20150401' && $today<='20150630'){
			$season_start='20150401';
			$season_end='20150630';
		}
		if($today>='20150701' && $today<='20150930'){
			$season_start='20150701';
			$season_end='20150930';
		}
		if($today>='20151001' && $today<='20151231'){
			$season_start='20151001';
			$season_end='20151231';
		}
	
	
		// 按支行和事业部维度生成结算量数组
		$result_js = $this->mkywl_js ( $type_js, $start, $end );
		// 按支行和事业部维度生成结售汇量数组
		$result_jsh = $this->mkywl_jsh ( $type_jsh, $start, $end );
		
		// 按支行和事业部维度生成本季结算量数组
		$result_js_season = $this->mkywl_js ( $type_js, $season_start, $season_end);
		// 按支行和事业部维度生成本季结售汇量数组
		$result_jsh_season = $this->mkywl_jsh ( $type_jsh, $season_start, $season_end );
		
		// 从机构信息表中生成所有一级经营单位（一级、直属支行加事业部）的组数
		$unit = $this->mkUnit ();
		// 按支行和事业部维度生成储蓄存款余额数组
		$result_cxrj=$this->mkcxrj();
		//取得经营销任务数据
		$result_task=$this->mktask($end);
		//取得所有客户
		$result_client=$this->clientcount($type_js,$start,$end);
		//存款余额增量日期
		$date_cxrj=$result_cxrj[0][c_date];
	
		// dump($result_client);
			
		// 历遍所有一级经营单 位
		foreach ( $unit as $key => &$u ) {
			$flag_js = 0;
			$flag_jsh = 0;
			$flag_cxrj= 0;
			$flag_client= 0;
			// 历遍所有结算量
			foreach ( $result_js as $r ) {
				// 如果行号与一级经营单位相同的，就将业务笔数和金额加入该数组中
				if ($u ['br_id'] == $r ['p_id']) {
					$u['js_bishu']=$r['bishu'];
					$u['js_amount']=$r['amount'];
					$flag_js = 1;
				}
			}
			// 如果所有结算量行号与一级经营单位都不相同的，就将业务笔数和金额置为0
			if ($flag_js == 0) {
				$u['js_bishu']=0;
				$u['js_amount']=0;
			}
			
			// 历遍所有本季结算量
			foreach ( $result_js_season as $r ) {
				// 如果行号与一级经营单位相同的，就将业务笔数和金额加入该数组中
				if ($u ['br_id'] == $r ['p_id']) {
					$u['js_amount_season']=$r['amount'];
					$flag_js = 1;
				}
			}
			// 如果所有结算量行号与一级经营单位都不相同的，就将业务笔数和金额置为0
			if ($flag_js == 0) {
				$u['js_amount_season']=0;
			}
			
			
			// 历遍所有结售汇量
			foreach ( $result_jsh as $r ) {
				// 如果行号与一级经营单位相同的，就将业务笔数和金额加入该数组中
				if ($u ['br_id'] == $r ['p_id']) {
					$u['jsh_bishu']=$r['bishu'];
					$u['jsh_amount']=$r['amount'];
					$flag_jsh = 1;
				}
			}
			// 如果所有结算量行号与一级经营单位都不相同的，就将业务笔数和金额置为0
			if ($flag_jsh == 0) {
				$u['jsh_bishu']=0;
				$u['jsh_amount']=0;
			}
			
			// 历遍所有本季结售汇量
			foreach ( $result_jsh_season as $r ) {
				// 如果行号与一级经营单位相同的，就将业务笔数和金额加入该数组中
				if ($u ['br_id'] == $r ['p_id']) {
					$u['jsh_amount_season']=$r['amount'];
					$flag_jsh = 1;
				}
			}
			// 如果所有结算量行号与一级经营单位都不相同的，就将业务笔数和金额置为0
			if ($flag_jsh == 0) {
				$u['jsh_amount_season']=0;
			}
				
			// 历遍所有客户量
			foreach ( $result_client as $r ) {
				// 如果行号与一级经营单位相同的，就将客户数加入该数组中
				if ($u ['br_id'] == $r ['p_id']) {
					$u['client_amount']=$r['clientcount'];
					$flag_client = 1;
				}
			}
			// 如果所有结算量行号与一级经营单位都不相同的，就将客户数置为0
			if ($flag_client== 0) {
				$u['client_amount']=0;
			}
				
			// 历遍所有存款余额数组
			foreach ( $result_cxrj as $r ) {
				// 如果行号与一级经营单位相同的，就将存款余额加入该数组中
				if ($u ['br_id'] == $r ['br_id']) {
					$u['cxrj']=$r['c_rj']-$r['b_rj'] ;
					$flag_cxrj= 1;
				}
			}
			// 如果所有结算量行号与一级经营单位都不相同的，就将存款余额置为0
			if ($flag_cxrj == 0) {
				$u['cxrj']=0;
			}
				
			// 历遍所有任务数组
			foreach ( $result_task as $r ) {
				// 如果行号与一级经营单位相同的，就将存款余额加入该数组中
				if ($u ['br_id'] == $r ['unit_id'] && $r['zb_type']==1) {
					$u['js_task']=$r['task'] ;
	
				}
				if ($u ['br_id'] == $r ['unit_id'] && $r['zb_type']==2) {
					$u['jsh_task']=$r['task'] ;
						
				}
				if ($u ['br_id'] == $r ['unit_id'] && $r['zb_type']==3) {
					$u['client_task']=$r['task'] ;
						
				}
				if ($u ['br_id'] == $r ['unit_id'] && $r['zb_type']==4) {
					$u['cxrj_task']=$r['task'] ;
						
				}
					
	
			}
				
				
		}
		//由于用&直接引用了UNIT数组，所以最后的$u要注销
		unset($u);
	
		//dump ($unit);
		//dump ($result_cxrj);
		// 生成支行和事部的业务任务小计
		$task_js_branch = 0;
		$task_js_department = 0;
		$task_jsh_branch = 0;
		$task_jsh_department = 0;
		$task_client_branch=0;
		$task_client_department=0;
		$task_cxrj_branch = 0;
		$task_cxrj_department = 0;
		// 生成支行和事部的业务金额小计
		$amount_js_branch = 0;
		$amount_js_department = 0;
		$amount_jsh_branch = 0;
		$amount_jsh_department = 0;
		$amount_js_branch_season = 0;
		$amount_js_department_season = 0;
		$amount_jsh_branch_season = 0;
		$amount_jsh_department_season = 0;
		$amount_client_branch=0;
		$amount_client_department=0;
		$amount_cxrj_branch = 0;
		$amount_cxrj_department = 0;
		// 生成支行和事部的业务量笔数小计
		$bishu_js_branch = 0;
		$bishu_js_department = 0;
		$bishu_jsh_branch = 0;
		$bishu_jsh_department = 0;
		$bishu_cxrj_branch = 0;
		$bishu_cxrj_department = 0;
		foreach ( $unit as $u ) {
			if ($u ['p_id'] == '08100') {
				$bishu_js_branch = $bishu_js_branch + $u ['js_bishu'];
				$amount_js_branch = $amount_js_branch + $u ['js_amount'];
				$amount_js_branch_season = $amount_js_branch_season + $u ['js_amount_season'];
				$bishu_jsh_branch = $bishu_jsh_branch + $u ['jsh_bishu'];
				$amount_jsh_branch = $amount_jsh_branch + $u ['jsh_amount'];
				$amount_jsh_branch_season = $amount_jsh_branch_season + $u ['jsh_amount_season'];
				$amount_client_branch = $amount_client_branch + $u ['client_amount'];
				$amount_cxrj_branch = $amount_cxrj_branch + $u ['cxrj'];
				$task_js_branch=$task_js_branch+$u['js_task'];
				$task_jsh_branch=$task_jsh_branch+$u['jsh_task'];
				$task_client_branch=$task_client_branch+$u['client_task'];
				$task_cxrj_branch=$task_cxrj_branch+$u['cxrj_task'];
			}
			if ($u ['p_id'] == '08101') {
				$bishu_js_department = $bishu_js_department +$u ['js_bishu'];;
				$amount_js_department = $amount_js_department + $u ['js_amount'];
				$amount_js_department_season = $amount_js_department_season + $u ['js_amount_season'];
				$bishu_jsh_department = $bishu_jsh_department + $u ['jsh_bishu'];
				$amount_jsh_department = $amount_jsh_department + $u ['jsh_amount'];
				$amount_jsh_department_season = $amount_jsh_department_season + $u ['jsh_amount_season'];
				$amount_client_department = $amount_client_department + $u ['client_amount'];
				$amount_cxrj_department = $amount_cxrj_department + $u ['cxrj'];
				$task_js_department=$task_js_department+$u['js_task'];
			}
		}
		
		/** Error reporting */
		error_reporting(E_ALL);
		ini_set('display_errors', TRUE);
		ini_set('display_startup_errors', TRUE);
		date_default_timezone_set('Europe/London');
		
		define('EOL',(PHP_SAPI == 'cli') ? PHP_EOL : '<br />');
		
		/** Include PHPExcel */
		//	require_once  '/jrrc_web/public/PHPExcel_1.8.0_doc/Classes/PHPExcel.php';
			
		// Create new PHPExcel object
		$objPHPExcel = new \PHPExcel();
		
		//输出表头
		$objPHPExcel->getActiveSheet()->mergeCells('A1:A2'); 
		$objPHPExcel->getActiveSheet()->mergeCells('b1:b2'); 
		$objPHPExcel->getActiveSheet()->mergeCells('c1:f2'); 
		$objPHPExcel->getActiveSheet()->mergeCells('g1:j2'); 
		$objPHPExcel->getActiveSheet()->mergeCells('k1:m2'); 
		$objPHPExcel->getActiveSheet()->mergeCells('n1:p2'); 
		
		$objPHPExcel->getActiveSheet()->setCellValue('c1','国际结算量'); 
		$objPHPExcel->getActiveSheet()->setCellValue('g1','结售汇量'); 
		$objPHPExcel->getActiveSheet()->setCellValue('k1','有效对公客户'); 
		$objPHPExcel->getActiveSheet()->setCellValue('n1','储蓄存款日均增量-'.$date_cxrj);
		 
		$objPHPExcel->getActiveSheet()->setCellValue('a3','行号'); 
		$objPHPExcel->getActiveSheet()->setCellValue('b3','经营单位名称'); 
		
		$objPHPExcel->getActiveSheet()->setCellValue('c3','任务'); 
		$objPHPExcel->getActiveSheet()->setCellValue('d3','实绩'); 
		$objPHPExcel->getActiveSheet()->setCellValue('e3','完成率（%）'); 
		$objPHPExcel->getActiveSheet()->setCellValue('f3','本季业绩'); 
		
		$objPHPExcel->getActiveSheet()->setCellValue('g3','任务'); 
		$objPHPExcel->getActiveSheet()->setCellValue('h3','实绩'); 
		$objPHPExcel->getActiveSheet()->setCellValue('i3','完成率（%）'); 
		$objPHPExcel->getActiveSheet()->setCellValue('j3','本季业绩'); 
		
		$objPHPExcel->getActiveSheet()->setCellValue('k3','任务'); 
		$objPHPExcel->getActiveSheet()->setCellValue('l3','实绩'); 
		$objPHPExcel->getActiveSheet()->setCellValue('m3','完成率（%）'); 
		$objPHPExcel->getActiveSheet()->setCellValue('n3','任务'); 
		$objPHPExcel->getActiveSheet()->setCellValue('o3','实绩'); 
		$objPHPExcel->getActiveSheet()->setCellValue('p3','完成率（%）'); 
		
		
		//输出支行
		$row=4;
		foreach($unit as $u){
			if($u['p_id']=='08100'){
				$objPHPExcel->getActiveSheet()->setCellValue('A'.$row,' '.$u['br_id']);
				$objPHPExcel->getActiveSheet()->setCellValue('b'.$row,str_replace('本部','',$u['br_name']));
				
				$objPHPExcel->getActiveSheet()->setCellValue('c'.$row,$u['js_task']);
				$objPHPExcel->getActiveSheet()->setCellValue('d'.$row,$u['js_amount']/10000);
				$objPHPExcel->getActiveSheet()->setCellValue('e'.$row,$u['js_amount']/10000/$u['js_task']*100);
				$objPHPExcel->getActiveSheet()->setCellValue('f'.$row,$u['js_amount_season']/10000);
				
				$objPHPExcel->getActiveSheet()->setCellValue('g'.$row,$u['jsh_task']);
				$objPHPExcel->getActiveSheet()->setCellValue('h'.$row,$u['jsh_amount']/10000);
				$objPHPExcel->getActiveSheet()->setCellValue('i'.$row,$u['jsh_amount']/10000/$u['jsh_task']*100);
				$objPHPExcel->getActiveSheet()->setCellValue('j'.$row,$u['jsh_amount_season']/10000);
				
				$objPHPExcel->getActiveSheet()->setCellValue('k'.$row,$u['client_task']);
				$objPHPExcel->getActiveSheet()->setCellValue('l'.$row,$u['client_amount']);
				$objPHPExcel->getActiveSheet()->setCellValue('m'.$row,$u['client_amount']/$u['client_task']*100);
				
				$objPHPExcel->getActiveSheet()->setCellValue('n'.$row,$u['cxrj_task']);
				$objPHPExcel->getActiveSheet()->setCellValue('o'.$row,$u['cxrj']/10000);
				$objPHPExcel->getActiveSheet()->setCellValue('p'.$row,$u['cxrj']/10000/$u['cxrj_task']*100);			
			$row++;
			}
		}
		
		//输出支行合计
		
		$objPHPExcel->getActiveSheet()->mergeCells('a'.$row.':b'.$row);
		$objPHPExcel->getActiveSheet()->setCellValue('A'.$row,'支行合计');
		//$objPHPExcel->getActiveSheet()->setCellValue('c'.$row,"=sum('c4:c".($row-1)."')");
		
		$objPHPExcel->getActiveSheet()->setCellValue('c'.$row,"=sum(c4:c".($row-1).")");
		$objPHPExcel->getActiveSheet()->setCellValue('d'.$row,"=sum(d4:d".($row-1).")");
		$objPHPExcel->getActiveSheet()->setCellValue('e'.$row,"=d".$row."/c".$row."*100");
		$objPHPExcel->getActiveSheet()->setCellValue('f'.$row,"=sum(f4:f".($row-1).")");
		
		$objPHPExcel->getActiveSheet()->setCellValue('g'.$row,"=sum(g4:g".($row-1).")");
		$objPHPExcel->getActiveSheet()->setCellValue('h'.$row,"=sum(h4:h".($row-1).")");
		$objPHPExcel->getActiveSheet()->setCellValue('i'.$row,"=h".$row."/g".$row."*100");
		$objPHPExcel->getActiveSheet()->setCellValue('j'.$row,"=sum(j4:j".($row-1).")");
		
		
		$objPHPExcel->getActiveSheet()->setCellValue('k'.$row,"=sum(k4:k".($row-1).")");
		$objPHPExcel->getActiveSheet()->setCellValue('l'.$row,"=sum(l4:l".($row-1).")");
		$objPHPExcel->getActiveSheet()->setCellValue('m'.$row,"=l".$row."/k".$row."*100");
		
		$objPHPExcel->getActiveSheet()->setCellValue('n'.$row,"=sum(n4:n".($row-1).")");
		$objPHPExcel->getActiveSheet()->setCellValue('o'.$row,"=sum(o4:o".($row-1).")");
		$objPHPExcel->getActiveSheet()->setCellValue('p'.$row,"=o".$row."/n".$row."*100");

		//输出部室
		$total_branch_row=$row;
		$department_row=$row+1;
		$row=$row+1;
		foreach($unit as $u){
			if($u['p_id']=='08101'){
				$objPHPExcel->getActiveSheet()->setCellValue('A'.$row,' '.$u['br_id']);
				$objPHPExcel->getActiveSheet()->setCellValue('b'.$row,str_replace('国际业务部','代理联社业务',$u['br_name']));
				$objPHPExcel->getActiveSheet()->setCellValue('c'.$row,$u['js_task']);
				$objPHPExcel->getActiveSheet()->setCellValue('d'.$row,$u['js_amount']/10000);
				$objPHPExcel->getActiveSheet()->setCellValue('e'.$row,$u['js_amount']/10000/$u['js_task']*100);
				$objPHPExcel->getActiveSheet()->setCellValue('f'.$row,$u['js_amount_season']/10000);
				
				$objPHPExcel->getActiveSheet()->setCellValue('g'.$row,$u['jsh_task']);
				$objPHPExcel->getActiveSheet()->setCellValue('h'.$row,$u['jsh_amount']/10000);
				//$objPHPExcel->getActiveSheet()->setCellValue('i'.$row,$u['jsh_amount']/10000/$u['jsh_task']*100);
				$objPHPExcel->getActiveSheet()->setCellValue('j'.$row,$u['jsh_amount_season']/10000);
				
				$objPHPExcel->getActiveSheet()->setCellValue('k'.$row,$u['client_task']);
				$objPHPExcel->getActiveSheet()->setCellValue('l'.$row,$u['client_amount']);
				//$objPHPExcel->getActiveSheet()->setCellValue('m'.$row,$u['client_amount']/$u['client_task']*100);
				
				
				$objPHPExcel->getActiveSheet()->setCellValue('n'.$row,$u['cxrj_task']);
				$objPHPExcel->getActiveSheet()->setCellValue('o'.$row,$u['cxrj']/10000);
				//$objPHPExcel->getActiveSheet()->setCellValue('p'.$row,$u['cxrj']/10000/$u['cxrj_task']*100);
				$row++;
			}
		}
		//输出部室合计	
		$objPHPExcel->getActiveSheet()->mergeCells('a'.$row.':b'.$row);
		$objPHPExcel->getActiveSheet()->setCellValue('A'.$row,'部室合计');
		//$objPHPExcel->getActiveSheet()->setCellValue('c'.$row,"=sum('c4:c".($row-1)."')");
		$objPHPExcel->getActiveSheet()->setCellValue('c'.$row,"=sum(c".$department_row.":c".($row-1).")");
		$objPHPExcel->getActiveSheet()->setCellValue('d'.$row,"=sum(d".$department_row.":d".($row-1).")");
		$objPHPExcel->getActiveSheet()->setCellValue('e'.$row,"=d".$row."/c".$row."*100");
		$objPHPExcel->getActiveSheet()->setCellValue('f'.$row,"=sum(f".$department_row.":f".($row-1).")");
		
		$objPHPExcel->getActiveSheet()->setCellValue('g'.$row,"=sum(f".$department_row.":f".($row-1).")");
		$objPHPExcel->getActiveSheet()->setCellValue('h'.$row,"=sum(g".$department_row.":g".($row-1).")");
		//$objPHPExcel->getActiveSheet()->setCellValue('i'.$row,"=h".$row."/g".$row."*100");
		$objPHPExcel->getActiveSheet()->setCellValue('j'.$row,"=sum(j".$department_row.":j".($row-1).")");
		
		$objPHPExcel->getActiveSheet()->setCellValue('k'.$row,"=sum(k".$department_row.":k".($row-1).")");
		$objPHPExcel->getActiveSheet()->setCellValue('l'.$row,"=sum(l".$department_row.":l".($row-1).")");
		//$objPHPExcel->getActiveSheet()->setCellValue('m'.$row,"=l".$row."/k".$row."*100");
		
		$objPHPExcel->getActiveSheet()->setCellValue('n'.$row,"=sum(n".$department_row.":n".($row-1).")");
		$objPHPExcel->getActiveSheet()->setCellValue('o'.$row,"=sum(o".$department_row.":o".($row-1).")");
		//$objPHPExcel->getActiveSheet()->setCellValue('p'.$row,"=o".$row."/n".$row."*100");
		
		
		
		
		//输出全行合计
		$total_depart_row=$row;
		$row=$row+1;
		$objPHPExcel->getActiveSheet()->mergeCells('a'.$row.':b'.$row);
		$objPHPExcel->getActiveSheet()->setCellValue('A'.$row,'全行合计');
		//$objPHPExcel->getActiveSheet()->setCellValue('c'.$row,"=sum('c4:c".($row-1)."')");
		$objPHPExcel->getActiveSheet()->setCellValue('c'.$row,"=c".$total_branch_row."+c".$total_depart_row);
		$objPHPExcel->getActiveSheet()->setCellValue('d'.$row,"=d".$total_branch_row."+d".$total_depart_row);
		$objPHPExcel->getActiveSheet()->setCellValue('e'.$row,"=d".$row."/c".$row."*100");
		$objPHPExcel->getActiveSheet()->setCellValue('f'.$row,"=f".$total_branch_row."+f".$total_depart_row);
		
		
		$objPHPExcel->getActiveSheet()->setCellValue('g'.$row,"=g".$total_branch_row."+g".$total_depart_row);
		$objPHPExcel->getActiveSheet()->setCellValue('h'.$row,"=h".$total_branch_row."+h".$total_depart_row);
		$objPHPExcel->getActiveSheet()->setCellValue('i'.$row,"=h".$row."/g".$row."*100");
		$objPHPExcel->getActiveSheet()->setCellValue('j'.$row,"=j".$total_branch_row."+j".$total_depart_row);
		
		$objPHPExcel->getActiveSheet()->setCellValue('k'.$row,"=k".$total_branch_row."+k".$total_depart_row);
		$objPHPExcel->getActiveSheet()->setCellValue('l'.$row,"=l".$total_branch_row."+l".$total_depart_row);
		$objPHPExcel->getActiveSheet()->setCellValue('m'.$row,"=l".$row."/k".$row."*100");
		
		$objPHPExcel->getActiveSheet()->setCellValue('n'.$row,"=n".$total_branch_row."+n".$total_depart_row);
		$objPHPExcel->getActiveSheet()->setCellValue('o'.$row,"=o".$total_branch_row."+o".$total_depart_row);
		$objPHPExcel->getActiveSheet()->setCellValue('p'.$row,"=o".$row."/n".$row."*100");
		
		
		//格式化数值列
		$objPHPExcel->getActiveSheet()->getStyle('c4:c'.$row)->getNumberFormat()->setFormatCode('#,##');
		$objPHPExcel->getActiveSheet()->getStyle('d4:d'.$row)->getNumberFormat()->setFormatCode('#,##0.00');
		$objPHPExcel->getActiveSheet()->getStyle('e4:e'.$row)->getNumberFormat()->setFormatCode('#,##0.00');
		$objPHPExcel->getActiveSheet()->getStyle('f4:f'.$row)->getNumberFormat()->setFormatCode('#,##0.00');
		
	
		$objPHPExcel->getActiveSheet()->getStyle('g4:g'.$row)->getNumberFormat()->setFormatCode('#,##');	
		$objPHPExcel->getActiveSheet()->getStyle('h4:h'.$row)->getNumberFormat()->setFormatCode('#,##0.00');
		$objPHPExcel->getActiveSheet()->getStyle('i4:i'.$row)->getNumberFormat()->setFormatCode('#,##0.00');
		$objPHPExcel->getActiveSheet()->getStyle('j4:j'.$row)->getNumberFormat()->setFormatCode('#,##0.00');
		
		$objPHPExcel->getActiveSheet()->getStyle('k4:k'.$row)->getNumberFormat()->setFormatCode('#,##');
		$objPHPExcel->getActiveSheet()->getStyle('l4:l'.$row)->getNumberFormat()->setFormatCode('#,##0.00');
		$objPHPExcel->getActiveSheet()->getStyle('m4:m'.$row)->getNumberFormat()->setFormatCode('#,##0.00');
		
		$objPHPExcel->getActiveSheet()->getStyle('n4:n'.$row)->getNumberFormat()->setFormatCode('#,##');
		$objPHPExcel->getActiveSheet()->getStyle('o4:o'.$row)->getNumberFormat()->setFormatCode('#,##0.00');
		$objPHPExcel->getActiveSheet()->getStyle('p4:p'.$row)->getNumberFormat()->setFormatCode('#,##0.00');
	
		//设定列的宽度
		$objPHPExcel->getActiveSheet()->getColumnDimension('b')->setWidth(20);
		$objPHPExcel->getActiveSheet()->getColumnDimension('c')->setWidth(10);
		$objPHPExcel->getActiveSheet()->getColumnDimension('d')->setWidth(10);
		$objPHPExcel->getActiveSheet()->getColumnDimension('e')->setWidth(15);
		$objPHPExcel->getActiveSheet()->getColumnDimension('f')->setWidth(10);
		$objPHPExcel->getActiveSheet()->getColumnDimension('g')->setWidth(10);
		$objPHPExcel->getActiveSheet()->getColumnDimension('h')->setWidth(15);
		$objPHPExcel->getActiveSheet()->getColumnDimension('i')->setWidth(10);
		$objPHPExcel->getActiveSheet()->getColumnDimension('j')->setWidth(10);
		$objPHPExcel->getActiveSheet()->getColumnDimension('k')->setWidth(15);
		$objPHPExcel->getActiveSheet()->getColumnDimension('l')->setWidth(10);
		$objPHPExcel->getActiveSheet()->getColumnDimension('m')->setWidth(10);
		$objPHPExcel->getActiveSheet()->getColumnDimension('n')->setWidth(15);
		
		$styleArray = array(
				'alignment' => array(
						'horizontal' =>'center',
						'vertical'=>'center'
				),
		);
		$objPHPExcel->getActiveSheet()->getStyle("c1:p1")->applyFromArray($styleArray);
		$objPHPExcel->getActiveSheet()->getStyle("a3:p3")->applyFromArray($styleArray);
		$objPHPExcel->getActiveSheet()->getStyle("a1:p".$row)->applyFromArray($styleArray);
		
		$styleArray1 = array(
				'borders' => array(
						'allborders' => array(
								'style' => \PHPExcel_Style_Border::BORDER_THIN,
								'color' => array('argb' => 'gray'),
						),
				),
		);
		
		$objPHPExcel->getActiveSheet()->getStyle('a1:p'.($row))->applyFromArray($styleArray1);
		
		// Rename worksheet
		$objPHPExcel->getActiveSheet()->setTitle('经营单位业务报表');
		// Set active sheet index to the first sheet, so Excel opens this as the first sheet
		$objPHPExcel->setActiveSheetIndex(0);
		// Save Excel 2007 file
		$objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
		$filename='unit_ywl_'.$start.'_'.$end.'.xlsx';
		unlink("../jrrc_web/public/report/excel/".$filename);
		$objWriter->save(str_replace('.php', '.xlsx', "../jrrc_web/public/report/excel/".$filename));
		echo $filename;
		
	
		
	}
	
	
	
	
	// ///////////////////////////////////////////////////////////////////////////////////////////////
	//
	// 查询所有一级经营单位，生成数组$unit
	//
	// ///////////////////////////////////////////////////////////////////////////////////////////////
	public function mkUnit() {
		$Model = M ();
		// 查询所有一级经营单位，生成数组$unit
		$sql_unit = "	
			select p_id,br_id,br_name from jrrc_struction
					where
					( br_id=08108)
						or
					( class=1 and level=2 and status=0 )
						or
					(
					 	class=1 and level=3 and status=0 
						and br_id!=08100 and br_id!=08101 
						and br_id!=08129 and br_id!=08131
					)";
		return $unit = $Model->query ( $sql_unit );
	}
	
	
	// ///////////////////////////////////////////////////////////////////////////////////////////////
	//
	// 生成支行，事业部的业务量
	//
	// ///////////////////////////////////////////////////////////////////////////////////////////////
	public function mkcxrj(){
		$Model=M();
		$sql_cxrj="			
						select br_id,br_name,SUM(c_usdamt)as c_rj,SUM(b_usdamt) as b_rj ,jrrc_struction.p_id ,c_date from
						(
						select 
									jrrc_cx_avg_current.br_id as brr_id ,
									jrrc_cx_avg_current.currency as c_cur,
									jrrc_cx_avg_current.amount as c_amount,
									jrrc_cx_avg_current.usdamt as c_usdamt,
									jrrc_cx_avg_current.rmbamt as c_rmbamt,
									jrrc_cx_avg_base.amount as b_amount,
									jrrc_cx_avg_base.usdamt as b_usdamt,
									jrrc_cx_avg_base.rmbamt as b_rmbamt,
									jrrc_cx_avg_current.date as c_date
									
						FROM
									jrrc_cx_avg_current 
						LEFT OUTER JOIN
									jrrc_cx_avg_base
						ON
									jrrc_cx_avg_base.br_id=jrrc_cx_avg_current.br_id
						AND
									jrrc_cx_avg_base.currency=jrrc_cx_avg_current.currency
						) as totable
						LEFT JOIN jrrc_struction
						ON
									totable.brr_id=jrrc_struction.br_id
						GROUP BY brr_id
					";
		return $result=$Model->query($sql_cxrj);
		
	}
	
	
	
	
	// ///////////////////////////////////////////////////////////////////////////////////////////////
	//
	// 生成支行，事业部的任务量
	//
	// ///////////////////////////////////////////////////////////////////////////////////////////////
	public   function mktask($date){
		$model=M('task');
		$month=substr($date, 4,2);
		
		$q='';
		if($month>='01' && $month<='03'){
			$q='q1';
		}
		if($month>='04' && $month<='06'){
			$q='q2';
		}
		if($month>='07' && $month<='09'){
			$q='q3';
		}
		if($month>='10' && $month<='12'){
			$q='q4';
		}
		
		$sql="select unit_id,zb_type,".$q." as task from jrrc_task";
		return $result=$model->query($sql);
	}
	
	
	// ///////////////////////////////////////////////////////////////////////////////////////////////
	//
	// 生成客户的的业务量（包括结算、结售汇、和贸易融资）
	//
	// ///////////////////////////////////////////////////////////////////////////////////////////////
	public  function mkclientywl($start,$end){
		
		$client_list=$this->mkclient( $start, $end);
		$client_js=$this->mkclientywl_js('01', $start,$end);
		$client_jsh=$this->mkclientywl_jsh('02', $start,$end);
		$client_tf=$this->mkclientywl_tf('03', $start,$end);
		
		
		$total_js_bishu=0;
		$total_js_amount=0;
		$total_jsh_bishu=0;
		$total_jsh_amount=0;
		$total_tf_bishu=0;
		$total_tf_amount=0;
		foreach ($client_list as &$l){
			
			$flag_js=0;
			$flag_jsh=0;
			$flag_tf=0;

			foreach ($client_js as $j){
				if($j['custno']==$l['custno']){
					$l['js_bishu']=$j['bishu'];
					$l['js_amount']=$j['totalamount'];
					$total_js_bishu=$total_js_bishu+$j['bishu'];
					$total_js_amount=$total_js_amount+$j['totalamount'];
					$flag_js=1;
				}
			}
			
			if($flag_js==0){
				$l['js_bishu']=0;
				$l['js_amount']=0;
			}
			
			foreach ($client_jsh as $jsh){
				if($jsh['custno']==$l['custno']){
					$l['jsh_bishu']=$jsh['bishu'];
					$l['jsh_amount']=$jsh['totalamount'];
					$total_jsh_bishu=$total_jsh_bishu+$jsh['bishu'];
					$total_jsh_amount=$total_jsh_amount+$jsh['totalamount'];
					$flag_jsh=1;
				}
			}
				
			if($flag_jsh==0){
				$l['jsh_bishu']=0;
				$l['jsh_amount']=0;
			}
			
			foreach ($client_tf as $tf){
				if($tf['custno']==$l['custno']){
					$l['tf_bishu']=$tf['bishu'];
					$l['tf_amount']=$tf['totalamount'];
					$total_tf_bishu=$total_tf_bishu+$tf['bishu'];
					$total_tf_amount=$total_tf_amount+$tf['totalamount'];
					$flag_tf=1;
				}
			}
			
			if($flag_tf==0){
				$l['tf_bishu']=0;
				$l['tf_amount']=0;
			}
			
		}
		unset($l);
		
		// 取得列的列表
		foreach ( $client_list  as  $key  =>  $row ) {
			//$br [ $key ]  =  $row [ 'p_id' ];
			$volume [ $key ]  =  $row [ 'p_id' ];
			$br [ $key ]  =  $row [ 'br_id' ];
			$edition [ $key ] =  $row [ 'js_amount' ];
		}
		
		// 将数据根据 volume 降序排列，根据 edition 升序排列
		// 把 $data 作为最后一个参数，以通用键排序
		array_multisort (  $volume ,  SORT_STRING , $br,  SORT_STRING , $edition ,  SORT_DESC ,  $client_list );
		
		
		$this->assign('client_list',$client_list);
		$this->assign('total_js_bishu',$total_js_bishu);
		$this->assign('total_js_amount',$total_js_amount);
		$this->assign('total_jsh_bishu',$total_jsh_bishu);
		$this->assign('total_jsh_amount',$total_jsh_amount);
		$this->assign('total_tf_bishu',$total_tf_bishu);
		$this->assign('total_tf_amount',$total_tf_amount);
		
		$this->display('report_client');
	}
	
	
	// ///////////////////////////////////////////////////////////////////////////////////////////////
	//
	// 导出客户EXCEL（包括结算、结售汇、和贸易融资）
	//
	// ///////////////////////////////////////////////////////////////////////////////////////////////
	public  function client_ywl_Excel($start,$end){
	
		$client_list=$this->mkclient($start, $end);
		$client_js=$this->mkclientywl_js('01', $start,$end);
		$client_jsh=$this->mkclientywl_jsh('02', $start,$end);
		$client_tf=$this->mkclientywl_tf('03', $start,$end);
	
	
		$total_js_bishu=0;
		$total_js_amount=0;
		$total_jsh_bishu=0;
		$total_jsh_amount=0;
		$total_tf_bishu=0;
		$total_tf_amount=0;
		foreach ($client_list as &$l){
				
			$flag_js=0;
			$flag_jsh=0;
			$flag_tf=0;
	
			foreach ($client_js as $j){
				if($j['custno']==$l['custno']){
					$l['js_bishu']=$j['bishu'];
					$l['js_amount']=$j['totalamount'];
					$total_js_bishu=$total_js_bishu+$j['bishu'];
					$total_js_amount=$total_js_amount+$j['totalamount'];
					$flag_js=1;
				}
			}
				
			if($flag_js==0){
				$l['js_bishu']=0;
				$l['js_amount']=0;
			}
				
			foreach ($client_jsh as $jsh){
				if($jsh['custno']==$l['custno']){
					$l['jsh_bishu']=$jsh['bishu'];
					$l['jsh_amount']=$jsh['totalamount'];
					$total_jsh_bishu=$total_jsh_bishu+$jsh['bishu'];
					$total_jsh_amount=$total_jsh_amount+$jsh['totalamount'];
					$flag_jsh=1;
				}
			}
	
			if($flag_jsh==0){
				$l['jsh_bishu']=0;
				$l['jsh_amount']=0;
			}
				
			foreach ($client_tf as $tf){
				if($tf['custno']==$l['custno']){
					$l['tf_bishu']=$tf['bishu'];
					$l['tf_amount']=$tf['totalamount'];
					$total_tf_bishu=$total_tf_bishu+$tf['bishu'];
					$total_tf_amount=$total_tf_amount+$tf['totalamount'];
					$flag_tf=1;
				}
			}
				
			if($flag_tf==0){
				$l['tf_bishu']=0;
				$l['tf_amount']=0;
			}
				
		}
		unset($l);
	
		// 取得列的列表
		foreach ( $client_list  as  $key  =>  $row ) {
			//$br [ $key ]  =  $row [ 'p_id' ];
			$volume [ $key ]  =  $row [ 'p_id' ];
			$br [ $key ]  =  $row [ 'br_id' ];
			$edition [ $key ] =  $row [ 'js_amount' ];
		}
	
		// 将数据根据 volume 降序排列，根据 edition 升序排列
		// 把 $data 作为最后一个参数，以通用键排序
		array_multisort (  $volume ,  SORT_STRING , $br,  SORT_STRING , $edition ,  SORT_DESC ,  $client_list );

		/** Error reporting */
		error_reporting(E_ALL);
		ini_set('display_errors', TRUE);
		ini_set('display_startup_errors', TRUE);
		date_default_timezone_set('Europe/London');
		
		define('EOL',(PHP_SAPI == 'cli') ? PHP_EOL : '<br />');
		
		/** Include PHPExcel */
	//	require_once  '/jrrc_web/public/PHPExcel_1.8.0_doc/Classes/PHPExcel.php';
			
		// Create new PHPExcel object
		$objPHPExcel = new \PHPExcel();
			
		$objPHPExcel->getActiveSheet()
		->getColumnDimension('c')
		->setWidth(20);
		$objPHPExcel->getActiveSheet()
		->getColumnDimension('d')
		->setWidth(40);
		$objPHPExcel->getActiveSheet()
		->getColumnDimension('e')
		->setWidth(20);
		$objPHPExcel->getActiveSheet()
		->getColumnDimension('f')
		->setWidth(20);
		$objPHPExcel->getActiveSheet()
		->getColumnDimension('g')
		->setWidth(20);
		$objPHPExcel->getActiveSheet()
		->getColumnDimension('h')
		->setWidth(20);
		$objPHPExcel->getActiveSheet()
		->getColumnDimension('i')
		->setWidth(20);
		$objPHPExcel->getActiveSheet()
		->getColumnDimension('j')
		->setWidth(20);
			
		// Set document properties
		$objPHPExcel->getProperties()->setCreator("Maarten Balliauw")
		->setLastModifiedBy("Maarten Balliauw")
		->setTitle("PHPExcel Test Document")
		->setSubject("PHPExcel Test Document")
		->setDescription("Test document for PHPExcel, generated using PHP classes.")
		->setKeywords("office PHPExcel php")
		->setCategory("Test result file");
		
		//dump($client_list);
		
		$objPHPExcel->setActiveSheetIndex(0)
		->setCellValue('A1', '一级支行')
		->setCellValue('B1', '二级支行')
		->setCellValue('C1', '行名')
		->setCellValue('D1', '客户名称')
		->setCellValue('e1', '国际结算笔数')
		->setCellValue('f1', '国际结算金额(美元)')
		->setCellValue('g1', '结售汇笔数')
		->setCellValue('h1', '结售汇金额(美元)')
		->setCellValue('i1', '贸易融资笔数')
		->setCellValue('j1', '贸易融资金额(美元)')
		;
		// Add some data
		$row=2;
		foreach ($client_list as $v){
			
		$objPHPExcel->setActiveSheetIndex(0)
		->setCellValue('A'.$row, " ".$v['p_id'])
		->setCellValue('B'.$row, " ".$v['br_id'])
		->setCellValue('C'.$row, $v['br_name'])
		->setCellValue('D'.$row, $v['name'])
		->setCellValue('e'.$row, $v['js_bishu'])
		->setCellValue('f'.$row, $v['js_amount'])
		->setCellValue('g'.$row, $v['jsh_bishu'])
		->setCellValue('h'.$row, $v['jsh_amount'])
		->setCellValue('i'.$row, $v['tf_bishu'])	
		->setCellValue('j'.$row , $v['tf_amount']);
			
		$objPHPExcel->setActiveSheetIndex(0)
		->setCellValue('d'.($row+1),"合计")
		->setCellValue('e'.($row+1), $total_js_bishu)
		->setCellValue('f'.($row+1), $total_js_amount)
		->setCellValue('g'.($row+1), $total_jsh_bishu)
		->setCellValue('h'.($row+1), $total_jsh_amount)
		->setCellValue('i'.($row+1), $total_tf_bishu)
		->setCellValue('j'.($row+1),$total_tf_amount);
		
		
		$objPHPExcel->getActiveSheet()
		->getStyle('f'.$row)
		->getNumberFormat()
		->setFormatCode('#,##0.00');
			
		$objPHPExcel->getActiveSheet()
		->getStyle('h'.$row)
		->getNumberFormat()
		->setFormatCode('#,##0.00');
			
		$objPHPExcel->getActiveSheet()
		->getStyle('j'.$row)
		->getNumberFormat()
		->setFormatCode('#,##0.00');
		
		$objPHPExcel->getActiveSheet()
		->getStyle('f'.($row+1))
		->getNumberFormat()
		->setFormatCode('#,##0.00');
			
		$objPHPExcel->getActiveSheet()
		->getStyle('h'.($row+1))
		->getNumberFormat()
		->setFormatCode('#,##0.00');
			
		$objPHPExcel->getActiveSheet()
		->getStyle('j'.($row+1))
		->getNumberFormat()
		->setFormatCode('#,##0.00');
		
		$styleArray = array(
				'borders' => array(
						'allborders' => array(
								'style' => \PHPExcel_Style_Border::BORDER_THIN,
								'color' => array('argb' => 'gray'),
						),
				),
		);
		
		$styleArray_head = array(			
						'alignment' => array(
                    			'horizontal' =>'center'
						),				
		);
				
		$objPHPExcel->getActiveSheet()->getStyle('a1:j'.($row+1))->applyFromArray($styleArray);
		$objPHPExcel->getActiveSheet()->getStyle('a1:j1')->applyFromArray($styleArray_head);		
		$objPHPExcel->getActiveSheet()->getStyle( 'A1:E1')->getFill()->getStartColor()->setARGB('FF808080');
				
			$row++;
		}
	
		// Rename worksheet
		$objPHPExcel->getActiveSheet()->setTitle('公司客户国际业务量');		
		// Set active sheet index to the first sheet, so Excel opens this as the first sheet
		$objPHPExcel->setActiveSheetIndex(0);	
		// Save Excel 2007 file	
		$objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
		$filename='client_ywl_'.$start.'_'.$end.'.xlsx';
		$objWriter->save(str_replace('.php', '.xlsx', "../jrrc_web/public/report/excel/".$filename));
		echo $filename;			
	}
	
	
	
	/*
	 *生成客户结算量，按时间段汇总 
	 *   
	 */
	public function mkclientywl_js($type,$start,$end){
		$Model = M ();
		$sql_client = "
						select totable.p_id ,totable.br_id,totable.br_name,totable.custno,totable.name,
                       			COUNT(jiner)  as bishu, 
                      			 SUM(totable.jiner) as totalamount
					FROM
					(
						(
							select jrrc_struction.p_id, jrrc_struction.br_id,jrrc_struction.br_name,custno,name,usdamt*precent/100 as jiner,er_type
							FROM
							jrrc_ywls LEFT JOIN jrrc_location
							ON jrrc_ywls.custno=jrrc_location.c_id
							LEFT JOIN jrrc_struction
							ON jrrc_location.br_id=jrrc_struction.br_id
							WHERE
										    type=".$type."
									and yw_date>=".$start."
									and yw_date<=".$end."
									AND er_type!='2'
									AND custno!='80042519478'
									and yw_type not in('lr','la','ib')
									
						)
						union ALL
						(
							select jrrc_struction.p_id, jrrc_struction.br_id,jrrc_struction.br_name,custno,name,usdamt as jiner,er_type
							FROM
							jrrc_ywls LEFT JOIN jrrc_struction
							ON
							jrrc_ywls.bl_brno=jrrc_struction.br_id
							WHERE
										  type=".$type."
									and yw_date>=".$start."
									and yw_date<=".$end."
									AND er_type='2'
						)
						union ALL
						(
							select jrrc_struction.p_id, jrrc_struction.br_id,jrrc_struction.br_name,custno,name,usdamt*precent/100 as jiner,er_type
							FROM
							jrrc_ywls LEFT JOIN jrrc_location_extend
							ON jrrc_ywls.custno=jrrc_location_extend.c_id
							LEFT JOIN jrrc_struction
							ON jrrc_location_extend.br_id=jrrc_struction.br_id
							WHERE
									 type=".$type."
									and yw_date>=".$start."
									and yw_date<=".$end."
									AND custno='80042519478'
									and yw_type not in('lr','la','ib')
									
						)
					) as totable
					WHERE totable.er_type!='2'
					GROUP BY totable.custno
					ORDER BY totable.p_id,totable.br_id,totalamount desc
				";
		
		return $result_branch = $Model->query ( $sql_client );
		
	}
	
	public function mkclientywl_jsh($type,$start,$end){
		$Model = M ();
		$sql_client = "
						select totable.p_id ,totable.br_id,totable.br_name,totable.custno,totable.name,
                       			COUNT(jiner)  as bishu,
                      			 SUM(totable.jiner) as totalamount
					FROM
					(
						(
							select jrrc_struction.p_id, jrrc_struction.br_id,jrrc_struction.br_name,custno,name,usdamt*precent/100 as jiner,er_type
							FROM
							jrrc_ywls LEFT JOIN jrrc_location
							ON jrrc_ywls.custno=jrrc_location.c_id
							LEFT JOIN jrrc_struction
							ON jrrc_location.br_id=jrrc_struction.br_id
							WHERE
										    type=".$type."
									and yw_date>=".$start."
									and yw_date<=".$end."
									AND er_type!='2'
									AND custno!='80042519478'
									and yw_type not in('lr','la')
					
						)
						union ALL
						(
							select jrrc_struction.p_id, jrrc_struction.br_id,jrrc_struction.br_name,custno,name,usdamt as jiner,er_type
							FROM
							jrrc_ywls LEFT JOIN jrrc_struction
							ON
							jrrc_ywls.bl_brno=jrrc_struction.br_id
							WHERE
										  type=".$type."
									and yw_date>=".$start."
									and yw_date<=".$end."
									AND er_type='2'
						)
						union ALL
						(
							select jrrc_struction.p_id, jrrc_struction.br_id,jrrc_struction.br_name,custno,name,usdamt*precent/100 as jiner,er_type
							FROM
							jrrc_ywls LEFT JOIN jrrc_location_extend
							ON jrrc_ywls.custno=jrrc_location_extend.c_id
							LEFT JOIN jrrc_struction
							ON jrrc_location_extend.br_id=jrrc_struction.br_id
							WHERE
									 type=".$type."
									and yw_date>=".$start."
									and yw_date<=".$end."
									AND custno='80042519478'
									and yw_type not in('lr','la')
					
						)
					) as totable
					WHERE totable.er_type!='2'
					GROUP BY totable.custno
					ORDER BY totable.p_id,totable.br_id,totalamount desc
				";
	
		return $result_branch = $Model->query ( $sql_client );
	
	}

	
	/*
	 *生成支行结算量，按月汇总
	 *
	 */
	public function mkbranch_ywl_month($br_id,$type,$start,$end){
		$Model = M ();
		$sql_client= "
					select totable.yw_month,totable.p_id ,jrrc_struction.br_name,
                       			COUNT(jiner)  as bishu,
                      			 SUM(totable.jiner) as totalamount
					FROM
					(
						(
							select jrrc_struction.p_id, jrrc_struction.br_id,jrrc_struction.br_name,custno,
				                     name,usdamt*precent/100 as jiner,er_type,
				                     Left(jrrc_ywls.yw_date,6) as yw_month
							FROM
							jrrc_ywls LEFT JOIN jrrc_location
							ON jrrc_ywls.custno=jrrc_location.c_id
							LEFT JOIN jrrc_struction
							ON jrrc_location.br_id=jrrc_struction.br_id
							WHERE
										    type=".$type."
									and yw_date>=".$start."
									and yw_date<=".$end."
									AND er_type!='2'
									AND custno!='80042519478'
									and yw_type not in('lr','la','ib')
			
						)
						union ALL
						(
							select jrrc_struction.p_id, jrrc_struction.br_id,jrrc_struction.br_name,custno,name,
											usdamt as jiner,er_type,
				                            left(jrrc_ywls.yw_date,6) as yw_month
							FROM
							jrrc_ywls LEFT JOIN jrrc_struction
							ON
							jrrc_ywls.bl_brno=jrrc_struction.br_id
							WHERE
										  type=".$type."
									and yw_date>=".$start."
									and yw_date<=".$end."
									AND er_type='2'
						)
						union ALL
						(
							select jrrc_struction.p_id, jrrc_struction.br_id,jrrc_struction.br_name,custno,name,
											usdamt*precent/100 as jiner,er_type,
				                            left(jrrc_ywls.yw_date,6) as yw_month
							FROM
							jrrc_ywls LEFT JOIN jrrc_location_extend
							ON jrrc_ywls.custno=jrrc_location_extend.c_id
							LEFT JOIN jrrc_struction
							ON jrrc_location_extend.br_id=jrrc_struction.br_id
							WHERE
									 type=".$type."
									and yw_date>=".$start."
									and yw_date<=".$end."
									AND custno='80042519478'
									and yw_type not in('lr','la','ib')
			
						)
					) as totable
					left join jrrc_struction 
					on totable.p_id=jrrc_struction.br_id
					WHERE  totable.p_id=".$br_id."
					GROUP BY totable.p_id, totable.yw_month
					ORDER BY totable.p_id,totable.yw_month
				";
	
		//return $result_branch = $Model->query ( $sql_client );
		$result= $Model->query ( $sql_client );
		$yw_type_result=$this->mkclient_ywl_type('unit', $br_id, $type, $start, $end);
		//dump(	$result= $Model->query ( $sql_client ));

		$this->assign('result',$result);
		$this->assign('yw_type_result',$yw_type_result);
		$this->assign('start',$start);
		$this->assign('end',$end);
		$this->assign('level','unit');
		$this->assign('br_id',$br_id);
		$this->assign('type',$type);
		$this->assign('now',NOW_TIME);
		$this->display('branch_month_list');
	
	}
	
	/*
	 *生成部室结算量，按月汇总
	 *
	 */
	public function mkunit_ywl_month($br_id,$type,$start,$end){
		//echo "正在更新功能中，请稍后查询";
		$Model = M ();
		$sql_client= "
					select totable.yw_month,totable.br_id ,totable.br_name,
                       			COUNT(jiner)  as bishu,
                      			 SUM(totable.jiner) as totalamount
					FROM
					(
						(
							select jrrc_struction.p_id, jrrc_struction.br_id,jrrc_struction.br_name,custno,
				                     name,usdamt*precent/100 as jiner,er_type,
				                     Left(jrrc_ywls.yw_date,6) as yw_month
							FROM
							jrrc_ywls LEFT JOIN jrrc_location
							ON jrrc_ywls.custno=jrrc_location.c_id
							LEFT JOIN jrrc_struction
							ON jrrc_location.br_id=jrrc_struction.br_id
							WHERE
										    type=".$type."
									and yw_date>=".$start."
									and yw_date<=".$end."
									AND er_type!='2'
									AND custno!='80042519478'
									and yw_type not in('lr','la','ib')
		
						)
						union ALL
						(
							select jrrc_struction.p_id, jrrc_struction.br_id,jrrc_struction.br_name,custno,name,
											usdamt as jiner,er_type,
				                            left(jrrc_ywls.yw_date,6) as yw_month
							FROM
							jrrc_ywls LEFT JOIN jrrc_struction
							ON
							jrrc_ywls.bl_brno=jrrc_struction.br_id
							WHERE
										  type=".$type."
									and yw_date>=".$start."
									and yw_date<=".$end."
									AND er_type='2'
						)
						union ALL
						(
							select jrrc_struction.p_id, jrrc_struction.br_id,jrrc_struction.br_name,custno,name,
											usdamt*precent/100 as jiner,er_type,
				                            left(jrrc_ywls.yw_date,6) as yw_month
							FROM
							jrrc_ywls LEFT JOIN jrrc_location_extend
							ON jrrc_ywls.custno=jrrc_location_extend.c_id
							LEFT JOIN jrrc_struction
							ON jrrc_location_extend.br_id=jrrc_struction.br_id
							WHERE
									 type=".$type."
									and yw_date>=".$start."
									and yw_date<=".$end."
									AND custno='80042519478'
									and yw_type not in('lr','la','ib')
		
						)
					) as totable
					WHERE  totable.br_id=".$br_id."
					GROUP BY totable.br_id, totable.yw_month
					ORDER BY totable.yw_month
				";
	
		//return $result_branch = $Model->query ( $sql_client );
		$result= $Model->query ( $sql_client );
		$yw_type_result=$this->mkclient_ywl_type('department', $br_id, $type, $start, $end);
		//dump(	$result= $Model->query ( $sql_client ));
		
		$this->assign('result',$result);
		$this->assign('yw_type_result',$yw_type_result);
		$this->assign('start',$start);
		$this->assign('end',$end);
		$this->assign('level','department');
		$this->assign('br_id',$br_id);
		$this->assign('type',$type);
		$this->assign('now',NOW_TIME);
		$this->display('unit_month_list');
		
		
	   // echo "<a href='jrrc_web/home/chart/search'></a>";
	    //$this->redirect('/Home/chart/search');
	}
	
	
	/*
	 *生成客户结算量，按月汇总
	 *
	 */
	public function mkclientywl_js_month($c_id,$type,$start,$end){
		$Model = M ();
		$sql_client= "
						select totable.yw_month,totable.p_id ,totable.br_id,totable.br_name,totable.custno,totable.name,
                       			COUNT(jiner)  as bishu,
                      			 SUM(totable.jiner) as totalamount
					FROM
					(
						(
							select jrrc_struction.p_id, jrrc_struction.br_id,jrrc_struction.br_name,custno,
				                     name,usdamt*precent/100 as jiner,er_type,
				                     Left(jrrc_ywls.yw_date,6) as yw_month
							FROM
							jrrc_ywls LEFT JOIN jrrc_location
							ON jrrc_ywls.custno=jrrc_location.c_id
							LEFT JOIN jrrc_struction
							ON jrrc_location.br_id=jrrc_struction.br_id
							WHERE
										    type=".$type."
									and yw_date>=".$start."
									and yw_date<=".$end."
									AND er_type!='2'
									AND custno!='80042519478'
									and yw_type not in('lr','la','ib')
					
						)
						union ALL
						(
							select jrrc_struction.p_id, jrrc_struction.br_id,jrrc_struction.br_name,custno,name,
											usdamt as jiner,er_type,
				                            left(jrrc_ywls.yw_date,6) as yw_month
							FROM
							jrrc_ywls LEFT JOIN jrrc_struction
							ON
							jrrc_ywls.bl_brno=jrrc_struction.br_id
							WHERE
										  type=".$type."
									and yw_date>=".$start."
									and yw_date<=".$end."
									AND er_type='2'
						)
						union ALL
						(
							select jrrc_struction.p_id, jrrc_struction.br_id,jrrc_struction.br_name,custno,name,
											usdamt*precent/100 as jiner,er_type,
				                            left(jrrc_ywls.yw_date,6) as yw_month
							FROM
							jrrc_ywls LEFT JOIN jrrc_location_extend
							ON jrrc_ywls.custno=jrrc_location_extend.c_id
							LEFT JOIN jrrc_struction
							ON jrrc_location_extend.br_id=jrrc_struction.br_id
							WHERE
									 type=".$type."
									and yw_date>=".$start."
									and yw_date<=".$end."
									AND custno='80042519478'
									and yw_type not in('lr','la','ib')
					
						)
					) as totable
					WHERE totable.er_type!='2' and totable.custno=".$c_id."
					GROUP BY totable.yw_month
					ORDER BY totable.p_id,totable.br_id,totable.custno,totable.yw_month
				";
	
		//return $result_branch = $Model->query ( $sql_client );
	$result= $Model->query ( $sql_client );
	$yw_type_result=$this->mkclient_ywl_type('client', $c_id, $type, $start, $end);
	//dump(	$result= $Model->query ( $sql_client ));
	
	$this->assign('result',$result);
	$this->assign('yw_type_result',$yw_type_result);
	$this->assign('start',$start);
	$this->assign('end',$end);
	$this->assign('level','client');
	$this->assign('br_id',$c_id);
	$this->assign('c_id',$c_id);
	$this->assign('type',$type);
	$this->assign('now',NOW_TIME);

	$this->display('client_month_list');
	
	}
	
	
	
	public function mkclientywl_tf($type,$start,$end){
		$Model = M ();
		$sql_branch = "
						select totable.p_id ,totable.br_id,totable.br_name,totable.custno,totable.name,
                       			COUNT(jiner)  as bishu,
                      			 SUM(totable.jiner) as totalamount
					FROM
					(
						(
							select jrrc_struction.p_id, jrrc_struction.br_id,jrrc_struction.br_name,custno,name,usdamt*precent/100 as jiner,er_type
							FROM
							jrrc_ywls LEFT JOIN jrrc_location
							ON jrrc_ywls.custno=jrrc_location.c_id
							LEFT JOIN jrrc_struction
							ON jrrc_location.br_id=jrrc_struction.br_id
							WHERE
										    type=".$type."
									and yw_date>=".$start."
									and yw_date<=".$end."
									AND er_type!='2'
									AND custno!='80042519478'
												and yw_stat not in ('2B')
									and yw_type not in('lr','la','ib')
					
						)
						union ALL
						(
							select jrrc_struction.p_id, jrrc_struction.br_id,jrrc_struction.br_name,custno,name,usdamt as jiner,er_type
							FROM
							jrrc_ywls LEFT JOIN jrrc_struction
							ON
							jrrc_ywls.bl_brno=jrrc_struction.br_id
							WHERE
										  type=".$type."
									and yw_date>=".$start."
									and yw_date<=".$end."
									AND er_type='2'
						)
						union ALL
						(
							select jrrc_struction.p_id, jrrc_struction.br_id,jrrc_struction.br_name,custno,name,usdamt*precent/100 as jiner,er_type
							FROM
							jrrc_ywls LEFT JOIN jrrc_location_extend
							ON jrrc_ywls.custno=jrrc_location_extend.c_id
							LEFT JOIN jrrc_struction
							ON jrrc_location_extend.br_id=jrrc_struction.br_id
							WHERE
									 type=".$type."
									and yw_date>=".$start."
									and yw_date<=".$end."
									AND custno='80042519478'
											and yw_stat not in ('2B')
									and yw_type not in('lr','la','ib')
					
						)
					) as totable
					WHERE totable.er_type!='2'
					GROUP BY totable.custno
					ORDER BY totable.p_id,totable.br_id,totalamount desc
				";
	
		return $result_branch = $Model->query ( $sql_branch );
	
	}
	
	// ///////////////////////////////////////////////////////////////////////////////////////////////
	//
	// 生成支行，事业部的业务量
	//
	// ///////////////////////////////////////////////////////////////////////////////////////////////
	public function mkywl_js($type, $start, $end) {
		$Model = M ();
		$sql_branch = "
				select totable.p_id, totable.br_id,jrrc_struction.br_name,count(jiner) as bishu,SUM(jiner) as amount
					FROM
					(
						(
							select jrrc_struction.p_id, jrrc_struction.br_id,usdamt*precent/100 as jiner
							FROM
							jrrc_ywls LEFT JOIN jrrc_location
							ON jrrc_ywls.custno=jrrc_location.c_id
							LEFT JOIN jrrc_struction
							ON jrrc_location.br_id=jrrc_struction.br_id
							WHERE 		
										   type=" . $type . "
									and yw_date>='" . $start . "' 
									and yw_date<='" . $end . "' 
									AND er_type!='2'
									AND custno!='80042519478'
									and yw_type not in('ib','la','lr')
									and jrrc_struction.p_id!='08101'
						)
						union ALL
						(
							select jrrc_struction.p_id ,jrrc_struction.br_id,usdamt as jiner
							FROM
							jrrc_ywls LEFT JOIN jrrc_struction
							ON
							jrrc_ywls.bl_brno=jrrc_struction.br_id
							WHERE
										type=" . $type . "
									and yw_date>='" . $start . "' 
									and yw_date<='" . $end . "' 
									AND er_type!='1'
						)
						union ALL
						(
							select jrrc_struction.p_id, jrrc_struction.br_id,usdamt*precent/100 as jiner
							FROM
							jrrc_ywls LEFT JOIN jrrc_location_extend
							ON jrrc_ywls.custno=jrrc_location_extend.c_id
							LEFT JOIN jrrc_struction
							ON jrrc_location_extend.br_id=jrrc_struction.br_id
							WHERE
									type=" . $type . "
									and yw_date>='" . $start . "' 
									and yw_date<='" . $end . "' 
									AND custno='80042519478'
									and yw_type not in('ib','la','lr')
									and jrrc_struction.p_id!='08101'
						)
					) as totable
					LEFT JOIN jrrc_struction
					ON
					totable.p_id=jrrc_struction.br_id
					GROUP BY jrrc_struction.br_id
				";
		$sql_department = "
				select totable.br_id as p_id,jrrc_struction.br_name,count(jiner) as bishu,SUM(jiner) as amount
					FROM
					(
						(
							select jrrc_struction.p_id, jrrc_struction.br_id,usdamt*precent/100 as jiner
							FROM
							jrrc_ywls LEFT JOIN jrrc_location
							ON jrrc_ywls.custno=jrrc_location.c_id
							LEFT JOIN jrrc_struction
							ON jrrc_location.br_id=jrrc_struction.br_id
							WHERE 		type=" . $type . "
									and yw_date>='" . $start . "' 
									and yw_date<='" . $end . "' 
									AND er_type!='2'
									and yw_type not in('ib','la','lr')		
									and jrrc_struction.p_id='08101'
						)
		
						union ALL
		
						(
							select jrrc_struction.p_id ,jrrc_struction.br_id,usdamt as jiner
							FROM
							jrrc_ywls LEFT JOIN jrrc_struction
							ON
							jrrc_ywls.bl_brno=jrrc_struction.br_id
							WHERE
											type=" . $type . "
									and yw_date>='" . $start . "' 
									and yw_date<='" . $end . "' 
									AND er_type!='1'
									and jrrc_struction.p_id='08101'
						)
						union ALL
						(
							select jrrc_struction.p_id, jrrc_struction.br_id,usdamt*precent/100 as jiner
							FROM
							jrrc_ywls LEFT JOIN jrrc_location_extend
							ON jrrc_ywls.custno=jrrc_location_extend.c_id
							LEFT JOIN jrrc_struction
							ON jrrc_location_extend.br_id=jrrc_struction.br_id
							WHERE
												type=" . $type . "
									and yw_date>='" . $start . "' 
									and yw_date<='" . $end . "' 
									and yw_type not in('ib','la','lr')	
									and jrrc_struction.p_id='08101' 
						)
					) as totable
					LEFT JOIN jrrc_struction
					ON
					totable.br_id=jrrc_struction.br_id
					group by totable.br_id
				";
		$result_branch = $Model->query ( $sql_branch );
		$result_department = $Model->query ( $sql_department );
		
		// 合并支行和部门业绩
		$result_all = array_merge ( $result_branch, $result_department );
		return $result_all;
	}
	
	// ///////////////////////////////////////////////////////////////////////////////////////////////
	//
	// 生成支行，事业部的结售汇量
	//
	// ///////////////////////////////////////////////////////////////////////////////////////////////
	public function mkywl_jsh($type, $start, $end) {
		$Model = M ();
		$sql_branch = "
				select totable.p_id, totable.br_id,jrrc_struction.br_name,count(jiner) as bishu,SUM(jiner) as amount
					FROM
					(
						(
							select jrrc_struction.p_id, jrrc_struction.br_id,usdamt*precent/100 as jiner
							FROM
							jrrc_ywls LEFT JOIN jrrc_location
							ON jrrc_ywls.custno=jrrc_location.c_id
							LEFT JOIN jrrc_struction
							ON jrrc_location.br_id=jrrc_struction.br_id
							WHERE
										   type=" . $type . "
									and yw_date>='" . $start . "'
									and yw_date<='" . $end . "'
									AND er_type!='2'
									AND custno!='80042519478'
					
									and jrrc_struction.p_id!='08101'
						)
						union ALL
						(
							select jrrc_struction.p_id ,jrrc_struction.br_id,usdamt as jiner
							FROM
							jrrc_ywls LEFT JOIN jrrc_struction
							ON
							jrrc_ywls.bl_brno=jrrc_struction.br_id
							WHERE
										type=" . $type . "
									and yw_date>='" . $start . "'
									and yw_date<='" . $end . "'
									AND er_type!='1'
						)
						union ALL
						(
							select jrrc_struction.p_id, jrrc_struction.br_id,usdamt*precent/100 as jiner
							FROM
							jrrc_ywls LEFT JOIN jrrc_location_extend
							ON jrrc_ywls.custno=jrrc_location_extend.c_id
							LEFT JOIN jrrc_struction
							ON jrrc_location_extend.br_id=jrrc_struction.br_id
							WHERE
									type=" . $type . "
									and yw_date>='" . $start . "'
									and yw_date<='" . $end . "'
									AND custno='80042519478'
					
									and jrrc_struction.p_id!='08101'
						)
					) as totable
					LEFT JOIN jrrc_struction
					ON
					totable.p_id=jrrc_struction.br_id
					GROUP BY jrrc_struction.br_id
				";
		$sql_department = "
				select totable.br_id as p_id,jrrc_struction.br_name,count(jiner) as bishu,SUM(jiner) as amount
					FROM
					(
						(
							select jrrc_struction.p_id, jrrc_struction.br_id,usdamt*precent/100 as jiner
							FROM
							jrrc_ywls LEFT JOIN jrrc_location
							ON jrrc_ywls.custno=jrrc_location.c_id
							LEFT JOIN jrrc_struction
							ON jrrc_location.br_id=jrrc_struction.br_id
							WHERE 		type=" . $type . "
									and yw_date>='" . $start . "'
									and yw_date<='" . $end . "'
									AND er_type!='2'
						
									and jrrc_struction.p_id='08101'
						)
	
						union ALL
	
						(
							select jrrc_struction.p_id ,jrrc_struction.br_id,usdamt as jiner
							FROM
							jrrc_ywls LEFT JOIN jrrc_struction
							ON
							jrrc_ywls.bl_brno=jrrc_struction.br_id
							WHERE
											type=" . $type . "
									and yw_date>='" . $start . "'
									and yw_date<='" . $end . "'
									AND er_type!='1'
									and jrrc_struction.p_id='08101'
						)
						union ALL
						(
							select jrrc_struction.p_id, jrrc_struction.br_id,usdamt*precent/100 as jiner
							FROM
							jrrc_ywls LEFT JOIN jrrc_location_extend
							ON jrrc_ywls.custno=jrrc_location_extend.c_id
							LEFT JOIN jrrc_struction
							ON jrrc_location_extend.br_id=jrrc_struction.br_id
							WHERE
												type=" . $type . "
									and yw_date>='" . $start . "'
									and yw_date<='" . $end . "'
	
									and jrrc_struction.p_id='08101'
						)
					) as totable
					LEFT JOIN jrrc_struction
					ON
					totable.br_id=jrrc_struction.br_id
					group by totable.br_id
				";
		$result_branch = $Model->query ( $sql_branch );
		$result_department = $Model->query ( $sql_department );
	
		// 合并支行和部门业绩
		$result_all = array_merge ( $result_branch, $result_department );
		return $result_all;
	}
	
	
	
	public function mkclient($start,$end){
		$Model=M();
		$sql="
		select totable.p_id ,totable.br_id,totable.br_name,totable.custno,totable.name
                       			
					FROM
					(
						(
							select jrrc_struction.p_id, jrrc_struction.br_id,jrrc_struction.br_name,custno,name,er_type
							FROM
							jrrc_ywls LEFT JOIN jrrc_location
							ON jrrc_ywls.custno=jrrc_location.c_id
							LEFT JOIN jrrc_struction
							ON jrrc_location.br_id=jrrc_struction.br_id
							WHERE	
									 yw_date>=".$start."
									and yw_date<=".$end."
									AND er_type!='2'
									AND er_type!='3'
									AND custno!='80042519478'
									and yw_type not in('lr','la','ib')
									
						)
			
						union ALL
						(
							select jrrc_struction.p_id, jrrc_struction.br_id,jrrc_struction.br_name,custno,name,er_type
							FROM
							jrrc_ywls LEFT JOIN jrrc_location_extend
							ON jrrc_ywls.custno=jrrc_location_extend.c_id
							LEFT JOIN jrrc_struction
							ON jrrc_location_extend.br_id=jrrc_struction.br_id
							WHERE
									
									 yw_date>=".$start."
									and yw_date<=".$end."
									AND custno='80042519478'
									and yw_type not in('lr','la','ib')					
						)
					) as totable
					WHERE totable.er_type!='2'
					GROUP BY totable.custno
					ORDER BY totable.br_id
				";
		$result_all = $Model->query ( $sql );
	
		//dump($result_all);
		return $result_all;
			
	}
	
	
	public function clientcount($type,$start,$end){
		$Model=M();
	
		$sql_client_branch="
			select totable.p_id, totable.br_id,jrrc_struction.br_name,count(distinct totable.custno) as clientcount
					FROM
					(
						(
							select jrrc_struction.p_id, jrrc_struction.br_id,usdamt*precent/100 as jiner,jrrc_ywls.custno
							FROM
							jrrc_ywls LEFT JOIN jrrc_location
							ON jrrc_ywls.custno=jrrc_location.c_id
							LEFT JOIN jrrc_struction
							ON jrrc_location.br_id=jrrc_struction.br_id
							WHERE
										   type=" . $type . "
									and yw_date>='" . $start . "'
									and yw_date<='" . $end . "'
									AND er_type!='2'
									AND custno!='80042519478'
									and jrrc_struction.p_id!='08101'
						)
	
						union ALL
						(
							select jrrc_struction.p_id, jrrc_struction.br_id,usdamt*precent/100 as jiner,jrrc_ywls.custno
							FROM
							jrrc_ywls LEFT JOIN jrrc_location_extend
							ON jrrc_ywls.custno=jrrc_location_extend.c_id
							LEFT JOIN jrrc_struction
							ON jrrc_location_extend.br_id=jrrc_struction.br_id
							WHERE
									type=" . $type . "
									and yw_date>='" . $start . "'
									and yw_date<='" . $end . "'
									AND custno='80042519478'
									and jrrc_struction.p_id!='08101'
						)
					) as totable
					LEFT JOIN jrrc_struction
					ON
					totable.p_id=jrrc_struction.br_id
					GROUP BY jrrc_struction.br_id
				";
	
	
		$sql_client_department="
			select totable.br_id as p_id,jrrc_struction.br_name,count(distinct totable.custno) as clientcount
					FROM
					(
						(
							select jrrc_struction.p_id, jrrc_struction.br_id,usdamt*precent/100 as jiner,jrrc_ywls.custno
							FROM
							jrrc_ywls LEFT JOIN jrrc_location
							ON jrrc_ywls.custno=jrrc_location.c_id
							LEFT JOIN jrrc_struction
							ON jrrc_location.br_id=jrrc_struction.br_id
							WHERE 		type=" . $type . "
									and yw_date>='" . $start . "'
									and yw_date<='" . $end . "'
									AND er_type!='2'
									and jrrc_struction.p_id='08101'
						)
	
		
						union ALL
						(
							select jrrc_struction.p_id, jrrc_struction.br_id,usdamt*precent/100 as jiner,jrrc_ywls.custno
							FROM
							jrrc_ywls LEFT JOIN jrrc_location_extend
							ON jrrc_ywls.custno=jrrc_location_extend.c_id
							LEFT JOIN jrrc_struction
							ON jrrc_location_extend.br_id=jrrc_struction.br_id
							WHERE
									type=" . $type . "
									and yw_date>='" . $start . "'
									and yw_date<='" . $end . "'
									and jrrc_struction.p_id='08101'
						)
					) as totable
					LEFT JOIN jrrc_struction
					ON
					totable.br_id=jrrc_struction.br_id
					group by jrrc_struction.br_id
				";
		$result_client_branch = $Model->query ( $sql_client_branch );
		$result_client_department = $Model->query ( $sql_client_department );
	
		// 合并支行和部门客户
		$result_all = array_merge ( $result_client_branch, $result_client_department );
		//dump($result_all);
		return $result_all;
	}
	
	/*
	 * 客户业务量分业务种类
	 *   
	 *   
	 *   */
	public  function mkclient_ywl_type($level,$id,$type,$start,$end){
		$condition='';
		if($level=='unit'){
			$condition='  totable.p_id='.$id;
		}else{
			$condition='  totable.custno='.$id;
		}
		if($level=='department'){
			$condition='  totable.br_id='.$id;
		}
		$Model = M ();
		$sql_client = "
						select  totable.yw_name,totable.br_id,totable.custno,
                       			COUNT(jiner)  as bishu,
                      			 SUM(totable.jiner) as totalamount
					FROM
					(
						(
							select jrrc_struction.p_id, jrrc_struction.br_id,jrrc_struction.br_name,custno,yw_name,name,usdamt*precent/100 as jiner,er_type
							FROM
							jrrc_ywls LEFT JOIN jrrc_location
							ON jrrc_ywls.custno=jrrc_location.c_id
							LEFT JOIN jrrc_struction
							ON jrrc_location.br_id=jrrc_struction.br_id
							WHERE
										    type=".$type."
									and yw_date>=".$start."
									and yw_date<=".$end."
									AND er_type!='2'
									AND custno!='80042519478'
					
						)
						union ALL
						(
							select jrrc_struction.p_id, jrrc_struction.br_id,jrrc_struction.br_name,custno,yw_name,name,usdamt as jiner,er_type
							FROM
							jrrc_ywls LEFT JOIN jrrc_struction
							ON
							jrrc_ywls.bl_brno=jrrc_struction.br_id
							WHERE
										type=".$type."
									and yw_date>=".$start."
									and yw_date<=".$end."
									AND er_type='2'
						)
						union ALL
						(
							select jrrc_struction.p_id, jrrc_struction.br_id,jrrc_struction.br_name,custno,yw_name,name,usdamt*precent/100 as jiner,er_type
							FROM
							jrrc_ywls LEFT JOIN jrrc_location_extend
							ON jrrc_ywls.custno=jrrc_location_extend.c_id
							LEFT JOIN jrrc_struction
							ON jrrc_location_extend.br_id=jrrc_struction.br_id
							WHERE
									 type=".$type."
									and yw_date>=".$start."
									and yw_date<=".$end."
									AND custno='80042519478'
					
						)
					) as totable
					WHERE 
					 ".$condition." 
					GROUP BY totable.yw_name
					ORDER BY totalamount desc
				";
		//echo $sql_client;
		return $result_branch = $Model->query ( $sql_client );
		//dump( $result_branch = $Model->query ( $sql_client ));
	
	}
	
}