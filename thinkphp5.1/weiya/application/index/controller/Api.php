<?php
namespace app\index\controller;

class Api extends \common\controller\Base{
    /**首页
     */
    public function index(){
        $urlToken = 'https://open.api.clife.cn/apigateway/commons/clife-open-api-app/cloud/token';
        $data = [
            'appId'=>31316,
            'appSecret'=>'0ab242fc269f4119bc9f4ad9e6884332',
            'timestamp'=> time().'000',
        ];
        $header =[
            "Content-type:application/x-www-form-urlencoded"
        ];
        $res = json_decode($this->httpGet($urlToken,$header,$data),true);
        $accessToken = $res['data']['accessToken'];

//        $uploadPath = config('upload_dir.upload_path').'/'. config('upload_dir.temp_path');
//        $image   = $uploadPath."1.jpg"; //图片地址
//        $p_size = filesize($image);
//        $img_binary = fread(fopen($image, "r"), $p_size);
        //file_put_contents($uploadPath.time().'.jpg',$img_binary);exit;
        $url = 'https://open.api.clife.cn/apigateway/commons/clife-open-api-app/cloud/skinImageAnalysis/photograph/analysis';

        $data2 = [
            'appId'=>31316,
            'accessToken'=>$accessToken,
            'timestamp'=> time().'000',
            'image'=>'https://timgsa.baidu.com/timg?image&quality=80&size=b9999_10000&sec=1553057709087&di=d35a586624659c17692adf62938baeb3&imgtype=0&src=http%3A%2F%2Fimages10.m.china.com.cn%2Fnewschina%2F2018_11_19%2F04%2Fca15ca9c3b5472460fb783c0bf5718766ef09bd56178accb_600_9999.jpg',
        ];
        $header2 =[
            "Content-type:multipart/form-data"
        ];
        $res = json_decode($this->http_request($url,$header2,$data2),true);
        print_r($res);
    }

    public function http_request($url, $header = [],$data = null)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
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

    public function httpGet($url,$header,$data = null) {
        $url = $url.'?'.http_build_query($data);
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_TIMEOUT, 500);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
        // 为保证第三方服务器与微信服务器之间数据传输的安全性，所有微信接口采用https方式调用，必须使用下面2行代码打开ssl安全校验。
        // 如果在部署过程中代码在此处验证失败，请到 http://curl.haxx.se/ca/cacert.pem 下载新的证书判别文件。
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_URL, $url);

        $res = curl_exec($curl);
        curl_close($curl);

        return $res;
    }

}