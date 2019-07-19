<?php
namespace common\controller;
use \common\component\image\Image;
/**基于公共基础控制器
 */
class BaseApi extends \think\Controller{
    protected $http_type = null;
    protected $host = null;
    protected $request_uri = null;
    public function __construct(){
        parent::__construct();
        header('Access-Control-Allow-Origin: *');
        header("Access-Control-Allow-Headers: token,Origin, X-Requested-With, Content-Type, Accept");
        header('Access-Control-Allow-Methods: POST,GET');
        if(request()->isOptions()){
            exit();
        }
        //登录验证后跳转回原验证发起页
        $this->http_type = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') || (isset($_SERVER['HTTP_X_FORWARDED_PROTO'])
                && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https')) ? 'https://' : 'http://';
        $this->host = $this->http_type . (isset($_SERVER['HTTP_X_FORWARDED_HOST']) ? $_SERVER['HTTP_X_FORWARDED_HOST'] :
            (isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : ''));
        $this->request_uri = $_SERVER['REQUEST_URI'] ? $this->host . $_SERVER['REQUEST_URI'] :$this->host . $_SERVER['HTTP_REFERER'];
        //去到页面跟返回跳转一样，前端不用传参
        session('backUrl',$this->request_uri);
        //去到页面跟返回跳转不一样，前端传参returnUrl
        session('returnUrl',input('get.returnUrl','')?:input('post.returnUrl',''));
    }





}