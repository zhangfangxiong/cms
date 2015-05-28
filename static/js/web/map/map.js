function MapConfig() {
}
MapConfig.MapData = {CityCode: null};
MapConfig.ZoomLevel1 = 10; //区域缩放最小级别
MapConfig.ZoomLevel2 = 14; //板块缩放最小级别
MapConfig.ZoomLevel3 = 16; //小区缩放最小级别
MapConfig.MapObj = null;
MapConfig.MapElement = null;
MapConfig.MapContainer = "map";
MapConfig.ListContainer = "units";
MapConfig.ListPannel = "pannel";
MapConfig.UnitTemplate = "unit_template";
MapConfig.TemplateHTML = null;
MapConfig.PageSize = 50; //每页大小
MapConfig.Y_SIZE = 10; //地图的纬度粒度
MapConfig.X_SIZE = 10; //地图的经度粒度
MapConfig.Position = {};
MapConfig.UnitDetialTipID = null;
MapConfig.UnitTipID = null;
MapConfig.Units = {};
MapConfig.SetUnits = {};
MapConfig.SetIcons = {};
MapConfig.ListUnits = [];
MapConfig.RoomsOrPriceList = null;
MapConfig.DetailTip = null;
MapConfig.Markers = {};
MapConfig.BatchNum = 0;
MapConfig.Scroll = {
    IntervalID: null
};

MapConfig.InitFilter = {Category: 1, Filter: ""};


//地图第一次加载的时候进行初始化
function InitializeTips(category) {
    MapConfig.MapObj.clearMap(); //删除覆盖物
       // SyncFilter();
        MapConfig.ListUnits = MapConfig.MapData.Overlays;
        ApplyFilter(1, true);
    var keyword = $(".key_words");
    if (MapConfig.MapData.Keyword != null) {
        if (MapConfig.MapData.Keyword.length >= 1) {
            $("b", keyword).text(MapConfig.MapData.Keyword);
            keyword.show();
        }
        else {
            keyword.hide();
        }
    }
}

//将布尔值转换成数字
function CollectItem(value) {
    if (value.Enabled) {
        return "1";
    }
    return "0";
}

//收集统计信息
function CollectData(item, except) {
    var a = [];
    for (var p in item) {
        if (p == except) {
            continue;
        }
        if (item[p] != true) {
            continue;
        }
        a.push(p);
    }
    return a.join(",");
}

//缩放到指定的缩放级别与坐标
function ZoomMap(zoom, x, y) {
    MapConfig.MapObj.setZoomAndCenter(zoom, new AMap.LngLat(x, y));
}

//在地图上显示某个小区的图标
function ShowUnitIcon(id, i) {
    var unit = MapConfig.Units["u" + id];
    if (unit != null) {
        if ($("#u" + id).length > 0) {
            return;
        }
        var content = "";
        content = "<div class=\"map_tip_wrap bdr1 shadow2\" id=\"smallTip" + unit.PK + "\" onclick=\"ShowUnitDetailTip('" + unit.PK + "',true)\" style=\"width:auto;left:-15px;top:-10px;\"><s class=\"zbt_tip\" style=\"left:22px;\"></s><dl style=\"width:auto;\"><dd style=\"width:auto;\"><h6 style=\"width:auto;\">";
        content += "<a href=\"javascript:void(0)\" onclick=\"ShowUnitDetailTip('" + unit.PK + "',true)\" title=\"" + unit.Tip + "\" style=\"min-width:48px\">" + unit.Tip + "</a></h6></dd></dl></div>";
        content += "<a class=\"ico_map_" + (unit.Category == 1 ? "xf" : "esf") + " unit_icon\" onclick=\"ShowUnitDetailTip('" + unit.PK + "',true,'click')\" onmouseover=\"ShowUnitDetailTip('" + unit.PK + "',true)\" onmouseout=\"window.clearTimeout(MapConfig.UnitDetailTipTimeID);\"></a>";

        marker = new window.AMap.Marker({
            position: new window.AMap.LngLat(unit.X8, unit.Y8), //位置 
            //	offset: new window.AMap.Pixel(0, 0),
            zIndex: 201,
            content: content
        });
        marker.setMap(MapConfig.MapObj);
        MapConfig.Markers[i] = marker;

    }
}

