<?php

namespace Home\Controller;

/*
 * 报表控制器
 */
use Think\Controller;
use Think\Model;
use Org\Util\Date;

require ("../Jrrc_web/Public/Classes/PHPExcel.php");
// 导入绘图类文件
require_once ("../jrrc_web/public/jpgraph/jpgraph/jpgraph.php");
require_once ("../jrrc_web/public/jpgraph/jpgraph/jpgraph_bar.php"); // 柱状图
require_once ("../jrrc_web/public/jpgraph/jpgraph/jpgraph_pie.php");
require_once ("../jrrc_web/public/jpgraph/jpgraph/jpgraph_pie3d.php");
class Report1Controller extends Controller {
	public function index() {
		$this->display ( 'index' );
	}
	
	/**
	 * 确定本季日期区间
	 */
	public function local_season($date) {
		$month = substr ( $date, 4, 2 );
		$season = array ();
		switch ($month) {
			case $month >= '01' && $month <= '03' :
				{
					$season ['startmonthday'] = '0101';
					$season ['endmonthday'] = '0331';
					break;
				}
			case $month >= '04' && $month <= '06' :
				{
					$season ['startmonthday'] = '0401';
					$season ['endmonthday'] = '0630';
					break;
				}
			case $month >= '07' && $month <= '09' :
				{
					$season ['startmonthday'] = '0701';
					$season ['endmonthday'] = '0930';
					break;
				}
			case $month >= '10' && $month <= '12' :
				{
					$season ['startmonthday'] = '1001';
					$season ['endmonthday'] = '1231';
					break;
				}
		}
		// dump($season);
		return $season;
	}
	
	// 生成一个月份的数组
	public function MakeMonthList($startDay, $endDay) {
		// 取得开始，结束的年、月
		$Year = substr ( $startDay, 0, 4 );
		$Month = substr ( $startDay, 4, 2 );
		
		$endYearMonth = substr ( $endDay, 0, 6 );
		$YearMonth = $Year . $Month;
		// echo $YearMonth;
		$monthList = array ();
		
		if (strcmp ( $YearMonth, $endYearMonth ) == 0) {
			array_push ( $monthList, $YearMonth );
			// dump($monthList);
			return $monthList;
		}
		
		while ( $YearMonth - $endYearMonth < 0 ) {
			$YearMonth = $Year . $Month;
			// echo $YearMonth;
			array_push ( $monthList, $YearMonth );
			if ($Month < 12) {
				$Month = str_pad ( ($Month + 1), 2, "0", STR_PAD_LEFT );
			} else {
				$Month = str_pad ( 1, 2, "0", STR_PAD_LEFT );
				$Year = $Year + 1;
			}
		}
		
		// dump($monthList);
		return $monthList;
	}
	
	// 生成机构分类业务数据
	public function get_unit_js_typelist($unit, $start, $end, $type) {
		$model = M ( 'ywls_fixed' );
		$condition ['upbranch'] = $unit;
		$condition ['type'] = $type;
		if ($type == '01') {
			// not in('lr','la','ib')
			$condition ['yw_type'] = array (
					array (
							'neq',
							'lr' 
					),
					array (
							'neq',
							'la' 
					),
					array (
							'neq',
							'ib' 
					) 
			);
		}
		
		$condition ['yw_date'] = array (
				array (
						'egt',
						$start 
				),
				array (
						'elt',
						$end 
				) 
		);
		$field = "sum(usdamt*precent/100) as jiner,count(type) as timers,yw_name,yw_type";
		$result = $model->field ( $field )->where ( $condition )->group ( 'yw_type' )->select ();
		// dump($result);
		return $result;
	}
	
	// 生成机构的月度经营数据
	public function get_unit_js_monthlist($unit, $start, $end, $type) {
		// 以查询时间段生成一个月份的数组
		$monthlist = $this->MakeMonthList ( $start, $end );
		$model = M ( 'ywls_fixed' );
		$condition ['upbranch'] = $unit;
		$condition ['type'] = $type;
		if ($type == '01') {
			// not in('lr','la','ib')
			$condition ['yw_type'] = array (
					array (
							'neq',
							'lr' 
					),
					array (
							'neq',
							'la' 
					),
					array (
							'neq',
							'ib' 
					) 
			);
		}
		
		$condition ['yw_date'] = array (
				array (
						'egt',
						$start 
				),
				array (
						'elt',
						$end 
				) 
		);
		$fields = "LEFT(yw_date,6) as month,count(type) as times,sum(usdamt*precent/100) as jiner,upbranch";
		$result = $model->field ( $fields )->where ( $condition )->group ( 'month' )->select ();
		// return $result;
		$arrayMonthList = array ();
		
		foreach ( $monthlist as $key => $m ) {
			$flag = 0;
			foreach ( $result as $r ) {
				if ($m == $r ['month']) {
					array_push ( $arrayMonthList, $r );
					$flag = 1;
				}
			}
			if ($flag == 0) {
				$arrayMonthList [$key] ['month'] = $m;
				$arrayMonthList [$key] ['times'] = 0;
				$arrayMonthList [$key] ['jiner'] = 0;
			}
		}
		
		return $arrayMonthList;
	}
	
	/**
	 * 生成客户业务分类明细数组
	 * 
	 * @param unknown $client_id        	
	 * @param unknown $start        	
	 * @param unknown $end        	
	 * @param unknown $type        	
	 * @return unknown
	 */
	public function get_client_js_typelist($client_id, $start, $end, $type) {
		$model = M ( 'ywls_fixed' );
		$condition ['custno'] = $client_id;
		$condition ['type'] = $type;
		if ($type == '01') {
			// not in('lr','la','ib')
			$condition ['yw_type'] = array (
					array (
							'neq',
							'lr' 
					),
					array (
							'neq',
							'la' 
					),
					array (
							'neq',
							'ib' 
					) 
			);
		}
		
		$condition ['yw_date'] = array (
				array (
						'egt',
						$start 
				),
				array (
						'elt',
						$end 
				) 
		);
		$field = "sum(usdamt*precent/100) as jiner,count(type) as timers,yw_name,yw_type";
		$result = $model->field ( $field )->where ( $condition )->group ( 'yw_type' )->select ();
		// dump($result);
		return $result;
	}
	
	/**
	 * 生成客户结算量分月明细数组
	 * 
	 * @param unknown $client_id        	
	 * @param unknown $start        	
	 * @param unknown $end        	
	 */
	public function get_client_js_monthlist($client_id, $start, $end, $type) {
		// 以查询时间段生成一个月份的数组
		$monthlist = $this->MakeMonthList ( $start, $end );
		$model = M ( 'ywls_fixed' );
		$condition ['custno'] = $client_id;
		$condition ['type'] = $type;
		if ($type == '01') {
			// not in('lr','la','ib')
			$condition ['yw_type'] = array (
					array (
							'neq',
							'lr' 
					),
					array (
							'neq',
							'la' 
					),
					array (
							'neq',
							'ib' 
					) 
			);
		}
		
		$condition ['yw_date'] = array (
				array (
						'egt',
						$start 
				),
				array (
						'elt',
						$end 
				) 
		);
		$fields = "LEFT(yw_date,6) as month,count(type) as times,sum(usdamt*precent/100) as jiner,custno";
		$result = $model->field ( $fields )->where ( $condition )->group ( 'month' )->select ();
		$arrayMonthList = array ();
		
		foreach ( $monthlist as $key => $m ) {
			$flag = 0;
			foreach ( $result as $r ) {
				if ($m == $r ['month']) {
					array_push ( $arrayMonthList, $r );
					$flag = 1;
				}
			}
			if ($flag == 0) {
				$arrayMonthList [$key] ['month'] = $m;
				$arrayMonthList [$key] ['times'] = 0;
				$arrayMonthList [$key] ['jiner'] = 0;
			}
		}
		
		return $arrayMonthList;
	}
	
	/**
	 * 生成全行业务量分类明细数组
	 *
	 * @param unknown $unit        	
	 * @param unknown $start        	
	 * @param unknown $end        	
	 * @param unknown $type        	
	 * @return unknown
	 */
	public function get_All_typelist($start, $end) {
		$model = M ( 'ywls_fixed' );
		
		$condition ['type'] = '01';
		
		$condition ['yw_date'] = array (
				array (
						'egt',
						$start 
				),
				array (
						'elt',
						$end 
				) 
		);
		$field = "sum(usdamt*precent/100) as jiner,count(type) as timers,yw_name,yw_type";
		$result = $model->field ( $field )->where ( $condition )->group ( 'yw_type' )->select ();
		return $result;
	}
	
