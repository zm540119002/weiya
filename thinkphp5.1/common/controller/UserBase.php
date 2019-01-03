<?php
namespace common\controller;
require_once dirname(__DIR__).'/component/payment/weixin/WxPay.JsApiPay.php';
/**用户信息验证控制器基类
 */
class UserBase extends Base{
    protected $user = null;
    protected $loginUrl = 'ucenter/UserCenter/login';//用户中心URL
    protected $indexUrl = 'Index/index';//首页URL
    
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

//        if ( strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false ){
//            $openId =  session('open_id');
//            if(empty($openId)){
//                $tools = new \JsApiPay();
//                $openId  = $tools->GetOpenid();
//                session('open_id',$openId);
//            }
//        }
    }
}