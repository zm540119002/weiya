<?php
namespace app\index\controller;
use Think\Controller;
use common\component\payment\unionpay\sdk\AcpService;
use common\component\payment\alipay\lib\AlipayNotify;
use common\component\payment\weixin\Jssdk;
class CallBack extends \common\controller\Base
{
    //支付回调
    public function notifyUrl()
    {
        if (strpos($_SERVER['QUERY_STRING'], 'weixin.order') == true) {
            $this->callBack('weixin', 'order');
        }
        if (strpos($_SERVER['QUERY_STRING'], 'weixin.recharge') == true) {
            $this->callBack('weixin', 'recharge');
        }
        if (strpos($_SERVER['QUERY_STRING'], 'weixin.group_buy') == true) {
            $this->callBack('weixin', 'group_buy');
        }
        //支付宝回调
        if (strpos($_SERVER['QUERY_STRING'], 'ali.order') == true) {
            $this->callBack('ali', 'order');
        }
        if (strpos($_SERVER['QUERY_STRING'], 'ali.recharge') == true) {
            $this->callBack('ali', 'recharge');
        }
        if (strpos($_SERVER['QUERY_STRING'], 'ali.group_buy') == true) {
            $this->callBack('ali', 'group_buy');
        }
        //银联回调
        if (strpos($_SERVER['QUERY_STRING'], 'union.recharge') == true) {
            $this->callBack('union', 'recharge');
        }
        if (strpos($_SERVER['QUERY_STRING'], 'union.order') == true) {
            $this->callBack('union', 'order');
        }
        if (strpos($_SERVER['QUERY_STRING'], 'union.group_buy') == true) {
            $this->callBack('union', 'group_buy');
        }

    }

    /**
     * @param $data ///支付商返回的数据
     * @param $payment_type //支付方式
     * @param $order_type //支付单类型
     */
    //支付完成，调用不同的支付的回调处理
    private function callBack($payment_type, $order_type)
    {

        if ($payment_type == 'weixin') {
            $this->weixinBack($order_type);
        }
        if ($payment_type == 'ali') {
            $this->aliBack($order_type);
        }
        if ($payment_type = 'union') {
            $this->unionBack($order_type);
        }
    }


