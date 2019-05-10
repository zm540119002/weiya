<?php
namespace common\controller;
/**用户信息验证控制器基类
 */
class UserBase extends Base{
    protected $user = null;
    protected $indexUrl = 'index/Index/index';//首页URL
    
    public function __construct(){
        parent::__construct();
        //判断是否登录
        $this->user = checkLogin();
        if (!$this->user) {
            if(request()->isAjax()){
                $this->errorMsg(config('error_code.no_login.explain'),config('error_code.no_login'));
            }else{
                echo $this->fetch('../../api/public/template/login_page.html');
                exit;
            }
        }
        //判断是否为微信浏览器
//        if(isWxBrowser() && !request()->isAjax()) {
//            if(!$this -> user['weiya_openid']){
//                $weiXinUserInfo = session('weiXinUserInfo');
//                //临时相对路径
//                $tempRelativePath = config('upload_dir.user_avatar');
//                $weiXinAvatarUrl = $weiXinUserInfo['headimgurl'];
//                $avatar = saveImageFromHttp($weiXinAvatarUrl,$tempRelativePath);
//                $data = [
//                    'id'=>$this->user['id'],
//                    'name'=>$weiXinUserInfo['nickname'],
//                    'avatar'=>$avatar,
//                    'weiya_openid'=>$weiXinUserInfo['openid'],
//                ];
//                if($this->user['avatar']){
//                    unset($data['avatar']);
//                }
//                if($this->user['name']){
//                    unset($data['name']);
//                }
//                $userModel = new \common\model\User();
//                $result = $userModel->isUpdate(true)->save($data);
//                if( false === $result){
//                    return errorMsg('添加微信信息失败');
//                }
//            }
//        }
    }
}