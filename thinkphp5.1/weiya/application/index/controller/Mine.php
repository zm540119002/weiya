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
        $user = session('user');
        if(empty($user)){
            return errorMsg('未登录');
        }
        $oldAvatar = $user['avatar'];
        $fileBase64 = input('post.fileBase64');
        $upload = config('upload_dir.user_avatar');
        $newAvatar = $this ->_uploadSingleFileToTemp($fileBase64,$upload);
        if($newAvatar['status'] == 0 && !$newAvatar){
            return errorMsg('失败');
        }
        $user['avatar'] = $newAvatar;
        $modelUser = new \common\model\User();
        $result = $modelUser->edit($user,true);
        if($result['status'] == 0){
            return errorMsg('失败');
        }
        //删除旧详情图
        delImgFromPaths($oldAvatar,$newAvatar);
        return successMsg('成功',['avatar'=>$newAvatar]);
    }

    //修改名字
    public function editName(){
        if(!request()->isPost()){
            return errorMsg('请求方式错误');
        }
        $modelUser = new \common\model\User();
        $user = session('user');
        $newName = preg_replace('# #','',input('post.name'));
        $user['avatar'] = $newName;
        $result = $modelUser->edit($user,true);
        if($result['status'] == 0){
            return errorMsg('失败');
        }
        return successMsg('成功',['name'=>$newName]);
    }
}