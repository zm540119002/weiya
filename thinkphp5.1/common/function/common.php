<?php
function GetTimeString()
{
    date_default_timezone_set('Asia/Shanghai');
    $timestamp=time();
    $hours = date('H',$timestamp);
    $minutes = date('i',$timestamp);
    $seconds =date('s',$timestamp);
    $month = date('m',$timestamp);
    $day =  date('d',$timestamp);
    $stamp= $month.$day.$hours.$minutes.$seconds;
    return $stamp;
}

/**
 * 字符串截取，支持中文和其他编码
 * @static
 * @access public
 * @param string $str 需要转换的字符串
 * @param string $start 开始位置
 * @param string $length 截取长度
 * @param string $charset 编码格式
 * @param string $suffix 截断显示字符
 * @return string
 */
function msubstr($str, $start = 0, $length, $charset = "utf-8", $suffix = true)
{
    if (function_exists("mb_substr"))
        $slice = mb_substr($str, $start, $length, $charset);
    elseif (function_exists('iconv_substr')) {
        $slice = iconv_substr($str, $start, $length, $charset);
        if (false === $slice) {
            $slice = '';
        }
    } else {
        $re['utf-8'] = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|[\xe0-\xef][\x80-\xbf]{2}|[\xf0-\xff][\x80-\xbf]{3}/";
        $re['gb2312'] = "/[\x01-\x7f]|[\xb0-\xf7][\xa0-\xfe]/";
        $re['gbk'] = "/[\x01-\x7f]|[\x81-\xfe][\x40-\xfe]/";
        $re['big5'] = "/[\x01-\x7f]|[\x81-\xfe]([\x40-\x7e]|\xa1-\xfe])/";
        preg_match_all($re[$charset], $str, $match);
        $slice = join("", array_slice($match[0], $start, $length));
    }
    return $suffix ? $slice . '...' : $slice;
}

/**
 * 系统加密方法
 * @param string $data 要加密的字符串
 * @param string $key 加密密钥
 * @param int $expire 过期时间 单位 秒
 * @return string
 */
function think_encrypt($data, $key = '', $expire = 0)
{
    $key = md5(empty($key) ? C('DATA_AUTH_KEY') : $key);
    $data = base64_encode($data);
    $x = 0;
    $len = strlen($data);
    $l = strlen($key);
    $char = '';

    for ($i = 0; $i < $len; $i++) {
        if ($x == $l) $x = 0;
        $char .= substr($key, $x, 1);
        $x++;
    }

    $str = sprintf('%010d', $expire ? $expire + time() : 0);

    for ($i = 0; $i < $len; $i++) {
        $str .= chr(ord(substr($data, $i, 1)) + (ord(substr($char, $i, 1))) % 256);
    }
    return str_replace(array('+', '/', '='), array('-', '_', ''), base64_encode($str));
}

/**
 * 系统解密方法
 * @param  string $data 要解密的字符串 （必须是think_encrypt方法加密的字符串）
 * @param  string $key 加密密钥
 * @return string
 */
function think_decrypt($data, $key = '')
{
    $key = md5(empty($key) ? C('DATA_AUTH_KEY') : $key);
    $data = str_replace(array('-', '_'), array('+', '/'), $data);
    $mod4 = strlen($data) % 4;
    if ($mod4) {
        $data .= substr('====', $mod4);
    }
    $data = base64_decode($data);
    $expire = substr($data, 0, 10);
    $data = substr($data, 10);

    if ($expire > 0 && $expire < time()) {
        return '';
    }
    $x = 0;
    $len = strlen($data);
    $l = strlen($key);
    $char = $str = '';

    for ($i = 0; $i < $len; $i++) {
        if ($x == $l) $x = 0;
        $char .= substr($key, $x, 1);
        $x++;
    }

    for ($i = 0; $i < $len; $i++) {
        if (ord(substr($data, $i, 1)) < ord(substr($char, $i, 1))) {
            $str .= chr((ord(substr($data, $i, 1)) + 256) - ord(substr($char, $i, 1)));
        } else {
            $str .= chr(ord(substr($data, $i, 1)) - ord(substr($char, $i, 1)));
        }
    }
    return base64_decode($str);
}

