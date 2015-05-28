define(["jquery"], function () {
    return commonObj = {
        nav_num: $('nav .line li').size(),
        Hx_lie: $(".house_type_right_child").size(),
        upflag: 1,
        downflag: 1,
        toppageJump: function () {
            var url = $(this).children("a").attr("href");
            window.location.href = url;
        },
        ininPage: function () {
            //canvas加载图片
            var imglength = $(".plan_wrap").find("canvas").length;
            if (imglength > 0) {
                $(".plan_wrap").find("canvas").each(function () {
                    var imgSrc = $(this).data("src");
                    var imageObj = new Image();
                    imageObj.canvs = $(this)[0];
                    var cvs = imageObj.canvs.getContext('2d');
                    if (cvs) {
                        imageObj.onload = function () {
                            imageObj.canvs.width = this.width;
                             imageObj.canvs.height = this.height;
                            cvs.drawImage(this, 0, 0);
                             $(imageObj.canvs).css("background-image","none");
                        }
                        imageObj.src = imgSrc;
                    }
                })
            }
        },
        BottomJumpPage: function () {
            var scrollTop = $(this).scrollTop();
            var scrollHeight = $(document).height();
            var windowHeight = $(this).height();
            if (scrollTop + windowHeight == scrollHeight) {  //滚动到底部执行事件
                var nextli = $("nav .line").find(".current").parent().next();
                if (nextli.length > 0) {
                    var blankContent = nextli.find(".no_con");
                    if (blankContent.length < 1) {
                        var url = nextli.children("a").attr("href");
                        window.location.href = url
                    }
                }
            }
            if (scrollTop == 0) {  //滚动到头部部执行事件
                var nextli = $("nav .line").find(".current").parent().prev();
                if (nextli.length > 0) {
                    var blankContent = nextli.find(".no_con");
                    if (blankContent.length < 1) {
                        var url = nextli.children("a").attr("href");
                        window.location.href = url
                    }
                }
            }
        },
        scrollDirect: function (fn) {
            var beforeScrollTop = document.body.scrollTop;
            fn = fn || function () {
            };
            window.addEventListener("scroll", function (event) {
                event = event || window.event;

                var afterScrollTop = document.body.scrollTop;
                delta = afterScrollTop - beforeScrollTop;
                beforeScrollTop = afterScrollTop;

                var scrollTop = $(this).scrollTop();
                var scrollHeight = $(document).height();
                var windowHeight = $(this).height();
                if (scrollTop + windowHeight > scrollHeight - 10) {  //滚动到底部执行事件
                    fn('up');
                    return;
                }
                if (afterScrollTop < 10 || afterScrollTop > $(document.body).height - 10) {
                    fn('up');
                } else {
                    if (Math.abs(delta) < 10) {
                        return false;
                    }
                    fn(delta > 0 ? "down" : "up");
                }
            }, false);
        },
        backtoTop: function () {
            var screenheight = $(window).height();
            if ($(this).scrollTop() > screenheight) {
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
    }
}
);