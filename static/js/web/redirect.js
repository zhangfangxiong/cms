
$(document).ready(function () {
    var url = "shanghai";
    var name = "上海";
    var city = "";
    var province = "";
    try {
        city = geoip_city().toLowerCase();
        province = geoip_region_name().toLowerCase();
    } catch (e) {

    }
    if (city == "shanghai") {
        url = city;
        name = "上海";
    }
    else if (city == "beijing") {
        url = city;
        name = "北京";
    }
    else if (city == "tianjin") {
        url = city;
        name = "天津";
    }
    else if (city == "chongqing") {
        url = city;
        name = "重庆";
    }
    else if (city == "guangzhou") {
        url = city;
        name = "广州";
    }
    else if (city == "shenzhen" || province == "guangdong") {
        url = "shenzhen";
        name = "深圳";
    }
    else if (city == "shenyang" || province == "liaoning") {
        url = "shenyang";
        name = "沈阳";
    }
    else if (city == "dalian" || province == "liaoning") {
        url = "dalian";
        name = "大连";
    }
    else if (city == "changchun" || province == "jilin") {
        url = "changchun";
        name = "长春";
    }
    else if (city == "hangzhou" || province == "zhejiang") {
        url = "hangzhou";
        name = "杭州";
    }
    else if (city == "wuhan" || province == "hubei") {
        url = "wuhan";
        name = "武汉";
    }
    else if (city == "xian" || province == "shaanxi") {
        url = "xian";
        name = "西安";
    }
    else if (city == "chengdu" || province == "sichuan") {
        url = "chengdu";
        name = "成都";
    }
    else if (city == "nanjing" || province == "jiangshu") {
        url = "nanjing";
        name = "南京";
    }
    else if (city == "wuxi" || province == "jiangshu") {
        url = "wuxi";
        name = "无锡";
    }
    else if (city == "suzhou" || province == "jiangshu") {
        url = "suzhou";
        name = "苏州";
    }
    else if (city == "qingdao" || province == "shandong") {
        url = "qingdao";
        name = "青岛";
    }
    else if (city == "changsha" || province == "hunan") {
        url = "changsha";
        name = "长沙";
    }
    else if (city == "zhengzhou" || province == "henan") {
        url = "zhengzhou";
        name = "郑州";
    }
    else if (city == "fuzhou" || province == "fujian") {
        url = "fuzhou";
        name = "福州";
    }
    else if (city == "xiamen" || province == "fujian") {
        url = "xiamen";
        name = "厦门";
    }
    url = "/change/" + url;
    $(".city_name").html(name);
    $(".city_link").attr({ href: url, title: "点击进入" + name + "站" });

    $("#location_city").show();
    $(".dl_citys dt").show();
    //window.location = url;
    $.each($(".area_citys>dd>a"), function (i, n) {
        var cityCode = $(n).attr("href").replace("/change/", "");
        if (cityCode == city) {
            $(n).addClass("current");
        }
    });
});