requirejs.config({
     baseUrl: localPath + 'lib',
    //baseUrl: '../lib',
    paths: {
        'jquery': 'jquery/jquery.min',
        'highcharts': 'plugs/highcharts',
        'chartfn': 'plugs/highcharts.ext',
        'swiper': 'plugs/swiper.min',
        'webtouchcommon': 'js/webtouchcommon'
    },
    shim: {
        'swiper': {
            deps: ['jquery']
        },
        'highcharts': {
            deps: ['jquery']
        },
        'chartfn': {
            deps: ['jquery']
        }
    }
});

requirejs(['highcharts', 'chartfn', 'swiper', 'webtouchcommon'], function (doc) {

    //自定义公共对象newhouseObj
    var newhouseObj = {
        showornot:0,
        updownShow: function () {
            if ($("#hide-content").is(":hidden")) {
                $("#hide-content").slideDown(100);
                $(this).addClass("infoup");
            } else {
                $("#hide-content").slideUp(100);
                $(this).removeClass("infoup");
            }
        },
        houseSelect:function(){
            var classes = $(this).data("classes");
                $("." + classes).slideDown(100);
                $("#BgDiv").slideDown(100);
                $(this).siblings().each(function () {
                    $("." + $(this).data("classes")).slideUp(100);
                    newhouseObj.showornot = 0;
                });
               setTimeout(newhouseObj.yanchizx(), 105);

        },
        yanchizx: function () {
            $(".choice_wrap2 ul").each(function () {
                if ($(this).is(":visible")) {
                    newhouseObj.showornot = 1
                }
            })
            if (newhouseObj.showornot) {
                $('body').css("overflow", "hidden");
                $("#BgDiv").slideDown(100);
            } else {
                $('body').css("overflow", "scroll");
                 $("#BgDiv").slideUp(100);
            }
        },
        cancelSelect: function () {
            $(".choice_wrap2 ul").each(function () {
                if ($(this).is(":visible")) {
                    $(this).hide()
                }
            })
            $('body').css("overflow", "scroll");
            $("#BgDiv").slideUp(100);
        },
        selectHt:function(){
                var htname=$(this).html();
                var changeid=$(this).parent().data("flag");
                $("#"+changeid).children().eq(0).html(htname);
                $(this).parent().hide();
                $("#BgDiv").hide();
                $('body').css("overflow", "scroll");
                //下面执行ajax过滤
            
        }
    }
    //document.ready 函数
    $(initNewhouse);
    function initNewhouse() {
        webtouchObj.canvasload();
        //swipe滑动
        var swiper = new Swiper('.Regionulcontain', {
            slidesPerView: 2.5,
            freeMode: true,
            spaceBetween : 5,
            freeModeMomentum: false
        });
         //swipe滑动理想家
        var swipertop = new Swiper('#pcSlide', {
         autoplay : 2000,
        });

        //获取pie数据
        var pieData = [{y: 54, name: "首期付款", color: "#7dc52c"}, {y: 5, name: "支付利息", color: "#1899dc"}, {y: 154, name: "贷款总额", color: "#e8382b"}, {y: 54, name: "月均还款", color: "#cccccc"}];
        DrawLoanPieCircle("pieContent", pieData);
        //图表
        
        var r = $('#region').val();
        var region = r.split(',');

        var h = $('#house').val();
        var house = h.split(',');

        var c = $('#cottage').val();
        var cottage = c.split(',');

        var year = $('#year').val();
        var month = $('#month').val();
        
        // var region = new Array(5566, 5566, 5566, 5566, 5566, 5566, 5566);
        // var house = new Array(8000, 7000, 8000, 8000, 8000, 8000, 8000);
        // var cottage = new Array(5566, 5566, 9999, 9000, 5566, 5566, 7800);

        var dataParam = [
            {"type": "area", "name": "区域", "color": "#E9A248", "data": region},
            {"type": "line", "name": "普通住宅", "color": "#F4352B", "data": house},
            {"type": "line", "name": "别墅", "color": "#B6D244", "data": cottage}];
        DrawMixtureChart("spContainer", "100%", 130, dataParam, year, month, 22);
        var tmpChart = $('#spContainer').highcharts();
        var options = tmpChart.options;
        var tooltip = {
            enabled: false
        };
        var plotOptions = {
            area: {
                fillOpacity: 0.3
            },
            series: {
                marker: {
                    radius: 5, //曲线点半径，默认是4
                    states: {
                        hover: {
                            enabled: false
                        }
                    },
                    symbol: 'circle' //曲线点类型："circle", "square", "diamond", "triangle","triangle-down"，默认是"circle"
                }
            }
        };
        options.tooltip = tooltip;
        options.plotOptions = plotOptions;
        new Highcharts.Chart(options);


        //显示全部点击
        $("#updownshow").on("click", newhouseObj.updownShow);
        
        //回到顶部
        $(window).scroll(webtouchObj.backtoTop);
        $('#backtop').on("click",webtouchObj.backtoTopdh);
        
         $(".newhouse_select").on("click","a",newhouseObj.houseSelect);
         $("#BgDiv").on("click",newhouseObj.cancelSelect);
         $(".choice_wrap2").on("click","ul li",newhouseObj.selectHt);
        
        
        
    }
});