<?php
namespace app\index\controller;
class CallBack extends \common\controller\Base
{
    public function weixinBack(){
        $xml = file_get_contents('php://input');
        $data = xmlToArray($xml);
        $data_sign = $data['sign'];
        //sign不参与签名算法
        unset($data['sign']);
        $sign = $this->makeSign($data);
        $data['payment_code'] = 1;//weixin 支付
        $data['actually_amount'] = $data['total_fee'];//支付金额
        $data['pay_sn'] = $data['transaction_id'];//服务商返回的交易号
        $data['order_sn'] = $data['out_trade_no'];//系统的订单号
        $data['payment_time'] = $data['time_end'];//支付时间

        // 判断签名是否正确  判断支付状态
        if ( ($data_sign !== $sign) || ($data['return_code'] !== 'SUCCESS') || ($data['result_code'] !== 'SUCCESS')) {
            //返回状态给微信服务器
            $this->errorReturn($data['out_trade_no']);
        }

        $order_type = '';
        if(input('?type')){
            $order_type =input('type');
        }
        if ($order_type == 'order') {
            $modelOrder = new \app\index\model\Order();
            $config = [
                'where' => [
                    ['o.status', '=', 0],
                    ['o.sn', '=', $data['out_trade_no']],
                ], 'field' => [
                    'o.id', 'o.sn', 'o.amount',
                    'o.user_id', 'o.actually_amount', 'o.order_status'
                ],
            ];
            $orderInfo = $modelOrder->getInfo($config);
            if ($orderInfo['order_status'] > 1) {
                return successMsg('已回调过，订单已处理');
            }
            if ($orderInfo['actually_amount'] * 100 != $data['total_fee']) {//校验返回的订单金额是否与商户侧的订单金额一致
                //返回状态给微信服务器
                return errorMsg('回调的金额和订单的金额不符，终止购买');
            }
            $res = $modelOrder->orderHandle($data, $orderInfo);
            if ($res['status']) {
                $this->successReturn();
            } else {
                $this->errorReturn();
            }
        }
        //充值
        if ($order_type == 'recharge') {
            $modelWalletDetail = new \app\index\model\WalletDetail();
            $config = [
                'where' => [
                    ['wd.status', '=', 0],
                    ['wd.sn', '=', $data['out_trade_no']],
                ], 'field' => [
                    'wd.id', 'wd.sn', 'wd.amount',
                    'wd.user_id','wd.recharge_status'
                ],
            ];
            $info = $modelWalletDetail->getInfo($config);
            if ($info['recharge_status'] == 1) {
                return successMsg('已回调过，订单已处理');
            }
            if ($info['amount'] * 100 != $data['total_fee']) {//校验返回的订单金额是否与商户侧的订单金额一致
                //返回状态给微信服务器
                return errorMsg('回调的金额和订单的金额不符，终止购买');
            }
            $res = $modelWalletDetail->rechargeHandle($data,$info);
            if ($res['status']) {
                $this->successReturn();
            } else {
                $this->errorReturn();
            }
        }

    }


   public function unionBack(){
       $order_type = '';
       if(input('?type')){
           $order_type =input('type');
       }
       $data = $_POST;
       //计算得出通知验证结果
//       $unionpayNotify = new AcpService(); // 使用银联原生自带的累 和方法 这里只是引用了一下 而已
//       $verify_result =$unionpayNotify->validate($data);
//       $verify_result =  \common\component\payment\unionpay\sdk\AcpService::validate($data);
//       print_r($verify_result);exit;
//       if (!$verify_result){
//           echo "fail"; //验证失败
//           die;
//       }
       $data['payment_code'] = 3;
       $data['order_sn'] = $data['orderId'];//系统的订单号
       $data['actually_amount'] = $data['txnAmt'];//支付金额
       $data['pay_sn'] = $data['queryId'];//服务商返回的交易号
       $data['payment_time'] = $data['time_end'];//支付时间
       // 解释: 交易成功且结束，即不可再做任何操作。
       if ($data['respMsg'] !== 'Success!') {
           echo "success";
          die;
       }
       // 修改订单支付状态
       if ($order_type == 'order') {
           $modelOrder = new \app\index\model\Order();
           $config = [
               'where' => [
                   ['o.status', '=', 0],
                   ['o.sn', '=', $data['order_sn']],
               ], 'field' => [
                   'o.id', 'o.sn', 'o.amount',
                   'o.user_id', 'o.actually_amount', 'o.order_status',
               ],
           ];
           $orderInfo = $modelOrder->getInfo($config);
           if ($orderInfo['order_status'] > 1) {
               echo "success";
               return successMsg('已回调过，订单已处理');
           }
           if ($orderInfo['actually_amount'] * 100 != $data['actually_amount']) {//校验返回的订单金额是否与商户侧的订单金额一致
               //返回状态给微信服务器
               echo "fail"; //验证失败
               return errorMsg('回调的金额和订单的金额不符，终止购买');
           }
           $res = $modelOrder->orderHandle($data, $orderInfo);
           if ($res['status']) {
               echo "success"; // 处理成功
           } else {
               echo "fail"; //验证失败
           }
       }

       if ($order_type == 'recharge') {
           $modelWalletDetail = new \app\index\model\WalletDetail();
           $config = [
               'where' => [
                   ['wd.status', '=', 0],
                   ['wd.sn', '=', $data['out_trade_no']],
               ], 'field' => [
                   'wd.id', 'wd.sn', 'wd.amount',
                   'wd.user_id','wd.recharge_status'
               ],
           ];
           $info = $modelWalletDetail->getInfo($config);
           if ($info['recharge_status'] == 1) {
               return successMsg('已回调过，订单已处理');
           }
           if ($info['amount'] * 100 != $data['total_fee']) {//校验返回的订单金额是否与商户侧的订单金额一致
               //返回状态给微信服务器
               return errorMsg('回调的金额和订单的金额不符，终止购买');
           }
           $res = $modelWalletDetail->rechargeHandle($data,$info);
           if ($res['status']) {
               echo "success"; // 处理成功
           } else {
               echo "fail"; //验证失败
           }
       }


   }