	/**
	 * 生成全行业务量分月明细数组
	 */
	public function get_All_Report_Month($start, $end) {
		// 以查询时间段生成一个月份的数组
		$monthlist = $this->MakeMonthList ( $start, $end );
		$model = M ( 'ywls_fixed' );
		$condition ['type'] = '01';
		$condition ['yw_type'] = array (
				array (
						'neq',
						'lr' 
				),
				array (
						'neq',
						'la' 
				),
				array (
						'neq',
						'ib' 
				) 
		);
		
		$condition ['yw_date'] = array (
				array (
						'egt',
						$start 
				),
				array (
						'elt',
						$end 
				) 
		);
		$fields = "LEFT(yw_date,6) as month,count(type) as times,sum(usdamt*precent/100) as jiner";
		$result = $model->field ( $fields )->where ( $condition )->group ( 'month' )->select ();
		// return $result;
		$arrayMonthList = array ();
		
		foreach ( $monthlist as $key => $m ) {
			$flag = 0;
			foreach ( $result as $r ) {
				if ($m == $r ['month']) {
					array_push ( $arrayMonthList, $r );
					$flag = 1;
				}
			}
			if ($flag == 0) {
				$arrayMonthList [$key] ['month'] = $m;
				$arrayMonthList [$key] ['times'] = 0;
				$arrayMonthList [$key] ['jiner'] = 0;
			}
		}
		
		return $arrayMonthList;
	}
	
	/**
	 * 显示全行业务量分月明细
	 */
	public function show_All_Report_Month($start, $end) {
		$result = $this->get_All_Report_Month ( $start, $end );
		$typeResult = $this->get_All_typelist ( $start, $end );
		
		$this->assign ( 'start', $start );
		$this->assign ( 'end', $end );
		$this->assign ( 'result', $result );
		$this->assign ( 'TypeResult', $typeResult );
		$this->display ( 'all_month_list' );
	}
	
	// 显示机构业务分月明细
	public function show_Unit_Report_Month($unit, $start, $end, $type) {
		$result = $this->get_unit_js_monthlist ( $unit, $start, $end, $type );
		$typeResult = $this->get_unit_js_typelist ( $unit, $start, $end, $type );
		$this->assign ( 'br_id', $unit );
		$this->assign ( 'start', $start );
		$this->assign ( 'end', $end );
		$this->assign ( 'type', $type );
		$this->assign ( 'result', $result );
		$this->assign ( 'TypeResult', $typeResult );
		$this->display ( 'unit_month_list' );
	}
	
	// 显示机构业务量任务表
	public function show_Unit_Report($start, $end) {
		$report = $this->Unit_Report ( $start, $end );
		$date_cxrj = M ( 'cx_avg_current' )->field ( 'date' )->limit ( 1 )->select ();
		// 计算支行合计
		$branch_sum = array ();
		$department_sum = array ();
		foreach ( $report as $r ) {
			if ($r ['Head_office'] == '08100') {
				$branch_sum ['task_amount_js_sum'] = $branch_sum ['task_amount_js_sum'] + $r ['task_amount_js'];
				$branch_sum ['task_amount_jsh_sum'] = $branch_sum ['task_amount_jsh_sum'] + $r ['task_amount_jsh'];
				$branch_sum ['task_amount_client_sum'] = $branch_sum ['task_amount_client_sum'] + $r ['task_amount_client'];
				$branch_sum ['task_amount_cx_sum'] = $branch_sum ['task_amount_cx_sum'] + $r ['task_amount_cx'];
				
				$branch_sum ['amount_js_sum'] = $branch_sum ['amount_js_sum'] + $r ['amount_js'];
				$branch_sum ['amount_jsh_sum'] = $branch_sum ['amount_jsh_sum'] + $r ['amount_jsh'];
				$branch_sum ['amount_client_sum'] = $branch_sum ['amount_client_sum'] + $r ['amount_client'];
				$branch_sum ['amount_cx_sum'] = $branch_sum ['amount_cx_sum'] + $r ['amount_cx'];
				
				$branch_sum ['js_complete_rate_sum'] = $branch_sum ['amount_js_sum'] / $branch_sum ['task_amount_js_sum'] * 100;
				$branch_sum ['jsh_complete_rate_sum'] = $branch_sum ['amount_jsh_sum'] / $branch_sum ['task_amount_jsh_sum'] * 100;
				$branch_sum ['client_complete_rate_sum'] = $branch_sum ['amount_client_sum'] / $branch_sum ['task_amount_client_sum'] * 100;
				$branch_sum ['cx_complete_rate_sum'] = $branch_sum ['amount_cx_sum'] / $branch_sum ['task_amount_cx_sum'] * 100;
				
				$branch_sum ['amount_js_season_sum'] = $branch_sum ['amount_js_season_sum'] + $r ['season_amount_js'];
				$branch_sum ['amount_jsh_season_sum'] = $branch_sum ['amount_jsh_season_sum'] + $r ['season_amount_jsh'];
				$branch_sum ['amount_client_season_sum'] = $branch_sum ['amount_client_season_sum'] + $r ['season_amount_client'];
				$branch_sum ['amount_cx_season_sum'] = $branch_sum ['amount_cx_season_sum'] + $r ['season_amount_cx'];
			} else {
				
				$department_sum ['task_amount_js_sum'] = $department_sum ['task_amount_js_sum'] + $r ['task_amount_js'];
				$department_sum ['task_amount_jsh_sum'] = $department_sum ['task_amount_jsh_sum'] + $r ['task_amount_jsh'];
				$department_sum ['task_amount_client_sum'] = $department_sum ['task_amount_client_sum'] + $r ['task_amount_client'];
				$department_sum ['task_amount_cx_sum'] = $department_sum ['task_amount_cx_sum'] + $r ['task_amount_cx'];
				
				$department_sum ['amount_js_sum'] = $department_sum ['amount_js_sum'] + $r ['amount_js'];
				$department_sum ['amount_jsh_sum'] = $department_sum ['amount_jsh_sum'] + $r ['amount_jsh'];
				$department_sum ['amount_client_sum'] = $department_sum ['amount_client_sum'] + $r ['amount_client'];
				$department_sum ['amount_cx_sum'] = $department_sum ['amount_cx_sum'] + $r ['amount_cx'];
				
				$department_sum ['js_complete_rate_sum'] = $department_sum ['amount_js_sum'] / $department_sum ['task_amount_js_sum'] * 100;
				$department_sum ['jsh_complete_rate_sum'] = $department_sum ['amount_jsh_sum'] / $department_sum ['task_amount_jsh_sum'] * 100;
				$department_sum ['client_complete_rate_sum'] = $department_sum ['amount_client_sum'] / $department_sum ['task_amount_client_sum'] * 100;
				$department_sum ['cx_complete_rate_sum'] = $department_sum ['amount_cx_sum'] / $department_sum ['task_amount_cx_sum'] * 100;
				
				$department_sum ['amount_js_season_sum'] = $department_sum ['amount_js_season_sum'] + $r ['season_amount_js'];
				$department_sum ['amount_jsh_season_sum'] = $department_sum ['amount_jsh_season_sum'] + $r ['season_amount_jsh'];
				$department_sum ['amount_client_season_sum'] = $department_sum ['amount_client_season_sum'] + $r ['season_amount_client'];
				$department_sum ['amount_cx_season_sum'] = $department_sum ['amount_cx_season_sum'] + $r ['season_amount_cx'];
			}
		}
		
		$total = array ();
		$total ['amount_js_sum'] = $branch_sum ['amount_js_sum'] + $department_sum ['amount_js_sum'];
		$total ['amount_jsh_sum'] = $branch_sum ['amount_jsh_sum'] + $department_sum ['amount_jsh_sum'];
		$total ['amount_client_sum'] = $branch_sum ['amount_client_sum'] + $department_sum ['amount_client_sum'];
		$total ['amount_cx_sum'] = $branch_sum ['amount_cx_sum'] + $department_sum ['amount_cx_sum'];
		
		$total ['js_complete_rate_sum'] = $total ['amount_js_sum'] / $branch_sum ['task_amount_js_sum'] * 100;
		$total ['jsh_complete_rate_sum'] = $total ['amount_jsh_sum'] / $branch_sum ['task_amount_jsh_sum'] * 100;
		$total ['client_complete_rate_sum'] = $total ['amount_client_sum'] / $branch_sum ['task_amount_client_sum'] * 100;
		$total ['cx_complete_rate_sum'] = $total ['amount_cx_sum'] / $branch_sum ['task_amount_cx_sum'] * 100;
		
		$total ['amount_js_season_sum'] = $branch_sum ['amount_js_season_sum'] + $department_sum ['amount_js_season_sum'];
		$total ['amount_jsh_season_sum'] = $branch_sum ['amount_jsh_season_sum'] + $department_sum ['amount_jsh_season_sum'];
		
		$this->assign ( "branch_sum", $branch_sum );
		$this->assign ( "department_sum", $department_sum );
		$this->assign ( "total", $total );
		$this->assign ( "report", $report );
		$this->assign ( 'date_cxrj', $date_cxrj );
		$this->display ( 'show_unit_report' );
	}
	
