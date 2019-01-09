ws = new WebSocket("ws://msy.meishangyun.com:8282");
ws.onopen = function(e){
    console.log('open');
};
// 服务端主动推送消息时会触发这里的onmessage
ws.onmessage = function(e){
    var data =  JSON.parse(e.data);
    var type = data.type || '';
    switch(type){
        case 'init':
            // Events.php中返回的init类型的消息，将client_id发给后台进行uid绑定
            // 利用jquery发起ajax请求，将client_id发给后端进行uid绑定
            var postData = {client_id: data.client_id};
            var url = domain + 'index/CustomerService/bindUid';
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
                success: function(msg){
                    $('.loading').hide();
                    if(msg.status==0){
                        dialog.error(msg.info);
                    }else if(msg.code==1 && msg.data=='no_login'){
                        loginDialog();
                    }
                }
            });
            break;
        case 'msg':
            on_message_call_back(data);
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