/**
 * 数据签名认证
 * @param  array $data 被认证的数据
 * @return string       签名
 */
function data_auth_sign($data)
{
    //数据类型检测
    if (!is_array($data)) {
        $data = (array)$data;
    }
    ksort($data); //排序
    $code = http_build_query($data); //url编码并生成query字符串
    $sign = sha1($code); //生成签名
    return $sign;
}

/**$this->error() 的Ajax格式
 * @param $msg
 * @param string $extend
 * @return array
 */
function errorMsg($msg, $extend = '')
{
    $return = array(
        'status' => 0,
        'info' => $msg
    );
    is_array($extend) && ($return = array_merge($return, $extend));
    return $return;
}

/**
 * $this->success() 的Ajax格式
 * @param $msg
 * @param string $extend
 * @return array
 */
function successMsg($msg, $extend = '')
{
    $return = array(
        'status' => 1,
        'info' => $msg
    );
    is_array($extend) && ($return = array_merge($return, $extend));
    return $return;
}

/**
 * Compares two strings $a and $b in length-constant time.
 * @param $a
 * @param $b
 * @return bool
 */
function slow_equals($a, $b)
{
    $diff = strlen($a) ^ strlen($b);
    for ($i = 0; $i < strlen($a) && $i < strlen($b); $i++) {
        $diff |= ord($a[$i]) ^ ord($b[$i]);
    }
    return $diff === 0;
}

/**
 * @param $mobile
 * @return bool
 */
function isMobile($mobile)
{
    return preg_match("/^1[34578]\d{9}$/", trim($mobile)) ? true : false;
}

/**
 * @param $email
 * @return bool
 */
function isEmail($email)
{
    return (strlen($email) > 6 && preg_match("/^[\w\-\.]+@[\w\-\.]+(\.\w+)+$/", $email)) ? true : false;
}

/**
 * @param $qq
 * @return bool
 */
function isQQ($qq)
{
    return preg_match('/^[1-9]\d{4,12}$/', $qq) ? true : false;
}

//验证不能为0的正数 11.1 | 0
function checkPricePlus($int)
{
    if (is_numeric($int) > 0) {
        if ($int > 0) {
            return true;
        } else {
            return false;
        }
    }
    return false;
}

//验证可以为0的正数 11.1
function checkPriceZero($int)
{
    if (is_numeric($int) > 0) {
        if ($int >= 0) {
            return true;
        } else {
            return false;
        }
    }
    return false;
}

//正整数
function positiveInteger($int)
{
    if (preg_match("/^[1-9]\d*$/", $int)) {
        return true;
    } else {
        return false;
    }
}

//非负正数
function nonNegativeInteger($int)
{
    if (preg_match("/^\d+$/", $int)) {
        return true;
    } else {
        return false;
    }
}

/**
 * 产生随机字串，可用来自动生成密码
 * 默认长度6位 数字 支持中文
 * @param string $len 长度
 * @param string $type 字串类型
 * 0--大小写字母混合 1--数字 2--大写字母 3--小写字母 4--中文汉字 其它--大小写字母混合
 * @param string $addChars 额外字符
 * @return string
 */
function create_random_str($len = 6, $type = 1, $prefixChars = "", $addChars = "")
{
    $range_code = common\lib\String::randString($len, $type, $addChars);
    return $prefixChars ? $prefixChars . $range_code : $range_code;
}

/**
 * 判断是否SSL协议
 * @return boolean
 */
function is_ssl() {
    if(isset($_SERVER['HTTPS']) && ('1' == $_SERVER['HTTPS'] || 'on' == strtolower($_SERVER['HTTPS']))){
        return true;
    }elseif(isset($_SERVER['SERVER_PORT']) && ('443' == $_SERVER['SERVER_PORT'] )) {
        return true;
    }
    return false;
}