	/**
	 * 显示对公客户分月明细和
	 * 
	 * @param unknown $client_id        	
	 * @param unknown $start        	
	 * @param unknown $end        	
	 */
	public function show_Client_Report_Month($client_id, $start, $end, $type) {
		$result = $this->get_client_js_monthlist ( $client_id, $start, $end, $type );
		$typeResult = $this->get_client_js_typelist ( $client_id, $start, $end, $type );
		$this->assign ( 'client_id', $client_id );
		$this->assign ( 'start', $start );
		$this->assign ( 'end', $end );
		$this->assign ( 'type', $type );
		$this->assign ( 'result', $result );
		$this->assign ( 'TypeResult', $typeResult );
		$this->display ( 'client_month_list' );
	}
	
	// 显示对公客户业务量表
	public function show_Client_Report($start, $end) {
		$result = $this->Client_Report ( $start, $end );
		$js_times_total = null;
		$js_jiner_total = null;
		$jsh_times_total = null;
		$jsh_jiner_total = null;
		$tf_times_total = null;
		$tf_jiner_total = null;
		// dump($result);
		foreach ( $result as $r ) {
			$js_times_total = $js_times_total + $r ['js_times'];
			$js_jiner_total = $js_jiner_total + $r ['js_jiner'];
			$jsh_times_total = $jsh_times_total + $r ['jsh_times'];
			$jsh_jiner_total = $jsh_jiner_total + $r ['jsh_jiner'];
			$tf_times_total = $tf_times_total + $r ['tf_times'];
			$tf_jiner_total = $tf_jiner_total + $r ['tf_jiner'];
		}
		$total = array (
				'js_times_total' => $js_times_total,
				'js_jiner_total' => $js_jiner_total,
				'jsh_times_total' => $jsh_times_total,
				'jsh_jiner_total' => $jsh_jiner_total,
				'tf_times_total' => $tf_times_total,
				'tf_jiner_total' => $tf_jiner_total 
		)
		;
		// dump($total);
		$this->assign ( "client_yw", $result );
		$this->assign ( "total", $total );
		$this->display ( 'show_client_report' );
	}
	
	// 生成机构业务量分类饼图
	public function Unit_Type_Pie($unit, $start, $end, $type) {
		$result_type = $this->get_unit_js_typelist ( $unit, $start, $end, $type );
		$title = "国际结算业务量构成";
		$yw_type = array ();
		$amount = array ();
		foreach ( $result_type as $v ) {
			array_push ( $yw_type, iconv ( 'utf-8', 'gb2312', substr ( $v ['yw_name'], 0, 27 ) ) );
			array_push ( $amount, $v ['jiner'] / 10000 );
		}
		
		$this->pie ( $title, $yw_type, $amount );
	}
	
	// 生成机构业务量分月柱状图
	public function Unit_Barplot($unit, $start, $end, $type) {
		$result = $this->get_unit_js_monthlist ( $unit, $start, $end, $type );
		
		// dump($result);
		$unit_name = M ( 'struction' )->field ( 'br_name' )->where ( 'br_id=' . $unit )->limit ( 1 )->select ();
		$title = str_replace ( '本部', '', $unit_name [0] ['br_name'] );
		$title = $title . "：国际结算量分月明细图";
		$time = array (); // 时间轴，横轴
		$amount = array (); // 业务量，纵轴
		foreach ( $result as $v ) {
			array_push ( $time, $v ['month'] );
			array_push ( $amount, $v ['jiner'] / 10000 );
		}
		
		$yw_type = '';
		if ($type == '01') {
			$yw_type = '国际结算量';
		}
		if ($type == '02') {
			$yw_type = '结售汇量';
		}
		if ($type == '03') {
			$yw_type = '贸易融资量';
		}
		
		$this->barplot ( $title, $yw_type, $time, $amount );
	}
	
	// 生成客户业务量分类饼图
	public function Client_Type_Pie($client_id, $start, $end, $type) {
		$result_type = $this->get_client_js_typelist ( $client_id, $start, $end, $type );
		$title = "国际结算业务量构成";
		$yw_type = array ();
		$amount = array ();
		foreach ( $result_type as $v ) {
			array_push ( $yw_type, iconv ( 'utf-8', 'gb2312', substr ( $v ['yw_name'], 0, 27 ) ) );
			array_push ( $amount, $v ['jiner'] / 10000 );
		}
		
		$this->pie ( $title, $yw_type, $amount );
	}
	
	// 生成客户业务量分月柱状图
	public function Client_Barplot($client_id, $start, $end, $type) {
		$result = $this->get_client_js_monthlist ( $client_id, $start, $end, $type );
		
		// dump($result);
		$client_name = M ( 'ywls_fixed' )->field ( 'name' )->where ( 'custno=' . $client_id )->limit ( 1 )->select ();
		$title = str_replace ( '本部', '', $client_name [0] ['name'] );
		$title = $title . "：分月明细图";
		$time = array (); // 时间轴，横轴
		$amount = array (); // 业务量，纵轴
		foreach ( $result as $v ) {
			array_push ( $time, $v ['month'] );
			array_push ( $amount, $v ['jiner'] / 10000 );
		}
		
		$yw_type = '';
		if ($type == '01') {
			$yw_type = '国际结算量';
		}
		if ($type == '02') {
			$yw_type = '结售汇量';
		}
		if ($type == '03') {
			$yw_type = '贸易融资量';
		}
		
		$this->barplot ( $title, $yw_type, $time, $amount );
	}
	
	// 生成全行业务量分类饼图
	public function All_Type_Pie($start, $end) {
		$result_type = $this->get_All_typelist ( $start, $end );
		$title = "国际结算业务量构成";
		$yw_type = array ();
		$amount = array ();
		foreach ( $result_type as $v ) {
			array_push ( $yw_type, iconv ( 'utf-8', 'gb2312', substr ( $v ['yw_name'], 0, 27 ) ) );
			array_push ( $amount, $v ['jiner'] / 10000 );
		}
		
		$this->pie ( $title, $yw_type, $amount );
	}
	
	// 生成全行业务量分月柱状图
	public function All_Barplot($start, $end) {
		$result = $this->get_All_Report_Month ( $start, $end );
		$title = "全行国际结算量分月明细图";
		$time = array (); // 时间轴，横轴
		$amount = array (); // 业务量，纵轴
		foreach ( $result as $v ) {
			array_push ( $time, $v ['month'] );
			array_push ( $amount, $v ['jiner'] / 10000 );
		}
		$yw_type = '国际结算量';
		
		$this->barplot ( $title, $yw_type, $time, $amount );
	}
	
	/*
	 * 函数名称：	barplot() 	绘画：柱状图
	 * 参数：
	 * $info 		图的名称，横轴和坚轴的名称
	 * $deriction 柱状图的方向
	 * $size 图片的大小
	 * $data 数据
	 */
	public function barplot($title, $yw_type, $time, $amount) {
		
		// Create the graph. These two calls are always required
		$graph = new \Graph ( 500, 280, 'auto' );
		$graph->SetScale ( "textlin" );
		
		$legendcolor = array (
				'blue' 
		);
		$legend = new \Legend ();
		$legend->Add ( $legenddate, $legendcolor );
		
		$theme_class = new \UniversalTheme ();
		$graph->SetTheme ( $theme_class );
		
		$graph->SetBox ( false );
		$graph->SetFrame ( true );
		
		$graph->ygrid->SetFill ( false );
		$graph->xaxis->SetTickLabels ( $time );
		// $graph->xaxis->title->Set(iconv('utf-8','gb2312','月'));
		$graph->xaxis->SetLabelAngle ( '45' );
		$graph->yaxis->HideLine ( false );
		$graph->yaxis->HideTicks ( false, false );
		
		// Create the bar plots
		$b1plot = new \BarPlot ( $amount );
		
		$b1plot->SetLegend ( iconv ( 'utf-8', 'gb2312', $yw_type ) );
		$graph->SetMargin ( '50', '20', '30', '70' );
		$graph->legend->Pos ( 0.5, 0.99, "center", "bottom" );
		
		$graph->legend->SetFont ( FF_SIMSUN, FS_NORMAL, 9 );
		
		// ...and add it to the graPH
		$graph->Add ( $b1plot );
		
		$b1plot->SetColor ( "white" );
		$b1plot->SetFillColor ( "green" );
		$b1plot->SetValuePos ( 'top' );
		$b1plot->value->SetFont ( FF_SIMSUN, FS_NORMAL, 9 );
		$b1plot->value->SetFormat ( '%01.1f' );
		$b1plot->value->show ();
		
		$graph->title->Set ( iconv ( 'utf-8', 'GBK', $title ) );
		$graph->subtitle->Set ( iconv ( 'utf-8', 'gb2312', "(单位：万美元)" ) );
		$graph->subtitle->SetMargin ( '25' );
		$graph->title->SetFont ( FF_SIMSUN, FS_BOLD, 18 );
		$graph->subtitle->SetFont ( FF_SIMSUN, FS_NORMAL, 9 );
		
		$graph->Stroke ();
	}
	
