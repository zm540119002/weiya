<?php
namespace app\index\controller;
class Order extends \common\controller\UserBase
{
    //生成订单
    public function generate()
    {
        if (!request()->isPost()) {
            return errorMsg('请求方式错误');
        }
        $modelOrder = new \app\index\model\Order();
        $modelOrderDetail = new \app\index\model\OrderDetail();
        $cartIds = input('post.cartIds/a');
        if (empty($cartIds)) {
            return errorMsg('请求数据不能为空');
        }
        $config = [
            'where' => [
                ['c.status', '=', 0],
                ['c.id', 'in', $cartIds],
            ], 'field' => [
                'g.id ','g.headline','g.thumb_img','g.bulk_price','g.specification','g.minimum_order_quantity',
                'g.minimum_sample_quantity','g.increase_quantity','g.purchase_unit','g.store_id','c.buy_type','c.num',
            ],'join'=>[
                ['goods g','g.id = c.foreign_id','left']
            ]
        ];
        //计算订单总价
        $modeCart = new \app\index\model\Cart();
        $goodsList = $modeCart->getList($config);
        $amount = 0;

        foreach ($goodsList as $k => &$goodsInfo) {
            if($goodsInfo['buy_type'] == 2){
                $goodsSalePrice = $goodsInfo['minimum_sample_quantity'];
            }else{
                $goodsSalePrice = $goodsInfo['minimum_order_quantity'];
            }
            $goodsList[$k]['price'] = $goodsSalePrice;
            $goodsList[$k]['store_id'] = $goodsInfo['store_id'];
            $goodsNum = intval($goodsInfo['num']);
            $totalPrices = $goodsSalePrice * $goodsNum;
            $amount += number_format($totalPrices, 2, '.', '');
        }
//
        //开启事务
        $modelOrder->startTrans();
        //订单编号
        $orderSN = generateSN(5);
        //组装父订单数组
        $data = [
                'sn' => $orderSN,
                'user_id' => $this->user['id'],
                'amount' => $amount,
                'actually_amount' => $amount,
                'create_time' =>  time(),
        ];
        //生成父订单
        $res = $modelOrder->allowField(true)->save($data);
        if (!$res) {
            $modelOrder->rollback();
            return errorMsg('失败');
        }
        $orderId = $modelOrder ->getAttr('id');
        //组装订单明细
        $dataDetail = [];
        foreach ($goodsList as $item=>&$goodsInfo) {
            $dataDetail[$item]['father_order_id'] = $orderId;
            $dataDetail[$item]['price'] = $goodsInfo['price'];
            $dataDetail[$item]['num'] = $goodsInfo['num'];
            $dataDetail[$item]['goods_id'] = $goodsInfo['id'];
            $dataDetail[$item]['user_id'] = $this->user['id'];
            $dataDetail[$item]['store_id'] = $goodsInfo['store_id'];
            $dataDetail[$item]['buy_type'] = $goodsInfo['buy_type'];
        }
        //生成订单明细
        $res = $modelOrderDetail->allowField(true)->saveAll($dataDetail)->toArray();
        if (!count($res)) {
            $modelOrder->rollback();
            return errorMsg('失败');
        }
        $modelOrder->commit();
        return successMsg('生成订单成功', array('order_sn' => $orderSN));
    }

   //订单-结算页
    public function settlement()
    {
        $modelOrder = new \app\index\model\Order();
        $orderSn = input('order_sn');
        $config = [
            'where' => [
                ['o.status', '=', 0],
                ['o.sn', '=', $orderSn],
                ['o.user_id', '=', $this->user['id']],
            ],'join' => [
                ['order_detail od','od.father_order_id = o.id','left'],
                ['goods g','g.id = od.goods_id','left']
            ],'field' => [
                'o.id', 'o.sn', 'o.amount',
                'o.user_id', 'od.goods_id','od.num','od.price',
                'g.name','g.thumb_img',
            ],
        ];
        $orderInfo = $modelOrder->getList($config);
        $this ->assign('info',$orderInfo);
        $unlockingFooterCart = unlockingFooterCartConfig([3]);
        $this->assign('unlockingFooterCart', $unlockingFooterCart);
        return $this->fetch();
    }

