$(function () {
    $('body').on('click','.my_cart,.address_manage,.recharge,.order_manage,.my_brand,.my_message,.my_collection,.my_report',
        function () {
            var jump_url = $(this).data('jump_url');
            loginBackFunctionParameter.jump_url = jump_url;
            var url = module+'Brand/index';
            console.log(url);
            var postData = {};
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
                    }else if(data.code==1){
                        if(data.data == 'no_login'){
                            loginDialog();
                        }
                    }else{
                        location.href = jump_url;
                    }
                }
            });
        }
    );
});


