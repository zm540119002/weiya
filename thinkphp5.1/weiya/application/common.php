<?php
// 异常错误报错级别,
error_reporting(E_ERROR | E_PARSE );
require __DIR__ . '/../../common/function/common.php';

/**检查是否登录
 */
function checkLogin(){
    $user = session('user','',config('custom.session_prefix'));
    $user_sign = session('user_sign','',config('custom.session_prefix'));
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