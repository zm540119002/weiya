<?php
namespace app\index\controller;
use \common\component\curl\Curl;

class Api extends \common\controller\Base{
    /**首页
     */
    public function index(){
        $curl = new Curl();
        $urlToken = 'https://open.api.clife.cn/apigateway/commons/clife-open-api-app/cloud/token/';
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
        $url = 'https://open.api.clife.cn/apigateway/commons/clife-open-api-app/cloud/skinImageAnalysis/photograph/analysis';
        $data2 = [
            'appId'=>31316,
            'accessToken'=>$accessToken,
            'timestamp'=> time().'000',
            'image'=>'@static/common/img/ldh.jpg',
        ];
        $curl->post($url, $data2);
        if ($curl->error) {
            echo 'Error: ' . $curl->errorCode . ': ' . $curl->errorMessage . "\n";
        } else {
            $returnData = [];
            foreach ($curl->response->data as $key=>$val){
                //解析眼型
                if($key === 'eyeshape'){
                    foreach (config('clife.eyeshape') as $v){
                        if($v['type'] == (array)($val)){
                            $returnData[] = '眼型： ' . $v['explain'];
                        }
                    }
                }
                //解析黑头
                if($key === 'blackHead'){
                    $returnData[] = '黑头： ' . config('clife.blackHeadLevel')[$val['level']] . '，数量：' . $val['number'];
                }
            }
            print_r($returnData);
        }
    }
}