<?php
namespace Home\Controller;
use Think\Controller;

class PageTestController extends Controller{
    public function user(){
          //import("Org.Util.AjaxPage");// 导入分页类  注意导入的是自己写的AjaxPage类
          $credit = M('phonebook');
          $count = $credit->count(); //计算记录数
        $limitRows = 15; // 设置每页记录数
       
        $p = new \Org\Util\AjaxPage($count, $limitRows,"user"); //第三个参数是你需要调用换页的ajax函数名
        $limit_value = $p->firstRow . "," . $p->listRows;
       
        $data = $credit->order('id desc')->limit($limit_value)->select(); // 查询数据
        $page = $p->show(); // 产生分页信息，AJAX的连接在此处生成
        
		//$this->ajaxReturn($data,'json');
        $this->assign('list',$data);
        $this->assign('page',$page);
        $this->display('index');

     }
}