/*新房项目业*/
var XfDetail = {
    roomTypes: null,
    picApi: null,
    picIsAll: true,
    init: function (roomTypes) {
        XfDetail.roomTypes = roomTypes;
        var visibleCount = 5;
        $("#detail_type_menu li a").click(function () {
            var roomType = $(this).attr("roomtype");
            var count = $(this).attr("count");
            $("#detail_type_menu li a").removeClass("cur");
            $(this).addClass("cur");
            if (roomType == "all") {
                $("#room_type_scroll ul li").show();
            } else {
                $("#room_type_scroll ul li").hide();
                var imgs = $("#room_type_scroll  ." + roomType + " img[src='']");
                $("#room_type_scroll ." + roomType).show();
            }
            apiT.switchTo(0);
            if (count <= visibleCount) {
                $("#room_type_scroll").next().css("visibility", "hidden");
            } else {
                $("#room_type_scroll").next().css("visibility", "visible");
            }
        });
        $("#detail_pic_menu li a").click(function () {
            var type = $(this).attr("type");
            var count = $(this).attr("count");
            $("#detail_pic_menu li a").removeClass("cur");
            $(this).addClass("cur");

            if (type == "all") {
                XfDetail.picIsAll = true;
                $("#detail_picture_scroll ul li").show();
            } else {
                XfDetail.picIsAll = false;
                $("#detail_picture_scroll ul li").hide();
                var imgs = $("#detail_picture_scroll ul ." + type + " img[src='']");
                $.each(imgs, function (i, val) {
                    $(this).attr("src", $(this).attr("srcurl"));
                    $(this).removeAttr("srcurl");
                });
                $("#detail_picture_scroll ul ." + type).show();
            }
            apiP.switchTo(0);
            if (count <= visibleCount) {
                $("#detail_picture_scroll").next().css("visibility", "hidden");
            } else {
                $("#detail_picture_scroll").next().css("visibility", "visible");
            }
        });
        var apiT = $('#room_type_scroll').switchable({
            triggers: '&bull;',
            putTriggers: 'insertBefore',
            panels: 'li',
            easing: 'ease-in-out',
            effect: 'scrollLeft',
            steps: visibleCount,
            visible: visibleCount,
            prev: '.s_prev',
            next: '.s_next',
            onSwitch: function (e, currentIndex) {
                this.prevBtn.toggleClass('disabled', currentIndex === 0);
                this.nextBtn.toggleClass('disabled', currentIndex === this.length - 1);
                $("#room_type_scroll").find("img[src='']").each(function (i, val) {
                    if (i < 5) {
                        $(this).attr("src", $(this).attr("srcurl"));
                        $(this).removeAttr("srcurl");
                        return true;
                    }
                    return false;
                });
            }, api: true
        });
        var apiP = $('#detail_picture_scroll').switchable({
            triggers: '&bull;',
            putTriggers: 'insertBefore',
            panels: 'li',
            easing: 'ease-in-out',
            effect: 'scrollLeft',
            steps: visibleCount,
            visible: visibleCount,
            prev: '.s_prev_pic',
            next: '.s_next_pic',
            onSwitch: function () {
                $("#detail_picture_scroll").find("img[src='']").each(function (i, val) {
                    if (i < 5) {
                        $(this).attr("src", $(this).attr("srcurl"));
                        $(this).removeAttr("srcurl");
                        return true;
                    }
                    return false;
                });
            },
            api: true
        });
    },
    showTypeDetail: function (cTarget) {
        var $target = $(cTarget).children(".rt_info");
        XfDetail.fillTypeDetail($target);
        $("#room_type_dialog").show();
    },
    showPictureDetail: function (cIndex, pName, target, typeCount) {
        var index = XfDetail.picIsAll ? cIndex : typeCount;
        var imgs = $(target).parent().children("li").find("img");
        $("#Proalbumbox #Prcboxside").empty();
        var count = 0;
        $.each(imgs, function (i, val) {
            var $img = $(this);
            if ($img.parent().css("display") != "none") {
                $("#Proalbumbox #Prcboxside").append("<img currindex=" + count + " mark=" + $img.attr("mark") + " src=" + $img.attr("bigurl") + " alt=\"\"/>");
                count++;
            }
        });
        $("#Proalbumbox .album_sz").html("1/" + count);
        $("#Proalbumbox .picName").html($("[currindex=" + index + "]").attr("mark"));
        XfDetail.picApi = $('#Prcboxside').switchable({
            triggers: '&bull;',
            putTriggers: 'insertBefore',
            effect: 'fade',
            easing: 'ease-in-out',
            prev: '#album_prev',
            next: '#album_next',
            onSwitch: function (event, currentIndex) {
                if (currentIndex < count) {
                    $("#Proalbumbox .album_sz").html(parseInt(currentIndex) + 1 + "/" + count);
                }
                $("#Proalbumbox .picName").html($("[currindex=" + currentIndex + "]").attr("mark"));
            },
            api: true
        });
        if (XfDetail.picApi.index != index) {
            XfDetail.picApi.switchTo(index);
        }
        showBg("Proalbumbox");
    },
    changeRoomType: function (name, code) {
        var target = $("#room_type_scroll").find("[roomType=" + name + code + "]").children(".rt_info");
        XfDetail.fillTypeDetail(target);
    },
    fillTypeDetail: function (target) {
        var $target = target;
        var typeName = $target.children(".type_name").html();
        var typeCode = $target.children(".type_code").html();
        var typeArea = $target.children(".type_area").html();
        var typeImgUrl = $target.children(".type_img_url").html();
        var typeYouShi = $target.children(".type_youshi").html();
        var typeLieShi = $target.children(".type_lieshi").html();
        $("#room_type_dialog").find(".rt_title").html(typeCode + " " + typeName + " " + typeArea);
        $("#room_type_dialog").find(".rt_img_url").attr("src", typeImgUrl);
        $("#room_type_dialog").find(".rt_typecode").html("<span>户型:</span>" + typeCode);
        $("#room_type_dialog").find(".rt_typename").html("<span>房型:</span>" + typeName);
        $("#room_type_dialog").find(".rt_typearea").html("<span>面积:</span>" + typeArea);
        $("#room_type_dialog").find(".rt_youshi").html(typeYouShi);
        $("#room_type_dialog").find(".rt_lieshi").html(typeLieShi);
        var types = XfDetail.roomTypes;
        $("#room_type_dialog #other_type").html("查看当前楼盘其他户型:");
        if (types) {
            var count = 1;
            for (var i = 0, item; i < types.length, item = types[i]; i++) {
                if (count > 5) {
                    break;
                }
                if (!(item.typeName == $.trim(typeName) && item.typeCode == $.trim(typeCode))) {
                    $("#room_type_dialog #other_type").append("<span onclick=\"XfDetail.changeRoomType('" + item.typeName + "','" + item.typeCode + "')\"><s></s>" + item.typeCode + " " + item.typeName + " " + item.area + "</span>");
                    count++;
                }
            }
        }
    }
};
//cms报告
var CMSEval = {
    cmsData: null,
    t: 0,
    evalData: {},
    $allA: $("#evaluation_menu li div cite").find("a"),
    bo: false,
    init: function (d, t) {
        CMSEval.cmsData = d;
        CMSEval.t = t;
        var data = CMSEval.cmsData.data;
        for (var i = 0, item; i < data.length, item = data[i]; i++) {
            CMSEval.evalData[item.subcolumn] = item.url;
        }
        CMSEval.hxfx();
        CMSEval.zxbz();
        CMSEval.sqpz();
        CMSEval.wyfw();
        CMSEval.shpt();
        CMSEval.blys();
        CMSEval.d(".ztpj", CMSEval.evalData["整体评价"], $("#a_hxfx"), "整体评价");
        CMSEval.d(".ghxfx", CMSEval.evalData["各户型分析"], $("#a_hxfx"), "各户型分析");
        CMSEval.d(".pppz", CMSEval.evalData["品牌配置"], $("#a_zxbz"), "品牌配置");
        CMSEval.d(".zxfx", CMSEval.evalData["装修分析"], $("#a_zxbz"), "装修分析");
        CMSEval.d(".ztgh", CMSEval.evalData["整体规划"], $("#a_sqpz"), "整体规划");
        CMSEval.d(".sqjg", CMSEval.evalData["社区景观"], $("#a_sqpz"), "社区景观");
        CMSEval.d(".jzlm", CMSEval.evalData["建筑立面"], $("#a_sqpz"), "建筑立面");
        CMSEval.d(".ggbw", CMSEval.evalData["公共部位"], $("#a_sqpz"), "公共部位");
        CMSEval.d(".hspz", CMSEval.evalData["会所配置"], $("#a_sqpz"), "会所配置");
        CMSEval.d(".cwqk", CMSEval.evalData["车位情况"], $("#a_sqpz"), "车位情况");
        CMSEval.d(".wyfy", CMSEval.evalData["物业费用"], $("#a_wyfw"), "物业费用");
        CMSEval.d(".wyfw", CMSEval.evalData["物业服务"], $("#a_wyfw"), "物业服务");
        CMSEval.d(".ggzy", CMSEval.evalData["公共资源"], $("#a_shpt"), "公共资源");
        CMSEval.d(".zbsf", CMSEval.evalData["周边商服"], $("#a_shpt"), "周边商服");
        CMSEval.d(".ylzy", CMSEval.evalData["医疗资源"], $("#a_shpt"), "医疗资源");
        CMSEval.d(".jyzy", CMSEval.evalData["教育资源"], $("#a_shpt"), "教育资源");
        CMSEval.d(".gjcx2", CMSEval.evalData["公交出行"], $("#a_shpt"), "公交出行");
        CMSEval.d(".gjcx", CMSEval.evalData["轨交出行"], $("#a_shpt"), "轨交出行");
        CMSEval.d(".zjcx", CMSEval.evalData["自驾出行"], $("#a_shpt"), "自驾出行");
        CMSEval.d(".qwjj", CMSEval.evalData["区位简介"], $("#a_shpt"), "区位简介");
        CMSEval.d(".sqnb", CMSEval.evalData["社区内部"], $("#a_blys"), "社区内部");
        CMSEval.d(".sqwb", CMSEval.evalData["社区外部"], $("#a_blys"), "社区外部");
        $("#evaluation_menu li").children("a").click(function () {
            $("#evaluation_menu li").removeClass("cur");
            $("#evaluation_menu li div").hide();
            $(this).parent().addClass("cur");
            $(this).next().show();
            var id = $(this).attr("id");
            var fName = "CMSEval." + id.split("_")[1] + "(true)";
            CMSEval.bo = false;
            eval(fName);
        });
        $("#evaluation_menu li div cite").find("a").click(function () {
            $("#evaluation_menu li div cite").find("a").removeClass("cur");
            $(this).addClass("cur");
            var key = $.trim($(this).text());
            if (CMSEval.evalData[key]) {
                $("#ifr_content").attr("src", CMSEval.evalData[key]);
            } else {
                $("#ifr_content").attr("src", "");
            }
        });
    },
    hxfx: function (flag) {
        if (CMSEval.t == 1 || CMSEval.t == 0 || flag) {
            CMSEval.c($("#a_hxfx").next().children().find(".ztpj"), CMSEval.evalData["整体评价"]);
            CMSEval.c($("#a_hxfx").next().children().find(".ghxfx"), CMSEval.evalData["各户型分析"]);
        }
    },
    zxbz: function (flag) {
        if (CMSEval.t == 2 || CMSEval.t == 0 || flag) {
            CMSEval.c($("#a_zxbz").next().children().find(".pppz"), CMSEval.evalData["品牌配置"]);
            CMSEval.c($("#a_zxbz").next().children().find(".zxfx"), CMSEval.evalData["装修分析"]);
        }
    },
    sqpz: function (flag) {
        if (CMSEval.t == 3 || CMSEval.t == 0 || flag) {
            CMSEval.c($("#a_sqpz").next().children().find(".ztgh"), CMSEval.evalData["整体规划"]);
            CMSEval.c($("#a_sqpz").next().children().find(".sqjg"), CMSEval.evalData["社区景观"]);
            CMSEval.c($("#a_sqpz").next().children().find(".jzlm"), CMSEval.evalData["建筑立面"]);
            CMSEval.c($("#a_sqpz").next().children().find(".ggbw"), CMSEval.evalData["公共部位"]);
            CMSEval.c($("#a_sqpz").next().children().find(".hspz"), CMSEval.evalData["会所配置"]);
            CMSEval.c($("#a_sqpz").next().children().find(".cwqk"), CMSEval.evalData["车位情况"]);
        }
    },
    wyfw: function (flag) {
        if (CMSEval.t == 4 || CMSEval.t == 0 || flag) {
            CMSEval.c($("#a_wyfw").next().children().find(".wyfy"), CMSEval.evalData["物业费用"]);
            CMSEval.c($("#a_wyfw").next().children().find(".wyfw"), CMSEval.evalData["物业服务"]);
        }
    },
    shpt: function (flag) {
        if (CMSEval.t == 5 || CMSEval.t == 0 || flag) {
            CMSEval.c($("#a_shpt").next().children().find(".qwjj"), CMSEval.evalData["区位简介"]);
            CMSEval.c($("#a_shpt").next().children().find(".zjcx"), CMSEval.evalData["自驾出行"]);
            CMSEval.c($("#a_shpt").next().children().find(".gjcx"), CMSEval.evalData["轨交出行"]);
            CMSEval.c($("#a_shpt").next().children().find(".gjcx2"), CMSEval.evalData["公交出行"]);
            CMSEval.c($("#a_shpt").next().children().find(".jyzy"), CMSEval.evalData["教育资源"]);
            CMSEval.c($("#a_shpt").next().children().find(".ylzy"), CMSEval.evalData["医疗资源"]);
            CMSEval.c($("#a_shpt").next().children().find(".zbsf"), CMSEval.evalData["周边商服"]);
            CMSEval.c($("#a_shpt").next().children().find(".ggzy"), CMSEval.evalData["公共资源"]);
        }
    },
    blys: function (flag) {
        if (CMSEval.t == 6 || CMSEval.t == 0 || flag) {
            CMSEval.c($("#a_blys").next().children().find(".sqnb"), CMSEval.evalData["社区内部"]);
            CMSEval.c($("#a_blys").next().children().find(".sqwb"), CMSEval.evalData["社区外部"]);
        }
    },
    c: function ($thisA, url) {
        if (!CMSEval.bo) {
            if (url) {
                CMSEval.$allA.removeClass("cur");
                $thisA.parents("li").siblings("li").removeClass("cur");
                $thisA.parents("li").addClass("cur");
                $thisA.addClass("cur");
                $("#ifr_content").attr("src", url);
                CMSEval.bo = true;
                XD.receiveMessage(function (result) {
                    CMSEval.h(result);
                });
            }
        }
    },
    d: function (thisA, url, $thisAType, text) {
        if (!url) {
            $thisAType.next().children().find(thisA).replaceWith("<span>" + text + "</span>");
        }
    },
    h: function (result) {
        var height = parseInt(result.height);
        if (height) {
            height = height + 30;
            $("#ifr_content").height(height);
        }
    }
};
//周边地图
var SFMap = {
    intervalId: null,
    markers: {},
    unitName: "",
    init: function (x, y, name) {
        SFMap.unitName = name;
        var type = getQueryString("t");
        $(function () {
            var top = $("#surr_fa_map").offset().top;
            if (type == "life" || type == "health" || type == "station" || type == "edu") {
                $("html,body").animate({ scrollTop: top - 250 });
            }
        });
        var minLevel = 13;
        var maxLevel = 18;
        var level = 16;
        var station = $("#station").attr("keywords").split(',');
        var health = $("#health").attr("keywords").split(',');
        var edu = $("#edu").attr("keywords").split(',');
        var life = $("#life").attr("keywords").split(',');
        var distance = 1000;
        var pageSize = 50;
        var coodxy = new AMap.LngLat(x, y);
        var mapElement = "surr_fa_map";
        var mapObj = null;
        var startFlag = 1;
        if (!$("#" + mapElement)[0]) {
            return;
        }
        $("#surrou_type_menu ul li a").click(function () {
            startFlag = 1;
            SFMap.markers = {};
            mapObj.clearMap();
            setUnitMarker();
            $("#surr_info_list").empty();
            $("#surrou_type_menu ul li a").removeClass("cur");
            $(this).addClass("cur");
            var keywords = $(this).attr("keywords").split(",");
            for (var i = 0, item; i < keywords.length, item = keywords[i]; i++) {
                e(coodxy, item);
            }
        });
        mapObj = new window.AMap.Map(mapElement, { level: level, zooms: [minLevel, maxLevel], center: new AMap.LngLat(x, y) });
        mapObj.plugin(["AMap.ToolBar"], function () {
            var toolbar = new window.AMap.ToolBar();
            toolbar.autoPosition = true; //加载工具条
            mapObj.addControl(toolbar);
        });

        mapObj.clearMap();
        setUnitMarker();
        var currentType = station;

        switch (type) {
            case "station":
                $("#station").addClass("cur");
                break;
            case "edu":
                currentType = edu;
                $("#edu").addClass("cur");
                break;
            case "life":
                currentType = life;
                $("#life").addClass("cur");
                break;
            case "health":
                currentType = health;
                $("#health").addClass("cur");
                break;
            default:
                $("#station").addClass("cur");
                break;
        }
        for (var i = 0, item; i < currentType.length, item = currentType[i]; i++) {
            e(coodxy, item);
        }
        var mSearch;
        function e(k, type) {
            window.AMap.service(["AMap.PlaceSearch"], function () {
                mSearch = new AMap.PlaceSearch({
                    type: type,
                    pageSize: pageSize
                });
                mSearch.searchNearBy(type, k, distance, function (status, result) {
                    mapObj.clearInfoWindow();
                    if (status === 'complete' && result.info === 'OK') {
                        var data = result.poiList.pois;
                        var mark;
                        for (var i = 0, item; i < data.length, item = data[i]; i++) {
                            var cite = document.createElement("cite");
                            var span = document.createElement("span");
                            var s = document.createElement("s");
                            s.setAttribute("class", "s" + startFlag);
                            span.appendChild(s);
                            var v = document.createElement("var");
                            v.innerText = startFlag + item.name;
                            span.appendChild(v);
                            cite.appendChild(span);
                            var em = document.createElement("em");
                            em.innerText = item.distance + "米";
                            cite.appendChild(em);
                            $("#surr_info_list")[0].appendChild(cite);
                            var location = item.location;
                            mark = new window.AMap.Marker({
                                map: mapObj,
                                position: new window.AMap.LngLat(location.lng, location.lat), //位置
                                zIndex: 201,
                                content: "<div item='" + i + "' class=\"sur_amap\"><div style='visibility:hidden;left:-132px;' class=\"sur_amap_outer\"><div class=\"sur_amap_content\"><div>" + item.name + "</div><div class=\"sur_amap_sharp\" style=\"left: 146px;\"></div></div></div><a href=\"javascript:void(0)\" class=\"emp1\" onmouseover='SFMap.s(" + startFlag + ",this," + i + ")' onmouseout='SFMap.h(" + startFlag + ",this," + i + ")'>" + startFlag + "</a></div>"
                            });
                            SFMap.markers[i] = mark;
                            startFlag++;
                        }
                    }
                });
            });
        }
        function setUnitMarker() {
            var marker = new window.AMap.Marker({
                map: mapObj,
                position: new window.AMap.LngLat(x, y), //位置
                zIndex: 201,
                content: "<div class=\"sur_amap\"><div style='visibility:hidden;' class=\"sur_amap_outer\"><div class=\"sur_amap_content\"><div>" + SFMap.unitName + "</div><div class=\"sur_amap_sharp\" style=\"left: 146px;\"></div></div></div><img src='/Content/images/mapp2.png' alt = '' onmouseover='$(this).prev().css(\"visibility\",\"\")' onmouseout='$(this).prev().css(\"visibility\",\"hidden\")'/></div>"
            });
            (function () {
                AMap.event.addListener(marker, 'mouseover', function () {
                    this.setzIndex(401);
                });
                AMap.event.addListener(marker, 'mouseout', function () {
                    this.setzIndex(201);
                });
            })();
        }
    },
    s: function (startFlag, cTarget, i) {
        SFMap.markers[i].setzIndex(401);
        $(cTarget).prev().css("visibility", "");
        $("#surr_info_list cite span .s" + startFlag).addClass("cur");
        // var num = i;
        var item = $("#surr_info_list cite span .s" + (startFlag)).parent();
        var itemHeight = item[0].offsetHeight;
        var panel = $("#surr_info_list");
        var scrollHandler = function () {
            if (item == null || item.length == 0) {
                SFMap.clearInterval();
                return;
            }
            var current = item.offset().top - panel.offset().top;
            var target = itemHeight * 2;

            var offset = (target - current) / 5;
            if (offset > 16) {
                offset = 16;
            }
            else if (offset < -16) {
                offset = -16;
            }
            if (Math.abs(offset) < 1) {
                window.clearInterval(SFMap.intervalId);
                return;
            }
            var top1 = panel[0].scrollTop;
            var top2 = top1 - offset;
            panel[0].scrollTop = top2;
            if (panel[0].scrollTop == top1) {
                window.clearInterval(SFMap.intervalId);
                return;
            }
        };
        SFMap.clearInterval();
        SFMap.intervalId = window.setInterval(scrollHandler, 20);
    },
    h: function (startFlag, cTarget, i) {
        SFMap.markers[i].setzIndex(201);
        $(cTarget).prev().css("visibility", "hidden");
        $("#surr_info_list cite span s").removeClass("cur");
        SFMap.clearInterval();
    },
    clearInterval: function () {
        if (SFMap.intervalId) {
            window.clearInterval(SFMap.intervalId);
        }
    }
};
//周边信息
var SurFac = {
    init: function (x, y) {
        var keywords_station = $("#station").attr("keywords").split(',');
        var keywords_health = $("#health").attr("keywords").split(',');
        var keywords_edu = $("#edu").attr("keywords").split(',');
        var keywords_life = $("#life").attr("keywords").split(',');
        var dan_wei = {
            "公交车站": "个", "地铁站": "条", "幼儿园": "所", "小学": "所", "中学": "所", "大学": "所", "银行": "个", "购物": "个", "餐厅": "个", "邮局": "个", "医院": "所", "健身": "个"
        };
        var k = new AMap.LngLat(x, y);
        var distance = 1000;
        for (var j = 0; j < keywords_station.length; j++) {
            e(k, keywords_station[j], "station");
        }
        for (var j = 0; j < keywords_health.length; j++) {
            e(k, keywords_health[j], "health");
        }
        for (var j = 0; j < keywords_edu.length; j++) {
            e(k, keywords_edu[j], "edu");
        }
        for (var j = 0; j < keywords_life.length; j++) {
            e(k, keywords_life[j], "life");
        }
        var mSearch;
        var pageSize = 50;
        function e(k, type, s) {
            window.AMap.service(["AMap.PlaceSearch"], function () {
                mSearch = new AMap.PlaceSearch({
                    type: type,
                    pageSize: pageSize
                });
                mSearch.searchNearBy(type, k, distance, function (status, result) {
                    if (status === 'complete' && result.info === 'OK') {
                        var total = result.poiList.count;
                        $("#" + s).parent().next().append("<em>" + total + dan_wei[type] + type + "</em>");
                    }
                });
            });
        }
    }
};
var CRICReport = {
    init: function (data, type) {
        var evalData = data;
        var t = type;
        var bo = false;
        var $allA = $("#evaluation_menu li div cite").find("a");
        hxfx();
        zxbz();
        sqpz();
        wyfw();
        shpt();
        blys();
        d(".ztpj", evalData["整体评价"], $("#a_hxfx"), "整体评价");
        d(".ghxfx", evalData["各户型分析"], $("#a_hxfx"), "各户型分析");
        d(".pppz", evalData["品牌配置"], $("#a_zxbz"), "品牌配置");
        d(".zxfx", evalData["装修分析"], $("#a_zxbz"), "装修分析");
        d(".ztgh", evalData["整体规划"], $("#a_sqpz"), "整体规划");
        d(".sqjg", evalData["社区景观"], $("#a_sqpz"), "社区景观");
        d(".jzlm", evalData["建筑立面"], $("#a_sqpz"), "建筑立面");
        d(".ggbw", evalData["公共部位"], $("#a_sqpz"), "公共部位");
        d(".hspz", evalData["会所配置"], $("#a_sqpz"), "会所配置");
        d(".cwqk", evalData["车位情况"], $("#a_sqpz"), "车位情况");
        d(".wyfy", evalData["物业费用"], $("#a_wyfw"), "物业费用");
        d(".wyfw", evalData["物业服务"], $("#a_wyfw"), "物业服务");
        d(".ggzy", evalData["公共资源"], $("#a_shpt"), "公共资源");
        d(".zbsf", evalData["周边商服"], $("#a_shpt"), "周边商服");
        d(".ylzy", evalData["医疗资源"], $("#a_shpt"), "医疗资源");
        d(".jyzy", evalData["教育资源"], $("#a_shpt"), "教育资源");
        d(".gjcx2", evalData["公交出行"], $("#a_shpt"), "公交出行");
        d(".gjcx", evalData["轨交出行"], $("#a_shpt"), "轨交出行");
        d(".zjcx", evalData["自驾出行"], $("#a_shpt"), "自驾出行");
        d(".qwjj", evalData["区位简介"], $("#a_shpt"), "区位简介");
        d(".sqnb", evalData["社区内部"], $("#a_blys"), "社区内部");
        d(".sqwb", evalData["社区外部"], $("#a_blys"), "社区外部");
        $("#evaluation_menu li").children("a").click(function () {
            $("#evaluation_menu li").removeClass("cur");
            $("#evaluation_menu li div").hide();
            $(this).parent().addClass("cur");
            $(this).next().show();
            var id = $(this).attr("id");
            var fName = id.split("_")[1] + "(true)";
            bo = false;
            eval(fName);
        });
        $("#evaluation_menu li div cite").find("a").click(function () {
            $("#evaluation_menu li div cite").find("a").removeClass("cur");
            $(this).addClass("cur");
            var key = $.trim($(this).text());
            if (evalData[key]) {
                buildHtml(evalData[key]);
            } else {
                buildHtml("");
            }
        });
        function hxfx(flag) {
            if (t == 1 || t == 0 || flag) {
                c($("#a_hxfx").next().children().find(".ztpj"), evalData["整体评价"]);
                c($("#a_hxfx").next().children().find(".ghxfx"), evalData["各户型分析"]);
            }
        }
        function zxbz(flag) {
            if (t == 2 || t == 0 || flag) {
                c($("#a_zxbz").next().children().find(".pppz"), evalData["品牌配置"]);
                c($("#a_zxbz").next().children().find(".zxfx"), evalData["装修分析"]);
            }
        }
        function sqpz(flag) {
            if (t == 3 || t == 0 || flag) {
                c($("#a_sqpz").next().children().find(".ztgh"), evalData["整体规划"]);
                c($("#a_sqpz").next().children().find(".sqjg"), evalData["社区景观"]);
                c($("#a_sqpz").next().children().find(".jzlm"), evalData["建筑立面"]);
                c($("#a_sqpz").next().children().find(".ggbw"), evalData["公共部位"]);
                c($("#a_sqpz").next().children().find(".hspz"), evalData["会所配置"]);
                c($("#a_sqpz").next().children().find(".cwqk"), evalData["车位情况"]);
            }
        }

        function wyfw(flag) {
            if (t == 4 || t == 0 || flag) {
                c($("#a_wyfw").next().children().find(".wyfy"), evalData["物业费用"]);
                c($("#a_wyfw").next().children().find(".wyfw"), evalData["物业服务"]);
            }
        }
        function shpt(flag) {
            if (t == 5 || t == 0 || flag) {
                c($("#a_shpt").next().children().find(".qwjj"), evalData["区位简介"]);
                c($("#a_shpt").next().children().find(".zjcx"), evalData["自驾出行"]);
                c($("#a_shpt").next().children().find(".gjcx"), evalData["轨交出行"]);
                c($("#a_shpt").next().children().find(".gjcx2"), evalData["公交出行"]);
                c($("#a_shpt").next().children().find(".jyzy"), evalData["教育资源"]);
                c($("#a_shpt").next().children().find(".ylzy"), evalData["医疗资源"]);
                c($("#a_shpt").next().children().find(".zbsf"), evalData["周边商服"]);
                c($("#a_shpt").next().children().find(".ggzy"), evalData["公共资源"]);
            }
        }
        function blys(flag) {
            if (t == 6 || t == 0 || flag) {
                c($("#a_blys").next().children().find(".sqnb"), evalData["社区内部"]);
                c($("#a_blys").next().children().find(".sqwb"), evalData["社区外部"]);
            }
        }
        function c($thisA, url) {
            if (!bo) {
                if (url) {
                    $allA.removeClass("cur");
                    $thisA.parents("li").siblings("li").removeClass("cur");
                    $thisA.parents("li").addClass("cur");
                    $thisA.addClass("cur");
                    buildHtml(url);
                    bo = true;
                }
            }
        }
        function d(thisA, url, $thisAType, text) {
            if (!url) {
                $thisAType.next().children().find(thisA).replaceWith("<span>" + text + "</span>");
            }
        }
        function h(result) {
            if (result.id) {
                var id = result.id;
                var height = parseInt(result.height);
                if (height) {
                    height = height + 60;
                }
                $("#" + id).height(height);
            }
        }
        function buildHtml(urls) {
            if (!urls) {
                return;
            }
            $("#iframes").empty();
            var urlArray = urls.split(',');
            for (var i = 0; i < urlArray.length; i++) {
                var url = urlArray[i].split(';');
                var iframe = "<iframe src=\"" + url[0] + "\" id=\"" + url[1] + "\" name=\"" + url[1] + "\" width=\"100%\" height=\"800px\" style=\"border: 0px\" scrolling='no'></iframe>";
                $("#iframes").append(iframe);
                XD.receiveMessage(function (result) {
                    h(result);
                });
            }
        }
    }
};
var RecommU = {
    init: function (cityCode, regionPinYin, unitid, homeId) {
        $.post("/" + cityCode + "/Newhouse/detail/getRecommendedUnit", {
            homeId: homeId, cityCode: cityCode,unitid:unitid
        }, function (result) {
            var units = result.data;
            for (var i = 0, item; i < units.length, item = units[i]; i++) {
                $("#recommended_unit_list").append(BuildJS.buildHtml(item, $("#recommended_unit_list_template").html()));
            }
        }, "json");
    }
};