function ShowUnitDetailTip(id, flag, event) {
    window.clearTimeout(MapConfig.UnitDetailTipTimeID);
    MapConfig.UnitDetailTipTimeID = window.setTimeout(function () {
        DoShowDetailUnitTip(id, flag, event);
    }, 300);
}

//将一个图标带到后台
function SendToBack(link) {
    if (link != null) {
        var parent = $(link).parent()[0];
        var z = parent.Z || parent.style.zIndex || 2;
        parent.style.zIndex = z;
    }
}

//地图上新房tip菜单过滤
function IsShowMapFljy(id) {
    var unit = MapConfig.Units["u" + id];
    if (unit.Category == 1 && unit.EvaluationFlag == 1) {
        $("#utd" + id + " .txt_nav_list .cor_l").show();
        $("#utd" + id + " .txt_nav_list .cor_g").show();
        $("#utd" + id + " .txt_nav_list .cor_h").show();
        $("#utd" + id + " .txt_nav_list .cor_z").show();
    }
    else if (unit.Category == 1 && unit.EvaluationFlag != 1) {
        $("#utd" + id + " .txt_nav_list .cor_l").hide();
        $("#utd" + id + " .txt_nav_list .cor_g").hide();
        $("#utd" + id + " .txt_nav_list .cor_z").hide();
    }
}
//在地图上显示某个小区的详细气泡
function DoShowDetailUnitTip(id, flag, event) {

    if (MapConfig.UnitDetialTipID == id) {
        if (event == "click") {
            if ($("#utd" + id).is(":hidden")) {
                IsShowMapFljy(id);
                $("#utd" + id).fadeIn(400);
                $("#smallTip" + id).css("visibility", "hidden");
            }
            else {
                $("#utd" + id).fadeOut(400);
                $("#smallTip" + id).css("visibility", "visible");
            }
        }
        else {
            IsShowMapFljy(id);
            $("#utd" + id).fadeIn(400);
            $("#smallTip" + id).css("visibility", "hidden");
        }
        return;
    }

    //隐藏最小的tip
    $("#smallTip" + id).css("visibility", "hidden");
    RemoveObject("ut" + MapConfig.UnitTipID, 400);
    RemoveObject("utd" + MapConfig.UnitDetialTipID, 400);
    $("#smallTip" + MapConfig.UnitDetialTipID).css("visibility", "visible");

    MapConfig.UnitDetialTipID = id;
    MapConfig.RoomsOrPriceList = null;
    MapConfig.BatchNum = 0;
    var func = function () {
        var unit = MapConfig.Units["u" + id];
        if (unit != null) {
            var clsName = "zbt";
            var offsetX = -35; //-26
            var offsetY = -120; //-86
            if (unit.Category == 0) {
                offsetY = -120; //-120
            }
            var x = unit.X8;
            var y = unit.Y8;
            var zoom = MapConfig.MapObj.getZoom();
            var bound = MapConfig.MapObj.getBounds();
            var x2 = bound.getNorthEast().getLng();
            var y2 = bound.getNorthEast().getLat();
            var x1 = bound.getSouthWest().getLng();
            var y1 = bound.getSouthWest().getLat();
            var x3 = parseFloat(x);
            var y3 = parseFloat(y);
            if (x1 > x3 || x3 > x2 || y1 > y3 || y3 > y2) {
                MapConfig.MapObj.panTo(new window.AMap.LngLat(x3, y3));
            }
            var pos2 = MapConfig.MapObj.lnglatToPixel(new window.AMap.LngLat(bound.northeast.lng, bound.northeast.lat), zoom);
            var pos3 = MapConfig.MapObj.lnglatToPixel(new window.AMap.LngLat(x, y), zoom);
            if (pos2.y > pos3.y + offsetY - 150) {
                offsetY = 22; //58
                clsName = "ztp";
            }

            if (pos2.x < pos3.x + offsetX + 960) {
                offsetX -= 435;
                offsetY = -15; //19
                clsName = "zrt";
            }
            var content;
            if (unit.Category == 1) {
                content = "<div class=\"map_nor_wrap bdr2 shadow4\" id=\"utd" + unit.PK + "\"><a onclick=\"RemoveObject('utd" + unit.PK + "');\" title=\"关闭\" class=\"wrap_close\">X</a><s class=\"" + clsName + "\"></s><dl class=\"clearfix\"><dt><a href=\"" + unit.Url + "\" target=\"_blank\" title=\"" + unit.Tip + "\"><img src=\"" + unit.PictureUrl + "\" alt=\"" + unit.Tip + "\" onerror=\"this.src='" + localurls + "/img/news/not_exists.jpg'\" /></a></dt><dd>";
                content += "<div class=\"txt_title_info\" id='batchTypeInfo' style=\"position:relative;\"><h6 style=\"position: absolute; top: 0px;\"><em><s class=\"ico_xf\"></s><var style='margin:0 5px 0 0;'" + ">" + ("[" + unit.Region + "]" + "</var>" + "<a title=" + unit.Tip) + " target=\"_blank\" href=" + unit.Url + ">" + unit.Tip + "</a></em></h6></div><div class=\"txt_info clearfix\" id=\"batchTypePrice\"><ul class=\"panel_left\"><li style='margin-right:5px;'><cite>实际售价：</cite><em>" + (unit.BiddingPrice || "-") + "</em><s>元/㎡</s></li><li><cite>分析师指导价：</cite><em style='color:#eb4d00;'>" + (unit.Price || "-") + "</em><s>元/㎡</s></li></ul></div>";

                content += "<div class=\"txt_nav_list clearfix\" ><a href='" + unit.Url + "_comment' target = '_blank' class='cor_l'>楼盘点评</a><a href='" + unit.Url + "_evaluation' target = '_blank' class='cor_g'>楼盘测评</a><a href='" + unit.PriceTableUrl + "' target = '_blank' class='cor_h'>价目表</a><a href='" + unit.Url + "_discount' target = '_blank' class='cor_z' style='margin-right:0;'>优惠折扣</a>";
                content += "</div></dd></dl>";
            }
            else {
                content = "<div class=\"map_nor_wrap bdr2 shadow4\" id=\"utd" + unit.PK + "\"><a onclick=\"RemoveObject('utd" + unit.PK + "');\" title=\"关闭\" class=\"wrap_close\">X</a><s class=\"" + clsName + "\"></s><dl class=\"clearfix\"><dt><a href=\"" + unit.Url + "\" target=\"_blank\" title=\"" + unit.Tip + "\"><img src=\"" + unit.PictureUrl + "\" alt=\"" + unit.Tip + "\" onerror=\"this.src='" + localurls + "/img/news/not_exists.jpg'\" /></a></dt><dd>";


                content += "<div class=\"txt_title_info\" id='batchTypeInfo' style=\"position:relative;\"><h6 style=\"position: absolute; top: 0px;\"><em><s class=\"ico_esf\"></s><var style='margin:0 5px 0 0;'" + ">" + ("[" + unit.Region + "]" + "</var>" + "<a title=" + unit.Tip) + " target=\"_blank\" href=" + unit.Url + ">" + unit.Tip + "</a></em></h6></div><div class=\"txt_info clearfix\" id=\"batchTypePrice\"><ul class=\"panel_left\"><li style='margin-right:5px;width:145px'><cite>参考均价：</cite><em style='color:#eb4d00;'>" + (unit.Price || "-") + "</em><s>元/㎡</s></li><li><cite>参考租金：</cite><em>" + (unit.MinRentPrice || "-") + "</em><s>元/月</s></li></ul></div>";
                if (unit.BlockTotal != 0) {
                    content += "<div class=\"txt_nav_list clearfix\" ><a href='" + unit.Url + "' target = '_blank' class='cor_l'>小区首页</a><a href='" + unit.Url + "_pricetable' target = '_blank' class='cor_g'>一房一价</a><a href='" + unit.Url + "_priceinfos' target = '_blank' class='cor_h'>价格行情</a><a href='" + unit.Url + "_des' target = '_blank' class='cor_z' style='margin-right:0;'>小区概述</a>";
                } else {
                    content += "<div class=\"txt_nav_list clearfix\" ><a href='" + unit.Url + "' target = '_blank' class='cor_l'>小区首页</a><a href='" + unit.Url + "_priceinfos' target = '_blank' class='cor_h'>价格行情</a><a href='" + unit.Url + "_des' target = '_blank' class='cor_z' style='margin-right:0;'>小区概述</a>";
                }

                content += "</div></dd></dl>";
            }
            var marker = new window.AMap.Marker({
                position: new window.AMap.LngLat(x, y), //位置 
                offset: new window.AMap.Pixel(offsetX, offsetY),
                zIndex: 402,
                content: content
            });
            MapConfig.DetailTip = marker;
            MapConfig.Markers["utd" + unit.PK] = marker;
            marker.setMap(MapConfig.MapObj);
            IsShowMapFljy(id);
            $("#utd" + unit.PK).hide().fadeIn();
        }
    };
    func();

    if (flag) {
        //滚动小区
        (function () {
            MapConfig.Scroll.Item = $("#ui" + id);
            var item = MapConfig.Scroll.Item;
            if (item == null || item.length == 0) {
                var unit = MapConfig.Units["u" + id];
                if (unit == null) {
                    return;
                }
                var teHtml = MapConfig.TemplateHTML;
                if (unit.Category == 0) {
                    teHtml = $("#unit_template_esf").html();
                }
                var html = GenerateListItem(unit, teHtml);
                $("#" + MapConfig.ListContainer).append(html);
                MapConfig.Scroll.Item = $("#ui" + id);
            }
            $(".result_list dl.current").removeClass("current");
            MapConfig.Scroll.Item.addClass("current");
            MapConfig.Scroll.ItemHeight = MapConfig.Scroll.ItemHeight || MapConfig.Scroll.Item[0].offsetHeight;
            MapConfig.Scroll.Panel = MapConfig.Scroll.Panel || $("#" + MapConfig.ListPannel);
            MapConfig.Scroll.Container = MapConfig.Scroll.Container || $("#" + MapConfig.ListContainer);

            MapConfig.Scroll.Handler = MapConfig.Scroll.Handler || function () {
                var item = MapConfig.Scroll.Item;
                if (item == null || item.length == 0) {
                    window.clearInterval(MapConfig.Scroll.IntervalID);
                    return;
                }
                var current = item.offset().top;
                var target = MapConfig.Scroll.ItemHeight * 2 + 9;

                var offset = (target - current) / 5;
                if (offset > 100) {
                    offset = 100;
                }
                else if (offset < -100) {
                    offset = -100;
                }
                if (Math.abs(offset) < 1) {
                    window.clearInterval(MapConfig.Scroll.IntervalID);
                    return;
                }
                var top1 = MapConfig.Scroll.Panel[0].scrollTop;
                var top2 = top1 - offset;
                MapConfig.Scroll.Panel[0].scrollTop = top2;
                if (MapConfig.Scroll.Panel[0].scrollTop == top1) {
                    window.clearInterval(MapConfig.Scroll.IntervalID);
                    return;
                }
            };
            window.clearInterval(MapConfig.Scroll.IntervalID);
            MapConfig.Scroll.IntervalID = window.setInterval(MapConfig.Scroll.Handler, 20);
        })();
    }


}

