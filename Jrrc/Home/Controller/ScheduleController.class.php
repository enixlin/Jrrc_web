<?php
namespace Home\Controller;


use Common\Controller\AuthController;
use Org\Util\Date;
class ScheduleController extends AuthController{
	public function index(){
		
	$uid=session('uid');
	$this->assign('uid',$uid);
	$this->display('index');
		
	}
	
	function createtable($uid,$year,$month){
		//
		$schedulelist=$this->mkschedulelist($uid, $year,$month);
		$day=date('d');
		$this->assign('uid',session('uid'));
		$this->assign('daylist',$schedulelist);
		$this->assign('month',$month);
		$this->assign('year',$year);
		$this->assign('day',$day);
		//dump($schedulelist);
		$this->display('createtable');
	}
	
	//取得提交的查询参数
	public function getarg(){
		$arg['title']=I('post.title');
		$arg['date']=I('post.date');
		$arg['content']=I('post.content');
		$arg['level']=I('post.level');
		$arg['is_public']=I('post.is_public');
		$arg['u_id']=session ( 'uid' );
		return $arg;
	}
	
	//
	public function showaddbox(){
		$this->assign('uid',session('uid'));
		$this->display('schedule_add');
	}
	

	public function showmodifybox($id){
	$schedule=M('schedule');
	$map['id']=$id;
	$result=$schedule->where($map)->select();
	//dump($result);
	$this->assign('result',$result);
	$this->display('schedule_modify');
	}
	public function add(){

		$this->handleAdd($this->getarg());
	}
	//处理添加记事
	public function handleAdd($arg){
		
		$schedule=M('schedule');
		$map=$arg;
		$result=$schedule->add($map);
		if($result!=0){
			echo $result;
		}else{
			echo 'false';
		}
	}
	
	//修改记事
	public function modify(){
		$id=I('post.id');
		$title=I('post.title');
		$date=I('post.date');
		$content=I('post.content');
		$level=I('post.level');
		$is_public=I('post.is_public');
		$u_id=session('uid');
		echo $this->handlemodify($id, $title, $date, $content, $level, $is_public, $u_id);
	}
	
	//处理修改记事
	public function handlemodify($id,$title,$date,$content,$level,$is_public,$u_id){
		$handleflag=0;
		
		$schedule=M('schedule');
		$data['id']=$id;
		$data['title']=$title;
		$data['date']=$date;
		$data['content']=$content;
		$data['level']=$level;
		$data['is_public']=$is_public;
		$data['u_id']=$u_id;
		
		$result=$schedule->save($data);
		
		if($result){
			$handleflag=1;
		}
		
		 return $handleflag;
		
	}
	
	public function delete(){
		 echo $this->handledelete(I('post.id'));
	}
	
	//处理删除记事
	public function handledelete($id){
		$handleflag=null;
		$Schedule=M('schedule');
		$result=$Schedule->where('id='.$id)->find();
		if(session('uid')!=$result['u_id']){
			$handleflag= '没有权限删除';
		}else{
			if($Schedule->where('id='.$id)->delete()){
				$handleflag= '删除成功';
			}else{
				$handleflag= '删除失败';
			}
		}
		return $handleflag;
	}
	

	
	function getmarks($uid,$startday,$endday){
		$schedule=D('schedule');
		$data['date']=array('elt',$endday);
		$data['date']=array('egt',$startday);
		$data['u_id']=$uid;
		$marks=$schedule->relation(true)->where($data)->select();
		return $marks;
	}
	
	function exportmarks($startday,$endday){
		$schedule=D('schedule');
		$data['date']=array('elt',$endday);
		$data['date']=array('egt',$startday);
		$marks=$schedule->relation(true)->where($data)->select();
		return $marks;
	}
	
	function mkdatelist($year,$month){
		//定义一个月表格中的第一天和最后一天的范围
		//本月第一天的时间戳
		$monthfirstday=mktime(null,null,null,$month,1,$year);
		//第一天是周几
		$wday=date('N',$monthfirstday);
		//月表格的第一天
		if($wday==1){
			$firstday=$monthfirstday-(24*7*3600);
		}else{
			$firstday=$monthfirstday-(($wday-1)*24*3600);
		}
		//月表格的最后一天的时间戳
		$lastday=$firstday+41*24*3600;
		
		$today=time();
		$datelist=array();
		for($i=$firstday;$i<=$lastday;){
			array_push($datelist,date('Y-m-d',$i));
			$i=$i+24*3600;
		}
		//var_dump($arr);
		
		return $datelist;
	}
	
	function mkschedulelist($uid,$year,$month){
		//生成当月的日程表
		$datelist=$this->mkdatelist($year, $month);
		//dump($datelist);
		$marks=$this->getmarks($uid, $datelist[0], $datelist[41]);
		//dump($marks);
		$schedulelist=array();
		
		for ($i=0;$i<count($datelist);$i++){
			$schedulelist[$datelist[$i]]=array();
			for($j=0;$j<count($marks);$j++){
				
				if($datelist[$i]==$marks[$j]['date']){
					array_push($schedulelist[$marks[$j]['date']],$marks[$j]);				
				}
			}
		}
		
		//dump($schedulelist);
		return $schedulelist;
		
	}
	
}