var DynamicInfo = {
    init: function (cityCode, regionPinYin, id) {
        //楼盘动态及资讯
        $.post("/" + cityCode + "/Newhouse/detail/getUnitDynamic", {
            unitid: id
        }, function (result) {
            if (result.data.dataCode == 1) {
                var newsDynamic = result.data.unitDynamic;
                //动态不为空
                if (newsDynamic.total > 0) {
                    $("#unit_dynamic_info").show();
                    var newsDynamicData = newsDynamic.data;
                    var html = "";
                    for (var i = 0; i < newsDynamicData.length; i++) {
                        html += "  <b>" + newsDynamicData[i].title + "</b>" + newsDynamicData[i].content + "<span>" + unixTimeFormat(newsDynamicData[i].createtime, "-") + "</span>";
                    }
                    $("#unit_dynamic_info .last_news .last_news_dt").html(html);
                }
            }

        }, "json");
    }
};
var SurrUnit = {
    init: function (cityCode, regionPinYin, regionName, unitId) {
        $.post("/" + cityCode + "/" + regionPinYin + "/surrounding/xf", {
            unitId: unitId, cityCode: cityCode, regionName: regionName
        }, function (data) {
            var units = data;
            for (var i = 0, item; i < units.length, item = units[i]; i++) {
                $("#surrounding_unit_list").append(BuildJS.buildHtml(item, $("#surrouding_unit_template").html()));
            }
        }, "json");
    }
};
var LoanC = {
    init: function (blocks, cityCode, regionPinYin, unitId, calcType) {
        if (calcType == 0) {
            return;
        }
        var rooms;
        $("#block_select").change(function () {
            LoanC.filterRoomByBlock(rooms);
        });
        $("#room_select").change(function () {
            LoanC.getTotalPriceByRoom(cityCode, unitId);
        });
        for (var i = 0, b; i < blocks.length, b = blocks[i]; i++) {
            $("#block_select").append("<option value='" + b.BlockID + "'>" + b.BlockNumber + "</option>");
        }
        loadRooms();
        LoanC.filterRoomByBlock(rooms);
        LoanC.getTotalPriceByRoom(cityCode,unitId);
        LoanC.calculator(calcType);
        function loadRooms() {

            $.ajax({
                type: 'get',
                url: "/" + cityCode +"/Newhouse/detail/getUnitRooms",
                data: { unitid: unitId },
                dataType: 'json',
                async: false,
                success: function (result) {
                    rooms = result.data;
                }
            })
           // $.get("http://web.cjj.dev.ipo.com/" + cityCode +"/Newhouse/detail/getUnitRooms?unitid="+unitId);
        }
    },
    calculator: function (calcType) {
        var zongjia = "";

        if (calcType == 0) {//没有房间
            zongjia = $("#txtTotalPrice").val();
        }
        else if (calcType == 1) {//有房间
            zongjia = $("#zongjia").html();
        }

        if (!zongjia) {
            $("#container").html("");
            return;
        }
        var anjie = $("#anjie_select option:selected").attr("value");
        var qishu = $("#qishu_select option:selected").attr("value");
        var huankuanfangshi = 1;
        var lilv = $("#lilv_select option:selected").attr("value");
        var d = $("#lilv_select option:selected").attr("d");
        zongjia = zongjia.replace(",", "").replace("万元", "");
        $.ajax({
            type: 'post', dataType: 'json', url: "/Calculator/Index/",
            data: {
                "zongjia": zongjia * 10000, "anjie": anjie, "qishu": qishu, "lilv": lilv * d, "huankuanfangshi": huankuanfangshi
            },
            success: function (result) {
                data = result.data;
                var shoufu = Math.round(data.Shoufu / 10000);
                $("#shouFu").html(shoufu + "万");
                var daikuan = Math.round(data.DaiKuan / 10000);
                $("#zonge").html(daikuan + "万");
                $("#huangkuan").html(Math.round(data.YueHuanKuan[0]));
                var lixi = Math.round(data.Lixi / 10000);
                $("#liXi").html(lixi + "万");
                var dataArray = [["首期付款", shoufu], ["支付利息", lixi], ["贷款总额", daikuan]];
                DrawLoanPieCircle("container", dataArray);
            }
        });
    },
    filterRoomByBlock: function (rooms) {
        $("#room_select").empty();
        var $option = $("#block_select");
        if (!rooms || $option.val() == 0) {
            $("#zongjia").html("");
            $("#zongmianji").html("");
            $("#zhidaojia").html("");
            return;
        }
        for (var i = 0, r; i < rooms.length, r = rooms[i]; i++) {
            if ($option.val() != r.BlockId) {
                continue;
            }
            $("#room_select").append("<option value='" + r.RoomID + "'>" + r.RoomNumber + "</option>");
        }
    },
    getTotalPriceByRoom: function (cityCode,unitid) {
        var $roomSelect = $("#room_select");
        var $blockSelect = $("#block_select");
        var blockId = $blockSelect.val();
        var roomId = $roomSelect.val();
        if (blockId == 0 || roomId == 0) {
            $("#zongjia").html("");
            $("#zongmianji").html("");
            $("#zhidaojia").html("");
            return;
        }
        $.ajax({
            type: 'post',
            url: "/" + cityCode + "/Newhouse/detail/getRoomPrice",
            data: { blockId: blockId, roomId: roomId ,unitid:unitid},
            dataType: 'json',
            async: false,
            success: function (result) {
                data = result.data;
                if (data.dataCode == 0) {
                    $("#zongjia").html(data.TotalPrice + "万元");
                    $("#zongmianji").html("面积" + data.Area + "㎡");
                    $("#zhidaojia").html("指导价" + data.Price + "元/㎡");
                } else {
                    //alert("服务器忙，请稍后再试");
                }
            }
        });
    }
};
var ToComment = {
    picCodes: "",
    cityCode: "",
    init: function (cityCode, regionName, regionPinYin) {
        ToComment.cityCode = cityCode;
        ToComment.loadHotComment(cityCode, regionName, regionPinYin);
        var name = getQueryString("n");
        var text = getQueryString("t");
        if (name && text) {
            $("#user_dp").html("<em>" + decodeURI(name) + "</em>" + decodeURI(text));
            $("#user_dp").parent().show();
        }
        $(".w_comment_min span").each(function () {
            if ($(this).width() >= 680) {
                $(this).width(680);
                $(this).next().show();
            } else {
                $(this).next().hide();
            }
        });
        $(".w_comment_min s").toggle(function () {
            $(this).addClass("up");
            $(this).prev().css("white-space", "normal");
        }, function () {
            $(this).removeClass("up");
            $(this).prev().css("white-space", "nowrap");
        });
        var interval = setInterval(function () {
            if ($(".WB_loginButton .login_a")[0]) {
                $(".WB_loginButton .login_a").html("[绑定]");
                clearInterval(interval);
            }
        }, 200);
        $("#comment_pic").uploadify({
            swf: "/Scripts/uploadify/uploadify.swf",
            uploader: "/cpic/upload",
            auto: true,
            multi: true,
            fileTypeDesc: "Image Files",
            fileTypeExts: "*.jpg;*.bmp;*.png;*.jpeg",
            buttonText: "上传图片",
            queueID: "fileQueue",
            queueSizeLimit: 8,
            simUploadLimit: 2,
            fileSizeLimit: "2MB",
            overrideEvents: ["onSelectError", "onUploadError", "onDialogClose"],
            onSelect: function (e, queueId, fileObj) {
                $(".up_pictures").show();
                var fileName = e.name;
                $("#uploadpic_list li").each(function () {
                    var name = $(this).attr("filename");
                    if (fileName == name) {
                        $("#comment_pic").uploadify('cancel', e.id);
                        return false;
                    }
                    return true;
                });
            },
            //返回一个错误，选择文件的时候触发
            onSelectError: function (file, errorCode, errorMsg) {
                switch (errorCode) {
                    case -100:
                        alert("上传的文件数量已经超出系统限制的8个文件！");
                        break;
                    case -110:
                        alert("文件 [" + file.name + "] 大小超出系统限制的2MB大小！");
                        break;
                    case -120:
                        alert("文件 [" + file.name + "] 大小异常！");
                        break;
                    case -130:
                        alert("文件 [" + file.name + "] 类型不正确！");
                        break;
                }
                return false;
            },
            onFallback: function () {
                alert("您未安装FLASH控件，无法上传图片！请安装FLASH控件后再试。");
            },
            onUploadError: function () {
            },
            onDialogClose: function () {
            },
            onQueueComplete: function (stats) {
                $(".up_pictures").hide();
            },
            onUploadSuccess: function (file, data, response) {
                var result = eval('(' + data + ')');
                if (result.dataCode == 0) {
                    if (ToComment.picCodes) {
                        ToComment.picCodes += "," + result.imgCode;
                    } else {
                        ToComment.picCodes = result.imgCode;
                    }
                    $("#uploadpic_list").append("<li filename='" + result.fileName + "'><cite><img src ='" + result.imgUrl + "' alt =''/><s onclick=\"ToComment.deletePic('" + result.imgCode + "',this)\"></s></cite></li>");
                    $("#uploadpic_list").parent().show();
                } else {
                    alert(result.errorMessage);
                }
                //$(".up_pictures").hide();
            }
        });
    },
    login: function (a, r, p) {
        showBg('loginDialog');
        $("#account").val(a);
        if (r == 1) {
            $("#pwd").val(p);
            $("#rememberMe").attr("checked", "checked");
            $("#remb").val(1);
        }
        loginSubmit(true, "partial");
    },
    submit: function (unitId, unitName, unitIntId, token) {
        //提交点评
        var content = $.trim($("#to_comment_content").val());
        if (content.length == 0 || content == "请输入评论内容...") {
            alert("请输入评论信息");
            return;
        }
        var anonymous = 0;
        var ck = $("#ck_anonymous")[0];
        if (ck) {
            var status = $(ck).attr('checked');
            if (status == "checked") {
                anonymous = 1;
            }
        }
        var syschronous = 0;
        var ckSysch = $("#ck_syschronous")[0];
        if (ckSysch) {
            var status = $(ckSysch).attr("checked");
            if (status == "checked") {
                syschronous = 1;
            }
        }
        var cid = getQueryString("cid");
        var type = getQueryString("type");
        var cpid = "";
        var t = 1;
        if (cid && parseInt(type) == 0) {
            cpid = cid;
            t = type;
        }
        var parameter = {
            UnitId: unitId,
            UnitName: encodeURIComponent(unitName),
            Url: location.href,
            Category: 1,
            CommentContent: content,
            IsAnonymous: anonymous,
            IsSynchronousWeiBo: syschronous,
            UnitIntId: unitIntId,
            PicCodes: ToComment.picCodes,
            DpType: t,
            UnitCommentPid: cpid
        };
        $.post("/unit/comment", parameter, function (data) {
            ToComment.successCallBack(data);
            if (syschronous == 1 && token) {
                WB2.anyWhere(function (W) {
                    //数据交互
                    W.parseCMD('/statuses/update.json', function (oResult, bStatus) {
                    }, {
                        access_token: token,
                        status: encodeURI(content)
                    });
                });
            }
        });
    },
    successCallBack: function (result) {
        if (result.dataCode == 5) {
            alert(result.errorMessage);
            return;
        }
        window.location = $("#ucomment_url").val();
    },
    loadHotComment: function (cityCode, regionName, regionPinYin) {
        $.post("/" + cityCode + "/" + regionPinYin + "/hotcomment/xf", {
            cityCode: cityCode, regionName: encodeURI(regionName)
        }, function (result) {
            if (result.dataCode == 0) {
                for (var i = 0, item; i < result.units.length, item = result.units[i]; i++) {
                    $("#hotcomment_list").append(BuildJS.buildHtml(item, $("#hotcomment_listtemplate").html()));
                }
            }

        });
    },
    deletePic: function (picCode, target) {
        $(target).parent().parent().remove();
        if ($("#uploadpic_list li").length > 0) {
            $("#uploadpic_list").parent().show();
        } else {
            $("#uploadpic_list").parent().hide();
        }
        var array = ToComment.picCodes.split(",");
        var index = array.indexOf(picCode);
        array.splice(index, 1);
        ToComment.picCodes = array.join(",");
    }
};
var UC = {
    page: 1,
    pageSize: 9,
    replayUrl: "",
    infoUrl: "",
    totalPage: 1,
    picApi: null,
    result: {},
    startSize: 0,
    init: function (rUrl, unitId, unitIntId, cityCode, rPinYin, infoUrl) {
        UC.replayUrl = rUrl;
        UC.infoUrl = infoUrl;
        UC.dynamicInfo(cityCode, rPinYin, unitIntId);
        UC.loadComment(unitId, unitIntId);
        $(window).bind("scroll", function () { UC.nextPage(unitId, unitIntId); });
    },
    support: function (analystId, cTarget) {
        $.post("/feedback/AnalystsListUp", {
            AnalystsID: analystId
        }, function (result) {
            var gCount = $(cTarget).children("span").text();
            $(cTarget).children("span").text(parseInt(gCount) + 1);
            $(cTarget).removeAttr("onclick");
        }, "json");
    },
    dynamicInfo: function (cityCode, rPinYin, unitIntId) {
        var num = 4;
        //楼盘动态及资讯
        $.post("/" + cityCode + "/" + rPinYin + "/xf/dynamic", {
            cityCode: cityCode, id: unitIntId, dynamicNum: num
        }, function (data) {
            if (data.dataCode == 1) {
                var newsDynamic = data.unitDynamic;
                if (newsDynamic.total > 0) {
                    $("#dyn_pro_list").parent().parent().parent().show();
                    for (var i = 0, item; i < newsDynamic.data.length, item = newsDynamic.data[i]; i++) {
                        $("#dyn_pro_list").append("<li><a href=\"" + UC.infoUrl + "?t=dy\" target='_blank'>" + item.title + "</a></li>");
                    }

                }
            }
        }, "json");
    },
    cheapOrExpensive: function (flag, cityCode, rPinYin, unitId) {
        $.post("/" + cityCode + "/" + rPinYin + "/unit/cheaporexpensive", {
            unitId: unitId, cheapOrExpensive: flag
        }, function (result) {
            var data = result.obj;
            $("#rate_tucao_m").hide();
            var total = data.Cheap + data.Expensive;
            var cheap = parseInt(data.Cheap / total * 100);
            var expensive = parseInt(data.Expensive / total * 100);
            $("#rate_result .rate_results_hirt i").css("width", expensive + "%");
            $("#rate_result .rate_results_hirt s").css("left", cheap + "%");
            $("#rate_result .rate_results_text .ct_left em").html(cheap + "%用户选择");
            $("#rate_result .rate_results_text .ct_right em").html(expensive + "%用户选择");
            $("#rate_result").show();
        });
    },
    loadComment: function (unitId, unitIntId) {
        var url = "/unit/comment/" + unitId + "/" + UC.page;
        $.post(url, {
            unitIntId: unitIntId, pageSize: UC.pageSize
        }, function (result) {
            UC.successCallBack(result);
        });
    },
    successCallBack: function (result) {
        UC.result = result;
        if (result.dataCode == 1) {
            var data = result.unitComments;
            $("#all_comment_count").html("所有点评（" + result.totalCount + "）");
            UC.totalPage = result.totalPage;
            var count = 0;
            for (var i = UC.startSize, item; i < data.length, item = data[i]; i++) {
                if (count > UC.pageSize) {
                    break;
                }
                UC.startSize++;
                count++;
                var html = "";
                var url = UC.replayUrl + "?n=" + encodeURI("回复" + item.ShowUserName + "点评：") + "&t=" + encodeURI(item.CommentContent.length > 200 ? item.CommentContent.substring(0, 200) : item.CommentContent) + "&type=0&cid=" + item.UnitCommentId;
                var dlClass = "clearfix com_info";
                if (i == data.length - 1) {
                    dlClass += " last";
                }
                html += "<dl class=\"" + dlClass + "\"><dt>";
                if (item.Avatar && item.IsAnonymous == 0) {
                    html += "<img src='" + item.AvatarUrl + "' alt=''/>";
                } else {
                    html += "<img src=\"/Content/images/yk_img.png\" alt=''/>";
                }
                html += "</dt><dd><cite class=\"ct_name\"><b>" + item.ShowUserName;
                if (item.IsAnalyst) {
                    html += "<i></i>";
                }
                html += "</b></cite>";
                if (item.DpType == 0) {
                    for (var j = 0, dpItem; j < data.length, dpItem = data[j]; j++) {
                        if (dpItem.DpType == 0 && dpItem.UnitCommentId != item.UnitCommentPid) {
                            continue;
                        }
                        if (dpItem.UnitCommentId == item.UnitCommentPid) {
                            html += "<p class=\"comment_min\"><span>回复" + dpItem.ShowUserName + "评论：" + dpItem.CommentContent + "</span><s></s></p>";
                            break;
                        }
                    }
                }
                html += "<p>" + item.CommentContent + "</p>";

                if (item.PicCodes) {
                    html += " <ul class=\"clearfix\">";
                    var picUrls = item.PicUrls.split(",");
                    var originalPicUrls = item.OriginalPicUrls.split(",");
                    for (var j = 0; j < picUrls.length; j++) {
                        html += "<li><img src='" + picUrls[j] + "' alt='' srcurl='" + originalPicUrls[j] + "'/></li>";
                    }

                    html += "</ul><a href=\"javascript:void(0)\" onclick='UC.showBigPic(this)' class=\"more_img\">一共" + picUrls.length + "张图片&gt;</a>";
                }
                html += "<div class=\"data_info clearfix\"><span>" + item.ShowCommentDate + "</span><a onclick=\"UC.commentSupport('" + item.UnitCommentId + "',this)\" href=\"javascript:void(0)\">支持<em>";
                if (item.SupportCount > 0) {
                    html += "(" + item.SupportCount + ")";
                }
                var shareContent = item.CommentContent.length > 200 ? item.CommentContent.substring(0, 200) : item.CommentContent;
                var shareUrl = $("#shareUrl").val();
                html += "</em></a><i>|</i><cite><a href=\"javascript:void(0)\" onclick=\"$(this).next().toggle()\">分享</a><em style='display:none;'><s></s><var class=\"sina_weibo\" onclick=\"window.Share(window.ShareUrl.sina,'','" + shareContent + "')\"></var><var class=\"tt_weibo\" onclick=\"window.Share(window.ShareUrl.qq,'','" + shareContent + "')\"></var><var onclick=\"jiathis_sendto('weixin');return false;\" class=\"jiathis_button_weixin tt_weixin\"></var><var class=\"tt_zone\" onclick=\"window.Share(window.ShareUrl.qzone,'','" + shareContent + "')\"></var><var class=\"tt_qq\" onclick=\"window.Share(window.ShareUrl.c_qq,'','" + shareContent + "')\"></var><var class=\"renren_icon\" onclick=\"window.Share(window.ShareUrl.renren,'','" + shareContent + "')\"></var><var class=\"cric_ewm\" onmouseover=\"$(this).next().show();\" onmouseout=\"$(this).next().hide();\"></var><img alt='' src='/qrcode?text=" + shareUrl + "&width=90&height=90&w=90'/></em></cite><i>|</i><a href=\"" + url + "\" target=\"_blank\">回复</a></div></dd></dl>";
                $("#comment_list .pl_loading").remove();
                $("#comment_list").append(html);
            }
            $(".comment_min span").each(function () {
                if ($(this).width() >= 570) {
                    $(this).width(570);
                    $(this).next().show();
                } else {
                    $(this).next().hide();
                }
            });
            $(".comment_min s").toggle(function () {
                $(this).addClass("up");
                $(this).prev().css("white-space", "normal");
            }, function () {
                $(this).removeClass("up");
                $(this).prev().css("white-space", "nowrap");
            });
        }
    },
    commentSupport: function (commentId, cTarget) {
        if (commentId) {
            $.post("/unit/comment/" + commentId, function (result) {
                $(cTarget).removeAttr("onclick");
                var emText = $(cTarget).children("em").text();
                if (emText) {
                    var count = emText.replace("(", "").replace(")", "");
                    if (count) {
                        $(cTarget).children("em").html("(" + (parseInt(count) + 1) + ")");
                    }
                } else {
                    $(cTarget).children("em").html("(1)");
                }
            });
        }
    },
    nextPage: function (unitId, unitIntId) {
        var top = $("#bfdRecommended").offset().top - 1000;
        var scrollTop = document.body.scrollTop;

        if (scrollTop >= top) {
            if (UC.page < UC.totalPage) {
                UC.page = UC.page + 1;
                UC.successCallBack(UC.result);
                ////正在加载动画
                //if (!$("#comment_list .pl_loading")[0]) {
                //    $("#comment_list").append("<div class='pl_loading'><img src='/Content/images/pl_loading.gif' alt=''/><span>正在加载</span></div>");
                //}
                // UC.loadComment(unitId, unitIntId);
            }
        }
    },
    showBigPic: function (target) {
        var img = $(target).parent().find("img");
        var html = "";
        $.each(img, function (i, val) {
            html += "<img src='" + $(this).attr("srcurl") + "'  style='width:auto;height:auto' alt='' />";
        });
        $("#Prcboxside").html(html);
        $("#Proalbumbox .album_sz").children("em").html(img.length);
        UC.picApi = $('#Prcboxside').switchable({
            triggers: '&bull;',
            putTriggers: 'insertBefore',
            effect: 'fade',
            easing: 'ease-in-out',
            prev: '#album_prev',
            next: $('#album_next'),
            onSwitch: function (event, currentIndex) {
                var api = this;
                if (currentIndex < api.length) {
                    $("#Proalbumbox .album_sz").children("span").html(parseInt(currentIndex) + 1);
                }
            },
            api: true
        });
        $("#Proalbumbox .album_sz").children("span").html(1);
        showBg("Proalbumbox");
    }
};

