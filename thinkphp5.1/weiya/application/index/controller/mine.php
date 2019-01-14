<?php
namespace app\index\controller;

class Mine extends \think\Controller{

    //用户中心
    public function index(){

        
        $this->assign('user',session('user'));
        return $this->fetch();
    }
}