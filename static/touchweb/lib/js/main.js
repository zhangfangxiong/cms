requirejs.config({
    baseUrl: localPath + 'lib',
    //baseUrl: '../lib',
    paths: {
        'jquery': 'jquery/jquery.min',
        'swiper': 'plugs/swiper.min',
        'touchSwipe': 'plugs/jquery.touchSwipe.min',
        'simpleshare': 'js/simpleshare',
        'commonfn': 'js/commonfn'
    },
    shim: {
        'touchSwipe': {
            deps: ['jquery']
        },
        'simpleshare': {
            deps: ['jquery']
        }
    }
});

requirejs(['swiper', 'touchSwipe', 'commonfn','simpleshare'], function (doc) {
    if (commonObj.nav_num <= 3) {
        $('nav .line').width('100%');
        var navWidth = (100 / commonObj.nav_num) + "%";
        $('nav .line li').width(navWidth);
    } else {
        var num = parseInt($('nav .line').find(".current").parent().index());
        if (num < 3) {
            num = 0
        }
        var swiper = new Swiper('.swiper-container', {
            slidesPerView: 3.5,
            freeMode: true,
            initialSlide: num,
            freeModeMomentum: false
        });
    }
    if (commonObj.Hx_lie <= 2) {
        $("#SlideTips").hide();
        var rightchild = (100 / commonObj.Hx_lie) + "%";
        $("ul.house_type_right_child").width(rightchild)
    } else {
        $("#SlideTips").show();
        var rightchildWh = 100 * commonObj.Hx_lie + "%";
        $(".house_type_allright").width(rightchildWh);
        var allrightW = parseFloat($(".house_type_allright").width()) - 90;
        $(".house_type_allright").width(allrightW);
        var rightlt = parseFloat((100 / commonObj.Hx_lie / 2.6).toFixed(3));
        var rightlt_li = rightlt + "%";
        $('ul.house_type_right_child').width(rightlt_li);
        var tabley = 0;
        var tablew = commonObj.Hx_lie - 2;
        $(".house_type_allright").swipe({
            swipeLeft: function () {
                if (tabley == -rightlt * tablew) {
                } else {
                    $("#SlideTips").hide();
                    tabley = parseFloat(tabley.toFixed(3)) - rightlt;
                    var tablet = tabley + "%";
                    $(this).css({'-webkit-transform': "translate(" + tablet + ")", 'transform': "translate(" + tablet + ")", '-webkit-transition': '300ms linear', 'transition': '300ms linear'});
                }
            },
            swipeRight: function () {
                if (Math.abs(tabley) < 1) {
                } else {
                    tabley = parseFloat(tabley.toFixed(3)) + rightlt;
                    var tablet = tabley + "%";
                    $(this).css({'-webkit-transform': "translate(" + tablet + ")", '-webkit-transition': '300ms linear', 'transform': "translate(" + tablet + ")", 'transition': '300ms linear'});
                }

            }
        });
    }
    $('nav .line li').on("click", commonObj.toppageJump);


    //pc端scroll滑动
    commonObj.scrollDirect(function (direction) {
        if (direction == "down") {
            if (commonObj.downflag) {
                $(".footer_wrap").slideUp(200);
                commonObj.downflag = 0;
                commonObj.upflag = 1;
            }
        }
        if (direction == "up") {
            if (commonObj.upflag) {
                $(".footer_wrap").slideDown(200);
                commonObj.downflag = 1;
                commonObj.upflag = 0;
            }
        }
    });
    
    if ($("#appornot").val() != "") {
            if ($(window).scrollTop() < 48) {
                $(".nav ").css("top", 48 - parseInt($(window).scrollTop()));
            }else{
              $(".nav ").css("top", "0");  
            }
        $(window).scroll(function () {
            $(".nav ").css("top", "0");
            var toplength = parseInt($(window).scrollTop());
            if ($(window).scrollTop() < 48) {
                $(".nav ").css("top", 48 - toplength);
            }
            var screenheight = $(window).height();
            if ($(this).scrollTop() > screenheight) {
                $('#backtop').fadeIn();
            } else {
                $('#backtop').fadeOut();
            }
        });

        $('#backtop').on("click", commonObj.backtoTopdh);
        $("#sharesCode").on("click", function () {
            if ($(".sharesdiv").is(":hidden")) {
                $("#BgDiv").show();
                $(".sharesdiv").show();
                $('body').css("overflow", "hidden");
            } else {
                $("#BgDiv").hide();
                $(".sharesdiv").hide();
                $('body').css("overflow", "initial");
            }
        })
        
      $("#BgDiv").on("click",function(){
                $("#BgDiv").hide();
                $(".sharesdiv").hide();
                $('body').css("overflow", "initial");
      })
      $(".innerCancle").on("click",function(){
                 $("#BgDiv").hide();
                $(".sharesdiv").hide();
                $('body').css("overflow", "initial");
      })
      var sharepic= $(".plan_wrap").find("canvas").eq(0).data("src");
      if(!sharepic){
          sharepic=""
      }
      var sharecontent= $(".plan_wrap").find("p").eq(0).text();
      if(!sharecontent){
          sharecontent=""
      }
    var share = new SimpleShare({
        url:document.location.href,
        title: $("title").html(),
        content: sharecontent,
        pic: sharepic
    });
   
    $(".qqzone").on("click",function(){
        share.qzone();
    })
    $(".qqfriend").on("click",function(){
        share.c_qq();
    })
    $(".sinaweibo").on("click",function(){
        share.weibo();
    })

    }
    //canvas加载图片
    commonObj.ininPage();
   
    
    var akit= $(".plan_wrap").find(".Kitchen_system").length;
    if(akit>0){
        for(var i=0;i<$(".Kitchen_system").length;i++){
            var kit=[];
            $(".Kitchen_system").eq(i).find("li").each(function(){
                kit.push($(this).height());

            })
          var maxInNumbers = Math.max.apply(Math, kit);
            $(".Kitchen_system").eq(i).find("li").each(function(){
             $(this).height(maxInNumbers)
            })
        }
    }

});

