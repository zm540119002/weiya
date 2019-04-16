/**
 * 验证是否手机号码
 * @param inputString
 * @returns {boolean}
 */
function isMobilePhone(inputString) {
    var reg = /^((13\d|14[57]|15[^4,\D]|17[678]|18\d)\d{8}|170[059]\d{7})$/;
    return reg.test(inputString);
}

/**
 * 密码长度检测及字符
 */
function checkValidPasswd(str) {
    var reg = /^[x00-x7f]+$/;
    if (!reg.test(str)) {
        return false;
    }
    if (str.length < 6 || str.length > 16) {
        return false;
    }
    return true;
}

/**
 * 数字或者字母
 * @param str
 * @param len 长度
 */
function isNumber(str,startLen,endLen){
	var reg =  /^[0-9a-zA-Z]+$/;
    if (!reg.test(str)) {
        return false;
    }
    if (str.length < (startLen ? startLen : 1) || str.length > (endLen ? endLen : 10) ) {
        return false;
    }
    return true;
}

/**
 * 验证邮箱
 * @param str
 * @returns {boolean}
 */
function isEmail(str) {
    var reg = /^([a-zA-Z0-9_-])+@([a-zA-Z0-9_-])+((\.[a-zA-Z0-9_-]{2,3}){1,2})$/;
    return reg.test(str);
}

/**
 * 验证固定电话
 * @param str
 * @returns {boolean}
 */
function isPhone(str) {
    var reg = /^((0\d{2,3})-)(\d{7,8})(-(\d{3,}))?$/;
    return reg.test(str);
}

/**
 * QQ号的验证
 * @param qq
 * @returns {boolean}
 */
function isQQ(qq) {
    var bValidate = RegExp(/^[1-9][0-9]{4,9}$/).test(qq);
    if (bValidate) return true;
    else return false;
}

/**
 * 正整数 (大于0的整数)
 * @returns {boolean}
 */
function isPosIntNumber(number) {
    //var g = /^[1-9]*[1-9][0-9]*$/;
    var reg = /^[1-9]\d*$/;
    return reg.test(number);
}

/**
 * 正整数或0
 * @returns {boolean}
 */
function isPosIntNumberOrZero(number) {
    //var g = /^[1-9]*[1-9][0-9]*$/;
    var reg = /^[0-9]\d*$/;
    return reg.test(number);
}

/**
 * 正数 (大于0的数,可以是小数)
 * @returns {boolean}
 */
function isPosNumber(number) {
    var reg = /^\d+(?=\.{0,1}\d+$|$)/;
    return reg.test(number);
}

/**
 * 自然数(非负整数)
 * @returns {boolean}
 */
function isNaturalNumber(number) {
    //var reg = /^([1-9]\d*|[0]{1,1})$/;
    var reg = /^\d+$/;
    return reg.test(number);
}

/**
 * 验证金额
 */
function isMoney(money) {
    var g = /^([1-9][\d]{0,7}|0)(\.[\d]{1,2})?$/;
    return g.test(money);
}

//数字转为两位小数
function changeTwoDecimal(x) {
    var f_x = parseFloat(x);
    if (isNaN(f_x)) {
        return 0;
    }
    var f_x = Math.round(x * 100) / 100;
    var s_x = f_x.toString();
    var pos_decimal = s_x.indexOf('.');
    if (pos_decimal < 0) {
        pos_decimal = s_x.length;
        s_x += '.';
    }
    while (s_x.length <= pos_decimal + 2) {
        s_x += '0';
    }
    return s_x;
}
//检查账号
function checkAccount(username) {
    //var g = /^[1-9]*[1-9][0-9]*$/;
    var reg = /^\w{6,16}$/;
    return reg.test(username);
}

/**
 * 和PHP一样的时间戳格式化函数
 * @param  {string} format    格式
 * @param  {int}    timestamp 要格式化的时间 默认为当前时间
 * @return {string}           格式化的时间字符串
 */