    //微信支付回调处理
    private function weixinBack($order_type)
    {
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
        if ($sign === $data_sign && ($data['return_code'] == 'SUCCESS') && ($data['result_code'] == 'SUCCESS')) {
            if ($order_type == 'order') {
                $modelOrder = new \app\index\model\Order();
                $config = [
                    'where' => [
                        ['o.status', '=', 0],
                        ['o.sn', '=', $data['order_sn']],
                    ], 'field' => [
                        'o.id', 'o.sn', 'o.amount',
                        'o.user_id', 'o.actually_amount', 'o.order_status'
                    ],
                ];
                $orderInfo = $modelOrder->getInfo($config);
                if ($orderInfo['order_status'] > 1) {
                    return successMsg('已回调过，订单已处理');
                }
                if ($orderInfo['actually_amount'] * 100 != $data['actually_amount']) {//校验返回的订单金额是否与商户侧的订单金额一致
                    //返回状态给微信服务器
                    return errorMsg('回调的金额和订单的金额不符，终止购买');
                }
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


    //银联支付回调处理
    private function unionBack($order_type)
    {
        $data = $_POST;
        //计算得出通知验证结果

        $unionpayNotify = new AcpService($this->unionpay_config); // 使用银联原生自带的累 和方法 这里只是引用了一下 而已
        $verify_result = $unionpayNotify->validate($data);
        if ($verify_result) //验证成功
        {
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
                            'o.user_id', 'o.actually_amount', 'o.order_status'
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
        } else {
            echo "fail"; //验证失败
        }
    }

    //支付宝支付回调处理
    private function aliBack($order_type)
    {
        require_once dirname(__DIR__) . './../../../common/component/payment/alipay/wappay/service/AlipayTradeService.php';
        require_once dirname(__DIR__) . './../../../common/component/payment/alipay/config.php';
        $data = $_POST;
        $payInfo['payment_code'] = 2; //支付类型
        $payInfo['order_sn'] = $data['out_trade_no'];//系统的订单号
        $payInfo['actually_amount'] = $data['receipt_amount'];//支付金额
        $payInfo['pay_sn'] = $data['trade_no'];//服务商返回的交易号
        $payInfo['payment_time'] = $data['gmt_payment'];//支付时间

        $alipaySevice = new \AlipayTradeService($config);
        $alipaySevice->writeLog(var_export($_POST, true));
        $result = $alipaySevice->check($_POST);
        if ($_POST['trade_status'] == 'TRADE_SUCCESS') {
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
                if ($orderInfo['actually_amount'] * 100 != $data['actually_amount']) {//校验返回的订单金额是否与商户侧的订单金额一致
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
        /* 实际验证过程建议商户添加以下校验。
        1、商户需要验证该通知数据中的out_trade_no是否为商户系统中创建的订单号，
        2、判断total_amount是否确实为该订单的实际金额（即商户订单创建时的金额），
        3、校验通知中的seller_id（或者seller_email) 是否为out_trade_no这笔单据的对应的操作方（有的时候，一个商户可能有多个seller_id/seller_email）
        4、验证app_id是否为该商户本身。
        */
//        if(!$result) {//验证成功
//            file_put_contents('ali3.text',json_encode($data));
//            /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//            //请在这里加上商户的业务逻辑程序代
//
//            //——请根据您的业务逻辑来编写程序（以下代码仅作参考）——
//
//            //获取支付宝的通知返回参数，可参考技术文档中服务器异步通知参数列表
//
//            //商户订单号
//
//            if($_POST['trade_status'] == 'TRADE_FINISHED') {
//
//                //判断该笔订单是否在商户网站中已经做过处理
//                //如果没有做过处理，根据订单号（out_trade_no）在商户网站的订单系统中查到该笔订单的详细，并执行商户的业务程序
//                //请务必判断请求时的total_amount与通知时获取的total_fee为一致的
//                //如果有做过处理，不执行商户的业务程序
//
//                //注意：
//                //退款日期超过可退款期限后（如三个月可退款），支付宝系统发送该交易状态通知
//
//                // 修改订单支付状态
//                if ($order_type == 'order') {
//                    file_put_contents('ali4.text',json_encode($data));
//                    $modelOrder = new \app\purchase\model\Order();
//                    $config = [
//                        'where' => [
//                            ['o.status', '=', 0],
//                            ['o.sn', '=', $data['order_sn']],
//                        ],'field' => [
//                            'o.id', 'o.sn', 'o.amount',
//                            'o.user_id','o.actually_amount','o.logistics_status'
//                        ],
//                    ];
//                    $orderInfo = $modelOrder->getInfo($config);
//                    $res = $this->orderHandle($data,$orderInfo);
//                    if($res['status']){
//                        echo "success"; // 处理成功
//                    }else{
//                        echo "fail"; //验证失败
//                    }
//                }
//                //修改支付订单支付状态
//                if ($order_type == 'recharge') {
//                    $this->rechargeHandle($data);
//                }
//
//            }
//
//            else if ($_POST['trade_status'] == 'TRADE_SUCCESS') {
//                //判断该笔订单是否在商户网站中已经做过处理
//                //如果没有做过处理，根据订单号（out_trade_no）在商户网站的订单系统中查到该笔订单的详细，并执行商户的业务程序
//                //请务必判断请求时的total_amount与通知时获取的total_fee为一致的
//                //如果有做过处理，不执行商户的业务程序
//                //注意：
//                //付款完成后，支付宝系统发送该交易状态通知
//
//
//                if ($order_type == 'order') {
//                    file_put_contents('ali5.text',json_encode($data));
//                    $modelOrder = new \app\purchase\model\Order();
//                    $config = [
//                        'where' => [
//                            ['o.status', '=', 0],
//                            ['o.sn', '=', $data['order_sn']],
//                        ],'field' => [
//                            'o.id', 'o.sn', 'o.amount',
//                            'o.user_id','o.actually_amount'
//                        ],
//                    ];
//                    $orderInfo = $modelOrder->getInfo($config);
//                    $res = $this->orderHandle($data,$orderInfo);
//                    if(!$res['status']){
//                        echo "fail";	//请不要修改或删除
//                    }
//                }
//
//                if ($order_type == 'recharge') {
//                    $this->rechargeHandle($data);
//                }
//            }
//            //——请根据您的业务逻辑来编写程序（以上代码仅作参考）——
//            echo "success";		//请不要修改或删除
//
//        }else {
//            //验证失败
//            echo "fail";	//请不要修改或删除
//
//        }

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
        $returnData = $modelOrder->edit($data2, $condition);
        if (!$returnData['status']) {
            $modelOrder->rollback();
            //返回状态给微信服务器
            return errorMsg($modelOrder->getLastSql());
        }
        
        //根据订单号查询关联的商品
        $modelOrderDetail = new \app\index\model\OrderDetail();
        $config = [
            'where' => [
                ['od.status', '=', 0],
                ['od.father_order_id', '=', $orderInfo['id']],
            ], 'field' => [
                'od.goods_id', 'od.price', 'od.num', 'od.store_id',
            ]
        ];
        $orderDetailList = $modelOrderDetail->getList($config);
        $modelOrderChild = new \app\index\model\OrderChild();

        //生成子订单
        $rse = $modelOrderChild -> createOrderChild($orderDetailList);
        if(!$rse['status']){
            $modelOrder->rollback();
            return errorMsg($modelOrder->getLastSql());
        }
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