<?php
namespace common\controller;
/**用户信息验证控制器基类
 */
class UserBaseApi extends BaseApi{
    protected $user = null;
    protected $indexUrl = 'index/Index/index';//首页URL
    
    public function __construct(){
        parent::__construct();
        //判断是否登录
        $rst = isLogin();
        if($rst['code'] == 1){
            $this->user = $rst['user'];
        }else{
            echo json_encode($rst);exit;
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
//                    return $this->errorMsg('添加微信信息失败');
//                }
//            }
//        }
    }
}