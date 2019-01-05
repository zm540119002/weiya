<?php
namespace app\index\controller;
//use Think\Controller;
use common\component\payment\unionpay\sdk\AcpService;
use common\component\payment\unionpay\sdk\SDKConfig;
//use common\component\payment\alipay\lib\AlipayNotify;
class CallBack extends \common\controller\Base
{
    public function weixinBack(){
        $xml = file_get_contents('php://input');
//        $xml = "<xml><appid><![CDATA[wx9eee7ee8c2ae57dc]]></appid>
//<attach><![CDATA[weixin]]></attach>
//<bank_type><![CDATA[HXB_CREDIT]]></bank_type>
//<cash_fee><![CDATA[1]]></cash_fee>
//<fee_type><![CDATA[CNY]]></fee_type>
//<is_subscribe><![CDATA[Y]]></is_subscribe>
//<mch_id><![CDATA[1234887902]]></mch_id>
//<nonce_str><![CDATA[jx0ylr9h1t3nel5ukt4cqiy4xqk729n0]]></nonce_str>
//<openid><![CDATA[oNalMuA6iE-T45TPb_ZeQYlJ3Jjk]]></openid>
//<out_trade_no><![CDATA[20190105135318529087889079702409]]></out_trade_no>
//<result_code><![CDATA[SUCCESS]]></result_code>
//<return_code><![CDATA[SUCCESS]]></return_code>
//<sign><![CDATA[2C2F10429F4B7E9BD6982E1D9B773A09]]></sign>
//<time_end><![CDATA[20190105141014]]></time_end>
//<total_fee>1</total_fee>
//<trade_type><![CDATA[NATIVE]]></trade_type>
//<transaction_id><![CDATA[4200000232201901056153817685]]></transaction_id>
//</xml>
//";
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
        if ( ($data['return_code'] == 'SUCCESS') && ($data['result_code'] == 'SUCCESS')) {
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

                file_put_contents('c.txt',$modelOrder->getLastSql());
                if ($orderInfo['order_status'] > 1) {
                    return successMsg('已回调过，订单已处理');
                }
                if ($orderInfo['actually_amount'] * 100 != $data['total_fee']) {//校验返回的订单金额是否与商户侧的订单金额一致
                    //返回状态给微信服务器
                    return errorMsg('回调的金额和订单的金额不符，终止购买');
                }
                file_put_contents('c.txt','2222');
                $res = $this->orderHandle($data, $orderInfo);
                if ($res['status']) {
                    $this->successReturn();
                } else {
                    $this->errorReturn();
                }
            }
            if ($order_type == 'recharge') {
                $res = $this->rechargeHandle($data);
                if ($res['status']) {
                    $this->successReturn();
                } else {
                    $this->errorReturn();
                }
            }

            if ($order_type == 'group_buy') {
                $res = $this->groupBuyHandle($data);
                if ($res['status']) {
                    $this->successReturn();
                } else {
                    $this->errorReturn();
                }
            }
        } else {
            //返回状态给微信服务器
            $this->errorReturn($data['out_trade_no']);
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
       if ($data['respMsg'] == 'Success!') {
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
               $res = $this->orderHandle($data, $orderInfo);
               if ($res['status']) {
                   echo "success"; // 处理成功
               } else {
                   echo "fail"; //验证失败
               }
           }

           if ($order_type == 'recharge') {
               $res = $this->rechargeHandle($data);
               if ($res['status']) {
                   echo "success"; // 处理成功
               } else {
                   echo "fail"; //验证失败
               }
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
        if ($data['trade_status'] == 'TRADE_SUCCESS') {
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
                $res = $this->orderHandle($payInfo, $orderInfo);

                if (!$res['status']) {
                    echo "fail";    //请不要修改或删除
                } else {
                    echo "success"; //请不要修改或删除
                }
            }
        }
    }

    /**
     * @param $data
     * 普通订单支付回调
     */

    private function orderHandle($data, $orderInfo)
    {
        $modelOrder = new \app\index\model\Order();
        $modelOrder->startTrans();
        //更新订单状态
        $data2 = [];
        $data2['order_status'] = 2;
        $data2['payment_code'] = $data['payment_code'];
        $data2['pay_sn'] = $data['pay_sn'];
        $data2['payment_time'] = $data['payment_time'];
        $condition = [
            ['user_id', '=', $orderInfo['user_id']],
            ['sn', '=', $data['order_sn']],
        ];
        $res = $modelOrder->allowField(true)->save($data2,$condition);
        file_put_contents('d.txt',$modelOrder->getLastSql());
        if($res === false){
            $modelOrder->rollback();
            //返回状态给微信服务器
            return errorMsg('失败');
        }
//        //根据订单号查询关联的商品
//        $modelOrderDetail = new \app\index\model\OrderDetail();
//        $config = [
//            'where' => [
//                ['od.status', '=', 0],
//                ['od.father_order_id', '=', $orderInfo['id']],
//            ], 'field' => [
//                'od.goods_id', 'od.price', 'od.num', 'od.store_id','od.father_order_id'
//            ]
//        ];
//
//        $orderDetailList = $modelOrderDetail->getList($config);
//        $modelOrderChild = new \app\index\model\OrderChild();
//
//        //生成子订单
//        $rse = $modelOrderChild -> createOrderChild($orderDetailList);
//        if(!$rse['status']){
//            $modelOrder->rollback();
//            return errorMsg($modelOrder->getLastSql());
//        }
        $modelOrder->commit();//提交事务
        //返回状态给微信服务器
        return successMsg('成功');

    }

    //生成子订单  子订单和order_detail表的关联
    private function splitOrder(){

    }

    /**充值支付回调
     * @param $parameter
     */
    private function rechargeHandle($data)
    {

    }

    /**团购订单支付回调
     * @param $parameter
     */
    private function groupBuyHandle($data)
    {

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