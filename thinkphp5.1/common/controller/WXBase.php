<?php
namespace common\controller;

use component\wx_pay_api\Jssdk;

class WXBase extends UserBase{
    private $_jssdk = null;
    public function __construct(){
        parent::__construct();
        $this->_jssdk = new Jssdk(config('custom.wx_config')['APPID'], config('custom.wx_config')['APPSECRET']);
        $this -> signPackage = $this -> weiXinShareInit();
    }

    //微信分享初始化
    public function weiXinShareInit(){
        $signPackage =  $this->_jssdk->GetSignPackage();
        return $signPackage;
    }

    //微信分享信息
    public function weiXinShareInfo($title,$shareLink,$shareImgRelativeUrl,$desc,$backUrl){
        $shLink = substr($shareLink,0,strrpos($shareLink,'/share'));
        if(empty($shLink)){
            $shLink = $shareLink;
        }
        $shareLink = $shLink.'.html';
        $shareImgUrl = (is_ssl()?'https://':'http://').$this->host.config('upload_dir.upload_path').$shareImgRelativeUrl;
        if(empty($backUrl)){
            $backUrl = $shareLink;
        }
        $shareInfo = array(
            'title' => $title,
            'shareLink' => $shareLink,
            'shareImgUrl' => $shareImgUrl,
            'desc' => $desc,
            'backUrl' => $backUrl,
        );
        return $shareInfo;
    }

    //微信分享信息
//    public function weiXinShare($title,$shareLink,$shareImgRelativeUrl,$desc,$backUrl){
    public function weiXinShare($shareInfo){
        $shareImgUrl = (is_ssl()?'https://':'http://').$this->host.config('upload_dir.upload_path').$shareInfo['shareImgUrl'];
        $shareInfo['shareImgUrl'] = $shareImgUrl;
        return $shareInfo;
    }

    //获取微信用户基本信息（已关注用户）
    public function getWeiXinUserInfo(){
        if(isWxBrowser()){//判断是否为微信浏览器
            return  $this->_jssdk ->get_user_info($this->_jssdk->getOpenid());
        }
    }

    //OAuth2 授权获取用户基本信息（OAuth2 授权的 Access Token 获取 未关注用户，Access Token为临时获取）
    public function getOAuthWeiXinUserInfo(){
        if(isWxBrowser()){//判断是否为微信浏览器
            return  $this->_jssdk ->getOauthUserInfo();
        }
    }

    //获取微信用户列表信息
    public function getWeiXinUserList(){
        if(isWxBrowser()) {//判断是否为微信浏览器
            return  $this->_jssdk ->getUserList();
        }
    }
    //生成带参二维码
    public function getQRcode($scene_type, $scene_id){
        if(isWxBrowser()) {//判断是否为微信浏览器
            return  $this->_jssdk ->create_qrcode($scene_type, $scene_id);
        }
    }

    //自定义菜单
    public function  create_menu_raw($menu){
        /**
         *        $menu = '{
        "button":[
        {
        "type":"view",
        "name":"美容机构",
        "url":"http://m.meishangyun.com/sys_mrjg/do.php"

        },
        {
        "type":"view",
        "name":"美容机构",
        "url":"http://m.meishangyun.com/sys_employee/do.php"
        }]
        }';
         */
        return  $this->_jssdk -> create_menu_raw($menu);
    }

    //获取Openid
    public function getOpenid(){
        if(isWxBrowser()) {//判断是否为微信浏览器
            return  $this->_jssdk ->getOpenid();
        }
    }

    //发送模版消息
    public function sendTemplateMessage($template){
//        if(isWxBrowser()) {//判断是否为微信浏览器
//            return  $this->_jssdk ->send_template_message($template);
//        }
        return  $this->_jssdk ->send_template_message($template);
    }
    //发送返现模板信息
    public function sendTemplateMessageCashBack($templateBase,$data){
        //返现通知
        $template = array(
            'touser'=>$templateBase['touser'],
            'template_id'=>$templateBase['template_id'],//参加团购通知模板Id
            "url"=>$templateBase['url'],
            'data'=>array(
                'first'=>array(
                    'value'=>$data['first'],'color'=>'#173177',
                ),
                'keyword1'=>array(
                    'value'=>$data['keyword1'],'color'=>'#173177',
                ),
                'keyword2'=>array(
                    'value'=>$data['keyword2'].'元','color'=>'#173177',
                ),
                'keyword3'=>array(
                    'value'=>$data['keyword3'].'元','color'=>'#173177',
                ),
                'remark'=>array(
                    'value'=>$data['remark'],'color'=>'#FF0000',
                ),
            ),
        );
        $rst = $this->sendTemplateMessage($template);
        if($rst['errmsg'] != 'ok'){
            \Think\Log::write('发送返现通知失败', 'NOTIC');
        }
        
    }

    //发送团购成功模板信息
    public function sendTemplateMessageGroupBuySuccess($templateBase,$data){
        //返现通知
        $template = array(
            'touser'=>$templateBase['touser'],
            'template_id'=>$templateBase['template_id'],//参加团购通知模板Id
            "url"=>$templateBase['url'],
            'data'=>array(
                'first'=>array(
                    'value'=>'亲，您已成功参加团购！','color'=>'#173177',
                ),
                'Pingou_ProductName'=>array(
                    'value'=>$data['product_name'],'color'=>'#173177',
                ),
                'Weixin_ID'=>array(
                    'value'=>$data['header'],'color'=>'#173177',
                ),
                'Remark'=>array(
                    'value'=>$data['remark'],'color'=>'#FF0000',
                ),
            ),
        );
        $rst = $this->sendTemplateMessage($template);
        if($rst['errmsg'] != 'ok'){
            \Think\Log::write('发送返现通知失败', 'NOTIC');
        }
    }
}