//删除地图上指定ID的覆盖物
function RemoveObject(id, duration) {
    var marker;
    if (duration > 0) {
        $("#" + id).fadeOut(duration, function () {
            marker = MapConfig.Markers[id];
            if (marker != null) {
                marker.setMap(null);
            }

            if (id == "ut" + MapConfig.UnitTipID) {
                MapConfig.UnitTipID = null;
            }
            else if (id == "utd" + MapConfig.UnitDetialTipID) {
                MapConfig.UnitDetialTipID = null;
            }
        });
    }
    else {
        marker = MapConfig.Markers[id];
        if (marker != null) {
            marker.setMap(null);
        }
        if (id == "ut" + MapConfig.UnitTipID) {
            MapConfig.UnitTipID = null;
        }
        else if (id == "utd" + MapConfig.UnitDetialTipID) {
            $("#smallTip" + MapConfig.UnitDetialTipID).css("visibility", "visible");
            MapConfig.UnitDetialTipID = null;
        }
    }
}

//按照当前坐标查找周边的小区
function SearchByXY() {
  //  $("#FilterCondtion").fadeOut();
    var bound = MapConfig.MapObj.getBounds();
    var x2 = bound.getNorthEast().getLng();
    var y2 = bound.getNorthEast().getLat();
    var x = bound.getSouthWest().getLng();
    var y = bound.getSouthWest().getLat();
    var zoom = MapConfig.MapObj.getZoom();
    var param={};
    var key = MapConfig.MapData.Keyword;
        if (key != "") {
            param={zoomLevel:zoom,minLat:y,minLon:x,maxLat:y2,maxLon:x2,keywords:key};
          //  url = "/" + MapConfig.MapData.CityCode + "/map/tile/" + x + "/" + y + "/" + x2 + "/" + y2 + "/" + zoom + "/" + encodeURIComponent(key);
        } else {
            param={zoomLevel:zoom,minLat:y,minLon:x,maxLat:y2,maxLon:x2};
        //    url = "/" + MapConfig.MapData.CityCode + "/map/tile/" + x + "/" + y + "/" + x2 + "/" + y2 + "/" + zoom;
        }

        $.post('/map/Mapajax', param, function (data) {
            MapConfig.ListUnits = data.data.Overlays;
            //SyncFilter();
            ApplyFilter();
            //console.log("loaded:" + data.length);
        },"json");
}

