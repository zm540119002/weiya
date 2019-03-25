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
//            print_r(json_encode($curl->response->data));
//            exit;
            $returnData = [];
            //解析
            foreach ($curl->response->data as $key=>$val){
                $val = (array)$val;
                //眼型
                if($key === 'eyeshape'){
                    foreach (config('clife.eyeshape') as $v){
                        if($v['type'] == $val){
                            $returnData[] = '眼型： ' . $v['explain'];
                        }
                    }
                }
                //黑头
                if($key === 'blackHead'){
                    $returnData[] = '黑头： ' . config('clife.blackHeadLevel')[$val['level']] . '，数量：' . $val['number'];
                }
                //毛孔
                if($key === 'pore'){
                    $returnData[] = '毛孔： ' . config('clife.poreLevel')[$val['level']] . '，数量：' . $val['number'];
                }
                //脸型
                if($key === 'faceshape'){
                    print_r($val);
                    $returnData[] = '脸型： ' . config('clife.faceshape')[$val];
                }
            }
            print_r($returnData);
        }
    }
}