<?php
namespace app\weiya\controller;

class Goods extends \common\controller\BaseApi{
    /**商品详情页
     */
    public function getInfo(){
        if(!request()->isGet()){
            return buildFailed(config('custom.not_get'));
        }
        $id = intval(input('id'));
        if(!$id){
            return buildFailed(config('custom.parameter_error'));
        }
        $model = new \app\weiya\model\Goods();
        $config =[
            'where' => [
                ['g.status', '=', 0],
                ['g.shelf_status', '=', 3],
                ['g.id', '=', $id],
            ],
        ];
        $info = $model->getInfo($config);
        if(empty($info)){
            return buildFailed('此商品已下架');
        }
        $info['main_img'] = explode(',',(string)$info['main_img']);
        $info['detail_img'] = explode(',',(string)$info['detail_img']);
        $info['tag'] = explode(',',(string)$info['tag']);
        return buildSuccess($info);
    }
    /**
     * 查出产商相关产品 分页查询
     */
    public function getList(){
        if(!request()->isGet()){
            return buildFailed(config('custom.not_get'));
        }
        $model = new \app\weiya\model\Goods();
        $config=[
            'where'=>[
                ['g.status', '=', 0],
                ['g.shelf_status', '=', 3],
            ],
            'field'=>[
                'g.id ','g.headline','g.thumb_img','g.bulk_price','g.sample_price','g.specification','g.minimum_order_quantity',
                'g.minimum_sample_quantity','g.increase_quantity','g.purchase_unit'
            ],
            'order'=>[
                'is_selection'=>'desc',
                'sort'=>'desc',
                'id'=>'desc'
            ],
        ];
        if(input('?get.category_id') && input('get.category_id/d')){
            $config['where'][] = ['g.category_id_1', '=', input('get.category_id/d')];
        }
        $keyword = input('get.keyword','');
        if($keyword) {
            $config['where'][] = ['name', 'like', '%' . trim($keyword) . '%'];
        }
        
        $list = $model -> pageQuery($config);
        return buildSuccess($list);
    }

    //分类相关的商品
    public function goodsWitchCategory(){
        $categoryId = intval(input('category_id'));
        if(!$categoryId){
            $this->error('没有此分类');
        }
        $modelGoodsCategory = new \app\index\model\GoodsCategory();
        $config =[
            'where' => [
                ['gc.status', '=', 0],
                ['gc.id', '=', $categoryId],
                ['gc.level','=',1]
            ],
        ];
        $info = $modelGoodsCategory->getInfo($config);
        $info['tag'] = explode(',',(string)$info['tag']);
        if(empty($info)){
            $this->error('没有此分类');
        }
        $this->assign('info',$info);

        //获取相关的商品
        $model = new \app\index\model\Goods();
        $config =[
            'where' => [
                ['g.status', '=', 0],
                ['g.category_id_1', '=', $categoryId],
                ['g.shelf_status', '=', 3],
            ],'field'=>[
                'g.id ','g.headline','g.thumb_img','g.bulk_price','g.specification','g.minimum_order_quantity',
                'g.minimum_sample_quantity','g.increase_quantity','g.purchase_unit'
            ],
        ];
        $goodsList= $model->getList($config);
        $this->assign('goodsList',$goodsList);
        $unlockingFooterCart = unlockingFooterCartConfig([0,2,1]);
        $this->assign('unlockingFooterCart', $unlockingFooterCart);
        return $this->fetch();
    }

    /**获取推荐商品
     * @return array|\think\response\View
     */
    public function getRecommendGoods(){
        if(!request()->isGet()){
            return $this->errorMsg('请求方式错误');
        }
        $goodsId = input('get.goods_id/d');
        //相关推荐商品
        $modelRecommendGoods = new \app\index\model\RecommendGoods();
        $config =[
            'where' => [
                ['rg.status', '=', 0],
                ['rg.goods_id', '=', $goodsId],
            ],'field'=>[
                'g.id ','g.headline','g.thumb_img','g.bulk_price','g.specification','g.minimum_order_quantity',
                'g.minimum_sample_quantity','g.increase_quantity','g.purchase_unit'
            ],'join'=>[
                ['goods g','g.id = rg.recommend_goods_id','left']
            ]
        ];
        $list = $modelRecommendGoods->getList($config);
        $this->assign('list',$list);
        $this->assign('goodsList',json_encode($list));
        return view('goods/recommend_list_tpl');
    }
}