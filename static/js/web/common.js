function showBg(ct) {
    var bH = $(document).height();
    $("#fullBg").css({ height: bH, display: "block" });
    $("#" + ct).css("visibility", "visible");
};

function closeBg(ct) {
    clearTimeout(timeout);
    $("#fullBg").css("display", "none");
    $("#photos").css("visibility", "hidden");
    $("#" + ct).css("visibility", "hidden");
};

function closeBgNew(ct, id) {
    $("#fullBg").css("display", "none");
    $("#photos" + "_" + id).css("visibility", "hidden");
    $("#" + ct).css("visibility", "hidden");
};

//input框只能输入数字
function clearNoNum(obj) {
    //先把非数字的都替换掉，除了数字和.
    obj.value = obj.value.replace(/[^\d.]/g, "");
    //必须保证第一个为数字而不是.
    obj.value = obj.value.replace(/^\./g, "");
    //保证只有出现一个.而没有多个.
    obj.value = obj.value.replace(/\.{2,}/g, ".");
    //保证.只出现一次，而不能出现两次以上
    obj.value = obj.value.replace(".", "$#$").replace(/\./g, "").replace("$#$", ".");
}
//点也不能出现
function clearNoNumNew(obj) {
    //先把非数字的都替换掉，除了数字和.
    obj.value = obj.value.replace(/[^\d]/g, "");
    //必须保证第一个为数字而不是.
    obj.value = obj.value.replace(/^\./g, "");
    //保证只有出现一个.而没有多个.
    obj.value = obj.value.replace(/\.{,}/g, ".");
    //保证.只出现一次，而不能出现两次以上
    obj.value = obj.value.replace(".", "$#$").replace(/\./g, "").replace("$#$", ".");
}

//留言弹出
function showMsgBg(ct, ansyid) {
    $("#AnysID").val(ansyid);
    var bH = $(document).height();
    $("#fullBg").css({ height: bH, display: "block" });
    $("#" + ct).css("visibility", "visible");
};


function FormatExt(number) {
    var s = parseInt(number);
    s += "";
    if (s.indexOf(".") == -1) s += ".0"; //如果没有小数点，在后面补个小数点和0
    if (/\.\d$/.test(s)) s += "0";   //正则判断
    while (/\d{4}(\.|,)/.test(s))  //符合条件则进行替换
        s = s.replace(/(\d)(\d{3}(\.|,))/, "$1,$2"); //每隔3位添加一个，
    return s.substring(0, s.indexOf('.'));
}

//显示关闭剩余秒数
var start = 5;
var timeout;
function countdown(timeTarget, dialogTarget) {
    $("#" + timeTarget).html(start);
    if (start > 0) {
        start = start - 1;
        timeout = setTimeout("countdown('" + timeTarget + "','" + dialogTarget + "')", 1000);
    } else {
        closeBg(dialogTarget);
        start = 5;
    }
}

function priceYear(para, obj) {
    var y = para.year;
    var m = para.month;
    var reversePrices = para.prices;

    var date = new Date();
    var j = 1;
    var labels = [];

    if (typeof (reversePrices) != "undefined") {
        reversePrices = reversePrices.reverse();
    }
    var priceLength = reversePrices.length;

    for (var i = 0; i < priceLength; i++) {
        if (i % 4 == 0 || i == 0) {
            var p = reversePrices[i];
            if (typeof (p) != "undefined") {
                date.setFullYear(y);
                date.setMonth(m - j);
                labels.push(date.getFullYear() + "年");
                j++;
            }
        }
    }
    $(obj).html(labels[labels.length - 1]);
}