/*
 *读取URL中传来的参数
 *@param string $variablename 参数的名称
 *@return string 参数值
 */
function get_url_param($variableName)
{
    return urldecode(input($variableName));
}

/*
 * 获取无参数URL
 */
function get_current_page_url()
{
    $pageURL = 'http';
    if ($_SERVER["HTTPS"] == "on") {
        $pageURL .= "s";
    }
    $pageURL .= "://";
    $this_page = $_SERVER["REQUEST_URI"];
    // 只取 ? 前面的内容
    if (strpos($this_page, "?") !== false)
        $this_page = reset(explode("?", $this_page));
    if ($_SERVER["SERVER_PORT"] != "80") {
        $pageURL .= $_SERVER["SERVER_NAME"] . ":" . $_SERVER["SERVER_PORT"] . $this_page;
    } else {
        $pageURL .= $_SERVER["SERVER_NAME"] . $this_page;
    }
    return $pageURL;
}

/**
 * 循环创建目录
 */
function mk_dir($dir, $mode = 0755)
{
    if (is_dir($dir) || @mkdir($dir, $mode)) return true;
    if (!mk_dir(dirname($dir), $mode)) return false;
    return @mkdir($dir, $mode);
}

/**
 * 计算 UTF-8 字符串长度（忽略字节的方案）
 *
 * @param string $str
 * @return int
 */
function strlen_utf8($str)
{
    $i = 0;
    $count = 0;
    $len = strlen($str);
    while ($i < $len) {
        $chr = ord($str[$i]);
        $count++;
        $i++;
        if ($i >= $len) {
            break;
        }
        if ($chr & 0x80) {
            $chr <<= 1;
            while ($chr & 0x80) {
                $i++;
                $chr <<= 1;
            }
        }
    }
    return $count;
}

/**
 * 验证字符长度是否在某个区间，
 * $str : 表单字段接收的内容，
 * $min:最小长度，
 * max:最大长度，
 */
function checkLength($str, $min = 6, $max = 10)
{
    preg_match_all("/./u", $str, $matches);
    $len = count($matches[0]);
    if ($len < $min || $len > $max) {
        return false;
    } else {
        return true;
    }
}

/**
 * 上传文件（单个）
 * @param $conf
 * @param bool $dbTemp
 * @return array
 */
function think_upload($conf)
{
    if (!array_key_exists('savePath', $conf)) {
        throw_exception('未设置savePath key值');
    }
    $config = array(
        'maxSize' => 2 * 1024 * 1024,
        'rootPath' => C('UPLOAD_PATH'),
        'autoSub' => true,
        'replace' => true,
        'exts' => array('jpg', 'gif', 'png', 'jpeg'),
        'subName' => '',
    );
    $upload = new \Think\Upload(array_merge($config, $conf));
    $info = $upload->upload();
    if (!$info) {
        return array(false, $upload->getError());
    }
    return array(true, $info[0]);
}

/*
 *重定向
 */
function immediatelyJump($url){
    header("location: ".$url);
    exit;
}

/**多维数组排序
 * @param $array
 * @param $field
 * @param bool $desc
 */
function sortArrByField(&$array, $field, $desc = false){
    $fieldArr = array();
    foreach ($array as $k => $v) {
        $fieldArr[$k] = $v[$field];
    }
    $sort = $desc == false ? SORT_ASC : SORT_DESC;
    array_multisort($fieldArr, $sort, $array);
}

/**
 * 将xml转换为数组
 * @param string $xml:xml文件或字符串
 * @return array
 */
function xmlToArray($xml){
    //考虑到xml文档中可能会包含<![CDATA[]]>标签，第三个参数设置为LIBXML_NOCDATA
    if (file_exists($xml)) {
        libxml_disable_entity_loader(false);
        $xml_string = simplexml_load_file($xml,'SimpleXMLElement', LIBXML_NOCDATA);
    }else{
        libxml_disable_entity_loader(true);
        $xml_string = simplexml_load_string($xml,'SimpleXMLElement', LIBXML_NOCDATA);
    }
    $result = json_decode(json_encode($xml_string),true);
    return $result;
}