    //订单-详情页
    public function detail()
    {
        $modelOrder = new \app\index\model\Order();
        $orderSn = input('order_sn');
        $config = [
            'where' => [
                ['o.status', '=', 0],
                ['o.sn', '=', $orderSn],
                ['o.user_id', '=', $this->user['id']],
            ],'join' => [
                ['order_detail od','od.father_order_id = o.id','left'],
                ['goods g','g.id = od.goods_id','left']
            ],'field' => [
                'o.id', 'o.sn', 'o.amount',
                'o.user_id', 'od.goods_id','od.num','od.price','od.buy_type',
                'g.headline','g.thumb_img','g.specification', 'g.purchase_unit'
            ],
        ];
        $orderGoodsList = $modelOrder->getList($config);
        $this ->assign('orderGoodsList',$orderGoodsList);
      
        //地址
        $modelAddress =  new \common\model\Address();
        $config = [
            'where' => [
                ['a.status', '=', 0],
                ['a.user_id', '=', $this->user['id']],
            ],
        ];
        $addressList = $modelAddress ->getList($config);
        $defaultAddress = [];
        foreach ($addressList as &$addressInfo){
            if($addressInfo['is_default'] == 1){
                $defaultAddress = $addressInfo;
                break;
            }
        }
        $this->assign('defaultAddress', $defaultAddress);
        $this->assign('addressList', $addressList);
        $unlockingFooterCart = unlockingFooterCartConfig([11]);
        $this->assign('unlockingFooterCart', $unlockingFooterCart);
        return $this->fetch();

    }
    //确定订单
    public function confirmOrder()
    {
        if (!request()->isPost()) {
            return errorMsg('请求方式错误');
        }
        $fatherOrderId = input('post.father_order_id',0,'int');
        $modelOrder = new \app\index\model\Order();

        $data = [
            'order_status' => 1,
            'address_id' => input('post.address_id',0,'int'),
        ];
        $condition = [
            ['user_id','=',$this->user['id']],
            ['id','=',$fatherOrderId],
        ];
        $res = $modelOrder -> allowField(true) -> save($data,$condition);
        if(false === $res){
            return errorMsg('失败');
        }
//        //根据订单号查询关联的商品
//        $modelOrderDetail = new \app\index\model\OrderDetail();
//        $config = [
//            'where' => [
//                ['od.status', '=', 0],
//                ['od.father_order_id', '=', $fatherOrderId],
//            ], 'field' => [
//                'od.goods_id', 'od.price', 'od.num', 'od.store_id','od.father_order_id',
//            ]
//        ];
//        $orderDetailList = $modelOrderDetail->getList($config);
//        $modelOrderChild = new \app\index\model\OrderChild();
//        //生成子订单
//        $rse = $modelOrderChild -> createOrderChild($orderDetailList,$this->user['id']);
//        if(!$rse['status']){
//            $modelOrder->rollback();
//            return errorMsg($modelOrder->getLastSql());
//        }
        $orderSn = input('post.order_sn','','string');
        return successMsg('成功',array('order_sn'=>$orderSn));
    }
    //支付
    public function pay()
    {
        $modelOrder = new \app\index\model\Order();
        $orderSn = input('order_sn');
        $config = [
            'where' => [
                ['o.status', '=', 0],
                ['o.sn', '=', $orderSn],
                ['o.user_id', '=', $this->user['id']],
            ],'field' => [
                'o.id', 'o.sn', 'o.amount',
                'o.user_id',
            ],
        ];
        $orderInfo = $modelOrder->getInfo($config);
        $this->assign('orderInfo', $orderInfo);
        $unlockingFooterCart = unlockingFooterCartConfig([4]);
        $this->assign('unlockingFooterCart', $unlockingFooterCart);
        return $this->fetch();
    }


}