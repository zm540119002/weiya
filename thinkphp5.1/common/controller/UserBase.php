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
        //判断是否为微信浏览器 没有
        if(isWxBrowser() && !request()->isAjax()) {
            if(!$this -> user['weiya_openid']){
                $weiXinUserInfo = session('weiXinUserInfo');
                //临时相对路径
                $tempRelativePath = config('upload_dir.user_avatar');
                $weiXinAvatarUrl = $weiXinUserInfo['headimgurl'];
                $avatar = saveImageFromHttp($weiXinAvatarUrl,$tempRelativePath);
                $data = [
                    'id'=>$this->user['id'],
                    'name'=>$weiXinUserInfo['nickname'],
                    'avatar'=>$avatar,
                    'weiya_openid'=>$weiXinUserInfo['openid'],
                ];
                if($this->user['avatar']){
                    unset($data['avatar']);
                }
                if($this->user['name']){
                    unset($data['name']);
                }
                $userModel = new \common\model\User();
                $result = $userModel->isUpdate(true)->save($data);
                if( false === $result){
                    return errorMsg('添加微信信息失败');
                }
            }
        }
    }
    
}