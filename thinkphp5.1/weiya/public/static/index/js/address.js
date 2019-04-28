
$(function(){
    $('.edit_operate').find('.address_edit').hide();

    //添加收货地址
    $('body').on('click','.add_address_1',function () {
        var title='添加新的收货地址';
        addressLayer(title);
    });

    // 修改地址
/*    $('body').on('click','.address_edit',function () {
        var title='修改地址';
        var data=$(this).parents('.item_addr').find('.consigneeInfo').serializeObject();
/!*        console.log(data);
        return false;*!/
        addressLayer(title,data);
    });*/

    // 显示地址信息
    $('body').on('click','.address_edit',function () {
        var title='修改地址';
        var data=$(this).parents('.item_addr').find('.consigneeInfo').serializeObject();

        addressLayer(title,data);
    });

    // 显示地址列表
    $('body').on('click','.select_address',function () {

        var url = module + 'Address/_popGetList';

        popBackFunction = getAddressList();
        pop(url);

        function getAddressList(){
            $(".item_addr .consigneeInfo").each(function(){
                var _this = $(this);
                var province = _this.find('input[name="province"]').val();
                var city     = _this.find('input[name="city"]').val();
                var area     = _this.find('input[name="area"]').val();

                var region = [];
                region.push(province);
                region.push(city);
                region.push(area);
                _this.prev().find('span').setArea(region);
            });
            intProvince();
        }
    });

    // 设定默认地址
    $('body').on('click','.myswitch',function(){
        if($(this).hasClass('myswitched')){
            $(this).removeClass('myswitched');
            $(this).attr('data-off',0);
        }else{
            $(this).addClass('myswitched');
            $(this).attr('data-off',1);
        }
    });

    // 选中地址
});

// 弹窗
function pop(url,data){
    $.ajax({
        url: url,
        data: data ? data : '',
        type: 'post',
        beforeSend: function(){
            $('.loading').show();
        },
        error:function(){
            $('.loading').hide();
            dialog.error('AJAX错误');
        },

        success: function(data){
            layer.open({
                type:1,
                className:'addressLayer',
                content: data,
                style: 'position:fixed; bottom:0; left:0; width: 100%; height: 100%; padding:10px 0; border:none;',
                success:function(){
                    if(popBackFunction && $.isFunction(popBackFunction) ){
                        popBackFunction();
                    }
                }
            });
        }
    });
}

//新增和修改地址弹窗
function addressLayer(title,data){
    var addressInfo=$('.section-address').html();
    layer.open({
        title:[title,'border-bottom:1px solid #d9d9d9;'],
        type:1,
        anim:'up',
        className:'addressLayer',
        content:addressInfo,
        btn:['保存','关闭'],
        success:function(){
            // 写入显示数据
            if(data){
                $ ('input[name="consignee"]').val(data.layer_consignee);
                $ ('input[name="mobile"]').val(data.layer_mobile);
                $ ('input[name="detail_address"]').val(data.layer_detail_address);
                $ ('input[name="address_id"]').val(data.layer_id);
                var region = [];
                region.push(data.province);
                region.push(data.city);
                region.push(data.area);
                $('.addressLayer .area_address').setArea(region);
                if(data.is_default==1){
                    $('.addressLayer .myswitch').addClass('myswitched');
                    $('.addressLayer .myswitch').attr('data-off',1);
                }
            }

        },
        yes:function(index){
            $('.section-address').empty();
            var area_address =$('.addressLayer .area-address-name').getArea();
            var postData  = $(".addressLayer .address_form").serializeObject();
            //$('.section-address').html(addressInfo);

            var content='';
            if(!postData.consignee){
                content='请填写收货人姓名';
            }else if(!register.phoneCheck(postData.mobile)){
                content='请填写正确的手机号码';
            }else if(!area_address){
                content='请选择地区';
            }else if(!postData.detail_address){
                content='请填写详细地址';
            }
            if(content){
                dialog.error(content);
                return false;
            }
            postData.is_default = $('.addressLayer .myswitch').attr('data-off');
            postData.province = area_address[0];
            postData.city = area_address[1];
            postData.area = area_address[2];
            //添加或修改地址
            var _this = $(this);
            _this.addClass("nodisabled");//防止重复提交
            var url = module + '/Address/edit';
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

                    }else{
                        $('#address_info').find('.item_addr,.add_address').remove();
                        $('#address_info').append(data);
                        var region = [];
                        region.push(postData.province);
                        region.push(postData.city);
                        region.push(postData.area);
                        $('.item_addr .area_address').setArea(region);
                        $('.edit_operate').find('.address_edit').hide();
                        var len=$('.delivery_address .item_addr').length;

                        if(!postData.address_id){
                            if(!len){
                                $('.delivery_address ').append(data);
                            }else{
                                $('.delivery_address div:first').before(data);
                            }

                        }else{
                            $.each($('.delivery_address .item_addr'),function(index,val){
                                var _thisId=$(this).find('.address_id').val();
                                if(postData.address_id==_thisId){

                                    $(this).after(data);
                                    $(this).remove();
                                }
                            })
                        }
                        layer.closeAll();
                    }
                }
            });
        },
        no:function(){
            $('.edit_operate').find('.address_edit').hide();
            layer.closeAll();
        }
    })
}