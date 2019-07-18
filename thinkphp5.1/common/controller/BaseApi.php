<?php
namespace common\controller;
use \common\component\image\Image;
use common\component\jwt\JWT;
use think\facade\Session;
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
    //返回图片临时相对路径
    public function uploadFileToTemp(){
        $postData = input('post.');
        $postData = $postData['data'];
        $savePath = isset($_POST['uploadpath']) ? $_POST['uploadpath'] : config('upload_dir.temp_path');
        if(is_string($postData['fileBase64'])){
            if(strpos($postData['fileBase64'],'data:image') !==false || strpos($postData['fileBase64'],'data:video') !== false){
                $result =  $this ->uploadSingleFileToTemp($postData['fileBase64'],$savePath);
                if(isset($result['code'])&& $result['code'] == 0){
                    return $result['msg'];
                }
                return buildSuccess($result['data']);
            }
        }
        if(is_array($postData['fileBase64'])){
            $filesNew = [];
            foreach ($postData['fileBase64'] as $k=>$file){
                //判断是否为base64编码图片
                if(strpos($file,'data:image') !==false || strpos($file,'data:video') !== false){
                    $result =  $this ->uploadSingleFileToTemp($file['fileSrc'],$savePath);
                    if(isset($result['code'])&& $result['code'] == 0){
                        return $result['msg'];
                    }
                    $filesNew[] = $result['data'][0];
                }else{
                    $filesNew[] = $file;
                }
            }
            return buildSuccess($filesNew);
        }
    }
    //返回图片临时相对路,上传多张图片带描述
    public function uploadMultiFileToTempWithDes(){
        $savePath = isset($_POST['uploadpath']) ? $_POST['uploadpath'] : config('upload_dir.temp_path');
        $files = $_POST['imgsWithDes'];
        $filesNew = [];
        foreach ($files as $k=>$file){
            //判断是否为base64编码图片
            if(strpos($file['fileSrc'],'data:image') !==false || strpos($file['fileSrc'],'data:video') !== false){
                $result =  $this ->uploadSingleFileToTemp($file['fileSrc'],$savePath);
                if(isset($result['code'])&& $result['code'] == 0){
                    return $result['msg'];
                }
                $filesNew[$k]['fileSrc'] = $result['data'];
                $filesNew[$k]['fileText'] = $file['fileText'];
            }else{
                $filesNew[$k] = $file;
            }
        }
        return json_encode($filesNew);
    }

    //上传单个data64位文件
    /**
     * @param $fileBase64 上传文件的Base64字符源
     * @param $savePath 保存路径
     * @return array|string
     */
    public function uploadSingleFileToTemp($fileBase64,$savePath){
        // 获取图片
        list($type, $data) = explode(',', $fileBase64);
        // 判断文件类型
        list($fileType,$ext) = explode('/', $type);
        $array = [
            'data:image/jpg;base64',
            'data:image/gif;base64',
            'data:image/png;base64',
            'data:image/jpeg;base64',
            'data:video/mp4;base64',
            'data:video/rm;base64',
            'data:video/mtv;base64',
            'data:video/wmv;base64',
            'data:video/avi;base64',
            'data:video/3gp;base64',
            'data:video/flv;base64',
            'data:video/rmvb;base64',
        ];
        if(in_array($type,$array)){
            $ext = explode(';', $ext);
            $ext = '.'.$ext[0];
        }

        if($fileType == 'data:image'){
            if(!getimagesize($fileBase64)){
                return buildFailed('不是图片文件');
                return $this->errorMsg();
            }
        }

        if(!$ext){
            return buildFailed('不支持此文件格式');
        }
        //文件大小 单位M
        $fileSize = strlen($data)/1024/1024;
        //图片限制大小
        if($fileType == 'data:image'){
            if($fileSize >3){//大于2M
                return buildFailed('请上传小于2M的图片');
            }
        }
        //视频限制大小
        if($fileType == 'data:video'){
            if($fileSize > 10){//大于10
                return buildFailed('请上传小于10M的视频');

            }
        }
        //上传公共路径
        $uploadPath = config('upload_dir.upload_path');
        if(!is_dir($uploadPath)){
            if(!mk_dir($uploadPath)){
                return buildFailed('创建Uploads目录失败');
            }
        }
//        $uploadPath = realpath($uploadPath);
        if($uploadPath === false){
            return buildFailed('获取Uploads实际路径失败');
        }
        $uploadPath = $uploadPath . '/' ;
        //临时相对路径
        $tempRelativePath = $savePath;

        //存储路径
        $storePath = $uploadPath . $tempRelativePath;
        if(!mk_dir($storePath)){
            return buildFailed('创建临时目录失败');
        }
        //文件名
        $fileName = generateSN(5) . $ext;
        //带存储路径的文件名
        $photo = $storePath . $fileName;
        // 生成文件
        $returnData = file_put_contents($photo, base64_decode($data), true);
        if(false === $returnData){
            return buildFailed('保存文件失败');
        }
        //压缩文件
        if( isset($_POST['imgWidth']) || isset($_POST['imgHeight']) ){
            $imgWidth = isset($_POST['imgWidth']) ? intval($_POST['imgWidth']) : 150;
            $imgHeight = isset($_POST['imgHeight']) ? intval($_POST['imgHeight']) : 150;
            $image = Image::open($photo);
            $image->thumb($imgWidth, $imgHeight,Image::THUMB_SCALING)->save($photo);
        }
        $data = [
            $tempRelativePath . $fileName
        ];
        return buildSuccess($data);
    }



}