$(document).ready(function(){
    //折叠
    $('body').on('click','.folding',function(){
        var _this = $(this);
        var status = _this.attr('status');
        var _thisTr = _this.parents('tr');
        if(status == 'open'){
            _this.attr('status','close');
            var postData = {};
            postData.level = _thisTr.data('level');
            postData.id = _thisTr.data('id');
            postData.parent_id_1 = _thisTr.data('parent-id-1');
            //异步加载子分类
            $.ajax({
                url: 'manage',
                type:'post',
                data:postData,
                dataType: 'html',
                error: function(){
                    dialog.error('AJAX错误。。。');
                },
                success: function(data){
                    _thisTr.after(data);
                }
            });
        }else if(status == 'close'){
            _this.attr('status','open');
            if(_thisTr.data('level') == 1){
                _thisTr.nextUntil('[data-level=1]').remove();
            }else if(_thisTr.data('level') == 2){
                _thisTr.nextUntil('[data-level!=3]').remove();
                _thisTr.nextAll('[data-level=0]').remove();
            }
        }
    });

    //编辑 or 新增下级
    $('body').on('click','.a-edit,.a-add',function(){
        var _this = $(this);
        var _thisTr = _this.parents('tr');
        var url = controller + 'edit';
        url += '/id/' + _thisTr.data('id');
        url += '/operate/' + _this.data('operate');
        location.href = url;
    });

    //删除
    $('body').on('click','.a-del',function(){
        var _thisTr = $(this).parents('tr');
        var postData = {};
        postData.id = _thisTr.data('id');
        postData.level = _thisTr.data('level');
        var url = controller + 'del';
        var info = '删除该分类将会同时删除该分类的所有下级分类，您确定要删除吗';
        //询问框
        parent.layer.confirm(info, {
            btn: ['确定','取消'] //按钮
        }, function(){
            $.post(url,postData,function(msg){
                if(msg.status ==1){
                    parent.layer.msg(msg.info,{time:1000},function(){
                        var level = _thisTr.data('level');
                        if(level == 1){
                            _thisTr.nextUntil('[data-level=1]').remove();
                        }else if(level == 2){
                            _thisTr.nextUntil('[data-level!=3]').remove();
                        }
                        _thisTr.remove();
                    });
                }else {
                    parent.layer.msg(msg.info,{time:3000});
                }
            });
        });
    });

    //批量删除
    $('body').on('click','.a-del-batch',function(){
    });
    //审核商标申请资料
    var auditInfoLayer=$('#auditInfoLayer').html();
    $('body').on('click','.entry-audit',function(){
		 layer.open({
            type:1,
            area: ['1500px','auto'],
            fix: true, //不固定
            maxmin: true,
            shade:0.4,
            tipsMore:true,
            title: '查看申请人资料',
            content: auditInfoLayer,
            btn:['不通过','通过'],
            btn1:function(index){
               //不通过
            },
            btn2:function(index){
               //通过
            }
        });
	});
    var maxImg=$('#maxImg').html();
    $('body').on('click','.audit-img',function(){
        var imgSrc=$(this).attr('src');
        layer.open({
            // type:1,
            title: '图片信息',
            fix:false,
            moveOut:true,
            content:maxImg,
            success:function(){
                $('.max-img').attr('src',imgSrc);
            }
        })
    })
    
});