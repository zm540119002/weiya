<?php
// 异常错误报错级别,
error_reporting(E_ERROR | E_PARSE );
require __DIR__ . '/../../common/function/common.php';

/**检查是否登录
 */
function checkLogin(){
    $user = session('user');
    $user_sign = session('user_sign');
    if (!$user || !$user_sign) {
        return false;
    }
    if ($user_sign != data_auth_sign($user)) {
        return false;
    }
    return $user;
}
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
    return $num?config('custom.payment_code')[$num]:'';
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
 * @param $imgFromUrl 图片的网络路径，支持本地。但是图片限制盗链的可能不行
 *                    本地举例：'Public/images/from.png'
 *                    网络图片示例：'https://timgsa.baidu.com/timg?image&quality=80&size=b9999_10000&sec=1538199326261&di=1e0eec15686209c2d456d554690327c1&imgtype=0&src=http%3A%2F%2Fimg5.duitang.com%2Fuploads%2Fitem%2F201410%2F13%2F20141013110308_QtVC8.thumb.700_0.jpeg'
 * @param $newFileName 此为重命名并进行保存的图片地址
 * @return bool|string 如果$filename不为空，方可进行下载并返回新图片地址
 *
 * 使用 举例：
 *      $img = saveImageFromHttp('Public/images/from.png',"Public/images/save".time().".png");
 *      echo $img;
 */
function saveImageFromHttp($imgFromUrl,$newFileName) {
    //如果$imgFromUrl地址为空，直接退出即可
    if ($imgFromUrl == "") {return false;}
    //如果没有指定新的文件名
    if ($newFileName == "") {
        //得到 $imgFromUrl 的图片格式
        $ext = strrchr($imgFromUrl, ".");
        //如果图片格式不为.gif 或者.jpg .png，直接退出即可
        if ($ext != ".gif" && $ext != ".jpg" && $ext != 'png'){
            return false;
        }
        $newFileName = date("dMYHis") . $ext;
        //用天月面时分秒来命名新的文件名
    }
    ob_start();//打开输出
    readfile($imgFromUrl);//输出图片文件
    $img = ob_get_contents();//得到浏览器输出
    ob_end_clean();//清除输出并关闭
    //$size = strlen($img);//得到图片大小
    $fp2 = @fopen($newFileName, "a");
    fwrite($fp2, $img);//向当前目录写入图片文件，并重新命名
    fclose($fp2);
    return $newFileName;//返回新的文件名
}
