
//设置钱包支付密码
$('body').on('click','.set_wallet',function () {
    var data = {jump_url:$(this).data('jump_url')};
    loginBackFunction = forgetWalletPasswordDialog(data);
    async_verify(data);
});

// 设置||忘记钱包支付密码-弹窗触发
function forgetWalletPasswordDialog(info){

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
                    layer.closeAll();
                    dialog.success('设置成功',info.jump_url);
                    return true;

                }
                if(!data.status){
                    dialog.success(data.info);
                    return false;
                }
            },'JSON');
        }
    });
}

// 付款
function walletPayDialog(postData) {
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

            if(password.length!=6){
                dialog.error('请输入正确6位数密码');
                return false;
            }
/*            var postData = {
                password:password
            };*/
            postData.password = password;
            var url = module+'Wallet/checkWallet';
            $.post(url,postData,function (data) {

                if(!data.status){
                    dialog.success(data.info);
                    return false;

                }else{
                    layer.close(index);
                    layer.open({
                        content : data.info,
                        btn: '确定',
                        yes : function(){
                            //if(data.url){
                                location.href=data.url;
                            //}
                        }
                    });
                }
            })

        }
    });
}
