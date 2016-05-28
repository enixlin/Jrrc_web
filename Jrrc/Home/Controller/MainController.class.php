<?php

namespace Home\Controller;

use Think\Controller;

class MainController extends Controller {
	public function Index() {
		// echo session ( 'name' );
		// 检查SESSION,如果没有信息就跳转登录页面
		if (session ( 'name' ) == null) {
			$this->error ( '你还没有登录', "/Jrrc_web/Home/Login/" );
		} else {
			// session信息存在，进入用户主功能页面
			$this->success ( '正在转至主页面', '/jrrc_web/Home/Main/initialize' );
		}
	}
	
	// 显示最新的业务概览
	public function showLastDayMark() {
		// 如果是查询用户的话，不执行查询
		if (session ( 'name' ) == 'guest') {
			return;
		}
		$model = M ( "ywls" );
		// 取得业务流水表里面最新的业务日期
		$LastDay = $model->where ( "type=01" )->Max ( 'yw_date' );
		// dump($LastDay);
		$condition ['type'] = '01';
		// $condition['yw_date']=array(array('egt',substr($LastDay,0,4).'0101'),array('elt',$LastDay));
		$condition ['yw_date'] = $LastDay;
		$condition ['yw_type'] = array (
				array (
						'neq',
						'lr' 
				),
				array (
						'neq',
						'ib' 
				),
				array (
						'neq',
						'la' 
				) 
		);
		
		$map ['type'] = '03';
		$map ['yw_date'] = $LastDay;
		$map ['yw_stat'] = '4a';
		
		// 取得最后一天的所有结算业务流水
		$LastDayJs = $model->where ( $condition )->order ( 'name,yw_type,usdamt' )->select ();
		// 取得最后一天的国际结算业务总量
		$LastDayJsAmount = $model->where ( $condition )->sum ( 'usdamt' );
		$LastDayJsCount = $model->where ( $condition )->count ( 'usdamt' );
		
		// 取得最后一天的所有融资业务流水
		$LastDayTf = $model->where ( $map )->order ( 'name,yw_type,usdamt' )->select ();
		// 取得最后一天的融资业务总量
		$LastDayTfAmount = $model->where ( $map )->sum ( 'usdamt' );
		$LastDayTfCount = $model->where ( $map )->count ( 'usdamt' );
		
		// 贸易融资业务余额信息
		$tf_balance=M('tf')->sum('remain_amount');
		// 客户贸易融资业务余额分户明细信息
		$tf_client_balance=M('tf')->where('remain_amount>0')->field("client_number,client_name,sum(remain_amount) as sum")->group('client_number')->select();
		// 未来15天贸易融资明细提醒
		//echo "今天是：".(date('Ymd',time()));
		$tf_next15day_detail=M('tf')->where("remain_amount>0 and r_date>=".date('Ymd')." and r_date<=".(date('Ymd',time()+24*3600*15)))->order('r_date,client_name')->select();
		//dump($tf_balance);
		//dump($tf_client_balance);
		//dump($tf_next15day_detail);
	
		
		$this->assign ( 'LastDay', $LastDay );
		$this->assign ( "js_list", $LastDayJs );
		$this->assign ( "js_amount", $LastDayJsAmount );
		$this->assign ( "js_count", $LastDayJsCount );
		$this->assign ( "tf_list", $LastDayTf );
		$this->assign ( "tf_amount", $LastDayTfAmount );
		$this->assign ( "tf_count", $LastDayTfCount );
		$this->assign ( "tf_balance", $tf_balance );
		$this->assign ( "tf_client_balance", $tf_client_balance );
		$this->assign ( "tf_next15day_detail", $tf_next15day_detail );
		
		$this->display ( 'lastDayInfo' );
	}
	public function initialize() {
		// 初始化用户菜单
		// 根据不同的用户权限组，加载不同的功能菜单
		$User = D ( 'User' );
		$data ['id'] = session ( 'uid' );
		$Menu = $User->relation ( 'auth_group' )->where ( $data )->select ();
		$AuthRule = M ( 'auth_rule' );
		$map ['id'] = array (
				'IN',
				$Menu [0] ['auth_group'] [0] ['rules'] 
		);
		$menuList = $AuthRule->where ( $map )->order ( 'id asc' )->select ();
		
		// 根据将二级功能插入到一个数组中
		$array1 = array (); // 一级功能
		$array2 = array (); // 二级功能
		$arrayall = array (); // 全部的功能
		                      
		// 根level字段将查询出来的行存入一级和二级数组
		foreach ( $menuList as $key => $v ) {
			// echo $key."---value--:".$v."</br>";
			if ($v ['level'] == '1') {
				array_push ( $array1, $v );
			}
			if ($v ['level'] == '2') {
				array_push ( $array2, $v );
			}
		}
		// 将一级和二级的功能合并成一个数组
		foreach ( $array1 as $v ) {
			$arraycombine = array ();
			foreach ( $array2 as $va ) {
				if ($v ['pid'] == $va ['pid']) {
					array_push ( $arraycombine, $va );
				}
			}
			array_push ( $v, $arraycombine );
			array_push ( $arrayall, $v );
		}
		$menuList = $arrayall;
		
		$name = session ( 'name' );
		$this->assign ( 'name', $name );
		$this->assign ( 'menu', $menuList );
		
		$this->display ( 'main' );
	}
}