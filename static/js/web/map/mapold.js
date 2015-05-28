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
MapConfig.UnitPool = {};
MapConfig.RoomsOrPriceList = null;
MapConfig.DetailTip = null;
MapConfig.Markers = {};
MapConfig.BatchNum = 0;
MapConfig.Scroll = {
    IntervalID: null
};
MapConfig.FilterBack = function () {
    return {
        XinFang: {
            Enabled: true,
            Level1: true,
            Level2: true,
            Level3: true,
            Level4: true

        },
        EsFang: {
            Enabled: true,
            LevelA: true,
            LevelB: true,
            LevelC: true
        },
        ZuFang: {
            Enabled: false,
            LevelA: false,
            LevelB: false,
            LevelC: false
        },
        Price: {
            All: true,
            P5000: false,
            P10000: false,
            P15000: false,
            P20000: false,
            P25000: false,
            P30000: false,
            P50000: false,
            P60000: false
        },
        RentPrice: {
            All: true,
            P0000: false,
            P1000: false,
            P2000: false,
            P3000: false,
            P4000: false
        },
        Area: {
            All: true,
            A00: false,
            A50: false,
            A80: false,
            A110: false,
            A130: false,
            A150: false,
            A200: false
        }
    };
};

MapConfig.Filter = MapConfig.FilterBack();
MapConfig.InitFilter = {Category: -1, Filter: ""};

//将一个选项下面所有的子选项设定为指定的值
function SetChildFilter(item, value, except) {
    for (var p in item) {
        if (p == except) {
            continue;
        }
        if (item[p] == true || item[p] == false) {
            item[p] = value == true;
        }
    }
}

//返回选项的子选项是否包含指定的值
function AnyChild(item, value, except) {
    for (var p in item) {
        if (p == except) {
            continue;
        }
        if (item[p] == true || item[p] == false) {
            if (item[p] == value) {
                return true;
            }
        }
    }
    return false;
}

//返回选项的子选项是否全部包含指定的值
function AllChild(item, value, except) {
    for (var p in item) {
        if (p == except) {
            continue;
        }
        if (item[p] == true || item[p] == false) {
            if (item[p] != value) {
                return false;
            }
        }
    }
    return true;
}

//修改一个过滤选项并且立即在地图上呈现
function FilterAndApply(item, value) {
    Filter(item, value);
    ApplyFilter();
}

//修改一个过滤选项
function Filter(item, value) {
    var code;
    if (value == null) {
        code = "(function(){MapConfig.Filter." + item + " = !MapConfig.Filter." + item + ";})()";
    }
    else {
        code = "(function(){MapConfig.Filter." + item + " = " + value + ";})()";
    }
    eval(code);
    SyncFilter(item);
}

