<?php
namespace app\index\controller;
use \common\component\curl\Curl;

class Api extends \common\controller\Base{
    /**首页
     */
    public function index(){

        $curl = new Curl();
        $urlToken = 'https://open.api.clife.cn/apigateway/commons/clife-open-api-app/cloud/token';
        $data = [
            'appId'=>31316,
            'appSecret'=>'0ab242fc269f4119bc9f4ad9e6884332',
            'timestamp'=> time().'000',
        ];
        $curl->get($urlToken, $data);
        if ($curl->error) {
            echo 'Error: ' . $curl->errorCode . ': ' . $curl->errorMessage . "\n";
        } else {
            $accessToken =$curl->response->data->accessToken;
        }

        print_r($accessToken);
        $url = 'https://open.api.clife.cn/apigateway/commons/clife-open-api-app/cloud/skinImageAnalysis/photograph/analysis';
        $data2 = [
            'appId'=>31316,
            'accessToken'=>$accessToken,
            'timestamp'=> time().'000',
            'image'=>'@static/common/img/ldh.jpg',
        ];
        $header2 =[
            "Content-type:multipart/form-data"
        ];
        $curl->post($url, $data2);
        if ($curl->error) {
            echo 'Error: ' . $curl->errorCode . ': ' . $curl->errorMessage . "\n";
        } else {
            var_dump($curl->response);exit;
        }
    }



}