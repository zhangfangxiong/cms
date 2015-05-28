F.module(mapCityDic+"static/common/ui/jquery_cookie/jquery_cookie.js", function (b, a) {
    (function (c) {
        if (typeof define === "function" && define.amd) {
            define(["jquery"], c)
        } else {
            if (typeof a === "object") {
                a = c
            } else {
                c(jQuery)
            }
        }
    } (function (h) {
        var c = /\+/g;

        function f(k) {
            return d.raw ? k : encodeURIComponent(k)
        }
        function i(k) {
            return d.raw ? k : decodeURIComponent(k)
        }
        function j(k) {
            return f(d.json ? JSON.stringify(k) : String(k))
        }
        function e(k) {
            if (k.indexOf('"') === 0) {
                k = k.slice(1, -1).replace(/\\"/g, '"').replace(/\\\\/g, "\\")
            }
            try {
                k = decodeURIComponent(k.replace(c, " "))
            } catch (l) {
                return
            }
            try {
                return d.json ? JSON.parse(k) : k
            } catch (l) { }
        }
        function g(l, k) {
            var m = d.raw ? l : e(l);
            return h.isFunction(k) ? k(m) : m
        }
        var d = h.cookie = function (r, q, w) {
            if (q !== undefined && !h.isFunction(q)) {
                w = h.extend({}, d.defaults, w);
                if (typeof w.expires === "number") {
                    var s = w.expires,
							v = w.expires = new Date();
                    v.setDate(v.getDate() + s)
                }
                return (document.cookie = [f(r), "=", j(q), w.expires ? "; expires=" + w.expires.toUTCString() : "", w.path ? "; path=" + w.path : "", w.domain ? "; domain=" + w.domain : "", w.secure ? "; secure" : ""].join(""))
            }
            var x = r ? undefined : {};
            var u = document.cookie ? document.cookie.split("; ") : [];
            for (var p = 0, n = u.length; p < n; p++) {
                var o = u[p].split("=");
                var k = i(o.shift());
                var m = o.join("=");
                if (r && r === k) {
                    x = g(m, q);
                    break
                }
                if (!r && (m = g(m)) !== undefined) {
                    x[k] = m
                }
            }
            return x
        };
        d.defaults = {};
        h.removeCookie = function (l, k) {
            if (h.cookie(l) !== undefined) {
                h.cookie(l, "", h.extend({}, k, {
                    expires: -1
                }));
                return true
            }
            return false
        }
    }));
    return a
}, []);