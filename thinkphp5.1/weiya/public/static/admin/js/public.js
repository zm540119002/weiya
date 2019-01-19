//限制input、textarea字数
var maximumWord =function(obj,max){
    var val=$(obj).val().length;
    var content='最多只能输入'+max+'个字';
    if(val>max){
        layer.open({
            content:content,
            time:2
        });
        $(obj).val($(obj).val().substring(0,max));
        return false;
    }
};

//选项卡切换
$.fn.tab = function(){
    alert(1);
    $(this).addClass("current").siblings().removeClass("current");
};

//选项卡切换
function tab_down(tab_k, tab_con, tab_dz) {
    var $div_li = $(tab_k);
    var timeout;
    if (tab_dz == "click") {
        $div_li.click(function() {
            $(this).addClass("current").siblings().removeClass("current");
            var index = $div_li.index(this);
            $(tab_con).hide().eq(index).show().addClass('active').siblings().removeClass('active');
        })
    } else if (tab_dz == "mouseover") {
        $div_li.hover(function() {
            var ts = $(this);
            timeout = setTimeout(function() {
                ts.addClass("current").siblings().removeClass("current");
                var index = ts.index();
                // $(tab_con).eq(index).show().siblings().hide();
                $(tab_con).hide().eq(index).show();
            }, 200)
        }, function() {
            clearTimeout(timeout);
        })
    }
}

//加载图片
function checkShow(ele){
    var winH=$(window).height(),
        scrollH=$(window).scrollTop();
    ele.each(function(){
        var _This=$(this),top;
        top =_This.offset().top;
        if(_This.attr('data-isloaded')){
            return ;
        }
        if(top < scrollH + winH){
            setTimeout(function(){
                // owImg(_This);
                _This.attr('src',_This.attr('data-img'));
                _This.attr('data-isloaded',true);
            },300)
        }
    })
}

//滑动轮播
function swipe(elemObj){
    window.mySwipe = Swipe(elemObj, {
        auto: 2500,
        callback: function(index,element){
            //回调函数
            $(".position li").eq(index).addClass("on").siblings().removeClass("on");
        }
    });
     $(".position li").click(
        function () {
            mySwipe.slide($(this).index());
        }
    );
}
//活动倒计时
function countDown(time,id){
    var day_elem = id.find('.day');
    var hour_elem = id.find('.hour');
    var minute_elem = id.find('.minute');
    var second_elem = id.find('.second');
    var end_time = new Date(time).getTime(),//月份是实际月份-1
        sys_second = (end_time-new Date().getTime())/1000;
    var timer = setInterval(function(){
        if (sys_second > 1) {
            sys_second -= 1;
            var day = Math.floor((sys_second / 3600) / 24);
            var hour = Math.floor((sys_second / 3600) % 24);
            var minute = Math.floor((sys_second / 60) % 60);
            var second = Math.floor(sys_second % 60);
            day_elem && $(day_elem).text(day);//计算天
            $(hour_elem).text(hour<10?"0"+hour:hour);//计算小时
            $(minute_elem).text(minute<10?"0"+minute:minute);//计算分
            $(second_elem).text(second<10?"0"+second:second);//计算秒
        } else {
            clearInterval(timer);
            $('.count_down_box').html('<span>本次活动已结束</span>');
        }
    }, 1000);
}

var addTimer = function(){
    var list = [],callback,interval,opt,unix,iStartUp=0;
    return function(id,timeStamp1,timeStamp2){
        unix=parseInt(new Date(timeStamp2).getTime());
        if(!interval){
            interval = setInterval(function(){
                go(unix);
            },1000);
        }
        
        list.push(
            {
                ele:document.getElementById(id),
                otime:timeStamp1,
                ctime:timeStamp2
            }
        );
    };

    function go(opt) {
        for (var i = 0; i < list.length; i++) {
            //list[i].ele.innerHTML = changeTimeStamp(list[i].time);
            callback= changeTimeStamp(list[i].otime,opt);
            if(!callback){
                list[i].ele.innerHTML='订单已取消';
                $(list[i].ele).attr('data-key',0);
                $(list[i].ele).parents('.order_info_list')
                .find('a.order_pay_btn')
                .removeClass('order_pay_btn')
                .text('已取消')
                .addClass('order_cancle');
                $(list[i].ele).removeAttr('id');
                //clearInterval(interval);
                //interval=null;
            }else{
                for(var k=0;k<callback.length;k++){
                    list[i].ele.children[k].innerHTML=callback[k];               
                }
            }
            if (new Date(list[i].otime).getTime()==opt){
                list.splice(i, 1); 
            }
        }
        unix=unix+1000;
    }

    //传入unix时间戳，得到倒计时
    function changeTimeStamp(endTime,backCurrentTime){
        var distancetime = new Date(endTime).getTime() - backCurrentTime;
        if(distancetime > 0){
　　　　　　 //如果大于0.说明尚未到达截止时间
            //var ms = Math.floor(distancetime%1000);
            var sec = Math.floor(distancetime/1000%60);
            var min = Math.floor(distancetime/1000/60%60);
            var hour =Math.floor(distancetime/1000/60/60%24);
            var day = Math.floor(distancetime/1000/60/60/24);

            // if(ms<100){
            //     ms = "0"+ ms;
            // }
            if(sec<10){
                sec = "0"+ sec;
            }
            if(min<10){
                min = "0"+ min;
            }
            if(hour<10){
                hour = "0"+ hour;
            }
            //return day + ":" +hour + ":" +min + ":" +sec + ":"+ms;
            return [day,hour,min,sec]
        }else{
　　　　　　//若否，就是已经到截止时间了  
            return false
        }
    }
}();

