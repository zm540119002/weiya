<?php
namespace app\index\controller;

class Wallet extends \common\controller\UserBase{
    /**首页
     */
    public function index(){
        return $this->fetch();
    }

    public function recharge(){
        if (request()->isPost()) {
           //
        }else{
            return $this->fetch();
        }


    }
}