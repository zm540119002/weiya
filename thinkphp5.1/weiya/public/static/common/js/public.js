(function($){
    var deviceWidth=document.documentElement.clientWidth;
    var html =document.getElementsByTagName('html')[0];
    html.style.fontSize=deviceWidth/6.4+'px';
    if(deviceWidth>768){
         html.style.fontSize=60+'px';
    }
    $.fn.moreText = function(options){
        var defaults = {
            maxLength:102,
            mainCell:".branddesc",
            openBtn:'显示全部>',
            closeBtn:'收起'
        };
        return this.each(function() {
            var _this = $(this);

            var opts = $.extend({},defaults,options);
            var maxLength = opts.maxLength;
            var TextBox = $(opts.mainCell,_this);
            var openBtn = opts.openBtn;
            var closeBtn = opts.closeBtn;

            var countText = TextBox.html();
            var newHtml = '';
            if(countText.length > maxLength){
                newHtml = countText.substring(0,maxLength)+'...<span class="more">'+openBtn+'</span>';
            }else{
                newHtml = countText;
            }
            TextBox.html(newHtml);
            TextBox.on("click",".more",function(){
                if($(this).text()==openBtn){
                    TextBox.html(countText+' <span class="more">'+closeBtn+'</span>');
                }else{
                    TextBox.html(newHtml);
                }
            });
        });
    };

    //星星评分等级(5个img布局，有点重复)
    $.fn.setStar=function(options){
        var defaults={
            getFractionValue:1,
            mainCell:".star_img img",
            star:'/static/common/img/star.png',
            starRed:'/static/common/img/starred.png'
        };
        if($.isNumeric(options)){
            defaults.getFractionValue=options;
        }
        return this.each(function(){
            //console.log($(this));
            var _this=$(this);
            
            var opts=$.extend({},defaults,options); 
            var starBox = $(opts.mainCell,_this);
            var getFractionValue=opts.getFractionValue;
            var star=opts.star;
            var starRed=opts.starRed;
            var starValue=parseInt(getFractionValue);

            starBox.each(function(index){
                	
				var prompt=['1分','2分','3分','4分','5分'];	//评价分数
				this.id=index;		//遍历img元素，设置单独的id
                //console.log(this.id);
				starBox.attr('src',star);//空心星
				// _this.find('#'+getFractionValue).attr('src',starRed);		//当前的图片为实星
				// _this.find('#'+getFractionValue).prevAll().attr('src',starRed);	//当前的前面星星为实星  prompt[getFractionValue]
                _this.find('#'+(starValue-1)).attr('src',starRed);		//当前的图片为实星
				_this.find('#'+(starValue-1)).prevAll().attr('src',starRed);	//当前的前面星星为实星  prompt[getFractionValue]
                $(this).parent().next('span').text(getFractionValue+'分');
                $(this).parent().next('span').attr('data-score',getFractionValue);  
			});
        });
    };
    $.fn.getStar=function(){
         return this.find("span").attr('data-score');
    };

    //星星评分（绝对定位布局）
     $.fn.classStar=function(options){
        var defaults={
            getFractionValue:1,
            mainCell:".real_star",
            star:'public/admin-img/common/sellerCompany/star.png',
            starRed:'public/admin-img/common/sellerCompany/starred.png'
        };
        if($.isNumeric(options)){
            defaults.getFractionValue=options;
        }
        return this.each(function(){
            //console.log($(this));
            var _this=$(this);
            
            var opts=$.extend({},defaults,options); 
            var starBox = $(opts.mainCell,_this);
            var getFractionValue=opts.getFractionValue;
            var star=opts.star;
            var starRed=opts.starRed;
            var starValue=parseInt(getFractionValue)*25;

            starBox.each(function(index){             	
				// this.id=index;
                $(this).css('width',starValue+'px');
                $(this).parent().next('span').text(getFractionValue+'分');
                $(this).parent().next('span').attr('data-score',getFractionValue);  
			});
        });
    };
    $.fn.getClassStar=function(){
         return this.find("span").attr('data-store');
    };

    //进度条
    $.fn.getProgressBar=function(options){
        var defaults={
            getProgressValue:1,
            mainCell:".real_star_progress"
        };
        if($.isNumeric(options)){
            defaults.getProgressValue=options;
        }
        return this.each(function(){
            // console.log($(this));
            var _this=$(this);
            
            var opts=$.extend({},defaults,options); 
            var progressBox = $(opts.mainCell,_this);
            var getProgressValue=opts.getProgressValue;
            var progressValue=parseInt(getProgressValue)*2.1;
            progressBox.each(function(index){
                // this.id=index;
                $(this).css('width',progressValue+'px');
			});
        });
    };
    //楼层导航
    $.fn.scrollFloor=function(options){
        var defaults={
            floorNavMenu:'nav-floor',
            floorContent:'floor-content',
            floorContentChild:'floor',
            activeClass:'active'
        };
        var settings=$.extend(defaults,options);
        $(document).scroll(function(){
                
            var parentHeight=$('.'+settings.floorContent).height(),
                parentOffsetTop=$('.'+settings.floorContent)[0].offsetTop,
                childHeight=$('.'+settings.floorContentChild).outerHeight(true),
                docScrollTop=$(window).scrollTop();
                result=docScrollTop-parentOffsetTop;//parentOffsetTop
                n=Math.floor(result/childHeight);
                console.log(childHeight);
                if(result>=0&&n<=2){
                    
                    $('.'+settings.floorNavMenu).children().removeClass(settings.activeClass).eq(n).addClass(settings.activeClass);
                }
        });
        $('.'+settings.floorNavMenu).children().on('click',function(){
            var i=$(this).index();
            var _this=$(this);
            var floorId=$(this).data('floor');
            //var scrollFloorH=$('.'+settings.floorContent)[0].offsetTop+$('.'+settings.floorContentChild).outerHeight(true)*i;
            //console.log($('.'+settings.floorContentChild).outerHeight(true)*i);
            $.each($('.floor-label'),function(){
                var floorScroll=$(this).attr('id');
                var h=$(this).offset().top;
                
                if(floorId==floorScroll){
                    _this.addClass('active').siblings().removeClass('active');
                    var abc=h;
                    $('body,html').animate({'scrollTop':abc+'px'},800);
                }
            });
            //$('body,html').animate({'scrollTop':scrollFloorH+'px'},800);
        })
    };

    //获取url中的参数名就可以获取到参数的值
    $.getUrlParam
        = function(name)
    {
        var reg
            = new RegExp("(^|&)"+
            name +"=([^&]*)(&|$)");
        var r
            = window.location.search.substr(1).match(reg);
        if (r!=null) return unescape(r[2]); return null;
    }
    //楼层
    $.fn.scrollFloor=function(options){
        var defaults={
            floorNavMenu:'nav-floor',
            floorContent:'floor-content',
            floorContentChild:'floor',
            activeClass:'active'
        };
        var settings=$.extend(defaults,options);
        $(document).scroll(function(){
                
            var parentHeight=$('.'+settings.floorContent).height(),
                parentOffsetTop=$('.'+settings.floorContent)[0].offsetTop,
                childHeight=$('.'+settings.floorContentChild).outerHeight(true),
                docScrollTop=$(window).scrollTop();
                result=docScrollTop-parentOffsetTop;
                n=Math.floor(result/childHeight);
                console.log(n);
                if(result>=0){
                    
                    $('.'+settings.floorNavMenu).children().removeClass(settings.activeClass).eq(n).addClass(settings.activeClass);
                }
        });
        $('.'+settings.floorNavMenu).children().on('click',function(){
            var i=$(this).index();
            var scrollFloorH=$('.'+settings.floorContent)[0].offsetTop+$('.'+settings.floorContentChild).outerHeight(true)*i;
            $('body,html').animate({'scrollTop':scrollFloorH+'px'},800);
        })
    }

})(jQuery);