//首页热门小区
var HotComment = {
    LoadHotComment: function (cityCode, regionName, regionPinYin) {
        $.post("/" + cityCode + "/" + regionPinYin + "/hotcomment/xf", {
            cityCode: cityCode, regionName: encodeURI(regionName)
        }, function (result) {
            if (result.dataCode == 0) {
                if (result.units.length > 0) {
                    for (var i = 0, item; i < result.units.length, item = result.units[i]; i++) {
                        if (i < 3) {
                            $("#hotcomment_list").append(HotComment.buildHtml(item, $("#hotCommentsTemplate").html()));
                        }
                    }
                    $("#hotcomment_list").parent().show();
                }
            }
        });
    },
    buildHtml: function (data, templateHtml) {
        var html = templateHtml;
        html = html.replace(/\.srcX/gi, ".src");
        for (var p in data) {
            var reg = new RegExp("\\$" + p + "\\b", "gi");
            html = html.replace(reg, data[p] || "-");
        }
        return html;
    }

};
//首页楼盘点评相关评论
var LPDP_UC = {
    page: 1,
    pageSize: 3,
    picApi: null,
    replayUrl: "",
    UnitName: "",
    CommentUrl: "",
    init: function (rUrl, unitId, unitIntId, cityCode, rPinYin, unitName, commentUrl) {
        LPDP_UC.replayUrl = rUrl;
        LPDP_UC.UnitName = unitName;
        LPDP_UC.CommentUrl = commentUrl;
        LPDP_UC.loadComment(unitId, unitIntId);
    },
    loadComment: function (unitId, unitIntId) {
        var url = "/unit/comment/" + unitId + "/" + LPDP_UC.page;
        $.post(url, {
            unitIntId: unitIntId, pageSize: LPDP_UC.pageSize
        }, function (result) {
            LPDP_UC.successCallBack(result);
        });
    },
    successCallBack: function (result) {
        if (result.dataCode == 1) {
            var data = result.unitComments;

            if (data.length > 0) {

                for (var i = 0, item; i < data.length, item = data[i]; i++) {
                    if (i > 2) {
                        break;
                    }
                    var html = "";
                    var url = LPDP_UC.replayUrl + "?n=" + encodeURI("回复" + item.ShowUserName + "点评：") + "&t=" + encodeURI(item.CommentContent.length > 200 ? item.CommentContent.substring(0, 200) : item.CommentContent) + "&type=0&cid=" + item.UnitCommentId;
                    //var url = LPDP_UC.replayUrl + "?n=" + item.ShowUserName;
                    var dlClass = "clearfix com_info";
                    if (i == data.length - 1) {
                        dlClass += " last";
                    }
                    html += "<dl class=\"" + dlClass + "\"><dt>";
                    if (item.Avatar && item.IsAnonymous == 0) {
                        html += "<img src='" + item.AvatarUrl + "' alt=''/>";
                    } else {
                        html += "<img src=\"/Content/images/yk_img.png\" alt=''/>";
                    }

                    html += "</dt><dd><cite class=\"ct_name\"><b>" + item.ShowUserName;
                    if (item.IsAnalyst) {
                        html += "<i></i>";
                    }
                    html += "</b></cite>";
                    if (item.DpType == 0) {
                        for (var j = 0, dpItem; j < data.length, dpItem = data[j]; j++) {
                            if (dpItem.DpType == 0 && dpItem.UnitCommentId != item.UnitCommentPid) {
                                continue;
                            }
                            if (dpItem.UnitCommentId == item.UnitCommentPid) {
                                html += "<p class=\"comment_min\"><span>回复" + dpItem.ShowUserName + "评论：" + dpItem.CommentContent + "</span><s></s></p>";
                                break;
                            }
                        }
                    }
                    html += "<p>" + item.CommentContent + "</p>";
                    //html += "</b></cite><p>" + item.CommentContent + "</p>";
                    if (item.PicCodes) {
                        html += " <ul class=\"clearfix\">";
                        var picUrls = item.PicUrls.split(",");
                        var originalPicUrls = item.OriginalPicUrls.split(",");
                        for (var j = 0; j < picUrls.length; j++) {
                            html += "<li><img src='" + picUrls[j] + "' alt='' srcurl='" + originalPicUrls[j] + "' /></li>";
                        }

                        html += "</ul><a href=\"javascript:void(0)\" onclick=\"LPDP_UC.showBigPic(this)\" class=\"more_img\">一共" + picUrls.length + "张图片&gt;</a>";
                    }
                    html += "<div class=\"data_info clearfix\"><span>" + item.ShowCommentDate + "</span><a onclick=\"LPDP_UC.commentSupport('" + item.UnitCommentId + "',this)\" href=\"javascript:void(0)\">支持<em>";
                    if (item.SupportCount > 0) {
                        html += "(" + item.SupportCount + ")";
                    }
                    var shareContent = item.CommentContent.length > 200 ? item.CommentContent.substring(0, 200) : item.CommentContent;
                    var shareUrl = $("#shareUrl").val();
                    html += "</em></a><i>|</i><cite><a href=\"javascript:void(0)\" onclick=\"$(this).next().toggle()\">分享</a><em style='display:none;'><s></s><var class=\"sina_weibo\" onclick=\"window.Share(window.ShareUrl.sina,'','" + shareContent + "')\"></var><var class=\"tt_weibo\" onclick=\"window.Share(window.ShareUrl.qq,'','" + shareContent + "')\"></var><var onclick=\"jiathis_sendto('weixin');return false;\" class=\"jiathis_button_weixin tt_weixin\"></var><var class=\"tt_zone\" onclick=\"window.Share(window.ShareUrl.qzone,'','" + shareContent + "')\"></var><var class=\"tt_qq\" onclick=\"window.Share(window.ShareUrl.c_qq,'','" + shareContent + "')\"></var><var class=\"renren_icon\" onclick=\"window.Share(window.ShareUrl.renren,'','" + shareContent + "')\"></var><var class=\"cric_ewm\" onmouseover=\"$(this).next().show();\" onmouseout=\"$(this).next().hide();\"></var><img alt='' src='/qrcode?text=" + shareUrl + "&width=90&height=90&w=90'/></em></cite><i>|</i><a href=\"" + url + "\" target=\"_blank\">回复</a></div></dd></dl>";
                    $("#CommentList").append(html);
                    $(".comment_min span").each(function () {
                        if ($(this).width() >= 570) {
                            $(this).width(570);
                            $(this).next().show();
                        } else {
                            $(this).next().hide();
                        }
                    });
                    $(".comment_min s").toggle(function () {
                        $(this).addClass("up");
                        $(this).prev().css("white-space", "normal");
                    }, function () {
                        $(this).removeClass("up");
                        $(this).prev().css("white-space", "nowrap");
                    });
                }
            }
            else {
                $("#CommentList").css("display", "none");
                $("#CommentList").parent().append("<div class=\"yh_com_patry\" ><b>您觉得  " + this.UnitName + "  怎么样？<br />欢迎写出您的想法。</b><a href=\"" + this.CommentUrl + "\" target=\"_blank\">我要说几句</a></div>");
                $("#user_comment").hide();
            }
        }
    },
    commentSupport: function (commentId, cTarget) {
        if (commentId) {
            $.post("/unit/comment/" + commentId, function (result) {
                $(cTarget).removeAttr("onclick");
                var emText = $(cTarget).children("em").text();
                if (emText) {
                    var count = emText.replace("(", "").replace(")", "");
                    if (count) {
                        $(cTarget).children("em").html("(" + (parseInt(count) + 1) + ")");
                    }
                } else {
                    $(cTarget).children("em").html("(1)");
                }
            });
        }
    },
    showBigPic: function (target) {
        var img = $(target).parent().find("img");
        var html = "";
        $.each(img, function (i, val) {
            html += "<img src='" + $(this).attr("srcurl") + "'  style='width:auto;height:auto' alt='' />";
        });
        $("#unitPictureName").html("评论图片");
        $("#Prcboxside").html(html);
        $("#Proalbumbox .album_sz").children("em").html(img.length);

        LPDP_UC.picApi = $('#Prcboxside').switchable({
            triggers: '&bull;',
            putTriggers: 'insertBefore',
            effect: 'fade',
            easing: 'ease-in-out',
            loop: false,
            prev: '#album_prev',
            next: $('#album_next'),
            onSwitch: function (event, currentIndex) {
                var api = this;
                if (currentIndex < api.length) {
                    $("#Proalbumbox .album_sz").children("span").html(parseInt(currentIndex) + 1);
                }
                api.prevBtn.toggleClass('disabled', currentIndex === 0);
                api.nextBtn.toggleClass('disabled', currentIndex === api.length - 1);

            },
            api: true
        });
        $("#Proalbumbox .album_sz").children("span").html(1);
        LPDP_UC.picApi.prevBtn.removeClass("disabled");
        LPDP_UC.picApi.nextBtn.removeClass("disabled");
        showBg("Proalbumbox");
    }
}

