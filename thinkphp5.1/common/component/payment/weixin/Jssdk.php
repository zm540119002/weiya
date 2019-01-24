<?php

namespace common\component\payment\weixin;
class Jssdk {
  private $appId;
  private $appSecret;
  private $path;
  private $access_token;

  public function __construct($appId, $appSecret) {
    $this->appId = $appId;
    $this->appSecret = $appSecret;
    print_r($appId);
    echo '----';
    print_r($appSecret);
    echo '----';
    $this->path = __DIR__ . 'Jssdk.php/';
    $data = json_decode($this->get_php_file("access_token.php"));
    print_r($data);
    echo '----';
    if ($data->expire_time < time()) {
      // 如果是企业号用以下URL获取access_token
      // $url = "https://qyapi.weixin.qq.com/cgi-bin/gettoken?corpid=$this->appId&corpsecret=$this->appSecret";
      $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appId=$this->appId&secret=$this->appSecret";
      $res = json_decode($this->httpGet($url));
      $access_token = $res->access_token;
      if ($access_token) {
        $data->expire_time = time() + 7000;
        $data->access_token = $access_token;
        $this->set_php_file("access_token.php", json_encode($data));
      }
    } else {
      print_r($data);
      echo '----';
      $access_token = $data->access_token;
    }
    print_r($access_token);
    $this -> access_token = $access_token;

//    $this->getAccessToken();
  }

  /**
   * @return array
   * 获取接口调用的信息包
   *
   */
  public function getSignPackage() {
    $jsapiTicket = $this->getJsApiTicket();

    // 注意 URL 一定要动态获取，不能 hardcode.
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
    $url = "$protocol$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

    $timestamp = time();
    $nonceStr = $this->createNonceStr();

    // 这里参数的顺序要按照 key 值 ASCII 码升序排序
    $string = "jsapi_ticket=$jsapiTicket&noncestr=$nonceStr&timestamp=$timestamp&url=$url";

    $signature = sha1($string);

    $signPackage = array(
        "appId"     => $this->appId,
        "nonceStr"  => $nonceStr,
        "timestamp" => $timestamp,
        "url"       => $url,
        "signature" => $signature,
        "rawString" => $string
    );
    return $signPackage;
  }

  /**
   * @param int $length
   * @return string
   * 生成签名的随机串
   */
  private function createNonceStr($length = 16) {
    $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
    $str = "";
    for ($i = 0; $i < $length; $i++) {
      $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
    }
    return $str;
  }

  /**
   * @return mixed
   *
   */
  private function getJsApiTicket() {
    // jsapi_ticket 应该全局存储与更新，以下代码以写入到文件中做示例
    $data = json_decode($this->get_php_file("jsapi_ticket.php"));
    if ($data->expire_time < time()) {
      $accessToken = $this->getAccessToken();
      // 如果是企业号用以下 URL 获取 ticket
      // $url = "https://qyapi.weixin.qq.com/cgi-bin/get_jsapi_ticket?access_token=$accessToken";
      $url = "https://api.weixin.qq.com/cgi-bin/ticket/getticket?type=jsapi&access_token=$accessToken";
      $res = json_decode($this->httpGet($url));
      $ticket = $res->ticket;
      if ($ticket) {
        $data->expire_time = time() + 7000;
        $data->jsapi_ticket = $ticket;
        $this->set_php_file("jsapi_ticket.php", json_encode($data));
      }
    } else {
      $ticket = $data->jsapi_ticket;
    }

    return $ticket;
  }

  /**
   * @return mixed
   * 获取全局Access Token
   */
  private function getAccessToken() {
    // access_token 应该全局存储与更新，以下代码以写入到文件中做示例
    $data = json_decode($this->get_php_file("access_token.php"));
    if ($data->expire_time < time()) {
      // 如果是企业号用以下URL获取access_token
      // $url = "https://qyapi.weixin.qq.com/cgi-bin/gettoken?corpid=$this->appId&corpsecret=$this->appSecret";
      $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appId=$this->appId&secret=$this->appSecret";
      $res = json_decode($this->httpGet($url));
      $access_token = $res->access_token;
      if ($access_token) {
        $data->expire_time = time() + 7000;
        $data->access_token = $access_token;
        $this->set_php_file("access_token.php", json_encode($data));
      }
    } else {
      $access_token = $data->access_token;
    }
    return $access_token;
  }

