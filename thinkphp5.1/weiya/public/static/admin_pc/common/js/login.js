var timer;
var requestSign = true;
//获取验证码
$('body').on('click','.mesg_code',function(){
    if($(this).attr('disabled')){
        return false;
    }
    var _form = $(this).parents('form');
    var postData = {};
    postData.mobile_phone = _form.find('[name=mobile_phone]').val();
    postData.captcha_type = _form.find('[name=captcha_type]').val();
    var userName=_form.find('.user_name').val();
    var userPhone=_form.find('.user_phone').val();
    if(!requestSign){
        return false;
    }
    //requestSign = false;
    var time=60;
    var content='';
    if(!register.phoneCheck(userPhone)){
        content='请输入正确手机号码';
    }
    if(content){
        dialog.error(content);
        return false;
    }
    $('.tel_code').val('');
    clearInterval(timer);
    timer=setInterval(CountDown,1000);
    function CountDown(){
        _form.find('.mesg_code').attr('disabled',true);
        _form.find('.mesg_code').text('重新发送'+time+'s');
        if(time==0){
            _form.find('.mesg_code').text("获取验证码").removeAttr("disabled");
            _form.find('.tel_code').val('');
            clearInterval(timer);
        }
        time--;
    }
    var url = '/index.php/Home/User/send_sms';
    $.post(url,postData,function(msg){
        requestSign = true;
        if(msg.status == 0){
            $('.phone').val('').removeAttr("disabled");
            _form.find('.mesg_code').val("获取验证码").removeAttr("disabled");
            _form.find('.tel_code').val('');
            clearInterval(timer);
            dialog.error(msg.info,3000);
            return false;
        }else if(msg.status == 1){
            dialog.error("验证码已发送至手机:"+ postData.mobile_phone +' ，请查看。',3000);
            return false;
        }
    });
});