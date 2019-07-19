<?php
namespace app\ucenter\controller;

class Upload extends \common\controller\BaseApi{
    //返回图片临时相对路径
    public function uploadFileToTemp(){
        $postData = input('post.');
        $postData = $postData['data'];
        $savePath = isset($_POST['uploadpath']) ? $_POST['uploadpath'] : config('upload_dir.temp_path');
        if(is_string($postData['fileBase64'])){
            if(strpos($postData['fileBase64'],'data:image') !==false || strpos($postData['fileBase64'],'data:video') !== false){
                $result =  json_decode($this ->_uploadSingleFileToTemp($postData['fileBase64'],$savePath),true);
                if(isset($result['code'])&& $result['code'] == -1){
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
                    $result =  $this ->_uploadSingleFileToTemp($file,$savePath);
                    if(isset($result['code'])&& $result['code'] == 0){
                        return $result['msg'];
                    }
                    $filesNew[] = $result['data'];
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
                $result =  $this ->_uploadSingleFileToTemp($file['fileSrc'],$savePath);
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

}