//生成HTML
function GenerateListItem(unit, template) {
    var html = template || MapConfig.TemplateHTML;
    if (html == null) {
        return;
    }

    html = html.replace(/\.srcX/gi, ".src");

    for (var p in unit) {
        var reg = new RegExp("\\$" + p + "\\b", "gi");
        if (p == "NameList") {
            if (unit[p] != "") {
                html = html.replace(reg, "(" + unit[p] + ")");
            }
            else {
                html = html.replace(reg, unit[p] || "");
            }
        }
        else if (p == "EvaluationFlag") {
            html = html.replace("$EvaluationFlag", unit[p] != null ? unit[p] : -1);
        } else if (p == "BlockTotal") {
            if (parseInt(unit[p]) == 0) {
                html = html.replace("$display_price", "none");
                html = html.replace("$display_jisuan", "block");
            } else {
                html = html.replace("$display_price", "block");
                html = html.replace("$display_jisuan", "none");
            }
            html = html.replace(reg, unit[p] || "-");
        }
        else {
            html = html.replace(reg, unit[p] || "-");
        }

        if (p == "TotalScore") {
            var str = scoreToStar(unit[p]);
            html = html.replace("$Star", str);
            html = html.replace("$TScore", unit[p].toFixed(2));
        }
    }
    return html;
}

