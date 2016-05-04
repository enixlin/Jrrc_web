<?php
namespace Home\Controller;


use Think\Controller;
class PageController extends Controller{
	public function index(){
		$credit = M('user');
		$count = $credit->count(); //计算记录数
		$limitRows = 5; // 设置每页记录数

		$p=new AjaxPage($count, $limitRows,"user",''); //第三个参数是你需要调用换页的ajax函数名
		$limit_value = $p->firstRow . "," . $p->listRows;
		 
		$data = $credit->order('id desc')->limit($limit_value)->select(); // 查询数据
		$page = $p->show(); // 产生分页信息，AJAX的连接在此处生成
		
		$this->assign('list',$data);
		$this->assign('page',$page);
		$this->display();
		
	}
}