	/*
	 * 函数名称：	pie() 	绘画：饼图
	 * 参数：
	 * $info 		图的名称，横轴和坚轴的名称
	 * $deriction 柱状图的方向
	 * $size 图片的大小
	 * $data 数据
	 */
	public function pie($title, $yw_type, $amount) {
		$data = $amount;
		// dump($yw_type);
		
		// Create the Pie Graph.
		$graph = new \PieGraph ( 550, 280 );
		$graph->SetShadow ();
		$graph->SetFrame ( true );
		
		// Set A title for the plot
		$graph->title->Set ( iconv ( 'utf-8', 'gb2312', $title ) );
		$graph->title->SetFont ( FF_SIMSUN, FS_BOLD, 18 );
		$graph->title->SetColor ( "darkblue" );
		$graph->title->SetAlign ( 'left' );
		// $graph->subtitle->Set ( iconv('utf-8','gb2312',"(单位：万美元)"));
		// $graph->subtitle->SetFont(FF_SIMSUN,FS_NORMAL,9);
		$graph->legend->Pos ( 0.01, 0.68, 'left', 'center' );
		// $graph->legend->SetFrameWeight(2);
		// $graph->legend->SetAbsPos(0, 50);
		$graph->legend->SetLeftMargin ( 0 );
		// $graph->legend->SetMarkAbsHSize(100);
		$graph->legend->SetFont ( FF_SIMSUN, FS_BOLD, 9 );
		$graph->legend->SetLayout ( LEGEND_VERT );
		$graph->legend->SetMarkAbsSize ( 6 );
		$graph->legend->SetLineWeight ( 0.5 );
		
		$graph->legend->SetReverse ( true );
		
		$p1 = new \PiePlot ( $data );
		
		$p1->SetCenter ( 0.1, 0.3 );
		
		$p1->SetGuideLinesAdjust ( 1, 0.8 );
		$p1->SetGuideLines ( true, true, false );
		$p1->SetSize ( 80 );
		$p1->SetStartAngle ( 0 );
		$p1->SetLabelPos ( 1.05 );
		$p1->value->SetFont ( FF_SIMSUN, FS_BOLD, 9 );
		
		$p1->SetLegends ( $yw_type );
		
		$graph->Add ( $p1 );
		
		$graph->Stroke ();
	}
	
	// 生成机构业务量报表
	public function Unit_Report($start, $end) {
		// 取得所有经营机构
		$Unit_list = $this->get_all_unit ();
		
		$season = $this->local_season ( $end );
		// dump($season);
		$year = substr ( $end, 0, 4 );
		$startday = $year . $season ['startmonthday'];
		$endday = $year . $season ['endmonthday'];
		
		$Report = array (
				array () 
		);
		foreach ( $Unit_list as $key => $v ) {
			
			// 取得任务数
			$task_amount_js = $this->get_unit_task ( $v ['br_id'], $end, '1' );
			$task_amount_jsh = $this->get_unit_task ( $v ['br_id'], $end, '2' );
			$task_amount_client = $this->get_unit_task ( $v ['br_id'], $end, '3' );
			$task_amount_cx = $this->get_unit_task ( $v ['br_id'], $end, '4' );
			// 取得业务实绩
			$amount_js = $this->get_unit_js ( $v ['br_id'], $start, $end, '01' );
			$amount_jsh = $this->get_unit_js ( $v ['br_id'], $start, $end, '02' );
			$amount_client = $this->get_unit_client ( $v ['br_id'], $start, $end, '1' );
			$cx = $this->get_unit_cx ( $v ['br_id'] );
			$amount_cx = $cx [0] ['c_rj'] - $cx [0] ['b_rj'];
			
			// 取得本季业绩
			$season_amount_js = $this->get_unit_js ( $v ['br_id'], $startday, $endday, '01' );
			$season_amount_jsh = $this->get_unit_js ( $v ['br_id'], $startday, $endday, '02' );
			
			// 写入机构名称和行号
			$Report [$key] ['br_id'] = $v ['br_id'];
			$Report [$key] ['br_name'] = $v ['br_name'];
			$Report [$key] ['Head_office'] = $v ['p_id'];
			
			// 写入经营任务
			$Report [$key] ['task_amount_js'] = $task_amount_js;
			$Report [$key] ['task_amount_jsh'] = $task_amount_jsh;
			$Report [$key] ['task_amount_client'] = $task_amount_client;
			$Report [$key] ['task_amount_cx'] = $task_amount_cx;
			// 写入实绩实绩
			$Report [$key] ['amount_js'] = $amount_js / 10000;
			$Report [$key] ['amount_jsh'] = $amount_jsh / 10000;
			$Report [$key] ['amount_client'] = $amount_client;
			$Report [$key] ['amount_cx'] = $amount_cx / 10000;
			// 写入完成率
			$Report [$key] ['js_complete_rate'] = $amount_js / 10000 / $task_amount_js * 100;
			$Report [$key] ['jsh_complete_rate'] = $amount_jsh / 10000 / $task_amount_jsh * 100;
			$Report [$key] ['client_complete_rate'] = $amount_client / $task_amount_client * 100;
			$Report [$key] ['cx_complete_rate'] = $amount_cx / 10000 / $task_amount_cx * 100;
			// 写入季度业绩
			$Report [$key] ['season_amount_js'] = $season_amount_js / 10000;
			$Report [$key] ['season_amount_jsh'] = $season_amount_jsh / 10000;
		}
		
		// dump($Report);
		return $Report;
	}
	
	/**
	 * 生成客户业务量报表数据数组
	 */
	public function Client_Report($start, $end) {
		
		// 1\查询所有外汇对公客户
		// 2\查询ywls_fixed表生成客户业务汇总表
		// 3\再查询每一条客户业务汇总表记录中行号查询的经营单位名
		$model_m = D ( 'Ywlsfixed' );
		$condition_m ['er_type'] = '1';
		$order = 'upbranch,branch';
		$result_client = $model_m->field ( 'upbranch,branch,custno,name' )->relation ( true )->where ( $condition_m )->group ( 'custno' )->order ( $order )->select ();
		// dump ( $result_client );
		
		$model = D ( 'Ywlsfixed' );
		// 由于国际结算量和结售汇量的统计规则不一致，所以要分开查询然后合并客户数组
		
		// 查询所有国际结算业务
		$condition ['type'] = '01';
		$condition ['yw_date'] = array (
				array (
						'egt',
						$start 
				),
				array (
						'elt',
						$end 
				) 
		);
		$condition ['yw_type'] = array (
				array (
						'neq',
						'lr' 
				),
				array (
						'neq',
						'la' 
				),
				array (
						'neq',
						'ib' 
				) 
		);
		$condition ['er_type'] = '1';
		$field = "upbranch,branch,custno,type,count(usdamt) as times,sum(usdamt*precent/100) as jiner";
		$groupby = "custno,type";
		$order = 'upbranch,branch,custno,type';
		
		$result_js = $model->field ( $field )->relation ( true )->where ( $condition )->group ( $groupby )->order ( $order )->select ();
		
		// 查询所有结售汇业务
		$model_jsh=M('ywls_fixed');
		$condition1 ['type'] = '02';
		$condition1 ['yw_date'] = array (
				array (
						'egt',
						$start 
				),
				array (
						'elt',
						$end 
				) 
		);
		
		$condition1 ['er_type'] = '1';
		$field1 = "upbranch,branch,custno,type,count(usdamt) as times,sum(usdamt*precent/100) as jiner";
		$groupby1 = "custno,type";
		$order1 = 'upbranch,branch,custno,type';
		
		$result_jsh = $model->field ( $field1 )->relation ( true )->where ( $condition1 )->group ( $groupby1 )->order ( $order1 )->select ();
		
		// dump($result);
		
		// 查询所有贸易融资借款业务
		$condition ['er_type'] = '1';
		$condition ['yw_stat'] = '4A';
		$condition ['type'] = '03';
		$field = "upbranch,branch,custno,type,count(usdamt) as times,sum(usdamt*precent/100) as jiner";
		$groupby = "custno,type";
		$order1 = 'upbranch,branch,custno,type';
		$result_tf_b = $model->field ( $field )->relation ( true )->where ( $condition )->group ( $groupby )->order ( $order )->select ();
		
		$flag_js = 0;
		$flag_jsh = 0;
		$flag_tf = 0;
		foreach ( $result_client as $key => $rc ) {
			$flag_js = 0;
			$flag_jsh = 0;
			$flag_tf = 0;
			
			foreach ( $result_tf_b as $rb ) {
				if (strcmp ( $rc ['custno'], $rb ['custno'] ) == 0 && $rb ['type'] == '03') {
					$result_client [$key] ['tf_times'] = $rb ['times'];
					$result_client [$key] ['tf_jiner'] = $rb ['jiner'];
					$flag_tf = 1;
				}
			}
			
			foreach ( $result_jsh as $rh ) {
				if (strcmp ( $rc ['custno'], $rh ['custno'] ) == 0 && $rh ['type'] == '02') {
					$result_client [$key] ['jsh_times'] = $rh ['times'];
					$result_client [$key] ['jsh_jiner'] = $rh ['jiner'];
					$flag_jsh = 1;
				}
			}
			
			foreach ( $result_js as $rj ) {
				
				if (strcmp ( $rc ['custno'], $rj ['custno'] ) == 0 && $rj ['type'] == '01') {
					$result_client [$key] ['js_times'] = $rj ['times'];
					$result_client [$key] ['js_jiner'] = $rj ['jiner'];
					$flag_js = 1;
				}
			}
			
			if ($flag_js == 0 && $flag_jsh == 0 && $flag_tf == 0) {
				unset ( $result_client [$key] );
				continue;
			}
			if ($flag_js == 0) {
				$result_client [$key] ['js_times'] = 0;
				$result_client [$key] ['js_jiner'] = 0;
			}
			if ($flag_jsh == 0) {
				$result_client [$key] ['jsh_times'] = 0;
				$result_client [$key] ['jsh_jiner'] = 0;
			}
			if ($flag_tf == 0) {
				$result_client [$key] ['tf_times'] = 0;
				$result_client [$key] ['tf_jiner'] = 0;
			}
		}
		
		// dump($result_client);
		return $result_client;
	}
	
