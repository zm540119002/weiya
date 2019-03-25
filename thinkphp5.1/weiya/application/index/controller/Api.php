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
                            $returnData['eyeshape'] = '眼型： ' . $v['explain'];
                        }
                    }
                }
                //黑头
                if($key === 'blackHead'){
                    $returnData['blackHead'] = '黑头： ' . config('clife.blackHeadLevel')[$val['level']] . '，数量：' . $val['number'];
                }
                //毛孔
                if($key === 'pore'){
                    $returnData['pore'] = '毛孔： ' . config('clife.poreLevel')[$val['level']] . '，数量：' . $val['number'];
                }
                //脸型
                if($key === 'faceshape'){
                    $returnData['faceshape'] = '脸型： ' . config('clife.faceshape')[$val[0]];
                }
                //痘痘
                if($key === 'acnes'){
                    foreach ($val as $v){
                        $v = (array)$v;
                        $returnData['acnes'][] = '部位： ' . config('clife.facePart')[$v['facePart']]
                            . '，痘痘类型：' . config('clife.acneTypeId')[$v['acneTypeId']]
                            . '，严重等级：' . config('clife.acneLevel')[$v['level']]
                            . '，数量：' . $v['number'];
                    }
                }
                //眉形
                if($key === 'eyebrow'){
                    $returnData['eyebrow']['left'] = '左眉形： ' . config('clife.eyebrow')[$val['left']];
                    $returnData['eyebrow']['right'] = '右眉形： ' . config('clife.eyebrow')[$val['right']];
                }
                //眼袋
                if($key === 'pouch'){
                    $returnData['pouch'] = '眼袋： ' . config('clife.pouchLevel')[$val['level']];
                }
                //皱纹
                if($key === 'wrinkles'){
                    foreach ($val as $v){
                        $v = (array)$v;
                        $returnData['wrinkles'][] = '类型：' . config('clife.wrinkleTypeId')[$v['wrinkleTypeId']]
                            . '，严重等级：' . config('clife.acneLevel')[$v['level']];
                    }
                }
            }
            print_r($returnData);
        }
    }
}