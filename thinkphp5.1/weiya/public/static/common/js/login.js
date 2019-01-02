$(function(){
    //登录 / 注册-切换
    tab_down('.loginNav li','.loginTab ','click');
    //登录 or 注册 or 重置密码
    $('body').on('click','.loginBtn,.registerBtn,.comfirmBtn',function(){
        var _this = $(this);
        var method = _this.data('method');
        var postData = {};
        var content='';
        var url = domain+'ucenter/UserCenter/'+method;
        var postForm = null;
        var loginSign = 'dialog';
        if(method=='login'){//登录
            if($('.loginLayer #formLogin').length){//弹框
                postForm = $('.loginLayer #formLogin');
            }else{
                loginSign = 'page';
                postForm = $('#formLogin');
            }
        }else if(method=='register'){//注册
            if($('.loginLayer #formRegister').length){//弹框
                postForm = $('.loginLayer #formRegister');
            }else{
                loginSign = 'page';
                postForm = $('#formRegister');
            }
        }else if(method=='forgetPassword'){//重置密码
            if($('.loginLayer #formForgetPassword').length){//弹框
                postForm = $('.loginLayer #formForgetPassword');
            }else{
                loginSign = 'page';
                postForm = $('#formForgetPassword');
            }
        }
        if(!postForm){
            dialog.error('未知操作');
            return false;
        }
        postData = postForm.serializeObject();
        if(!register.phoneCheck(postData.mobile_phone)){
            content='请输入正确手机号码';
        }else if(method!='login' && !register.vfyCheck(postData.captcha)){
            content = "请输入正确的验证码";
        }else if(!register.pswCheck(postData.password)){
            content = "请输入6-16数字或字母的密码";
        }
        if(method && content){
            dialog.error(content);
            return false;
        }else if(content){
            errorTipc(content);
            return false;
        }else{
            $.post(url,postData,function (data) {
                if(data.status==0){
                    dialog.error(data.info);
                    return false;
                }else if(data.status==1){
                    if(loginSign=='page'){
                        location.href = data.info;
                    }else if(loginSign=='dialog'){
                        if($.isFunction(dialogLoginCallBack)){
                            dialogLoginCallBack(data)
                        }else{
                            dialogLoginCommonCallBack(data);
                        }
                    }
                }
            });
        }
    });
    //弹框登录成功默认回调函数
    function dialogLoginCommonCallBack(data) {
        location.href = data.info;
    }

    //显示隐藏密码
    //var onOff = true;
    $('body').on('click','.view-password',function(){
        var _this=$(this);
        //_this.toggleClass('active');
        if(_this.prev().attr('type')=='password'){
            $('.login_item .password').attr('type','text');
            $('.view-password').addClass('active');
        }else{
            $('.login_item .password').attr('type','password');
            $('.view-password').removeClass('active');
        }
    });

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
        var url = send_sms;
        $.post(url,postData,function(msg){
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

    //使用须知
    var attentionForm=$('#attentionForm').html();
    $('body').on('click','.use-attention',function(){
        var pageii = layer.open({
            title:['《美尚平台使用须知》','border-bottom:1px solid #d9d9d9;'],
            className:'addCcountLayer',
            type: 1,
            content: attentionForm,
            anim: 'up',
            style: 'position:fixed; left:0; top:0; width:100%; height:100%; border: none; -webkit-animation-duration: .5s; animation-duration: .5s;',
            success:function(){
            },
            btn:['确定']
        });
    });
});