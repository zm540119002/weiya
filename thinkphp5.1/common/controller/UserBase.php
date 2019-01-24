<?php
namespace common\controller;
use think\facade\Session;
/**用户信息验证控制器基类
 */
class UserBase extends Base{
    protected $user = null;
    protected $loginUrl = 'ucenter/UserCenter/login';//用户中心URL
    protected $indexUrl = 'index/Index/index';//首页URL
    
    public function __construct(){
        parent::__construct();
        //判断是否登录
        $this->user = checkLogin();
        if (!$this->user) {
            if (request()->isAjax()) {
                $this->success('异步登录失败',url($this->indexUrl),'no_login',0);
            }else{
                $this->error(config('custom.error_login'),url($this->loginUrl));
            }
        }
        /**
         *   [openid] => oaObx0eEaPcRhGysHH47cSi3hzws
        [nickname] => 杨观保
        [sex] => 1
        [language] => zh_CN
        [city] => 深圳
        [province] => 广东
        [country] => 中国
        [headimgurl] => http://thirdwx.qlogo.cn/mmopen/vi_32/HHQ88bOEIa9ccyGib4xRa0xcEM3YM7o33fJQGlUanKib9eZ0Q6NwRGlia7shbiboIlgxoJdm5apunboEjchxRuz6Hg/132
         */
        if(isWxBrowser() && !request()->isAjax()) {//判断是否为微信浏览器
            if(!$this -> user['weiya_openid']){
                $weiXinUserInfo = session('weiXinUserInfo');
                //上传公共路径
                $uploadPath = config('upload_dir.upload_path');
                //临时相对路径
                $tempRelativePath = config('upload_dir.temp_path');
                $newFile = $uploadPath.'/'.$tempRelativePath.generateSN(15)."png";

                $header = array(
                    'User-Agent: Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:45.0) Gecko/20100101 Firefox/45.0',
                    'Accept-Language: zh-CN,zh;q=0.8,en-US;q=0.5,en;q=0.3',
                    'Accept-Encoding: gzip, deflate',);
                $url=$weiXinUserInfo['headimgurl'];
                $curl = curl_init();
                curl_setopt($curl, CURLOPT_URL, $url);
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
                curl_setopt($curl, CURLOPT_ENCODING, 'gzip');
                curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
                $data = curl_exec($curl);$code = curl_getinfo($curl, CURLINFO_HTTP_CODE);curl_close($curl);
                if ($code == 200) {//把URL格式的图片转成base64_encode格式的！
                    $imgBase64Code = "data:image/jpeg;base64," . base64_encode($data);
                }
                $img_content=$imgBase64Code;//图片内容
                //echo $img_content;exit;
                if (preg_match('/^(data:\s*image\/(\w+);base64,)/', $img_content, $result))
                {
                    $type = $result[2];//得到图片类型png?jpg?gif?
                    $newFile = $uploadPath.'/'.$tempRelativePath.generateSN(15)."{$type}";
                    if (file_put_contents($newFile, base64_decode(str_replace($result[1], '', $img_content))))
                    {  echo '新文件保存成功：', $newFile; }}


                print_r($newFile);exit;
                $data = [
                    'id'=>$this->user['id'],
                    'name'=>$weiXinUserInfo['nickname'],
                    'avatar'=>$weiXinUserInfo['headimgurl'],
                    'weiya_openid'=>$weiXinUserInfo['openid'],
                ];
                $userModel = new \common\model\User();
                $result = $userModel->isUpdate(true)->save($data);
                if( false === $result){
                    return errorMsg('添加微信信息失败');
                }
            }
        }


    }
}