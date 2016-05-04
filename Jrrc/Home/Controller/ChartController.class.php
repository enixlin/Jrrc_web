<?php
namespace Home\Controller;
use Think\Controller;
// 导入绘图类文件
require_once  ("../jrrc_web/public/jpgraph/jpgraph/jpgraph.php");
require_once  ("../jrrc_web/public/jpgraph/jpgraph/jpgraph_bar.php"); // 柱状图
require_once ("../jrrc_web/public/jpgraph/jpgraph/jpgraph_pie.php");
require_once ("../jrrc_web/public/jpgraph/jpgraph/jpgraph_pie3d.php");

class ChartController extends Controller {
	public function index() {
		//$this->assign ( 'data', $datay );
		$this->display ( 'index' );
	}
	
	/**
	 * 全行业务量分月柱状图
	 */
	public function all_month_barplot($start,$end){
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
		$time = array (	);
		$amount = array ();
		$title='全行结算量分月明细图';
		$yw_type='国际结算量';
		foreach ($result as $v){
			array_push($time,$v['yw_month']);
			array_push($amount,$v['js_amount']/10000);
			
		}
		
		$this->barplot($title,$yw_type,$time,$amount);;
		
	}
	
	/**
	 * 全行业务分类明细
	 */
	public function all_type_pie($start,$end){
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
		$result= $Model->query ( $sql_type );
		$title="国际结算业务量构成";
		$yw_type=array();
		$amount=array();
		foreach ($result as $v){
			array_push($yw_type,iconv('utf-8','gb2312',substr($v['yw_name'],0,27)));
			array_push($amount,$v['js_amount']/10000);
		}
		$this->pie($title, $yw_type, $amount);
	}
	
	public function branch_type_pie($level,$id,$type,$start,$end){
		$condition='';
		if($level=='unit'){
			$condition='  totable.p_id='.$id;
		}else{
			$condition='  totable.custno='.$id;
		}
		
		$Model = M ();
		$sql_client = "
						select  totable.yw_name,totable.br_id,totable.custno,br_name,
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
		 $result_branch = $Model->query ( $sql_client );
		 $title="国际结算业务量构成";
		 $yw_type=array();
		 $amount=array();
		 foreach ($result_branch as $v){
		 	array_push($yw_type,iconv('utf-8','gb2312',substr($v['yw_name'],0,27)));
		 	array_push($amount,$v['totalamount']/10000);
		 	// 			echo $v['totalamount']/10000;
		 	// 			echo "</br>";
		 }
		
		 //dump($yw_type);
		 $this->pie($title, $yw_type, $amount);
	}
	
	public function client_type_pie($level,$id,$type,$start,$end){
		$condition='';
		if($level=='unit'){
			$condition='  totable.p_id='.$id;
		}else{
			$condition='  totable.custno='.$id;
		}
	
		$Model = M ();
		$sql_client = "
						select  totable.yw_name,totable.br_id,totable.custno,name,
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
		$result_branch = $Model->query ( $sql_client );
		$title=$result_branch[0]['name']."：国际业务量构成";
		$yw_type=array();
		$amount=array();
		foreach ($result_branch as $v){
			array_push($yw_type,iconv('utf-8','gb2312',substr($v['yw_name'],0,27)));
			array_push($amount,$v['totalamount']/10000);
			// 			echo $v['totalamount']/10000;
			// 			echo "</br>";
		}
	
		//dump($yw_type);
		$this->pie($title, $yw_type, $amount);
	}
	
	
	public function department_type_pie($level,$id,$type,$start,$end){
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
						select  totable.yw_name,totable.br_id,totable.custno,br_name,
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
		$result_branch = $Model->query ( $sql_client );
		 $title=$result_branch[0]['br_name']."：国际结算业务量构成";
		$yw_type=array();
		$amount=array();
		foreach ($result_branch as $v){
			array_push($yw_type,iconv('utf-8','gb2312',substr($v['yw_name'],0,27)));
			array_push($amount,$v['totalamount']/10000);
			// 			echo $v['totalamount']/10000;
			// 			echo "</br>";
		}
	
		//dump($yw_type);
		$this->pie($title, $yw_type, $amount);
	}
	
	
	
	/* 
	 * 
	 * 输出客户结算量分月明细
	 * 
	 * */
	public  function client_js_month_barplot($c_id,$type,$start,$end){
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
		$title=$result[0]['name'];
		$yw_type='';
		if($type=='01'){
			$yw_type='国际结算量';
		}
		if($type=='02'){
			$yw_type='结售汇量';
		}
		if($type=='03'){
			$yw_type='贸易融资量';
		}
		
		$time = array (	);
		$amount = array ();
		$data3y = array ();
		foreach ($result as $v){
			array_push($time,$v['yw_month']);
			array_push($amount,$v['totalamount']/10000);
// 			echo $v['totalamount']/10000;
// 			echo "</br>";
		}
		
		//dump($amount);
		
		$this->barplot($title,$yw_type,$time,$amount);;
		
	}
	
	public function department_month_barplot($br_id,$type,$start,$end) {
		
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
		
		$title=$result[0]['br_name'];
		
		$title=$title."：国际结算量分月明细图";
	
		$time = array (	);
		$amount = array ();
		$data3y = array ();
		foreach ($result as $v){
			array_push($time,$v['yw_month']);
			array_push($amount,$v['totalamount']/10000);
		}
		
		$yw_type='';
		if($type=='01'){
			$yw_type='国际结算量';
		}
		if($type=='02'){
			$yw_type='结售汇量';
		}
		if($type=='03'){
			$yw_type='贸易融资量';
		}
		
	  $this->barplot($title,$yw_type,$time,$amount);
	}
	