//使地图移到某个小区
function MoveToUnit(id) {
    var unit = MapConfig.Units["u" + id];
    if (unit == null) {
        return;
    }
    ShowUnitDetailTip(id, false);
}

//修正小区数据
function FixUnitData(unit, zoom) {
    if (unit.Category == 1) {
        unit.IconClass = "ico_xf";
        if (unit.DataFixed != 1) {
            unit.DataFixed = 1;
        }
        unit.PriceName = "分析师指导价";
        unit.PriceName2 = "实际售价";
        unit.Price2 = unit.BiddingPrice;
        unit.PriceTag2 = "元/㎡";
    }
    else {
        unit.IconClass = "ico_esf";
        if (unit.EJScore == "" || unit.EJScore == null) {
            unit.EJScore = "BBB";
        }
        unit.PriceName = "参考均价";
        unit.PriceName2 = "参考租金";
        unit.Price2 = unit.MinRentPrice;
        unit.PriceTag2 = "元/月";
    }
    var p = new window.AMap.LngLat(unit.X8, unit.Y8);
    p = MapConfig.MapObj.lnglatToPixel(p, zoom);
    unit.PixX = p.x;
    unit.PixY = p.y;
}

//根据指定区域进行过滤
function ApplyFilter(page, skipBounds) {
//    if (MapConfig.Filter.Enabeld != false) {
//        $("#FilterCondtion").fadeIn();
//    }
    MapConfig.MapObj.clearMap();
    var zoom = MapConfig.MapObj.getZoom();
    var htmls = [];
    var buffer = {};
    var bound = MapConfig.MapObj.getBounds();
    var x2 = bound.getNorthEast().getLng();
    var y2 = bound.getNorthEast().getLat();
    var x1 = bound.getSouthWest().getLng();
    var y1 = bound.getSouthWest().getLat();
    if (MapConfig.ListUnits == undefined) {
        return;
    }
    var units = MapConfig.ListUnits;
    page = page || 1;
    var min = (page - 1) * MapConfig.PageSize;
    var max = page * MapConfig.PageSize;
    var num = 0;
    var foundUnits = [];
    var searchTip = "楼盘";

    skipBounds = skipBounds || false;
    for (var i = 0; i < units.length; i++) {
        var unit = units[i];
        num++;
        if (num < min) {
            continue;
        }
        if (num > max) {
            continue;
        }
        //修正小区数据
        FixUnitData(unit, zoom);
        foundUnits.push(unit);
    }
    var txt = num;
    if (MapConfig.MapData.Keyword == "") {
        $(".key_words").hide();
    } else {
        $(".key_words").show();
    }

    if (txt >= 400) {
        txt = "<span>列出找到的" + searchTip + txt + "个,请试着放大或移动地图.</span>";
        $(".result_txt").show();
        $(".no_find_ban").hide();
    }
    else if (txt == 0) {
        $(".result_txt").hide();
        $(".no_find_ban").show();
        $(".key_words").hide();
    }
    else {
        if (MapConfig.MapData.Keyword == "") {
            txt = "<span>地图显示范围内共找到符合条件的" + searchTip + txt + "个.</span>";
        } else {
            txt = "<span>共找到符合条件的" + searchTip + txt + "个.</span>";
        }

        $(".result_txt").show();
        $(".no_find_ban").hide();

    }

    if (MapConfig.ListContainer != null) {
        $("#" + MapConfig.ListPannel)[0].scrollTop = 0;

        $(".filter_cond_list span b").html(num);
        $(".result_txt").html(txt);
        //分页
        pager(".page", function (page) {
            MapConfig.MapObj.clearMap();
            ApplyFilter(page);
            $("#" + MapConfig.ListPannel)[0].scrollTop = 0;
        }, page, Math.ceil(num / MapConfig.PageSize));
    }

    for (var i = 0; i < foundUnits.length; i++) {
        var unit = foundUnits[i];
        if (unit != null) {
            var teHtml = MapConfig.TemplateHTML;
            if (unit.Category == 0) {
                teHtml = $("#unit_template_esf").html();
            }
            var html = GenerateListItem(unit, teHtml);
            MapConfig.Units["u" + unit.PK] = unit;
            ShowUnitIcon(unit.PK, i);
            htmls.push(html);
        }
    }

    if (MapConfig.ListContainer != null) {
        html = htmls.join("");
        $("#" + MapConfig.ListContainer).html(html);
        if ($(window).width() <= 1024) {
            $("#pannel").width(325);
            $(".map_left_con").css("margin-right", 325);
            $("#units dl dd").css("width", "175");
            $("#units .panel_right").css("display", "none");
            $("#map").width($("#Header").width() - ($("#pannel").width() + 2));
            //alert("1");
        }
        else {
            $("#pannel").width(502);
            $(".map_left_con").css("margin-right", 502);
            $("#units dl dd").css("width", "355");
            $("#units .panel_right").css("display", "block");
            $("#map").width($("#Header").width() - ($("#pannel").width() + 2));
        }
        //结果超过一定高度出滚动条
        if ($("#" + MapConfig.ListContainer).height() > $(window).height() - $("#Header").height() - 72) {
            $("#" + MapConfig.ListPannel).removeClass("ofy_h").addClass("ofy_s");
        }
        else {
            $("#" + MapConfig.ListPannel).removeClass("ofy_s").addClass("ofy_h");
        }
        $(".filter_cond_box").height($(".filter_cond_list").height() + 12);
    }
}

