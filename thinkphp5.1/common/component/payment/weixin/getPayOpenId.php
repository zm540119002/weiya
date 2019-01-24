<?php

namespace common\component\payment\weixin;
class getPayOpenId {
  private $appId;
  private $appSecret;
  private $path;
  private $access_token;

  public function __construct($appId, $appSecret) {
    $this->appId = $appId;
    $this->appSecret = $appSecret;
    $this->path = __DIR__ . 'Jssdk.php/';
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
    $this -> access_token = $access_token;
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


  //生成OAuth2的Access Token
  public function oauth2_access_token($code)
  {

    $url = "https://api.weixin.qq.com/sns/oauth2/access_token?appId=".$this->appId."&secret=".$this->appSecret."&code=".$code."&grant_type=authorization_code";
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














  //长链接转短链接接口
  public function url_long2short($longurl)
  {
    $msg = array('action' => "long2short",
        'long_url' => $longurl
    );
    $url = "https://api.weixin.qq.com/cgi-bin/shorturl?access_token=".$this->access_token;
    return $this->http_request($url, json_encode($msg));
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