function dateFormat(val) {
    if (val != null) {
        var date = new Date(parseInt(val.replace("/Date(", "").replace(")/", ""), 10));
        //月份为0-11，所以+1，月份小于10时补个0   
        var month = date.getMonth() + 1 < 10 ? "0" + (date.getMonth() + 1) : date.getMonth() + 1;
        var currentDate = date.getDate() < 10 ? "0" + date.getDate() : date.getDate();
        var hours = date.getHours() < 10 ? "0" + date.getHours() : date.getHours();
        var minutes = date.getMinutes() < 10 ? "0" + date.getMinutes() : date.getMinutes();
        return date.getFullYear() + "-" + month + "-" + currentDate + " " + hours + ":" + minutes;
    }
    return "";
}

function checkAccount(flag, account, type) {
    var $this = account;
    var phone = $this.val();
    if (phone) {
        if (phone.length == 0) {
            if (flag) {
                return false;
            }
            $this.parent().next("span").html("<s></s>请输入正确的手机号码");
            $this.parent().parent().addClass("dd_error");
            return false;
        }
        if (!/^\d{11}$/.test(phone)) {
            $this.parent().next("span").html("<s></s>请输入正确的手机号码");
            $this.parent().parent().addClass("dd_error");
            return false;
        }
        var result = false;
        $.ajax({
            type: 'post',
            url: "/user/index/checkUser",
            data: { account: phone },
            dataType: 'json',
            async: false,
            success: function (data) {
                //如果是注册新用户，找到了对应的记录提示
                if (type == 0 && data.data.code == 0) {
                    $this.parent().next("span").html("<s></s>该手机号已存在，<a href='/user/index/login'>登录</a>");
                    $this.parent().parent().addClass("dd_error");
                    result = false;
                } else if (type == 1 && data.data.code == 1) {
                    $this.parent().next("span").html("<s></s>您输入的手机号码未找到对应记录");
                    $this.parent().parent().addClass("dd_error");
                    result = false;
                } else if (type == 2 && data.data.code == 0) {
                    $this.parent().next("span").html("<s></s>该手机号已存在，请重新填写");
                    $this.parent().parent().addClass("dd_error");
                    result = false;
                } else {
                    result = true;
                }
            }
        });
        return result;
    } else {
        if (flag) {
            return false;
        }
        $this.parent().next("span").html("<s></s>请输入正确的手机号码");
        $this.parent().parent().addClass("dd_error");
        return false;
    }
}

function checkPwd(flag, pwd) {
    var $this = pwd;
    pwd = $this.val();
    if (pwd) {
        if (pwd.length == 0) {
            if (flag) {
                return false;
            }
            $this.parent().next("span").html("<s></s>请输入密码");
            $this.parent().parent().addClass("dd_error");
            return false;
        }
        if (pwd.length < 6) {
            $this.parent().next("span").html("<s></s>密码格式不正确");
            $this.parent().parent().addClass("dd_error");
            return false;
        }
        return true;
    } else {
        $this.parent().next("span").html("<s></s>请输入密码");
        $this.parent().parent().addClass("dd_error");
        return false;
    }
}

function checkConfirmPwd(flag, password, confirmPwd) {
    var checkResult = checkPwd(false, password);
    if (!checkResult) {
        return false;
    }
    var $this = confirmPwd;
    pwd = $this.val();
    if (pwd) {
        if (pwd.length == 0) {
            if (flag) {
                return false;
            }
            $this.parent().next("span").html("<s></s>请输入确认密码");
            $this.parent().parent().addClass("dd_error");
            return false;
        }
        if (pwd != password.val()) {
            $this.parent().next("span").html("<s></s>两次密码输入不一致，请重新确认");
            $this.parent().parent().addClass("dd_error");
            return false;
        }
        return true;
    } else {
        $this.parent().next("span").html("<s></s>请输入确认密码");
        $this.parent().parent().addClass("dd_error");
        return false;
    }
}

