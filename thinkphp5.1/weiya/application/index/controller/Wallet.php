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
            $model = new \app\index\model\Wallet();;
            $postData = input('post.');
            $postData['user_id'] = $this->user['id'];
            return $model->login($postData);
        } else {
            return $this->fetch();
        }
    }
    
    /**忘记密码 /注册
     */
    public function forgetPassword(){
        if (request()->isAjax()) {
            $model = new \app\index\model\Wallet();;
            $postData = input('post.');
            $postData['user_id'] = $this->user['id'];
            return $model->resetPassword($postData);
        } else {
            return $this->fetch();
        }
    }

    /**
     * 钱包充值页面
     */
    public function recharge(){
        if (request()->isAjax()) {
        } else {
            if(isWxBrowser() && !request()->isAjax()) {//判断是否为微信浏览器
                $payOpenId =  session('pay_open_id');
                if(empty($payOpenId)){
                    $tools = new \common\component\payment\weixin\getPayOpenId(config('wx_config.appid'), config('wx_config.appsecret'));
                    $payOpenId  = $tools->getOpenid();
                    session('pay_open_id',$payOpenId);
                }
            }
            return $this->fetch();
        }
    }
}