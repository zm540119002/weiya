    //多图片上传
    var fileList;
    var num;
    $('body').on('change','#file',function(){
        var file = $(this);
        fileList = $(this).get(0).files;
        num=file.data('num');//限制个数
        if(num==0){ //0代表无限制个数
            uploadPic(fileList[0],0,fileList.length);
        }else{
            uploadPic(fileList[0],0,num);
        }      
    });
    $('body').on('change','#video',function(){
        var file = $(this);
        var fileList = $(this).get(0).files;
        var imgContainer = $('.multi-picture-module');
        for (var i = 0; i < fileList.length; i++) {
            var video = event.target.files[i];
            var obj=$(this).parent();
            // 判断是否图片
            if(!video){
                return false;
            }
            // 判断图片格式
            var imgRegExp=/\.(?:mp4|rmvb|avi|ts)$/;
            if(!(video.type.indexOf('video')==0 && video.type && imgRegExp.test(video.name)) ){
                errorTipc('请上传：mp4、rmvb、avi、ts格式视频');
                return false;
            }
            var reader = new FileReader();
            reader.readAsDataURL(video);
            reader.onload = function(e){
                var videoUrl=e.target.result;
                var html=$('#img_list').html();
                var video=  $('<video src="" class="upload_img" accept="video/*" autoplay="autoplay"></video>');
                video.attr("src", videoUrl);
                var videoAdd = $('<li><div class="picture-module active"><input type="file" class="uploadImg uploadSingleVideo" name=""><span class="delete-picture">X</span></div></li>');
                videoAdd.find('.picture-module').append(video);
                imgContainer.append(videoAdd);             
            }
        }
    });
    //上传视频
    var goodsVideoList=$('#goodsVideoList').html();
    $('body').on('click','.uploadGoodsVideo',function(){
        uploadsMultiVideo(goodsVideoList);
    });
     //上传单图片和描述
    $('body').on('change','.uploadImgDescribe',function () {
        var file = $(this);
        fileList = $(this).get(0).files;
        num=file.data('num');//限制个数
        uploadPicDescribe(fileList[0],0,fileList.length);
    });   
    //上传单视频和描述
    $('body').on('change','.uploadVideoDescribe',function () {
        var img = event.target.files[0];
        var obj=$(this).parent();
        var imgContainer = $('.multi-picture-module');
        var reader = new FileReader();
        reader.readAsDataURL(img);
        reader.onload = function(e){
            var videoUrl=e.target.result;
            var video=  $('<video src="" class="upload_img" autoplay="autoplay"></video>');
            video.attr("src", videoUrl);
            var videoAdd = $('<li><a href="javascript:void(0);" class="edit-describe">编辑视频描述</a><textarea name="" cols="30" rows="5" placeholder="请填写描述" class="edit-text"></textarea><div class="picture-module active"><input type="file" class="uploadImg uploadSingleVideo" name=""><span class="delete-picture">X</span></div></li>');
            videoAdd.find('.picture-module').append(video);
            imgContainer.append(videoAdd);
        }
    });
    //编辑商品详情
    var editDetail=$('#editDetail').html();
    $('body').on('click','.editDetail',function(){
        var _this=$(this);
        var storageDataObj=_this.siblings('input[type="hidden"]');
        var num=_this.siblings('input[type="hidden"]').data('picture-num');
        uploadsMultiImg(editDetail,storageDataObj,num,'编辑商品详情');
    }); 
    //删除
    $('body').on('click','.delete-picture',function(){
        $(this).parents('li').remove();
        $('.company-video').data('src','');
    });
    // 修改单个图片
    $('body').on('change','.uploadSingleEditImg',function () {
        var img = event.target.files[0];
        var obj=$(this).parent();
        // 判断是否图片
        if(!img){
            return false;
        }
        // 判断图片格式
        var imgRegExp=/\.(?:jpg|jpeg|png|gif)$/;
        if(!(img.type.indexOf('image')==0 && img.type && imgRegExp.test(img.name)) ){
            errorTipc('请上传：jpg、jpeg、png、gif格式图片');
        }
        var reader = new FileReader();
        reader.readAsDataURL(img);
        reader.onload = function(e){
            var imgUrl=e.target.result;
            $(obj).addClass('active');
            var postData = {fileBase64: e.target.result};
            //提交
            $.post(controller + "uploadImgToTemp",postData,function(msg){
                if(msg.status == 1){
                    $(obj).find('.img').val(msg.info);
                    $(obj).find('img').attr('src','/uploads/'+msg.info);
                }else{
                    dialog.error(msg.info);
                }
            });
            $(obj).find('img').attr('src',imgUrl);
            $(obj).find('.img').val(imgUrl);
        }
    });
    //上传企业图片
    var uploadCompanyPic=$('#uploadCompanyPic').html();
    $('body').on('click','.uploadCompanyPic',function(){
        var _this=$(this);
        var storageDataObj = _this.next('input[type="hidden"]');
        uploadsImgDescribe(uploadCompanyPic,storageDataObj);
    });
    //上传企业视频
    var companyVideoList=$('#companyVideoList').html();
    $('body').on('click','.companyVideoList',function(){
        var _this=$(this);
        var storageDataObj=_this.next('input[type="hidden"]');
        uploadsVideoDescribe(companyVideoList,storageDataObj);
    }); 
    //编辑描述
    $('body').on('click','.edit-describe',function () {
        var _this=$(this);
        _this.next('.edit-text').toggleClass('active');
    });

