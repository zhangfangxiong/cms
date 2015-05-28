function AppTrack() {
};
AppTrack.StartTrack = function () {
    var state = (function () {
        var width = screen.width;
        var height = screen.height;
        var userAgent = navigator.userAgent;
        var name = "";
        var ver = "";
        var os = "";
        var b = $.browser;
        if (/\bMSIE/gi.test(userAgent)) {
            name = "MSIE";
            ver = userAgent.replace(/(.+)MSIE\s*([0-9\.]+)(.*)/gi, "$2");
        }
        else if (/\bChrome/gi.test(userAgent)) {
            name = "Chrome";
            ver = userAgent.replace(/(.+)Chrome\/([0-9\.]+)(.*)/gi, "$2");
        }
        else if (/\bSafari/gi.test(userAgent)) {
            name = "Safari";
            ver = userAgent.replace(/(.+)Safari\/([0-9\.]+)(.*)/gi, "$2");
        }
        else if (/\bFirefox/gi.test(userAgent)) {
            name = "Firefox";
            ver = userAgent.replace(/(.+)Firefox\/([0-9\.]+)(.*)/gi, "$2");
        }
        else if (/\bOpera/gi.test(userAgent)) {
            name = "Opera";
            ver = userAgent.replace(/(.*)Version\/([0-9\.]+)(.*)/gi, "$2");
        }

        if (/\bTheWorld/gi.test(userAgent)) {
            name = "世界之窗";
        }

        if (/\bSE.+MetaSr/gi.test(userAgent)) {
            name = "搜狗";
            ver = userAgent.replace(/(.+)se ([0-9\.x]+)(.*)/gi, "$2");
        }
        if (/\bQQBrowser/gi.test(userAgent)) {
            name = "QQ浏览器";
            ver = userAgent.replace(/(.+)QQBrowser\/([0-9\.x]+)(.*)/gi, "$2");
        }
        os = navigator.platform || "";
        if (/Windows/gi.test(userAgent)) {
            os = "Windows";
            if (/\sme\b/gi.test(userAgent)) {
                os += " ME";
            }
            if (/98/gi.test(userAgent)) {
                os += " 98";
            }
            if (/nt 5.0/gi.test(userAgent)) {
                os += " 2000";
            }
            if (/nt 5.1/gi.test(userAgent)) {
                os += " XP";
            }
            if (/nt 5.2/gi.test(userAgent)) {
                os += " 2003";
            }
            if (/nt 6.0/gi.test(userAgent)) {
                os += " VISTA";
            }
            if (/nt 6.1/gi.test(userAgent)) {
                os += " 7";
            }
            if (/nt 6.2/gi.test(userAgent)) {
                os += " 8";
            }
        }
        if (/Solaris/gi.test(userAgent)) {
            os = "Solaris";
        }
        if (/Mac/gi.test(userAgent)) {
            os = "Macintosh";
        }
        if (/FreeBSD/gi.test(userAgent)) {
            os = "FreeBSD";
        }
        if (/Android/gi.test(userAgent)) {
            os = "Android";
        }
        else if (/Linux/gi.test(userAgent) || /Linux/gi.test(navigator.platform || "")) {
            os = "Linux";
        }
        if (/ipad/gi.test(userAgent)) {
            os = "IPad";
        }
        else if (/iphone/gi.test(userAgent)) {
            os = "IPhone";
        }
        if (/Mobile/gi.test(userAgent)) {
            os += " Mobile";
        }
        if (/ios/gi.test(userAgent)) {
            os = "IOS";
        }
        if (/WOW64|x64|win64/gi.test(userAgent) || /_64$/gi.test(navigator.platform || "")) {
            os += " x64";
        }
        if (!/\d+/.test(width)) {
            return;
        }
        if (!/\d+/.test(height)) {
            return;
        }
        var lang = navigator.language || navigator.systemLanguage || navigator.browserLanguage || "";
        var referer = document.referrer;
        var url = window.location.toString();
        var queryString = "";
        var index = url.indexOf("?");
        if (index > 0) {
            queryString = url.substring(index);
            url = url.substring(0, index);
        }

        var city = "";
        var region = "";
        var country = "";
        var longitude = "";
        var latitude = "";
        try {
            city = geoip_city();
            region = geoip_region_name();
            country = geoip_country_name();
            longitude =geoip_longitude();
            latitude = geoip_latitude();
        } catch (e) {

        }
        return { browser: name, ver: ver, os: os, width: width, height: height, city: city, province: region, country: country, lang: lang, longitude: longitude, latitude: latitude, visitID: AppTrack.Tracker.Server.VisitID, url: url, queryString: queryString, referer: referer, controller: AppTrack.Tracker.Page.Controller, action: AppTrack.Tracker.Page.Action, title: document.title };
    })();
    $.post("/track", state, function (script) {
        eval(script);
    });
    AppTrack.Tracker.Client = state;
};