//错误提示;默认1.2s
function errorTipc(info,time){
    $('.error_tipc').text(info?info:'出错啦！').fadeIn().fadeOut(time?time:1200);
}

//阻止弹窗滑动穿透2
function isRolling(container){
    // 移动端touch重写
    var startX, startY;
    var button=document.getElementById('formLogin');
    button.addEventListener('click',function(){
       $('input').focus();
    });
    container.on('touchstart', function(e){
        //console.log(e.changedTouches[0]);
        // startX = e.changedTouches[0].pageX;
        // startY = e.changedTouches[0].pageY;
        startX = e.originalEvent.touches[0].pageX;
        startY = e.originalEvent.touches[0].pageY;
        
    });

    // 仿innerScroll方法
    container.on('touchmove', function(e){
        e.stopPropagation();

        var deltaX = e.originalEvent.touches[0].pageX - startX;
        var deltaY = e.originalEvent.touches[0].pageY - startY;

        // 只能纵向滚
        if(Math.abs(deltaY) < Math.abs(deltaX)){
            e.preventDefault();
            return false;
        }

        var box = $(this).get(0);

        if($(box).height() + box.scrollTop >= box.scrollHeight){
            if(deltaY < 0) {
                e.preventDefault();
                return false;
            }
        }
        if(box.scrollTop === 0){
            if(deltaY > 0) {
                e.preventDefault();
                return false;
            }
        }
        // 会阻止原生滚动
        // return false;
    });
}

$(function () {
    //返回顶部
    $('body').on('click','.backTop',function(){
        $('body,html').animate({scrollTop:0+'px'},500);
    });
    $('.top_menu_list .underdevelopment').on('click',function(){
        var index=$(this).index();
        if(index>0){
            dialog.error('功能正在开发中,暂未上线,敬请期待');
        }
    });
    //全选
    $('body').on('click','.checkall,.check_all_2',function () {
        var _thisChecked = $(this).prop("checked");
        $.each($('.checkitem,.check_item_2'),function () {
            $(this).prop('checked',_thisChecked);
        });
    });
    //反选
    $('body').on('click','.checkitem,.check_item_2',function () {
        var sign = true;
        //一票否决
        $.each($('.checkitem,.check_item_2'),function () {
            if(!$(this).prop('checked')){
                sign = false;
            }
        });
        $('.checkall,.check_all_2').prop('checked',sign);
    });
    //折叠
    $('body').on('click','.node_folding',function(){
        var _this = $(this);
        var status = _this.attr('status');
        var _thisTbody = _this.parents('tbody');
        if(status == 'open'){
            _this.attr('status','close');
            _thisTbody.find('[level=2]').show();
        }else if(status == 'close'){
            _this.attr('status','open');
            _thisTbody.find('[level=2]').hide();
        }
    });
    //复选-二级
    $('body').on('click','.check_item_2',function () {
        var _thisTbody = $(this).parents('tbody');
        if($(this).parents('tr').attr('level')=='1'){
            _thisTbody.find('[level=2]').find('.check_item_2').prop('checked',$(this).prop('checked'));
        }else if($(this).parents('tr').attr('level')=='2'){
            var sign = true;
            //一票否决
            $.each(_thisTbody.find('[level=2]').find('.check_item_2'),function () {
                if(!$(this).prop('checked')){
                    sign = false;
                }
            });
            _thisTbody.find('[level=1]').find('.check_item_2').prop('checked',sign);
        }
    });
    $(window).on('scroll',function(){
        var scrolltop=$(document).scrollTop();
        if(scrolltop>=300){
            $('.right_sidebar').show();
        }else{
            $('.right_sidebar').hide();
        }
    });
    var h = document.documentElement.clientHeight || document.body.clientHeight;
    $('.Hui-article-box').css('height',(h-50)+'px').addClass('scrollContainer');
});