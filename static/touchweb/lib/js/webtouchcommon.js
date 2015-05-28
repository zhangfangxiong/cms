define(["jquery"], function () {
    return webtouchObj = {
        showornot: 0,
        canvasload: function () {
            //canvas加载图片
            var imglength = $("#mainsection").find("canvas").length;
            if (imglength > 0) {
                $("#mainsection").find("canvas").each(function () {
                    var imgSrc = $(this).data("src");
                    var imageObj = new Image();
                    imageObj.canvs = $(this)[0];
                    var cvs = imageObj.canvs.getContext('2d');
                    if (cvs) {
                        imageObj.onload = function () {
                            imageObj.canvs.width = this.width;
                            imageObj.canvs.height = this.height;
                            cvs.drawImage(this, 0, 0);
                            $(imageObj.canvs).css("background-image", "none");
                        }
                        imageObj.src = imgSrc;
                    }
                })
            }
        },
        backtoTop: function () {
            var screenheight = $(window).height();
            if ($(window).scrollTop() > screenheight) {
                $('#backtop').fadeIn();
            } else {
                $('#backtop').fadeOut();
            }
        },
        backtoTopdh: function () {
            $('body,html').animate({
                scrollTop: 0
            }, 500);
        },
        trim: function (text) {
            if (typeof (text) == "string")
            {
                return text.replace(/^\s*|\s*$/g, "");
            }
            else
            {
                return text;
            }
        },
        formatPrice: function (n) {
            var t = parseInt(n), i, r;
            for (t = t.toString().replace(/^(\d*)$/, "$1."), t = (t + "00").replace(/(\d*\.\d\d)\d*/, "$1"), t = t.replace(".", ","), i = /(\d)(\d{3},)/; i.test(t); )
                t = t.replace(i, "$1,$2");
            return t = t.replace(/,(\d\d)$/, ".$1"), r = t.split("."), r[1] == "00" && (t = r[0]), t
        },
        topSelect: function () {
            var classes = $(this).data("classes");
            if ($(this).children().eq(1).hasClass("up") && $(this).children().eq(0).hasClass("current")) {
                $(this).children().eq(1).addClass("down").removeClass("up");
                $(this).children().eq(0).removeClass("current");
                $("." + classes).slideUp(100);
                $(this).siblings().each(function () {
                    $(this).children().eq(0).removeClass("current");
                    $(this).children().eq(1).removeClass("up").addClass("down");
                    $("." + $(this).data("classes")).slideUp(100);
                    webtouchObj.showornot = 0;
                })
            } else {
                $(this).children().eq(1).addClass("up").removeClass("down");
                $(this).children().eq(0).addClass("current");
                $("." + classes).slideDown(100);
                $(this).siblings().each(function () {
                    $(this).children().eq(0).removeClass("current");
                    $(this).children().eq(1).removeClass("up").addClass("down");
                    $("." + $(this).data("classes")).slideUp(100);
                    webtouchObj.showornot = 0;
                })
            }
            setTimeout("webtouchObj.yanchizx()", 105);
        },
        yanchizx: function () {
            $(".choice_wrap ul").each(function () {
                if ($(this).is(":visible")) {
                    webtouchObj.showornot = 1
                }
            })
            if (webtouchObj.showornot) {
                $('body').css("overflow", "hidden");
                $(".choice_wrap").css({"height": "calc(100% - 99px)", "background": "rgba(0,0,0,0.5)"});
            } else {
                $('body').css("overflow", "scroll");
                $(".choice_wrap").css({"height": "auto", "background": "none"});
            }
        },
        cancelSelect: function () {
            $(".newHosue_nav  ul li,.evaluation_tab  ul li").each(function () {
                if ($(this).children().eq(1).hasClass("up") && $(this).children().eq(0).hasClass("current")) {
                    $(this).children().eq(1).addClass("down").removeClass("up");
                    $(this).children().eq(0).removeClass("current");
                }
            })
            $(".choice_wrap ul").each(function () {
                if ($(this).is(":visible")) {
                    $(this).hide()
                }
            })
            $('body').css("overflow", "scroll");
            $(".choice_wrap").css({"height": "auto", "background": "none"});
        },
        showLocation: function (position) {
            var latitude = position.coords.latitude;
            var longitude = position.coords.longitude;
            webtouchObj.setCookie("latitude",latitude,1);
            webtouchObj.setCookie("longitude",longitude,1);
        },
        errorHandler: function (err) {
            if (err.code == 1) {
                console.dir("拒绝访问当前位置!");
            } else if (err.code == 2) {
                console.dir("您的位置获取不到!");
            }
        },
        getLocation: function () {
            if (navigator.geolocation) {
                // timeout at 60000 milliseconds (60 seconds)
                var options = {timeout: 60000};
                navigator.geolocation.getCurrentPosition(webtouchObj.showLocation, webtouchObj.errorHandler, options);
            } else {
                alert("浏览器不支持定位!");
            }
        },
        getCookie: function (name) {
            var arr, reg = new RegExp("(^| )" + name + "=([^;]*)(;|$)");

            if (arr = document.cookie.match(reg))
                return unescape(arr[2]);
            else
                return null;
        },
        setCookie: function (name, value, Days) {
            var exp = new Date();
            exp.setTime(exp.getTime() + Days * 24 * 60 * 60 * 1000);
            document.cookie = name + "=" + escape(value) + ";expires=" + exp.toGMTString();
        }
    }
});