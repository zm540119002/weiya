$(function () {
    //购物车.加减
    $('body').on('click','.gplus,.greduce',function(){
        replaceOneGoodsToCart($(this).parents('.item'));
    });
    //购买数量.失去焦点
    $('body').on('blur','.gshopping_count',function(){
        replaceOneGoodsToCart($(this));
    });
    //购物车删除
    $('body').on('click','.detele_cart',function(){
        var _li = $(this).parents('li');
        var postData = {};
        var cart_ids=[];
        cart_ids.push(_li.data('cart_id'));
        postData.cart_ids = cart_ids;
        var type = 'single';
        delCart(postData,type,$(this));
    });
    //选择删除购物车
    $('body').on('click','.detele_carts',function(){
        var postData = {};
        var cart_ids = [];
        $.each($('.cart_goods_list li'),function(){
            var _this=$(this);
            if(_this.find('.sign_checkitem').is(':checked')){
                var cart_id = _this.data('cart_id');
                cart_ids.push(cart_id);
            }
        });
        postData.cart_ids = cart_ids;
        var type = 'more';
        delCart(postData,type,$('.cart_goods_list li'));
    });
});
//从购物车里替换单个商品信息
function replaceOneGoodsToCart(obj) {
    var _li = obj.parents('li');
    var postData = {};
    postData.foreign_id = _li.data('id');
    postData.num = obj.find('.gshopping_count').val();
    var url = controller + 'replaceOneGoodsToCart';
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
            if(data.status==0) {
                dialog.error(data.info);
            }
        }
    });
}
//选择或当个删除购物车
function delCart(postData,type,obj) {
    var url = controller + 'del';
    layer.open({
        content:'是否删除？',
        btn:['确定','取消'],
        yes:function(index){
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
                    if(data.status==0){
                        dialog.error(data.info);
                    }else {
                        if(type == 'single'){
                            obj.parents('li').remove();
                        }
                        if(type == 'more'){
                            $.each(obj,function(){
                                var _this=$(this);
                                var cartId = _this.data('cart_id');
                                for(var i=0;i<postData.cart_ids.length;i++){
                                    if(cartId == postData.cart_ids[i]){
                                        _this.remove();
                                    }
                                }
                            });
                        }
                        if(!$('.cart_goods_list li').length){
                            var no_cart_data=$('.no_cart_data').html();
                            $('.cart_goods_list').append(no_cart_data);
                        }
                        calculateCartTotalPrice();
                        dialog.success(data.info);
                    }
                }
            });
            layer.close(index);
        }
    });
}


