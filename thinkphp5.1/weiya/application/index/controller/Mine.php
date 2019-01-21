<?php
namespace app\index\controller;

class Mine extends \common\controller\Base{
    //我的首页
    public function index(){
        $this->assign('user',session('user','',config('custom.session_prefix')));
        return $this->fetch();
    }
}