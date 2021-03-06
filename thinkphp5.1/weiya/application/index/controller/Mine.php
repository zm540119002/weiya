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
            return $this->errorMsg('请求方式错误');
        }
        $user = session('user');
        if(empty($user)){
            return $this->errorMsg('未登录');
        }
        $oldAvatar = $user['avatar'];
        $fileBase64 = input('post.fileBase64');
        $upload = config('upload_dir.user_avatar');

        $newAvatar = $this ->_uploadSingleFileToTemp($fileBase64,$upload);
        if($newAvatar['status'] == 0 && !$newAvatar){
            return $this->errorMsg('失败');
        }
        $user['avatar'] = $newAvatar;
        $modelUser = new \common\model\User();
        $result = $modelUser->allowField(['avatar'])->save($user, ['id' => $user['id']]);
        if(!$result){
            return $this->errorMsg('失败');
        }
        //删除旧详情图
        delImgFromPaths($oldAvatar,$newAvatar);
          setSession($user);
        return successMsg('成功',['avatar'=>$newAvatar]);
    }

    //修改名字
    public function editName(){
        if(!request()->isPost()){
            return $this->errorMsg('请求方式错误');
        }
        $modelUser = new \common\model\User();
        $user = session('user');
        $newName = preg_replace('# #','',input('post.name'));
        $user['name'] = $newName;
        $result = $modelUser->allowField(['name'])->save($user, ['id' => $user['id']]);
        if(!$result){
            return $this->errorMsg('失败');
        }
        setSession($user);
        return successMsg('成功',['name'=>$newName]);
    }
}