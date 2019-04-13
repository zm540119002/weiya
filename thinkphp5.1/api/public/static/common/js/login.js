//登录-弹窗触发
function loginDialog(){
    var content=$('.userInfoWrapper').html();
    window.scrollTo(0,0);
    layer.open({
        className:'loginLayer',
        type:1,
        shadeClose:false,
        content:content,
        btn:[''],
        success:function(indexs,i){
            tab_down('.loginNav li','.loginTab','click');
            $('.layui-m-layershade').on('touchmove',function(e){
                event.preventDefault();
            });
            fixedLayer();
        },
        yes:function(index){
            cancleFixedLayer();
            $('.loginTab').eq(0).show();
            layer.close(index);
        }
    });
}
//退出-弹窗触发
function logoutDialog(){
    var url = domain+'ucenter/UserCenter/logout';
    layer.open({
        content:'是否退出？',
        btn:['确定','取消'],
        yes:function(index){
            $.ajax({
                url: url,
                data: {},
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
                        location.reload();
                    }
                }
            });
            layer.close(index);
        }
    });
}
/**异步登录回调函数
*/
var loginBackFunctionParameter = {};
var loginBackFunction = function(){
    loginBackFunctionParameter.jump_url ?
        location.href = loginBackFunctionParameter.jump_url :
        location.href = action;
};
$(function(){
    //登录-弹窗事件
    $('body').on('click','#login_dialog',function(){
        loginBackFunction = flushPage;
        loginDialog();
    });
    //退出-弹窗事件
    $('body').on('click','#logout_dialog',function(){
        logoutDialog();
    });
    //登录 or 注册 or 重置密码
    tab_down('.loginNav li','.loginTab ','click');
    $('body').on('click','.loginBtn,.registerBtn',function(){
        var _this = $(this);
        var postData = _this.parents('form').serializeObject();
        var content='';
        var method = _this.data('method');
        if(!register.phoneCheck(postData.mobile_phone)){
            content='请输入正确手机号码';
        }else if(method!='login' && method!='login_admin' && !register.vfyCheck(postData.captcha)){
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
            var url = domain + 'ucenter/UserCenter/' + method;
            $.post(url,postData,function (data) {
                // return false;
                if(data.status==0){
                    dialog.error(data.info);
                    return false;
                }else if(data.status==1){
                    $('.layui-m-layer').remove();
                    loginBackFunctionParameter.jump_url = data.info;
                    loginBackFunction();
                }
            });
        }
    });
    //异步登录验证
    $('body').on('click','.async_login',function () {
        var jump_url = $(this).data('jump_url');
        var postData = {};
        $.ajax({
            url: jump_url,
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
                }else if(data.code==1){
                    if(data.data == 'no_login'){
                        loginDialog();
                    }
                    if(data.data=='no_empower'){
                        dialog.error(data.msg);
                    }
                    if(data.data=='no_factory_register'){
                        location.href = data.url;
                    }
                }else{
                    loginBackFunctionParameter.jump_url = jump_url;
                    loginBackFunction();
                }
            }
        });
    });
    //显示隐藏密码
    $('body').on('click','.view-password',function(){
        var _this=$(this);
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