/**
 * 将数组转换为xml
 * @param array $arr:数组
 * @param object $dom:Document对象，默认null即可
 * @param object $node:节点对象，默认null即可
 * @param string $root:根节点名称
 * @param string $cdata:是否加入CDATA标签，默认为false
 * @return string
 */
function arrayToXml($arr,$dom=null,$node=null,$root='xml',$cdata=false){
    if (!$dom){
        $dom = new DOMDocument('1.0','utf-8');
    }
    if(!$node){
        $node = $dom->createElement($root);
        $dom->appendChild($node);
    }
    foreach ($arr as $key=>$value){
        $child_node = $dom->createElement(is_string($key) ? $key : 'node');
        $node->appendChild($child_node);
        if (!is_array($value)){
            if (!$cdata) {
                $data = $dom->createTextNode($value);
            }else{
                $data = $dom->createCDATASection($value);
            }
            $child_node->appendChild($data);
        }else {
            arrayToXml($value,$dom,$child_node,$root,$cdata);
        }
    }
    return $dom->saveXML();
}




/**
 * 生成签名
 * @return 签名，本函数不覆盖sign成员变量
 */
//require_once(dirname(dirname(__FILE__)) . '/Component/WxpayAPI/lib/WxPay.Api.php');
function makeSign($data){
    //获取微信支付秘钥
    $key = config('wx_config.key');
    // 去空
    $data=array_filter($data);
    //签名步骤一：按字典序排序参数
    ksort($data);
    $string_a=http_build_query($data);
    $string_a=urldecode($string_a);
    //签名步骤二：在string后加入KEY
    //$config=$this->config;
    $string_sign_temp=$string_a."&key=".$key;
    //签名步骤三：MD5加密
    $sign = md5($string_sign_temp);
    // 签名步骤四：所有字符转为大写
    $result=strtoupper($sign);
    return $result;
}

/**
 * 是否为微信浏览器
 * @return bool
 */
function isWxBrowser(){
    if ( strpos($_SERVER['HTTP_USER_AGENT'],'MicroMessenger') !== false ){
        return true;
    }
    return false;
}

/**生成编号（32位纯数字）
 * @return string
 */
function generateSN($len=18){
    return date('YmdHis',time()) . create_random_str($len);
}

/**
 * 过滤数组元素前后空格 (支持多维数组)
 * @param $array 要过滤的数组
 * @return array|string
 */
function trim_array_element($array){
    if(!is_array($array))
        return trim($array);
    return array_map('trim_array_element',$array);
}

/**
 * @return bool
 * 判断客户端是PCweb端还是移动手机端方法
 */
function isPhoneSide()
{
    // 如果有HTTP_X_WAP_PROFILE则一定是移动设备
    if (isset ($_SERVER['HTTP_X_WAP_PROFILE']))
    {
        return true;
    }
    // 如果via信息含有wap则一定是移动设备,部分服务商会屏蔽该信息
    if (isset ($_SERVER['HTTP_VIA']))
    {
        // 找不到为flase,否则为true
        return stristr($_SERVER['HTTP_VIA'], "wap") ? true : false;
    }
    // 脑残法，判断手机发送的客户端标志,兼容性有待提高
    if (isset ($_SERVER['HTTP_USER_AGENT']))
    {
        $clientkeywords = array ('nokia',
            'sony',
            'ericsson',
            'mot',
            'samsung',
            'htc',
            'sgh',
            'lg',
            'sharp',
            'sie-',
            'philips',
            'panasonic',
            'alcatel',
            'lenovo',
            'iphone',
            'ipod',
            'blackberry',
            'meizu',
            'android',
            'netfront',
            'symbian',
            'ucweb',
            'windowsce',
            'palm',
            'operamini',
            'operamobi',
            'openwave',
            'nexusone',
            'cldc',
            'midp',
            'wap',
            'mobile'
        );
        // 从HTTP_USER_AGENT中查找手机浏览器的关键字
        if (preg_match("/(" . implode('|', $clientkeywords) . ")/i", strtolower($_SERVER['HTTP_USER_AGENT'])))
        {
            return true;
        }
    }
    // 协议法，因为有可能不准确，放到最后判断
    if (isset ($_SERVER['HTTP_ACCEPT']))
    {
        // 如果只支持wml并且不支持html那一定是移动设备
        // 如果支持wml和html但是wml在html之前则是移动设备
        if ((strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') !== false) && (strpos($_SERVER['HTTP_ACCEPT'], 'text/html') === false || (strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') < strpos($_SERVER['HTTP_ACCEPT'], 'text/html'))))
        {
            return true;
        }
    }
    return false;
}

