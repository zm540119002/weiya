<?php
namespace app\index_admin\controller;

class Index extends \common\controller\UserBaseAdmin{
    //首页
    public function index(){
        return $this->fetch();
    }
    //欢迎页
    public function welcome(){
        return $this->fetch();
    }
}