function checkVCode(flag, code, account, type) {
    if (type != 4) {
        var checkResult = checkAccount(false, account, type);
        if (!checkResult) {
            return false;
        }
    }
    var $this = code;
    code = $this.val();
    if (code) {
        if (code.length < 6 || !/^[0-9]{6}$/.test(code)) {
            $this.parent().next().next("span").html("<s></s>请输入6位短信验证码");
            $this.parent().parent().addClass("dd_error");
            return false;
        }

        //if (type == 4) {
            //return verifyCode(account.val(), $this, type);
        //} else {
            return verifyCode(account.val(), $this, type);
       // }

    } else {
        $this.parent().next().next("span").html("<s></s>请输入6位短信验证码");
        $this.parent().parent().addClass("dd_error");
        return false;
    }
}

var interval = 0;
var seconds;
var send = true;
function sendSMS(type) {
    if (!send) {
        return;
    }

    var tel = $("#account");
    if (!/^\d{11}$/.test(tel.val())) {
        return;
    }

    if (type != 4) {
        var checkAccountResult = checkAccount(false, tel, type);
        if (!checkAccountResult) {
            return;
        }
    }

    seconds = 30;
    interval = window.setInterval(function () {
        seconds--;
        if (seconds <= 0) {
            clearInterval(interval);
            $("#getVCode").attr("href", "javascript:sendSMS(" + type + ")");
            $("#getVCode").html("获取验证码");
            send = true;
        } else {
            $("#getVCode").attr("href", "javascript:void(0)");
            $("#getVCode").html(seconds + "秒钟后重新获取");
            if (seconds == 29) {
                $("#getVCode").parents("dd").addClass("dd_error");
                $("#getVCode").next().html("<s></s>验证码已发送，请查收短信");
            }

        }
    }, 1000);
    send = false;
    $.post("/user/index/getCode",{ mobile: tel.val(),type:type}, function (data) {
    });

}
function loginSubmit(flag, sourse) {
    var phone = $.trim($("#account").val());
    var accountResult = checkAccount(flag, $("#account"), 1);
    if (!accountResult) {
        return;
    }
    var pwdResult = checkPwd(flag, $("#pwd"));
    if (!pwdResult) {
        return;
    }

    $.post("/user/index/loginUser", { mobile: phone, pwd: $.trim($("#pwd").val()), remb: $("#remb").val() }, function (result) {
        if (result.data.msg == 'ok') {
            //if (sourse == "partial") {
                //location.href = location.href;
            //} else {
                location.href = "/user/index/userInfo";
            //}

        } else if (result.data.msg != 'ok') {
            $("#account").parent().next("span").html("<s></s>"+result.data.msg );
            $("#account").parent().parent().addClass("dd_error");
            $("#pwd").val("");

        }
        else {
            alert("服务器忙，请稍后再试！");
        }
    },'json');
}

function verifyCode(tel, code, type) {
    var $this = code;
    var result = false;
    $.ajax({
        type: 'POST',
        url: "/user/index/checkCode",
        data:{mobile:tel,code: $this.val(),type:type},
        dataType: 'json',
        async: false,
        success: function (data) {
            if (data.data.status) {
                result = false;
                $this.parent().next().next("span").html("<s></s>短信验证码错误或者已过期");
                $this.parent().parent().addClass("dd_error");
            } else {
                result = true;
            }

        }
    });
    return result;
}


//获取url参数值
function getQueryString(name) {
    var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)", "i");
    var u = decodeURI(window.location.search.substr(1));
    u = u.replace(/\s+/g, "");
    var r = u.match(reg);
    if (r != null) return decodeURI(r[2]); return null;
}
function get_scrollTop_of_body() {
    var scrollTop;
    if (typeof window.pageYOffset != 'undefined') {//pageYOffset指的是滚动条顶部到网页顶部的距离
        scrollTop = window.pageYOffset;
    } else if (typeof document.compatMode != 'undefined' && document.compatMode != 'BackCompat') {
        scrollTop = document.documentElement.scrollTop;
    } else if (typeof document.body != 'undefined') {
        scrollTop = document.body.scrollTop;
    }
    return scrollTop;
}

function stopPropagation(e) {
    e = e || window.event;
    if (e.stopPropagation) {
        e.stopPropagation();
    } else {
        e.cancelBubble = true;
    }
}

