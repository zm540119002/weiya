var dialog = {
    // 错误弹出层
    error: function(message) {
        layer.open({
            content: message?message:'出错啦！'
            ,icon: 2
            ,skin: 'msg'
            ,time: 2000 //2秒后自动关闭
        });
    },
    //成功弹出层
    success : function(message,url) {
        layer.open({
            content : message?message:'成功',
            time : 3000,
            skin: 'msg',
            end : function(){
                if(url){
                    location.href=url;
                }
            }
        });
    },
    //确认框
    confirm:function(message,url){
        layer.open({
            content : message?message:'成功',
            btn:['确定','取消'],
            end : function(){
              
            },
            yes:function(index){
                if(url){
                    if($.isFunction(url)){
                        url();
                    }else{
                        location.href=url;
                    }
                }
                layer.close(index)
            }
        })
    },
    //消息框
    msg:function (message,option,callback) {
        var _option ={};
        if(message.status==0){
            _option ={icon: 2,time: 3000};
        }else if(message.status==1){
            _option ={icon: 1,time: 1000};
        }
        $.extend(_option,option);
        layer.msg(message.info,_option,callback);
    }
};