$(function () {
    //计算商品列表总价
    //calculateTotalPrice();
    //加
    $('body').on('click','.gplus',function(){
        var incrementObj={};
        incrementObj.order_quantity=$(this).siblings('.minimum_order_quantity').val();
        incrementObj.increase_quantity=$(this).siblings('.increase_quantity').val();
        //单个商品数量自加
        goodsNumPlus($(this),incrementObj);
        //计算商品列表总价
        calculateTotalPrice($(this));
    });
    //减
    $('body').on('click','.greduce',function(){
        var incrementObj={};
        incrementObj.order_quantity=$(this).siblings('.minimum_order_quantity').val();
        incrementObj.increase_quantity=$(this).siblings('.increase_quantity').val();
        //单个商品数量自减
        goodsNumReduce($(this),incrementObj);
        //计算商品列表总价
        calculateTotalPrice($(this));
    });
    //购买数量.失去焦点
    $('body').on('blur','.gshopping_count',function(){
        var buyNum=parseInt($(this).val());
        var orderNum=parseInt($(this).siblings('.minimum_order_quantity').val());
        if(buyNum<orderNum){
             dialog.error('起订数量不能少于'+orderNum);
             $(this).val(orderNum);
             return false;
        }
        //计算商品列表总价
        calculateTotalPrice($(this));
    });
    //购物车加
    $('body').on('click','.cart_gplus',function(){
        //购物车单个商品数量自加
        cartGoodsNumPlus($(this));
        //购物车复选框勾选
        cartCheckedBox($(this));
        //计算商品列表总价
        calculateCartTotalPrice($(this));
    });
    //购物车减
    $('body').on('click','.cart_greduce',function(){
        //购物车单个商品数量自减
        cartGoodsNumReduce($(this));
        //购物车复选框勾选
        cartCheckedBox($(this));
        //计算购物车商品列表总价
        calculateCartTotalPrice($(this));
    });
    //购物车购买数量.失去焦点
    $('body').on('blur','.cart_gshopping_count',function(){
        var buyNum=parseInt($(this).val());
        if(buyNum<1){
             //dialog.error('起订数量不能少于'+orderNum);
             $(this).val(1);
             return false;
        }
        $(this).parents('li').find('.sign_checkitem').prop("checked",true);
        //购物车复选框勾选
        cartCheckedBox($(this));
        //计算购物车商品列表总价
        calculateCartTotalPrice($(this));
    });
    //购物车全选总价
    $('body').on('click','footer .checkall,.cpy_checkitem,.sign_checkitem',function(){
        //计算购物车商品列表总价
        calculateCartTotalPrice();
    });
    //立即结算/立即购买
    $('body').on('click','.buy_now,.clearing_now',function(){
        var postData = assemblyData($('ul.goods_list').find('li'));
        
        if(!postData){
            return false;
        }
        generateOrder(postData,buyNowCallBack);
    });

    //加入购物车
    $('body').on('click','.add_cart,.add_purchase_cart',function(){
        var lis = null;
        if($($(this).context).hasClass('add_purchase_cart')){
            lis = $(this).parents('li');
        }else{
            lis = $('ul.goods_list').find('li[data-buy_type="1"]');
        }
        var postData = assemblyData(lis);
        if(!postData){
            return false;
        }
        var url = module + 'Cart/addCart';
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
                }
                else if(data.code==1 && data.data=='no_login'){
					loginDialog();
                    return false;
				}
                else{
                    dialog.success(data.info);
                    var num = 0;
                    $.each(lis,function(index,val){
                        var buyType=$(this).data('buy_type');
                        if(buyType==1){
                            num += parseInt($(this).find('.gshopping_count').val());
                        }
                    });
                    $('footer').find('.cart_num').text(num);
                    $('footer').find('.add_num').text('+'+num).addClass('current');
                    setTimeout(function(){
                        $('.add_num').removeClass('current');
                    },2000)
                    
                }
            }
        });
    });
    //样品弹窗加入购物车
    $('body').on('click','.goodsInfoLayer .add_cart_layer',function(){
        var lis = null;
        if($($(this).context).hasClass('add_purchase_cart')){
            lis = $(this).parents('li');
        }else{
            lis = $('.goodsInfoLayer ul.goods_list').find('li');
        }
        var postData = assemblyData(lis);
        if(!postData){
            return false;
        }
        console.log(postData);
        var url = module + 'Cart/addCart';
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
                }
                else if(data.code==1 && data.data=='no_login'){
					loginDialog();
                    return false
				}
                else{
                    //dialog.success(data.info);
                    var num = 0;
                    $.each(lis,function(){
                        num += parseInt($(this).find('.gshopping_count').val());
                    });
                    //parseInt($('footer').find('num').text())+parseInt(num)
                    $('.goodsInfoLayer').find('.cart_num').text(num);
                    $('.goodsInfoLayer').find('.add_num').text('+'+num).addClass('current');
                    setTimeout(function(){
                        $('.add_num').removeClass('current');
                    },2000)
                    
                }
            }
        });
    });
    //购物车列表页
    $('body').on('click','.add_cart_icon',function(){
    var url = module + 'Cart/index';
    location.href=url;
    });
    //去结算
    $('body').on('click','.settlement',function(){
        var postData = {};
        var cartIds = [];
        var oLis=$('.cart_goods_list li');
        $.each(oLis,function () {
            var signcheck=$(this).find('.sign_checkitem');
            if(signcheck.prop('checked')){
                var cart_id=$(this).data('cart_id');
                cartIds.push(cart_id);
            }
        });
        postData.cartIds = cartIds;
        generateOrder(postData)
    });
    //确认订单
    // $('body').on('click','.determine_order',function(){
    //     var consigneeName=$('.consignee_name').text();
    //     var consigneePhone=$('.consignee_phone').text();
    //     var consigneeAddress=$('.consignee_address').text();
    //     var content='';
    //     if(!consigneeName || !isMobilePhone(consigneePhone) ||!consigneeAddress){
    //         content="请选择收货人地址";
    //     }
    //     var orderId = $('section.orderInfo').data('id');
    //     if(!orderId){
    //         content="请确定订单是否正确";
    //     }
    //     if(content){
    //         dialog.error(content);
    //         return false;
    //     }
    //     var postData = {};
    //     postData.orderId = orderId;
    //     var url = MODULE + '/Order/confirmOrder';
    //     $.ajax({
    //         url: url,
    //         data: postData,
    //         type: 'post',
    //         beforeSend: function(){
    //             $('.loading').show();
    //         },
    //         error:function(){
    //             $('.loading').hide();
    //             dialog.error('AJAX错误');
    //         },
    //         success: function(data){
    //             $('.loading').hide();
    //             if(data.status==0){
    //                 dialog.error(data.info);
    //             }else {
    //                 location.href = MODULE + '/Order/settlement/orderId/' + data.id;
    //             }
    //         }
    //     });
    // });
    //确认订单
    $('body').on('click','.confirm_order',function () {
        _this = $(this);
        var orderId = "{$info[0]['id']}";
        var orderSn = "{$info[0]['sn']}";
        var postData = {};
        postData.father_order_id = orderId;
        postData.order_sn = orderSn;
        _this.addClass("nodisabled");//防止重复提交
        var url = module + 'Order/confirmOrder';
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
                if(data.status == 0){

                }else if(data.status == 1){
                    location.href = module + 'Order/pay/order_sn/' + data.order_sn;
                }
            }
        });
    });
    //购物车弹窗
    var goodsInfoLayer=$('#goodsInfoLayer').html();
    var pageii;
    $('.sample_purchase').on('click',function(){
        var _this=$(this);
        pageii = layer.open({
            className:'goodsInfoLayer',
            content: goodsInfoLayer,
            closeBtn:2,
            shadeClose:false,
            btn:['取消'],
            // fixed:false,
            success:function(){
                var winHeight=$(window).height();
                var goodsTitle=_this.parents('li').find('.goods_title').text();
                var id=_this.parents('li').data('id');
                var price=_this.parents('li').find('price').text();
                var specification=_this.parents('li').find('.specification_text').text();
                $('.goodsInfoLayer .goods_title').text(goodsTitle);
                $('.goodsInfoLayer li').data('id',id);
                $('.goodsInfoLayer price').text(price);
                $('.goodsInfoLayer .specification').text(specification);
            },
            no:function(){
            }
            
        });
    });
});