	/**
	 * 取得所有的经营机构
	 */
	public function get_all_unit() {
		$model = M ( 'struction' );
		$condition ['class'] = 1;
		$condition ['status'] = '0';
		$condition ['br_id'] = array (
				array (
						'neq',
						'08129' 
				),
				array (
						'neq',
						'08131' 
				) 
		);
		$map ['br_id'] = '08108';
		$map ['_logic'] = 'or';
		$map ['_complex'] = $condition;
		return $result = $model->where ( $map )->select ();
		// dump($result);
	}
	
	/**
	 * 取得所有的业务清单
	 */
	public function get_unit_js($unit, $start, $end, $type) {
		$model = M ( 'ywls_fixed' );
		$condition ['upbranch'] = $unit;
		$condition ['type'] = $type;
		if ($type == '01') {
			// not in('lr','la','ib')
			$condition ['yw_type'] = array (
					array (
							'neq',
							'lr' 
					),
					array (
							'neq',
							'la' 
					),
					array (
							'neq',
							'ib' 
					) 
			);
		}
		
		$condition ['yw_date'] = array (
				array (
						'egt',
						$start 
				),
				array (
						'elt',
						$end 
				) 
		);
		$result = $model->where ( $condition )->sum ( 'usdamt*precent/100' );
		return $result;
		// dump($result);
	}
	
	/**
	 * 取得所有sm的任务量
	 */
	public function get_unit_task($unit, $date, $type) {
		$model = M ( 'task' );
		// 先计算出$date 在那一个季度
		$season = '';
		$month = substr ( $date, 4, 2 );
		
		if ($month >= "01" && $month <= '03') {
			$season = "q1";
		}
		if ($month >= "04" && $month <= '06') {
			$season = "q2";
		}
		if ($month >= "07" && $month <= '09') {
			$season = "q3";
		}
		if ($month >= "10" && $month <= '12') {
			$season = "q4";
		}
		// echo $season;
		$condition ['unit_id'] = $unit;
		$condition ['zb_type'] = $type;
		$result = $model->where ( $condition )->select ();
		// dump( $result);
		return $result [0] [$season];
	}
	
	/**
	 * 获取经营单位所有的客户数量
	 */
	public function get_unit_client($unit, $start, $end, $client_type) {
		$model = M ( 'ywls_fixed' );
		$condition ['upbranch'] = $unit;
		$condition ['er_type'] = $client_type;
		$condition ['yw_date'] = array (
				array (
						'gt',
						$start 
				),
				array (
						'lt',
						$end 
				) 
		);
		$result = $model->where ( $condition )->count ( 'distinct custno' );
		return $result;
	}
	
	/**
	 * 获取经营单位的储蓄数据
	 */
	public function get_unit_cx($unit) {
		$model = M ( '' );
		$sql = "
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
					where totable.brr_id=" . $unit . "  
									";
		// dump($reuslt);
		return $reuslt = $model->query ( $sql );
	}
	
	/**
	 * 取得所有的对公客户
	 */
	public function get_all_client_company($start, $end) {
		$model = M ( 'ywls_fixed' );
		$condition ['er_type'] = 1;
		$condition ['yw_date'] = array (
				array (
						'egt',
						$start 
				),
				array (
						'elt',
						$end 
				) 
		);
		$field = "distinct name,custno,upbranch";
		$result = $model->field ( $field )->where ( $condition )->select ();
		// dump($result);
		return $result;
	}
	
	/**
	 * 取得客户的业务量
	 */
	public function get_client_js($client, $start, $end, $type) {
		$model = M ( 'ywls_fixed' );
		$condition ['custno'] = $client;
		$condition ['type'] = $type;
		if ($type == '01') {
			$condition ['yw_type'] = array (
					array (
							'neq',
							'lr' 
					),
					array (
							'neq',
							'la' 
					),
					array (
							'neq',
							'ib' 
					) 
			);
		}
		
		if ($type == '03') {
			$condition ['yw_stat'] = '4A';
		}
		
		$condition ['yw_date'] = array (
				array (
						'egt',
						$start 
				),
				array (
						'elt',
						$end 
				) 
		);
		$result_count = $model->where ( $condition )->count ( 'type' );
		$result_sum = $model->where ( $condition )->sum ( 'usdamt*precent/100' );
		$result = array ();
		
		$result ['count'] = $result_count;
		$result ['sum'] = $result_sum;
		return $result;
	}
	