    //支付宝支付回调处理
    public function aliBack()
    {
        $order_type = '';
        if(input('?type')){
            $order_type =input('type');
        }
        $data = $_POST;
        $payInfo['payment_code'] = 2; //支付类型
        $payInfo['order_sn'] = $data['out_trade_no'];//系统的订单号
        $payInfo['actually_amount'] = $data['receipt_amount'];//支付金额
        $payInfo['pay_sn'] = $data['trade_no'];//服务商返回的交易号
        $payInfo['payment_time'] = $data['gmt_payment'];//支付时间
//        $alipaySevice = new \AlipayTradeService($config);
//        $alipaySevice->writeLog(var_export($_POST, true));
//        $result = $alipaySevice->check($_POST);
//        if(!$result){
//            echo "fail"; //验证失败
//           die;
//        }
        if ($data['trade_status'] !== 'TRADE_SUCCESS') {
            echo "fail"; //验证失败
            die;
        }
        //判断该笔订单是否在商户网站中已经做过处理
        //如果没有做过处理，根据订单号（out_trade_no）在商户网站的订单系统中查到该笔订单的详细，并执行商户的业务程序
        //请务必判断请求时的total_amount与通知时获取的total_fee为一致的
        //如果有做过处理，不执行商户的业务程序
        //注意：
        //付款完成后，支付宝系统发送该交易状态通知
        if ($order_type == 'order') {
            $modelOrder = new \app\index\model\Order();
            $config = [
                'where' => [
                    ['o.status', '=', 0],
                    ['o.sn', '=', $payInfo['order_sn']],
                ], 'field' => [
                    'o.id', 'o.sn', 'o.amount',
                    'o.user_id', 'o.actually_amount','o.order_status'
                ],
            ];
            $orderInfo = $modelOrder->getInfo($config);
            if ($orderInfo['order_status'] > 1) {
                echo "success";
                return successMsg('已回调过，订单已处理');
            }
            if ($orderInfo['actually_amount'] != $data['actually_amount']) {//校验返回的订单金额是否与商户侧的订单金额一致
                //返回状态给微信服务器
                return errorMsg('回调的金额和订单的金额不符，终止购买');
            }
            $res = $modelOrder->orderHandle($payInfo, $orderInfo);

            if (!$res['status']) {
                echo "fail";    //请不要修改或删除
            } else {
                echo "success"; //请不要修改或删除
            }
        }
        if ($order_type == 'recharge') {
            $modelWalletDetail = new \app\index\model\WalletDetail();
            $config = [
                'where' => [
                    ['wd.status', '=', 0],
                    ['wd.sn', '=', $data['out_trade_no']],
                ], 'field' => [
                    'wd.id', 'wd.sn', 'wd.amount',
                    'wd.user_id','wd.recharge_status'
                ],
            ];
            $info = $modelWalletDetail->getInfo($config);
            if ($info['recharge_status'] == 1) {
                return successMsg('已回调过，订单已处理');
            }
            if ($info['amount'] != $data['total_fee']) {//校验返回的订单金额是否与商户侧的订单金额一致
                //返回状态给微信服务器
                return errorMsg('回调的金额和订单的金额不符，终止购买');
            }
            $res = $modelWalletDetail->rechargeHandle($data,$info);
            if ($res['status']) {
                echo "success"; // 处理成功
            } else {
                echo "fail"; //验证失败
            }
        }
    }



    //成功返回
    private function successReturn()
    {
        echo '<xml><return_code><![CDATA[SUCCESS]]></return_code><return_msg><![CDATA[OK]]></return_msg></xml>';
        return true;
    }

    //失败返回
    private function errorReturn($dataSn = '', $error = '签名错误', $type = '订单')
    {
        \Think\Log::write($type . '支付失败：' . $dataSn . "\r\n失败原因：" . $error, 'NOTIC');
        echo '<xml><return_code><![CDATA[FAIL]]></return_code><return_msg><![CDATA[签名失败]]></return_msg></xml>';
        return false;
    }


    /**
     * 微信生成签名
     * @return 签名，本函数不覆盖sign成员变量
     */
    private function makeSign($data)
    {
        //获取微信支付秘钥
        $key = config('wx_config.key');
        // 去空
        $data = array_filter($data);
        //签名步骤一：按字典序排序参数
        ksort($data);
        $string_a = http_build_query($data);
        $string_a = urldecode($string_a);
        //签名步骤二：在string后加入KEY
        //$config=$this->config;
        $string_sign_temp = $string_a . "&key=" . $key;
        //签名步骤三：MD5加密
        $sign = md5($string_sign_temp);
        // 签名步骤四：所有字符转为大写
        $result = strtoupper($sign);
        return $result;
    }
}