//生成不带二维码
function createQRcode($url){
    //生成二维码图片
    $object = new \common\component\code\Qrcode();
    $qrcodePath = WEB_URL.'Public/images/qrcode/';//保存文件路径
    $fileName = time().'.png';//保存文件名
    $outFile = $qrcodePath.$fileName;
    $level = 'L'; //容错级别
    $size = 10; //生成图片大小
    $frameSize = 2; //边框像素
    $saveAndPrint = true;
    $object->png($url, $outFile, $level, $size, $frameSize,$saveAndPrint);
}


//生成二维码 有$logo,生成带logo的二维码
/**
 * @param $url 要跳转的url
 * @param $newRelativePath 生成二维码图片保存路径 相对路径
 * @param string $logo 中间logo的图片路径 绝对路径
 * @return array|string
 */
function createLogoQRcode($url,$newRelativePath,$logo=''){
    $QRcode =  new \common\component\code\Qrcode();;
    $uploadPath = realpath( config('upload_dir.upload_path')) . '/';
    if(!is_dir($uploadPath)){
        if(!mk_dir($uploadPath)){
            return errorMsg('创建Uploads目录失败');
        }
    }

    //二维码图片保存路径
    $newPath = $uploadPath . $newRelativePath; //绝对路经
    if(!mk_dir($newPath)){
        return errorMsg('创建新目录失败');
    }
    //生产没有logo二维码图片
    $filename = generateSN(5).'nologo.png';
    $noLogoFile = $newPath.$filename;
    $QRcode->png($url, $noLogoFile);
    if(!empty($logo))
    {
        $filename = generateSN().'withlogo.png';
        $logoFile = $newPath.$filename;
        $QR = imagecreatefromstring(file_get_contents($noLogoFile));
        $logo = imagecreatefromstring(file_get_contents($logo));
        if(imageistruecolor($logo)){
            imagetruecolortopalette($logo,false,65535);//解决颜色失真
        }
        $QR_width = imagesx($QR);//二维码图片宽度
        $QR_height = imagesy($QR);//二维码图片高度
        $logo_width = imagesx($logo);// LOGO图片宽度
        $logo_height = imagesy($logo);// logo图片高度
        $logo_qr_width = $QR_width / 5;
        $scale = $logo_width / $logo_qr_width;
        $logo_qr_height = $logo_height / $scale;
        $from_width = ($QR_width - $logo_qr_width) / 2;
        imagecopyresampled($QR, $logo, $from_width, $from_width, 0, 0, $logo_qr_width, $logo_qr_height, $logo_width, $logo_height);
        unlink($noLogoFile);
        imagepng($QR,$logoFile);
    }
    return $newRelativePath.$filename;
}

/**
 * 生成数据返回值
 */
function ajaxReturn($msg,$status = -1,$data = []){;
    $rs = ['status'=>$status,'msg'=>$msg];
    if(!empty($data))$rs['data'] = $data;
    return $rs;
}

/**从临时目录里移动文件到新的目录
 * @param $newRelativePath 新相对路径
 * @param $filename 文件名
 * @return string 返回相对文件路径
 */
