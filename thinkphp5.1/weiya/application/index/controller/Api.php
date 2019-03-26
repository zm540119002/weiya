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
            'image'=>'@static/common/img/6.jpg',
        ];
        $curl->post($url, $data2);
        if ($curl->error) {
            echo 'Error: ' . $curl->errorCode . ': ' . $curl->errorMessage . "\n";
        } else {
            $returnData = [];
            //解析
            foreach ($curl->response->data as $key=>$val){
                $val = (array)$val;
                //眼型
                if($key === 'eyeshape'){
                    foreach (config('clife.eyeshape') as $v){
                        if($v['type'] == $val){
                            $returnData['眼型'] = '眼型： ' . $v['explain'];
                        }
                    }
                }
                //黑头
                if($key === 'blackHead'){
                    $returnData['黑头'] = '严重等级： ' . config('clife.blackHeadLevel')[$val['level']] . '，数量：' . $val['number'];
                }
                //毛孔
                if($key === 'pore'){
                    $returnData['毛孔'] = '严重等级： ' . config('clife.poreLevel')[$val['level']] . '，数量：' . $val['number'];
                }
                //脸型
                if($key === 'faceshape'){
                    $returnData['脸型'] = '脸型： ' . config('clife.faceshape')[$val[0]];
                }
                //痘痘
                if($key === 'acnes'){
                    foreach ($val as $v){
                        $v = (array)$v;
                        $returnData['痘痘'][] = '部位： ' . config('clife.facePart')[$v['facePart']]
                            . '，痘痘类型：' . config('clife.acneTypeId')[$v['acneTypeId']]
                            . '，严重等级：' . config('clife.acneLevel')[$v['level']]
                            . '，数量：' . $v['number'];
                    }
                }
                //眉形
                if($key === 'eyebrow'){
                    $returnData['眉形']['左'] = '左眉形： ' . config('clife.eyebrow')[$val['left']];
                    $returnData['眉形']['右'] = '右眉形： ' . config('clife.eyebrow')[$val['right']];
                }
                //眼袋
                if($key === 'pouch'){
                    $returnData['眼袋'] = '眼袋： ' . config('clife.pouchLevel')[$val['level']];
                }
                //皱纹
                if($key === 'wrinkles'){
                    foreach ($val as $v){
                        $v = (array)$v;
                        $returnData['皱纹'][] = '类型：' . config('clife.wrinkleTypeId')[$v['wrinkleTypeId']]
                            . '，严重等级：' . config('clife.acneLevel')[$v['level']];
                    }
                }
                //色素斑
                if($key === 'pigmentations'){
                    foreach ($val as $v){
                        $v = (array)$v;
                        $returnData['色素斑'][] = '部位： ' . config('clife.facePart')[$v['facePart']]
                            . '，类型：' . config('clife.pigmentationTypeId')[$v['pigmentationTypeId']]
                            . '，严重等级：' . config('clife.acneLevel')[$v['level']];
                    }
                }
                //水分
                if($key === 'moisture'){
                    foreach ($val as $v){
                        $v = (array)$v;
                        $returnData['水分'][] = '部位： ' . config('clife.facePart')[$v['facePart']]
                            . '，类型：' . config('clife.className')[$v['className']]
                            . '，严重等级：' . config('clife.acneLevel')[$v['level']];
                    }
                }
                //敏感度
                if($key === 'sensitivity'){
                    foreach ($val['sensitivityCategory'] as $v){
                        $v = (array)$v;
                        $returnData['敏感度'][] = '部位： ' . config('clife.facePart')[$v['facePart']]
                            . '，严重等级：' . config('clife.acneLevel')[$v['level']]
                            . '，敏感类型：' . config('clife.sensitivityTypeId')[$val['typeId']];
                    }
                }
                //黑眼圈
                if($key === 'darkCircle'){
                    foreach ($val as $v){
                        $v = (array)$v;
                        $returnData['黑眼圈'][] = '部位： ' . config('clife.darkCirclePosition')[$v['position']]
                            . '，严重等级：' . config('clife.darkCircleLevel')[$v['level']]
                            . '，类型：' . config('clife.darkCircleType')[$val['type']];
                    }
                }
                //肌肤年龄
                if($key === 'skinAge'){
                    $returnData['肌肤年龄'] = '肌肤年龄： ' . $val[0];
                }
                //油分
                if($key === 'oil'){
                    foreach ($val as $v){
                        $v = (array)$v;
                        $returnData['油分'][] = '部位： ' . config('clife.facePart')[$v['facePart']]
                            . '，严重等级：' . config('clife.darkCircleLevel')[$v['level']];
                    }
                }
                //脂肪粒
                if($key === 'fatGranule'){
                    foreach ($val as $v) {
                        $v = (array)$v;
                        $returnData['脂肪粒'][] = '类型：' . config('clife.fatGranuleTypeId')[$v['fatGranuleTypeId']]
                            . '，严重等级：' . config('clife.darkCircleLevel')[$v['level']]
                            . '，数量：' . $v['number'];
                    }
                }
            }
            $returnData['time'] = time() .'000';
            print_r(($returnData));
        }
    }
}