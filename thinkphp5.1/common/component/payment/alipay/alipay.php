<?php


namespace common\component\payment\alipay;

require_once dirname ( __FILE__ ).DIRECTORY_SEPARATOR.'wappay/service/AlipayTradeService.php';
require_once dirname ( __FILE__ ).DIRECTORY_SEPARATOR.'wappay/buildermodel/AlipayTradeWapPayContentBuilder.php';
require_once dirname ( __FILE__ ).DIRECTORY_SEPARATOR.'wappay/buildermodel/AlipayTradeQueryContentBuilder.php';

/**
 * 支付 逻辑定义
 * Class AlipayPayment
 * @package Home\Payment
 */

class alipay
{
    public $alipay_config = array();// 支付宝支付配置参数

    /**
     * 析构流函数
     */
    public function  __construct() {
        //支付配置
        require dirname ( __FILE__ ).DIRECTORY_SEPARATOR.'config.php';
        $this->alipay_config = $config;
    }
    /**
     * 生成支付代码
     * @param   array   $order      订单信息
     *
     */
    function aliPay($payInfo)
    {
        if (!empty($payInfo['sn'])&& trim($payInfo['sn'])!=""){
            //商户订单号，商户网站订单系统中唯一订单号，必填
            $timeout_express="10m";//该笔订单允许的最晚付款时间，逾期将关闭交易。取值范围：1m～15d。m-分钟，h-小时，d-天，1c-当天（1c-当天的情况下，无论交易何时创建，都在0点关闭）。 该参数数值不接受小数点， 如 1.5h，可转换为 90m。
            $payRequestBuilder = new \AlipayTradeWapPayContentBuilder();
            $payRequestBuilder->setBody('美尚云');//对一笔交易的具体描述信息。如果是多种商品，请将商品描述字符串累加传给body。
            $payRequestBuilder->setSubject('美尚云');//	商品的标题/交易标题/订单标题/订单关键字等。
            $payRequestBuilder->setOutTradeNo($payInfo['sn']);//商户网站唯一订单号 最长64位
            $payRequestBuilder->setTotalAmount($payInfo['actually_amount']);//订单总金额，单位为元，精确到小数点后两位，取值范围[0.01,100000000]
            $payRequestBuilder->setTimeExpress($timeout_express);//
            $payResponse = new \AlipayTradeService($this->alipay_config);
            $result=$payResponse->wapPay($payRequestBuilder,$payInfo['return_url'],$payInfo['notify_url']);
            return ;
        }


    }

    /**
     * 交易订单查询
     * @param $orderInfo ///订单详情
     * @return bool|\SimpleXMLElement[]|string|\//提交表单HTML文本
     */
    public function orderQuery($orderInfo){
        if (!empty($orderInfo['sn']) || !empty($orderInfo['pay_sn'])){

            //商户订单号和支付宝交易号不能同时为空。 trade_no、  out_trade_no如果同时存在优先取trade_no
            //商户订单号，和支付宝交易号二选一
            $out_trade_no = trim($orderInfo['sn']);

            //支付宝交易号，和商户订单号二选一
            $trade_no = trim($orderInfo['pay_sn']);
            $RequestBuilder = new \AlipayTradeQueryContentBuilder();
            $RequestBuilder->setTradeNo($trade_no);
            $RequestBuilder->setOutTradeNo($out_trade_no);

            $Response = new \AlipayTradeService( $this->alipay_config);
            $result=$Response->Query($RequestBuilder);
            return $result;
        }
    }


}