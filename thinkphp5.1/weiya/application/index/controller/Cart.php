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

    /**
     * @return array
     * @throws \Exception
     *
     */
    public function addCart(){
        if(!request()->isPost()){
            return $this->errorMsg('请求方式错误');
        }
        $goodsList = input('post.goodsList/a');
        if(empty($goodsList)){
            return $this->errorMsg('没有数据');
        }
        $userId = $this->user['id'];
        $model = new \app\index\model\Cart();
        $config = [
          'where' => [
              ['user_id','=',$userId],
              ['status','=',0]
          ],'field'=>[
              'id','user_id','foreign_id','num','create_time','goods_type','buy_type','brand_name','brand_id'
            ]
        ];
        $cartList = $model->getList($config);
        $addData = [];
        $updateData =[];
        foreach ($goodsList as $goods){
            //假定没找到
            $find = false;
            foreach ($cartList as $cart){
                if($goods['foreign_id'] == $cart['foreign_id'] && $goods['buy_type'] == $cart['buy_type'] && $goods['brand_name'] == $cart['brand_name'] ){//找到了，则更新记录
                    $find = true;
                    $data = [
                        'user_id' => $this->user['id'],
                        'id' => $cart['id'],
                        'foreign_id' => $cart['foreign_id'],
                        'buy_type' => $cart['buy_type'],
                        'num' => $goods['num'] + $cart['num'],
                        'brand_name' => $cart['brand_name'],
                        'brand_id' => $cart['brand_id'],
                    ];
                    $updateData[] = $data;
                }
            }
            if(!$find){//如果没找到，则新增
                $data = [
                    'user_id' => $this->user['id'],
                    'foreign_id' => $goods['foreign_id'],
                    'buy_type' =>$goods['buy_type'],
                    'num' =>$goods['num'],
                    'brand_name' => $goods['brand_name'],
                    'brand_id' => $goods['brand_id'],
                    'create_time'=>time(),
                ];
                $addData[] = $data;
            }
        }
        $model->startTrans();
        if(!empty($addData)){
            $res =  $model->saveAll($addData);
            if (!count($res)) {
                $model->rollback();
                return $this->errorMsg('失败');
            }
        }
        if(!empty($updateData)){
            $res =  $model->saveAll($updateData);
            if (!count($res)) {
                $model->rollback();
                return $this->errorMsg('失败');
            }
        }
        $model -> commit();
        return successMsg('成功');
    }

    /**
     * 分页查询
     */
    public function getList(){
        if(!request()->isGet()){
            return $this->errorMsg('请求方式错误');
        }
        $userId = $this->user['id'];
        $model = new \app\index\model\Cart();
         $config=[
             'where'=>[
                 ['c.user_id','=',$userId],
//                 ['c.create_time','>',time()-7*24*60*60],//只展示7天的数据
                 ['c.status','=',0],
             ],'join' => [
                 ['goods g','g.id = c.foreign_id','left']
             ],'field'=>[
                 'c.id as cart_id','c.foreign_id','c.num','c.goods_type','c.buy_type','c.create_time','c.brand_id','c.brand_name',
                 'g.id','g.headline','g.name','g.thumb_img','g.bulk_price','g.sample_price','g.specification','g.minimum_order_quantity',
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
        $currentPage = input('get.page/d');
        $this->assign('currentPage',$currentPage);
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

    //修改购物车数量
    public function editCartNum(){
        if(!request()->isPost()){
            return $this->errorMsg('请求方式错误');
        }
        $data = input('post.');
        $data['user_id'] = $this -> user['id'];
        $model = new \app\index\model\Cart();
        $res = $model ->isUpdate(true)-> save($data);
        if(false === $res){
            return $this->errorMsg('失败');
        }
        return successMsg('成功');
    }

    //删除地址
    public function del(){
        if(!request()->isAjax()){
            return $this->errorMsg(config('custom.not_ajax'));
        }
        $ids = input('post.cart_ids/a');
        $model = new \app\index\model\Cart();
        $condition = [
            ['user_id','=',$this->user['id']],
            ['id','in',$ids],
        ];
        $result = $model -> del($condition,true);
        if($result['status']){
            return successMsg('删除成功');
        }else{
            return $this->errorMsg('删除失败');
        }
    }
}