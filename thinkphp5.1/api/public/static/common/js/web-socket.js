ws = new WebSocket("wss://www.worldview.com.cn:8282");
ws.onopen = function(e){
    console.log('open');
};
var init_client_id = 0;
// 服务端主动推送消息时会触发这里的onmessage
ws.onmessage = function(e){
    var data =  JSON.parse(e.data);
    var type = data.type || '';
    console.log(data);
    switch(type){
        case 'init':
            init_client_id = data.client_id;
            if(typeof on_init_call_back === "function"){
                on_init_call_back(data);
            }
            break;
        case 'msg':
            if(typeof on_init_call_back === "function"){
                on_msg_call_back(data);
            }
            break;
        default :
            console.log('default');
            break;
    }
};
ws.onerror = function (e) {
    console.log('error');
};
ws.onclose = function(e){
    console.log('close');
};
//获取列表
function getList(config) {
    $.ajax({
        url: config.url,
        data: config.postData?config.postData:{},
        type: 'post',
        beforeSend: function(xhr){
            $('.loading').show();
        },
        error:function(xhr){
            $('.loading').hide();
            dialog.error('AJAX错误');
        },
        success: function(data){
            $('.loading').hide();
            if(config.callBack){
                config.callBack(config,data);
            }else{
                getListDefaultCallBack(config,data)
            }
        }
    });
}
//获取列表-默认回调函数
function getListDefaultCallBack(config,data) {
    if(data.status==0){
        dialog.error(data.info);
    }else{
        var container = config.container?config.container:$('ul.list');
        container.empty().append(data);
    }
}
//设置消息已读
function setMessageRead(obj,postData){
    if(!postData.messageIds.length || !postData.from_id){
        return false;
    }
    var url = domain + 'index/CustomerService/setMessageRead';
    $.ajax({
        url: url,
        data: postData,
        type: 'post',
        beforeSend: function(xhr){
            $('.loading').show();
        },
        error:function(xhr){
            $('.loading').hide();
            dialog.error('AJAX错误');
        },
        success: function(data){
            $('.loading').hide();
            if(data.status==0){
                dialog.error(data.info);
            }else{
                obj.find('span.news_num').text('');
            }
        }
    });
}