<?php
namespace app\index\controller;

class Comment extends \common\controller\Base{
    /**首页
     */
    public function index(){
        if(request()->isAjax()){
        }else{
            $unlockingFooterCart = unlockingFooterCartConfig([16]);
            $this->assign('unlockingFooterCart', $unlockingFooterCart);
            return $this->fetch();
        }
    }

    /**
     *  分页查询
     */
    public function getList(){
        if(!request()->isGet()){
            return errorMsg('请求方式错误');
        }
        $model = new \app\index\model\Comment();
        $goodsId = input('get.goods_id/d');
        $page = (int)input('get.page');
        if($page == 1){
            $where = [
                ['status','=',0],
                ['goods_id','=',$goodsId],
            ];
            $averageScore = $model -> where($where)->avg('score');
            $this ->assign('averageScore',$averageScore);
            $total = $model -> where($where)->count('user_id');
            $this ->assign('total',$total);
        }
        $config=[
            'where'=>[
                ['c.status','=',0],
                ['c.goods_id','=',$goodsId],
            ],
            'field'=>[
               'u.name','c.score','c.img','c.title','c.content','c.create_time','c.update_time'
            ],
            'join'=>[
                ['common.user u','u.id = c.user_id','left']
            ],
            'order'=>[
                'c.id'=>'desc'
            ],
        ];
        $list = $model -> pageQuery($config);
        $list->each(function($item, $key){
               $item['img'] =  explode(',',(string) $item['img']);

        });
        $this->assign('list',$list);
        $page++;
        $this ->assign('nextPage',$page);
        return $this->fetch('list_tpl');
    }

    /**商品详情页
     */
    public function detail(){
        if(request()->isAjax()){
        }else{
            $goodsId = intval(input('goods_id'));
            if(!$goodsId){
                $this->error('此商品已下架');
            }
            $model = new \app\index\model\Comment();
            $config =[
                'where' => [
                    ['g.status', '=', 0],
                    ['g.shelf_status', '=', 3],
                    ['g.id', '=', $goodsId],
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
        $config =[
            'where' => [
                ['rg.status', '=', 0],
                ['rg.goods_id', '=', $goodsId],
            ],'field'=>[
                'rg.recommend_goods_id',
            ]
        ];
        $modelRecommendGoods = new \app\index\model\RecommendGoods();
        $recommendGoodsIds = $modelRecommendGoods->getList($config);
        $recommendGoodsIds = array_column($recommendGoodsIds,'recommend_goods_id');

        $config =[
            'where' => [
                ['g.status', '=', 0],
                ['g.shelf_status', '=', 3],
                ['g.id', 'in', $recommendGoodsIds],
            ],'field'=>[
               'g.id as goods_id','g.headline','g.thumb_img','g.bulk_price'
            ]
        ];

        $model = new \app\index\model\Comment();
        $list = $model->getList($config);
        $this->assign('list',$list);
        return view('goods/recommend_list_tpl');
    }
}