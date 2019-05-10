<?php
namespace app\index\controller;

class Wallet extends \common\controller\UserBase{
    protected $wallet = null;
    public function __construct(){
        parent::__construct();
        // 平台初始化
        $this->wallet = session('wallet'.$this->user['id']);
        if (empty( $this->wallet)){
            $model = new \app\index\model\Wallet();
            $config = [
                'where' => [
                    ['status', '=', 0],
                    ['user_id', '=', $this->user['id']],
                ], 'field' => [
                    'id','user_id','status','amount','password'
                ],
            ];
            $this->wallet = $model->getInfo($config);
            if(empty( $this->wallet)){
                $model->isUpdate(false)->save(['user_id'=>$this->user['id']]);
                $this->wallet = $model->getInfo($config);
            }
            session('wallet'.$this->user['id'],$this->wallet->toArray());
        }
        // 判断是否已开通钱包,后面改进此方法
        if( in_array(request()->action(),['recharge']) ){
            if(empty($this->wallet['password'])){
                $this->assign('user',$this->user);
                echo $this->fetch('wallet_opening');
                exit;
            }
        }
    }

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
            $amount = input('post.amount/f');
            $payCode= input('post.pay_code/d');
            if( !$amount || !$payCode ){
                return $this->errorMsg('参数错误');
            }
            //生成充值明细
            $walletDetailSn = generateSN();
            $data = [
                'sn'=>$walletDetailSn,
                'user_id'=>$this->user['id'],
                'amount'=>$amount,
                'actually_amount'=>$amount, // 还没有其它的业务 暂时先用$amount
                'create_time'=>time(),
                'pay_code'=>$payCode,
            ];
            // 线下汇款凭证
            if( isset($_POST['voucher']) && $_POST['voucher'] ){
                $data['voucher_img'] = moveImgFromTemp(config('upload_dir.scheme'),$_POST['voucher']);
            }

            $model= new \app\index\model\WalletDetail();
            $res  = $model->isUpdate(false)->save($data);
            if(!$res){
                return $this->errorMsg('充值失败');

            }
            //生成支付表的数据
            $modelPay = new \app\index\model\Pay();
            //增加
            $data = [
                'sn' => $walletDetailSn,
                'actually_amount' =>$amount,
                'user_id' => $this->user['id'],
                'pay_code' => $payCode,
                'type' => config('custom.pay_type')['rechargePay']['code'],
            ];
            $result  = $modelPay->isUpdate(false)->save($data);
            if(!$result){
                $model->rollback();
                return $this->errorMsg('失败');
            }
            // 各充值方式的处理
            switch($payCode){
                case config('custom.recharge_code.WeChatPay.code') :
                case config('custom.recharge_code.Alipay.code') :
                case config('custom.recharge_code.UnionPay.code') :
                    $url = config('custom.pay_recharge').$walletDetailSn;
                    return successMsg($url);
                    break;

                case config('custom.recharge_code.OfflinePay.code') :
                    // 更新状态
                    $result  = $model->isUpdate(false)->save(['sn'=>$walletDetailSn]);
                    return successMsg('成功');
                    break;
            }

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
            return $this->errorMsg('订单已处理',['code'=>1]);
        }
        if($this->wallet['amount'] < $orderInfo['actually_amount']){
            //返回状态
            return $this->errorMsg('余额不足，请先充值',['code'=>2]);
        }
        $modelOrder ->startTrans();
        $modelWalletDetail = new \app\index\model\WalletDetail();
        $orderInfo['pay_sn'] = generateSN();
        $orderInfo['payment_time'] = time();
        $res = $modelWalletDetail->walletPaymentHandle($orderInfo);
        if(!$res['status'] ){
            $modelOrder->rollback();
            //返回状态
            return $this->errorMsg('失败');
        }
        $data = [
            'pay_code'=>4,
            'pay_sn'=> $orderInfo['pay_sn'],
            'payment_time'=> $orderInfo['payment_time'],
            'order_sn'=> $orderInfo['sn'],
        ];
        $res = $modelOrder->orderHandle($data, $orderInfo);
        if(!$res['status']){
            $modelOrder->rollback();
            //返回状态
            return $this->errorMsg('失败');
        }
        $modelOrder->commit();
        return successMsg('成功');
    }
}