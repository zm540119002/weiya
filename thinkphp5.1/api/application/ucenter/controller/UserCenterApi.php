<?php
namespace app\ucenter\controller;

class UserCenterAPi extends \common\controller\BaseApi{
    /**登录
     */
    public function login(){
        if (request()->isPost()) {
            $modelUser = new \common\model\UserCenterApi();
            $data = input('post.');
            $data = $data['data'];
            return  $modelUser->login($data);
        }
    }
    /**注册
     */
    public function register(){
        if (request()->isPost()) {
            $modelUser = new \common\model\UserCenterApi();
            $data = input('post.');
            $data = $data['data'];
            return $modelUser->register($data);
        }
    }

    //退出
    public function logout(){
        if (!request()->isPost()) {
           return buildFailed('请求方式错误');
        }
        $a = request()->header();
        print_r($a);exit;
        $data = input('post.');
        $token = $data['data']['token'];
        cache('Login:' . $token,null);
        return buildSuccess([],'退出成功');
    }
    /*发送验证码
     */
    public function sendSms(){
        if (!(request()->isPost())) {
            return config('custom.not_post');
        }
        $data = input('post.');
        $mobilePhone = $data['data']['mobile_phone'];
        $captcha = create_random_str(4);
        $config = array(
            'mobilePhone' => $mobilePhone,
            'smsSignName' => config('custom.sms_sign_name'),
            'smsTemplateCode' => config('custom.sms_template_code'),
            'captcha' => $captcha,
        );
        $response = \common\lib\Sms::sendSms($config);
        if('OK'!==$response->Code){
            if('BUSINESS_LIMIT_CONTROL'===$response->Code){
                return buildFailed('同一个手机号码发送短信验证码，支持1条/分钟，5条/小时 ，累计10条/天。');
            }
            return buildFailed($response->Message);
        }
        //设置session
        session('captcha_'.$mobilePhone,$captcha);
        return buildSuccess($response->Message);
    }

}