//使选项与界面同步
function SyncFilter(item) {
    var filter = MapConfig.Filter;
    if (/xinfang.level/i.test(item)) {
        filter.XinFang.Enabled = AnyChild(filter.XinFang, true, "Enabled");
        SetChildFilter(filter.ZuFang, false);
        $("#dlrent").hide();
        $("#dlsale").show();
    }
    else if (/xinfang.enabled/i.test(item)) {
        SetChildFilter(filter.XinFang, filter.XinFang.Enabled);
        SetChildFilter(filter.ZuFang, false);
        if (!filter.ZuFang.Enabled && !filter.EsFang.Enabled) {
            SetChildFilter(filter.EsFang, true);
        }
        $("#dlrent").hide();
        $("#dlsale").show();
    }
    if (/EsFang.level/i.test(item)) {
        filter.EsFang.Enabled = AnyChild(filter.EsFang, true, "Enabled");
        SetChildFilter(filter.ZuFang, false);
        $("#dlrent").hide();
        $("#dlsale").show();
    }
    else if (/EsFang.enabled/i.test(item)) {
        SetChildFilter(filter.EsFang, filter.EsFang.Enabled);
        SetChildFilter(filter.ZuFang, false);
        if (!filter.ZuFang.Enabled && !filter.XinFang.Enabled) {
            SetChildFilter(filter.XinFang, true);
        }
        $("#dlrent").hide();
        $("#dlsale").show();
    }
    if (/ZuFang.level/i.test(item)) {
        filter.ZuFang.Enabled = AnyChild(filter.ZuFang, true, "Enabled");
        if (filter.ZuFang.Enabled) {
            SetChildFilter(filter.XinFang, false);
            SetChildFilter(filter.EsFang, false);
            $("#dlsale").hide();
            $("#dlrent").show();
        }
    }
    else if (/ZuFang.enabled/i.test(item)) {
        SetChildFilter(filter.ZuFang, filter.ZuFang.Enabled);
        if (filter.ZuFang.Enabled) {
            SetChildFilter(filter.XinFang, false);
            SetChildFilter(filter.EsFang, false);
            $("#dlsale").hide();
            $("#dlrent").show();
        }
        else {
            if (!filter.EsFang.Enabled && !filter.XinFang.Enabled) {
                SetChildFilter(filter.XinFang, true);
                SetChildFilter(filter.EsFang, true);
            }
        }
    }
    if (filter.ZuFang.Enabled) {
        $(".CheckZf dl").show();
        $(".CheckXf dl").hide();
        $(".CheckEsf dl").hide();
    }
    else {
        SetChildFilter(filter.ZuFang, false);
        $(".CheckZf dl").hide();
        $(".CheckXf dl").show();
        $(".CheckEsf dl").show();
    }
    if (/price.all/i.test(item)) {
        if (filter.Price.All)
            SetChildFilter(filter.Price, !filter.Price.All, "All");
    }
    else if (/price/i.test(item)) {
        filter.Price.All = !AnyChild(filter.Price, true, "All");
    }
    if (/rentprice.all/i.test(item)) {
        if (filter.RentPrice.All)
            SetChildFilter(filter.RentPrice, !filter.RentPrice.All, "All");
    }
    else if (/rentprice/i.test(item)) {
        filter.RentPrice.All = !AnyChild(filter.RentPrice, true, "All");
    }
    if (/area.all/i.test(item)) {
        if (filter.Area.All)
            SetChildFilter(filter.Area, !filter.Area.All, "All");
    }
    else if (/area/i.test(item)) {
        filter.Area.All = !AnyChild(filter.Area, true, "All");
    }
    filter = MapConfig.Filter;
    if (!filter.XinFang.Enabled && !filter.ZuFang.Enabled && !filter.EsFang.Enabled) {
        SetChildFilter(filter.XinFang, true);
        SetChildFilter(filter.EsFang, true);
    }
    if (!AnyChild(filter.Price, true)) {
        filter.Price.All = true;
    }
    if (!AnyChild(filter.RentPrice, true)) {
        filter.RentPrice.All = true;
    }
    if (!AnyChild(filter.Area, true)) {
        filter.Area.All = true;
    }
    SyncFilterTip("Xf", filter.XinFang.Enabled, !AnyChild(filter.XinFang, false, "Enabled") || AllChild(filter.XinFang, false, "Enabled"));
    SyncFilterTip("XfLevel4", filter.XinFang.Level4, AnyChild(filter.XinFang, false));
    SyncFilterTip("XfLevel3", filter.XinFang.Level3, AnyChild(filter.XinFang, false));
    SyncFilterTip("XfLevel2", filter.XinFang.Level2, AnyChild(filter.XinFang, false));
    SyncFilterTip("XfLevel1", filter.XinFang.Level1, AnyChild(filter.XinFang, false));
    SyncFilterTip("Esf", filter.EsFang.Enabled, !AnyChild(filter.EsFang, false, "Enabled") || AllChild(filter.EsFang, false, "Enabled"));
    SyncFilterTip("EsfLevelA", filter.EsFang.LevelA, AnyChild(filter.EsFang, false));
    SyncFilterTip("EsfLevelB", filter.EsFang.LevelB, AnyChild(filter.EsFang, false));
    SyncFilterTip("EsfLevelC", filter.EsFang.LevelC, AnyChild(filter.EsFang, false));
    SyncFilterTip("Zf", filter.ZuFang.Enabled, !AnyChild(filter.ZuFang, false, "Enabled") || AllChild(filter.ZuFang, false, "Enabled"));
    SyncFilterTip("ZfLevelA", filter.ZuFang.LevelA, AnyChild(filter.ZuFang, false));
    SyncFilterTip("ZfLevelB", filter.ZuFang.LevelB, AnyChild(filter.ZuFang, false));
    SyncFilterTip("ZfLevelC", filter.ZuFang.LevelC, AnyChild(filter.ZuFang, false));

    SyncFilterTip("RentPriceAll", filter.RentPrice.All, filter.ZuFang.Enabled);
    SyncFilterTip("RentPriceP0000", filter.RentPrice.P0000, filter.ZuFang.Enabled);
    SyncFilterTip("RentPriceP1000", filter.RentPrice.P1000, filter.ZuFang.Enabled);
    SyncFilterTip("RentPriceP2000", filter.RentPrice.P2000, filter.ZuFang.Enabled);
    SyncFilterTip("RentPriceP3000", filter.RentPrice.P3000, filter.ZuFang.Enabled);
    SyncFilterTip("RentPriceP4000", filter.RentPrice.P4000, filter.ZuFang.Enabled);

    SyncFilterTip("PriceAll", filter.Price.All, !filter.ZuFang.Enabled);
    SyncFilterTip("PriceP5000", filter.Price.P5000, !filter.ZuFang.Enabled);
    SyncFilterTip("PriceP10000", filter.Price.P10000, !filter.ZuFang.Enabled);
    SyncFilterTip("PriceP15000", filter.Price.P15000, !filter.ZuFang.Enabled);
    SyncFilterTip("PriceP20000", filter.Price.P20000, !filter.ZuFang.Enabled);
    SyncFilterTip("PriceP25000", filter.Price.P25000, !filter.ZuFang.Enabled);
    SyncFilterTip("PriceP30000", filter.Price.P30000, !filter.ZuFang.Enabled);
    SyncFilterTip("PriceP50000", filter.Price.P50000, !filter.ZuFang.Enabled);
    SyncFilterTip("PriceP60000", filter.Price.P60000, !filter.ZuFang.Enabled);

    SyncFilterTip("AreaAll", filter.Area.All);
    SyncFilterTip("AreaA00", filter.Area.A00);
    SyncFilterTip("AreaA50", filter.Area.A50);
    SyncFilterTip("AreaA80", filter.Area.A80);
    SyncFilterTip("AreaA110", filter.Area.A110);
    SyncFilterTip("AreaA130", filter.Area.A130);
    SyncFilterTip("AreaA150", filter.Area.A150);
    SyncFilterTip("AreaA200", filter.Area.A200);
    filter.Enabeld = false;
    if (AllChild(filter.XinFang, true) && AllChild(filter.EsFang, true) && filter.Price.All && filter.Area.All) {
        $("#FilterCondtion").hide();
    }
    else {
        filter.Enabeld = true;
        $("#FilterCondtion").show();
    }
}