	// ///////////////////////////////////////////////////////////////////////////////////////////////
	//
	// 生成经营单位报表EXCEL文件
	//
	// ///////////////////////////////////////////////////////////////////////////////////////////////
	public function Export_Unit_Excel($start, $end) {
		$result = $this->Unit_Report ( $start, $end );
		$date_cxrj = M ( 'cx_avg_current' )->field ( 'date' )->limit ( 1 )->select ();
		// dump($date_cxrj);
		
		/**
		 * Error reporting
		 */
		error_reporting ( E_ALL );
		ini_set ( 'display_errors', TRUE );
		ini_set ( 'display_startup_errors', TRUE );
		date_default_timezone_set ( 'Europe/London' );
		
		define ( 'EOL', (PHP_SAPI == 'cli') ? PHP_EOL : '<br />' );
		
		/**
		 * Include PHPExcel
		 */
		// require_once '/jrrc_web/public/PHPExcel_1.8.0_doc/Classes/PHPExcel.php';
		
		// Create new PHPExcel object
		$objPHPExcel = new \PHPExcel ();
		
		// 输出表头
		$objPHPExcel->getActiveSheet ()->mergeCells ( 'A1:A2' );
		$objPHPExcel->getActiveSheet ()->mergeCells ( 'b1:b2' );
		$objPHPExcel->getActiveSheet ()->mergeCells ( 'c1:f2' );
		$objPHPExcel->getActiveSheet ()->mergeCells ( 'g1:j2' );
		$objPHPExcel->getActiveSheet ()->mergeCells ( 'k1:m2' );
		$objPHPExcel->getActiveSheet ()->mergeCells ( 'n1:p2' );
		
		$objPHPExcel->getActiveSheet ()->setCellValue ( 'c1', '国际结算量' );
		$objPHPExcel->getActiveSheet ()->setCellValue ( 'g1', '结售汇量' );
		$objPHPExcel->getActiveSheet ()->setCellValue ( 'k1', '有效对公客户' );
		$objPHPExcel->getActiveSheet ()->setCellValue ( 'n1', '储蓄存款日均增量-' . $date_cxrj [0] ['date'] );
		
		$objPHPExcel->getActiveSheet ()->setCellValue ( 'a3', '行号' );
		$objPHPExcel->getActiveSheet ()->setCellValue ( 'b3', '经营单位名称' );
		
		$objPHPExcel->getActiveSheet ()->setCellValue ( 'c3', '任务' );
		$objPHPExcel->getActiveSheet ()->setCellValue ( 'd3', '实绩' );
		$objPHPExcel->getActiveSheet ()->setCellValue ( 'e3', '完成率（%）' );
		$objPHPExcel->getActiveSheet ()->setCellValue ( 'f3', '本季业绩' );
		
		$objPHPExcel->getActiveSheet ()->setCellValue ( 'g3', '任务' );
		$objPHPExcel->getActiveSheet ()->setCellValue ( 'h3', '实绩' );
		$objPHPExcel->getActiveSheet ()->setCellValue ( 'i3', '完成率（%）' );
		$objPHPExcel->getActiveSheet ()->setCellValue ( 'j3', '本季业绩' );
		
		$objPHPExcel->getActiveSheet ()->setCellValue ( 'k3', '任务' );
		$objPHPExcel->getActiveSheet ()->setCellValue ( 'l3', '实绩' );
		$objPHPExcel->getActiveSheet ()->setCellValue ( 'm3', '完成率（%）' );
		$objPHPExcel->getActiveSheet ()->setCellValue ( 'n3', '任务' );
		$objPHPExcel->getActiveSheet ()->setCellValue ( 'o3', '实绩' );
		$objPHPExcel->getActiveSheet ()->setCellValue ( 'p3', '完成率（%）' );
		
		// 输出支行
		$row = 4;
		foreach ( $result as $u ) {
			if ($u ['Head_office'] == '08100') {
				$objPHPExcel->getActiveSheet ()->setCellValue ( 'A' . $row, ' ' . $u ['br_id'] );
				$objPHPExcel->getActiveSheet ()->setCellValue ( 'b' . $row, str_replace ( '本部', '', $u ['br_name'] ) );
				
				$objPHPExcel->getActiveSheet ()->setCellValue ( 'c' . $row, $u ['task_amount_js'] );
				$objPHPExcel->getActiveSheet ()->setCellValue ( 'd' . $row, $u ['amount_js'] );
				$objPHPExcel->getActiveSheet ()->setCellValue ( 'e' . $row, $u ['amount_js'] / $u ['task_amount_js'] * 100 );
				$objPHPExcel->getActiveSheet ()->setCellValue ( 'f' . $row, $u ['season_amount_js'] );
				
				$objPHPExcel->getActiveSheet ()->setCellValue ( 'g' . $row, $u ['task_amount_jsh'] );
				$objPHPExcel->getActiveSheet ()->setCellValue ( 'h' . $row, $u ['amount_jsh'] );
				$objPHPExcel->getActiveSheet ()->setCellValue ( 'i' . $row, $u ['amount_jsh'] / $u ['task_amount_jsh'] * 100 );
				$objPHPExcel->getActiveSheet ()->setCellValue ( 'j' . $row, $u ['season_amount_jsh'] );
				
				$objPHPExcel->getActiveSheet ()->setCellValue ( 'k' . $row, $u ['task_amount_client'] );
				$objPHPExcel->getActiveSheet ()->setCellValue ( 'l' . $row, $u ['amount_client'] );
				$objPHPExcel->getActiveSheet ()->setCellValue ( 'm' . $row, $u ['amount_client'] / $u ['task_amount_client'] * 100 );
				
				$objPHPExcel->getActiveSheet ()->setCellValue ( 'n' . $row, $u ['task_amount_cx'] );
				$objPHPExcel->getActiveSheet ()->setCellValue ( 'o' . $row, $u ['amount_cx'] );
				$objPHPExcel->getActiveSheet ()->setCellValue ( 'p' . $row, $u ['amount_cx'] / $u ['task_amount_cx'] * 100 );
				$row ++;
			}
		}
		
		// 输出支行合计
		
		$objPHPExcel->getActiveSheet ()->mergeCells ( 'a' . $row . ':b' . $row );
		$objPHPExcel->getActiveSheet ()->setCellValue ( 'A' . $row, '支行合计' );
		// $objPHPExcel->getActiveSheet()->setCellValue('c'.$row,"=sum('c4:c".($row-1)."')");
		
		$objPHPExcel->getActiveSheet ()->setCellValue ( 'c' . $row, "=sum(c4:c" . ($row - 1) . ")" );
		$objPHPExcel->getActiveSheet ()->setCellValue ( 'd' . $row, "=sum(d4:d" . ($row - 1) . ")" );
		$objPHPExcel->getActiveSheet ()->setCellValue ( 'e' . $row, "=d" . $row . "/c" . $row . "*100" );
		$objPHPExcel->getActiveSheet ()->setCellValue ( 'f' . $row, "=sum(f4:f" . ($row - 1) . ")" );
		
		$objPHPExcel->getActiveSheet ()->setCellValue ( 'g' . $row, "=sum(g4:g" . ($row - 1) . ")" );
		$objPHPExcel->getActiveSheet ()->setCellValue ( 'h' . $row, "=sum(h4:h" . ($row - 1) . ")" );
		$objPHPExcel->getActiveSheet ()->setCellValue ( 'i' . $row, "=h" . $row . "/g" . $row . "*100" );
		$objPHPExcel->getActiveSheet ()->setCellValue ( 'j' . $row, "=sum(j4:j" . ($row - 1) . ")" );
		
		$objPHPExcel->getActiveSheet ()->setCellValue ( 'k' . $row, "=sum(k4:k" . ($row - 1) . ")" );
		$objPHPExcel->getActiveSheet ()->setCellValue ( 'l' . $row, "=sum(l4:l" . ($row - 1) . ")" );
		$objPHPExcel->getActiveSheet ()->setCellValue ( 'm' . $row, "=l" . $row . "/k" . $row . "*100" );
		
		$objPHPExcel->getActiveSheet ()->setCellValue ( 'n' . $row, "=sum(n4:n" . ($row - 1) . ")" );
		$objPHPExcel->getActiveSheet ()->setCellValue ( 'o' . $row, "=sum(o4:o" . ($row - 1) . ")" );
		$objPHPExcel->getActiveSheet ()->setCellValue ( 'p' . $row, "=o" . $row . "/n" . $row . "*100" );
		
		// 输出部室
		$total_branch_row = $row;
		$department_row = $row + 1;
		$row = $row + 1;
		foreach ( $result as $u ) {
			if ($u ['Head_office'] == '08101') {
				
				$objPHPExcel->getActiveSheet ()->setCellValue ( 'A' . $row, ' ' . $u ['br_id'] );
				$objPHPExcel->getActiveSheet ()->setCellValue ( 'b' . $row, str_replace ( '国际业务部', '代理联社业务', $u ['br_name'] ) );
				
				$objPHPExcel->getActiveSheet ()->setCellValue ( 'c' . $row, $u ['task_amount_js'] );
				$objPHPExcel->getActiveSheet ()->setCellValue ( 'd' . $row, $u ['amount_js'] );
				$objPHPExcel->getActiveSheet ()->setCellValue ( 'e' . $row, $u ['amount_js'] / $u ['task_amount_js'] * 100 );
				$objPHPExcel->getActiveSheet ()->setCellValue ( 'f' . $row, $u ['season_amount_js'] );
				
				$objPHPExcel->getActiveSheet ()->setCellValue ( 'g' . $row, $u ['task_amount_jsh'] );
				$objPHPExcel->getActiveSheet ()->setCellValue ( 'h' . $row, $u ['amount_jsh'] );
				$objPHPExcel->getActiveSheet ()->setCellValue ( 'i' . $row, $u ['amount_jsh'] / $u ['task_amount_jsh'] * 100 );
				$objPHPExcel->getActiveSheet ()->setCellValue ( 'j' . $row, $u ['season_amount_jsh'] );
				
				$objPHPExcel->getActiveSheet ()->setCellValue ( 'k' . $row, $u ['task_amount_client'] );
				$objPHPExcel->getActiveSheet ()->setCellValue ( 'l' . $row, $u ['amount_client'] );
				$objPHPExcel->getActiveSheet ()->setCellValue ( 'm' . $row, $u ['amount_client'] / $u ['task_amount_client'] * 100 );
				
				$objPHPExcel->getActiveSheet ()->setCellValue ( 'n' . $row, $u ['task_amount_cx'] );
				$objPHPExcel->getActiveSheet ()->setCellValue ( 'o' . $row, $u ['amount_cx'] );
				$objPHPExcel->getActiveSheet ()->setCellValue ( 'p' . $row, $u ['amount_cx'] / $u ['task_amount_cx'] * 100 );
				$row ++;
			}
		}
		// 输出部室合计
		$objPHPExcel->getActiveSheet ()->mergeCells ( 'a' . $row . ':b' . $row );
		$objPHPExcel->getActiveSheet ()->setCellValue ( 'A' . $row, '部室合计' );
		// $objPHPExcel->getActiveSheet()->setCellValue('c'.$row,"=sum('c4:c".($row-1)."')");
		$objPHPExcel->getActiveSheet ()->setCellValue ( 'c' . $row, "=sum(c" . $department_row . ":c" . ($row - 1) . ")" );
		$objPHPExcel->getActiveSheet ()->setCellValue ( 'd' . $row, "=sum(d" . $department_row . ":d" . ($row - 1) . ")" );
		$objPHPExcel->getActiveSheet ()->setCellValue ( 'e' . $row, "=d" . $row . "/c" . $row . "*100" );
		$objPHPExcel->getActiveSheet ()->setCellValue ( 'f' . $row, "=sum(f" . $department_row . ":f" . ($row - 1) . ")" );
		
		$objPHPExcel->getActiveSheet ()->setCellValue ( 'g' . $row, "=sum(f" . $department_row . ":f" . ($row - 1) . ")" );
		$objPHPExcel->getActiveSheet ()->setCellValue ( 'h' . $row, "=sum(g" . $department_row . ":g" . ($row - 1) . ")" );
		// $objPHPExcel->getActiveSheet()->setCellValue('i'.$row,"=h".$row."/g".$row."*100");
		$objPHPExcel->getActiveSheet ()->setCellValue ( 'j' . $row, "=sum(j" . $department_row . ":j" . ($row - 1) . ")" );
		
		$objPHPExcel->getActiveSheet ()->setCellValue ( 'k' . $row, "=sum(k" . $department_row . ":k" . ($row - 1) . ")" );
		$objPHPExcel->getActiveSheet ()->setCellValue ( 'l' . $row, "=sum(l" . $department_row . ":l" . ($row - 1) . ")" );
		// $objPHPExcel->getActiveSheet()->setCellValue('m'.$row,"=l".$row."/k".$row."*100");
		
		$objPHPExcel->getActiveSheet ()->setCellValue ( 'n' . $row, "=sum(n" . $department_row . ":n" . ($row - 1) . ")" );
		$objPHPExcel->getActiveSheet ()->setCellValue ( 'o' . $row, "=sum(o" . $department_row . ":o" . ($row - 1) . ")" );
		// $objPHPExcel->getActiveSheet()->setCellValue('p'.$row,"=o".$row."/n".$row."*100");
		
		// 输出全行合计
		$total_depart_row = $row;
		$row = $row + 1;
		$objPHPExcel->getActiveSheet ()->mergeCells ( 'a' . $row . ':b' . $row );
		$objPHPExcel->getActiveSheet ()->setCellValue ( 'A' . $row, '全行合计' );
		// $objPHPExcel->getActiveSheet()->setCellValue('c'.$row,"=sum('c4:c".($row-1)."')");
		$objPHPExcel->getActiveSheet ()->setCellValue ( 'c' . $row, "=c" . $total_branch_row . "+c" . $total_depart_row );
		$objPHPExcel->getActiveSheet ()->setCellValue ( 'd' . $row, "=d" . $total_branch_row . "+d" . $total_depart_row );
		$objPHPExcel->getActiveSheet ()->setCellValue ( 'e' . $row, "=d" . $row . "/c" . $row . "*100" );
		$objPHPExcel->getActiveSheet ()->setCellValue ( 'f' . $row, "=f" . $total_branch_row . "+f" . $total_depart_row );
		
		$objPHPExcel->getActiveSheet ()->setCellValue ( 'g' . $row, "=g" . $total_branch_row . "+g" . $total_depart_row );
		$objPHPExcel->getActiveSheet ()->setCellValue ( 'h' . $row, "=h" . $total_branch_row . "+h" . $total_depart_row );
		$objPHPExcel->getActiveSheet ()->setCellValue ( 'i' . $row, "=h" . $row . "/g" . $row . "*100" );
		$objPHPExcel->getActiveSheet ()->setCellValue ( 'j' . $row, "=j" . $total_branch_row . "+j" . $total_depart_row );
		
		$objPHPExcel->getActiveSheet ()->setCellValue ( 'k' . $row, "=k" . $total_branch_row . "+k" . $total_depart_row );
		$objPHPExcel->getActiveSheet ()->setCellValue ( 'l' . $row, "=l" . $total_branch_row . "+l" . $total_depart_row );
		$objPHPExcel->getActiveSheet ()->setCellValue ( 'm' . $row, "=l" . $row . "/k" . $row . "*100" );
		
		$objPHPExcel->getActiveSheet ()->setCellValue ( 'n' . $row, "=n" . $total_branch_row . "+n" . $total_depart_row );
		$objPHPExcel->getActiveSheet ()->setCellValue ( 'o' . $row, "=o" . $total_branch_row . "+o" . $total_depart_row );
		$objPHPExcel->getActiveSheet ()->setCellValue ( 'p' . $row, "=o" . $row . "/n" . $row . "*100" );
		
		// 格式化数值列
		$objPHPExcel->getActiveSheet ()->getStyle ( 'c4:c' . $row )->getNumberFormat ()->setFormatCode ( '#,##' );
		$objPHPExcel->getActiveSheet ()->getStyle ( 'd4:d' . $row )->getNumberFormat ()->setFormatCode ( '#,##0.00' );
		$objPHPExcel->getActiveSheet ()->getStyle ( 'e4:e' . $row )->getNumberFormat ()->setFormatCode ( '#,##0.00' );
		$objPHPExcel->getActiveSheet ()->getStyle ( 'f4:f' . $row )->getNumberFormat ()->setFormatCode ( '#,##0.00' );
		
		$objPHPExcel->getActiveSheet ()->getStyle ( 'g4:g' . $row )->getNumberFormat ()->setFormatCode ( '#,##' );
		$objPHPExcel->getActiveSheet ()->getStyle ( 'h4:h' . $row )->getNumberFormat ()->setFormatCode ( '#,##0.00' );
		$objPHPExcel->getActiveSheet ()->getStyle ( 'i4:i' . $row )->getNumberFormat ()->setFormatCode ( '#,##0.00' );
		$objPHPExcel->getActiveSheet ()->getStyle ( 'j4:j' . $row )->getNumberFormat ()->setFormatCode ( '#,##0.00' );
		
		$objPHPExcel->getActiveSheet ()->getStyle ( 'k4:k' . $row )->getNumberFormat ()->setFormatCode ( '#,##' );
		$objPHPExcel->getActiveSheet ()->getStyle ( 'l4:l' . $row )->getNumberFormat ()->setFormatCode ( '#,##0.00' );
		$objPHPExcel->getActiveSheet ()->getStyle ( 'm4:m' . $row )->getNumberFormat ()->setFormatCode ( '#,##0.00' );
		
		$objPHPExcel->getActiveSheet ()->getStyle ( 'n4:n' . $row )->getNumberFormat ()->setFormatCode ( '#,##' );
		$objPHPExcel->getActiveSheet ()->getStyle ( 'o4:o' . $row )->getNumberFormat ()->setFormatCode ( '#,##0.00' );
		$objPHPExcel->getActiveSheet ()->getStyle ( 'p4:p' . $row )->getNumberFormat ()->setFormatCode ( '#,##0.00' );
		
		// 设定列的宽度
		$objPHPExcel->getActiveSheet ()->getColumnDimension ( 'b' )->setWidth ( 20 );
		$objPHPExcel->getActiveSheet ()->getColumnDimension ( 'c' )->setWidth ( 10 );
		$objPHPExcel->getActiveSheet ()->getColumnDimension ( 'd' )->setWidth ( 10 );
		$objPHPExcel->getActiveSheet ()->getColumnDimension ( 'e' )->setWidth ( 15 );
		$objPHPExcel->getActiveSheet ()->getColumnDimension ( 'f' )->setWidth ( 10 );
		$objPHPExcel->getActiveSheet ()->getColumnDimension ( 'g' )->setWidth ( 10 );
		$objPHPExcel->getActiveSheet ()->getColumnDimension ( 'h' )->setWidth ( 15 );
		$objPHPExcel->getActiveSheet ()->getColumnDimension ( 'i' )->setWidth ( 10 );
		$objPHPExcel->getActiveSheet ()->getColumnDimension ( 'j' )->setWidth ( 10 );
		$objPHPExcel->getActiveSheet ()->getColumnDimension ( 'k' )->setWidth ( 15 );
		$objPHPExcel->getActiveSheet ()->getColumnDimension ( 'l' )->setWidth ( 10 );
		$objPHPExcel->getActiveSheet ()->getColumnDimension ( 'm' )->setWidth ( 10 );
		$objPHPExcel->getActiveSheet ()->getColumnDimension ( 'n' )->setWidth ( 15 );
		
		$styleArray = array (
				'alignment' => array (
						'horizontal' => 'center',
						'vertical' => 'center' 
				) 
		);
		$objPHPExcel->getActiveSheet ()->getStyle ( "c1:p1" )->applyFromArray ( $styleArray );
		$objPHPExcel->getActiveSheet ()->getStyle ( "a3:p3" )->applyFromArray ( $styleArray );
		$objPHPExcel->getActiveSheet ()->getStyle ( "a1:p" . $row )->applyFromArray ( $styleArray );
		
		$styleArray1 = array (
				'borders' => array (
						'allborders' => array (
								'style' => \PHPExcel_Style_Border::BORDER_THIN,
								'color' => array (
										'argb' => 'gray' 
								) 
						) 
				) 
		);
		
		$objPHPExcel->getActiveSheet ()->getStyle ( 'a1:p' . ($row) )->applyFromArray ( $styleArray1 );
		
		// Rename worksheet
		$objPHPExcel->getActiveSheet ()->setTitle ( '经营单位业务报表' );
		// Set active sheet index to the first sheet, so Excel opens this as the first sheet
		$objPHPExcel->setActiveSheetIndex ( 0 );
		// Save Excel 2007 file
		$objWriter = \PHPExcel_IOFactory::createWriter ( $objPHPExcel, 'Excel2007' );
		$filename = 'unit_ywl_' . $start . '_' . $end . '.xlsx';
		unlink ( "../jrrc_web/public/report/excel/" . $filename );
		$objWriter->save ( str_replace ( '.php', '.xlsx', "../jrrc_web/public/report/excel/" . $filename ) );
		echo $filename;
	}
	