//更新统计信息
function AdjustRegion(s) {
    var regions = MapConfig.MapData.MapOverlays.Regions;
    for (var i = 0; i < regions.length; i++) {
        var region = regions[i];
        if (region.Tip == s.Region) {
            region.UnitTotal = s.xf;
            region.UnitTotal2 = s.esf;
        }
    }
}

////重新过滤
//function ResetFilter() {
//    $("#FilterCondtion").fadeOut();
//}

function ChangeFilter(name, link) {
    $(link).hide();
    MapConfig.Filter[name].Enabled = false;
    var checkbox = $("." + name.toLowerCase() + " h6 input")[0];
    checkbox.checked = false;
    window.FilterMain(name, checkbox);
    ApplyFilter(1);
}

//地图初始化
$(document).ready(function () {
    MapConfig.MapElement = $("#" + MapConfig.MapContainer)[0];
    MapConfig.TemplateHTML = $("#" + MapConfig.UnitTemplate).html();
    //如果地图不存在,则退出
    if (MapConfig.MapElement == null) {
        return;
    }
    $("header div.row").removeClass("row");
    $(".login_user").css("margin-right", "40px");
    var opt = {
        level: MapConfig.MapData.ZoomLevel,
        zooms: [MapConfig.MapData.MinZoomLevel, MapConfig.MapData.MaxZoomLevel],
        center: new window.AMap.LngLat(MapConfig.MapData.X8, MapConfig.MapData.Y8),
        jogEnable: true
    };

    MapConfig.MapObj = new window.AMap.Map(MapConfig.MapContainer, opt);

    if (MapConfig.MapData.DisableToobar != true) {
        MapConfig.MapObj.plugin(["AMap.ToolBar", "AMap.MapType"], function () {
            var toolbar = new window.AMap.ToolBar();
            toolbar.autoPosition = false; //加载工具条  
            MapConfig.MapObj.addControl(toolbar);
            var mapType = new window.AMap.MapType(); //加载地图类型
            MapConfig.MapObj.addControl(mapType);
        });
    }
    InitializeTips();


    window.AMap.event.addListener(MapConfig.MapObj, "dragend", function (e) {
        if (MapConfig.DisableEvent == true) {
            return;
        }
        MapConfig.UnitDetialTipID = null;
        MapConfig.UnitTipID = null;
        MapConfig.MapObj.clearMap(); //删除覆盖物
        SearchByXY();

    });

    window.AMap.event.addListener(MapConfig.MapObj, "zoomchange", function (e) {
        if (MapConfig.DisableEvent == true) {
            return;
        }
        MapConfig.MapObj.clearMap(); //删除覆盖物
        MapConfig.UnitTipID = null;
        MapConfig.UnitDetialTipID = null;
        SearchByXY();
    });
    var divs = document.getElementsByTagName("div");
    for (var i = 0; i < divs.length; i++) {
        var div = divs[i];
        if (div.id == null || div.id == "") {
            continue;
        }
        if (/copyright/.test(div.id)) {
            $(div).css({opacity: 0.0});
        }
    }

});