AppTrack.Tracker = { Client: { browser: null, ver: null, os: null, width: null, height: null, city: null, province: null, country: null, lang: null, longitude: null, latitude: null, x: 0, y: 0, url: null, queryString: null, referer: null, controller: null, action: null, title: null }, Track: { Mouse: [], Link: [], time: 0, count: 0 }, Link: { id: null, title: null, href: null, date: null, time: 0, x: 0, y: 0 }, Server: { PageID: null, VisitID: null, TrackID: null, UserID: null, Session: null }, Page: { Controller: null, Action: null }, Time: new Date(), Enabled: true, StartDate: new Date() };

AppTrack.History = { Mouse: {}, Link: {} };

AppTrack.StartMouseTrack = function () {
    $(document).mousemove(function (args) {
        AppTrack.Tracker.Client.x = args.clientX;
        AppTrack.Tracker.Client.y = args.clientY;
    });
    window.setInterval(function () {
        if (AppTrack.Enabled == false) {
            return;
        }
        var client = AppTrack.Tracker.Client;
        var track = AppTrack.Tracker.Track;
        var x = client.x;
        var y = client.y;
        if (x == null || y == null) {
            return;
        }
        x += parseInt(document.body.scrollLeft);
        y += parseInt(document.body.scrollTop);
        x = x - x % 50;
        y = y - y % 50;
        track.x = x;
        track.y = y;
        if (AppTrack.History.Mouse[x + "_" + y] == null) {
            AppTrack.History.Mouse[x + "_" + y] = 1;
            var o = { x: x, y: y, m: 1 };
            track.Mouse.push(o);
        }
        else {
            AppTrack.History.Mouse[x + "_" + y]++;
            track.Mouse.push({ x: x, y: y, m: AppTrack.History.Mouse[x + "_" + y] });
        }
        track.count++;
        if (track.count > 5) {
            track.count = 0;
            AppTrack.UploadLog(true);
        }
    }, 10000);
};

AppTrack.StartLinkTrack = function () {
    AppTrack.RefreshTrack();
};