//生成订单
function generateOrder(postData) {
    var url = module + 'Order/generate';
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
            location.href = module + 'Order/detail/order_sn/' + data.order_sn;
        }
    });
}

//组装数据
function assemblyData(lis) {
    if(!$('footer').find('price').length){
        return false;
    }
    var postData = {};
    postData.goodsList = [];
    var isInt = true;
    //console.log(lis);
    $.each(lis,function(){
        var _this = $(this);
        var num = _this.find('.gshopping_count').val();
        var buy_type=_this.data('buy_type');
        //alert(num);
        if(!isPosIntNumberOrZero(num)){
            isInt = false;
            return false;
        }
        var goodsId = _this.data('id');
        //alert(goodsId);
        if(parseInt(num) && goodsId){
            var tmp = {};
            tmp.foreign_id = goodsId;
            tmp.num = num;
            tmp.buy_type=buy_type;
            postData.goodsList.push(tmp);
        }
    });
    if(postData.goodsList && postData.goodsList.length == 0){
        dialog.error('请选择商品');
        return false;
    }
    if(!isInt){
        dialog.error('购买数量为正整数');
        return false;
    }
    return postData;
}

//计算商品列表总价
function calculateTotalPrice(obj){
    var buyType=obj.data('type');
    
    if(!$('footer').find('price').length){
        return false;
    }
    var isInt = true;
    var amount = 0;
    if(buyType=='sample'){
        var _thisLis = $('.goodsInfoLayer ul.goods_list').find('li');
        $.each(_thisLis,function(index,val){
            var _thisLi = $(this);
            var num = _thisLi.find('.gshopping_count').val();
            if(!isPosIntNumberOrZero(num)){
                isInt = false;
                return false;
            }
            amount += _thisLi.find('price').text() * num;
        });
        $('.goodsInfoLayer footer').find('price').html(amount.toFixed(2));
    }else{
        var _thisLis = $('.list.goods_list').find('li');
        $.each(_thisLis,function(index,val){
            var _thisLi = $(this);
            var num = _thisLi.find('.gshopping_count').val();
            if(!isPosIntNumberOrZero(num)){
                isInt = false;
                return false;
            }
            amount += _thisLi.find('price').text() * num;
        });
        $('footer').find('price').html(amount.toFixed(2));
    }
    
    if(!isInt){
        dialog.error('购买数量为正整数');
        return false;
    }
   
}
//计算购物车商品列表总价
function calculateCartTotalPrice(obj){
    if(!$('footer').find('price').length){
        return false;
    }
    var isInt = true;
    var totalNum=0;
    var amount = 0;
    var _thisLis = $('.cart_goods_list').find('li');
    $.each(_thisLis,function(index,val){
        var _thisLi = $(this);
        if(_thisLi.find('.sign_checkitem').is(':checked')){
            var num = _thisLi.find('.cart_gshopping_count').val();
            totalNum+=parseInt(num);
            amount += _thisLi.find('price').text() * num;
            if(!isPosIntNumberOrZero(num)){
                isInt = false;
                return false;
            }
        }
    });
    $('footer').find('price').html(amount.toFixed(2));
    $('footer').find('.total_num').text('('+totalNum+')'+'件');
}
//单个商品数量自减
function goodsNumReduce(obj,opt) {
    var _li = obj.parents('li');
    var num = _li.find('.gshopping_count').val();
    num=parseInt(num);
    var orderQuantity=parseInt(opt.order_quantity);
    if(num<=orderQuantity){
        return false;
    }
    num=num-parseInt(opt.increase_quantity);
    _li.find('.gshopping_count').val(num);

}

