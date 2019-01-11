<?php
namespace app\index_admin\controller;

class Index extends Base
{
    //首页
    public function index(){
        echo 123;exit;
        return $this->fetch();
    }
}