//首页
var Index = {
    logoPic: "",
    init: function (sUploadUrl,sStaticRoot) {
        Index.logoUpload(sUploadUrl,sStaticRoot);
    },
    loadUnitPicture: function (urlArr, type) {
        var html = "";

        switch (type) {
            case "1":
                $("#unitPictureName").html("社区实景");
                break; //社区实景
            case "2":
                $("#unitPictureName").html("效果图");
                break; //效果图
            case "3":
                $("#unitPictureName").html("总平图");
                break; //总平图
            case "4":
                $("#unitPictureName").html("楼盘周边");
                break; //楼盘周边
        }

        for (var i = 0; i < urlArr.length; i++) {
            if (i == 0) {
                html = html + '<img src="' + urlArr[i].ImageUrl + '" alt="">';
            } else {
                html = html + '<img src="" imgurl="' + urlArr[i].ImageUrl + '" alt="">';
            }
        }

        $(".album_sz").html("1/" + urlArr.length);

        $("#Prcboxside").html(html);
        $('#Prcboxside').switchable({
            triggers: '&bull;',
            putTriggers: 'insertBefore',
            effect: 'fade',
            /* fade effect only supports steps == 1 */
            //steps: 1,
            easing: 'ease-in-out',
            loop: false,
            prev: '#album_prev',
            next: $('#album_next'),
            onSwitch: function (event, currentIndex) {
                $("#Prcboxside").find("img").eq(currentIndex).each(function () {
                    if ($(this).attr("src") == "")
                        $(this).attr("src", $(this).attr("imgurl"));
                });

                var api = this;
                api.prevBtn.toggleClass('disabled', currentIndex === 0);
                api.nextBtn.toggleClass('disabled', currentIndex === api.length - 1);
                if (currentIndex < api.length) {
                    $(".album_sz").html((currentIndex + 1) + "/" + this.length);
                }

            }
        });
        showBg('Proalbumbox');
    },
    addDeveloperInfo: function (unitId, UnitName) {
        if ($.trim($("#txtDeveloperName").val()) == "") {
            alert("开发商名称不能为空！");
            return;
        }

        if ($.trim(Index.logoPic) == "") {
            alert("未上传Logo图标！");
            return;
        }

        if ($.trim($("#txtCommnet").val()) == "") {
            alert("点评内容不能为空！");
            return;
        }

        if ($.trim($("#txtCommnet").val()).length > 70) {
            alert("点评内容字数不能超过70！");
            return;
        }

        if ($.trim($("#txtContactName").val()) == "") {
            alert("联系人姓名不能为空！");
            return;
        }

        if ($.trim($("#txtContactPhone").val()) == "") {
            alert("联系电话不能为空！");
            return;
        }

        var data = {
            unitid: unitId,
            unitName: encodeURI(UnitName),
            developerName: encodeURI($("#txtDeveloperName").val()),
            logoPic: Index.logoPic,
            comment: encodeURI($("#txtCommnet").val()),
            contactName: encodeURI($("#txtContactName").val()),
            contactPhone: $("#txtContactPhone").val()
        };

        $.post("/Newhouse/detail/addDeveloperInfo", data, function (result) {
            data = result.data;
            if (data.code == 1) {
                hideDeveloperInfo();
                $("#developerBox").css("display", "block");
                start = 3;
                countdown("emDeveloperTime", "developerBox");
            } else {
                alert("网络异常，请稍后重试！");
                return;
            }

        }, "json");

    },
    hideFloatWindow: function (id) {
        $("#" + id).css("display", "none");
    },
    charCountCheck: function (obj) {
        var comment = $(obj).val();

        if (comment.length < 70) {
            $("#lessCharLen").html(70 - comment.length);
        } else {
            $("#lessCharLen").html(0);
            return false;
        }
    },
    logoUpload: function (sUploadUrl,sStaticRoot) {
        $("#uploadLogo").each(function() {
            var ele = this;
            var ccuploader = new plupload.Uploader({
                runtimes : 'html5,flash,silverlight,html4',
                browse_button : ele,
                url : sUploadUrl,
                flash_swf_url : sStaticRoot + '/plupload/Moxie.swf',
                silverlight_xap_url : sStaticRoot + '/plupload/Moxie.xap',
                filters : {
                    max_file_size : '2mb',
                    mime_types: [
                        {title : "Image files", extensions : "jpg,jpeg,gif,png"}
                    ]
                },
                init: {
                    FilesAdded: function(up, files) {
                        ccuploader.start();
                    },
                    FileUploaded: function(up, file, ret) {
                        eval('var tmp=' + ret.response);
                        if (tmp.iError != 0) {
                            alert(tmp.msg);
                            return true;
                        }
                        var file = tmp.file.sKey + '.' + tmp.file.sExt;
                        Index.logoPic = file;
                        $("#logoMsg").html("图标已上传");
                    }
                }
            });
            ccuploader.init();
        });
    }

};