AppTrack.RefreshTrack = function () {
    $("a").each(function () {
        var a = $(this);
        if (a.attr("tk") != "1" && /^https?:\/\/.+/gi.test(this.href) && !/^#.*$/gi.test(a.attr("href"))) {
            a.mouseover(function (args) {
                var link = $(args.srcElement || args.target);
                var track = AppTrack.Tracker.Track;
                var linkObj = AppTrack.Tracker.Link;
                var element = link[0];
                var uniqueID = link.attr("uid");
                if (uniqueID == null || uniqueID == "") {
                    uniqueID = AppTrack.ToHex(parseInt(Math.random() * 0xFFFFFFFF)) + "_" + track.x + "_" + track.y;
                    link.attr("uid", uniqueID);
                }
                var pos = link.position();
                var title = link.attr("title");
                if (title == null || /^\s*$/.test(title)) {
                    title = link.attr("alt");
                }
                if (title == null || /^\s*$/.test(title)) {
                    title = link.text();
                }
                linkObj.x = pos.left;
                linkObj.y = pos.top;
                linkObj.id = uniqueID;
                linkObj.title = title;
                linkObj.href = link[0].href;
                linkObj.date = new Date();
            });
            a.mouseout(function (args) {
                var lo = AppTrack.Tracker.Link;
                lo.time = new Date() - lo.date;
                var links = AppTrack.Tracker.Track.Link;
                var succ = false;
                for (var i = 0; i < links.length; i++) {
                    var o = links[i];
                    if (o.id == lo.id) {
                        o.time += lo.time;
                        succ = true;
                        break;
                    }
                }
                if (!succ) {
                    AppTrack.Tracker.Track.Link.push({ title: lo.title || "", href: lo.href || "", time: lo.time, id: lo.id || "", x: lo.x, y: lo.y });
                }
            });
            a.click(function (args) {
                var lo = AppTrack.Tracker.Link;
                lo.time = new Date() - lo.date;
                var links = AppTrack.Tracker.Track.Link;
                var succ = false;
                for (var i = 0; i < links.length; i++) {
                    var o = links[i];
                    if (o.id == lo.id) {
                        o.time += lo.time;
                        succ = true;
                        break;
                    }
                }
                if (!succ) {
                    AppTrack.Tracker.Track.Link.push({ title: lo.title, href: lo.href || "", time: lo.time, id: lo.id, x: lo.x, y: lo.y });
                }
                AppTrack.UploadLog(false, "0", lo.title, link[0].href);
            });
            a.attr("tk", "1");
        }
    });
};
AppTrack.Request = function (url, data, async) {
    var request;
    if (window.XMLHttpRequest) {
        request = new XMLHttpRequest();
    }
    else if (window.ActiveXObject) {
        try {
            request = new ActiveXObject("Msxml2.XMLHTTP");
        }
        catch (e) {
            try {
                request = new ActiveXObject("Microsoft.XMLHTTP");
            }
            catch (e) {
                return;
            }
        }
    }
    request.open("POST", url, async);
    request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    request.send(data);
};
AppTrack.UploadLog = function (async, close, next, nextUrl) {
    var links = AppTrack.Tracker.Track.Link;
    if (close != "1") {
        close = "0";
    }
    next = next || "";
    nextUrl = nextUrl || "";
    AppTrack.Tracker.Track.Link = [];
    var link = "";
    for (var i = 0; i < links.length; i++) {
        var item = links[i];
        if (item.href == null) {
            continue;
        }
        if (i > 0) {
            link += "#";
        }
        var update = 0;
        if (AppTrack.History.Link[item.id] != null) {
            update = 1;
        }
        else {
            AppTrack.History.Link[item.id] = 1;
        }
        link += encodeURIComponent(item.title) + "^" + encodeURIComponent(item.href) + "^" + encodeURIComponent(item.time) + "^" + encodeURIComponent(item.id) + "^" + encodeURIComponent(item.x) + "^" + encodeURIComponent(item.y) + "^" + update;
    }

    var mouses = AppTrack.Tracker.Track.Mouse;
    AppTrack.Tracker.Track.Mouse = [];
    var mouse = "";
    for (var i = 0; i < mouses.length; i++) {
        var item = mouses[i];
        if (i > 0) {
            mouse += "#";
        }
        mouse += encodeURIComponent(item.x) + "^" + encodeURIComponent(item.y) + "^" + encodeURIComponent(item.m);
    }
    var st = new Date() - AppTrack.Tracker.StartDate;

    AppTrack.Tracker.StartDate = new Date();
    var s = AppTrack.Tracker.Server;
    var c = AppTrack.Tracker.Client;
    AppTrack.Request("/trackaction", "link=" + encodeURIComponent(link) + "&mouse=" + encodeURIComponent(mouse) + "&pid=" + s.PageID + "&vid=" + s.VisitID + "&title=" + encodeURIComponent(c.title) + "&url=" + encodeURIComponent(c.url) + "&queryString=" + encodeURIComponent(c.queryString) + "&close=" + close + "&next=" + encodeURIComponent(next) + "&nextUrl=" + encodeURIComponent(nextUrl) + "&st=" + encodeURIComponent(st), async);
};
AppTrack.ToHex = function (n) {
    var hex = ["0", "1", "2", "3", "4", "5", "6", "7", "8", "9", "A", "B", "C", "D", "E", "F"];
    var a = [];
    a.push(hex[n % 16]);
    n = n - (n % 16);
    while (n > 0) {
        a.push(hex[n % 16]);
        n = (n - (n % 16)) / 16;
    }
    return a.join("");
};
$(document).ready(function () {
    AppTrack.StartTrack();
    //AppTrack.StartMouseTrack();
    //AppTrack.StartLinkTrack();
});
$(window).unload(function () {
    AppTrack.UploadLog(false, "1");
}).focus(function () {
    AppTrack.Enabled = true;
}).blur(function () {
    AppTrack.Enabled = false;
    AppTrack.UploadLog(true);
});