F.use([mapCityDic+"static/common/ui/jquery/jquery.js", mapCityDic+"static/common/ui/jquery_cookie/jquery_cookie.js", mapCityDic+"static/common/ui/vectorMap/vectorMap.js", mapCityDic+"static/common/ui/bubblePanel/bubblePanel.js"], function (h, k, j, d) {
    k(h);
    if (h.cookie("meishiActivity") != 1) { }
    h("#dialog-info .dialog-info").click(function () {
        h("#dialog-info").hide();
        h("#dialog-mask").hide()
    });
    h("#dialog-info .dialog-sumbit").click(function () {
        var y = h("#dialog-input").val();
        var A = allCityList[y];
        var z = h("#dialog-tip");
        if (A) {
            window.open("/change/" + A.citydomain)
        } else {
            z.html("\u62b1\u6b49\uff0c\u57ce\u5e02\u767e\u79d1\u6682\u672a\u6536\u5f55\u8be5\u57ce\u5e02\u3002\u770b\u770b\u522b\u7684\u57ce\u5e02\u5427");
            z.show();
            setTimeout(function () {
                z.hide();
                z = null
            }, 3000)
        }
    });
    h("#dialog-input").keypress(function (y) {
        if (y.keyCode == 13) {
            h("#dialog-info .dialog-sumbit").trigger("click")
        }
    });
    h("#search a").click(function () {
        var y = h(this).prev().val();
        var A = allCityList[y];
        var z = h("#search .msg");
        if (A) {
            if (A.status == 1) {
                z.html("\u8be5\u57ce\u5e02\u8fd8\u5728\u5efa\u8bbe\u4e2d\uff0c\u5148\u770b\u770b\u522b\u7684\u57ce\u5e02\u5427~");
                z.show();
                setTimeout(function () {
                    z.hide();
                    z = null
                }, 3000)
            } else {
                window.open("/change/" + A.citydomain)
            }
        } else {
            z.html("\u62b1\u6b49\uff0c\u57ce\u5e02\u767e\u79d1\u6682\u672a\u6536\u5f55\u8be5\u57ce\u5e02\u3002\u770b\u770b\u522b\u7684\u57ce\u5e02\u5427");
            z.show();
            setTimeout(function () {
                z.hide();
                z = null
            }, 3000)
        }
    });
    h("#search input").keypress(function (y) {
        if (y.keyCode == 13) {
            h("#search a").trigger("click")
        }
    });
//    var p = {
//        jiangsu: [["nanjing"], ["suzhou"], ["wuxi"], ["changzhou"]],
//        guangdong: [["guangzhou"], ["shenzhen"]],
//        liaoning: [["shenyang"], ["dalian"]],
//        fujian: [["fuzhou"], ["xiamen"]],
//        beijing: ["beijing"],
//        shanghai: ["shanghai"],
//        henan: ["zhengzhou"],
//        tianjin: ["tianjin"],
//        chongqing: ["chongqing"],
//        jilin: ["changchun"],
//        sichuan: ["chengdu"],
//        zhejiang:[["hangzhou"],["ningbo"]],
//        hubei: ["wuhan"],
//        shandong: [["qingdao"],["jinan"]],
//        hunan: ["changsha"],
//        hainan: ["haikou"],
//        guizhou: ["guiyang"],
//        anhui: ["hefei"],
//        yunnan: ["kunming"],
//        guangxi: ["nanning"]
//    };
    var p = {
        jiangsu: [["nanjing"], ["suzhou"], ["wuxi"], ["changzhou"]],
        guangdong: [["guangzhou"], ["shenzhen"]],
        liaoning: [["shenyang"], ["dalian"]],
        fujian: [["fuzhou"], ["xiamen"]],
        beijing: ["beijing"],
        shanghai: ["shanghai"],
        henan: ["zhengzhou"],
        tianjin: ["tianjin"],
        chongqing: ["chongqing"],
        jilin: ["changchun"],
        sichuan: ["chengdu"],
        zhejiang: [["hangzhou"], ["ningbo"]],
        hubei: ["wuhan"],
        shandong: [["qingdao"], ["jinan"]],
        guangxi: [["nanning"]],
        anhui: [["hefei"]],
        yunnan: ["kunming"],
        heilongjiang: ["haerbin"]
    };

    var t = h("#mapBox"),
        o = h("#mapNav"),
        u = h(".mapContainer .container").width(),
        r = h("#wholeMapBtn"),
        q = h("#mapInfoLayer"),
        w = h("#southChinaSea"),
        a = h("#meishiActivityEntrance"),
        c = !document.implementation.hasFeature("http://www.w3.org/TR/SVG11/feature#BasicStructure", "1.1"),
        n = {
            areaColor: "#FFF",
            areaColorHover: "#A5CCF8",
            strokeColor: "#E9E8E7",
            strokeColorHover: "#8EBBEE",
            areaMaskedColor: "#DAD9D7",
            strokeMaskedColor: "#cbcac8",
            areaMaskedColorHover: "#d2d1cf",
            sublayerAreaColor: "#FAFAFA"
        };
    var l = function (y) {
        return '<a class="infoBubbleCon" href="change/' + y.lnk + '" target="_blank">' +
                    '<img src="' + y.img + '" class="inlineBl" />' +
                    '<div class="info inlineBl">' +
                        '<p class="name"><span>' + y.cityName + '</span>' +
                            //'<em class="circleRight">&#xE602;</em>' +
                        '</p>' +
                        //'<p class="detail"><em>&#xE600;</em>&nbsp;<i>' + y.viewTimes + "</i>&nbsp;&nbsp;"+ '</p>' +
                    '</div>' +
                    '<a href="/change/' + y.lnk + '" class="zk_link"><s class="zk_ico"></s></a>' +
                '</a>' +
                '<em class="triangle">&#xE603;</em>';
    };
    a.on("click", function () {
        h(this).hide();
        h("#dialog-info").show();
        h("#dialog-mask").show()
    });
    a.on("click", "a", function (y) {
        a.hide();
        h.cookie("meishiActivity", "1", {
            path: "/",
            expires: 1
        });
        y.stopPropagation()
    });
    var v, b = {};

    function g(z, y) {
        if (b[z]) {
            y(b[z])
        } else {
            h.getJSON(mapCityDic+"cms/rc/cityname/mapData/" + z + ".rar", function (A) {
                b[z] = A;
                y(b[z])
            })
        }
    }
    function m(y) {
        setTimeout(function () {
            q.show();
            h.each(p, function (z, A) {
                g(z, function (B) {
                    h.each(A, function (D, G) {
                        var H = j.getAreaCentroid(B.objects[G], B.arcs, B.transform, y),
                            E = {
                                left: H[0] * v.baseTransform.scale + v.baseTransform.translate[0],
                                top: H[1] * v.baseTransform.scale + v.baseTransform.translate[1]
                            };
                        var C = h('<a href="javascript:;" class="cityDot">&#xE604;</a>').css(E).attr({
                            area_key: z,
                            city_id: B.objects[G].properties.cityId
                        }).appendTo(q);
                        (function (I) {
                            setTimeout(function () {
                                I.css({
                                    "-webkit-transform": "scale(1)",
                                    "-moz-transform": "scale(1)",
                                    "-o-transform": "scale(1)",
                                    transform: "scale(1)"
                                })
                            }, Math.random() * 400)
                        })(C)
                    })
                })
            })
        }, c ? 100 : 0)
    }
    var f;
    var s = (function () {
        var y = 0;
        return function () {
            var z = h(".cityDot");
            f = setInterval(function () {
                h(z[y++ % z.length]).mouseenter()
            }, 3000)
        }
    })();

    function e() {
        clearInterval(f)
    }
    function i() {
        t.html('<div class="loadingFailTip"><em>&#xE605;</em><span>\ufffd\ufffd\ucdbc\ufffd\ufffd\ufffd\ufffd\ucaa7\ufffd\udca3\ufffd\ufffd\ufffd <a href="javascript:window.location.reload();">\ucba2\ufffd\ufffd\ud2b3\ufffd\ufffd</a> \ufffd\ufffd\ufffd\ufffd</span></div>')
    }
    h.ajax({
        url: mapCityDic+"cms/rc/cityname/mapData/china.rar",
        dataType: "json",
        cache: false,
        error: function () {
            i()
        },
        timeout: function () {
            i()
        },
        success: function (y) {
            x(y)
        }
    });

    function x(G) {
        v = new j(t);
        var D = new d({
            host: h("body"),
            marginFromHost: 10,
            animation: "SLIDE",
            pos: "top",
            zIndex: 900
        });
        var y, z = {};
        function B(L, J) {
            y = L;
            function K(M) {
                D.setHost(J).setContent(l({
                    img: M.picUrl,
                    //lnk: "/change/" + M.cityDomain,
                    lnk: M.cityCode,
                    cityName: M.cityName,
                    viewTimes: M.lemmaCnt,
                    voteCount: M.voteCnt
                }));
                if (!D.isVisible) {
                    D.show()
                } else {
                    if (D.relatedCityId != y) {
                        D.hide(function () {
                            D.show()
                        })
                    }
                }
                D.relatedCityId = L
            }
            if (z[L]) {
                K(z[L])
            } 
            else {
                h.getJSON("/index/index/mapCityApi", {
                    cityId: L,
                    isData: 1
                }, function (M) {
                    M= M.data;
                    if (!M.errno && M.data.cityId == y) {
                        z[L] = M.data;
                        K(z[L])
                    }
                })
            }
        }
        function H() {
            w.css({
                left: I.boundingRect.left + I.boundingRect.width - w.outerWidth(),
                top: I.boundingRect.top + I.boundingRect.height - w.outerHeight()
            }).show().animate({
                opacity: 1
            })
        }
        a.on("show", function () {
            a.css({
                left: I.boundingRect.left + I.boundingRect.width - a.outerWidth() + 26,
                top: I.boundingRect.top + I.boundingRect.height - a.outerHeight() - 120
            }).show().animate({
                opacity: 1
            })
        });
        var E = new d({
            host: h("body"),
            content: "\u5efa\u8bbe\u4e2d",
            classNames: "cityOfflineTip",
            hideWhenBlur: false,
            hideAfterDelay: 15000,
            pos: "top",
            animation: "FADE"
        });
        D.getNode().on("mouseleave", function (L) {
            var J = h(L.relatedTarget),
                K = h('.cityDot[city_id="' + y + '"]');
            if (!(J.hasClass("area") && (J.attr("city_id") == y || K.length && K.attr("area_key") == J.data("areaKey")))) {
                D.hide();
                h(".area, .nameRect").removeClass("cur")
            }
        });
        var I = v.buildMap({
            geoData: G,
            padding: [26, (t.width() - u) / 2, 26, o.offset().left + o.width() + 15],
            onAniStart: function () {
                if (c) {
                    h(".area").each(function (J, K) {
                        K.fillcolor = n.areaColor;
                        K.strokecolor = n.strokeColor
                    });
                    h(".mask .area").each(function (J, K) {
                        K.fillcolor = n.areaMaskedColor;
                        K.strokecolor = n.strokeMaskedColor
                    })
                }
            },
            onAniComplete: function () {
                I.layerNode.removeClass("noInteraction");
                m(G.bbox);
                //setTimeout(s, 1000);�Զ�������С��
                if (c) {
                    h("#ieTipClose").one("click", function () {
                        h("#ieTip").hide()
                    });
                    setTimeout(function () {
                        h("#ieTip").show().animate({
                            opacity: 0.75,
                            top: 0
                        })
                    }, 500)
                }
                H();
                h("#dialog-info").show().animate({
                    opacity: 1
                }, function () {
                    h("#dialog-input").focus()
                });
                h("#dialog-mask").show().animate({
                    opacity: 0.3
                })
            }
        });
        t.on("mouseover", ".layer:not(.sublayer) .area", function () {
            var K = h(this),
                J = K.data("areaKey");
            h('.cityDot[area_key="' + J + '"]').addClass("breath");
            K.data("nameBox").addClass("cur");
            e();
            if (c) {
                if (K.parents(".masked").length) {
                    K[0].fillcolor = n.areaMaskedColorHover
                } else {
                    K[0].fillcolor = n.areaColorHover;
                    K[0].strokecolor = n.strokeColorHover
                }
            }
        }).on("mouseout", ".layer:not(.sublayer) .area", function (L) {
            var K = h(this),
                J = h(L.relatedTarget);
            if (!(J.parents(".bubblePanel").length || J.hasClass("cityDot") && J.attr("area_key") == K.data("areaKey"))) {
                h(".breath").removeClass("breath");
                h(".area").removeClass("cur");
                h(".nameRect").removeClass("cur");
                y = null;
                D.hide();
                if (c) {
                    if (K.parents(".masked").length) {
                        K[0].fillcolor = n.areaMaskedColor
                    } else {
                        K[0].fillcolor = n.areaColor;
                        K[0].strokecolor = n.strokeColor
                    }
                }
            }
        }).on("click", ".layer:not(.sublayer) .area", function () {
            var K = h(this),
                J = K.data("areaKey");
            h('a[provName="' + K.data("areaKey") + '"]').click()
        });
        q.on("mouseenter", ".cityDot", function (L) {
            var J = h(this),
                K = h(L.relatedTarget);
            if (!c && K.hasClass("area") && K.data("areaKey") == J.attr("area_key")) {
                K.addClass("cur");
                K.data("nameBox").addClass("cur")
            }
            B(J.attr("city_id"), J)
        }).on("mouseleave", ".cityDot", function (L) {
            var K = h(this),
                J = h(L.relatedTarget);
            if (!(J.hasClass("area") && J.data("areaKey") == K.attr("area_key") || J.parents(".bubblePanel").length)) {
                h(".area").removeClass("cur");
                h(".nameRect").removeClass("cur");
                y = null;
                D.hide()
            }
        }).on("click", ".cityDot", function () {
            h('a[provName="' + h(this).attr("area_key") + '"]').click()
        });
        r.click(function () {
            v.removeAllSublayers(true, function () {
                m(G.bbox);
                H();
                if (h.cookie("meishiActivity") != 1) { }
            });
            if (c) {
                h(".area").each(function (J, K) {
                    K.fillcolor = n.areaColor;
                    K.strokecolor = n.strokeColor
                })
            }
            r.animate({
                opacity: 0,
                top: -60
            }, function () {
                r.hide()
            });
            h(".cur", o).removeClass("cur")
        });
        o.on("click", "a[provName]:not(.cur)", function () {
            var J = h(this),
                M = J.attr("provName"),
                L = J.hasClass("municipality"),
                K = J.hasClass("noScale");
            h(".cur", o).removeClass("cur");
            if (K) {
                r.click();
                setTimeout(function () {
                    h('.cityDot[area_key="' + M + '"]').mouseover()
                }, 600)
            } else {
                J.addClass("cur");
                v.removeAllSublayers();
                q.animate({
                    opacity: 0
                }, 200, function () {
                    q.empty().hide().css({
                        opacity: 1
                    })
                });
                g(M, function (O) {
                    var P = v.addSublayer({
                        layerId: M,
                        layerData: O,
                        extScale: L ? 0.5 : 1,
                        onAniStart: function () {
                            h(".sublayer .area, .sublayer .nameRect").each(function (Q, S) {
                                var R = h(S),
                                    T = R.data("areaData").properties.cityId;
                                if (T && onlineCities[T]) {
                                    R.addClass("online").attr("city_id", T);
                                    v.addLnk({
                                        //lnk: "/change/" + onlineCities[T].cityDomain,
                                        lnk: onlineCities[T].cityDomain,
                                        node: R
                                    })
                                }
                            });
                            if (M == "hainan") {
                                H()
                            } else {
                                w.animate({
                                    opacity: 0
                                }, function () {
                                    w.hide()
                                })
                            }
                            a.animate({
                                opacity: 0
                            }, function () {
                                a.hide()
                            });
                            if (c) {
                                h(".sublayer .area").each(function (Q, R) {
                                    R.fillcolor = n.sublayerAreaColor;
                                    R.strokecolor = n.strokeColor
                                });
                                h(".sublayer .online").each(function (Q, R) {
                                    R.fillcolor = n.areaColor
                                });
                                h(".masked .area").each(function (Q, R) {
                                    R.fillcolor = n.areaMaskedColor;
                                    R.strokecolor = n.strokeMaskedColor
                                })
                            }
                        },
                        onAniComplete: function () {
                            P.layerNode.removeClass("noInteraction")
                        }
                    }),
                        N = P.boundingRect;
                    parseInt(r.css("top")) < 0 && r.css({
                        left: N.left + N.width + 30
                    }).show();
                    r.animate({
                        opacity: 1,
                        left: N.left + N.width + 30,
                        top: 100
                    })
                })
            }
        }).on("mouseenter", function () {
            e()
        });
        var C;
        t.on("mouseover", ".sublayer .area", function (M) {
            var K = h(this),
                L = K.data("nameBox"),
                N = K.attr("city_id"),
                J = h(M.relatedTarget).parents(".bubblePanel");
            if (N) {
                L.addClass("cur");
                if (c) {
                    K[0].fillcolor = n.areaColorHover
                }
            }
            if (!J.length || D.relatedCityId != N) {
                C = setTimeout(function () {
                    C = null;
                    D.hide();
                    h(".area").removeClass("cur");
                    if (N != undefined) {
                        B(N, L)
                    } else {
                        E.setHost(L).show()
                    }
                }, 200)
            }
        }).on("mouseout", ".sublayer .area", function (L) {
            var J = h(this),
                K = J.data("nameBox");
            if (c && J.hasClass("online")) {
                J[0].fillcolor = n.areaColor
            }
            if (C) {
                clearTimeout(C);
                C = null;
                K.removeClass("cur")
            } else {
                if (!h(L.relatedTarget).parents(".bubblePanel").length) {
                    D.hide();
                    y = null;
                    J.removeClass("cur");
                    K.removeClass("cur")
                } else {
                    if (!c) {
                        J.addClass("cur");
                        K.addClass("cur")
                    }
                }
            }
        }).on("click", ".sublayer .area", function (M) {
            var K = h(this),
                L = K.data("nameBox"),
                N = K.attr("city_id"),
                J = h(M.relatedTarget).parents(".bubblePanel");
            if (!N && !J.length) {
                C = null;
                h(".area").removeClass("cur");
                D.hide();
                E.setHost(L).show()
            }
        });
        var A;
        h(window).resize(function () {
            A && clearTimeout(A);
            A = setTimeout(function () {
                v.resetPos({
                    //padding: [26, (t.width() - u) / 2, 26, o.offset().left + o.width() + 15]
                });
                w.animate({
                    left: I.boundingRect.left + I.boundingRect.width - w.outerWidth(),
                    top: I.boundingRect.top + I.boundingRect.height - w.outerHeight()
                });
                a.animate({
                    left: I.boundingRect.left + I.boundingRect.width - a.outerWidth() + 26,
                    top: I.boundingRect.top + I.boundingRect.height - a.outerHeight() - 120
                });
                if (!h(".sublayer").length) {
                    q.animate({
                        opacity: 0
                    }, 200, function () {
                        q.empty().hide().css({
                            opacity: 1
                        });
                        m(G.bbox)
                    })
                } else {
                    var J = v.geoLayers[1].boundingRect;
                    r.animate({
                        left: J.left + J.width + 30
                    })
                }
            }, 100)
        })
    }
});