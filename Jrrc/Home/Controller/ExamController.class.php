<?php

namespace Home\Controller;
/*
 * 这是一个考试类
 *
 *
 */
use Think\Controller;

class ExamController extends Controller {
	public function index() {
		$this->display ( 'index' );
	}
}