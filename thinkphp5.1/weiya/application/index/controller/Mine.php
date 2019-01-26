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
        $user = session('user');
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
        session('user',$user);
        if($user['avatar']){
            //删除旧详情图
            delImgFromPaths($user['avatar'],$userAvatar);
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
        $name = input('post.name');
        $data = [
            'id'=>$user['id'],
            'name'=>$name,
        ];
        $result = $modelUser -> isUpdate(true)->save($data);
        if(false === $result){
            return errorMsg('失败');
        }
        $user['name'] = $name;
        session('user',$user);
        return successMsg('成功',['name'=>$name]);
    }
}