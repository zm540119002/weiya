
function addCart(postData) {
    //console.log(postData)
    var url = module + 'Cart/addCart';
     var _this=postData._this;
     var lis=postData.lis;
    _this.addClass("nodisabled");//防止重复提交
    delete postData._this;
    delete postData.lis;
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
            _this.removeClass("nodisabled");//防止重复提交
            if(data.status==0){
                dialog.error(data.info);
            }
            else if(data.code==1 && data.data=='no_login'){
                loginDialog();
                loginBackFunction = addCart;
                loginBackFunctionParameter = postData;
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
                $('footer').find('.cart_num').addClass('cur');
                $('footer').find('.add_num').text('+'+num).addClass('current');
                setTimeout(function(){
                    $('.add_num').removeClass('current');
                },2000)

            }
        }
    });
}
$(function () {
    //计算商品列表总价
    //calculateTotalPrice();
    //加
    $('body').on('click','.gplus',function(){
        var incrementObj={};
        incrementObj.order_quantity=$(this).siblings('.minimum_order_quantity').val();
        incrementObj.increase_quantity=$(this).siblings('.increase_quantity').val();
        console.log(incrementObj);
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
        if(buyNum>orderNum){
            dialog.error('购买限额为'+orderNum);
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
        //修改数据库数量
        var _this = $(this);
        var postData = {};
        var id = _this.parents('li').data('cart_id');
        var num = _this.siblings('.cart_gshopping_count').val();
        postData.id = id;
        postData.num = num;
        _this.addClass("nodisabled");//防止重复提交
        editCartNum(postData,_this);

    });
    //购物车减
    $('body').on('click','.cart_greduce',function(){
        //购物车单个商品数量自减
        cartGoodsNumReduce($(this));
        //购物车复选框勾选
        cartCheckedBox($(this));
        //计算购物车商品列表总价
        calculateCartTotalPrice($(this));
        //修改数据库数量
        var _this = $(this);
        var postData = {};
        var id = _this.parents('li').data('cart_id');
        var num = _this.siblings('.cart_gshopping_count').val();
        postData.id = id;
        postData.num = num;
        _this.addClass("nodisabled");//防止重复提交
        editCartNum(postData,_this);

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

    //加入购物车
    $('body').on('click','.add_cart,.add_purchase_cart',function(){
        var _this = $(this);
        var lis = null;
        lis = $('ul.goods_list').find('li[data-buy_type="1"]');
        var postData = assemblyData(lis);
        var goodsList = postData.goodsList;
        console.log(postData)
        for(var i=0;i<goodsList.length;i++){
            if(goodsList[i].buy_type == 1 && !goodsList[i].brand_name){
                dialog.error('请设置品牌');
                return false;
            }
        }

        if(!postData){
            return false;
        }
        postData._this = _this;
        postData.lis = lis;

        addCart(postData);
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
				}else{
                    //dialog.success(data.info);
                    var num = 0;
                    $.each(lis,function(){
                        num += parseInt($(this).find('.gshopping_count').val());
                    });
                    //parseInt($('footer').find('num').text())+parseInt(num)
                    $('.goodsInfoLayer').find('.cart_num').addClass('cur');
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
                }else if(data.status==1){

                }else{
                    location.href = url;
                }
            }
        });
    });
    //去结算 生成订单
    $('body').on('click','.settlement',function(){
        var postData = {};
        var goodsList = [];
        var oLis=$('.cart_goods_list li');
        $.each(oLis,function () {
            var signcheck=$(this).find('.sign_checkitem');
            if(signcheck.prop('checked')){
                var goods_id=$(this).data('id');
                var buy_type=$(this).data('buy_type');
                var brand_id=$(this).data('brand_id');
                var brand_name=$(this).data('brand_name');
                var num=$(this).find('.cart_gshopping_count').val();
                goodsList.push({
                    goods_id:goods_id,
                    buy_type:buy_type,
                    brand_id:brand_id,
                    brand_name:brand_name,
                    num:num
                });
            }
        });
        postData.goodsList = goodsList;
        if($.isEmptyArray(goodsList)){
            dialog.error('请选择要结算的商品');
            return false
        }
        var _this = $(this);
        _this.addClass("nodisabled");//防止重复提交
        generateOrder(postData,_this);
    });
    //确认订单
    $('body').on('click','.confirm_order',function () {
        _this = $(this);
        var consignee=$('.consigneeInfo input[name="layer_consignee"]').val();
        var mobile=$('.consigneeInfo input[name="layer_mobile"]').val();
        var province=$('.consigneeInfo input[name="province"]').val();
        var city=$('.consigneeInfo input[name="city"]').val();
        var area=$('.consigneeInfo input[name="area"]').val();
        var detail_address=$('.consigneeInfo input[name="layer_detail_address"]').val();
        var orderId = $('.order_id').val();
        var orderSn = $('.order_sn').val();
        var addressId = $('.address_id').val();
        var orderArr =[];
        $.each($('.goods_order_item li'),function () {
            _this = $(this);
            var order_detail_id = _this.data('order_detail_id');
            var brand_id = _this.find('.brand_name').data('id');
            var brand_name = _this.find('.brand_name').text();
            orderArr.push({
                id:order_detail_id,
                brand_id:brand_id,
                brand_name:brand_name,
            });
        })
        if(!addressId){
            dialog.error('请选择收货地址');
            return false;
        }
        var postData ={
            father_order_id:orderId,
            order_sn:orderSn,
            consignee:consignee,
            mobile:mobile,
            province:province,
            city:city,
            area:area,
            detail_address:detail_address,
            orderDetail:orderArr
        };
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
                    location.href = module + 'Order/toPay/order_sn/' + data.order_sn;

                }
            }
        });
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

    //购物车弹窗 样品购买
    var goodsInfoLayer=$('#goodsInfoLayer').html();
    var pageii;
    $('.sample_purchase').on('click',function(){
        var _this=$(this);
        pageii = layer.open({
            className:'goodsInfoLayer',
            content: goodsInfoLayer,
            closeBtn:2,
            type:1,
            shadeClose:false,
            btn:[''],
            // fixed:false,
            success:function(){
                var winHeight=$(window).height();
                var goodsTitle=_this.parents('li').find('.goods_title').text();
                var id=_this.parents('li').data('id');
                var sample_price=_this.find('.sample_price').val();
                var specification=_this.parents('li').find('.specification_text').text();
                var goodsImg=_this.parents('body').find('.swiper-slide').eq(0).find('img').attr('src');
                var minimum_sample_quantity=_this.find('.minimum_sample_quantity').val();
                $('.goodsInfoLayer .goods_title').text(goodsTitle);
                $('.goodsInfoLayer li').data('id',id);
                $('.goodsInfoLayer .sample_price').text(sample_price);
                $('.goodsInfoLayer .specification').text(specification);
                $('.goodsInfoLayer .goods_img').attr('src',goodsImg);
                $('.goodsInfoLayer .minimum_sample_quantity').text(minimum_sample_quantity);
                $('.goodsInfoLayer .minimum_order_quantity').val(minimum_sample_quantity);
            },
            no:function(){
            }
            
        });
    });
    //去支付
    $('body').on('click','.pay',function () {
        var orderSn =  $('#order_sn').val();
        location.href = module + 'Order/toPay/order_sn/' + orderSn;
    });
    //一键分享转发 微信分享提示图
    $('body').on('click','.share',function(){
        $('.mcover').show();
    });
    //关闭微信分享提示图
    $('body').on('click','.weixinShare_btn',function(){
        $('.mcover').hide();
    });

});