	// ///////////////////////////////////////////////////////////////////////////////////////////////
	//
	// 导出客户EXCEL（包括结算、结售汇、和贸易融资）
	//
	// ///////////////////////////////////////////////////////////////////////////////////////////////
	public function Export_Client_Excel($start, $end) {
		$client_list = $this->Client_Report ( $start, $end );
		
		/**
		 * Include PHPExcel
		 */
		// require_once '/jrrc_web/public/PHPExcel_1.8.0_doc/Classes/PHPExcel.php';
		
		// Create new PHPExcel object
		$objPHPExcel = new \PHPExcel ();
		$objPHPExcel->getActiveSheet ()->getColumnDimension ( 'c' )->setWidth ( 20 );
		$objPHPExcel->getActiveSheet ()->getColumnDimension ( 'd' )->setWidth ( 40 );
		$objPHPExcel->getActiveSheet ()->getColumnDimension ( 'e' )->setWidth ( 20 );
		$objPHPExcel->getActiveSheet ()->getColumnDimension ( 'f' )->setWidth ( 20 );
		$objPHPExcel->getActiveSheet ()->getColumnDimension ( 'g' )->setWidth ( 20 );
		$objPHPExcel->getActiveSheet ()->getColumnDimension ( 'h' )->setWidth ( 20 );
		$objPHPExcel->getActiveSheet ()->getColumnDimension ( 'i' )->setWidth ( 20 );
		$objPHPExcel->getActiveSheet ()->getColumnDimension ( 'j' )->setWidth ( 20 );
		
		// dump($client_list);
		
		$objPHPExcel->setActiveSheetIndex ( 0 )->setCellValue ( 'A1', '一级支行' )->setCellValue ( 'B1', '二级支行' )->setCellValue ( 'C1', '行名' )->setCellValue ( 'D1', '客户名称' )->setCellValue ( 'e1', '国际结算笔数' )->setCellValue ( 'f1', '国际结算金额(美元)' )->setCellValue ( 'g1', '结售汇笔数' )->setCellValue ( 'h1', '结售汇金额(美元)' )->setCellValue ( 'i1', '贸易融资笔数' )->setCellValue ( 'j1', '贸易融资金额(美元)' );
		// Add some data
		$row = 2;
		foreach ( $client_list as $v ) {
			
			$objPHPExcel->setActiveSheetIndex ( 0 )->setCellValue ( 'A' . $row, " " . $v ['upbranch'] )->setCellValue ( 'B' . $row, " " . $v ['branch'] )->setCellValue ( 'C' . $row, $v ['br_name'] )->setCellValue ( 'D' . $row, $v ['name'] )->setCellValue ( 'e' . $row, $v ['js_times'] )->setCellValue ( 'f' . $row, $v ['js_jiner'] )->setCellValue ( 'g' . $row, $v ['jsh_times'] )->setCellValue ( 'h' . $row, $v ['jsh_jiner'] )->setCellValue ( 'i' . $row, $v ['tf_times'] )->setCellValue ( 'j' . $row, $v ['tf_jiner'] );
			
			$objPHPExcel->setActiveSheetIndex ( 0 )->setCellValue ( 'd' . ($row + 1), "合计" )->setCellValue ( 'e' . ($row + 1), "=sum(e2:e" . ($row) . ")" )->setCellValue ( 'f' . ($row + 1), "=sum(f2:f" . ($row) . ")" )->setCellValue ( 'g' . ($row + 1), "=sum(g2:g" . ($row) . ")" )->setCellValue ( 'h' . ($row + 1), "=sum(h2:h" . ($row) . ")" )->setCellValue ( 'i' . ($row + 1), "=sum(i2:i" . ($row) . ")" )->setCellValue ( 'j' . ($row + 1), "=sum(j2:j" . ($row) . ")" );
			
			// $objPHPExcel->getActiveSheet()->setCellValue('c'.$row,"=sum(c4:c".($row-1).")");
			
			$objPHPExcel->getActiveSheet ()->getStyle ( 'f' . $row )->getNumberFormat ()->setFormatCode ( '#,##0.00' );
			
			$objPHPExcel->getActiveSheet ()->getStyle ( 'h' . $row )->getNumberFormat ()->setFormatCode ( '#,##0.00' );
			
			$objPHPExcel->getActiveSheet ()->getStyle ( 'j' . $row )->getNumberFormat ()->setFormatCode ( '#,##0.00' );
			
			$objPHPExcel->getActiveSheet ()->getStyle ( 'f' . ($row + 1) )->getNumberFormat ()->setFormatCode ( '#,##0.00' );
			
			$objPHPExcel->getActiveSheet ()->getStyle ( 'h' . ($row + 1) )->getNumberFormat ()->setFormatCode ( '#,##0.00' );
			
			$objPHPExcel->getActiveSheet ()->getStyle ( 'j' . ($row + 1) )->getNumberFormat ()->setFormatCode ( '#,##0.00' );
			
			$styleArray = array (
					'borders' => array (
							'allborders' => array (
									'style' => \PHPExcel_Style_Border::BORDER_THIN,
									'color' => array (
											'argb' => 'gray' 
									) 
							) 
					) 
			);
			
			$styleArray_head = array (
					'alignment' => array (
							'horizontal' => 'center' 
					) 
			);
			
			$objPHPExcel->getActiveSheet ()->getStyle ( 'a1:j' . ($row + 1) )->applyFromArray ( $styleArray );
			$objPHPExcel->getActiveSheet ()->getStyle ( 'a1:j1' )->applyFromArray ( $styleArray_head );
			$objPHPExcel->getActiveSheet ()->getStyle ( 'A1:E1' )->getFill ()->getStartColor ()->setARGB ( 'FF808080' );
			
			$row ++;
		}
		
		// Rename worksheet
		$objPHPExcel->getActiveSheet ()->setTitle ( '公司客户国际业务量' );
		// Set active sheet index to the first sheet, so Excel opens this as the first sheet
		$objPHPExcel->setActiveSheetIndex ( 0 );
		// Save Excel 2007 file
		$objWriter = \PHPExcel_IOFactory::createWriter ( $objPHPExcel, 'Excel2007' );
		$filename = 'client_ywl_' . $start . '_' . $end . '.xlsx';
		$objWriter->save ( str_replace ( '.php', '.xlsx', "../jrrc_web/public/report/excel/" . $filename ) );
		echo $filename;
	}
	