function moveImgFromTemp($newRelativePath,$filename){
    //上传文件公共路径
    $uploadPath = realpath( config('upload_dir.upload_path')) . '/';
    if(!is_dir($uploadPath)){
        if(!mk_dir($uploadPath)){
            return errorMsg('创建Uploads目录失败');
        }
    }
    //临时相对路径
    $tempRelativePath = config('upload_dir.temp_path');
    //旧路径
    $tempPath = $uploadPath . $tempRelativePath;
    if(!is_dir($tempPath)){
        return errorMsg('临时目录不存在！');
    }
    //旧文件
    $tempFile = $tempPath . $filename;

    //新路径
    $newPath = $uploadPath . $newRelativePath;
    if(!mk_dir($newPath)){
        return errorMsg('创建新目录失败！');
    }
    //新文件
    $newFile = $newPath . $filename;
    //重命名文件
    if(file_exists($tempFile)){//临时文件存在则移动
        if(!rename($tempFile,$newFile)){
            return errorMsg('重命名文件失败！');
        }
    }
    return $newRelativePath . $filename;
}

/**从临时目录里移动多文件带描述到新的目录
 * @param $newRelativePath 目标相对路径
 * @param $filename 文件名
 * @return string 返回相对文件名
 */
function moveImgsWithDecFromTemp($newRelativePath,$imgsWithDec){
    $imgs =[];
    $imgsWithDecNew = [];
    $imgsArray = [];
    foreach ($imgsWithDec as $k => $value) {
        if($value){
            //上传文件公共路径
            $uploadPath = realpath( config('upload_dir.upload_path')) . '/';

            //临时相对路径
            $tempRelativePath = config('upload_dir.temp_path');
           
            //旧路径
            $oldPath = $uploadPath . $tempRelativePath;

            if(!file_exists($oldPath)){
                //检查是否有该文件夹，如果没有就创建，并给予最高权限
                if(!mkdir($oldPath, 0700,true)){
                    show(0,'创建目录失败');
                }
            }
            //旧文件
            $oldFile = $oldPath . basename($value['fileSrc']);

            //新路径
            $newPath = $uploadPath . $newRelativePath;

            if(!file_exists($newPath)){
                //检查是否有该文件夹，如果没有就创建，并给予最高权限
                if(!mkdir($newPath, 0700,true)){
                    show(0,'创建目录失败');
                }
            }
            //新文件
            $newFile = $newPath .basename($value['fileSrc']);

            //重命名文件
            if(file_exists($oldFile)){
                if(!rename($oldFile,$newFile)){
                    show(0,'重命名文件失败');
                    //$this->ajaxReturn(errorMsg('重命名文件失败'));
                }
            }
            $imgsArray[] = $newRelativePath.basename($value['fileSrc']);
            $imgsWithDecNew[$k]['fileSrc'] = $newRelativePath.basename($value['fileSrc']);
            $imgsWithDecNew[$k]['fileText'] = $value['fileText'];
        }
    }

    $imgs['imgsWithDecNew'] = json_encode($imgsWithDecNew);
    $imgs['imgsArray'] = $imgsArray;
    return  $imgs ;
}


//新增图片对比数据库，删除不同的图片
function delImgFromPaths($oldImgPaths,$newImgPaths){
    //上传文件公共路径
    $uploadPath = realpath( config('upload_dir.upload_path')) . '/';
    if(!is_dir($uploadPath)){
        return errorMsg('目录：'.$uploadPath.'不存在！');
    }

    if(is_string($oldImgPaths) && is_string($newImgPaths)){
        if($oldImgPaths !== $newImgPaths){
            if(!file_exists($uploadPath . $oldImgPaths)){
                return errorMsg('旧文件不存在！');
            }
            if(!unlink($uploadPath . $oldImgPaths)){
                return errorMsg('删除旧文件失败！');
            }
        }
    }elseif(is_array($oldImgPaths) && is_array($newImgPaths)){
        $delImgPaths = array_diff($oldImgPaths,$newImgPaths);
        foreach ($delImgPaths as $delImgPath) {
            if(!file_exists($uploadPath . $delImgPath)){
                return errorMsg('旧文件不存在！');
            }
            if(!unlink($uploadPath . $delImgPath)){
                return errorMsg('删除旧文件失败！');
            }
        }
    }
}