//生成订单
function generateOrder(postData,obj) {
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
            obj.removeClass("nodisabled");//防止重复提交
            $('.loading').hide();
            if(data.status){
                location.href = module + 'Order/confirmOrder/order_sn/' + data.order_sn;
            }else{
                dialog.error(data.info);
            }

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
        var brand_id=_this.find('.brand_name').data('id');
        var brand_name=_this.find('.brand_name').text();
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
            tmp.brand_id=brand_id;
            tmp.brand_name=brand_name;
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
            amount += _thisLi.find('.sample_price').text() * num;
        });
        $('.goodsInfoLayer footer').find('price').html(amount.toFixed(2));
    }else{
        var _thisLis = $('.list.goods_list').find('li');
        $.each(_thisLis,function(index,val){
            var _thisLi = $(this);
            var num = _thisLi.find('.gshopping_count').val();
            //console.log(111);
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
    var buy_type=_li.data('buy_type');
    var orderQuantity=parseInt(opt.order_quantity);
    if(buy_type==1){
        var num = _li.find('.gshopping_count').val();
        num=parseInt(num);
        if(num==0){
            _li.find('.gshopping_count').val(opt.order_quantity);
        }else{
            num=num+parseInt(opt.increase_quantity);
            _li.find('.gshopping_count').val(num);
        }
    }
    
    if(buy_type==2){
        var num = $('.goodsInfoLayer').find('.gshopping_count').val();
        num=parseInt(num);
        if(num>=orderQuantity){
            dialog.error('购买限额为'+num);
            //return false;
        }else{
            $('.goodsInfoLayer').find('.gshopping_count').val(++num);
        }
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

//修改购物车商品数量
function editCartNum(postData,obj) {
    var url = module + 'Cart/editCartNum';
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
            obj.removeClass("nodisabled");//防止重复提交
            $('.loading').hide();
            
        }
    });
}
