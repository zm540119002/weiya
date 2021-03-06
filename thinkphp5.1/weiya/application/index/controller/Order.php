<?php
namespace app\index\controller;
class Order extends \common\controller\UserBase
{
    //生成订单
    public function generate()
    {
        if (!request()->isPost()) {
            return $this->errorMsg('请求方式错误');
        }
        $modelOrder = new \app\index\model\Order();
        $modelOrderDetail = new \app\index\model\OrderDetail();
        $goodsList = input('post.goodsList/a');
        if (empty($goodsList)) {
            return $this->errorMsg('请求数据不能为空');
        }
        $goodsIds = array_column($goodsList,'goods_id');
        $config = [
            'where' => [
                ['g.status', '=', 0],
                ['g.id', 'in', $goodsIds],
            ], 'field' => [
                'g.id as goods_id','g.headline','g.thumb_img','g.bulk_price','g.specification','g.sample_price',
                'g.purchase_unit','g.store_id'
            ]
        ];
        //计算订单总价
        $modeGoods = new \app\index\model\Goods();
        $goodsListNew = $modeGoods->getList($config);
        $amount = 0;
        foreach ($goodsList as $k1 => &$goodsInfo) {
            foreach ($goodsListNew as $k2 => &$goodsInfoNew) {
                if($goodsInfo['goods_id'] == $goodsInfoNew['goods_id']){
                    $goodsList[$k1]['headline'] = $goodsInfoNew['headline'];
                    $goodsList[$k1]['thumb_img'] = $goodsInfoNew['thumb_img'];
                    $goodsList[$k1]['specification'] = $goodsInfoNew['specification'];
                    $goodsList[$k1]['purchase_unit'] = $goodsInfoNew['purchase_unit'];
                    $goodsList[$k1]['store_id'] = $goodsInfoNew['store_id'];
                    switch ($goodsInfo['buy_type']){
                        case 1:
                            $goodsList[$k1]['price'] = $goodsInfoNew['bulk_price'];
                            break;
                        case 2:
                            $goodsList[$k1]['price'] = $goodsInfoNew['sample_price'];
                             break;
                        default:
                    }
                    $totalPrices = $goodsInfo['num'] * $goodsList[$k1]['price'];
                    $amount += number_format($totalPrices, 2, '.', '');
                }
            }
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
            return $this->errorMsg('失败');
        }
        $orderId = $modelOrder ->getAttr('id');
        //组装订单明细
        $dataDetail = [];
        foreach ($goodsList as $item=>&$goodsInfo) {
            $dataDetail[$item]['father_order_id'] = $orderId;
            $dataDetail[$item]['price'] = $goodsInfo['price'];
            $dataDetail[$item]['num'] = $goodsInfo['num'];
            $dataDetail[$item]['goods_id'] = $goodsInfo['goods_id'];
            $dataDetail[$item]['user_id'] = $this->user['id'];
            $dataDetail[$item]['store_id'] = $goodsInfo['store_id'];
            $dataDetail[$item]['buy_type'] = $goodsInfo['buy_type'];
            $dataDetail[$item]['brand_name'] = $goodsInfo['brand_name'];
            $dataDetail[$item]['brand_id'] = $goodsInfo['brand_id'];
        }
        //生成订单明细
        $res = $modelOrderDetail->allowField(true)->saveAll($dataDetail)->toArray();
        if (!count($res)) {
            $modelOrder->rollback();
            return $this->errorMsg('失败');
        }
        $modelOrder->commit();
        return successMsg('生成订单成功', array('order_sn' => $orderSN));
    }


