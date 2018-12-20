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
            lis = $('ul.goods_list').find('li');
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
                }else if(data.code==1 && data.data=='no_login'){
					loginDialog();
				}else{
                     dialog.success(data.info);
                    var num = 0;
                    $.each(lis,function(){
                        num += parseInt($(this).find('.gshopping_count').val());
                    });
                    //parseInt($('footer').find('num').text())+parseInt(num)
                    $('footer').find('.cart_num').text(num);
                    $('footer').find('.add_num').text(num);
                    
                }
            }
        });
    });

    //确认订单
    $('body').on('click','.determine_order',function(){
        var consigneeName=$('.consignee_name').text();
        var consigneePhone=$('.consignee_phone').text();
        var consigneeAddress=$('.consignee_address').text();
        var content='';
        if(!consigneeName || !isMobilePhone(consigneePhone) ||!consigneeAddress){
            content="请选择收货人地址";
        }
        var orderId = $('section.orderInfo').data('id');
        if(!orderId){
            content="请确定订单是否正确";
        }
        if(content){
            dialog.error(content);
            return false;
        }
        var postData = {};
        postData.orderId = orderId;
        var url = MODULE + '/Order/confirmOrder';
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
                    location.href = MODULE + '/Order/settlement/orderId/' + data.id;
                }
            }
        });
    });
    //开通推客分享功能
    $('body').on('click','.open_referrer',function(){
        var url = MODULE + '/Referrer/openReferrer';
        layer.open({
            content:'是否开通？<br/>一键免费开通推客分享功能',
            btn:['确定','取消'],
            yes:function(index){
                $.ajax({
                    url: url,
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
                        if(data.status == 0){
                            if(data.url){
                                location.href = data.url;
                            }else{
                                dialog.error(data.info);
                            }
                        }else if(data.status == 1){
                            if(data.info=='isAjax'){
                                loginDialog();
                            }else{
                                layer.open({
                                    content : '已免费开通！<br/>推客分享功能',
                                    btn:['确定'],
                                    end : function(){

                                    },
                                    yes:function(index){
                                        $('.open_referrer').hide();
                                        layer.close(index)
                                    }
                                })
                            }
                        }
                    }
                });
                layer.close(index);
            }
        })
    });

    //我的二维码
    $('body').on('click','.QR_codes',function(){
        var url = location.href;
        $.ajax({
            url: MODULE + '/Referrer/myQRCodesWithGoods',
            data: {url:url},
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
                if(data.status == 0){
                    if(data.url){
                        location.href = data.url;
                    }else{
                        dialog.error(data.info);
                    }
                }else if(data.status == 1){
                    if(data.info=='isAjax'){
                        loginDialog(flushPage);
                    }else{
                        $('.mask,.express-code-box').show();
                        $('.twitter_code_img img').attr('src','/Uploads/'+data.url);
                    }
                }
            }
        });
    });

    //关闭删除二维码
    $("#areaMask2,.closeBtn").click(function() {
        var imgUrl = $('.twitter_code_img img').attr('src');
        $("#areaMask2").hide();
        $.ajax({
            url: MODULE + '/Referrer/delMyQRCodesWithGoods',
            data: {imgUrl:imgUrl},
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
                if(data.status == 1){
                    clockArea();
                }
                if(data.status == 0){
                    dialog.error(data.info)
                }
            }
        });
    });

    //一键分享转发 微信分享提示图
    $('body').on('click','.forward',function(){
        $.ajax({
            url: MODULE + '/CommonAuthUser/checkLogin',
            type:'post',
            beforeSend: function(){
                $('.loading').show();
            },
            error:function(){
                $('.loading').hide();
                dialog.error('AJAX错误');
            },
            success:function(data){
                $('.loading').hide();
                if(data.status == 0){
                    if(data.url){
                        location.href = data.url;
                    }else{
                        dialog.error(data.info);
                    }
                }else if(data.status == 1){
                    if(data.info=='isAjax'){
                        loginDialog(flushPage);
                    }else{
                        $('.mcover').show();
                    }
                }
            }
        });
    });

    //关闭微信分享提示图
    $('body').on('click','.weixinShare_btn',function(){
        $('.mcover').hide();
    });
    
    var group_buy_end = $('.groupBuyEnd').val();
    if(group_buy_end){ //重新开团
       dialog.confirm('此团购已结束，是否重新开团')
    }
    //发起微团购并支付
    $('body').on('click','.initiate_group_buy',function(){
        var postData = assemblyData($('ul.goods_list').find('li'));
        if(!postData){
            return false;
        }
        postData.returnUrl = location.href;
        postData.orderType = 1;
        postData.groupBuyId = $('.groupBuyId').val();
        if(group_buy_end){ //重新开团
            delete(postData["groupBuyId"]);
        }
        generateOrder(postData,groupBuyCallBack);
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
            // fixed:false,
            success:function(){
                var winHeight=$(window).height();
                var goodsTitle=_this.parents('li').find('.goods_title').text();
                $('.goodsInfoLayer .goods_title').text(goodsTitle);
                // $('.twitter-release-content').css('height',winHeight-120+'px');
                // $('.layui-m-layermain .layui-m-layersection').addClass('bottom-layer');
            },
            btn:['取消']
        });
    });
});

//生成订单
function generateOrder(postData,callBack) {
    postData.url = postData.url?postData.url:MODULE + '/Order/generate';
    $.ajax({
        url: postData.url,
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
            if(data.status == 0){
                if(data.joined){
                    layer.open({
                        content : data.info?data.info:'成功',
                        btn:['确定','取消'],
                        end : function(){

                        },
                        yes:function(index){
                            delete(postData["groupBuyId"]);
                            generateOrder(postData,groupBuyCallBack);
                            layer.close(index)
                        }
                    });
                    //dialog.confirm(data.info,data.url);
                }else{
                    dialog.error(data.info);
                }
            }else if(data.status == 1){
                if(data.info=='isAjax'){
                    loginDialog(callBack);
                }else{
                    location.href = MODULE + '/Order/orderDetail/orderId/' + data.orderId;
                }
            }
        }
    });
}
//立即购买回调
function buyNowCallBack() {
    $('.buy_now,.clearing_now').click();
}
//团购回调
function groupBuyCallBack() {
    $('.initiate_group_buy').click();
}

//关闭二维码
function clockArea() {
    $("#areaMask,.express-code-box").fadeOut();
}

//组装数据
function assemblyData(lis) {
    if(!$('footer').find('price').length){
        return false;
    }
    var postData = {};
    postData.goodsList = [];
    var isInt = true;
    $.each(lis,function(){
        var _this = $(this);
        var num = _this.find('.gshopping_count').val();
        if(!isPosIntNumberOrZero(num)){
            isInt = false;
            return false;
        }
        var goodsId = _this.data('id');
        if(parseInt(num) && goodsId){
            var tmp = {};
            tmp.foreign_id = goodsId;
            tmp.num = num;
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
    num=parseInt(num);
    num=num+parseInt(opt.increase_quantity);
    _li.find('.gshopping_count').val(num);

}