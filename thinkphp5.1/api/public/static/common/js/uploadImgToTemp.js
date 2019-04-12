
var uploadsSingleImgFlag = true; //上传单图片成功标识位
var uploadsSingleVideoFlag = true;//上传单视频成功标识位
$(function(){
    // 选择单图片
    $('body').on('change','.uploadSingleImg',function () {
        var _this=$(this);
        uploadsSingleImgFlag = false;
        var img = event.target.files[0];
        var uploadfileSize=img.size/1024/1024;
        var obj=$(this).parent();
        // 判断是否图片
        if(!img){
            return false;
        }
        // 判断图片格式
        var imgRegExp=/\.(?:jpg|jpeg|png|gif)$/;
        if(!(img.type.indexOf('image')==0 && img.type && imgRegExp.test(img.name)) ){
            dialog.error('请上传：jpg、jpeg、png、gif格式图片');
            return false;
        }
        if(uploadfileSize>1){
            dialog.error('图片大小不能超过1M');
            return false;
        }
        var reader = new FileReader();
        reader.readAsDataURL(img);
        reader.onload = function(e){
            var imgUrl=e.target.result;
            $(obj).addClass('active');
            var postData = {fileBase64: e.target.result};
            var uploadpath=$(obj).find('.uploadSingleImg').data('upload_path');
            postData.uploadpath=uploadpath;
            console.log(uploadpath);
            // postData.imgWidth = 145;
            // postData.imgHeight = 100;
            $(obj).find('img').attr('src',imgUrl);
            var type = _this.data('type');
            if(type == 'notupload'){
                $(obj).find('.img').val(imgUrl);
                console.log(1);
                return false;
            }
            //提交
            $.post(controller+"uploadFileToTemp",postData,function(msg){
                if(msg.status == 1){
                    uploadsSingleImgFlag = true;
                    $(obj).find('.img').val(msg.info);
                }else{
                    dialog.error(msg.info)
                }
            })
        }
    });

    // 选择单视频
    $('body').on('change','.uploadSingleVideo',function () {
        _this = $(this);
        uploadsSingleVideoFlag = false;
        var video = event.target.files[0];
        var obj=$(this).parent();
        // 判断是否图片
        if(!video){
            return false;
        }
        // 判断图片格式
        var imgRegExp=/\.(?:mp4|rmvb|avi|ts)$/;
        if(!(video.type.indexOf('video')==0 && video.type && imgRegExp.test(video.name)) ){
            // dialog.error('请上传：mp4、rmvb、avi、ts格式图片');
            return false;
        }
        var reader = new FileReader();
        reader.readAsDataURL(video);
        reader.onload = function(e){
            var postData = {fileBase64: e.target.result};
            var videoUrl=e.target.result;
            $(obj).find('video').attr('src',videoUrl);
            //提交
            var type = _this.data('type');
            if(type == 'notupload'){
                $(obj).find('.img').val(videoUrl);
                return false;
            }
            $.post(controller+"uploadFileToTemp",postData,function(msg){
                if(msg.status == 1){
                    uploadsSingleVideoFlag = true;
                    $(obj).find('.img').val(msg.info);
                }else{
                    dialog.error(msg.info)
                }
            })

        }
    })
  
});
