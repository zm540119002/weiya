//获取分页列表-公共回调函数
function commonCallBack(config,data){
    if(config.currentPage == 1){
         config.container.find('li').remove();
        config.container.append(data);
        if(config.type=='sort'){
            config.disableBtn();
        }
    }else{
        if(config.type=='click'){
            console.log(typeof data.length);
        }
        config.container.find('li:last').after(data);
        if(config.type=='sort'){
            config.disableBtn();
        }
    }
}

/**获取分页列表
 * @param config  必须是全局变量
 *例子
 * var config = {
        requestEnd:false,//必须配置项
		loadTrigger:false,//必须配置项
		currentPage:1,//必须配置项
		url:module+'goods/getList', //非必须配置项，默认为当前方法
		callBack:callBack //非必须配置项，默认为commonCallBack
	};
 * @param postData 提交数据
 */
function getPagingList(config,postData) {
    //容器
    config.container = config.container?config.container:$("#list");
    //提交路径
    config.url = config.url?config.url:action;
    //回调函数名
    config.callBack = config.callBack?config.callBack:commonCallBack;
    //要提交的数据
    postData = postData?postData:$('#form1').serializeObject();
    postData.page = postData.currentPage ? postData.currentPage : config.currentPage;
    postData.pageSize = postData.pageSize ? postData.pageSize:4;
    //请求结束标志
    if(config.requestEnd){
        $('.ctype-title').remove();
        config.container.after('<div class="ctype-title"><span class="line"></span><span class="txt f24">已到底部，加载完！</span><span class="line"></span></div>');
        config.loadTrigger = true;
        return false;
    }
    $.ajax({
        url: config.url,
        data: postData,
        type: 'get',
        beforeSend: function(){
            $('.loading').show();
        },
        error:function (xhr) {
            $('.loading').hide();
            dialog.error('AJAX错误');
        },
        success: function(data){
            $('.loading').hide();
            $('.ctype-title').remove();
            config.callBack(config,data);
            if($($.parseHTML(data)).length<postData.pageSize){
                config.requestEnd = true;
            }
            config.currentPage ++;
            config.loadTrigger = true;
        }
    });
}
//禁用移动按钮
var disableBtn=function disableBtn(){
    var listUl = $('#list');
    listUl.find('li').find('.move-btn').removeProp('disabled');
    listUl.find('li:first').find('.up-btn').prop('disabled','disabled').addClass('disabled');
    listUl.find('li:last').find('.down-btn').prop('disabled','disabled').addClass('disabled');
    listUl.find('li:last').find('.down-btn').addClass('down-disabled-icons');
};