$.fn.setCursorPosition = function (position) {
    if (this.lengh == 0) return this;
    return $(this).setSelection(position, position);
};
$.fn.setSelection = function (selectionStart, selectionEnd) {
    if (this.lengh == 0) return this;
    input = this[0];

    if (input.createTextRange) {
        var range = input.createTextRange();
        range.collapse(true);
        range.moveEnd('character', selectionEnd);
        range.moveStart('character', selectionStart);
        range.select();
    } else if (input.setSelectionRange) {
        input.focus();
        input.setSelectionRange(selectionStart, selectionEnd);
    }

    return this;
};

function deleteCookie(name, value, domain) {
    var date = new Date();
    date.setTime(date.getTime() + (-1 * 24 * 60 * 60 * 1000));
    var expires = "; expires=" + date.toGMTString();
    var cstr = name + "=" + value + expires + "; path=/; domain=" + (domain || "");
    document.cookie = cstr;
}

function getCookie(c_name) {
    if (document.cookie.length > 0) {
        c_start = document.cookie.indexOf(c_name + "=");
        if (c_start != -1) {
            c_start = c_start + c_name.length + 1;
            c_end = document.cookie.indexOf(";", c_start);
            if (c_end == -1) c_end = document.cookie.length;
            return unescape(document.cookie.substring(c_start, c_end));
        }
    }
    return "";
}

function phoneFormat(phone) {
    try {
        var reg = /^[1][3-8]+\d{9}/;
        if (reg.test(phone)) {
            return phone.replace(/(\d{3})\d{4}(\d{4})/, "$1****$2");
        }

    } catch (e) {

    }
    return phone;
}

function myPager(id, pageTotal, callback, pageSize, currentPage) {
    $("#" + id).pagination(pageTotal, {
        callback: callback,
        prev_text: '上一页',
        next_text: '下一页',
        items_per_page: pageSize,
        num_display_entries: 6,
        num_edge_entries: 1,
        current_page: currentPage - 1
    });
}
function unixTimeFormat(unixtime, defaultFormat) {
    if (!defaultFormat) {
        defaultFormat = "-";
    }
    var timestr = new Date(parseInt(unixtime) * 1000);
    var hour = timestr.getHours();
    var minutes = timestr.getMinutes();
    var datetime = timestr.getFullYear() + defaultFormat + (timestr.getMonth() + 1) + defaultFormat + timestr.getDate() + " " + (hour > 9 ? hour : "0" + hour) + ":" + (minutes > 9 ? minutes : "0" + minutes);
    return datetime;
}

