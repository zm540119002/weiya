<?php
namespace app\ucenter\controller;
class UserCenter extends \think\Controller{
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
    /**后台登录
     */
    public function login_admin(){
        if (request()->isAjax()) {
            $modelUser = new \common\model\UserCenter();
            $postData = input('post.');
            return $modelUser->login($postData);
        } else {
            return $this->fetch();
        }
    }
    /**注册
     */
    public function register(){
        if (request()->isAjax()) {
            $modelUser = new \common\model\UserCenter();
            $postData = input('post.');
            return $modelUser->register($postData);
        }
    }
    /**忘记密码
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
    //退出
    public function logout(){
        session(null);
        header('Content-type: text/html; charset=utf-8');
        return successMsg('成功');
        return redirect('login');
    }
    /*发送验证码
     */
    public function sendSms(){
        if (!(request()->isPost())) {
            return config('custom.not_post');
        }
        $mobilePhone = input('post.mobile_phone',0);
        $captcha = create_random_str();
        $config = array(
            'mobilePhone' => $mobilePhone,
            'smsSignName' => config('custom.sms_sign_name'),
            'smsTemplateCode' => config('custom.sms_template_code'),
            'captcha' => $captcha,
        );
        $response = \common\lib\Sms::sendSms($config);
        if('OK'!==$response->Code){
            if('BUSINESS_LIMIT_CONTROL'===$response->Code){
                return errorMsg('同一个手机号码发送短信验证码，支持1条/分钟，5条/小时 ，累计10条/天。');
            }
            return errorMsg($response->Message);
        }
        //设置session
        session('captcha_'.$mobilePhone,$captcha);
        return successMsg($response->Message);
    }
}