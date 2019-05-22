//设置钱包支付密码
$('body').on('click','.set_wallet',function () {
    var opt = $(this);
    var data = {jump_url:$(this).data('jump_url')};
    loginBackFunction = forgetWalletPasswordDialog(opt);
    async_verify(data);
});
//异步验证
function async_verify(param){
    var jump_url = param.jump_url;
    $.ajax({
        url: jump_url,
        data: {},
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
                if(data.data.code == '1001'){
                    loginBackFunctionParam.jump_url = jump_url;
                    loginDialog();
                }else if(data.data.code=='1002'){
                    dialog.error(data.data.msg);
                }else{
                    dialog.error(data.info);
                }
            }else if(data.status==1){
                dialog.success(data.info);
            }
        }
    });
}

//获取验证码
var timer;
var requestSign = true;
$('body').on('click','.send_sms',function(){

    if($(this).attr('disabled')){
        return false;
    }
    var _form = $(this).parents('form');
    var postData = {};
    postData.mobile_phone = _form.find('[name=mobile_phone]').val();
    var userPhone=_form.find('.user_phone').val();
    if(!requestSign){
        return false;
    }
    var time=60;
    var content='';
    if(!register.phoneCheck(userPhone)){
        content='请输入正确手机号码';
    }
    if(content){
        errorTipc(content);
        return false;
    }
    $('.tel_code').val('');
    clearInterval(timer);
    timer=setInterval(CountDown,1000);
    function CountDown(){
        _form.find('.send_sms').attr('disabled',true);
        _form.find('.send_sms').text('重新发送'+time+'s');
        if(time==0){
            _form.find('.send_sms').text("获取验证码").removeAttr("disabled");
            _form.find('.tel_code').val('');
            clearInterval(timer);
        }
        time--;
    }
    var send_sms_url = domain + 'ucenter/UserCenter/sendSms';
    $.post(send_sms_url,postData,function(msg){
        requestSign = true;
        if(msg.status == 0){
            $('.phone').val('').removeAttr("disabled");
            _form.find('.send_sms').val("获取验证码").removeAttr("disabled");
            _form.find('.tel_code').val('');
            clearInterval(timer);
            errorTipc(msg.info,3000);
            return false;
        }else if(msg.status == 1){
            errorTipc("验证码已发送至手机:"+ postData.mobile_phone +' ，请查看。',3000);
            return false;
        }
    });
});

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
            $('.payPasswordLayer li').eq(0).find('input[type="password"]').focus();
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