function submitGoodCount(name, id, analystsid) {
    var txt = $("#" + "goodcoun" + id).text();
    var goodcount;
    if (txt != "赞")
    { goodcount = parseInt($("#" + "goodcoun" + id).text()); }
    else {
        goodcount = 0;
    }

    var goodcount1 = parseInt($("#" + "goodcoun_1" + id).text());
    if (!goodcount1) {
        goodcount1 = 0;
    }

    $.ajax({
        type: 'POST',
        url: '/feedback/AnalystsListUp',
        dateType: 'json',
        data: { AnalystsID: analystsid },
        async: false,
        success: function (result) {
            if (result > 0) {
                $("#goodsrc" + id).addClass("gayOver");
                goodcount = goodcount + 1;
                $("#" + "goodcoun_1" + id).html(goodcount1 + 1);
                $("#" + "goodcoun" + id).html("<s></s>" + goodcount);
                $("#" + "goodcoun_1" + id).parent().parent().removeAttr("onclick");
                $("#" + "goodcoun_1" + id).parent().removeAttr("onclick");
                $("#" + "goodsrc" + id).parent().css("border-color", "#999");
                $("#" + "goodsrc" + id).next().css("color", "#999");
                $("#" + "goodsrc" + id).css("background-color", "#999");
                $("#" + "goodsrc" + id).removeAttr('href');
                $("#" + "goodsrc" + id).removeAttr('onclick');
                $("#" + "goodsrc" + id).parent().removeAttr('onclick');
            }
            else {
                alert("服务器忙,请稍后再试！");
            }
        }
    });

}
function submitAnalystsListAdd() {
    var s = $("#hideMessage").html();
    var userPhone = $("#txtUserPhone").val();
    var userEmail = $("#txtUserEmail").val();
    var userName = $("#tUserName").val();
    var remark = $("#remark").val();
    var ansyid = $("#AnysID").val();
    if ($.trim(remark).length == 0 || $.trim(userName).length == 0 || $.trim(userPhone).length == 0 || $.trim(userEmail).length == 0) {
        alert("请输入完整信息！");
        return;
    }
    var regPhone = /^1[34568]\d{9}$/;
    if (!regPhone.test(userPhone)) {
        alert("手机号码格式不正确，请重新输入，谢谢！");
        return;
    }
    var reg = /^([a-zA-Z0-9]+[_|\_|\.]?)*[a-zA-Z0-9]+@([a-zA-Z0-9]+[_|\_|\.]?)*[a-zA-Z0-9]+\.[a-zA-Z]{2,3}$/;
    if (!reg.test(userEmail)) {
        alert("邮件格式不正确，请重新输入，谢谢！");
        return;
    }

    $.ajax({
        type: 'POST',
        url: '/feedback/AnalystsListAdd',
        dateType: 'json',
        data: { remark: remark, userName: userName, userEmail: userEmail, userPhone: userPhone, anlysId: ansyid },
        async: false,
        success: function (result) {
            if (result > 0) {
                $("#hideMessage").html(" ");
                showBg('submitSuccess');

                var timeOut = setTimeout(function () {
                    $("#showMessage").css("visibility", "hidden");
                    $("#fullBg").hide();
                }, 1000);
                timeOut = setTimeout(function () {
                    closeBg('submitSuccess');
                    $("#hideMessage").html(s);
                    if (timeOut) {
                        clearTimeout(timeOut);
                    }
                }, 1000);
                $("#txtUserPhone").val("");
                $("#txtUserEmail").val("");
                $("#tUserName").val("");
                $("#remark").val("");

            } else {
                $("#showMessage").css("visibility", "hidden");
                alert("服务器忙,请稍后再试！");
            }
        }
    });

}

var BuildJS = {
    buildHtml: function (data, templateHtml) {
        var html = templateHtml;
        if (!html) {
            return "";
        }
        html = html.replace(/\.srcX/gi, ".src");
        for (var p in data) {
            var reg = new RegExp("\\$" + p + "\\b", "gi");
            html = html.replace(reg, data[p] || "-");
        }
        return html;
    }
};
var User = {
    logout: function () {
        if (WB2.checkLogin()) {
            WB2.logout(function () {
                window.location = "/logout";
            });
        } else {
            window.location = "/logout";
        }
    }
};
$(function () {
    $("#login_username").hover(function () {
        $(this).parent().next().show();
    }, function () {
        var timeout = setTimeout(function () {
            clearTimeout(timeout);
            $("#login_username").parent().next().hide();
        }, 300);

    $(this).parent().next().hover(function () {
            clearTimeout(timeout);
            $(this).show();
        }, function () {
            $(this).hide();
        });
    });
});
//格式化数字，添加千分位分隔符
function formatPrice(inputStr) {
    var s = parseInt(inputStr);

    s = s.toString().replace(/^(\d*)$/, "$1.");
    s = (s + "00").replace(/(\d*\.\d\d)\d*/, "$1");
    s = s.replace(".", ",");
    var re = /(\d)(\d{3},)/;
    while (re.test(s))
        s = s.replace(re, "$1,$2");
    s = s.replace(/,(\d\d)$/, ".$1");

    var arr = s.split('.');
    if (arr[1] == "00") {
        s = arr[0];
    }
    return s;
}