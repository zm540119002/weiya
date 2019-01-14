<?php
namespace app\index\controller;
class Payment extends \common\controller\UserBase{
    //订单-支付
    public function orderPayment(){
        //微信支付
        if( empty(input('order_sn')) || empty(input('?pay_code'))){
            $this -> error('参数错误');
        }
        $modelOrder = new \app\index\model\Order();
        $orderSn = input('order_sn','','string');
        $config = [
            'where' => [
                ['o.status', '=', 0],
                ['o.sn', '=', $orderSn],
                ['o.user_id', '=', $this->user['id']],
            ],'field' => [
                'o.id', 'o.sn', 'o.amount','o.actually_amount',
                'o.user_id','o.type'
            ],
        ];
        $orderInfo = $modelOrder->getInfo($config);
        if($orderInfo['actually_amount']<=0){
            $this -> error('支付不能为0');
        }
        $payInfo = [
            'sn'=>$orderInfo['sn'],
            'actually_amount'=>$orderInfo['actually_amount'],
            'return_url' => $this->host.url('payComplete'),
            'cancel_url' => $this->host.url('payCancel'),
            'fail_url' => $this->host.url('payFail'),
            'notify_url'=>$this->host."/index/".config('wx_config.call_back_url'),
        ];
        $payCode = input('pay_code','0','int');
        //微信支付
        if($payCode == 1){
            $payInfo['notify_url'] = $this->host."/index.php/index/CallBack/weixinBack/type/order";
            \common\component\payment\weixin\weixinpay::wxPay($payInfo);
        }
        //支付宝支付
        if($payCode == 2){
            $payInfo['notify_url'] = $this->host."/index.php/index/CallBack/aliBack/type/order";
            $model = new \common\component\payment\alipay\alipay;
            $model->aliPay($payInfo);
        }
        //银联支付
        if($payCode == 3){
            $payInfo['notify_url'] = $this->host."/index.php/index/CallBack/unionBack/type/order";
            $model = new \common\component\payment\unionpay\unionpay;
            $model->unionPay($payInfo);
        }
        //银联支付
        if($payCode == 4){
            $modelOrder = new \app\index\model\Order();
            $config = [
                'where' => [
                    ['o.status', '=', 0],
                    ['o.sn', '=', $orderSn],
                    ['o.user_id', '=', $this->user['id']],
                ], 'field' => [
                    'o.id', 'o.sn', 'o.amount',
                    'o.user_id', 'o.actually_amount', 'o.order_status'
                ],
            ];
            $orderInfo = $modelOrder->getInfo($config);
            if ($orderInfo['order_status'] > 1) {
                return errorMsg('订单已处理',['code'=>1]);
            }

            $modelWallet = new \app\index\model\Wallet();
            $config = [
                'where'=>[
                    ['status', '=', 0],
                    ['user_id', '=', $this->user['id']],
                ]
            ];
            $walletInfo = $modelWallet->getInfo($config);
            if($walletInfo['amount'] < $orderInfo['actually_amount']){
                $modelOrder->rollback();
                //返回状态给微信服务器
                return errorMsg('余额不够',['code'=>2]);
            }
            $modelOrder ->startTrans();
            $modelWalletDetail = new \app\index\model\WalletDetail();
            $orderInfo['pay_sn'] = generateSN();
            $orderInfo['payment_time'] = time();
            $res = $modelWalletDetail->walletPaymentHandle($orderInfo);
            if(!$res['status'] ){
                $modelOrder->rollback();
                //返回状态给微信服务器
                return errorMsg('失败');
            }
            $data = [
                'payment_code'=>4,
                'pay_sn'=> $orderInfo['pay_sn'],
                'payment_time'=> $orderInfo['payment_time'],
                'order_sn'=> $orderInfo['sn'],
            ];
            $res = $modelOrder->orderHandle($data, $orderInfo);
            if(!$res['status']){
                $modelOrder->rollback();
                //返回状态给微信服务器
                return errorMsg('失败');
            }
            $modelOrder->commit();
            return successMsg('成功');
        }
    }

    //充值-支付
    public function rechargePayment(){
        //微信支付
        if( empty(input('amount')) ||  empty(input('?pay_code'))){
            $this -> error('参数错误');
        }
        $model = new \app\index\model\WalletDetail();
        $amount = input('amount/f');
        //生成充值明细
        $WalletDetailSn = generateSN();
        $data = [
            'sn'=>$WalletDetailSn,
            'user_id'=>$this->user['id'],
            'amount'=>$amount,
            'create_time'=>time()
        ];
        $res = $model->isUpdate(false)->save($data);
        if(!$res){
            $this -> error('生成充值明细失败');
        }
        //支付信息
        $payInfo = [
            'sn'=>$WalletDetailSn,
            'actually_amount'=>$amount,
            'return_url' => $this->host.url('payComplete'),
            'cancel_url' => $this->host.url('payCancel'),
            'fail_url' => $this->host.url('payFail'),
            'notify_url'=>$this->host."/index/".config('wx_config.call_back_url'),
        ];
        $payCode = input('pay_code','0','int');
        //微信支付
        if($payCode == 1){
            $payInfo['notify_url'] = $this->host."/index.php/index/CallBack/weixinBack/type/recharge";
            \common\component\payment\weixin\weixinpay::wxPay($payInfo);
        }
        //支付宝支付
        if($payCode == 2){
            $payInfo['notify_url'] = $this->host."/index.php/index/CallBack/aliBack/type/recharge";
            $model = new \common\component\payment\alipay\alipay;
            $model->aliPay($payInfo);
        }
        //银联支付
        if($payCode == 3){
            $payInfo['notify_url'] = $this->host."/index.php/index/CallBack/unionBack/type/recharge";
            $model = new \common\component\payment\unionpay\unionpay;
            $model->unionPay($payInfo);
        }
    }

   //支付完跳转的页面
    public function payComplete(){
        $arr = $_GET;
        $model = new \common\component\payment\alipay\alipay;
        $result = $model->check($arr);
        return $this->fetch();
    }
    //取消支付完跳转的页面
    public function payCancel(){
        return $this->fetch();
    }
    //支付失败完跳转的页面
    public function payFail(){
        return $this->fetch();
    }

}