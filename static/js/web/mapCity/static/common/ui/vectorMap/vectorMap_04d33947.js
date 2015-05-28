F.module(mapCityDic+"static/common/ui/vectorMap/vectorMap.js", function (e, g) {
    var h = e(mapCityDic+"static/common/ui/jquery/jquery.js");
    var m = (function () {
        var q = 0;
        return function () {
            return +new Date() + q++
        }
    })();

    function b(v) {
        var s = 1 * Math.PI * 2,
            r = s,
            u = s / 2,
            t = 1.27,
            q = v[0] * Math.PI / 180,
            z = v[1] * Math.PI / 180;
        z = 1.25 * Math.log(Math.tan(0.25 * Math.PI + 0.4 * z));
        q = (r / 2) + (r / (2 * Math.PI)) * q;
        z = (u / 2) - (u / (2 * t)) * z;
        return [q * i, z * i]
    }
    function l(B) {
        function q() {
            var C = -1,
                E = B.length,
                y, x = B[E - 1],
                D = 0;
            while (++C < E) {
                y = x;
                x = B[C];
                D += y[1] * x[0] - y[0] * x[1]
            }
            return D * 0.5
        }
        var t = -1,
            r = B.length,
            A = 0,
            v = 0,
            z, w = B[r - 1],
            u;
        var s = -1 / (6 * q());
        while (++t < r) {
            z = w;
            w = B[t];
            u = z[0] * w[1] - w[0] * z[1];
            A += (z[0] + w[0]) * u;
            v += (z[1] + w[1]) * u
        }
        return [A * s, v * s]
    }
    function o(u, v) {
        var w = [];
        for (var t = 0; t < v.length; ++t) {
            w.push([]);
            for (var s = 0; s < v[t].length; ++s) {
                var q = v[t][s];
                var r = [].concat(q >= 0 ? u[q] : [].concat(u[~q]).reverse());
                w[t] = w[t].concat(s ? r.slice(1) : r)
            }
        }
        return w
    }
    function j(w, r, u) {
        if (u.length > 2) {
            var s = [].concat(b([u[0], u[1]]), b([u[2], u[3]]));
            u = [s[0], s[3]]
        }
        var q = [];
        for (var v = 0; v < w.length; ++v) {
            var A = 0,
                z = 0;
            q.push([]);
            for (var t = 0; t < w[v].length; t++) {
                var B = b([(A += w[v][t][0]) * r.scale[0] + r.translate[0], (z += w[v][t][1]) * r.scale[1] + r.translate[1]]);
                q[v].push([B[0] - u[0], B[1] - u[1]])
            }
        }
        return q
    }
    var p = "http://www.w3.org/2000/svg";
    var a = {
        svg: {
            init: function () {
                var q = h.attr;
                h.attr = function (v, t, w, u) {
                    if (v.namespaceURI == p && t == "href" && w) {
                        v.setAttributeNS("http://www.w3.org/1999/xlink", t, w);
                        return w
                    } else {
                        return q.apply(null, arguments)
                    }
                };
                var s = /\s+/,
                    r = /[\t\r\n]/g;
                h.fn.addClass = function (x) {
                    var z, v, u, w, y, A, t;
                    if (h.isFunction(x)) {
                        return this.each(function (B) {
                            h(this).addClass(x.call(this, B, this.className))
                        })
                    }
                    if (x && typeof x === "string") {
                        z = x.split(s);
                        for (v = 0, u = this.length; v < u; v++) {
                            w = this[v];
                            if (w.nodeType === 1) {
                                if (w.namespaceURI == p) {
                                    y = w.className ? w.className.baseVal : h(w).attr("class");
                                    for (A = 0, t = z.length; A < t; A++) {
                                        if (! ~h.inArray(z[A], y)) {
                                            y += (y ? " " : "") + z[A]
                                        }
                                    }
                                    w.className ? (w.className.baseVal = y) : h(w).attr("class", y)
                                } else {
                                    if (!w.className && z.length === 1) {
                                        w.className = x
                                    } else {
                                        y = " " + w.className + " ";
                                        for (A = 0, t = z.length; A < t; A++) {
                                            if (! ~y.indexOf(" " + z[A] + " ")) {
                                                y += z[A] + " "
                                            }
                                        }
                                        w.className = h.trim(y)
                                    }
                                }
                            }
                        }
                    }
                    return this
                };
                h.fn.removeClass = function (y) {
                    var B, w, t, x, A, v, u;
                    if (h.isFunction(y)) {
                        return this.each(function (C) {
                            h(this).removeClass(y.call(this, C, this.className))
                        })
                    }
                    if ((y && typeof y === "string") || y === undefined) {
                        B = (y || "").split(s);
                        for (v = 0, u = this.length; v < u; v++) {
                            t = this[v];
                            if (t.nodeType === 1) {
                                if (t.namespaceURI == p) {
                                    for (x = 0, A = B.length; x < A; x++) {
                                        w = (" " + (t.className ? t.className.baseVal : h(t).attr("class")) + " ").replace(r, " ");
                                        for (x = 0, A = B.length; x < A; x++) {
                                            while (w.indexOf(" " + B[x] + " ") > -1) {
                                                w = w.replace(" " + B[x] + " ", " ")
                                            }
                                        }
                                        var z = y ? h.trim(w) : "";
                                        t.className ? (t.className.baseVal = z) : h(t).attr("class", z)
                                    }
                                } else {
                                    if (t.className) {
                                        w = (" " + t.className + " ").replace(r, " ");
                                        for (x = 0, A = B.length; x < A; x++) {
                                            while (w.indexOf(" " + B[x] + " ") > -1) {
                                                w = w.replace(" " + B[x] + " ", " ")
                                            }
                                        }
                                        t.className = y ? h.trim(w) : ""
                                    }
                                }
                            }
                        }
                    }
                    return this
                };
                h.fn.hasClass = function (t) {
                    var x = " " + t + " ",
                        v = 0,
                        u = this.length;
                    for (; v < u; v++) {
                        var w, y = this[v];
                        if (y.namespaceURI == p) {
                            w = y.className ? y.className.baseVal : h(y).attr("class")
                        } else {
                            w = y.className
                        }
                        if (y.nodeType === 1 && (" " + w + " ").replace(r, " ").indexOf(x) > -1) {
                            return true
                        }
                    }
                    return false
                }
            },
            createElement: function (q) {
                return h(document.createElementNS(p, q))
            },
            getCanvas: function (q) {
                var r = a.svg.createElement("svg").attr({
                    width: "100%",
                    height: "100%"
                }).addClass("mapCanvas").appendTo(q);
                r.append(a.svg.createElement("defs").attr({
                    id: "svgDefs"
                }));
                return a.svg.getGroup().appendTo(r)
            },
            getGroup: function (q) {
                return a.svg.createElement("g").addClass(q || "group")
            },
            getPath: function (s) {
                var q = [];
                for (var r = 0; r < s.length; ++r) {
                    q.push("M" + s[r].shift().join() + " L" + s[r].join())
                }
                return a.svg.createElement("path").attr({
                    d: q.join(" ")
                })
            },
            getDot: function (q) {
                return a.svg.createElement("circle").attr({
                    cx: q[0],
                    cy: q[1]
                })
            },
            getLnk: function (q) {
                if (effectiveCity.indexOf(q) >= 0) {
                    return a.svg.createElement("a").attr({
                        href: "/change/" + q,
                        target: "_blank"
                    })
                }
                else {
                    return a.svg.createElement("a").attr({
                        href: "javascript:;"
                    })
                }
            },
            getNameBox: function (q) {
                return a.svg.getGroup("nameRect").append(a.svg.createElement("rect").addClass("nameBg")).append(a.svg.createElement("text").addClass("name").text(q))
            },
            drawShadows: function (r) {
                var u = [],
                    t = "srcBoundary_" + r.id;
                if (!h("#" + t).length) {
                    for (var s = 0; s < r.arcs.length; ++s) {
                        u = u.concat(o(r.topoArcs, r.arcs[s]))
                    }
                    h("#svgDefs").append(a.svg.getPath(u).attr({
                        id: t
                    }))
                }
                for (var s = 1; s < 5; ++s) {
                    var q = a.svg.createElement("use").attr({
                        href: "#" + t
                    }).addClass("shadow").css({
                        "fill-opacity": 1 - 0.2 * s
                    }).data("offset", [s, s]);
                    r.container.append(q)
                }
                r.container.append(a.svg.createElement("use").attr({
                    href: "#" + t
                }).addClass("boundary"))
            },
            transform: function (r, q) {
                r.canvas.attr({
                    transform: "translate(" + q.translate.join() + ")"
                }).css({
                    "stroke-width": 1 / q.scale
                });
                h(".scaled", r.canvas).attr({
                    transform: "scale(" + q.scale + ")"
                });
                h(".dot", r.canvas).each(function (t, s) {
                    s = h(s);
                    s.attr({
                        r: s.data("r") / q.scale
                    })
                });
                h(".nameRect", r.canvas).each(function (y, x) {
                    var z = h(x),
                        t = h("text", z),
                        w = t[0].getBBox(),
                        v = h("rect", z),
                        s = z.data("pos"),
                        u = {
                            width: w.width + 20,
                            height: w.height + 8
                        };
                    v.attr({
                        x: s.x * q.scale - u.width / 2,
                        y: s.y * q.scale - u.height / 2,
                        rx: 2,
                        ry: 2,
                        width: u.width,
                        height: u.height
                    });
                    t.attr({
                        x: s.x * q.scale,
                        y: s.y * q.scale
                    })
                });
                h(".boundary", r.canvas).css({
                    "stroke-width": 2 / q.scale
                });
                h(".shadow", r.canvas).each(function (s, u) {
                    u = h(u);
                    var t = u.data("offset");
                    u.attr({
                        transform: "translate(" + t[0] / q.scale + "," + t[1] / q.scale + ")"
                    })
                })
            }
        },
        vml: {
            init: function () {
                document.namespaces.add("v", "urn:schemas-microsoft-com:vml");
                document.createStyleSheet().addRule(".v", "behavior:url(#default#VML)")
            },
            createElement: function (q) {
                return h(document.createElement("<v:" + q + ' class="v" />')).attr({
                    coordsize: i + "," + i
                }).css({
                    position: "absolute",
                    display: "block",
                    top: 0,
                    left: 0
                })
            },
            getCanvas: function (q) {
                return a.vml.getGroup().addClass("mapCanvas").appendTo(q)
            },
            getGroup: function (q) {
                return h("<div></div>").addClass(q || "group").css({
                    position: "absolute",
                    top: 0,
                    left: 0
                })
            },
            getPath: function (s) {
                var q = [];
                for (var r = 0; r < s.length; ++r) {
                    var t = h.map(s[r], function (u) {
                        return [[Math.round(u[0]), Math.round(u[1])]]
                    });
                    q.push("m" + t.shift().join() + " l" + t.join())
                }
                return a.vml.createElement("shape").attr({
                    path: q.join(" ")
                })
            },
            getDot: function (q) {
                return ""
            },
            getLnk: function (r, q) {
                return h('<a href="' + r + '" target="_blank"></a>')
            },
            getNameBox: function (q) {
                return h('<a href="javascript:;" class="nameRect"><span class="name">' + q + "</span></a>")
            },
            drawShadows: function (q) { },
            transform: function (r, q) {
                h(".v:not(shadow)").css({
                    width: i * q.scale,
                    height: i * q.scale,
                    left: q.translate[0],
                    top: q.translate[1]
                });
                h(".nameRect", r.canvas).each(function (w, v) {
                    var x = h(v),
                        t = x.data("pos"),
                        u = x.text().length,
                        s = x[0].getBoundingClientRect();
                    x.css({
                        left: t.x * q.scale + q.translate[0] - (s.right - s.left) / 2,
                        top: t.y * q.scale + q.translate[1] - (s.bottom - s.top) / 2
                    })
                })
            }
        }
    };
    var n, d, i;
    if (!document.implementation.hasFeature("http://www.w3.org/TR/SVG11/feature#BasicStructure", "1.1")) {
        n = a.vml;
        d = true;
        i = 10000
    } else {
        n = a.svg;
        i = 100
    }
    n.init();

    function k(q, t, r) {
        var s = [].concat(b([t.bbox[0], t.bbox[1]]), b([t.bbox[2], t.bbox[3]]));
        this.leftTop = [s[0], s[3]];
        this.rightBottom = [s[2], s[1]];
        this.mapData = t;
        this.layerId = q || m();
        this.topoArcs = j(t.arcs, t.transform, r || this.leftTop)
    }
    k.prototype = {
        constructor: k,
        getLayerNode: function () {
            this.layerNode = n.getGroup("layer noInteraction");
            this.boundaryGroup = n.getGroup("group scaled").appendTo(this.layerNode);
            this.polygonGroup = n.getGroup("group scaled").appendTo(this.layerNode);
            this.nameGroup = n.getGroup().appendTo(this.layerNode);
            var q = this;
            h.each(this.mapData.objects, function (u, w) {
                var s = [],
                    y = [];
                if (w.type == "Polygon") {
                    var r = o(q.topoArcs, w.arcs);
                    y = y.concat(r);
                    s = s.concat(r[0])
                } else {
                    if (w.type == "MultiPolygon") {
                        for (var t = 0; t < w.arcs.length; ++t) {
                            var r = o(q.topoArcs, w.arcs[t]);
                            y = y.concat(r);
                            s = s.concat(r[0])
                        }
                    }
                }
                //var v = n.getPath(y).addClass("area").data({
                var v = n.getPath(y).addClass("area_wsf").data({
                    areaKey: u,
                    areaData: w
                }).appendTo(q.polygonGroup);
                q.mapData.objects[u].centroid = q.mapData.objects[u].type == "Point" ? s : l(s);
                var x = n.getNameBox(w.properties.name).data({
                    areaKey: u,
                    areaData: w,
                    pos: {
                        x: q.mapData.objects[u].centroid[0] + (w.properties.nameOffset ? w.properties.nameOffset[0] * i : 0),
                        y: q.mapData.objects[u].centroid[1] + (w.properties.nameOffset ? w.properties.nameOffset[1] * i : 0)
                    }
                });
                q.nameGroup.append(x);
                v.data("nameBox", x)
            });
            this.mapData.properties && this.mapData.properties.boundaryArcs && !d && n.drawShadows({
                id: this.layerId,
                topoArcs: this.topoArcs,
                arcs: this.mapData.properties.boundaryArcs,
                container: this.boundaryGroup
            });
            return this.layerNode
        },
        remove: function () {
            var q = h(this.layerNode);
            if (d) {
                q.remove()
            } else {
                q.animate({
                    opacity: 0
                }, function () {
                    q.remove()
                })
            }
        }
    };

    function f(q, s, r) {
        var u = Math.min(q.width / this.oriSize.width, q.height / this.oriSize.height) * (r || 1),
            t = {
                left: (q.width - this.oriSize.width * u) / 2,
                top: (q.height - this.oriSize.height * u) / 2
            };
        this.boundingRect = {
            left: q.left + t.left,
            top: q.top + t.top,
            width: this.oriSize.width * u,
            height: this.oriSize.height * u
        };
        return this.transform = {
            scale: u,
            translate: [this.boundingRect.left - (this.leftTop[0] - s[0]) * u, this.boundingRect.top - (this.leftTop[1] - s[1]) * u]
        }
    }
    function c(q) {
        this.mapBox = h(q);
        this.canvas = n.getCanvas(this.mapBox);
        this.mapSize = {
            width: this.mapBox.width(),
            height: this.mapBox.height()
        };
        this.geoLayers = []
    }
    c.prototype = {
        constructor: c,
        buildMap: function (r) {
            r.initialAni = h.type(r.initialAni) == "boolean" ? r.initialAni : true;
            var u = new k("mapLayer_main", r.geoData);
            this.geoLayers.push(u);
            this.canvas.append(u.getLayerNode());
            u.layerNode.css({
                opacity: 0
            });
            var t = r.padding && r.padding.length ? [r.padding[0], isNaN(r.padding[1]) ? r.padding[0] : r.padding[1], isNaN(r.padding[2]) ? r.padding[0] : r.padding[2], isNaN(r.padding[3]) ? (isNaN(r.padding[1]) ? r.padding[0] : r.padding[1]) : r.padding[3]] : [0, 0, 0, 0];
            this.viewRect = {
                left: t[3],
                top: t[0],
                width: this.mapSize.width - t[1] - t[3],
                height: this.mapSize.height - t[0] - t[2]
            };
            u.oriSize = {
                width: u.rightBottom[0] - u.leftTop[0],
                height: u.rightBottom[1] - u.leftTop[1]
            };
            var s = f.call(u, this.viewRect, u.leftTop, 10);
            f.call(u, this.viewRect, u.leftTop);
            this.baseTransform = (d || !r.initialAni) ? u.transform : s;
            if (r.initialFocusedAreaId) {
                var q = this;
                this.transform({
                    targetTransform: s,
                    duration: 0,
                    onAniComplete: function () {
                        u.layerNode.css({
                            opacity: 1
                        });
                        q.transformTo(h.extend({}, r, {
                            areaId: r.initialFocusedAreaId,
                            duration: !r.initialAni ? 0 : 800
                        }))
                    }
                })
            } else {
                this.transform(h.extend({}, r, {
                    targetTransform: u.transform,
                    duration: !r.initialAni ? 0 : 800,
                    onAniStep: function (v) {
                        u.layerNode.css({
                            opacity: v
                        });
                        r.onAniStep && r.onAniStep()
                    }
                }))
            }
            return u
        },
        addSublayer: function (q) {
            var s = new k("mapLayer_" + (q.layerId || m()), q.layerData, this.geoLayers[0].leftTop);
            this.geoLayers.push(s);
            h(".layer:not(.sublayer)").addClass("masked");
            var r = s.layerNode = s.getLayerNode().addClass("sublayer").css({
                opacity: 0
            }).data("layerObj", s);
            this.canvas.append(r);
            s.oriSize = {
                width: s.rightBottom[0] - s.leftTop[0],
                height: s.rightBottom[1] - s.leftTop[1]
            };
            f.call(s, this.viewRect, this.geoLayers[0].leftTop, q.extScale);
            this.transform(h.extend({}, q, {
                targetTransform: s.transform,
                onAniStep: function (t) {
                    r.css({
                        opacity: t
                    });
                    q.onAniStep && q.onAniStep()
                }
            }));
            return s
        },
        transform: function (r) {
            var t = h.extend({}, this.baseTransform, r.targetTransform);
            var q = this,
                u = h.extend({}, this.baseTransform),
                s = this.baseTransform ? {
                    scale: t.scale - this.baseTransform.scale,
                    left: t.translate[0] - this.baseTransform.translate[0],
                    top: t.translate[1] - this.baseTransform.translate[1]
                } : t;
            this.baseTransform = t;
            h({
                n: 0
            }).animate({
                n: 1
            }, {
                duration: isNaN(r.duration) ? 400 : r.duration,
                start: function () {
                    r.onAniStart && r.onAniStart()
                },
                step: function (v) {
                    n.transform(q, {
                        scale: u.scale + s.scale * v,
                        translate: [u.translate[0] + s.left * v, u.translate[1] + s.top * v]
                    });
                    r.onAniStep && r.onAniStep(v)
                },
                complete: function () {
                    r.onAniComplete && r.onAniComplete()
                }
            })
        },
        removeAllSublayers: function (r, q) {
            this.geoLayers.splice(1);
            h(".sublayer", this.mapBox).each(function (t, s) {
                h(s).data("layerObj").remove()
            });
            if (r) {
                f.call(this.geoLayers[0], this.viewRect, this.geoLayers[0].leftTop);
                this.transform({
                    targetTransform: this.geoLayers[0].transform,
                    onAniComplete: q
                });
                h(".masked").removeClass("masked")
            }
        },
        addLnk: function (q) {
            h(q.node).wrap(n.getLnk(q.lnk))
        },
        resetPos: function (q) {
            var u = this.geoLayers[0],
                t = this.geoLayers[1];
            this.mapSize = {
                width: this.mapBox.width(),
                height: this.mapBox.height()
            };
            var s = q.padding && q.padding.length ? [q.padding[0], isNaN(q.padding[1]) ? q.padding[0] : q.padding[1], isNaN(q.padding[2]) ? q.padding[0] : q.padding[2], isNaN(q.padding[3]) ? (isNaN(q.padding[1]) ? q.padding[0] : q.padding[1]) : q.padding[3]] : [0, 0, 0, 0];
            this.viewRect = {
                left: s[3],
                top: s[0],
                width: this.mapSize.width - s[1] - s[3],
                height: this.mapSize.height - s[0] - s[2]
            };
            var r;
            if (t) {
                r = f.call(t, this.viewRect, u.leftTop)
            } else {
                r = f.call(u, this.viewRect, u.leftTop)
            }
            this.transform({
                targetTransform: r
            })
        },
        transformTo: function (q) {
            var s = this.geoLayers[0],
                r = h.extend({}, s.mapData, {
                    objects: {
                        "0": s.mapData.objects[q.areaId]
                    },
                    bbox: s.mapData.objects[q.areaId].properties.bbox,
                    properties: {
                        boundaryArcs: s.mapData.objects[q.areaId].type == "Polygon" ? [s.mapData.objects[q.areaId].arcs] : s.mapData.objects[q.areaId].arcs
                    }
                });
            this.removeAllSublayers();
            this.addSublayer(h.extend({}, q, {
                layerId: "mainLayerArea_" + q.areaId,
                layerData: r
            }))
        }
    };
    c.getAreaCentroid = function (v, u, w, q) {
        var t = j(u, w, q);
        var r = [];
        if (v.type == "Point") {
            r = b(v.coordinates)
        } else {
            if (v.type == "Polygon") {
                r = r.concat(o(t, v.arcs)[0])
            } else {
                if (v.type == "MultiPolygon") {
                    for (var s = 0; s < v.arcs.length; ++s) {
                        r = r.concat(o(t, v.arcs[s])[0])
                    }
                }
            }
        }
        return v.type == "Point" ? r : l(r)
    };
    g = c;
    return g
}, [mapCityDic+"static/common/ui/jquery/jquery.js"]);