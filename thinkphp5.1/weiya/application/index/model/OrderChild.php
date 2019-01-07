<?php
namespace app\index\model;

class OrderChild extends \common\model\Base {
	// 设置当前模型对应的完整数据表名称
	protected $table = 'order_child';
	// 设置主键
	protected $pk = 'id';
	// 设置当前模型的数据库连接
    protected $connection = 'db_config_weiya';
	//表的别名
	protected $alias = 'oc';

    //生成子订单
	public function createOrderChild($orderDetailList)
	{
        print_r($orderDetailList);exit;
		$this->startTrans();
        $storeId = $orderDetailList[0]['store_id'];
        $splitOrderFlag = 0; //0：不拆 1：拆
        foreach ($orderDetailList as $item =>$orderDetail){
            if($storeId != $orderDetail['store_id']){
                $splitOrderFlag = 1;
                break;
            }
        }
        if($splitOrderFlag){
            //获取父订单商品所属店铺id，并去重
            $storeIds = array_unique(array_column($orderDetailList, 'store_id'));
            //组装子订单数据
            foreach ($storeIds as $k => &$storeId) {
                foreach ($orderDetailList as &$orderDetail) {
                    if ($storeId == $orderDetail['store_id']) {
                        $childOrderData[$k]['father_order_id'] = $orderDetailList[0]['father_order_id'];
                        $childOrderData[$k]['sn'] = generateSN();
                        $childOrderData[$k]['user_id'] = $orderDetailList[0]['user_id'];
                        $totalPrices = $orderDetail['price'] * $orderDetail['num'];
                        $childOrderData[$k]['amount'] += number_format($totalPrices, 2, '.', '');
                        $childOrderData[$k]['actually_amount'] += number_format($totalPrices, 2, '.', '');
                        $childOrderData[$k]['create_time'] = time();
                        $childOrderData[$k]['store_id'] = $orderDetail['store_id'];
                    }
                }
            }
        }else{
			$amount = 0;
			foreach ($orderDetailList as $k => &$v) {
				$totalPrices = $v['price'] * $v['num'];
				$amount += number_format($totalPrices, 2, '.', '');
			}
            //组装子订单数据
            $childOrderData = [
                [
                    'father_order_id' => $orderDetailList[0]['father_order_id'],
                    'sn' => generateSN(),
                    'user_id' => $userId,
                    'amount' => $amount,
                    'actually_amount' => $amount,
                    'create_time' =>  time(),
                    'store_id' =>  $storeId,
                ]
            ];
        }
        $childOrders = $this->allowField(true)->saveAll($childOrderData)->toArray();

        if (!count($childOrders)) {
            $this->rollback();
            return errorMsg('失败');
        }
        ///获取父订单的所有商品id
        $goodsIds = array_column($orderDetailList,'goods_id');
        //把子订单号添加到订单明细中
        foreach ( $goodsIds as $item1=>$goodsId) {
            foreach ($childOrders as $item=>$value) {
                $data = [
                    'child_order_id' => $value['id'],
                ];
                $condition = [
                    ['father_order_id','=',$orderDetailList[0]['father_order_id']],
                    ['goods_id','=',$goodsId],
                    ['store_id','=',$value['store_id']],
                ];
				$modelOrderDetail = new \app\index\model\OrderDetail();
                $res = $modelOrderDetail->where($condition)->setField($data);
                if(false === $res){
                    return errorMsg('失败');
                }
            }
        }
        $this->commit();//提交事务
		return successMsg('成功');
	}

}