//删除图片
function delImg($imgPaths){
    //上传文件公共路径
    $uploadPath = realpath(config('upload_dir.upload_path')) . '/';
    if(!is_dir($uploadPath)){
        return (errorMsg('目录：'.$uploadPath.'不存在！'));
    }
    if(is_string($imgPaths)){
        if(!file_exists($uploadPath . $imgPaths)){
            return (errorMsg('旧文件不存在！'));
        }
        if(!unlink($uploadPath . $imgPaths)){
            return (errorMsg('删除旧文件失败！'));
        }
    }elseif(is_array($imgPaths) ){
        foreach ($imgPaths as $delImgPath) {
            if(!file_exists($uploadPath . $delImgPath)){
                return (errorMsg('文件不存在！'));
            }
            if(!unlink($uploadPath . $delImgPath)){
                return (errorMsg('删除文件失败！'));
            }
        }
    }
}

/**
 * 合成商品图片
 *
 * @param array $config 合成图片参数
 * @return $img->path 合成图片的路径
 *
 */
function compose(array $config=[])
{
    $init = [
        'filename'=>'goods',   //保存目录  ./uploads/compose/goods....
        'title'=>'美尚官方旗舰店',
        'type'=>'供应商自营',
        'slogan'=>"采购平台·省了即赚到！",
        'name'=>'产品名称即“品牌名称（brand name）”。',
        'introduce'=>'产品标识所用文字应当为规范中文。',
        'money'=>'￥ 68.56 元',
        'logo'=>'./static/common/img/ucenter_logo.png', // 60*55px
        'brand'=>'http://msy.new.com/uploads/factory_goods/15268896785.jpeg', // 160*55
        'goods'=>'http://msy.new.com/uploads/factory_goods/15268896785.jpeg', // 460*534
        'qrcode'=>'./static/common/img/default/compose/no_pic_40.jpghttps://gss0.bdstatic.com/94o3dSag_xI4khGkpoWK1HF6hhy/baike/c0%3Dbaike80%2C5%2C5%2C80%2C26/sign=fa9140accd95d143ce7bec711299e967/2934349b033b5bb571dc8c5133d3d539b600bc12.jpg', // 120*120
        'font'=>'./static/font/simhei.ttf',   //字体
    ];
    $init = array_merge($init, $config);
    $logo = imgInfo($init['logo']);
    $brand = imgInfo($init['brand']);
    $goods = imgInfo($init['goods']);
    $qrcode = imgInfo($init['qrcode']);
    if(!$logo ||!$brand || !$goods || !$qrcode){
        return '提供的图片有问题';
    }
    $im = imagecreatetruecolor(480, 780);  //图片大小
    $color = imagecolorallocate($im, 240, 255, 255);
    $text_color = imagecolorallocate($im, 0, 0, 0);
    imagefill($im, 0, 0, $color);
    imagettftext($im, 14, 0, 265, 35, $text_color, $init['font'], $init['title']); //XX官方旗舰店
    imagettftext($im, 12, 0, 265, 55, $text_color, $init['font'], $init['type']); //供应商自营
    imagettftext($im, 16, 0, 10,  96, $text_color, $init['font'], $init['slogan']);   //标语
    imagettftext($im, 14, 0, 10, 670, $text_color, $init['font'], $init['name']); //说明
    imagettftext($im, 12, 0, 10, 700, $text_color, $init['font'], $init['introduce']); //规格
    imagettftext($im, 12, 0, 10, 730, $text_color, $init['font'], $init['money']); //金额
    imagecopyresized($im, $logo['obj'], 10, 10, 0, 0, 60, 55, $logo['width'], $logo['height'] );  //平台logo
    imageline($im, 80, 10, 80, 65, $text_color); //划一条实线
    imagecopyresized($im, $brand['obj'], 95, 10, 0, 0, 160, 55, $brand['width'], $brand['height'] );  //店铺logo
    imagecopyresized($im, $goods['obj'], 10, 106, 0, 0, 460, 534, $goods['width'], $goods['height']);  //商品
    imagecopyresized($im, $qrcode['obj'], 350, 650, 0, 0, 120, 120, $qrcode['width'], $qrcode['height'] );  //二维
    $dir = './uploads/compose/'.$init['filename'].'/'.date('Y').'/'.date('m');
    if(!is_dir($dir)){
        mkdir($dir, 0777, true);
    }
    $filename = $dir.'/'.time().mt_rand(1000, 9999).'.jpg';
    if( !imagejpeg($im, $filename, 90) ){
        return '合成图片失败';
    }
    imagedestroy($im);
    return  substr($filename, 1);
}

