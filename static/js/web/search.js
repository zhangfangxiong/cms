window.Searcher = {
    Key: null, //输入框
    City: null,
    IntervalID: null,
    FocusID: null,
    BlurID: null,
    AdvBlurID: null,
    AutoSuggest: null,
    ShowSuggest: null,
    HideSuggest: null,
    LastKey: null,
    Dropdown: null,
    TemplateHTML: null,
    SelectedUrl: "",
    SelectedIndex: -1,
    DefaultInputFocus: true,
    Search: "search",
    // XfEsf: "0",
    SubmitIsRedirect: true,//选择楼盘按下enter是否跳转
    Page: null
};
var searcher = window.Searcher;
$(document).ready(function () {
    searcher.City = $("#city");
    //搜索进入地图
    var $search = $("#searchMap");
    //默认搜索框
    var key = $("#key")[0];
    $search.click(function () {
        var value = key.value;
        value = value.replace(/^\s+/gi, "").replace(/\s+$/gi, "").replace("%", "");
        if (key.defaultValue == value) {
            value = "";
            key.value = value;
            window.location = "/" + searcher.City.val() + "/";
            return;
        }
        if (/^\s*$/.test(value)) {
            window.location = "/" + searcher.City.val() + "/";
            return;
        }
        window.location = "/" + searcher.City.val() + "/" + encodeURIComponent(value);
    });

    //搜索按钮进列表
    var $searchList = $("#searchList");
    $searchList.click(function () {
        var value = key.value;
        value = value.replace(/^\s+/gi, "").replace(/\s+$/gi, "").replace("%", "");
        if (window.Searcher.Page == "AdvancedSearch") {
            if (key.defaultValue == value) {
                value = "";
                key.value = value;
                keyword = "";
            } else {
                keyword = value;
            }
            //供高级搜索页面使用
            feature = 0;
            loadXfData(true);
            searcher.StopMonite();
            searcher.HideSuggest();
            return;
        }
        if (window.Searcher.Page == "Map") {
            if (key.defaultValue == value) {
                value = "";
                key.value = value;
                window.location = "/" + searcher.City.val() + "/";
                return;
            }
            if (/^\s*$/.test(value)) {
                window.location = "/" + searcher.City.val() + "/";
                return;
            }
            window.location = "/" + searcher.City.val() + "/" + encodeURIComponent(value);
            return;
        }

        if (key.defaultValue == value) {
            value = "";
            key.value = value;
            window.location = "/" + searcher.City.val() + "/advanced/search";
            return;
        }
        if (/^\s*$/.test(value)) {
            window.location = "/" + searcher.City.val() + "/advanced/search";
            return;
        }
        window.location = "/" + searcher.City.val() + "/advanced/search?k=" + encodeURIComponent(value);
    });
    $("#key").attr("autocomplete", "off").focus(searcher.KeyFocus).click(searcher.KeyClick).blur(searcher.KeyBlur).keydown(searcher.Submit);
    ////pk添加楼盘搜索框
    //var $pkSearch = $("#searchPK");
    //var $pkKey = $("#keyPK");
    //$pkSearch.click(function () {
    //    var id = $pkKey.attr("pk_id");
    //    var uId = $pkKey.attr("uid");
    //    var locat = location.href;
    //    $.post("/savePK/" + id, { uId: uId, iId: id }, function (data) {
    //        if (data.dataCode == 2) {
    //            alert("不能添加重复项");
    //            return;
    //        }
    //        closeBg("div_unitPK");
    //        var cUrl = locat.substring(0, locat.indexOf('?'));
    //        window.location = cUrl + "?id=" + data.value;
    //    });
    //});
    //$pkKey.attr("autocomplete", "off").focus(searcher.KeyFocus).click(searcher.KeyClick).blur(searcher.KeyBlur).keydown(searcher.Submit);
});

