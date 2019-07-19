<?php
namespace app\ucenter\controller;

class Upload extends \common\controller\BaseApi{
    //返回图片临时相对路径
    public function uploadFileToTemp(){
        $postData = input('post.');
        $postData = $postData['data'];
        $savePath = isset($_POST['uploadpath']) ? $_POST['uploadpath'] : config('upload_dir.temp_path');
        if(is_array($postData['fileBase64'])){
            $filesNew = [];
            foreach ($postData['fileBase64'] as $k=>$file){
                //判断是否为base64编码图片
                if(strpos($file,'data:image') !==false || strpos($file,'data:video') !== false){
                    $result =  json_decode($this ->_uploadSingleFileToTemp($file,$savePath),true);
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
        $postData = input('post.');
        $postData = $postData['data'];
        $filesNew = [];
        foreach ($postData["fileBase64"] as $k=>$file){
            //判断是否为base64编码图片
            if(strpos($file['fileSrc'],'data:image') !==false || strpos($file['fileSrc'],'data:video') !== false){
                $result =  json_decode($this ->_uploadSingleFileToTemp($file['fileSrc'],$savePath),true);
                if(isset($result['code'])&& $result['code'] == 0){
                    return $result['msg'];
                }
                $filesNew[$k]['fileSrc'] = $result['data'][0];
                $filesNew[$k]['fileText'] = $file['fileText'];
            }else{
                $filesNew[$k] = $file;
            }
        }
        return buildSuccess($filesNew);
        return json_encode($filesNew);
    }

}