    // 订单确认页
    public function confirmOrder()
    {
        if (request()->isPost()) {
            $fatherOrderId = input('post.order_id',0,'int');
            $modelOrder = new \app\index\model\Order();
            $config = [
                'where' => [
                    ['o.status', '=', 0],
                    ['o.id', '=', $fatherOrderId],
                    ['o.user_id', '=', $this->user['id']],
                ],'field' => [
                    'o.id', 'o.sn', 'o.order_status'
                ],
            ];
            $orderInfo = $modelOrder -> getInfo($config);
            if($orderInfo['order_status']>1){
                return $this->errorMsg('此订单已提交过');
            }
            $modelOrder ->startTrans();
            $data = input('post.');
            $data['order_status'] = 1;
            $condition = [
                ['user_id','=',$this->user['id']],
                ['id','=',$fatherOrderId],
            ];

            $res = $modelOrder -> allowField(true) -> save($data,$condition);

            if(false === $res){
                $modelOrder ->rollback();
                return $this->errorMsg('失败');
            }
            /*            $modelOrderDetail = new \app\index\model\OrderDetail();
                        $res = $modelOrderDetail -> isUpdate(true)-> saveAll($data['orderDetail']);
                        if (!count($res)) {
                            $modelOrder->rollback();
                            return $this->errorMsg('失败');
                        }*/
            //根据订单号查询关联的购物车的商品 删除
            $modelOrderDetail = new \app\index\model\OrderDetail();
            $config = [
                'where' => [
                    ['od.status', '=', 0],
                    ['od.father_order_id', '=', $fatherOrderId],
                ], 'field' => [
                    'od.goods_id','od.buy_type','od.price', 'od.num', 'od.store_id','od.father_order_id','od.user_id',
                ]
            ];
            $orderDetailList = $modelOrderDetail->getList($config);
            $model = new \app\index\model\Cart();
            foreach ($orderDetailList as &$orderDetailInfo){
                $condition = [
                    ['user_id','=',$this->user['id']],
                    ['foreign_id','=',$orderDetailInfo['goods_id']],
                    ['buy_type','in',$orderDetailInfo['buy_type']],
                ];
                $result = $model -> del($condition,false);
                if(!$result['status']){
                    $modelOrder->rollback();
                    return $this->errorMsg('删除失败');
                }
            }
            $modelOrder -> commit();
            return successMsg('成功');

        }else{
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
                    'o.id', 'o.sn', 'o.amount','o.actually_amount','o.consignee','o.mobile','o.province','o.city','o.area','o.detail_address',
                    'o.user_id', 'od.goods_id','od.num','od.price','od.buy_type','od.brand_id','od.brand_name','od.id as order_detail_id',
                    'g.headline','g.thumb_img','g.specification', 'g.purchase_unit'
                ],
            ];
            $orderGoodsList = $modelOrder->getList($config);
            $this ->assign('orderGoodsList',$orderGoodsList);

            $orderInfo = reset($orderGoodsList);
            // 显示地址
            $this->getOrderAddressInfo($orderInfo);

            $unlockingFooterCart = unlockingFooterCartConfig([0,111,11]);
            $this->assign('unlockingFooterCart', $unlockingFooterCart);