  private function httpGet($url) {
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_TIMEOUT, 500);
    // 为保证第三方服务器与微信服务器之间数据传输的安全性，所有微信接口采用https方式调用，必须使用下面2行代码打开ssl安全校验。
    // 如果在部署过程中代码在此处验证失败，请到 http://curl.haxx.se/ca/cacert.pem 下载新的证书判别文件。
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, true);
    curl_setopt($curl, CURLOPT_URL, $url);

    $res = curl_exec($curl);
    curl_close($curl);

    return $res;
  }

  /**
   * @param $filename
   * @return string
   * 获取文件信息
   */
  private function get_php_file($filename) {
    return trim(substr(file_get_contents($filename), 15));
  }

  /**
   * @param $filename
   * @param $content
   * 保存文件信息
   */
  private function set_php_file($filename, $content) {
    $fp = fopen($filename, "w");
    fwrite($fp, "<?php exit();?>" . $content);
    fclose($fp);
  }


  //获取用户列表
  public function getUserList($next_openid = NULL)
  {
    $url = "https://api.weixin.qq.com/cgi-bin/user/get?access_token=".$this->access_token."&next_openid=".$next_openid;
    $res = $this->http_request($url);
    $list = json_decode($res, true);
    if ($list["count"] == 10000){
      $new = $this->getUserList($next_openid = $list["next_openid"]);
      $list["data"]["openid"] = array_merge_recursive($list["data"]["openid"], $new["data"]["openid"]); //合并OpenID列表
    }
    return $list;
  }

  //获取用户基本信息
  public function getUserInfo()
  {
    $openid = $this->getOpenid();
//    $access_token = $this->getAccessToken();
    $url = "https://api.weixin.qq.com/cgi-bin/user/info?access_token=".$this->access_token."&openid=".$openid."&lang=zh_CN";
    $res = $this->http_request($url);
    return json_decode($res, true);
  }

  //要授权取用户基本信息；
  public function getOauthUserInfo()
  {
    $data = $this -> GetAccessTokenAndOpenid();
    $access_token = $data['access_token'];
    $openid =  $data['openid'];
    $url = "http://api.weixin.qq.com/sns/userinfo?access_token=".$access_token."&openid=".$openid."&lang=zh_CN";
    $res = $this->http_request($url);
    return json_decode($res, true);
  }


  function getJson($url){
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $output = curl_exec($ch);
    curl_close($ch);
    return json_decode($output, true);
  }


  public $data = null;

  /**
   *
   * 通过跳转获取用户的openid，跳转流程如下：
   * 1、设置自己需要调回的url及其其他参数，跳转到微信服务器https://open.weixin.qq.com/connect/oauth2/authorize
   * 2、微信服务处理完成之后会跳转回用户redirect_uri地址，此时会带上一些参数，如：code
   *
   * @return 用户的openid
   */
  public function getOpenid()
  {
    //通过code获得openid
    if (!isset($_GET['code'])){
      //触发微信返回code码
//			$baseUrl = urlencode('http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'].$_SERVER['QUERY_STRING']);
      $baseUrl = urlencode('https://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
      $url = $this->__CreateOauthUrlForCode($baseUrl);
      header("Location: $url");
      exit();
    } else {
      //获取code码，以获取openid
      $code = $_GET['code'];
      session('code1',$code);
      $data = $this->GetOpenidFromMp($code);
      return $data['openid'];
    }
  }


  //通过code换取网页授权access_token与openid
  public function GetAccessTokenAndOpenid(){

    if ( !isset($_GET['code'])){
      //触发微信返回code码
//			$baseUrl = urlencode('http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'].$_SERVER['QUERY_STRING']);
      $baseUrl = urlencode('https://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
      $url = $this->__CreateUrlForCode($baseUrl);
      Header("Location: $url");
      exit();
    } else {
      //获取code码，以获取openid
      $code = $_GET['code'];
      $data = $this->getOpenidFromMp($code);
      return $data;
    }
  }


  /**
   *
   * 通过code从工作平台获取openid机器access_token
   * @param string $code 微信跳转回来带上的code
   *
   * @return openid
   */
  public function GetOpenidFromMp($code)
  {

    $url = $this->__CreateOauthUrlForOpenid($code);
    //初始化curl
    $ch = curl_init();
    //设置超时
    curl_setopt($ch, CURLOPT_TIMEOUT, $this->curl_timeout);
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER,FALSE);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST,FALSE);
    curl_setopt($ch, CURLOPT_HEADER, FALSE);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    if( config('wx_config.curl_proxy_host') != "0.0.0.0"
        && config('wx_config.curl_proxy_port') != 0){
      curl_setopt($ch,CURLOPT_PROXY, config('wx_config.curl_proxy_host'));
      curl_setopt($ch,CURLOPT_PROXYPORT, config('wx_config.curl_proxy_port'));
    }
    //运行curl，结果以jason形式返回
    $res = curl_exec($ch);
    curl_close($ch);
    //取出openid
    $data = json_decode($res,true);
    $this->data = $data;
//    $openid = $data['openid'];
    return $data;
  }

  /**
   *
   * 拼接签名字符串
   * @param array $urlObj
   *
   * @return 返回已经拼接好的字符串
   */
  private function ToUrlParams($urlObj)
  {
    $buff = "";
    foreach ($urlObj as $k => $v)
    {
      if($k != "sign"){
        $buff .= $k . "=" . $v . "&";
      }
    }

    $buff = trim($buff, "&");
    return $buff;
  }


  /**
   *
   * 构造默认获取code的url连接
   * @param string $redirectUrl 微信服务器回跳的url，需要url编码
   *
   * @return 返回构造好的url
   */
  private function __CreateOauthUrlForCode($redirectUrl)
  {

    $urlObj["appId"] = $this->appId;
    $urlObj["redirect_uri"] = "$redirectUrl";
    $urlObj["response_type"] = "code";
    $urlObj["scope"] = "snsapi_base";
    $urlObj["state"] = "STATE"."#wechat_redirect";
    $bizString = $this->ToUrlParams($urlObj);
    return "https://open.weixin.qq.com/connect/oauth2/authorize?".$bizString;
  }
  /**
   *
   * 构造需授权获取code的url连接
   * @param string $redirectUrl 微信服务器回跳的url，需要url编码
   *
   * @return 返回构造好的url
   */
  private function __CreateUrlForCode($redirectUrl)
  {

    $urlObj["appId"] = $this->appId;
    $urlObj["redirect_uri"] = "$redirectUrl";
    $urlObj["response_type"] = "code";
    $urlObj["scope"] = "snsapi_userinfo";
    $urlObj["state"] = "STATE"."#wechat_redirect";
    $bizString = $this->ToUrlParams($urlObj);
    return "https://open.weixin.qq.com/connect/oauth2/authorize?".$bizString;
  }

  /**
   *
   * 构造获取open和access_toke的url地址
   * @param string $code，微信跳转带回的code
   *
   * @return 请求的url
   */
  private function __CreateOauthUrlForOpenid($code)
  {
    $urlObj["appId"] = $this->appId;
    $urlObj["secret"] = $this->appSecret;
    $urlObj["code"] = $code;
    $urlObj["grant_type"] = "authorization_code";
    $bizString = $this->ToUrlParams($urlObj);
    return "https://api.weixin.qq.com/sns/oauth2/access_token?".$bizString;
  }

  /**
   * @return mixed
   * 创建二维码ticket
   */
  public function createQRcodeTicket(){
    $access_token  = $this->getAccessToken();
   //临时
//    $qrcode = '{"expire_seconds": 1800, "action_name": "QR_SCENE", "action_info": {"scene": {"scene_id": 10000}}}';
    //永久
    $qrcode = '{"action_name": "QR_LIMIT_SCENE", "action_info": {"scene": {"scene_id": 1000}}}';
    $url = "https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token=$access_token";
    $result = $this->http_request($url,$qrcode);
    return json_decode($result, true);
  }


  /*
  测试接口，获取微信服务器IP地址
  */
  public function get_callback_ip()
  {
    $url = "https://api.weixin.qq.com/cgi-bin/getcallbackip?access_token=".$this->access_token;
    $res = $this->http_request($url);
    return json_decode($res, true);
  }

  /*
  *  PART1 用户管理
  */

  //OAuth2
  /*
  require_once('weixin.class.php');
  $weixin = new class_weixin();
  //如果url中已带，则不再获取，要注意正确性
  if (isset($_GET["openid"]) && !empty($_GET["openid"])){
      $openid = $_GET["openid"];
  }else{
      if (!isset($_GET["code"])){
          $redirect_url = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
          $jumpurl = $weixin->oauth2_authorize($redirect_url, "snsapi_base", "123");
          Header("Location: $jumpurl");
      }else{
          $oauth2_access_token = $weixin->oauth2_access_token($_GET["code"]);
          $openid = $oauth2_access_token['openid'];
      }
  }

  */
  //生成OAuth2的URL
  public function oauth2_authorize($redirect_url, $scope, $state = NULL)
  {
    $url = "https://open.weixin.qq.com/connect/oauth2/authorize?appId=".$this->appId."&redirect_uri=".urlencode($redirect_url)."&response_type=code&scope=".$scope."&state=".$state."#wechat_redirect";
    return $url;
  }

  //生成OAuth2的Access Token
  public function oauth2_access_token($code)
  {

    $url = "https://api.weixin.qq.com/sns/oauth2/access_token?appId=".$this->appId."&secret=".$this->appSecret."&code=".$code."&grant_type=authorization_code";
    $res = $this->http_request($url);
    return json_decode($res, true);
  }

  //获取用户基本信息（OAuth2 授权的 Access Token 获取 未关注用户，Access Token为临时获取）
  public function oauth2_get_user_info($access_token, $openid)
  {

    $url = "https://api.weixin.qq.com/sns/userinfo?access_token=".$access_token."&openid=".$openid."&lang=zh_CN";
    $res = $this->http_request($url);
    return json_decode($res, true);
  }

  //获得模板ID
  public function getTemplateId(){
    $url = "https://api.weixin.qq.com/cgi-bin/template/api_add_template?access_token=".$this->access_token;
    $res = $this->http_request($url);
    return json_decode($res, true);
  }

  /**
   * oauth 授权跳转接口
   * @param string $callback 回调URI
   * @return string
   */
  public function getOauthRedirect($callback,$state='',$scope='snsapi_userinfo'){
    $url= 'https://open.weixin.qq.com/connect/oauth2/authorize?appid='.$this->appId.'&redirect_uri='.urlencode($callback).'&response_type=code&scope='.$scope.'&state='.$state.'#wechat_redirect';
    Header("Location: $url");
    exit();
  }


  //获取用户基本信息（全局Access Token 获取 已关注用户，注意和OAuth时的区别）
  /*
  CREATE TABLE IF NOT EXISTS `wx_user` (
    `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '序号',
    `openid` varchar(30) NOT NULL COMMENT '微信openid',
    `nickname` varchar(20) NOT NULL COMMENT '昵称',
    `sex` varchar(2) NOT NULL COMMENT '性别',
    `country` varchar(10) NOT NULL COMMENT '国家',
    `province` varchar(16) NOT NULL COMMENT '省份',
    `city` varchar(16) NOT NULL COMMENT '城市',
    `latitude` float(10,6) NOT NULL COMMENT '纬度',
    `longitude` float(10,6) NOT NULL COMMENT '经度',
    `headimgurl` varchar(200) NOT NULL COMMENT '头像',
    `latest` varchar(100) NOT NULL COMMENT '最后互动',
    PRIMARY KEY (`id`),
    UNIQUE KEY `openid` (`openid`)
  ) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

  INSERT INTO `wx_user` (`id`, `openid`, `nickname`, `sex`, `country`, `province`, `city`, `latitude`, `longitude`, `headimgurl`, `latest`) VALUES (NULL, 'ooPwuszxAG9I2VmesRbC8gKCBsFI', 'Aro只对她认真。', '男', '', '', '', 31.289900, 121.553001, '', '');

  $userinfo = $weixin->get_user_info($openid);
  $mysql_state2 = "INSERT INTO `wx_user` (`id`, `openid`, `nickname`, `sex`, `country`, `province`, `city`, `latitude`, `longitude`, `headimgurl`, `latest`) VALUES (NULL, '$openid', '".$userinfo['nickname']."', '".$userinfo['sex']."', '".$userinfo['country']."', '".$userinfo['province']."', '".$userinfo['city']."', '', '', '', '');";
  $db->execute($mysql_state2);
  */
  public function get_user_info($openid)
  {
    $url = "https://api.weixin.qq.com/cgi-bin/user/info?access_token=".$this->access_token."&openid=".$openid."&lang=zh_CN";
    $res = $this->http_request($url);
    return json_decode($res, true);
  }

  //批量获取用户基本信息
  // $openidlist = array("ojK6Wjs_iMOCF5rF8PqW6_EBGV7g",
  // "ojK6WjixnuaZEVSjxnhHW-dihG6c",
  // "ojK6WjgfrW6SUYTqggjCwIHB0R_Y",
  // "ojK6WjhO_8h_s0PZS1poq2HU-G6g");

  // $result = $weixin->batchget_user_info($openidlist);
  public function batchget_user_info($openidlist, $lang = 'zh-CN')
  {
    $openids = array();
    foreach ($openidlist as &$item) {
      $openids[] = array('openid' => $item, 'lang' => $lang);
    }
    $data = json_encode(array('user_list' => $openids));
    $url = "https://api.weixin.qq.com/cgi-bin/user/info/batchget?access_token=".$this->access_token;
    $res = $this->http_request($url, $data);
    return json_decode($res, true);
  }
  //获取关注者列表
  /*
  $result = $weixin->get_user_list();
  foreach ($result["data"]["openid"] as &$openid) {
      var_dump($openid);
  }

  require_once('weixin.class.php');
  $weixin = new class_weixin();
  $result = $weixin->get_user_list();
  var_dump(count($result["data"]["openid"]));

  require_once('mysql.class.php');
  $db = new class_mysql();

  $sql = "SELECT `openid` FROM `tp_user`";
  $openidlist = $db->query_array_openid($sql);
  var_dump(count($openidlist));

  foreach ($result["data"]["openid"] as &$openid) {
      if (!in_array($openid, $openidlist)){
          $mysql_state = "INSERT INTO `tp_user` (`id`, `openid`) VALUES (NULL, '$openid');";
          $result = $db->execute($mysql_state);
      }
  }
  */
  public function get_user_list($next_openid = NULL)
  {
    $url = "https://api.weixin.qq.com/cgi-bin/user/get?access_token=".$this->access_token."&next_openid=".$next_openid;
    $res = $this->http_request($url);
    $list = json_decode($res, true);
    if ($list["count"] == 10000){
      $new = $this->get_user_list($next_openid = $list["next_openid"]);
      $list["data"]["openid"] = array_merge_recursive($list["data"]["openid"], $new["data"]["openid"]); //合并OpenID列表
    }
    return $list;
  }

  /*
  * PART 用户分组
  */
  //创建分组
  public function create_group($name)
  {
    $msg = array('group' => array('name' => $name));
    $url = "https://api.weixin.qq.com/cgi-bin/groups/create?access_token=".$this->access_token;
    $res = $this->http_request($url, json_encode($msg));
    return json_decode($res, true);
  }

  //移动用户分组
  public function update_group_member($openid, $to_groupid)
  {
    $msg = array('openid' => $openid,
        'to_groupid' => $to_groupid);
    $url = "https://api.weixin.qq.com/cgi-bin/groups/members/update?access_token=".$this->access_token;
    $res = $this->http_request($url, json_encode($msg));
    return json_decode($res, true);
  }

  //修改分组名
  public function update_group($groupid, $groupname)
  {
    $msg = array('group' => array('id' => $groupid,
        'name' => $groupname)
    );
    $url = "https://api.weixin.qq.com/cgi-bin/groups/update?access_token=".$this->access_token;
    $res = $this->http_request($url, json_encode($msg));
    return json_decode($res, true);
  }

  //查询所有分组
  public function get_groups()
  {
    $url = "https://api.weixin.qq.com/cgi-bin/groups/get?access_token=".$this->access_token;
    $res = $this->http_request($url);
    return json_decode($res, true);
  }

  /*
  * PART 菜单部分
  */
  //创建菜单
  // var_dump($weixin->create_menu($button, $matchrule));
  /*
  $button = array(array('type' => "view",
                        'name' => "微信小店",
                        'url'  => "http://www.baidu.com/",
                       ),
                  array('name' => "我的交易",
                        'sub_button' => array(
                                              array('type' => "click",
                                                    'name' => "我的订单",
                                                    'key'  => "订单KEY"
                                                    ),
                                              array('type' => "view",
                                                    'name' => "维权",
                                                    'url'  => "https://mp.weixin.qq.com/payfb/payfeedbackindex?appId="
                                                   ),
                                              )
                        )
                  );
  //个性化菜单配置信息
  $matchrule = array('group_id' => "",
                    'sex' => "1",
                    'country'  => "中国",
                    'province'  => "新疆",
                    'city'  => "五家渠",
                    'client_platform_type'  => "IOS"
                    );
  */
  public function create_menu($button, $matchrule = NULL)
  {
    foreach ($button as &$item) {
      foreach ($item as $k => $v) {
        if (is_array($v)){
          foreach ($item[$k] as &$subitem) {
            foreach ($subitem as $k2 => $v2) {
              $subitem[$k2] = urlencode($v2);
            }
          }
        }else{
          $item[$k] = urlencode($v);
        }
      }
    }

    if (isset($matchrule) && !is_null($matchrule)){
      foreach ($matchrule as $k => $v) {
        $matchrule[$k] = urlencode($v);
      }
      $data = urldecode(json_encode(array('button' => $button, 'matchrule' => $matchrule)));
      $url = "https://api.weixin.qq.com/cgi-bin/menu/addconditional?access_token=".$this->access_token;
    }else{
      $data = urldecode(json_encode(array('button' => $button)));
      $url = "https://api.weixin.qq.com/cgi-bin/menu/create?access_token=".$this->access_token;
    }
    $res = $this->http_request($url, $data);
    return json_decode($res, true);
  }
  /* 创建自定义菜单 原始数据
  $menu = '{
    "button":[
    {
          "name":"天气预报",
         "sub_button":[
          {
             "type":"click",
             "name":"北京天气",
             "key":"天气北京"
          },
          {
              "type":"view",
              "name":"本地天气",
              "url":"http://m.hao123.com/a/tianqi"
          }]
     },
     {
         "name":"方倍工作室",
         "sub_button":[
          {
             "type":"click",
             "name":"公司简介",
             "key":"company"
          },
          {
              "type":"click",
              "name":"讲个笑话",
              "key":"笑话"
          }]
     }]
  }';
  */
  public function create_menu_raw($menu)
  {
    if (stripos($menu, "matchrule")){
      $url = "https://api.weixin.qq.com/cgi-bin/menu/addconditional?access_token=".$this->access_token;
    }else{
      $url = "https://api.weixin.qq.com/cgi-bin/menu/create?access_token=".$this->access_token;
    }
    $res = $this->http_request($url, $menu);
    return json_decode($res, true);
  }

  /*
  PART 发送消息
  */
  //发送客服消息，已实现发送文本、图文，其他类型可扩展
  /*
  $result = $weixin->send_custom_message($openid, "text", "没有查询到你的订单记录！");

  //小写样式
  $data[] = array("title"=>"123", "description"=>"", "picurl"=>"", "url" =>"");
  $data[] = array("title"=>"456", "description"=>"", "picurl"=>"", "url" =>"");
  //大写样式，基础消息接口格式，已兼容
  $content[] = array("Title"=>"多图文1标题", "Description"=>"", "PicUrl"=>"http://discuz.comli.com/weixin/weather/icon/cartoon.jpg", "Url" =>"http://m.cnblogs.com/?u=txw1958");
  $content[] = array("Title"=>"多图文2标题", "Description"=>"", "PicUrl"=>"http://d.hiphotos.bdimg.com/wisegame/pic/item/f3529822720e0cf3ac9f1ada0846f21fbe09aaa3.jpg", "Url" =>"http://m.cnblogs.com/?u=txw1958");
  $content[] = array("Title"=>"多图文3标题", "Description"=>"", "PicUrl"=>"http://g.hiphotos.bdimg.com/wisegame/pic/item/18cb0a46f21fbe090d338acc6a600c338644adfd.jpg", "Url" =>"http://m.cnblogs.com/?u=txw1958");
  $result = $weixin->send_custom_message($openid, "news", $data);

  //国片、语音
  $upload_result = $weixin->upload_media("image", "winter.jpg");
  $send_result = $weixin->send_custom_message($openid, $upload_result['type'], array('media_id'=>$upload_result['media_id']));
  $upload_result = $weixin->upload_media("voice", "invite.mp3");
  $send_result = $weixin->send_custom_message($openid, $upload_result['type'], array('media_id'=>$upload_result['media_id']));
  //视频
  $upload_video_result = $weixin->upload_media("video", "bbb.mp4");
  $upload_thumb_result = $weixin->upload_media("thumb", "pondbay.jpg");
  $data = array("media_id"=>$upload_video_result['media_id'], "thumb_media_id"=>$upload_thumb_result['thumb_media_id']);
  $send_result = $weixin->send_custom_message($openid, $upload_video_result['type'], $data);
  //音乐
  $upload_thumb_result = $weixin->upload_media("thumb", "pondbay.jpg");
  $data = array("title"=>urlencode("最炫民族风2"),
                "description"=>urlencode("歌手：凤凰传奇"),
                "musicurl"=>"http://121.199.4.61/music/zxmzf.mp3",
                "hqmusicurl"=>"http://121.199.4.61/music/zxmzf.mp3",
                "thumb_media_id"=>$upload_thumb_result['thumb_media_id']);
  $send_result = $weixin->send_custom_message($openid, "music", $data);
  */
  public function send_custom_message($touser, $type, $data)
  {
    $msg = array('touser' =>$touser);
    $msg['msgtype'] = $type;
    switch($type)
    {
      case 'text':
        $msg[$type]    = array('content'=>urlencode($data));
        break;
      case 'news':
        $data2 = array();
        foreach ($data as &$item) {
          $item2 = array();
          foreach ($item as $k => $v) {
            $item2[strtolower($k)] = urlencode($v);
          }
          $data2[] = $item2;
        }
        $msg[$type]    = array('articles'=>$data2);
        break;
      case 'music':
      case 'image':
      case 'voice':
      case 'video':
        $msg[$type]    = $data;
        break;
      default:
        $msg['text'] = array('content'=>urlencode("不支持的消息类型 ".$type));
        break;
    }
    $url = "https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token=".$this->access_token;
    return $this->http_request($url, urldecode(json_encode($msg)));
  }


  //高级群发(根据分组)
  public function mass_send_group($groupid, $type, $data)
  {
    $msg = array('filter' => array('group_id'=>$groupid));
    $msg['msgtype'] = $type;

    switch($type)
    {
      case 'text':
        $msg[$type] = array('content'=> $data);
        break;
      case 'image':
      case 'voice':
      case 'mpvideo':
      case 'mpnews':
        $msg[$type] = array('media_id'=> $data);
        break;

    }
    $url = "https://api.weixin.qq.com/cgi-bin/message/mass/sendall?access_token=".$this->access_token;
    $res = $this->http_request($url, json_encode($msg));
    return json_decode($res, true);
  }

  //发送模版消息
  /*
  $template = array('touser' => "owddJuAiiQpXZedAWxjpp3pkZTzU",
                    'template_id' => "jD1Jfu0ElKcyEK0CfJ2JjTy4U1fjYI09l6eax9BBu9U",
                    'url' => "",
                    'topcolor' => "#7B68EE",
                    'data' => array('first'    => array('value' => "您好，方倍，欢迎使用模版消息！",
                                                       'color' => "#743A3A",
                                                      ),
                                    'product' => array('value' => "微信公众平台开发最佳实践",
                                                       'color' => "#FF0000",
                                                      ),
                                     'price'     => array('value' => "69.00元",
                                                       'color' => "#C4C400",
                                                      ),
                                     'time'     => array('value' => "2014年6月1日",
                                                       'color' => "#0000FF",
                                                      ),
                                    'remark'     => array('value' => "\\n你的订单已提交，我们将尽快发货。祝您生活愉快！",
                                                       'color' => "#008000",
                                                      ),
                                  )
  );
  $weixin->send_template_message($template);
  */
  public function send_template_message($template)
  {
    foreach ($template['data'] as  $k => &$item) {
      $item['value'] = urlencode($item['value']);
    }
    $url = "https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=".$this->access_token;
    $res = $this->http_request($url, urldecode(json_encode($template)));
    return json_decode($res, true);
  }

  //生成参数二维码
  /*
  for ($i = 12; $i <= 50; $i++) {
      $result = $weixin->create_qrcode("QR_LIMIT_SCENE", $i);
  }
  */
  public function create_qrcode($scene_type, $scene_id)
  {
    switch($scene_type)
    {
      case 'QR_LIMIT_SCENE': //永久
        $msg = array('action_name' => $scene_type,
            'action_info' => array('scene' => array('scene_id' => $scene_id))
        );
        break;
      case 'QR_SCENE':        //临时
        $msg = array('action_name' => $scene_type,
            'expire_seconds' => 2592000,   //30天
            'action_info' => array('scene' => array('scene_id' => $scene_id))
        );
        break;
      case 'QR_LIMIT_STR_SCENE':    //永久字符串
        $msg = array('action_name' => $scene_type,
            'action_info' => array('scene' => array('scene_str' => strval($scene_id)))
        );
        break;
    }
    // var_dump(json_encode($msg));
    $url = "https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token=".$this->access_token;
    $res = $this->http_request($url, json_encode($msg));
    $result = json_decode($res, true);
    // $imgurl = "https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket=".urlencode($result["ticket"]);
    // $imgdata = $this->http_request($imgurl);
    // return file_put_contents($scene_type."_".$scene_id.".jpg", $imgdata);
    return $result;
  }

  //长链接转短链接接口
  public function url_long2short($longurl)
  {
    $msg = array('action' => "long2short",
        'long_url' => $longurl
    );
    $url = "https://api.weixin.qq.com/cgi-bin/shorturl?access_token=".$this->access_token;
    return $this->http_request($url, json_encode($msg));
  }


  /*
  *  PART2 微信小店接口
  */

  // //上传图片
  // public function upload_image($filename, $imgdata)
  // {
  // $url = "https://api.weixin.qq.com/merchant/common/upload_img?access_token=".$this->access_token."&filename=".$filename;
  // $res = $this->http_request($url, $imgdata);
  // return json_decode($res, true);
  // }

  //获取所有运费模版
  public function get_all_express()
  {
    $url = "https://api.weixin.qq.com/merchant/express/getall?access_token=".$this->access_token;
    $res = $this->http_request($url);
    return json_decode($res, true);
  }

  //增加分组
  // $groupname = "测试分组";
  // $result = $weixin->add_product_group($groupname);
  public function add_product_group($groupname)
  {
    $url = "https://api.weixin.qq.com/merchant/group/add?access_token=".$this->access_token;
    $msg = array('group_detail' => array('group_name' => urlencode($groupname),
        'product_list' => array()
    )
    );
    $data = urldecode(json_encode($msg));
    $res = $this->http_request($url, $data);
    return json_decode($res, true);
  }

  //修改商品分组
  public function mod_product_group($groupid, $productid, $action = 1)
  {
    $msg = array('group_id' => $groupid,
        'product'  => array(array('product_id' => $productid,
            'mod_action' => $action
        )
        )
    );
    $url = "https://api.weixin.qq.com/merchant/group/productmod?access_token=".$this->access_token;
    $res = $this->http_request($url, urldecode(json_encode($msg)));
    return json_decode($res, true);
  }

  //获取所有分组
  public function get_all_group()
  {
    $url = "https://api.weixin.qq.com/merchant/group/getall?access_token=".$this->access_token;
    $res = $this->http_request($url);
    // return json_decode($res, true);
    return $res;
  }

  //上传商品
  public function create_merchant($productData)
  {
    $url = "https://api.weixin.qq.com/merchant/create?access_token=".$this->access_token;
    $res = $this->http_request($url, $productData);
    return json_decode($res, true);
  }

  //获取指定状态商品
  public function get_merchant_by_status($status)
  {
    $data = array('status' =>$status);
    $url = "https://api.weixin.qq.com/merchant/getbystatus?access_token=".$this->access_token;
    $res = $this->http_request($url, urldecode(json_encode($data)));
    return json_decode($res, true);
    // return $res;
  }

  //商品上下架
  public function set_delivery_status($orderid, $data = null)
  {
    $msg = array('order_id' => $orderid,
        'need_delivery'  => "0",
    );
    $url = "https://api.weixin.qq.com/merchant/order/setdelivery?access_token=".$this->access_token;
    $res = $this->http_request($url, urldecode(json_encode($msg)));
    return json_decode($res, true);
  }

  //查询商品
  public function get_merchant_by_product_id($id)
  {
    $data = array('product_id' =>$id);
    $url = "https://api.weixin.qq.com/merchant/get?access_token=".$this->access_token;
    $res = $this->http_request($url, urldecode(json_encode($data)));
    return json_decode($res, true);
    // return $res;
  }

  //查询分类
  public function get_sub_category($id)
  {
    $data = array('cate_id' =>$id);
    $url = "https://api.weixin.qq.com/merchant/category/getsub?access_token=".$this->access_token;
    $res = $this->http_request($url, json_encode($data));
    return json_decode($res, true);
    // return $res;
  }

  //商品上下架
  public function mod_product_status($productid, $status = 1)
  {
    $msg = array('product_id' => $productid,
        'status'  => $status
    );
    $url = "https://api.weixin.qq.com/merchant/modproductstatus?access_token=".$this->access_token;
    $res = $this->http_request($url, urldecode(json_encode($msg)));
    return json_decode($res, true);
  }

  //根据订单ID获取订单详情
  public function get_detail_by_order_id($id)
  {
    $data = array('order_id' =>$id);
    $url = "https://api.weixin.qq.com/merchant/order/getbyid?access_token=".$this->access_token;
    $res = $this->http_request($url, json_encode($data));
    return json_decode($res, true);
  }

  //根据订单状态/创建时间获取订单详情
  public function get_detail_by_filter($data = null)
  {
    $url = "https://api.weixin.qq.com/merchant/order/getbyfilter?access_token=".$this->access_token;
    $res = $this->http_request($url, $data);
    return json_decode($res, true);
  }


  /*
  * PART 素材管理
  */
  //新增临时素材(原多媒体文件)( (图片（image）、语音（voice）、视频（video）和缩略图（thumb）)) ,临时素材 不包括图文消息
  // $weixin->upload_temporary_material("thumb", "logo.jpg"); //logo.jpg须放于类同目录，注意路径
  // { ["type"]=> string(5) "thumb" ["thumb_media_id"]=> string(64) "4rqefcVf-dcsnmuz1Q9ESn_KC3sBVuZhC7JM5-0fEvrttwQFs2KNErCw2YTYPx_l" ["created_at"]=> int(1463105848) }
  public function upload_temporary_material($type, $file)
  {
    if (PHP_OS == "Linux"){        //Linux
      $data = array("media"  => "@".dirname(__FILE__).'/'.$file);
    }else{                        //WINNT
      $data = array("media"  => "@".dirname(__FILE__).'\\'.$file);
    }
    $url = "https://api.weixin.qq.com/cgi-bin/media/upload?access_token=".$this->access_token."&type=".$type;
    $res = $this->http_request($url, $data);
    return json_decode($res, true);
  }

  //获取临时素材 请注意，视频文件不支持 https下载
  public function get_temporary_material($media_id, $file_extension)
  {
    $url = "https://api.weixin.qq.com/cgi-bin/media/get?access_token=".$this->access_token."&media_id=".$media_id;
    $media_data = $this->http_request($url);
    return file_put_contents($media_id.".".$file_extension, $media_data);
  }

  //上传图片 用于永久素材中图文消息内的图片URL
  public function upload_image($file)
  {
    if (PHP_OS == "Linux"){        //Linux
      $data = array("buffer"  => "@".dirname(__FILE__).'/'.$file);
    }else{                        //WINNT
      $data = array("buffer"  => "@".dirname(__FILE__).'\\'.$file);
    }
    $url = "https://api.weixin.qq.com/cgi-bin/media/uploadimg?access_token=".$this->access_token;
    $res = $this->http_request($url, $data);
    return json_decode($res, true);
  }

  //上传永久素材 (图片（image）、语音（voice）、视频（video）和缩略图（thumb）)
  /*  1. 视频接口有误，使用linux curl命令也上传失败？
      $result = $weixin->upload_permanent_material("video", "intro.mp4", array('title'=> "TITLE1", 'introduction'=>"INTRODUCTION1"));
      [root@iZ94xwvm4vxZ test]# curl "https://api.weixin.qq.com/cgi-bin/material/add_material?access_token=ruY5chntUrm64Yu7oAzLLyN1dETow4eNN0AJd70skAYsJ36lyVVJTrvd5ARtyD_Z7D1FOIPf0NjvSjwf1ebL8-cNv0v7Ol81Q1i6Kc0sMHHnrIUCQ1NM7bJD6V4sJYW4LBViCHASIC" -F media=@intro.mp4 -F  description='{"title":VIDEO_TITLE1, "introduction":INTRODUCTION2}'
      {"errcode":40062,"errmsg":"invalid title size hint: [mH8uIa0329sz63]"}
      2. 图片、缩略图能上传成功但不在后台中显示
      3. 语音上传正常且在后台中可看到
  */
  // $weixin->upload_permanent_material("image", "logo.jpg"); //logo.jpg须放于类同目录，注意路径
  public function upload_permanent_material($type, $file, $video_info = null)
  {
    if (PHP_OS == "Linux"){        //Linux
      $data = array("media" => "@".dirname(__FILE__).'/'.$file);
    }else{                       //WINNT
      $data = array("media" => "@".dirname(__FILE__).'\\'.$file);
    }
    if ($type == "video"){
      $data["description"] = json_encode($video_info);
    }
    $url = "https://api.weixin.qq.com/cgi-bin/material/add_material?access_token=".$this->access_token."&type=".$type; //官方url有误
    $res = $this->http_request($url, $data);
    return json_decode($res, true);
  }


  //上传永久图文消息素材
  /*
  $news[] = array("title"=>"微信公众平台开发教程",
                  "thumb_media_id"=>"jUNrpW0f_CAOs07HY_ENiJY4olL29ZD4gggCjT9_LqY",
                  "author"=>"",
                  "digest" =>"",
                  "show_cover_pic" =>"0",
                  "content" =>"<li>1. 入门教程</li>
  <li>2. 消息收发</li>
  <li>3. 自定义菜单</li>
  <li>4. JS SDK</li>
  <li>5. 群发</li>",
                  "content_source_url" =>"",
                  );
  $news[] = array("title"=>"微信公众平台开发(1) 入门教程",
                  "thumb_media_id"=>"jUNrpW0f_CAOs07HY_ENiGf7yUMZi6aaqoQsaMBKTIU",
                  "author"=>"方倍工作室",
                  "digest" =>"微信公众平台开发经典的入门教程，学习微信公众平台开发必经之路！",
                  "show_cover_pic" =>"1",
                  "content" =>"<div>
  <p>本教程是微信公众平台的入门教程，它将引导你完成如下任务：</p>
  <ol>
  <li>1. 创建新浪云计算平台应用</li>
  <li>2. 启用微信公众平台开发模式</li>
  <li>3. 基础接口消息及事件</li>
  <li>4. 微信公众平台PHP SDK</li>
  <li>5. 微信公众平台开发模式原理</li>
  <li>6. 开发天气预报功能</li>
  </ol>
  </div>",
                  "content_source_url" =>"http://m.cnblogs.com/99079/3153567.html?full=1",
                  );
  $result = $weixin->upload_permanent_news($news);
  */
  public function upload_permanent_news($news)
  {
    foreach ($news as &$item) {
      foreach ($item as $k => $v) {
        $item[$k] = urlencode($v);
      }
    }
    $data = array("articles"=>$news);
    $url = "https://api.weixin.qq.com/cgi-bin/material/add_news?access_token=".$this->access_token;
    $res = $this->http_request($url, urldecode(json_encode($data)));
    return json_decode($res, true);
  }

  //修改永久图文素材
  /*
  $news = array( "media_id"=>"jUNrpW0f_CAOs07HY_ENiC26qkeiCC3mJ2G4rzRecQY",
                  "index"=>"0",
                  "articles"=> array("title"=>"微信公众平台开发教程修改",
                                  "thumb_media_id"=>"jUNrpW0f_CAOs07HY_ENiJY4olL29ZD4gggCjT9_LqY",
                                  "author"=>"方倍",
                                  "digest" =>"",
                                  "show_cover_pic" =>"1",
                                  "content" =>"<li>1. 入门教程</li><br><li>2. 消息收发</li><br><li>3. 自定义菜单</li><br><li>4. JS SDK</li><br><li>5. 群发</li><br><li>6. 微信支付</li>",
                                  "content_source_url" =>"http://m.cnblogs.com/99079/3153567.html?full=1",
                                  ),
                  );
  $result = $weixin->update_permanent_news($news);
  */
  public function update_permanent_news($news)
  {
    foreach ($news as &$item) {
      if (is_array($item)){
        foreach ($item as $k => $v) {
          $item[$k] = urlencode($v);
        }
      }
    }
    // var_dump(urldecode(json_encode($news)));
    $url = "https://api.weixin.qq.com/cgi-bin/material/update_news?access_token=".$this->access_token;
    $res = $this->http_request($url, urldecode(json_encode($news)));
    return json_decode($res, true);
  }

  //删除永久素材
  // $result = $weixin->del_permanent_material("image");
  public function del_permanent_material($media_id)
  {
    $msg = array('media_id' =>$media_id);
    $url = "https://api.weixin.qq.com/cgi-bin/material/del_material?access_token=".$this->access_token;
    return $this->http_request($url, json_encode($msg));
  }

  //获取素材总数
  public function get_material_count()
  {
    $url = "https://api.weixin.qq.com/cgi-bin/material/get_materialcount?access_token=".$this->access_token;
    return $this->http_request($url);
  }

  //获取素材列表
  // $send_result = $weixin->batch_get_material("image");
  public function batch_get_material($type, $offset = 0, $count = 20)
  {
    $msg = array('type' =>$type, 'offset' =>$offset, 'count' =>$count);
    // var_dump($msg);
    $url = "https://api.weixin.qq.com/cgi-bin/material/batchget_material?access_token=".$this->access_token;
    // var_dump(json_encode($msg));
    $res = $this->http_request($url, json_encode($msg));
    return json_decode($res, true);
    // return $this->http_request($url, json_encode($msg));
  }


  //HTTP请求（支持HTTP/HTTPS，支持GET/POST）
  protected function http_request($url, $data = null)
  {
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
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

  //日志记录
//  private function logger($log_content)
//  {
//    if(isset($_SERVER['HTTP_APPNAME'])){   //SAE
//      sae_set_display_errors(false);
//      sae_debug($log_content);
//      sae_set_display_errors(true);
//    }else if($_SERVER['REMOTE_ADDR'] != "127.0.0.1"){ //LOCAL
//      $max_size = 500000;
//      $log_filename = "log.xml";
//      if(file_exists($log_filename) and (abs(filesize($log_filename)) > $max_size)){unlink($log_filename);}
//      file_put_contents($log_filename, date('Y-m-d H:i:s').$log_content."\r\n", FILE_APPEND);
//    }
//  }







}