//同步一个细项
function SyncFilterTip(id, checked, more) {
    if (checked) {
        $("#Check" + id).attr({checked: true});
        $("#Tip" + id).show();
    }
    else {
        $("#Tip" + id).hide();
        $("#Check" + id).attr({checked: false});
    }
    if (more == false) {
        $("#Tip" + id).hide();
    }
}

//提交之前修正纬度数据
function FixY(y, side) {
    y = parseInt(y * 1000);
    y = y - (y % MapConfig.Y_SIZE);
    y = y + MapConfig.Y_SIZE * side;
    return y;
}

//提交之前修正经度数据
function FixX(x, side) {
    x = parseInt(x * 1000);
    x = x - (x % MapConfig.X_SIZE);
    x = x + MapConfig.X_SIZE * side;
    return x;
}

//地图第一次加载的时候进行初始化
function InitializeTips(category) {
    MapConfig.MapObj.clearMap(); //删除覆盖物
    if (MapConfig.InitFilter != null && MapConfig.InitFilter.Category != -1) {
        switch (MapConfig.InitFilter.Category) {
            case 1:
                Filter("XinFang.Enabled", true);
                Filter("EsFang.Enabled", false);
                if (MapConfig.InitFilter.Filter.charAt(0) == "L") {
                    SetChildFilter(MapConfig.Filter.XinFang, false, "Enabled");
                    Filter("XinFang." + MapConfig.InitFilter.Filter, true);
                }
                if (MapConfig.InitFilter.Filter.charAt(0) == "P") {
                    SetChildFilter(MapConfig.Filter.Price, false);
                    Filter("Price." + MapConfig.InitFilter.Filter, true);
                }
                break;
            case 0:
                Filter("EsFang.Enabled", true);
                Filter("XinFang.Enabled", false);
                if (MapConfig.InitFilter.Filter.charAt(0) == "P") {
                    SetChildFilter(MapConfig.Filter.Price, false);
                    Filter("Price." + MapConfig.InitFilter.Filter, true);
                }
                if (MapConfig.InitFilter.Filter.charAt(0) == "L") {
                    SetChildFilter(MapConfig.Filter.EsFang, false, "Enabled");
                    Filter("XinFang." + MapConfig.InitFilter.Filter, true);
                }
                break;
            case 2:
                Filter("ZuFang.Enabled", true);
                if (MapConfig.InitFilter.Filter.charAt(0) == "P") {
                    SetChildFilter(MapConfig.Filter.RentPrice, false);
                    Filter("RentPrice." + MapConfig.InitFilter.Filter, true);
                }
                break;
        }

        SyncFilter();
        MapConfig.ListUnits = MapConfig.MapData.Overlays;
        ApplyFilter();
    }
    else {
        SyncFilter();
        MapConfig.ListUnits = MapConfig.MapData.Overlays;
        ApplyFilter(1, true);
    }
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
    $("#FilterCondtion").fadeOut();
    var bound = MapConfig.MapObj.getBounds();
    var x2 = bound.getNorthEast().getLng();
    var y2 = bound.getNorthEast().getLat();
    var x = bound.getSouthWest().getLng();
    var y = bound.getSouthWest().getLat();
    var x22 = bound.getNorthEast().getLng();
    var y22 = bound.getNorthEast().getLat();
    var x1 = bound.getSouthWest().getLng();
    var y1 = bound.getSouthWest().getLat();
    var zoom = MapConfig.MapObj.getZoom();
    x2 = FixX(x2, 1);
    x = FixX(x, -1);
    y2 = FixY(y2, 1);
    y = FixY(y, -1);
    var param={};
   var data = MapConfig.UnitPool[x + "_" + y + "_" + zoom];
    var key = MapConfig.MapData.Keyword;
    if (data == null) {
        if (key != "") {
            param={zoomLevel:zoom,minLat:y1,minLon:x1,maxLat:y22,maxLon:x22};
          //  url = "/" + MapConfig.MapData.CityCode + "/map/tile/" + x + "/" + y + "/" + x2 + "/" + y2 + "/" + zoom + "/" + encodeURIComponent(key);
        } else {
            param={zoomLevel:zoom,minLat:y1,minLon:x1,maxLat:y22,maxLon:x22};
        //    url = "/" + MapConfig.MapData.CityCode + "/map/tile/" + x + "/" + y + "/" + x2 + "/" + y2 + "/" + zoom;
        }

        $.post('/map/Mapajax', param, function (data) {
            MapConfig.MapData.SearchID = data.data.SearchID;
            MapConfig.ListUnits = data.data.Overlays;
            SyncFilter();
            ApplyFilter();
            //console.log("loaded:" + data.length);
            MapConfig.UnitPool[data.data.X + "_" + data.data.Y + "_" + data.data.Z] = data.data.Units;
        },"json");
    }
    else {
        MapConfig.ListUnits = data;
        //console.log("from cache" + data.length);
        SyncFilter();
        ApplyFilter();
    }
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
        if (unit.BatchId == 0) {
        }
        else {
            if (unit.DataFixed != 1) {
                unit.DataFixed = 1;
            }
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
    if (MapConfig.Filter.Enabeld != false) {
        $("#FilterCondtion").fadeIn();
    }
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


        if (!skipBounds) {
            var x = parseFloat(unit.X8);
            var y = parseFloat(unit.Y8);
            if (x1 > x || x > x2) {
                //console.log("out of bound...");
                continue;
            }
            if (y1 > y || y > y2) {
                //console.log("out of bound...");
                continue;
            }
        }

        //可能有重复,需要过滤
        var unitId = unit.PK;
        if (unit.BatchId > 0) {
            unitId = unitId + "_" + unit.BatchId;
        }
        if (buffer[unitId] == 1) {
            continue;
        }
        buffer[unitId] = 1;
        if (unit.Category == 1) {
            if (!MapConfig.Filter.XinFang.Enabled) {
                continue;
            }
            if (!FilterUnitPrice(unit)) {
                //console.log("xf price:");
                //console.log(unit);
                continue;
            }
        }
        else if (MapConfig.Filter.EsFang.Enabled) {

            if (!FilterUnitPrice(unit)) {
                continue;
            }
        }
        else if (MapConfig.Filter.ZuFang.Enabled) {

            if (!FilterUnitRentPrice(unit)) {
                continue;
            }
        }
        else {
            continue;
        }
        //按照面积进行过滤
        if (!FilterRoomArea(unit)) {
            //console.log("area:");
            //console.log(unit);
            continue;
        }
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

function FilterRoomArea(unit) {

    var succ = false;
    if (!MapConfig.Filter.Area.All) {
        if (MapConfig.Filter.Area.A00) {
            if (unit.MaxRoomArea < 50) {
                succ = true;
            }
        }
        if (MapConfig.Filter.Area.A50) {
            if (unit.MaxRoomArea < 80 && unit.MaxRoomArea >= 50) {
                succ = true;
            }
        }
        if (MapConfig.Filter.Area.A80) {
            if (unit.MaxRoomArea < 110 && unit.MaxRoomArea >= 80) {
                succ = true;
            }
        }
        if (MapConfig.Filter.Area.A110) {
            if (unit.MaxRoomArea < 130 && unit.MaxRoomArea >= 110) {
                succ = true;
            }
        }
        if (MapConfig.Filter.Area.A130) {
            if (unit.MaxRoomArea < 150 && unit.MaxRoomArea >= 130) {
                succ = true;
            }
        }
        if (MapConfig.Filter.Area.A150) {
            if (unit.MaxRoomArea < 200 && unit.MaxRoomArea >= 150) {
                succ = true;
            }
        }
        if (MapConfig.Filter.Area.A200) {
            if (unit.MaxRoomArea >= 200) {
                succ = true;
            }
        }
    }
    else {
        succ = true;
    }
    return succ;
}

function FilterUnitRentPrice(unit) {
    if (!MapConfig.Filter.RentPrice.All) {
        var price = unit.MinRentPrice;
        if (price < 1000) {
            if (!MapConfig.Filter.RentPrice.P0000) {
                return false;
            }
        }
        else if (price < 2000) {
            if (!MapConfig.Filter.RentPrice.P1000) {
                return false;
            }
        }
        else if (price < 3000) {
            if (!MapConfig.Filter.RentPrice.P2000) {
                return false;
            }
        }
        else if (price < 4000) {
            if (!MapConfig.Filter.RentPrice.P3000) {
                return false;
            }
        }
        else if (price >= 4000) {
            if (!MapConfig.Filter.RentPrice.P4000) {
                return false;
            }
        }
    }
    return true;
}

function FilterUnitPrice(unit) {
    if (!MapConfig.Filter.Price.All) {
        if (unit.Price < 5000) {
            if (!MapConfig.Filter.Price.P5000) {
                return false;
            }
        }
        else if (unit.Price < 10000) {
            if (!MapConfig.Filter.Price.P10000) {
                return false;
            }
        }
        else if (unit.Price < 15000) {
            if (!MapConfig.Filter.Price.P15000) {
                return false;
            }
        }
        else if (unit.Price < 20000) {
            if (!MapConfig.Filter.Price.P20000) {
                return false;
            }
        }
        else if (unit.Price < 25000) {
            if (!MapConfig.Filter.Price.P25000) {
                return false;
            }
        }
        else if (unit.Price < 30000) {
            if (!MapConfig.Filter.Price.P30000) {
                return false;
            }
        }
        else if (unit.Price < 50000) {
            if (!MapConfig.Filter.Price.P50000) {
                return false;
            }
        }
        else {
            if (!MapConfig.Filter.Price.P60000) {
                return false;
            }
        }
    }
    return true;
}

//重新过滤
function ResetFilter() {
    $("#FilterCondtion").fadeOut();
}

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
