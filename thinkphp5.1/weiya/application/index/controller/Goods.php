<?php
namespace app\index\controller;

class Goods extends \common\controller\Base{
    /**首页
     */
    public function index(){
        if(request()->isAjax()){
        }else{
            return $this->fetch();
        }
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

    /**
     * 查出产商相关产品 分页查询
     */
    public function getList(){
        if(!request()->isGet()){
            return errorMsg('请求方式错误');
        }
        $model = new \app\index\model\Goods();
        $config=[
            'where'=>[
                ['g.status', '=', 0],
                ['g.shelf_status', '=', 3],
            ],
            'field'=>[
                'g.id ','g.headline','g.thumb_img','g.bulk_price','g.specification','g.minimum_order_quantity',
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
        $this->assign('list',$list);
        if(isset($_GET['pageType'])){
            if($_GET['pageType'] == 'index' ){
                return $this->fetch('list_index_tpl');
            }
        }
    }

    /**商品详情页
     */
    public function detail(){
        if(request()->isAjax()){
        }else{
            $id = intval(input('id'));
            if(!$id){
                $this->error('此商品已下架');
            }
            $model = new \app\index\model\Goods();
            $config =[
                'where' => [
                    ['g.status', '=', 0],
                    ['g.shelf_status', '=', 3],
                    ['g.id', '=', $id],
                ],
            ];
            $info = $model->getInfo($config);
            if(empty($info)){
                $this->error('此商品已下架');
            }
            $info['main_img'] = explode(',',(string)$info['main_img']);
            $info['detail_img'] = explode(',',(string)$info['detail_img']);
            $info['tag'] = explode(',',(string)$info['tag']);
            $this->assign('info',$info);

            $modelComment = new \app\index\model\Comment();
            $where = [
                ['status','=',0],
                ['goods_id','=',$id],
            ];
            $averageScore = $modelComment -> where($where)->avg('score');
            $averageScore = round($averageScore,2);
            $this ->assign('averageScore',$averageScore);
            $total = $modelComment -> where($where)->count('user_id');
            $this ->assign('total',$total);

            //登录判断是否已收藏
            $user = session('user');
            if(!empty($user)){
                $modelCollection = new \app\index\model\Collection();
                $config = [
                    'where'=>[
                        ['user_id','=',$user['id']],
                        ['goods_id','=',$id],
                        ['status','=',0]
                    ],'filed'=>[
                        'id'
                    ]
                ];
                $info = $modelCollection -> getInfo($config);
                if(count($info)){
                    $this->assign('collected', 1);
                }
            }

            $unlockingFooterCart = unlockingFooterCartConfig([0,2,1]);
            $this->assign('unlockingFooterCart', $unlockingFooterCart);
            return $this->fetch();
        }
    }

    /**获取推荐商品
     * @return array|\think\response\View
     */
    public function getRecommendGoods(){
        if(!request()->isGet()){
            return errorMsg('请求方式错误');
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
        $list= $modelRecommendGoods->getList($config);
        $this->assign('list',$list);
        return view('goods/recommend_list_tpl');
    }
}