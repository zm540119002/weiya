<?php
namespace app\index\controller;

class Wallet extends \common\controller\UserBase{
    /**首页
     */
    public function index(){
        return $this->fetch();
    }

    /**登录
     */
    public function login(){
        if (request()->isAjax()) {
            $model = new \app\index\model\Wallet();;
            $postData = input('post.');
            $postData['user_id'] = $this->user['id'];
            return $model->login($postData);
        } else {
            return $this->fetch();
        }
    }
    
    /**忘记密码 /注册
     */
    public function forgetPassword(){
        if (request()->isAjax()) {
            $model = new \app\index\model\Wallet();;
            $postData = input('post.');
            $postData['user_id'] = $this->user['id'];
            return $model->resetPassword($postData);
        } else {
            return $this->fetch();
        }
    }

    /**
     * 钱包充值页面
     */
    public function recharge(){
        if (request()->isAjax()) {
        } else {
//            if(isWxBrowser() && !request()->isAjax()) {//判断是否为微信浏览器
//                $payOpenId =  session('pay_open_id');
//                if(empty($payOpenId)){
//                    $tools = new \common\component\payment\weixin\getPayOpenId(config('wx_config.appid'), config('wx_config.appsecret'));
//                    $payOpenId  = $tools->getOpenid();
//                    session('pay_open_id',$payOpenId);
//                }
//            }
            return $this->fetch();
        }
    }

    //订单支付
    function orderPayment(){
        $orderSn = input('post.order_sn');
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
            //返回状态
            return errorMsg('余额不足，请先充值',['code'=>2]);
        }
        $modelOrder ->startTrans();
        $modelWalletDetail = new \app\index\model\WalletDetail();
        $orderInfo['pay_sn'] = generateSN();
        $orderInfo['payment_time'] = time();
        $res = $modelWalletDetail->walletPaymentHandle($orderInfo);
        if(!$res['status'] ){
            $modelOrder->rollback();
            //返回状态
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
            //返回状态
            return errorMsg('失败');
        }
        $modelOrder->commit();
        return successMsg('成功');
    }
}