
$(function () {
    // 弹出支付方式
    $('body').on('click','.confirm_order',function(){
        var settlementMethod=$('.settlementMethod').html();
        layer.open({
            type: 1
            ,anim: 'up'
            ,style: 'position:fixed; bottom:0; left:0; width: 100%; height: 50%; padding:10px 0; border:none;',
            className:'settlementMethod bankTransferLayer',
            content: settlementMethod
        });
    });

    // 选择支付方式
    $('body').on('click','.settlementMethod .pay_nav li',function(){
        $(this).addClass('current').siblings().removeClass('current');
        var pay_code = $(this).data('paycode');
        $(this).find('input[type="checkbox"]').prop('checked',true);
        $('.pay_code').val(pay_code);
    });


    // 提交订单带地址
    $('body').on('click','.settlement_btn',function () {

        var postData = {};
        postData = addAddress(postData);
        postData.order_id = $('.order_id').val();
        postData.order_sn = $('.order_sn').val();
        postData.pay_code = $('.pay_code').val();

        // 钱包支付 加载wallet.js文件
        if(postData.pay_code==4){
            walletPayDialog(postData);
            return false

        }else{
            _this = $(this);
            submitOrders(_this,postData);
        }
    });

    //再次购买
    $('body').on('click','.purchase_again',function () {
        //再次购买跳转到提交订单位置
        var _This=$(this);
        var oLis=$(this).parents('li').find('.order_item');
        var postData={};
        var goodsList=[];
        $.each(oLis,function () {
            var _this=$(this);
            var goods_id=_this.data('id');
            var buy_type=_this.data('buy_type');
            var brand_id=_this.data('brand_id');
            var brand_name=_this.data('brand_name');
            var num=_this.find('span.num').text();
            goodsList.push({
                goods_id:goods_id,
                buy_type:buy_type,
                num:num,
                brand_id:brand_id,
                brand_name:brand_name,
            });
        });
        if($.isEmptyArray(goodsList)){
            dialog.error('数据错误');
            return false
        }
        postData.goodsList=goodsList;
        _This.addClass("disabled");//防止重复提交
        generateOrder(postData,_This);
    });

    // 付款
    $('body').on('click','.forget_wallet_password',function () {
        forgetWalletPasswordDialog()
    });

});

// 增加订单收货地址信息
function addAddress(postData) {
    var addressId= $('.address_id').val();
    if(!addressId){
        dialog.error('请选择收货地址');
        return false;
    }

    postData.consignee= $('.consigneeInfo input[name="layer_consignee"]').val();
    postData.mobile   = $('.consigneeInfo input[name="layer_mobile"]').val();
    postData.province = $('.consigneeInfo input[name="province"]').val();
    postData.city     = $('.consigneeInfo input[name="city"]').val();
    postData.area     = $('.consigneeInfo input[name="area"]').val();
    postData.detail_address = $('.consigneeInfo input[name="layer_detail_address"]').val();
    postData.address_id = addressId;

    return postData;
}


// 其它支付方式提交订单
function submitOrders(_this,postData){
    var url = module + 'Order/confirmOrder';
    _this.addClass("nodisabled");//防止重复提交

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
            _this.removeClass("nodisabled");//删除防止重复提交
            $('.loading').hide();
            if(data.status){
                location.href = data.info;

            }else{
                dialog.error('结算提交失败!');
            }
        }
    });
}

