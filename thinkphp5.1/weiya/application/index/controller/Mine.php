<?php
namespace app\index\controller;

class Mine extends \think\Controller{
    //我的首页
    public function index(){
        $this->assign('user',session('user'));
        return $this->fetch();
    }
}