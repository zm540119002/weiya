
$(function(){
    $('.edit_operate').find('.address_edit').hide();

    // id转换成字符地址
    $(document).ready(function() {
        var data=$('.address_info .consigneeInfo').serializeObject();

        if(!$.isEmptyObject(data)){
            var region = [];
            region.push(data.province);
            region.push(data.city);
            region.push(data.area);
            $(".list_area_address").setArea(region);
        }
    });

    // 添加收货地址
    $('body').on('click','.add_address,.add_address_1',function () {
        var title='添加新的收货地址';
        addressLayer(title);
    });

    // 修改地址
    $('body').on('click','.address_edit',function () {
        var title='修改地址';
        var data=$(this).parents('.item_addr').find('.consigneeInfo').serializeObject();

        addressLayer(title,data);
    });

    // 显示地址列表&&选择地址
    $('body').on('click','.select_address',function () {
        var url = module + 'Address/_popGetList';
        $.ajax({
            url: url,
            data: '',
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
                        $(".delivery_address .consigneeInfo").each(function(){
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
                    }
                });
            }
        });

    });

    // 设置默认地址
    $('body').on('click','.myswitch',function(){
        if($(this).hasClass('myswitched')){
            $(this).removeClass('myswitched');
            $(this).attr('data-off',0);
        }else{
            $(this).addClass('myswitched');
            $(this).attr('data-off',1);
        }
    });

    // 选中地址 修改id,收货人,手机,地址
    $('body').on('click','.addressLayer .item_info',function(){
        var _this = $(this);
        _this.parents('.item_addr').addClass('active').siblings().removeClass('active');
        var data = _this.clone();

        $('#address_info').find('.select_address').show();

        $('#address_info .item_info').replaceWith(data);

        $('#address_info').find('.address_edit').hide();

        setTimeout(function(){
            layer.closeAll();
        },1000);
        return false;
    })
});


//新增和修改地址弹窗
var addressInfo=$('.section-address').html();
function addressLayer(title,data){
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
            // 获取参数用
            var area_address =$('.addressLayer .area-address-name').getArea();
            var postData  = $(".addressLayer .address_form").serializeObject();

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