//限制input、textarea字数
var maximumWord = function(obj,max){
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

function swiper(elemObj){
    var swiper = new Swiper(elemObj, {
            slidesPerView: 5,
            spaceBetween: 50,
            // init: false,
            pagination: {
            el: '.swiper-pagination',
            clickable: true
        },
        breakpoints: {
        1024: {
            slidesPerView: 4,
            spaceBetween: 40
        },
        768: {
            slidesPerView: 3,
            spaceBetween: 30
        },
        640: {
            slidesPerView: 2,
            spaceBetween: 20
        },
        320: {
            slidesPerView: 1,
            spaceBetween: 10
        }
        }
    });
}
//活动倒计时
function countDown(time,id){
    console.log(time);
    var day_elem = id.find('.day');
    var hour_elem = id.find('.hour');
    var minute_elem = id.find('.minute');
    var second_elem = id.find('.second');
    var end_time = new Date(time).getTime(),//月份是实际月份-1
        sys_second = (end_time-new Date().getTime())/1000;
        //console.log(sys_second);
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
            countDown(getWeek(4),$('#countDownBox'));
            // $('.count_down_box').html('<span>本次活动已结束</span>');
        }
    }, 1000);
}
//获取每周五日期和时分秒
function getWeek(i) {
    var now = new Date();
    var nowTime=now.getTime();
    var day=now.getDay();
    var oneDayTime=24*60*60*1000;
    //显示周一
    //var firstDay=new Date(nowTime- (day- 4 )* oneDayTime);
    //console.log(firstDay);
    //显示周日
    // var SundayTime =new Date(nowTime); 
    // console.log(SundayTime);

    //显示周五
    var SundayTime =new Date((5-day)*oneDayTime+now.getTime()); 
    console.log(SundayTime);


    //firstDay.setDate(firstDay.getDate() + i);
    
    //console.log(firstDay.setDate(firstDay.getDate() + i));
    //日期
    //mon = Number(firstDay.getMonth())+1;
    //准确年月日
    mon = Number(firstDay.getMonth());
    //return now.getFullYear() + "/" + mon + "/" + firstDay.getDate()+" "+now.getHours()+":"+now.getMinutes()+":"+now.getSeconds();
    
    return SundayTime;
    
    //当天00：00：00
    // var endYear=new Date().getFullYear();
    // var endMonth=new Date().getMonth();
    // var endDay=new Date().getDate();
    // var endTime2=new Date(endYear,endMonth,endDay);
	// console.log(endTime2);
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
    // var button=document.getElementById('formLogin');
    // button.addEventListener('click',function(){
    //    $('input').focus();
    // });
    container.on('touchstart', function(e){
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

// 移除数组中的第二项
//array.remove(1);
// 移除数组中的倒数第二项
//array.remove(-2);
// 移除数组中的第二项和第三项（从第二项开始，删除2个元素）
//array.remove(1,2);
// 移除数组中的最后一项和倒数第二项（数组中的最后两项）
//array.remove(-2,-1);
Array.prototype.remove = function(from, to) {
    var rest = this.slice((to || from) + 1 || this.length);
    this.length = from < 0 ? this.length + from : from;
    return this.push.apply(this, rest);
};

//获取列表
function getList(config) {
    $.ajax({
        url: config.url,
        data: config.postData?config.postData:{},
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
            if(config.callBack){
                config.callBack(config,data);
            }else{
                getListDefaultCallBack(config,data)
            }
        }
    });
}
function getListDefaultCallBack(config,data) {
    if(data.status==0){
        dialog.error(data.info);
    }else{
        var container = config.container?config.container:$('ul.list');
        container.empty().append(data);
    }
}

//新增-表单提交
function dialogFormAdd(config) {
    $.ajax({
        url: config.url,
        data: config.postData,
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
            if(config.callBack){
                config.callBack(config,data);
            }else{
                dialogFormAddDefaultCallBack(config,data);
            }
        }
    });
}
//新增-表单提交-默认回调
function dialogFormAddDefaultCallBack(config,data) {
    if(data.status == 0){
        dialog.error(data.info);
    }else{
        var container = config.container?config.container:$('ul.list');
        container.prepend(data);
        container.find('.no-data').remove();
        layer.close(config.index);
    }
}

//修改-表单提交
function dialogFormEdit(config) {
    $.ajax({
        url: config.url,
        data: config.postData,
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
            if(config.callBack){
                config.callBack(config,data);
            }else{
                dialogFormEditDefaultCallBack(config,data);
            }
        }
    });
}
//修改-表单提交-默认回调
function dialogFormEditDefaultCallBack(config,data) {
    if(data.status == 0){
        dialog.error(data.info);
    }else{
        config.modifyObj.replaceWith(data);
        layer.close(config.index);
    }
}

