//钱包支付弹窗
function walletPayDialog(fn_name,data) {
    var content=$('#walletPay').html();
    window.scrollTo(0,0);
    layer.open({
        className:'payPasswordLayer',
        type:1,
        shadeClose:false,
        content:content,
        title:['钱包支付密码','border-bottom:1px solid #d9d9d9;'],
        btn:['确定支付'],
        success:function(indexs,i){
            $('#fn_name').val(fn_name);
            $('#order_sn').val(data.order_sn);
            //钱包密码
            var oLis=$('.payPasswordLayer input.password_item');
            for(var i = 0;i<oLis.length;i++){
                var obj=oLis[i];
                $(obj).data('index',i);
                $(obj).attr('readonly',true);
                $(obj).on('keyup',function(){
                    var _this=$(this);
                    _this.val().replace(/^(.).*$/,'$1');
                    _this.attr('readonly',true);
                    var next=_this.data('index')+1;
                    if(next>oLis.length-1){
                        console.log('12345678');
                    }
                    $(oLis[next]).removeAttr('readonly');
                    $(oLis[next]).focus();
                });
            }
            $(oLis[0]).removeAttr('readonly');
        },
        yes:function(index){
            var oLis=$('.payPasswordLayer input.password_item');
            var password='';
            for(var i=0;i<oLis.length;i++){
                password=password+$(oLis[i]).val();
            }
            if(password.length<4){
                dialog.error('请输入正确4位数密码');
                return false;
            }
            var postData = data;
;           postData.fn_name = fn_name;
;           postData.password = password;
            var url = module+'Wallet/login';
            $.post(url,postData,function (data) {
                if(data.status){
                    var info =  JSON.parse( data.info);
                    var fn_name = info.fn_name;
                    if(fn_name){
                      if(fn_name == 'orderPayment'){
                          orderPayment(info);
                      }
                        // info=JSON.parse( info );
                        // console.log(info)
                        // eval(fn_name +'("'+info+'")');
                         return false;
                    }
                }
                if(!data.status){
                    dialog.success(data.info);
                }
                // layer.close(index);
            },'JSON')
            
        }
    });
}

//忘记密码-弹窗触发
function forgetWalletPasswordDialog(){
    var content = $('#WalletPasswordHtml').html();
    layer.open({
        title:['重置密码','border-bottom:1px solid #d9d9d9;'],
        className:'forgetWalletPasswordLayer',
        content:content,
        type:1,
        shadeClose:false,
        btn:['确定'],
        success:function(){
           
        },
        yes:function(){
            var postForm = $('.forgetWalletPasswordLayer #ForgetWalletPassword');
            var content='';
            var postData = postForm.serializeObject();
            // if(!register.vfyCheck(postData.captcha)){
            //     content = "请输入正确的验证码";
            // }else if(!register.pswCheck(postData.password)){
            //     content = "请输入4数字或字母的密码";
            // }
            if(content){
                dialog.error(content);
                return false;
            }
            var url = module+'Wallet/forgetPassword';
            $.post(url,postData,function (data) {
                dialog.success(data.info);
            })
        }
    });
}
//订单支付
 function orderPayment(postData) {
    var url = module + 'Payment/orderPayment';
    $.ajax({
        url: url,
        data: postData,
        type: 'post',
        beforeSend: function(){
            $('.loading').show();
        },
        error:function(){
            $('.loading').hide();
            dialog.error('AJAX错误');
        },
        success: function(data){
            $('.loading').hide();
            if(data.status){
                location.href = module + 'Payment/payComplete';
            }
            if(!data.status){
                if(data.code == 1){
                    dialog.success(data.info,module+'Order/manage');
                }else if(data.code == 2){
                    //余额不足
                    dialog.success(data.info);
                }else{
                    dialog.error('失败');
                }

            }
            // obj.removeClass("nodisabled");//防止重复提交

            // location.href = module + 'Order/confirmOrder/order_sn/' + data.order_sn;
        }
    });
}