	//导出客户首笔业务日期Excel文件
	public function Export_Client_first_date($start, $end) {
		$client_list = $this->Client_first_date ( $start, $end );
	
		/**
		 * Include PHPExcel
		*/
		// require_once '/jrrc_web/public/PHPExcel_1.8.0_doc/Classes/PHPExcel.php';
	
		// Create new PHPExcel object
		$objPHPExcel = new \PHPExcel ();
	
	
		// dump($client_list);
	
		$objPHPExcel->setActiveSheetIndex ( 0 )
		->setCellValue ( 'A1', '行号' )
		->setCellValue ( 'B1', '行名' )
		->setCellValue ( 'C1', '客户名称' )
		->setCellValue ( 'D1', '首笔业务日期' ) ;
		// Add some data
		$row = 2;
		foreach ( $client_list as $v ) {
				
			$objPHPExcel
			->setActiveSheetIndex ( 0 )
			->setCellValue ( 'A' . $row, " " . $v ['upbranch'] )
			->setCellValue ( 'B' . $row, $v ['br_name'] )
			->setCellValue ( 'C' . $row, $v ['name'] )
			->setCellValue ( 'D' . $row, $v ['yw_date'] );
			
				
			$styleArray = array (
					'borders' => array (
							'allborders' => array (
									'style' => \PHPExcel_Style_Border::BORDER_THIN,
									'color' => array (
											'argb' => 'gray'
									)
							)
					)
			);
				
			$styleArray_head = array (
					'alignment' => array (
							'horizontal' => 'center'
					)
			);

			$row ++;
		}
	
		// Rename worksheet
		$objPHPExcel->getActiveSheet ()->setTitle ( '公司客户首笔业务发生日期' );
		// Set active sheet index to the first sheet, so Excel opens this as the first sheet
		$objPHPExcel->setActiveSheetIndex ( 0 );
		// Save Excel 2007 file
		$objWriter = \PHPExcel_IOFactory::createWriter ( $objPHPExcel, 'Excel2007' );
		$filename = "clientfirstdate.xlsx";
		$objWriter->save ( str_replace ( '.php', '.xlsx', "../Jrrc_web/Public/report/excel/" . $filename ) );
		echo $filename;
	}
	
	
	
	// 查询客户首笔国际结算业务的日期
	public function Client_first_date() {
		$model = M ();
		$sql = "select upbranch,br_name,name,yw_date,custno

			from jrrc_ywls_fixed  LEFT JOIN  jrrc_struction
			
			on upbranch=br_id
			
			where type='01' and er_type='1' GROUP BY custno ORDER BY yw_date ";
		
		//$result = $model->query ( $sql );
		//dump($result);
		return $result = $model->query ( $sql );
	}
	
	public function show_Client_first_date($start,$end){
		$result=$this->Client_first_date();
		$this->assign('result',$result);
		$this->display('show_Client_first_date');
	}
}