//删除-表单提交
function dialogFormDel(config) {
    $.ajax({
        url: config.url,
        data: config.postData,
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
            if(config.callBack){
                config.callBack(config,data);
            }else{
                dialogFormDelDefaultCallBack(config,data);
            }
        }
    });
}
//删除-表单提交-默认回调
function dialogFormDelDefaultCallBack(config,data) {
    if(data.status == 0){
        dialog.error(data.info);
    }else{
        config.delObj.remove();
        layer.close(config.index);
    }
}

//文档就绪
$(function(){
    //返回顶部
    $('body').on('click','.backTop',function(){
        $('body,html').animate({scrollTop:0+'px'},500);
    });
    //窗口滚动条滚动
    $(window).on('scroll',function(){
        var scrollTop=$(document).scrollTop();
        if(scrollTop>=300){
            $('.fixedtop').addClass('active');
            $('.right_sidebar').show();
        }else{
            $('.right_sidebar').hide();
        }
    });
    //未开发菜单点击提示
    $('.top_menu_list .underdevelopment').on('click',function(){
        var index=$(this).index();
        if(index>0){
            dialog.error('功能正在开发中,暂未上线,敬请期待');
        }
    });
    $('body').on('click','.cpy_checkitem',function () {
        var _thisChecked = $(this).prop("checked");
        var oItem =$(this).parent().siblings('.item');
        $.each(oItem,function () {
            var _this=$(this);
            _this.find('.checkitem').prop('checked',_thisChecked);
        });
    });
    //根据公司反选
    $('body').on('click','.sign_checkitem',function () {
        var sign = true;
        var _this=$(this);
        var oItem =$(this).parents('li').find('.sign_checkitem');
        //一票否决
        $.each(oItem,function () {
            if(!$(this).prop('checked')){
                sign = false;
            }
        });
        _this.parents('li').find('.cpy_checkitem').prop('checked',sign);
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
    
});