var IAD = {
    dynamicNum: 6,
    infoNum: 6,
    page: 1,
    init: function (cityCode, regionPinYin, id) {
        $.post("/" + cityCode + "/" + regionPinYin + "/xf/dynamic", {
            cityCode: cityCode, id: id, dynamicNum: IAD.dynamicNum, infoNum: IAD.infoNum, page: IAD.page
        }, function (data) {
            if (data.dataCode == 1) {
                var newsDynamic = data.unitDynamic;
                var newsDynamicData = newsDynamic.data;
                for (var j = 0, itemDynamic; j < newsDynamicData, itemDynamic = newsDynamicData[j]; j++) {
                    var html = BuildJS.buildHtml(itemDynamic, $("#dynamic_template").html());
                    $("#iad_info").parents(".detail_eval_left").append(html);
                }
                var t = getQueryString("t");
                if (t == "dy") {
                    $("#iad_dynamic").trigger("click");
                } else {
                    $("#iad_info").trigger("click");
                }
            }

        }, "json");

        $("#iad_info").click(function () {
            $(this).parent("li").siblings().removeClass("cur");
            $(this).parent("li").addClass("cur");
            $(this).parents(".detail_eval_left").children("dl.dynamic").hide();
            $(this).parents(".detail_eval_left").children("dl.info").show();
        });
        $("#iad_dynamic").click(function () {
            $(this).parent("li").siblings().removeClass("cur");
            $(this).parent("li").addClass("cur");
            $(this).parents(".detail_eval_left").children("dl.dynamic").show();
            $(this).parents(".detail_eval_left").children("dl.info").hide();
        });
    }
};
