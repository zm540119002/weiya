<?php
namespace common\controller;

/**用户信息验证控制器基类
 */
class UserBaseAdmin extends Base{
    protected $user = null;
    protected $loginUrl = 'ucenter/UserCenterAdmin/login';//用户中心URL
    protected $indexUrl = 'Index/index';//首页URL
    
    public function __construct(){
        parent::__construct();
        //判断是否登录
        $this->user = checkLogin();
        print_r($this->user );exit;
        if (!$this->user) {
            if (request()->isAjax()) {
                $this->success('异步登录失败',url($this->indexUrl),'no_login',0);
            }else{
                $this->error(config('custom.error_login'),url($this->loginUrl));
            }
        }
    }
}