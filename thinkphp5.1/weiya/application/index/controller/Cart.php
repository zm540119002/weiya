<?php
namespace app\index\controller;

class Cart extends \common\controller\UserBase{
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
        $goodsList = input('post.goodsList/a');
        if(empty($goodsList)){
            return errorMsg('没有数据');
        }
        $userId = $this->user['id'];
        $model = new \app\index\model\Cart();
        $config = [
          'where' => [
              ['c.user_id','=',$userId]
          ]
        ];
        $cartList = $model->getList($config);
        foreach ($goodsList as $goods){
            //假定没找到
            $find = false;
            foreach ($cartList as $cart){
                if($goods['foreign_id'] == $cart['foreign_id'] && $goods['buy_type'] == $cart['buy_type']){//找到了，则更新记录
                    $find = true;
                    $where = [
                        'user_id' => $this->user['id'],
                        'id' => $cart['id'],
                        'foreign_id' => $cart['foreign_id'],
                    ];
                    $data['num'] = $goods['num'] + $cart['num'];
                    $res = $model->allowField(true)->save($data,$where);
                    if(false === $res){
                        break 2;
                    }
                }
            }
            if(!$find){//如果没找到，则新增
                $data = [];
                $data['user_id'] = $this->user['id'];
                $data['foreign_id'] = $goods['foreign_id'];
                $data['num'] = $goods['num'];
                $data['buy_type'] = $goods['buy_type'];
                $data['create_time'] = time();
                $res = $model->allowField(true)->save($data);
                if(!$res){
                    break;
                }
            }
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
        $userId = $this->user['id'];
        $model = new \app\index\model\Cart();
         $config=[
             'where'=>[
                 ['c.user_id','=',$userId],
                 ['c.create_time','>',time()-7*24*60*60],//只展示7天的数据
                 ['c.status','=',0],
             ],'join' => [
                 ['goods g','g.id = c.foreign_id','left']
             ],'field'=>[
                 'c.id as cart_id','c.foreign_id','c.num','c.goods_type','c.buy_type','c.create_time',
                 'g.id  ','g.headline','g.name','g.thumb_img','g.bulk_price','g.sample_price','g.specification','g.minimum_order_quantity',
                 'g.minimum_sample_quantity','g.increase_quantity','g.purchase_unit'
             ],'order'=>[
                 'c.id'=>'desc'
             ],
         ];
         $keyword = input('get.keyword','');
         if($keyword) {
             $config['where'][] = ['g.name', 'like', '%' . trim($keyword) . '%'];
         }

         $list = $model -> pageQuery($config);
         $this->assign('list',$list);
         if(isset($_GET['pageType'])){
             if($_GET['pageType'] == 'index' ){
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