function mapZoomIn() {
    if (MapConfig.MapObj) {
        MapConfig.MapObj.zoomIn();
    }

}

//缩放到指定的缩放级别与坐标
function ZoomMap(zoom, x, y) {
    MapConfig.MapObj.setZoomAndCenter(zoom, new AMap.LngLat(x, y));
}

function scoreToStar(score) {
    var str;
    if (score > 0 && score < 1) {
        str = "<em class='star_icon star_b'></em><em class='star_icon star_w'></em><em class='star_icon star_w'></em><em class='star_icon star_w'></em><em class='star_icon star_w'></em>";
    }
    else if (Math.abs(score - 1) <= 0) {
        str = "<em class='star_icon star_q'></em><em class='star_icon star_w'></em><em class='star_icon star_w'></em><em class='star_icon star_w'></em><em class='star_icon star_w'></em>";
    }
    else if (score > 1 && score < 2) {
        str = "<em class='star_icon star_q'></em><em class='star_icon star_b'></em><em class='star_icon star_w'></em><em class='star_icon star_w'></em><em class='star_icon star_w'></em>";
    }
    else if (Math.abs(score - 2) <= 0) {
        str = "<em class='star_icon star_q'></em><em class='star_icon star_q'></em><em class='star_icon star_w'></em><em class='star_icon star_w'></em><em class='star_icon star_w'></em>";
    }
    else if (score > 2 && score < 3) {
        str = "<em class='star_icon star_q'></em><em class='star_icon star_q'></em><em class='star_icon star_b'></em><em class='star_icon star_w'></em><em class='star_icon star_w'></em>";
    }
    else if (Math.abs(score - 3) <= 0) {
        str = "<em class='star_icon star_q'></em><em class='star_icon star_q'></em><em class='star_icon star_q'></em><em class='star_icon star_w'></em><em class='star_icon star_w'></em>";
    }
    else if (score > 3 && score < 4) {
        str = "<em class='star_icon star_q'></em><em class='star_icon star_q'></em><em class='star_icon star_q'></em><em class='star_icon star_b'></em><em class='star_icon star_w'></em>";
    }
    else if (Math.abs(score - 4) <= 0) {
        str = "<em class='star_icon star_q'></em><em class='star_icon star_q'></em><em class='star_icon star_q'></em><em class='star_icon star_q'></em><em class='star_icon star_w'></em>";
    }
    else if (score > 4 && score < 5) {
        str = "<em class='star_icon star_q'></em><em class='star_icon star_q'></em><em class='star_icon star_q'></em><em class='star_icon star_q'></em><em class='star_icon star_b'></em>";
    }
    else if (score >= 5) {
        str =
                "<em class='star_icon star_q'></em><em class='star_icon star_q'></em><em class='star_icon star_q'></em><em class='star_icon star_q'></em><em class='star_icon star_q'></em>";
    }
    else {
        str = "<em class='star_icon star_w'></em><em class='star_icon star_w'></em><em class='star_icon star_w'></em><em class='star_icon star_w'></em><em class='star_icon star_w'></em>";
    }
    return str;
}
