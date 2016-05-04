<?php

/*
 * 政策文件查询类
 */
namespace Home\Controller;

use Common\Controller\AuthController;

class PolicyController extends AuthController {
	// 显示查询页面
	public function index() {
		$this->display ( 'search' );
	}
	// 接收查询参数
	public function getSearchArg() {
		$data = $_GET;
		return $data;
	}
	// 公文查询
	public function search() {
		$this->searchhandle ( $this->getSearchArg () );
	}
	
	// 处理公文查询
	public function searchhandle($data) {
		$Policy = M ( 'Document' );
		if($data ['d_title']!=''){
				$map ['d_title'] = array (
					'like',
					"%" . $data ['d_title'] . "%"
			);
		}

		if($data ['d_issue_num']!=''){
			$map ['d_issue_num'] = array (
					'like',
					"%" . $data ['d_issue_num'] . "%"
			);
		}
		if($data ['d_content']!=''){
			$map ['d_content'] = array (
					'like',
					"%" . $data ['d_content'] . "%"
			);
		}

		$count = $Policy->where ( $map )->count(); // 计算记录数
		$limitRows = 12; // 设置每页记录数
		
		$p = new \Org\Util\AjaxPage ( $count, $limitRows, "search" ); // 第三个参数是你需要调用换页的ajax函数名
		$limit_value = $p->firstRow . "," . $p->listRows;
		
		//$data = $Policy->where ( $map )->order ( 'id desc' )->field ( "d_title,d_issue_num,d_issue_date" )->select (); // 查询数据
		$data = $Policy->where ( $map )->field ( "d_title,d_issue_num,d_issue_date,d_state" )->order ( 'd_issue_date desc' )->limit($limit_value)->select (); // 查询数据
		$page = $p->show (); // 产生分页信息，AJAX的连接在此处生成
		//dump ( $data );
		// $this->ajaxReturn($data,'json');
		$this->assign ( 'list', $data );
		$this->assign ( 'page', $page );
		$this->display ( 'list' );
	}
	
	// 按公文标题查询
	public function findbytitle($title) {
		$Policy = M ( 'Document' );
// 		$map ['d_title'] = array (
// 				'like',
// 				"%" . $title . "%" 
// 		);

		$map['d_title']=$title;
		$result = $Policy->where ( $map )->field ( "d_content" )->select();
	
		// 将html文体中的非utf8 charset编码转为utf8
		//$str=$result['d_content'];
		$str = str_replace ( 'x-mac-chinesesimp', 'utf8', $result [0] ['d_content'] );
		$str = str_replace ( 'gb2312', 'utf8', $str );
		$str = str_replace ( '【标 题】国家外汇管理局关于合作办理远期结售汇业务有关问题的通知', '', $str );
		$str = str_replace ( 'gbk', 'utf8', $str );
		echo $str;
	}
}