<?php
function getToken($data = [],$expTime = 7200)
{
    $key = "huang";  //这里是自定义的一个随机字串，应该写在config文件中的，解密时也会用，相当    于加密中常用的 盐  salt
    $token = [
        "iss" => "",  //签发者 可以为空
        "aud" => "", //面象的用户，可以为空
        "iat" => time(), //签发时间
        "nbf" => time(), //在什么时候jwt开始生效  （这里表示生成100秒后才生效）
        "exp" => time() + $expTime, //token 过期时间
    ];
    $token = array_merge($token,$data);
    $jwt =  \common\component\jwt\JWT::encode($token, $key, "HS256"); //根据参数生成了 token
    return $jwt;
    return json([
        "token" => $jwt
    ]);
}


function check()
{
    $jwt = input("token");  //上一步中返回给用户的token
    $key = "huang";  //上一个方法中的 $key 本应该配置在 config文件中的
    //$info = JWT::decode($jwt, $key, ["HS256"]); //解密jwt
    try {
        $jwtAuth = json_encode(\common\component\jwt\JWT::decode($jwt, $key, array('HS256')));
        $authInfo = json_decode($jwtAuth, true);
//            $jwtAuth = JWT::decode($jwt, $key, ["HS256"]);
//            $authInfo = json_decode($jwtAuth, true);
        p($authInfo);
        $msg = [];
        if (!empty($authInfo['uid'])) {
            $msg = [
                'status' => 1001,
                'msg' => 'Token验证通过'
            ];
        } else {
            $msg = [
                'status' => 1002,
                'msg' => 'Token验证不通过,用户不存在'
            ];
        }
        p($msg);
    } catch (\common\component\jwt\BeforeValidException $e) {
        return json_encode([
            'status' => 1002,
            'msg' => 'Token无效'
        ]);
    } catch (\common\component\jwt\ExpiredException $e) {
        return json_encode([
            'status' => 1003,
            'msg' => 'Token过期'
        ]);
    } catch (Exception $e) {
        return $e;
    }
}


function buildSuccess($data, $msg = '操作成功',$code) {
    $code=$code?$code:config('return_code.success');
    $return = [
        'code' => $code,
        'msg'  => $msg,
        'data' => $data
    ];
    return json_encode($return);
}

function buildFailed( $msg, $code,$data = []) {
    $code=$code?$code:config('return_code.invalid');
    $return = [
        'code' => $code,
        'msg'  => $msg,
        'data' => $data
    ];
    return json_encode($return);
}