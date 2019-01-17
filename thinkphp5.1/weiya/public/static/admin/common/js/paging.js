//获取分页列表-公共回调函数
function commonCallBack(config,data){
    config.container.html(data);
}
/**获取分页列表
 * @param config  必须是全局变量
 *例子
 * var config = {
		currentPage:1,//必须配置项
		url:module+'goods/getList', //非必须配置项，默认为当前方法
		callBack:callBack //非必须配置项，默认为commonCallBack
	};
 * @param getData 提交数据
 */
function getPagingList(config,getData) {
    //容器
    config.container = config.container?config.container:$("#list");
    //提交路径
    config.url = config.url?config.url:action;
    //回调函数名
    config.callBack = config.callBack?config.callBack:commonCallBack;
    //要提交的数据
    getData = getData?getData:$('#form1').serializeObject();
    getData.page = config.currentPage ? config.currentPage : 1;
    getData.pageSize = getData.pageSize ? getData.pageSize: 10;
    $.ajax({
        url: config.url,
        data: getData,
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
            config.callBack(config,data);
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
