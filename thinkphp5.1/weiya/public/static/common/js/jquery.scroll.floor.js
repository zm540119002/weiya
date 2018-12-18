(function(window){
	var defaults = {
		floorClass : '.scroll-floor',
		navClass : '.scroll-nav',
		activeClass : 'active',
		activeTop : 100,
		scrollTop : 100,
		delayTime : 200
	};
	
	var $body = $('body'),data = [];
	function getItem(_list,newOptions){
		_list.each(function() {
            var item = {};
            item.$obj = $body.find(this);
            item.$activeTop = $body.find(this).offset().top - newOptions.activeTop;
            item.$scrollTop = $body.find(this).offset().top + newOptions.scrollTop-100;
            console.log($body.find(this).offset().top);
            data.push(item);
        });
	}
	
	function scrollActive(_list,newOptions){
		var nowScrollTop = $(window).scrollTop();
		$.each(data,function(i,item){
			if(nowScrollTop > item.$activeTop){
				_list.removeClass(newOptions.activeClass).eq(i).addClass(newOptions.activeClass);
			}
		});
	}
	
	var scroll_floor = window.scrollFloor = function(options){
		var newOptions = $.extend({}, defaults, options);
		var floorList = $body.find(newOptions.floorClass),navList = $body.find(newOptions.navClass);
		
		getItem(floorList,newOptions);
		scrollActive(navList,newOptions);
		
        $(window).on('scroll',function(){scrollActive(navList,newOptions);});
        
        navList.on('click',function(){
        	var _index = $body.find(this).index();
        	$('html,body').animate({'scrollTop' : data[_index].$scrollTop},newOptions.delayTime);
        });
		
	}
})(window);
