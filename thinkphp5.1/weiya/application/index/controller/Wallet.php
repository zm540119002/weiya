<?php
namespace app\index\controller;

class Wallet extends \common\controller\UserBase{
    /**首页
     */
    public function index(){
        return $this->fetch();
    }

    /**登录
     */
    public function login(){
        if (request()->isAjax()) {
            $modelUser = new \common\model\UserCenter();
            $postData = input('post.');
            return $modelUser->login($postData);
        } else {
            return $this->fetch();
        }
    }
    
    /**忘记密码 /注册
     */
    public function forgetPassword(){
        if (request()->isAjax()) {
            $modelUser = new \common\model\UserCenter();
            $postData = input('post.');
            return $modelUser->resetPassword($postData);
        } else {
            return $this->fetch();
        }
    }
}