	public function branch_month_barplot($br_id,$type,$start,$end) {
	
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
	
	
	
		$title=str_replace('本部','',$result[0]['br_name']);
		$title=$title."：国际结算量分月明细图";
		$time = array ();//时间轴，横轴
		$amount = array ();//业务量，纵轴
		foreach ($result as $v){
			array_push($time,$v['yw_month']);
			array_push($amount,$v['totalamount']/10000);
		}
		
		$yw_type='';
		if($type=='01'){
			$yw_type='国际结算量';
		}
		if($type=='02'){
			$yw_type='结售汇量';
		}
		if($type=='03'){
			$yw_type='贸易融资量';
		}
				
		$this->barplot($title,$yw_type,$time,$amount);
	}
	
	
	
	/*
	 * 函数名称：	barplot() 	绘画：柱状图
	 * 参数：
	 * $info 		图的名称，横轴和坚轴的名称
	 * $deriction 柱状图的方向
	 * $size 图片的大小
	 * $data 数据
	 */
	public function barplot($title,$yw_type,$time,$amount) {

		// Create the graph. These two calls are always required
		$graph = new \Graph ( 500, 280, 'auto' );
		$graph->SetScale ( "textlin" );
						
		
		$legendcolor=array('blue');
		$legend=new \Legend();
		$legend->Add($legenddate, $legendcolor);

		$theme_class = new \UniversalTheme ();
		$graph->SetTheme ( $theme_class );
		

		$graph->SetBox ( false );
		$graph->SetFrame(true) ;
		
		$graph->ygrid->SetFill ( false );
		$graph->xaxis->SetTickLabels ($time );
		//$graph->xaxis->title->Set(iconv('utf-8','gb2312','月'));
		$graph->xaxis->SetLabelAngle('45');
		$graph->yaxis->HideLine ( false );
		$graph->yaxis->HideTicks ( false, false );
		
		// Create the bar plots
		$b1plot = new \BarPlot ( $amount );
		
		$b1plot->SetLegend(iconv('utf-8','gb2312',$yw_type));
		$graph->SetMargin('50', '20', '30', '70');
		$graph->legend->Pos(0.5,0.99,"center","bottom");
	
		$graph->legend->SetFont(FF_SIMSUN,FS_NORMAL,9);

		// ...and add it to the graPH
		$graph->Add ( $b1plot );
		
		$b1plot->SetColor ( "white" );
		$b1plot->SetFillColor ( "green" );
		$b1plot->SetValuePos('top');
		$b1plot->value->SetFont(FF_SIMSUN,FS_NORMAL,9);
		$b1plot->value->SetFormat('%01.1f');
		$b1plot->value->show();
		
	
		
		$graph->title->Set ( iconv('utf-8','GBK',$title));
		$graph->subtitle->Set ( iconv('utf-8','gb2312',"(单位：万美元)"));
		$graph->subtitle->SetMargin('25');
		$graph->title->SetFont(FF_SIMSUN,FS_BOLD,18);
		$graph->subtitle->SetFont(FF_SIMSUN,FS_NORMAL,9);
		
		$graph->Stroke();

	}
	
	
	/*
	 * 函数名称：	pie() 	绘画：饼图
	 * 参数：
	 * $info 		图的名称，横轴和坚轴的名称
	 * $deriction 柱状图的方向
	 * $size 图片的大小
	 * $data 数据
	 */
	public function pie($title,$yw_type,$amount) {
		$data = $amount;
		//dump($yw_type);
		
		// Create the Pie Graph.
		$graph = new \PieGraph(550,280);
		$graph->SetShadow();
		$graph->SetFrame(true) ;
		
		
		// Set A title for the plot
		$graph->title->Set(iconv('utf-8','gb2312',$title));
		$graph->title->SetFont(FF_SIMSUN,FS_BOLD,18);
		$graph->title->SetColor("darkblue");
		$graph->title->SetAlign('left');
// 		$graph->subtitle->Set ( iconv('utf-8','gb2312',"(单位：万美元)"));
// 		$graph->subtitle->SetFont(FF_SIMSUN,FS_NORMAL,9);
		$graph->legend->Pos(0.01,0.68,'left','center');
		//$graph->legend->SetFrameWeight(2);
		//$graph->legend->SetAbsPos(0, 50);
		$graph->legend->SetLeftMargin(0);
		//$graph->legend->SetMarkAbsHSize(100);
		$graph->legend->SetFont(FF_SIMSUN,FS_BOLD,9);
		$graph->legend->SetLayout(LEGEND_VERT);
		$graph->legend->SetMarkAbsSize(6);
		$graph->legend->SetLineWeight(0.5);
	
		$graph->legend->SetReverse(true);
		
		
		// Create pie plot
		$p1 = new \PiePlot($data);
		
		//$p1->SetTheme("sand");
		$p1->SetCenter(0.1,0.3);
	
		//$p1->SetLabelType(PIE_VALUE_ABS);
		$p1->SetGuideLinesAdjust(1,0.8);
		$p1->SetGuideLines(true,true,false);
		$p1->SetSize(80);
		$p1->SetStartAngle(0);
		$p1->SetLabelPos(1.05);
		$p1->value->SetFont(FF_SIMSUN,FS_BOLD,9);
		//$p1->SetLegends(iconv('utf-8','gb2312',$yw_type));
		$p1->SetLegends($yw_type);
		
		
		//$p1->SetLegends($type);
		//dump($yw_type);
		$graph->Add($p1);
		
		$graph->Stroke();
	}




}
?>