function date(format, timestamp) {
    var a, jsdate = ((timestamp) ? new Date(timestamp * 1000) : new Date());
    var pad = function (n, c) {
        if ((n = n + "").length < c) {
            return new Array(++c - n.length).join("0") + n;
        } else {
            return n;
        }
    };
    var txt_weekdays = ["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"];
    var txt_ordin = {1: "st", 2: "nd", 3: "rd", 21: "st", 22: "nd", 23: "rd", 31: "st"};
    var txt_months = ["", "January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
    var f = {
        // Day
        d: function () {
            return pad(f.j(), 2)
        },
        D: function () {
            return f.l().substr(0, 3)
        },
        j: function () {
            return jsdate.getDate()
        },
        l: function () {
            return txt_weekdays[f.w()]
        },
        N: function () {
            return f.w() + 1
        },
        S: function () {
            return txt_ordin[f.j()] ? txt_ordin[f.j()] : 'th'
        },
        w: function () {
            return jsdate.getDay()
        },
        z: function () {
            return (jsdate - new Date(jsdate.getFullYear() + "/1/1")) / 864e5 >> 0
        },
        // Week
        W: function () {
            var a = f.z(), b = 364 + f.L() - a;
            var nd2, nd = (new Date(jsdate.getFullYear() + "/1/1").getDay() || 7) - 1;
            if (b <= 2 && ((jsdate.getDay() || 7) - 1) <= 2 - b) {
                return 1;
            } else {
                if (a <= 2 && nd >= 4 && a >= (6 - nd)) {
                    nd2 = new Date(jsdate.getFullYear() - 1 + "/12/31");
                    return date("W", Math.round(nd2.getTime() / 1000));
                } else {
                    return (1 + (nd <= 3 ? ((a + nd) / 7) : (a - (7 - nd)) / 7) >> 0);
                }
            }
        },
        // Month
        F: function () {
            return txt_months[f.n()]
        },
        m: function () {
            return pad(f.n(), 2)
        },
        M: function () {
            return f.F().substr(0, 3)
        },
        n: function () {
            return jsdate.getMonth() + 1
        },
        t: function () {
            var n;
            if ((n = jsdate.getMonth() + 1) == 2) {
                return 28 + f.L();
            } else {
                if (n & 1 && n < 8 || !(n & 1) && n > 7) {
                    return 31;
                } else {
                    return 30;
                }
            }
        },
        // Year
        L: function () {
            var y = f.Y();
            return (!(y & 3) && (y % 1e2 || !(y % 4e2))) ? 1 : 0
        },
        //o not supported yet
        Y: function () {
            return jsdate.getFullYear()
        },
        y: function () {
            return (jsdate.getFullYear() + "").slice(2)
        },
        // Time
        a: function () {
            return jsdate.getHours() > 11 ? "pm" : "am"
        },
        A: function () {
            return f.a().toUpperCase()
        },
        B: function () {
            // peter paul koch:
            var off = (jsdate.getTimezoneOffset() + 60) * 60;
            var theSeconds = (jsdate.getHours() * 3600) + (jsdate.getMinutes() * 60) + jsdate.getSeconds() + off;
            var beat = Math.floor(theSeconds / 86.4);
            if (beat > 1000) beat -= 1000;
            if (beat < 0) beat += 1000;
            if ((String(beat)).length == 1) beat = "00" + beat;
            if ((String(beat)).length == 2) beat = "0" + beat;
            return beat;
        },
        g: function () {
            return jsdate.getHours() % 12 || 12
        },
        G: function () {
            return jsdate.getHours()
        },
        h: function () {
            return pad(f.g(), 2)
        },
        H: function () {
            return pad(jsdate.getHours(), 2)
        },
        i: function () {
            return pad(jsdate.getMinutes(), 2)
        },
        s: function () {
            return pad(jsdate.getSeconds(), 2)
        },
        //u not supported yet
        // Timezone
        //e not supported yet
        //I not supported yet
        O: function () {
            var t = pad(Math.abs(jsdate.getTimezoneOffset() / 60 * 100), 4);
            if (jsdate.getTimezoneOffset() > 0) t = "-" + t; else t = "+" + t;
            return t;
        },
        P: function () {
            var O = f.O();
            return (O.substr(0, 3) + ":" + O.substr(3, 2))
        },
        //T not supported yet
        //Z not supported yet
        // Full Date/Time
        c: function () {
            return f.Y() + "-" + f.m() + "-" + f.d() + "T" + f.h() + ":" + f.i() + ":" + f.s() + f.P()
        },
        //r not supported yet
        U: function () {
            return Math.round(jsdate.getTime() / 1000)
        }
    };
    return format.replace(/[\\]?([a-zA-Z])/g, function (t, s) {
        if (t != s) {
            // escaped
            ret = s;
        } else if (f[s]) {
            // a date function exists
            ret = f[s]();
        } else {
            // nothing special
            ret = s;
        }
        return ret;
    });
}

/**表单转json对象
 */
$.fn.serializeObject = function() {
    var o = {};
    var a = this.serializeArray();
    $.each(a, function(index,val) {
        if (o[this.name]) {
            if (!o[this.name].push) {
                o[this.name] = [ o[this.name] ];
            }
            o[this.name].push(this.value || '');
        } else {
            o[this.name] = this.value || '';
        }
    });
    //解决checkbox未选中时，没有序列化到对象中的代码
    var checkboxes = $(this).find('input[type=checkbox]');
    $.each(checkboxes, function () {
        if (o[this.name]) {
            if (!o[this.name].push) {
                o[this.name] = [o[this.name]];
            }
            if($(this).prop('checked')) {
                o[this.name].push('1');
            }else{
                o[this.name].push('0');
            }
        }else{
            if($(this).prop('checked')){
                o[this.name] = '1';
            }else{
                o[this.name] = '0';
            }
        }
    });
    return o;
};
//jquery 扩展
$.extend({
    isEmptyArray: function(arr) {
        return (Array.prototype.isPrototypeOf(arr) && arr.length === 0)?true:false;
    },
    isEmptyObject: function(obj) {
        return (Object.prototype.isPrototypeOf(obj) && Object.keys(obj).length === 0)?true:false;
    }
});

/**清空表单
 * $obj jquery 对象
 */
function clearForm($obj){
	$obj
		.find(':input')
		.not(':button, :submit, :reset')
	 	.val('')
	 	.removeAttr('checked')
	 	.removeAttr('selected')
	 	.removeAttr('disabled');
}

function create_code(callback){
    var code = Math.floor(Math.random() * 999999999999);
    callback(code);
}

//注册验证
var register={
    phoneCheck:function(phoneStr){
        var patrn = /^((?:13|15|18|14|17)\d{9}|0(?:10|2\d|[3-9]\d{2})[1-9]\d{6,7})$/;
        if(patrn.test(phoneStr)){
            return true;
        }else{
            return false;
        }
    },
    vfyCheck:function(vfyStr){
        var vfy=/^\d{6}$/;
        if(vfy.test(vfyStr)){
            return true;
        }else{
            return false;
        }
    },
    pswCheck:function(pswStr){
        var pswReg = /^[A-Za-z0-9]{6,16}$/;
        if(pswReg.test(pswStr)){
            return true;
        }else{
            return false;
        }
    }
};

//获取对象长度
function objectLength(o) {
    var len = 0;
    for(var item in o){
        len ++ ;
    }
    return len;
}

/**根据name在对象表单之间复制数据
 * @param fromObj 复制数据来源（jquery）对象
 * @param toObj 复制数据目的（jquery）对象
 */
function copyDataByName(fromObj,toObj) {
    $.each(toObj.find(':input'),function(){
        var name = $(this).attr('name');
        var val= fromObj.find('[name='+name+']').val();
        $(this).val(val);
    });
}
function copyDataByClassName(fromObj,toObj){
    $.each(toObj.find('.span_text'),function(){
        var name = $(this).attr('name');
        var val= fromObj.find('span[name='+name+']').text();
        $(this).text(val);
    });
}

/**
 * 刷新当前页面
 */
function flushPage() {
    location.reload();
}

/**去除输入框空格 */
function trim(str,is_global){
    var result;
    result = str.replace(/(^\s+)|(\s+$)/g,"");
    if(is_global.toLowerCase()=="g")
    {
        result = result.replace(/\s/g,"");
    }
    return result;
}

//判断是否是微信浏览器的函数
function isWeiXin(){
    //window.navigator.userAgent属性包含了浏览器类型、版本、操作系统类型、浏览器引擎类型等信息，这个属性可以用来判断浏览器类型
    var ua = window.navigator.userAgent.toLowerCase();
    //通过正则表达式匹配ua中是否含有MicroMessenger字符串
    if(ua.match(/MicroMessenger/i) == 'micromessenger'){
        return true;
    }else{
        return false;
    }
}

function sum(arr) {
    var len = arr.length;
    if(len == 0){
        return 0;
    } else if (len == 1){
        return arr[0];
    } else {
        return arr[0] + sum(arr.slice(1));
    }
}