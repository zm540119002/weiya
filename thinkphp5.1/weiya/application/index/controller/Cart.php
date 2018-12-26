<?php
namespace app\index\controller;

class Cart extends \common\controller\Base{
    /**首页
     */
    public function index(){
        if(request()->isAjax()){
        }else{
            $unlockingFooterCart = unlockingFooterCartConfig([10,0,9]);
            $this->assign('unlockingFooterCart', $unlockingFooterCart);
            return $this->fetch();
        }
    }

    public function addCart(){
        if(!request()->isPost()){
            return errorMsg('请求方式错误');
        }
        $data = input('post.goodsList/a');
        if(empty($data)){
            return errorMsg('没有数据');
        }
//        $userId = $this->user['id'];
        $userId = 24;
        $arr = [
            'user_id' => $userId,
            'create_time' => time(),
        ];
        array_walk($data, function (&$value, $key, $arr) {
            $value = array_merge($value, $arr);
        }, $arr);
        $model = new \app\index\model\Cart();
        $res = $model->allowField(true)->saveAll($data)->toArray();
        if (!count($res)) {
            return errorMsg('失败');
        }
        return successMsg('成功');

    }

    /**
     * 分页查询
     */
    public function getList(){
        if(!request()->isGet()){
            return errorMsg('请求方式错误');
        }

        $model = new \app\index\model\Cart();
         $config=[
             'where'=>[
                 ['c.user_id','=',24],
                 ['c.status','=',0],
             ],'join' => [
                 ['goods g','g.id = c.foreign_id','left']
             ],'field'=>[
                 'c.id as cart_id','c.foreign_id','c.num','c.goods_type','c.buy_type','c.create_time',
                 'g.id as goods_id ','g.headline','g.name','g.thumb_img','g.bulk_price','g.sample_price','g.specification','g.minimum_order_quantity',
                 'g.minimum_sample_quantity','g.increase_quantity','g.purchase_unit'
             ],'order'=>[
                 'c.id'=>'desc'
             ],
         ];

         $keyword = input('get.keyword','');
         if($keyword) {
             $config['where'][] = ['g.name', 'like', '%' . trim($keyword) . '%'];
         }
         $list = $model -> pageQuery($config)->toArray();
         $this->assign('list',$list);
         if(isset($_GET['pageType'])){
             if($_GET['pageType'] == 'index' ){//店铺产品列表
                 return $this->fetch('list_tpl');
             }
         }
    }

    /**详情页
     */
    public function detail(){
        if(request()->isAjax()){
        }else{
            $goodsId = intval(input('goods_id'));
            if(!$goodsId){
                $this->error('此商品已下架');
            }
            $model = new \app\index\model\Cart();
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
            $this->assign('info',$info);

            $unlockingFooterCart = unlockingFooterCartConfig([0,2,1]);
            $this->assign('unlockingFooterCart', $unlockingFooterCart);
            return $this->fetch();
        }
    }

}