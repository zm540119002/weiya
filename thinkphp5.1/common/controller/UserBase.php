<?php
namespace common\controller;
use think\facade\Session;
/**用户信息验证控制器基类
 */
class UserBase extends Base{
    protected $user = null;
    protected $loginUrl = 'ucenter/UserCenter/login';//用户中心URL
    protected $indexUrl = 'index/Index/index';//首页URL
    
    public function __construct(){
        parent::__construct();
        //判断是否登录
        $this->user = checkLogin();
        if (!$this->user) {
            if (request()->isAjax()) {
                $this->success('异步登录失败',url($this->indexUrl),'no_login',0);
            }else{
                $this->error(config('custom.error_login'),url($this->loginUrl));
            }
        }
        if(isWxBrowser() && !request()->isAjax()) {//判断是否为微信浏览器
//            $payOpenId =  session('pay_open_id','');
//            if(empty($payOpenId)){
//                $tools = new \common\component\payment\weixin\getPayOpenId(config('wx_config.appid'), config('wx_config.appsecret'));
//                $payOpenId  = $tools->getOpenid();
//                session('pay_open_id',$payOpenId);
//            }
            print_r(config('weiya_weixin.appid'));
            print_r(config('weiya_weixin.appsecret'));
exit;
            $mineTools = new \common\component\payment\weixin\Jssdk(config('weiya_weixin.appid'), config('weiya_weixin.appsecret'));
            $weiXinUserInfo = $mineTools->get_user_info($mineTools->getOpenid());
            print_r($weiXinUserInfo);exit;
        }

    }
}