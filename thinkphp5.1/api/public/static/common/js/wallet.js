
function walletPayDialog() {
    var content=$('#walletPay').html();
    window.scrollTo(0,0);
    layer.open({
        className:'payPasswordLayer',
        type:1,
        shadeClose:false,
        content:content,
        title:['钱包支付密码','border-bottom:1px solid #d9d9d9;'],
        btn:['确定支付',''],
        success:function(indexs,i){
            //钱包密码
            var oLis=$('.payPasswordLayer input.password_item');
            for(var i = 0;i<oLis.length;i++){
                var obj=oLis[i];
                $(obj).data('index',i);
                $(obj).attr('readonly',true);
                $(obj).on('keyup',function(){
                    var _this=$(this);
                    var len=_this.val().length;
                    var reg=/^\d+$/;
                    if(!reg.test(_this.val())){
                        var result=_this.val().substring(0,1);
                        _this.val(result);
                        dialog.error('只能输入纯数字密码');
                        return false;
                    }else{
                        if(len==0){
                            _this.removeAttr('readonly');
                            _this.focus();
                        }else if(len>1){
                            var result=_this.val().substring(0,1);
                            _this.val(result);
                            dialog.error('每个框只能输入一位数字');
                            _this.removeAttr('readonly');
                        }else{
                            var next=_this.data('index')+1;
                            if(next>oLis.length-1){
                                //obj.attr('readonly',true);
                            }
                            $(oLis[next]).removeAttr('readonly');
                            $(oLis[next]).focus(); 
                        }
                    }
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
            if(password.length<6){
                dialog.error('请输入正确6位数密码');
                return false;
            }
            var postData = {
                password:password,
            };
            var url = module+'Wallet/login';
            $.post(url,postData,function (data) {
                if(data.status){
                    walletPayCallBack(walletPayCallBackParameter);
                }
                if(!data.status){
                    dialog.success(data.info);
                }
                // layer.close(index);
            })

        }
    });
}

$(function(){
   
})

//忘记钱包支付密码-弹窗触发
function forgetWalletPasswordDialog(opt){
    var content = $('#WalletPasswordHtml').html();
    layer.open({
        title:['重置/设置支付密码','border-bottom:1px solid #d9d9d9;'],
        className:'forgetWalletPasswordLayer',
        content:content,
        type:1,
        shadeClose:false,
        btn:['确定',''],
        success:function(){
            //钱包密码
            var oLis=$('.forgetWalletPasswordLayer input.password_item');
            for(var i = 0;i<oLis.length;i++){
                var obj=oLis[i];
                $(obj).data('index',i);
                $(obj).attr('readonly',true);
                $(obj).on('keyup',function(){
                    var _this=$(this);
                    var len=_this.val().length;
                    var reg=/^\d+$/;
                    if(!reg.test(_this.val())){
                        var result=_this.val().substring(0,1);
                        _this.val(result);
                        dialog.error('只能输入纯数字密码');
                        return false;
                    }else{
                        if(len==0){
                            _this.removeAttr('readonly');
                            _this.focus();
                        }else if(len>1){
                            var result=_this.val().substring(0,1);
                            _this.val(result);
                            dialog.error('每个框只能输入一位数字');
                            _this.removeAttr('readonly');
                        }else{
                            var next=_this.data('index')+1;
                            if(next>oLis.length-1){
                                //obj.attr('readonly',true);
                            }
                            $(oLis[next]).removeAttr('readonly');
                            $(oLis[next]).focus(); 
                        }
                    }
                });
            }
            $(oLis[0]).removeAttr('readonly');
        },
        yes:function(index){
            var postForm = $('.forgetWalletPasswordLayer #ForgetWalletPassword');
            var content='';
            var postData = postForm.serializeObject();

            var oLis=$('.forgetWalletPasswordLayer input.password_item');
            var password='';
            for(var i=0;i<oLis.length;i++){
                password=password+$(oLis[i]).val();
            }
            postData.password = password;
            console.log(postData)
            if(!register.vfyCheck(postData.captcha)){
                content = "请输入正确的验证码";
            }else if(!postData.password&&postData.password.length<6){
                content = "请输入6位数字的密码";
            }
            if(content){
                dialog.error(content);
                return false;
            }
            var url = module+'Wallet/forgetPassword';
            $.post(url,postData,function (data) {
                if(data.status){
                    //成功后弹出支付密码框
                    if(opt=='set'){
                        layer.closeAll();
                        return false;
                    }else{
                        layer.closeAll();
                        walletPayDialog();
                    }
                }
                if(!data.status){
                    dialog.success(data.info);
                }
            },'JSON')
        }
    });
}
//订单支付
 function orderPayment(postData) {
    var url = module + 'Payment/orderPayment';
     postData.pay_code=4;
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
                dialog.success(data.info,module + 'Payment/payComplete');
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