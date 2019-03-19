<?php
namespace app\index\controller;
class Test extends \common\controller\Base{
    /**首页
     */
    public function index(){
        $urlToken = "https://open.api.clife.cn/apigateway/commons/clife-open-api-app/cloud/token";
        $data = [
            'appId'=>'31316',
            'appSecret'=>'0ab242fc269f4119bc9f4ad9e6884332',
            'timestamp'=>time(),
        ];
        $res = $this->http_request($urlToken,$data);
        print_r($res);exit;

    }

    //HTTP请求（支持HTTP/HTTPS，支持GET/POST）
    protected function http_request($url, $data = null)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
        if (!empty($data)){
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        }
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
        $output = curl_exec($curl);
        curl_close($curl);
        // var_dump($output);
        return $output;
    }
}