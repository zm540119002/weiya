<?php
namespace app\index\controller;
require_once dirname(__DIR__).'./../../../common/component/payment/weixin/WxPay.JsApiPay.php';
class Payment extends \common\controller\UserBase{
    //订单-支付
    public function orderPayment(){
        //微信支付
        if( !empty(input('order_sn')) && !empty(input('?pay_code'))){
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
            $payInfo = [
                'sn'=>$orderInfo['sn'],
                'actually_amount'=>0.01,
                'return_url' => $this->host.url('payComplete'),
                'cancel_url' => $this->host.url('payCancel'),
                'fail_url' => $this->host.url('payFail'),
                'notify_url'=>$this->host."/index/".config('wx_config.call_back_url'),
            ];
            $payCode = input('pay_code','0','int');
            //微信支付
            if($payCode == 1){
                $payInfo['notify_url'] = $this->host."/index.php/index/CallBack/weixinCallBack/type/order";
                \common\component\payment\weixin\weixinpay::wxPay($payInfo);
            }
            //支付宝支付
            if($payCode == 2){
                $payInfo['notify_url'] = $payInfo['notify_url'].'/ali.order';
                $model = new \common\component\payment\alipay\alipay;
                $model->aliPay($payInfo);
            }
            //银联支付
            if($payCode == 3){
                $payInfo['notify_url'] = $payInfo['notify_url'].'/union.order';
                $model = new \common\component\payment\unionpay\unionpay;
                $model->unionPay($payInfo);
            }
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