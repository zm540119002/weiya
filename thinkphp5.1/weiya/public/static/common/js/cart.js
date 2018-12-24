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
        var foreign_ids=[];
        foreign_ids.push(_li.data('id'));
        postData.foreign_ids = foreign_ids;
        var type = 'single';
        delCart(postData,type,$(this));
    });
    //选择删除购物车
    $('body').on('click','.detele_carts',function(){
        var postData = {};
        var foreign_ids = [];
        $.each($('.purchase_package_list li'),function(){
            var _this=$(this);
            if(_this.find('.sigle_checkbox').is(':checked')){
                var foreign_id = _this.data('id');
                foreign_ids.push(foreign_id);
            }
        });
        postData.foreign_ids = foreign_ids;
        var type = 'more';
        delCart(postData,type,$('.purchase_package_list li'));
    });
});
//从购物车里替换单个商品信息
function replaceOneGoodsToCart(obj) {
    var _li = obj.parents('li');
    var postData = {};
    postData.foreign_id = _li.data('id');
    postData.num = obj.find('.gshopping_count').val();
    var url = CONTROLLER + '/replaceOneGoodsToCart';
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
    var url = CONTROLLER + '/delCart';
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
                                var cartId = _this.data('id');
                                for(var i=0;i<postData.foreign_ids.length;i++){
                                    if(cartId == postData.foreign_ids[i]){
                                        _this.remove();
                                    }
                                }
                            });
                        }
                        if( $('.purchase_package_list li').length == 0){
                            $('.select_checkbox_box').hide();
                            $('#no_data').show();
                        }else{
                            $('.select_checkbox_box').show();
                        }
                        calculateTotalPrice();
                        dialog.success(data.info);
                    }
                }
            });
            layer.close(index);
        }
    });
}


