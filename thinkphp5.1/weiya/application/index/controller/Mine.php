<?php
namespace app\index\controller;

class Mine extends \common\controller\Base{
    //我的首页
    public function index(){
        $this->assign('user',session('user'));
        return $this->fetch();
    }

    //修改头像
    public function editAvatar(){
        if(!request()->isPost()){
            return errorMsg('请求方式错误');
        }
        $fileBase64 = input('post.fileBase64');
        $upload = config('upload_dir.user_avatar');
        $userAvatar = $this ->_uploadSingleFileToTemp($fileBase64,$upload);
        if(isset($userAvatar['status']) &&$userAvatar['status'] == 0){
            return errorMsg('失败');
        }
        $user = session('user');
        $oldAvatar = $user['avatar'];
        $modelUser = new \common\model\User();
        $data = [
            'id'=>$user['id'],
            'avatar'=>$userAvatar,
        ];
        $result = $modelUser -> isUpdate(true)->save($data);
        if(false === $result){
            return errorMsg('失败');
        }
        $user['avatar'] = $userAvatar;
        $modelUserCenter = new \common\model\UserCenter();
        $modelUserCenter->_setSession($user);
        if($user['avatar']){
            //删除旧详情图
            delImgFromPaths($oldAvatar,$userAvatar);
        }
        return successMsg('成功',['avatar'=>$userAvatar]);
    }

    //修改名字
    public function editName(){
        if(!request()->isPost()){
            return errorMsg('请求方式错误');
        }
        $modelUser = new \common\model\User();
        $user = session('user');
        $name = preg_replace('# #','',input('post.name'));
        $data = [
            'id'=>$user['id'],
            'name'=>$name,
        ];
        $result = $modelUser -> isUpdate(true)->save($data);
        if(false === $result){
            return errorMsg('失败');
        }

        $modelUserCenter = new \common\model\UserCenter();
        $user['name'] = $name;
        $modelUserCenter->_setSession($user);
        return successMsg('成功',['name'=>$name]);
    }
}