function imgInfo($path)
{
    $info = getimagesize($path);
    //检测图像合法性
    if (false === $info) {
        return false; //图片不合法
    }
    if($info[2]>3){
        return false; //不支持此图片类型
    }
    $type = image_type_to_extension($info[2], false);
    $fun = "imagecreatefrom{$type}";
    //返回图像信息
    if(!$fun) return false;
    return [
        'width'  => $info[0],
        'height' => $info[1],
        'type'   => $type,
        'mime'   => $info['mime'],
        'obj'    => $fun($path),
    ];
}
//获取终端的ip
function get_client_ip() {
    if(getenv('HTTP_CLIENT_IP') && strcasecmp(getenv('HTTP_CLIENT_IP'), 'unknown')) {
        $ip = getenv('HTTP_CLIENT_IP');
    } elseif(getenv('HTTP_X_FORWARDED_FOR') && strcasecmp(getenv('HTTP_X_FORWARDED_FOR'), 'unknown')) {
        $ip = getenv('HTTP_X_FORWARDED_FOR');
    } elseif(getenv('REMOTE_ADDR') && strcasecmp(getenv('REMOTE_ADDR'), 'unknown')) {
        $ip = getenv('REMOTE_ADDR');
    } elseif(isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], 'unknown')) {
        $ip = $_SERVER['REMOTE_ADDR'];
    }
    return preg_match ( '/[\d\.]{7,15}/', $ip, $matches ) ? $matches [0] : '';
}

//取数组中重复数据
function FetchRepeatMemberInArray($array) {
    // 获取去掉重复数据的数组
    $unique_arr = array_unique ( $array );
    // 获取重复数据的数组
    $repeat_arr = array_diff_assoc ( $array, $unique_arr );
    return  $repeat_arr ? $repeat_arr : false;
}

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

/**设置登录session
 */
function setSession($user){
    $user = array_merge($user,array('rand' => create_random_str(10, 0),));
    session('user',$user);
    session('user_sign',data_auth_sign($user));
    //返回发起页或平台首页
    //$jumpUrl = session('backUrl')?:session('returnUrl');
    return session('backUrl');

    $pattern  =  '/index.php\/([A-Z][a-z]*)\//';
    preg_match ($pattern,$jumpUrl,$matches);
    return $jumpUrl?:url('index/Index/index');
}

//传递数据以易于阅读的样式格式化后输出
function p($data){
    // 定义样式
    $str='<pre style="display: block;padding: 9.5px;margin: 44px 0 0 0;font-size: 13px;line-height: 1.42857;color: #333;word-break: break-all;word-wrap: break-word;background-color: #F5F5F5;border: 1px solid #CCC;border-radius: 4px;">';
    // 如果是boolean或者null直接显示文字；否则print
    if (is_bool($data)) {
        $show_data=$data ? 'true' : 'false';
    }elseif (is_null($data)) {
        $show_data='null';
    }else{
        $show_data=print_r($data,true);
    }
    $str.=$show_data;
    $str.='</pre>';
    echo $str;
}



