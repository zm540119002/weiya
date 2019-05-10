<?php
// 异常错误报错级别,
error_reporting(E_ERROR | E_PARSE );
require __DIR__ . '/../../common/function/common.php';

/**循环判断键值是否存在
 * @return bool
 */
function multi_array_key_exists( $needle, $haystack ) {
    foreach ( $haystack as $key => $value ) :
        if ( $needle == $key )
            return true;
        if ( is_array( $value ) ) :
            if ( multi_array_key_exists( $needle, $value ) == true )
                return true;
            else
                continue;
        endif;
    endforeach;
    return false;
}
//获取店铺类型
function getStoreType($num){
    return $num?config('custom.store_type')[$num]:'';
}
//获取店铺经营类型
function getRunType($num){
    return $num?config('custom.run_type')[$num]:'';
}
//获取店铺合作类型
function getOperationalModel($num){
    return $num?config('custom.operational_model')[$num]:'';
}
//获取岗位中文
function getPostCn($num){
    $post = config('permission.post');
    $res = '';
    foreach ($post as $value){
        if($num == $value['id']){
            $res = $value['name'];
        }
    }
    return $res;
}
//获取职务中文
function getDutyCn($num){
    $duty = config('permission.duty');
    $res = '';
    foreach ($duty as $value){
        if($num == $value['id']){
            $res = $value['name'];
        }
    }
    return $res;
}
//获取单位
function getUnit($num){
    return $num?config('custom.unit')[$num]:'';
}
/*开启底部购物车配置项
 */
function unlockingFooterCartConfig($arr){
    $footerCartConfig = config('footer_menu.menu');
    $tempArr = array();
    $tempArr['count'] = count($arr);
    foreach ($arr as $val) {
        $tempArr['menu'][] = $footerCartConfig[$val];
    }
    return $tempArr;
}
/**获取支付代码
 * @param $num
 * @return string`'支付方式：0：保留 1 微信 2：支付宝 3：网银 4:钱包',
 */
function getPaymentCode($num){
    return $num?config('custom.pay_code')[$num]:'';
}
/**获取品牌分类
 * @param $num
 * @return string
 */
function getBrandType($num){
    return $num?config('custom.brand_type')[$num]:'';
}

/**
 * TODO PHP 从网络上获取图片 并保存
 * @param $url 图片的网络路径，支持本地。但是图片限制盗链的可能不行
 * @param $savePath 此为重命名并进行保存的图片地址
 * @return array|string 如果$filename不为空，方可进行下载并返回新图片地址
 */
function saveImageFromHttp($url,$savePath) {
    $header = array(
        'User-Agent: Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:45.0) Gecko/20100101 Firefox/45.0',
        'Accept-Language: zh-CN,zh;q=0.8,en-US;q=0.5,en;q=0.3',
        'Accept-Encoding: gzip, deflate',);
    //上传公共路径
    $uploadPath = config('upload_dir.upload_path'). '/';
    if(!is_dir($uploadPath)){
        if(!mk_dir($uploadPath)){
            return  errorMsg('创建Uploads目录失败');
        }
    }
    $uploadPath = realpath($uploadPath);
    if($uploadPath === false){
        return  errorMsg('获取Uploads实际路径失败');
    }
    //存储路径
    $storePath = $uploadPath . $savePath;
    if(!mk_dir($storePath)){
        return errorMsg('创建临时目录失败');
    }
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($curl, CURLOPT_ENCODING, 'gzip');
    curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
    $newFileName = generateSN(15);
    $data = curl_exec($curl);
    $code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    curl_close($curl);
    if ($code == 200) {//把URL格式的图片转成base64_encode格式的！
        $imgBase64Code = "data:image/jpeg;base64," . base64_encode($data);
    }
    $img_content=$imgBase64Code;//图片内容
    if (preg_match('/^(data:\s*image\/(\w+);base64,)/', $img_content, $result))
    {
        $type = $result[2];//得到图片类型png?jpg?gif?
        $newFile = $storePath.$newFileName.".{$type}";
        if (file_put_contents($newFile, base64_decode(str_replace($result[1], '', $img_content)))){
            return $savePath.$newFileName.".{$type}";
        }
    }
}