//上传图片描述
function uploadPicDescribe(fil,i,len){
    
    var img = fil;
    var obj=$(this).parent();
    var fileSize=fil.size/1024/1024;
    // 判断是否图片
    if(!img){
        return false;
    }
    // 判断图片格式
    var imgRegExp=/\.(?:jpg|jpeg|png|gif)$/;
    if(!(img.type.indexOf('image')==0 && img.type && imgRegExp.test(img.name)) ){
        errorTipc('请上传：jpg、jpeg、png、gif格式图片');
    }
    var reader = new FileReader();
    reader.readAsDataURL(img);
    reader.onload = function(e){
        var imgUrl=e.target.result;
        var img=  $('<img src="" class="upload_img">');
        img.attr("src", imgUrl);
        var imgAdd = $('<li><a href="javascript:void(0);" class="edit-describe">编辑照片描述</a><textarea name="" cols="30" rows="5" placeholder="请填写描述" class="edit-text"></textarea><div class="picture-module active"><input type="file" class="uploadImg uploadSingleEditImg" name=""><a class="delete-picture">X</a></div></li>');
        imgAdd.find('.picture-module').append(img);
        $('.multi-picture-moduleDes').append(imgAdd);
        if(i<len-1){
            if(fileList[i+1]){
                uploadPicDescribe(fileList[i+1],i+1,len);
            }
        }
    }
}

//上传图片
function uploadPic(fil,i,len){
    var img = fil;
    var obj=$(this).parent();
    var fileSize=fil.size/1024/1024;
    // 判断是否图片
    if(!img){
        return false;
    }
    // 判断图片格式
    var imgRegExp=/\.(?:jpg|jpeg|png|gif)$/;
    if(!(img.type.indexOf('image')==0 && img.type && imgRegExp.test(img.name)) ){
        errorTipc('请上传：jpg、jpeg、png、gif格式图片');

    }
    if(fileSize>1){
        errorTipc('图片大小不能超过1M');
        return false;
    }
    if($('.editDetailLayer li').length>=num && num){
        errorTipc('只能上传'+num+'张图片');
        return false;
    }
    var reader = new FileReader();
    reader.readAsDataURL(img);
    reader.onload = function(e){
        var imgUrl=e.target.result;
        var img=  $('<img src="" class="upload_img">');
        img.attr("src", imgUrl);
        var imgAdd = $('<li><div class="picture-module active"><input type="file" class="uploadImg uploadSingleEditImg" name=""><span class="delete-picture">X</span></div></li>');
        imgAdd.find('.picture-module').append(img);
        $('.multi-picture-module').append(imgAdd);
        if(i<len-1){
            if(fileList[i+1]){
                uploadPic(fileList[i+1],i+1,len);
            }
        }
    }
}

