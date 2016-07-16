<?php
namespace  Home\Controller;


use Think\Controller;
class TestController extends Controller{
	
	public function index(){
		$model=M();
		
		$sql="select * from jrrc_ywls";
		$time_s=microtime();
		$result=$model->query($sql);
		$time_e=microtime();
		echo "start time:".$time_s."</br>";
		echo  "end time:".$time_e."</br>";
	}
}