searcher.KeyBlur = function () {
    //  console.log("blur");
    var searcher = window.Searcher;
    searcher.Key = $(this);
    var key = searcher.Key[0];
    if ("" == key.value) {
        key.value = key.defaultValue;
    }
    searcher.HideSuggest();
    searcher.StopMonite();
};
searcher.KeyClick = function () {
    // console.log("click");
    var searcher = window.Searcher;
    window.clearTimeout(searcher.FocusID);
    var key = searcher.Key[0];
    if (key.defaultValue == key.value) {
        key.value = "";
    }
    searcher.Key.css("color", "#333");
    searcher.ShowSuggest();
    searcher.StartMonite();
};
searcher.KeyFocus = function () {
    // console.log("focus");
    var searcher = window.Searcher;
    searcher.Key = $(this);
    var keyId = searcher.Key.attr("id");
    switch (keyId) {
        case "key":
            searcher.SubmitIsRedirect = true;
            //  searcher.XfEsf = 0;
            searcher.DefaultInputFocus = true;
            searcher.Dropdown = $("#suggestList");
            searcher.TemplateHTML = $("#suggestTemplate").html();
            $("#suggestList_2").fadeOut({ quene: false });
            $("#suggestListPK").fadeOut({ quene: false });
            break;
            //case "keyPK":
            //    searcher.SubmitIsRedirect = false;
            //    searcher.XfEsf = 1;
            //    searcher.DefaultInputFocus = true;
            //    searcher.Dropdown = $("#suggestListPK");
            //    searcher.TemplateHTML = $("#suggestTemplatePK").html();
            //    $("#suggestList_2").fadeOut({ quene: false });
            //    $("#suggestList").fadeOut({ quene: false });
            //    break;
        default:
    }
    searcher.FocusID = window.setTimeout(searcher.KeyClick, 200);
};
searcher.Submit = function (evt) {
    searcher = window.searcher;
    if (!searcher.Key) {
        return;
    }
    var key = searcher.Key[0];
    if (key.defaultValue == key.value) {
        key.value = "";
    }
    var value = key.value;
    value = value.replace(/^\s+/gi, "").replace(/\s+$/gi, "").replace(/[~!@#$%\^\+\*&\\\/\?\|:\.<>{}';="]/, "");
    var items = $("dl", searcher.Dropdown);
    switch (evt.keyCode || evt.key) {
        case 0x0D:
        case 0x0A:
            if (!searcher.SubmitIsRedirect) {
                //pk添加楼盘的搜索框
                var sItem = $(items[searcher.SelectedIndex]);
                sItem.click();
                searcher.HideSuggest();
                return;
            }
            if (searcher.SelectedIndex > -1) {
                window.location = searcher.SelectedUrl;
                return;
            }
            if (window.Searcher.Page != "Map") {
                if (window.Searcher.Page == "AdvancedSearch") {
                    if (key.defaultValue == value) {
                        value = "";
                        key.value = value;
                        keyword = "";
                    } else {
                        keyword = value;
                    }
                    if (keyword == "") {
                        return;
                    }
                    searcher.StopMonite();
                    searcher.HideSuggest();
                    loadXfData(true);
                }
                else {
                    if (key.defaultValue == value) {
                        value = "";
                        key.value = value;
                        window.location = "/" + searcher.City.val() + "/advanced/search";
                        return;
                    }
                    if (/^\s*$/.test(value)) {
                        window.location = "/" + searcher.City.val() + "/advanced/search";
                        return;
                    }
                    window.location = "/" + searcher.City.val() + "/advanced/search?k=" + encodeURIComponent(value);
                }
                return;
            }

            //if (searcher.XfEsf > 0) {
            //    return;
            //}
            if (key.defaultValue == value) {
                window.location = "/" + searcher.City.val() + "/";
                return;
            }
            if (/^\s*$/.test(value)) {
                window.location = "/" + searcher.City.val() + "/";
                return;
            }
            value = encodeURIComponent(value);
            window.location = "/" + searcher.City.val() + "/" + value;
            return;
        case 38:
            searcher.SelectedIndex--;
            if (searcher.SelectedIndex < 0) searcher.SelectedIndex = items.length - 1;
            break;
        case 40:
            //console.log(searcher.SelectedIndex);
            searcher.SelectedIndex++;
            if (searcher.SelectedIndex >= items.length) searcher.SelectedIndex = 0;
            break;
        default:
            searcher.SelectedIndex = -1;
            searcher.SelectedUrl = "";
            break;
    }
    items.removeClass("selected");
    var selectedItem = $(items[searcher.SelectedIndex]);
    searcher.SelectedUrl = selectedItem.attr("Url");
    selectedItem.addClass("selected");
};

//搜索的监视线程
searcher.AutoSuggest = function () {
    var searcher = window.Searcher;
    var key = searcher.Key.val();
    key = key.replace(/^\s+/gi, "").replace(/\s+$/gi, "").replace(/[~!@#$%\^\+\*&\\\/\?\|:\.<>{}';="]/, "");
    if (key != searcher.LastKey) {
        searcher.LastKey = key;
        if (key.length > 0) {
            var category = 1;//搜索新房还是二手房
          //  var selectCat = $("#select_category").children("em");
           // if (selectCat[0]) {
           //     category = selectCat.attr("cat");
          //  }
          var param={keywords:key,zoomLevel:12};
            //var url = "/" + searcher.City.val() + "/suggest/" + category + "/" + encodeURIComponent(key);
            //  url = searcher.XfEsf > 0 ? url + "/xf" : url;
            $.post('/map/Mapajax',param, function (data) {
                searcher = window.Searcher;
                //if (searcher.XfEsf > 0 && data.Overlays.length < 1) {
                //    $("#suggestListPK").html("没有结果。请重新搜索！").show();
                //    return;
                //}
                if (searcher.Key.val() == key) {
                    if (MapConfig.MapElement != null) {
                        ZoomMap(data.data.ZoomLevel, data.data.X8, data.data.Y8);
                        MapConfig.MapData.Overlays = data.data.Overlays;
                        InitializeTips();
                    }
                    searcher.SelectedIndex = -1;
                    searcher.SelectedUrl = "";
                    var htmls = [];
                    var n = 0;
                    if (data.data.Overlays == undefined) {
                        return;
                    }
                    for (var i = 0; i < data.data.Overlays.length; i++) {
                        var unit = data.data.Overlays[i];
                        var html = GenerateListItem(unit, searcher.TemplateHTML);
                        htmls.push(html);
                        n++;
                        if (searcher.DefaultInputFocus) {
                            if (n >= 7) {
                                break;
                            }
                        } else {
                            if (n >= 3) {
                                break;
                            }
                        }
                    }
                    if (htmls.length > 0) {
                        searcher.ShowSuggest(htmls.join(""));
                    }
                    else {
                        searcher.ShowSuggest("");
                    }
                }
            },"json");
        }
    }
};
//开始监视用户输入
searcher.StartMonite = function () {
    var searcher = window.Searcher;
    searcher.StopMonite();
    searcher.IntervalID = window.setInterval(searcher.AutoSuggest, 300);
};
//停止监视用户输入
searcher.StopMonite = function () {
    var searcher = window.Searcher;
    if (searcher.IntervalID != null) {
        window.clearInterval(searcher.IntervalID);
    }
};
//显示搜索结果
searcher.ShowSuggest = function (html) {
    var searcher = window.Searcher;
    searcher.StartMonite();
    window.clearTimeout(searcher.BlurID);
    html = html || "";
    var func = function () {
        searcher.Dropdown.html(html);
        var list = $("dl", searcher.Dropdown);
        if (list.length > 0) {
            searcher.Dropdown.fadeIn({ duration: 200, quene: false });
        }
    };
    if (searcher.Dropdown.css("opacity") != 0) {
        searcher.Dropdown.fadeOut({ duration: 200, quene: false, complete: func });
    }
    else {
        func();
    }
};
//隐藏搜索结果
searcher.HideSuggest = function () {
    var searcher = window.Searcher;
    searcher.BlurID = window.setTimeout(function () {
        var searcher = window.Searcher;
        window.clearInterval(searcher.IntervalID);
        searcher.Dropdown.fadeOut({ quene: false });
    }, 200);
};

//function visitUnit(unitName, unitUrl) {
//    var url = "/" + searcher.City.val() + "/VisitUnit";
//    $.post(url, { searchId: MapConfig.MapData.SearchID, unitName: unitName, unitUrl: unitUrl });
//}
