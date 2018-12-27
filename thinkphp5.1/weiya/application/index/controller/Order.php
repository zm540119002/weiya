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
        $goodsList = $_POST['goodsList'];
        if (empty($goodsList)) {
            return errorMsg('请求数据不能为空');
        }
//        $goodsList = [
//            ['goods_id'=>13,'num'=>1],
//            ['goods_id'=>14,'num'=>3],
//            ['goods_id'=>15,'num'=>1],
//            ['goods_id'=>16,'num'=>2],
//            ['goods_id'=>17,'num'=>1],
//        ];
        //计算订单总价
        $modelGoods = new \app\index\model\Goods();
        $amount = 0;
        foreach ($goodsList as $k => &$v) {
            $config = [
                'where' => [
                    ['g.status', '=', 0],
                    ['g.id', '=', $v['goods_id']],
                ], 'field' => [
                    'g.id', 'g.name', 'g.sale_price', 'g.retail_price', 'g.store_id'
                ],
            ];
            $goodsInfo = $info = $modelGoods->getInfo($config);;
            $goodsNum = intval($v['num']);
            $goodsList[$k]['price'] = $goodsInfo['sale_price'];
            $goodsList[$k]['store_id'] = $goodsInfo['store_id'];
            $totalPrices = $goodsInfo['sale_price'] * $goodsNum;
            $amount += number_format($totalPrices, 2, '.', '');
        }

        //开启事务
        $modelOrder->startTrans();
        //订单编号
        $orderSN = generateSN();
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
        foreach ($goodsList as $item=>$value) {
            $dataDetail[$item]['father_order_id'] = $orderId;
            $dataDetail[$item]['price'] = $value['price'];
            $dataDetail[$item]['num'] = $value['num'];
            $dataDetail[$item]['goods_id'] = $value['goods_id'];
            $dataDetail[$item]['user_id'] = $this->user['id'];
            $dataDetail[$item]['store_id'] = $value['store_id'];
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
//        $modelOrderDetail = new \app\purchase\model\OrderDetail();
//        $config = [
//            'where' => [
//                ['od.status', '=', 0],
//                ['od.father_order_id', '=', $fatherOrderId],
//            ], 'field' => [
//                'od.goods_id', 'od.price', 'od.num', 'od.store_id','od.father_order_id',
//            ]
//        ];
//        $orderDetailList = $modelOrderDetail->getList($config);
//        $modelOrderChild = new \app\purchase\model\OrderChild();
//        //生成子订单
//        $rse = $modelOrderChild -> createOrderChild($orderDetailList,$this->user['id']);
//        if(!$rse['status']){
//            $modelOrder->rollback();
//            return errorMsg($modelOrder->getLastSql());
//        }

        $orderSn = input('post.order_sn','','string');
        return successMsg('成功',array('order_sn'=>$orderSn));
    }
    //订单-详情页
    public function detail()
    {
        $unlockingFooterCart = unlockingFooterCartConfig([0,1,2]);
        $this->assign('unlockingFooterCart', $unlockingFooterCart);
        return $this->fetch();

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