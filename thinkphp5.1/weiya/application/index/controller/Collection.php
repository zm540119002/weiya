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
            ] ,'field'=>[
                'id'
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
            return successMsg('已成功收藏');
        }else{
            return errorMsg('收藏失败');
        }
    }


    /**
     * @return array|mixed
     * 查出产商相关收藏 分页查询
     */
    public function getList(){
        if(!request()->isGet()){
            return errorMsg('请求方式错误');
        }
        $model = new \app\index\model\Collection();
        $config=[
            'where'=>[
                ['co.status', '=', 0],
                ['co.user_id', '=', $this->user['id']],
            ],
            'field'=>[
                'co.id','co.goods_id','g.headline','g.thumb_img','g.bulk_price','g.specification', 'g.purchase_unit'

            ], 'join'=>[
                ['goods g','g.id = co.goods_id','left'],
            ],'order'=>[
                'co.id'=>'desc'
            ]

        ];
        if(input('?get.category_id') && input('get.category_id/d')){
            $config['where'][] = ['o.category_id_1', '=', input('get.category_id/d')];
        }
        $keyword = input('get.keyword','');
        if($keyword) {
            $config['where'][] = ['o.name', 'like', '%' . trim($keyword) . '%'];
        }

        $list = $model -> pageQuery($config);
        $currentPage = input('get.page/d');
        $this->assign('currentPage',$currentPage);
        $this->assign('list',$list);
        if(isset($_GET['pageType'])){
            $pageType = $_GET['pageType'];
            return $this->fetch($pageType);
        }
    }

    //删除
    public function del(){
        if(!request()->isAjax()){
            return errorMsg(config('custom.not_ajax'));
        }
        $ids = input('post.ids/a');
        $model = new \app\index\model\Collection();
        $condition = [
            ['user_id','=',$this->user['id']],
            ['goods_id','in',$ids],
        ];
        $result = $model -> del($condition);
        if($result['status']){
            return successMsg('已取消收藏');
        }else{
            return errorMsg('删除失败');
        }
    }
}