            //钱包
            $modelWallet = new \app\index\model\Wallet();
            $config = [
                'where' => [
                    ['status', '=', 0],
                    ['user_id', '=', $this->user['id']],
                ],'field' => [
                    'id','amount',
                ],
            ];
            $walletInfo = $modelWallet->getInfo($config);
            $this->assign('walletInfo', $walletInfo);
            $this->assign('user',$this->user);
            return $this->fetch();
        }

    }
    // 去结算
    public function toPay()
    {
        if (!request()->isPost()) {
            return $this->errorMsg('请求方式错误');
        }
        $postData = input('post.');
        $modelOrder = new \app\index\model\Order();
        $condition = [
            'where' => [
                ['user_id','=',$this->user['id']],
                ['sn','=',$postData['order_sn']],
                ['order_status','<',2],
            ], 'field'=>[
                'id','sn','actually_amount'
            ]
        ];
        $orderInfo  = $modelOrder->getInfo($condition);
        //先查找支付表是否有数据
        $modelPay = new \app\index\model\Pay();
        $condition = [
            'where' => [
                ['user_id','=',$this->user['id']],
                ['sn','=',$orderInfo['sn']],
                ['pay_status','=',1],
                ['type','=',config('custom.pay_type')['orderPay']['code']]
            ], 'field'=>[
                'id','sn','actually_amount'
            ]
        ];
        $payInfo  = $modelPay->getInfo($condition);
        if(empty($payInfo)){
            //增加
            $data = [
                'sn' => $orderInfo['sn'],
                'actually_amount' =>$orderInfo['actually_amount'],
                'user_id' => $this->user['id'],
                'pay_code' => $postData['pay_code'],
                'type' => config('custom.pay_type')['orderPay']['code'],
            ];
            $result  = $modelPay->isUpdate(false)->save($data);
            if(!$result){
                $modelPay ->rollback();
                return $this->errorMsg('失败');
            }

        }else{
            //修改
            $updateData = [
                'actually_amount' =>$orderInfo['actually_amount'],
                'pay_code' => $postData['pay_code'],
            ];
            $where = [
                'sn' => $orderInfo['sn'],
                'user_id' => $this->user['id'],
            ];
            $result  = $modelPay->isUpdate(true)->save($updateData,$where);
            if($result === false){
                $modelPay ->rollback();
                return $this->errorMsg('失败');
            }
        }
        // 各支付方式的处理方式 //做到这里
        switch($postData['pay_code']){
            // 支付中心处理
            case config('custom.pay_code.WeChatPay.code') :
            case config('custom.pay_code.Alipay.code') :
            case config('custom.pay_code.UnionPay.code') :
                $url = config('custom.pay_gateway').$orderInfo['sn'];
                break;
        }
        return successMsg( '成功',['url'=>$url]);

    }

    //订单管理
    public function manage(){
        if(input('?order_status')){
            $orderStatus = input('order_status');
            $this ->assign('order_status',$orderStatus);
        }
       return $this->fetch();
    }

    //订单-详情页
    public function detail()
    {
        $model = new \app\index\model\Order();
        $orderSn = input('order_sn');
        $config=[
            'where'=>[
                ['o.status', '=', 0],
                ['o.user_id', '=', $this->user['id']],
                ['o.sn', '=', $orderSn],
            ],
            'field'=>[
                'o.id','o.pay_sn','o.sn','o.order_status','o.pay_code','o.amount','o.actually_amount','o.remark',
                'o.consignee','o.mobile','o.province','o.city','o.area','o.detail_address','o.create_time','o.payment_time',
                'o.finished_time',
                'u.name','u.mobile_phone'
            ],'join'=>[
                ['common.user u','u.id = o.user_id','left'],
            ],'order'=>[
                'o.id'=>'desc'
            ]
        ];
        $info = $model->getInfo($config);
        $info =  $info!=0?$info->toArray():[];
        $modelOrderDetail = new \app\index\model\OrderDetail();
        $config=[
            'where'=>[
                ['od.status', '=', 0],
                ['od.father_order_id','=',$info['id']]
            ],
            'field'=>[
                'od.goods_id', 'od.price', 'od.num', 'od.buy_type','od.brand_id','od.brand_name',
                'g.name','g.thumb_img','g.specification'
            ],
            'join'=>[
                ['goods g','g.id = od.goods_id','left'],
            ],

        ];
        $goodsList = $modelOrderDetail -> getList($config);
        $goodsNum = 0;
        foreach ($goodsList as &$goods){
            $goodsNum+=$goods['num'];
        }
        $info['goods_list'] = $goodsList;
        $info['goods_num'] = $goodsNum;
        $this->assign('info',$info);
        $configFooter = [];
        switch ($info['order_status'])
        {
            /*
             * 0：临时 1:待付款 2:待收货 3:待评价 4:已完成 5:已取消 6:售后',
             */
            case "1":
                $configFooter = [5];
                break;
            case "2":
                $configFooter = [12];
                break;
            case "3":
                $configFooter = [13];
                break;
            case "4":
                $configFooter = [14];
                break;
            case "5":
                $configFooter = [14];
                break;
            case "6":
                $configFooter = [];
                break;
            default:

        }
        $unlockingFooterCart = unlockingFooterCartConfig($configFooter);
        $this->assign('unlockingFooterCart', $unlockingFooterCart);
        return $this->fetch();

    }

    /**
     * 设置状态
     */
    public function setOrderStatus(){
        if(!request()->isPost()){
            return config('custom.not_post');
        }
        $model = new \app\index\model\Order();
        $id = input('post.id/d');
        if(!input('?post.id') && !$id){
            return $this->errorMsg('失败');
        }
        $where=[
            ['id','=',$id],
            ['user_id','=',$this->user['id']],
        ];
        $orderStatus = input('post.order_status/d');
        $data = [
            'order_status' => $orderStatus,
        ];
        $rse = $model->where($where)->setField($data);
        if(!$rse){
            return $this->errorMsg('失败');
        }
        return successMsg('成功');
    }


    /**
     * @return array|mixed
     * 查出产商相关产品 分页查询
     */
    public function getList(){
        if(!request()->isGet()){
            return $this->errorMsg('请求方式错误');
        }
        $model = new \app\index\model\Order();
        $config=[
            'where'=>[
                ['o.status', '=', 0],
                ['o.user_id', '=', $this->user['id']],
            ],
            'field'=>[
                'o.id','o.pay_sn','o.sn','o.order_status','o.pay_code','o.amount','o.actually_amount','o.remark',
                'o.consignee','o.mobile','o.province','o.city','o.area','o.detail_address','o.create_time','o.payment_time',
                'o.finished_time',
            ],'order'=>[
            'o.id'=>'desc'
        ]

        ];
        if(input('?get.order_status') && input('get.order_status/d')){
            $config['where'][] = ['o.order_status', '=', input('get.order_status/d')];
        }else{
            $config['where'][] = ['o.order_status', '>', 0];
        }
        if(input('?get.category_id') && input('get.category_id/d')){
            $config['where'][] = ['o.category_id_1', '=', input('get.category_id/d')];
        }
        $keyword = input('get.keyword','');
        if($keyword) {
            $config['where'][] = ['o.name', 'like', '%' . trim($keyword) . '%'];
        }

        $list = $model -> pageQuery($config)->each(function($item, $key){
            $modelOrderDetail = new \app\index\model\OrderDetail();
            $config=[
                'where'=>[
                    ['od.status', '=', 0],
                    ['od.father_order_id','=',$item['id']]
                ],
                'field'=>[
                    'od.goods_id', 'od.price', 'od.num', 'od.buy_type','od.brand_id','od.brand_name',
                    'g.name','g.thumb_img',
                ],
                'join'=>[
                    ['goods g','g.id = od.goods_id','left'],
                ],

            ];
            $goodsList = $modelOrderDetail -> getList($config);
            $goodsNum = 0;
            foreach ($goodsList as &$goods){
                $goodsNum+=$goods['num'];
            }
            $item['goods_list'] = $goodsList;
            $item['goods_num'] = $goodsNum;
            return $item;
        });
        $currentPage = input('get.page/d');
        $this->assign('currentPage',$currentPage);
        $this->assign('list',$list);
        if(isset($_GET['pageType'])){
            $pageType = $_GET['pageType'];
            $this->fetch($pageType);
        }
        return $this->fetch('list_tpl');
    }

    // 获取订单地址的默认值
    private function getOrderAddressInfo($orderInfo){

        // 显示地址
        if( !empty($orderInfo['mobile']) && !empty($orderInfo['consignee']) ){
            $addressInfo = $orderInfo;

        }else{
            $modelAddress =  new \common\model\Address();

            $condition = [
                'where' => [
                    ['a.user_id','=',$this->user['id']],
                    ['a.is_default','=',1]
                ]
            ];
            $addressInfo = $modelAddress->getAddressDataList($condition,'info');
        }
        $this->assign('addressInfo', $addressInfo);
    }
}