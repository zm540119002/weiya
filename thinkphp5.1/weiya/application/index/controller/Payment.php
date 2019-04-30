<?php
namespace app\index\controller;
class Payment extends \common\controller\UserBase{

   //支付完跳转的页面
    public function payComplete(){
        return $this->fetch();
    }

    //取消支付完跳转的页面
    public function payCancel(){
        return $this->fetch();
    }

    //支付失败完跳转的页面
    public function payFail(){
        return $this->fetch();
    }

}