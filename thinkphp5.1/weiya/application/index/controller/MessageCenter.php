<?php
namespace app\index\controller;

class MessageCenter extends \common\controller\UserBase{
    /**首页
     */
    public function index(){

        return $this->fetch();
    }

   
}