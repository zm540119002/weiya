<?php
namespace app\index\controller;

class Consultation extends \common\controller\Base{
    /**首页
     */
    public function index(){
        return $this->fetch();
    }

    /**
     * 提交需求留言
     */
    public function addNeedsMessage(){
        if(!request()->isPost()){
            return errorMsg('请求方式错误');
        }
        $data = input('post.');
        $model = new \app\index\model\NeedsMessage();
        $result = $model -> isUpdate(false)->save($data);
        if (!$result){
            return errorMsg('失败');
        }else{
            return successMsg('已提交，等待电话回复，谢谢~');
        }
    }
}