//单个商品数量自加
function goodsNumPlus(obj,opt) {
    var _li = obj.parents('li');
    var num = _li.find('.gshopping_count').val();
    if(num==0){
        _li.find('.gshopping_count').val(opt.order_quantity);
    }else{
        num=parseInt(num);
        num=num+parseInt(opt.increase_quantity);
        _li.find('.gshopping_count').val(num);
    }
}
//购物车中单个商品数量自减
function cartGoodsNumReduce(obj) {
    var _item = obj.parents('li');
    var num = _item.find('.cart_gshopping_count').val();
    num=parseInt(num);
    // var orderQuantity=parseInt(opt.order_quantity);
    if(num<2){
        // _item.find('.sign_checkitem').prop("checked",false);
        return false;
    }
    //num=num-parseInt(opt.increase_quantity);
    _item.find('.cart_gshopping_count').val(--num);
    _item.find('.sign_checkitem').prop("checked",true);
}

//购物车中单个商品数量自加
function cartGoodsNumPlus(obj) {
    var _item = obj.parents('li');
        _item.find('.sign_checkitem').prop("checked",true);
    var num = _item.find('.cart_gshopping_count').val();
    num=parseInt(num);
    //num=num+parseInt(opt.increase_quantity);
    _item.find('.cart_gshopping_count').val(++num);
}
// function cartGoodsNumPlus(obj) {
//     var _item = obj.parents('.item');
//         _item.find('.sign_checkitem').prop("checked",true);
//     var num = _item.find('.cart_gshopping_count').val();
//     num=parseInt(num);
//     //num=num+parseInt(opt.increase_quantity);
//     _item.find('.cart_gshopping_count').val(++num);
// }