//多图片弹窗
function uploadsMultiImg(content,obj,limitNum,title){
    layer.open({
        title:[title,'border-bottom:1px solid #d9d9d9'],
        className:'editDetailLayer',
        type:1,
        content:content,
        btn:['确定','取消'],
        success:function(){
            var winHeight=$(window).height();
            $('.editDetailLayer .layui-m-layercont').css('height',winHeight-120+'px');
            var html=$('#img_list').html();
            var multiImgSrc=obj.data('src');
            var multiImgAttr=multiImgSrc.split(',');
                $('.editDetailLayer .uploadMultiImg').data('num',limitNum);
            for(var i=0;i<multiImgAttr.length-1;i++){
                if(multiImgAttr[i].indexOf("uploads") == -1 && multiImgAttr[i] !=''){
                    multiImgAttr[i] = uploads+multiImgAttr[i];
                }
                $('.editDetailLayer .multi-picture-module').append(html);
                $('.editDetailLayer .upload_img').eq(i).attr('src',multiImgAttr[i]);
            }
        },
        yes:function(index){
            var layerMultiImgAttr=[];
            $.each($('.editDetailLayer li'),function(i,val){
                var _this=$(this);
                var imgSrc=_this.find('img').attr('src');
                layerMultiImgAttr.push(imgSrc);
            });
            if(layerMultiImgAttr.length==0){
                obj.data('src','');
                layer.close(index);
                return false;
            }
            var postData = {};
            postData.fileBase64 = layerMultiImgAttr;
             $.ajax({
                url: controller + 'uploadFileToTemp',
                data: postData,
                type: 'post',
                beforeSend: function(){
                    errorTipc('文件还没上传完毕');
                },
                success:function(info){
                    if(info.status == 0){
                        dialog.error(info.info);
                        return false;
                    }
                    var imgArray ='';
                    $.each(info.info,function(index,img){
                        if(img.indexOf("uploads") == -1 && img !=''){
                            img = uploads+img;
                        }
                        imgArray+=img+',';
                    });

                    obj.data('src',imgArray);
                    layer.close(index);
                }
             })
        },
        error:function (xhr) {
            dialog.error('AJAX错误'+xhr);
        },
        no:function(){
            $('.editDetailLayer li').remove();
        }
    });
}

