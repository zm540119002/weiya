<?php
namespace app\index\controller;

class Collection extends \common\controller\UserBase{
    /**首页
     */
    public function index(){
        $unlockingFooterCart = unlockingFooterCartConfig([10,17]);
        $this->assign('unlockingFooterCart', $unlockingFooterCart);
        return $this->fetch();
    }

    /**
     * 收藏
     */
    public function collect(){
        if(!request()->isAjax()){
            return errorMsg('请求方式错误');
        }
        $goodsId = input('post.goods_id/d');
        if(!$goodsId){
            return errorMsg('参数错误');
        }
        $model = new \app\index\model\Collection();
        $config = [
            'where'=>[
                ['user_id','=',$this->user['id']],
                ['goods_id','=',$goodsId],
                ['status','=',0]
            ]
        ];
        $info = $model -> getInfo($config);
        if(count($info)){
            return successMsg('收藏成功');
        }
        $data = [
            'user_id'=>$this->user['id'],
            'goods_id'=>$goodsId,
            'create_time'=>time(),
        ];
        $result = $model -> isUpdate(false) -> save($data);
        if($result){
            return successMsg('收藏成功');
        }else{
            return errorMsg('收藏失败');
        }
        
    }
}