<?php
namespace app\index\controller;

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
        session('user', null);
        session('user_sign', null);
        session('currentStoreId', null);
        session('currentStoreId',null,'factory_');
        session('currentStoreId',null,'store_');
        header('Content-type: text/html; charset=utf-8');
        return redirect('store/Index/index');
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

    //用户中心
    public function index(){
        $a = request()->domain();
        print_r($a);exit;
        $this->assign('user',session('user'));
        return $this->fetch();
    }
}