//多视频弹窗
function uploadsMultiVideo(content){
    layer.open({
        title:['上传商品视频','border-bottom:1px solid #d9d9d9'],
        className:'editVideoLayer',
        content:content,
        btn:['确定','取消'],
        success:function(){
            var html=$('#video_list').html();
            var multiVideoSrc=$('.goods-video').data('src');
            if(multiVideoSrc){
                var multiVideoAttr=multiVideoSrc.split(',');
                for(var i=0;i<multiVideoAttr.length-1;i++){
                    if(multiVideoAttr[i].indexOf("uploads") == -1 && multiVideoAttr[i] !=''){
                        multiVideoAttr[i] = uploads+multiVideoAttr[i];
                    }
                    $('.editVideoLayer .multi-picture-module').append(html);
                    $('.editVideoLayer video').eq(i).attr('src',multiVideoAttr[i]);
                }
            }
        },
        yes:function(index){
            var layermultiVideoAttr=[];
            $.each($('.editVideoLayer li'),function(i,val){
                var _this=$(this);
                var videoSrc=_this.find('video').attr('src');
                layermultiVideoAttr.push(videoSrc);
            });

            if(layermultiVideoAttr.length==0){
                $('.goods-video').data('src','');
                layer.close(index);
                return false;
            }
            var postData = {};
            postData.fileBase64 = layermultiVideoAttr;
            $.ajax({
                url: controller + 'uploadFileToTemp',
                data: postData,
                type: 'post',
                beforeSend: function(){
                    //$('.loading').show();
                },
                success: function(info){
                    if(info.status == 0){
                        dialog.error(info.msg);
                        return false;
                    }
                    var videoArray = '';
                    $.each(info.info,function(index,img){
                        if(img.indexOf("uploads") == -1 && img !=''){
                            img = uploads+img;
                        }
                        videoArray+=img+',';
                    });
                        $('.goods-video').data('src',videoArray);
                        layer.close(index);
                },
                complete:function(){

                },
                error:function (xhr) {
                    dialog.error('AJAX错误'+xhr);
                }
            });
        }
    });
}
//图片描述弹窗
function uploadsImgDescribe(content,obj){
    layer.open({
        title:['上传照片和描述','border-bottom:1px solid #d9d9d9'],
        className:'editCompanyPicLayer',
        content:content,
        type:1,
        btn:['确定','取消'],
        success:function(){
            var winHeight=$(window).height();
            document.documentElement.scrollTop = document.body.scrollTop = 0;
            $('html,body').addClass('scrollStyle');
            $('.editCompanyPicLayer .layui-m-layercont').css('height',winHeight-112+'px');
            $('.layui-m-layer .layui-m-layermain').addClass('arrow-bottom');
            $('.layui-m-layermain .layui-m-layersection').addClass('bottom-layer');
            var html='';
                html+='<li>';
                html+='<a href="javascript:void(0);" class="edit-describe">编辑照片描述</a>';
                html+='<textarea name="" cols="30" rows="5" placeholder="请填写描述" class="edit-text"></textarea>';
                html+='<div class="picture-module active">';
                html+='<input type="file" class="uploadImg uploadSingleEditImg" name="">';
                html+='<a href="javascript:void(0);" class="delete-picture">X</a>';
                html+='<img src="" class="upload_img">';
                html+='</div>';
                html+='</li>';                  
            var multiImgAttr=obj.data('src');
            for(var i=0;i<multiImgAttr.length;i++){
                if(multiImgAttr[i].fileSrc.indexOf("uploads") == -1 && multiImgAttr[i].fileSrc !=''){
                    multiImgAttr[i].fileSrc = uploads+multiImgAttr[i].fileSrc;
                }
                $('.editCompanyPicLayer .multi-picture-moduleDes').append(html);
                $('.editCompanyPicLayer .upload_img').eq(i).attr('src',multiImgAttr[i].fileSrc);
                $('.editCompanyPicLayer .edit-text').eq(i).val(multiImgAttr[i].fileText);
            }
        },
        yes:function(index){
            var layerMultiImgAttr=[];
            var layerImgInfoData={};
            $.each($('.editCompanyPicLayer li'),function(i,val){
                var _this=$(this);
                var fileSrc=_this.find('img').attr('src');
                var fileText=_this.find('textarea').val();
                layerImgInfoData={
                    fileSrc:fileSrc,
                    fileText:fileText
                };
                layerMultiImgAttr.push(layerImgInfoData);
            });
            console.log(layerMultiImgAttr);
            obj.data('src',layerMultiImgAttr);
            if(layerMultiImgAttr.length==0){
                $('html,body').removeClass('scrollStyle');
                layer.close(index);
                return false;
            }
            var postData = {};
            postData.imgsWithDes = layerMultiImgAttr;
            $('.editCompanyPicLayer .layui-m-layerbtn span[yes]').addClass('disabled');            
            $.ajax({
                url: controller + 'uploadMultiFileToTempWithDes',
                data: postData,
                type: 'post',
                beforeSend: function(){
                    dialog.error('文件还没上传完毕');
                },
                success: function(info){
                    if(info.status == 0){
                        dialog.error(info.msg);
                        return false;
                    }
                    var imgArray = [];
                    var returnData=JSON.parse(info);
                    for(var i=0;i<returnData.length;i++){
                        if(returnData[i].fileSrc.indexOf("uploads") == -1 && returnData[i]!=''){
                            returnData[i].fileSrc= uploads+returnData[i].fileSrc;
                        }
                        imgArray.push(returnData[i]);
                    }
                    obj.data('src', imgArray);
                    if(info != ''){
                        dialog.error('图片文件上传完');
                        layer.close(index);
                    }else{
                        
                    }
                    $('html,body').removeClass('scrollStyle');
                    $('.editCompanyPicLayer .layui-m-layerbtn span[yes]').removeClass('disabled');
                },
                complete:function(){
                    
                },
                error:function (xhr) {
                    dialog.error('AJAX错误'+xhr);
                }
            });
        },
        no:function(){
            $('html,body').removeClass('scrollStyle');
            $('.editCompanyPicLayer li').remove();
        }
    });
}
//视频描述弹窗
function uploadsVideoDescribe(content,obj){
    layer.open({
        title:['上传企业视频','border-bottom:1px solid #d9d9d9'],
        className:'editCompanyPicLayer',
        content:content,
        type:1,
        btn:['确定','取消'],
        success:function(){
            var winHeight=$(window).height();
            $('html,body').addClass('scrollStyle');
            $('.editCompanyPicLayer .layui-m-layercont').css('height',winHeight-112+'px');
            $('.layui-m-layer .layui-m-layermain').addClass('arrow-bottom');
            $('.layui-m-layermain .layui-m-layersection').addClass('bottom-layer');
            var html='';
                html+='<li>';
                html+='<div class="picture-module active">';
                html+='<input type="file" class="uploadImg uploadSingleVideo" name="">';
                html+='<a href="javascript:void(0);" class="delete-picture">X</a>';
                html+='<video src="" class="upload_img"></video>';
                html+='</div>';
                html+='<a href="javascript:void(0);" class="edit-describe">编辑照片描述</a>';
                html+='<textarea name="" cols="30" rows="5" placeholder="请填写描述" class="edit-text"></textarea>';
                html+='</li>';
            var multiImgAttr=obj.data('src');
            for(var i=0;i<multiImgAttr.length;i++){
                if(multiImgAttr[i].fileSrc.indexOf("uploads") == -1 && multiImgAttr[i].fileSrc !=''){
                    multiImgAttr[i].fileSrc = uploads+multiImgAttr[i].fileSrc;
                }
                $('.editCompanyPicLayer .multi-picture-module').append(html);
                $('.editCompanyPicLayer .upload_img').eq(i).attr('src',multiImgAttr[i].fileSrc);
                $('.editCompanyPicLayer .edit-text').eq(i).val(multiImgAttr[i].fileText);
            }
        },
        yes:function(index){
            var layerMultiImgAttr=[];
            var layerImgInfoData={};
            $.each($('.editCompanyPicLayer li'),function(i,val){
                var _this=$(this);
                var fileSrc=_this.find('video').attr('src');
                var fileText=_this.find('textarea').val();
                layerImgInfoData={
                    fileSrc:fileSrc,
                    fileText:fileText
                };
                layerMultiImgAttr.push(layerImgInfoData);
            });
            if(layerMultiImgAttr==false){
                $('html,body').removeClass('scrollStyle');
                layer.close(index);
                return false;
            }
            obj.data('src',layerMultiImgAttr);
            var postData = {};
            postData.imgsWithDes = layerMultiImgAttr;
            $('.editCompanyPicLayer .layui-m-layerbtn span[yes]').addClass('disabled');
            $.ajax({
                url: controller + 'uploadMultiFileToTempWithDes',
                data: postData,
                type: 'post',
                beforeSend: function(){
                    errorTipc('文件还没上传完毕');
                },
                success: function(info){
                    if(info.status == 0){
                        dialog.error(info.msg);
                        return false;
                    }
                    var imgArray = [];
                    var returnData=JSON.parse(info);
                    for(var i=0;i<returnData.length;i++){
                        if(returnData[i].fileSrc.indexOf("uploads") == -1 && returnData[i]!=''){
                            returnData[i].fileSrc= uploads+returnData[i].fileSrc;
                        }
                        imgArray.push(returnData[i]);
                    }
                    obj.data('src', imgArray);
                    if(info != ''){
                        dialog.error('视频文件上传完！');
                        layer.close(index);
                    }
                    $('html,body').removeClass('scrollStyle');
                    $('.editCompanyPicLayer .layui-m-layerbtn span[yes]').removeClass('disabled');
                },
                complete:function(){
                    $('html,body').removeClass('scrollStyle');
                },
                error:function (xhr) {
                    dialog.error('AJAX错误'+xhr);
                }
            });
        },
        no:function(){
            $('html,body').removeClass('scrollStyle');
            $('.editCompanyPicLayer li').remove();
        }
    });
}

