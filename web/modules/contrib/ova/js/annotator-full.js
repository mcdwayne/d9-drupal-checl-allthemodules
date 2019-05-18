/*
 ** Annotator v1.2.7
 ** https://github.com/okfn/annotator/
 **
 ** Copyright 2012 Aron Carroll, Rufus Pollock, and Nick Stenning.
 ** Dual licensed under the MIT and GPLv3 licenses.
 ** https://github.com/okfn/annotator/blob/master/LICENSE
 **
 ** Built at: 2013-06-27 21:49:35Z
 */
(function(){
((function() {
    var a, b, c, d, e, f, g, h, i, j, k, l, m, n, o, p, q, r, s, t, u, v, w, x, y, z, A, B, C, D, E, F = [].slice,
        G = {}.hasOwnProperty,
        H = function(a, b) {
            function d() {
                this.constructor = a
            }
            for (var c in b) G.call(b, c) && (a[c] = b[c]);
            return d.prototype = b.prototype, a.prototype = new d, a.__super__ = b.prototype, a
        },
        I = function(a, b) {
            return function() {
                return a.apply(b, arguments)
            }
        },
        J = [].indexOf || function(a) {
            for (var b = 0, c = this.length; b < c; b++)
                if (b in this && this[b] === a) return b;
            return -1
        };
    r = function(b) {
        var c;
        return c = this.map(function() {
            var c, d, e, f;
            e = "", c = this;
            while ((c != null ? c.nodeType : void 0) === Node.ELEMENT_NODE && c !== b) f = c.tagName.replace(":", "\\:"), d = a(c.parentNode).children(f).index(c) + 1, d = "[" + d + "]", e = "/" + c.tagName.toLowerCase() + d + e, c = c.parentNode;
            return e
        }), c.get()
    }, s = function(a) {
        var b, c, d, e;
        return b = function(a) {
            var b, c;
            return b = n(a), c = o(a), "" + b + "[" + c + "]"
        }, e = a, c = function(a) {
            var c;
            c = "";
            while (a !== e) {
                if (a == null) throw new Error("Called getPathTo on a node which was not a descendant of @rootNode. " + e);
                c = b(a) + "/" + c, a = a.parentNode
            }
            return c = "/" + c, c = c.replace(/\/$/, ""), c
        }, d = this.map(function() {
            var a;
            return a = c(this), a
        }), d.get()
    }, j = function(a, b, c) {
        var d, e, f, g, h, i;
        if (!a.hasChildNodes()) throw new Error("XPath error: node has no children!");
        e = a.childNodes, f = 0;
        for (h = 0, i = e.length; h < i; h++) {
            d = e[h], g = n(d);
            if (g === b) {
                f += 1;
                if (f === c) return d
            }
        }
        throw new Error("XPath error: wanted child not found.")
    }, n = function(a) {
        var b;
        b = a.nodeName.toLowerCase();
        switch (b) {
            case "#text":
                return "text()";
            case "#comment":
                return "comment()";
            case "#cdata-section":
                return "cdata-section()";
            default:
                return b
        }
    }, o = function(a) {
        var b, c;
        b = 0, c = a;
        while (c) c.nodeName === a.nodeName && b++, c = c.previousSibling;
        return b
    }, p = null, typeof Gettext != "undefined" && Gettext !== null ? (u = new Gettext({
        domain: "annotator"
    }), p = function(a) {
        return u.gettext(a)
    }) : p = function(a) {
        return a
    }, E = function(a) {
        return p(a)
    }, (typeof jQuery != "undefined" && jQuery !== null ? (z = jQuery.fn) != null ? z.jquery : void 0 : void 0) || console.error(E("Annotator requires jQuery: have you included lib/vendor/jquery.js?")), JSON && JSON.parse && JSON.stringify || console.error(E("Annotator requires a JSON implementation: have you included lib/vendor/json2.js?")), a = jQuery, f = {}, f.flatten = function(b) {
        var c;
        return c = function(b) {
            var d, e, f, g;
            e = [];
            for (f = 0, g = b.length; f < g; f++) d = b[f], e = e.concat(d && a.isArray(d) ? c(d) : d);
            return e
        }, c(b)
    }, f.getTextNodes = function(a) {
        var b;
        return b = function(a) {
            var c;
            if (a && a.nodeType !== Node.TEXT_NODE) {
                c = [];
                if (a.nodeType !== Node.COMMENT_NODE) {
                    a = a.lastChild;
                    while (a) c.push(b(a)), a = a.previousSibling
                }
                return c.reverse()
            }
            return a
        }, a.map(function() {
            return f.flatten(b(this))
        })
    }, f.xpathFromNode = function(a, b) {
        var c, d;
        try {
            d = r.call(a, b)
        } catch (e) {
            c = e, console.log("jQuery-based XPath construction failed! Falling back to manual."), d = s.call(a, b)
        }
        return d
    }, f.nodeFromXPath = function(a, b) {
        var c, d, e, f, g, h, i, k;
        g = a.substring(1).split("/"), e = b;
        for (h = 0, i = g.length; h < i; h++) f = g[h], k = f.split("["), d = k[0], c = k[1], c = c != null ? parseInt((c != null ? c.split("]") : void 0)[0]) : 1, e = j(e, d.toLowerCase(), c);
        return e
    }, f.escape = function(a) {
        return a.replace(/&(?!\w+;)/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(/"/g, "&quot;")
    }, f.uuid = function() {
        var a;
        return a = 0,
            function() {
                return a++
            }
    }(), f.getGlobal = function() {
        return function() {
            return this
        }()
    }, f.maxZIndex = function(b) {
        var c, d;
        return c = function() {
            var c, e, f;
            f = [];
            for (c = 0, e = b.length; c < e; c++) d = b[c], a(d).css("position") === "static" ? f.push(-1) : f.push(parseInt(a(d).css("z-index"), 10) || -1);
            return f
        }(), Math.max.apply(Math, c)
    }, f.mousePosition = function(b, c) {
        var d;
        return d = a(c).position(), {
            top: b.pageY - d.top,
            left: b.pageX - d.left
        }
    }, f.preventEventDefault = function(a) {
        return a != null ? typeof a.preventDefault == "function" ? a.preventDefault() : void 0 : void 0
    }, l = ["log", "debug", "info", "warn", "exception", "assert", "dir", "dirxml", "trace", "group", "groupEnd", "groupCollapsed", "time", "timeEnd", "profile", "profileEnd", "count", "clear", "table", "error", "notifyFirebug", "firebug", "userObjects"];
    if (typeof console != "undefined" && console !== null) {
        console.group == null && (console.group = function(a) {
            return console.log("GROUP: ", a)
        }), console.groupCollapsed == null && (console.groupCollapsed = console.group);
        for (v = 0, x = l.length; v < x; v++) k = l[v], console[k] == null && (console[k] = function() {
            return console.log(E("Not implemented:") + (" console." + name))
        })
    } else {
        this.console = {};
        for (w = 0, y = l.length; w < y; w++) k = l[w], this.console[k] = function() {};
        this.console.error = function() {
            var a;
            return a = 1 <= arguments.length ? F.call(arguments, 0) : [], alert("ERROR: " + a.join(", "))
        }, this.console.warn = function() {
            var a;
            return a = 1 <= arguments.length ? F.call(arguments, 0) : [], alert("WARNING: " + a.join(", "))
        }
    }
    c = function() {
        function b(b, c) {
            this.options = a.extend(!0, {}, this.options, c), this.element = a(b), this.on = this.subscribe, this.addEvents()
        }
        return b.prototype.events = {}, b.prototype.options = {}, b.prototype.element = null, b.prototype.addEvents = function() {
            var a, b, c, d, e, f, g, h;
            f = this.events, h = [];
            for (c in f) b = f[c], g = c.split(" "), d = 2 <= g.length ? F.call(g, 0, e = g.length - 1) : (e = 0, []), a = g[e++], h.push(this.addEvent(d.join(" "), a, b));
            return h
        }, b.prototype.addEvent = function(b, c, d) {
            var e, f, g = this;
            return e = function() {
                return g[d].apply(g, arguments)
            }, f = typeof b == "string" && b.replace(/\s+/g, "") === "", f && (b = this.element), typeof b == "string" ? this.element.delegate(b, c, e) : this.isCustomEvent(c) ? this.subscribe(c, e) : a(b).bind(c, e), this
        }, b.prototype.isCustomEvent = function(c) {
            return c = c.split(".")[0], a.inArray(c, b.natives) === -1
        }, b.prototype.publish = function() {
            return this.element.triggerHandler.apply(this.element, arguments), this
        }, b.prototype.subscribe = function(b, c) {
            var d;
            return d = function() {
                return c.apply(this, [].slice.call(arguments, 1))
            }, d.guid = c.guid = a.guid += 1, this.element.bind(b, d), this
        }, b.prototype.unsubscribe = function() {
            return this.element.unbind.apply(this.element, arguments), this
        }, b
    }(), c.natives = function() {
        var a, b, c;
        return b = function() {
            var b, d;
            b = jQuery.event.special, d = [];
            for (a in b) {
                if (!G.call(b, a)) continue;
                c = b[a], d.push(a)
            }
            return d
        }(), "blur focus focusin focusout load resize scroll unload click dblclick\nmousedown mouseup mousemove mouseover mouseout mouseenter mouseleave\nchange select submit keydown keypress keyup error".split(/[^a-z]+/).concat(b)
    }(), e = {}, e.sniff = function(a) {
        return a.commonAncestorContainer != null ? new e.BrowserRange(a) : typeof a.start == "string" ? new e.SerializedRange(a) : a.start && typeof a.start == "object" ? new e.NormalizedRange(a) : (console.error(E("Could not sniff range type")), !1)
    }, e.nodeFromXPath = function(b, c) {
        var d, e, g, h, i;
        return c == null && (c = document), e = function(a, b) {
            var d;
            b == null && (b = null);
            try {
                return document.evaluate("." + a, c, b, XPathResult.FIRST_ORDERED_NODE_TYPE, null).singleNodeValue
            } catch (e) {
                return d = e, console.log("XPath evaluation failed."), console.log("Trying fallback..."), f.nodeFromXPath(a, c)
            }
        }, a.isXMLDoc(document.documentElement) ? (d = document.createNSResolver(document.ownerDocument === null ? document.documentElement : document.ownerDocument.documentElement), h = e(b, d), h || (b = function() {
            var a, c, d, e;
            d = b.split("/"), e = [];
            for (a = 0, c = d.length; a < c; a++) i = d[a], i && i.indexOf(":") === -1 ? e.push(i.replace(/^([a-z]+)/, "xhtml:$1")) : e.push(i);
            return e
        }().join("/"), g = document.lookupNamespaceURI(null), d = function(a) {
            return a === "xhtml" ? g : document.documentElement.getAttribute("xmlns:" + a)
        }, h = e(b, d)), h) : e(b)
    }, e.RangeError = function(a) {
        function b(a, c, d) {
            this.type = a, this.message = c, this.parent = d != null ? d : null, b.__super__.constructor.call(this, this.message)
        }
        return H(b, a), b
    }(Error), e.BrowserRange = function() {
        function a(a) {
            this.commonAncestorContainer = a.commonAncestorContainer, this.startContainer = a.startContainer, this.startOffset = a.startOffset, this.endContainer = a.endContainer, this.endOffset = a.endOffset
        }
        return a.prototype.normalize = function(a) {
            var b, c, d, f, g, h, i, j, k;
            if (this.tainted) return console.error(E("You may only call normalize() once on a BrowserRange!")), !1;
            this.tainted = !0, h = {}, d = {}, k = ["start", "end"];
            for (i = 0, j = k.length; i < j; i++) {
                g = k[i], c = this[g + "Container"], f = this[g + "Offset"];
                if (c.nodeType === Node.ELEMENT_NODE) {
                    b = c.childNodes[f], c = b || c.childNodes[f - 1], c.nodeType === Node.ELEMENT_NODE && !c.firstChild && (b = null, c = c.previousSibling);
                    while (c.nodeType !== Node.TEXT_NODE) c = c.firstChild;
                    f = b ? 0 : c.nodeValue.length
                }
                h[g] = c, h[g + "Offset"] = f
            }
            d.start = h.startOffset > 0 ? h.start.splitText(h.startOffset) : h.start, h.start === h.end ? (h.endOffset - h.startOffset < d.start.nodeValue.length && d.start.splitText(h.endOffset - h.startOffset), d.end = d.start) : (h.endOffset < h.end.nodeValue.length && h.end.splitText(h.endOffset), d.end = h.end), d.commonAncestor = this.commonAncestorContainer;
            while (d.commonAncestor.nodeType !== Node.ELEMENT_NODE) d.commonAncestor = d.commonAncestor.parentNode;
            return new e.NormalizedRange(d)
        }, a.prototype.serialize = function(a, b) {
            return this.normalize(a).serialize(a, b)
        }, a
    }(), e.NormalizedRange = function() {
        function b(a) {
            this.commonAncestor = a.commonAncestor, this.start = a.start, this.end = a.end
        }
        return b.prototype.normalize = function(a) {
            return this
        }, b.prototype.limit = function(b) {
            var c, d, e, f, g, h;
            c = a.grep(this.textNodes(), function(c) {
                return c.parentNode === b || a.contains(b, c.parentNode)
            });
            if (!c.length) return null;
            this.start = c[0], this.end = c[c.length - 1], e = a(this.start).parents(), h = a(this.end).parents();
            for (f = 0, g = h.length; f < g; f++) {
                d = h[f];
                if (e.index(d) !== -1) {
                    this.commonAncestor = d;
                    break
                }
            }
            return this
        }, b.prototype.serialize = function(b, c) {
            var d, g, h;
            return g = function(d, e) {
                var g, h, i, j, k, l, m, n;
                c ? j = a(d).parents(":not(" + c + ")").eq(0) : j = a(d).parent(), l = f.xpathFromNode(j, b)[0], k = f.getTextNodes(j), h = k.slice(0, k.index(d)), i = 0;
                for (m = 0, n = h.length; m < n; m++) g = h[m], i += g.nodeValue.length;
                return e ? [l, i + d.nodeValue.length] : [l, i]
            }, h = g(this.start), d = g(this.end, !0), new e.SerializedRange({
                start: h[0],
                end: d[0],
                startOffset: h[1],
                endOffset: d[1]
            })
        }, b.prototype.text = function() {
            var a;
            return function() {
                var b, c, d, e;
                d = this.textNodes(), e = [];
                for (b = 0, c = d.length; b < c; b++) a = d[b], e.push(a.nodeValue);
                return e
            }.call(this).join("")
        },b.prototype.textNodes = function() {
            var b, c, d, e;
            return d = f.getTextNodes(a(this.commonAncestor)), e = [d.index(this.start), d.index(this.end)], c = e[0], b = e[1], a.makeArray(d.slice(c, +b + 1 || 9e9))
        }, b.prototype.toRange = function() {
            var a;
            return a = document.createRange(), a.setStartBefore(this.start), a.setEndAfter(this.end), a
        }, b
    }(), e.SerializedRange = function() {
        function b(a) {
            this.start = a.start, this.startOffset = a.startOffset, this.end = a.end, this.endOffset = a.endOffset
        }
        return b.prototype.normalize = function(b) {
            var c, d, g, h, i, j, k, l, m, n, o, p, q;
            j = {}, p = ["start", "end"];
            for (l = 0, n = p.length; l < n; l++) {
                i = p[l];
                try {
                    h = e.nodeFromXPath(this[i], b)
                } catch (r) {
                    throw d = r, new e.RangeError(i, "Error while finding " + i + " node: " + this[i] + ": " + d, d)
                }
                if (!h) throw new e.RangeError(i, "Couldn't find " + i + " node: " + this[i]);
                g = 0, q = f.getTextNodes(a(h));
                for (m = 0, o = q.length; m < o; m++) {
                    k = q[m];
                    if (g + k.nodeValue.length >= this[i + "Offset"]) {
                        j[i + "Container"] = k, j[i + "Offset"] = this[i + "Offset"] - g;
                        break
                    }
                    g += k.nodeValue.length
                }
                if (j[i + "Offset"] == null) throw new e.RangeError("" + i + "offset", "Couldn't find offset " + this[i + "Offset"] + " in element " + this[i])
            }
            return c = document.compareDocumentPosition == null ? function(a, b) {
                return a.contains(b)
            } : function(a, b) {
                return a.compareDocumentPosition(b) & 16
            }, a(j.startContainer).parents().each(function() {
                if (c(this, j.endContainer)) return j.commonAncestorContainer = this, !1
            }), (new e.BrowserRange(j)).normalize(b)
        }, b.prototype.serialize = function(a, b) {
            return this.normalize(a).serialize(a, b)
        }, b.prototype.toObject = function() {
            return {
                start: this.start,
                startOffset: this.startOffset,
                end: this.end,
                endOffset: this.endOffset
            }
        }, b
    }(), t = this.Annotator, b = function(b) {
        function c(b, d) {
            this.onDeleteAnnotation = I(this.onDeleteAnnotation, this), this.onEditAnnotation = I(this.onEditAnnotation, this), this.onAdderClick = I(this.onAdderClick, this), this.onAdderMousedown = I(this.onAdderMousedown, this), this.onHighlightMouseover = I(this.onHighlightMouseover, this), this.checkForEndSelection = I(this.checkForEndSelection, this), this.checkForStartSelection = I(this.checkForStartSelection, this), this.clearViewerHideTimer = I(this.clearViewerHideTimer, this), this.startViewerHideTimer = I(this.startViewerHideTimer, this), this.showViewer = I(this.showViewer, this), this.onEditorSubmit = I(this.onEditorSubmit, this), this.onEditorHide = I(this.onEditorHide, this), this.showEditor = I(this.showEditor, this), c.__super__.constructor.apply(this, arguments), this.plugins = {};
            if (!c.supported()) return this;
            this.options.readOnly || this._setupDocumentEvents(), this._setupWrapper()._setupViewer()._setupEditor(), this._setupDynamicStyle(), this.adder = a(this.html.adder).appendTo(this.wrapper).hide()
        }
        return H(c, b), c.prototype.events = {
            ".annotator-adder button click": "onAdderClick",
            ".annotator-adder button mousedown": "onAdderMousedown",
            ".annotator-hl mouseover": "onHighlightMouseover",
            ".annotator-hl mouseout": "startViewerHideTimer"
        }, c.prototype.html = {
            adder: '<div class="annotator-adder"><button>' + E("Annotate") + "</button></div>",
            wrapper: '<div class="annotator-wrapper"></div>'
        }, c.prototype.options = {
            readOnly: !1
        }, c.prototype.plugins = {}, c.prototype.editor = null, c.prototype.viewer = null, c.prototype.selectedRanges = null, c.prototype.mouseIsDown = !1, c.prototype.ignoreMouseup = !1, c.prototype.viewerHideTimer = null, c.prototype._setupWrapper = function() {
            return this.wrapper = a(this.html.wrapper), this.element.find("script").remove(), this.element.wrapInner(this.wrapper), this.wrapper = this.element.find(".annotator-wrapper"), this
        }, c.prototype._setupViewer = function() {
            var b = this;
            return this.viewer = new c.Viewer({
                readOnly: this.options.readOnly
            }), this.viewer.hide().on("edit", this.onEditAnnotation).on("delete", this.onDeleteAnnotation).addField({
                load: function(c, d) {
                    return d.text ? a(c).html(f.escape(d.text)) : a(c).html("<i>" + E("No Comment") + "</i>"), b.publish("annotationViewerTextField", [c, d])
                }
            }).element.appendTo(this.wrapper).bind({
                mouseover: this.clearViewerHideTimer,
                mouseout: this.startViewerHideTimer
            }), this
        }, c.prototype._setupEditor = function() {
            return this.editor = new c.Editor, this.editor.hide().on("hide", this.onEditorHide).on("save", this.onEditorSubmit).addField({
                type: "textarea",
                label: E("Comments") + "â€¦",
                load: function(b, c) {
                    return a(b).find("textarea").val(c.text || "")
                },
                submit: function(b, c) {
                    // c.text returns user entered comment value
                    return c.text = a(b).find("textarea").val()
                    
                }
            }), this.editor.element.appendTo(this.wrapper), this
        }, c.prototype._setupDocumentEvents = function() {
            return a(document).bind({
                mouseup: this.checkForEndSelection,
                mousedown: this.checkForStartSelection
            }), this
        }, c.prototype._setupDynamicStyle = function() {
            var b, c, d, e;
            return d = a("#annotator-dynamic-style"), d.length || (d = a('<style id="annotator-dynamic-style"></style>').appendTo(document.head)), c = "*" + function() {
                var a, b, c, d;
                c = ["adder", "outer", "notice", "filter"], d = [];
                for (a = 0, b = c.length; a < b; a++) e = c[a], d.push(":not(.annotator-" + e + ")");
                return d
            }().join(""), b = f.maxZIndex(a(document.body).find(c)), b = Math.max(b, 1e3), d.text([".annotator-adder, .annotator-outer, .annotator-notice {", "  z-index: " + (b + 20) + ";", "}", ".annotator-filter {", "  z-index: " + (b + 10) + ";", "}"].join("\n")), this
        }, c.prototype.getSelectedRanges = function() {
            var b, c, d, g, h, i, j, k, l;
            j = f.getGlobal().getSelection(), h = [], i = [], j.isCollapsed || (h = function() {
                var a, f, h;
                h = [];
                for (c = a = 0, f = j.rangeCount; 0 <= f ? a < f : a > f; c = 0 <= f ? ++a : --a) g = j.getRangeAt(c), b = new e.BrowserRange(g), d = b.normalize().limit(this.wrapper[0]), d === null && i.push(g), h.push(d);
                return h
            }.call(this), j.removeAllRanges());
            for (k = 0, l = i.length; k < l; k++) g = i[k], j.addRange(g);
            return a.grep(h, function(a) {
                return a && j.addRange(a.toRange()), a
            })
        }, c.prototype.createAnnotation = function() {
            var a;
            return a = {}, this.publish("beforeAnnotationCreated", [a]), a
        }, c.prototype.setupAnnotation = function(b) {
            var c, d, f, g, h, i, j, k, l, m;
            h = this.wrapper[0], b.ranges || (b.ranges = this.selectedRanges), f = [], m = b.ranges;
            for (i = 0, k = m.length; i < k; i++) {
                g = m[i];
                try {
                    f.push(e.sniff(g).normalize(h))
                } catch (n) {
                    c = n;
                    if (!(c instanceof e.RangeError)) throw c;
                    this.publish("rangeNormalizeFail", [b, g, c])
                }
            }
            b.quote = [], b.ranges = [], b.highlights = [];
            for (j = 0, l = f.length; j < l; j++) d = f[j], b.quote.push(a.trim(d.text())), b.ranges.push(d.serialize(this.wrapper[0], ".annotator-hl")), a.merge(b.highlights, this.highlightRange(d));
            return b.quote = b.quote.join(" / "), a(b.highlights).data("annotation", b), b
        }, c.prototype.updateAnnotation = function(a) {
            return this.publish("beforeAnnotationUpdated", [a]), this.publish("annotationUpdated", [a]), a
        }, c.prototype.deleteAnnotation = function(b) {
            var c, d, e, f, g;
            if (b.highlights != null) {
                g = b.highlights;
                for (e = 0, f = g.length; e < f; e++) {
                    d = g[e];
                    if (d.parentNode == null) continue;
                    c = d.childNodes[0], a(d).replaceWith(d.childNodes)
                }
            }
            return this.publish("annotationDeleted", [b]), b
        }, c.prototype.loadAnnotations = function(a) {
            var b, c, d = this;
            return a == null && (a = []), c = function(a) {
                var e, f, g, h;
                a == null && (a = []), f = a.splice(0, 10);
                for (g = 0, h = f.length; g < h; g++) e = f[g], d.setupAnnotation(e);
                return a.length > 0 ? setTimeout(function() {
                    return c(a)
                }, 10) : d.publish("annotationsLoaded", [b])
            }, b = a.slice(), a.length && c(a), this
        }, c.prototype.dumpAnnotations = function() {
            return this.plugins.Store ? this.plugins.Store.dumpAnnotations() : (console.warn(E("Can't dump annotations without Store plugin.")), !1)
        }, c.prototype.highlightRange = function(b, c) {
            var d, e, f, g, h, i, j;
            c == null && (c = "annotator-hl"), f = /^\s*$/, d = a("<span class='" + c + "'></span>"), i = b.textNodes(), j = [];
            for (g = 0, h = i.length; g < h; g++) e = i[g], f.test(e.nodeValue) || j.push(a(e).wrapAll(d).parent().show()[0]);
            return j
        }, c.prototype.highlightRanges = function(b, c) {
            var d, e, f, g;
            c == null && (c = "annotator-hl"), d = [];
            for (f = 0, g = b.length; f < g; f++) e = b[f], a.merge(d, this.highlightRange(e, c));
            return d
        }, c.prototype.addPlugin = function(a, b) {
            var d, e;
            return this.plugins[a] ? console.error(E("You cannot have more than one instance of any plugin.")) : (d = c.Plugin[a], typeof d == "function" ? (this.plugins[a] = new d(this.element[0], b), this.plugins[a].annotator = this, typeof(e = this.plugins[a]).pluginInit == "function" && e.pluginInit()) : console.error(E("Could not load ") + a + E(" plugin. Have you included the appropriate <script> tag?"))), this
        }, c.prototype.showEditor = function(a, b) {
            return this.editor.element.css(b), this.editor.load(a), this.publish("annotationEditorShown", [this.editor, a]), this
        }, c.prototype.onEditorHide = function() {
            return this.publish("annotationEditorHidden", [this.editor]), this.ignoreMouseup = !1
        }, c.prototype.onEditorSubmit = function(a) {
            return this.publish("annotationEditorSubmit", [this.editor, a])
        }, c.prototype.showViewer = function(a, b) {
            return this.viewer.element.css(b), this.viewer.load(a), this.publish("annotationViewerShown", [this.viewer, a])
        }, c.prototype.startViewerHideTimer = function() {
            if (!this.viewerHideTimer) return this.viewerHideTimer = setTimeout(this.viewer.hide, 250)
        }, c.prototype.clearViewerHideTimer = function() {
            return clearTimeout(this.viewerHideTimer), this.viewerHideTimer = !1
        }, c.prototype.checkForStartSelection = function(a) {
            return (!a || !this.isAnnotator(a.target)) && this.startViewerHideTimer(), this.mouseIsDown = !0
        }, c.prototype.checkForEndSelection = function(b) {
            var c, d, e, g, h;
            this.mouseIsDown = !1;
            if (this.ignoreMouseup) return;
            this.selectedRanges = this.getSelectedRanges(), h = this.selectedRanges;
            for (e = 0, g = h.length; e < g; e++) {
                d = h[e], c = d.commonAncestor, a(c).hasClass("annotator-hl") && (c = a(c).parents("[class!=annotator-hl]")[0]);
                if (this.isAnnotator(c)) return
            }
            return b && this.selectedRanges.length ? this.adder.css(f.mousePosition(b, this.wrapper[0])).show() : this.adder.hide()
        }, c.prototype.isAnnotator = function(b) {
            return !!a(b).parents().andSelf().filter("[class^=annotator-]").not(this.wrapper).length
        }, c.prototype.onHighlightMouseover = function(b) {
            var c;
            return this.clearViewerHideTimer(), this.mouseIsDown || this.viewer.isShown() ? !1 : (c = a(b.target).parents(".annotator-hl").andSelf().map(function() {
                return a(this).data("annotation")
            }), this.showViewer(a.makeArray(c), f.mousePosition(b, this.wrapper[0])))
        }, c.prototype.onAdderMousedown = function(a) {
            return a != null && a.preventDefault(), this.ignoreMouseup = !0
        }, c.prototype.onAdderClick = function(b) {
            var c, d, e, f, g, h = this;
            return b != null && b.preventDefault(), f = this.adder.position(), this.adder.hide(), c = this.setupAnnotation(this.createAnnotation()), a(c.highlights).addClass("annotator-hl-temporary"), g = function() {
                return e(), a(c.highlights).removeClass("annotator-hl-temporary"), h.publish("annotationCreated", [c])
            }, d = function() {
                return e(), h.deleteAnnotation(c)
            }, e = function() {
                return h.unsubscribe("annotationEditorHidden", d), h.unsubscribe("annotationEditorSubmit", g)
            }, this.subscribe("annotationEditorHidden", d), this.subscribe("annotationEditorSubmit", g), this.showEditor(c, f)
        }, c.prototype.onEditAnnotation = function(a) {
            var b, c, d, e = this;
            return c = this.viewer.element.position(), d = function() {
                return b(), e.updateAnnotation(a)
            }, b = function() {
                return e.unsubscribe("annotationEditorHidden", b), e.unsubscribe("annotationEditorSubmit", d)
            }, this.subscribe("annotationEditorHidden", b), this.subscribe("annotationEditorSubmit", d), this.viewer.hide(), this.showEditor(a, c)
        }, c.prototype.onDeleteAnnotation = function(a) {
            return this.viewer.hide(), this.deleteAnnotation(a)
        }, c
    }(c), b.Plugin = function(a) {
        function b(a, c) {
            b.__super__.constructor.apply(this, arguments)
        }
        return H(b, a), b.prototype.pluginInit = function() {}, b
    }(c), m = f.getGlobal(), ((A = m.document) != null ? A.evaluate : void 0) == null && a.getScript("http://assets.annotateit.org/vendor/xpath.min.js"), m.getSelection == null && a.getScript("http://assets.annotateit.org/vendor/ierange.min.js"), m.JSON == null && a.getScript("http://assets.annotateit.org/vendor/json2.min.js"), m.Node == null && (m.Node = {
        ELEMENT_NODE: 1,
        ATTRIBUTE_NODE: 2,
        TEXT_NODE: 3,
        CDATA_SECTION_NODE: 4,
        ENTITY_REFERENCE_NODE: 5,
        ENTITY_NODE: 6,
        PROCESSING_INSTRUCTION_NODE: 7,
        COMMENT_NODE: 8,
        DOCUMENT_NODE: 9,
        DOCUMENT_TYPE_NODE: 10,
        DOCUMENT_FRAGMENT_NODE: 11,
        NOTATION_NODE: 12
    }), b.$ = a, b.Delegator = c, b.Range = e, b.Util = f, b._t = E, b.supported = function() {
        return function() {
            return !!this.getSelection
        }()
    }, b.noConflict = function() {
        return f.getGlobal().Annotator = t, this
    }, a.fn.annotator = function(c) {
        var d;
        return d = Array.prototype.slice.call(arguments, 1), this.each(function() {
            var e;
            return e = a.data(this, "annotator"), e ? c && e[c].apply(e, d) : (e = new b(this, c), a.data(this, "annotator", e))
        })
    }, this.Annotator = b, b.Widget = function(c) {
        function d(c, e) {
            d.__super__.constructor.apply(this, arguments), this.classes = a.extend({}, b.Widget.prototype.classes, this.classes)
        }
        return H(d, c), d.prototype.classes = {
            hide: "annotator-hide",
            invert: {
                x: "annotator-invert-x",
                y: "annotator-invert-y"
            }
        }, d.prototype.checkOrientation = function() {
            var c, d, e, f, g;
            return this.resetOrientation(), g = a(b.Util.getGlobal()), f = this.element.children(":first"), d = f.offset(), e = {
                top: g.scrollTop(),
                right: g.width() + g.scrollLeft()
            }, c = {
                top: d.top,
                right: d.left + f.width()
            }, c.top - e.top < 0 && this.invertY(), c.right - e.right > 0 && this.invertX(), this
        }, d.prototype.resetOrientation = function() {
            return this.element.removeClass(this.classes.invert.x).removeClass(this.classes.invert.y), this
        }, d.prototype.invertX = function() {
            return this.element.addClass(this.classes.invert.x), this
        }, d.prototype.invertY = function() {
            return this.element.addClass(this.classes.invert.y), this
        }, d.prototype.isInvertedY = function() {
            return this.element.hasClass(this.classes.invert.y)
        }, d.prototype.isInvertedX = function() {
            return this.element.hasClass(this.classes.invert.x)
        }, d
    }(c), b.Editor = function(c) {
        function d(b) {
            this.onCancelButtonMouseover = I(this.onCancelButtonMouseover, this), this.processKeypress = I(this.processKeypress, this), this.submit = I(this.submit, this), this.load = I(this.load, this), this.hide = I(this.hide, this), this.show = I(this.show, this), d.__super__.constructor.call(this, a(this.html)[0], b), this.fields = [], this.annotation = {}
        }
        return H(d, c), d.prototype.events = {
            "form submit": "submit",
            ".annotator-save click": "submit",
            ".annotator-cancel click": "hide",
            ".annotator-cancel mouseover": "onCancelButtonMouseover",
            "textarea keydown": "processKeypress"
        }, d.prototype.classes = {
            hide: "annotator-hide",
            focus: "annotator-focus"
        }, d.prototype.html = '<div class="annotator-outer annotator-editor">\n  <form class="annotator-widget">\n    <ul class="annotator-listing"></ul>\n    <div class="annotator-controls">\n      <a href="#cancel" class="annotator-cancel">' + E("Cancel") + '</a>\n<a href="#save" class="annotator-save annotator-focus">' + E("Save") + "</a>\n    </div>\n  </form>\n</div>", d.prototype.options = {}, d.prototype.show = function(a) {
            return b.Util.preventEventDefault(a), this.element.removeClass(this.classes.hide), this.element.find(".annotator-save").addClass(this.classes.focus), this.checkOrientation(), this.element.find(":input:first").focus(), this.setupDraggables(), this.publish("show")
        }, d.prototype.hide = function(a) {
            return b.Util.preventEventDefault(a), this.element.addClass(this.classes.hide), this.publish("hide")
        }, d.prototype.load = function(a) {
            var b, c, d, e;
            this.annotation = a, this.publish("load", [this.annotation]), e = this.fields;
            for (c = 0, d = e.length; c < d; c++) b = e[c], b.load(b.element, this.annotation);
            return this.show()
        }, d.prototype.submit = function(a) {
            var c, d, e, f;
            b.Util.preventEventDefault(a), f = this.fields;
            for (d = 0, e = f.length; d < e; d++) c = f[d], c.submit(c.element, this.annotation);
            return this.publish("save", [this.annotation]), this.hide()
        }, d.prototype.addField = function(c) {
            var d, e, f;
            e = a.extend({
                id: "annotator-field-" + b.Util.uuid(),
                type: "input",
                label: "",
                load: function() {},
                submit: function() {}
            }, c), f = null, d = a('<li class="annotator-item" />'), e.element = d[0];
            switch (e.type) {
                case "textarea":
                    f = a("<textarea />");
                    break;
                case "input":
                case "checkbox":
                    f = a("<input />");
                    break;
                case "select":
                    f = a("<select />")
            }
            return d.append(f), f.attr({
                id: e.id,
                placeholder: e.label
            }), e.type === "checkbox" && (f[0].type = "checkbox", d.addClass("annotator-checkbox"), d.append(a("<label />", {
                "for": e.id,
                html: e.label
            }))), this.element.find("ul:first").append(d), this.fields.push(e), e.element
        }, d.prototype.checkOrientation = function() {
            var a, b;
            return d.__super__.checkOrientation.apply(this, arguments), b = this.element.find("ul"), a = this.element.find(".annotator-controls"), this.element.hasClass(this.classes.invert.y) ? a.insertBefore(b) : a.is(":first-child") && a.insertAfter(b), this
        }, d.prototype.processKeypress = function(a) {
            if (a.keyCode === 27) return this.hide();
            if (a.keyCode === 13 && !a.shiftKey) return this.submit()
        }, d.prototype.onCancelButtonMouseover = function() {
            return this.element.find("." + this.classes.focus).removeClass(this.classes.focus)
        }, d.prototype.setupDraggables = function() {
            var b, c, d, e, f, g, h, i, j, k, l, m = this;
            return this.element.find(".annotator-resize").remove(), this.element.hasClass(this.classes.invert.y) ? d = this.element.find(".annotator-item:last") : d = this.element.find(".annotator-item:first"), d && a('<span class="annotator-resize"></span>').appendTo(d), f = null, b = this.classes, e = this.element, k = null, j = e.find(".annotator-resize"), c = e.find(".annotator-controls"), l = !1, g = function(b) {
                if (b.target === this) return f = {
                    element: this,
                    top: b.pageY,
                    left: b.pageX
                }, k = e.find("textarea:first"), a(window).bind({
                    "mouseup.annotator-editor-resize": i,
                    "mousemove.annotator-editor-resize": h
                }), b.preventDefault()
            }, i = function() {
                return f = null, a(window).unbind(".annotator-editor-resize")
            }, h = function(a) {
                var d, g, h, i, m;
                if (f && l === !1) return d = {
                    top: a.pageY - f.top,
                    left: a.pageX - f.left
                }, f.element === j[0] ? (i = k.outerHeight(), m = k.outerWidth(), g = e.hasClass(b.invert.x) ? -1 : 1, h = e.hasClass(b.invert.y) ? 1 : -1, k.height(i + d.top * h), k.width(m + d.left * g), k.outerHeight() !== i && (f.top = a.pageY), k.outerWidth() !== m && (f.left = a.pageX)) : f.element === c[0] && (e.css({
                    top: parseInt(e.css("top"), 10) + d.top,
                    left: parseInt(e.css("left"), 10) + d.left
                }), f.top = a.pageY, f.left = a.pageX), l = !0, setTimeout(function() {
                    return l = !1
                }, 1e3 / 60)
            }, j.bind("mousedown", g), c.bind("mousedown", g)
        }, d
    }(b.Widget), b.Viewer = function(c) {
        function e(b) {
            this.onDeleteClick = I(this.onDeleteClick, this), this.onEditClick = I(this.onEditClick, this), this.load = I(this.load, this), this.hide = I(this.hide, this), this.show = I(this.show, this), e.__super__.constructor.call(this, a(this.html.element)[0], b), this.item = a(this.html.item)[0], this.fields = [], this.annotations = []
        }
        return H(e, c), e.prototype.events = {
            ".annotator-edit click": "onEditClick",
            ".annotator-delete click": "onDeleteClick"
        }, e.prototype.classes = {
            hide: "annotator-hide",
            showControls: "annotator-visible"
        }, e.prototype.html = {
            element: '<div class="annotator-outer annotator-viewer">\n  <ul class="annotator-widget annotator-listing"></ul>\n</div>',
            item: '<li class="annotator-annotation annotator-item">\n  <span class="annotator-controls">\n    <a href="#" title="View as webpage" class="annotator-link">View as webpage</a>\n    <button title="Edit" class="annotator-edit">Edit</button>\n    <button title="Delete" class="annotator-delete">Delete</button>\n  </span>\n</li>'
        }, e.prototype.options = {
            readOnly: !1
        }, e.prototype.show = function(a) {
            var c, d = this;
            return b.Util.preventEventDefault(a), c = this.element.find(".annotator-controls").addClass(this.classes.showControls), setTimeout(function() {
                return c.removeClass(d.classes.showControls)
            }, 500), this.element.removeClass(this.classes.hide), this.checkOrientation().publish("show")
        }, e.prototype.isShown = function() {
            return !this.element.hasClass(this.classes.hide)
        }, e.prototype.hide = function(a) {
            return b.Util.preventEventDefault(a), this.element.addClass(this.classes.hide), this.publish("hide")
        }, e.prototype.load = function(b) {
            var c, e, f, g, h, i, j, k, l, m, n, o, p, q, r, s, t;
            this.annotations = b || [], n = this.element.find("ul:first").empty(), s = this.annotations;
            for (o = 0, q = s.length; o < q; o++) {
                c = s[o], k = a(this.item).clone().appendTo(n).data("annotation", c), f = k.find(".annotator-controls"), l = f.find(".annotator-link"), h = f.find(".annotator-edit"), g = f.find(".annotator-delete"), m = (new d(c.links || [])).get("alternate", {
                    type: "text/html"
                }), m.length === 0 || m[0].href == null ? l.remove() : l.attr("href", m[0].href), this.options.readOnly ? (h.remove(), g.remove()) : e = {
                    showEdit: function() {
                        return h.removeAttr("disabled")
                    },
                    hideEdit: function() {
                        return h.attr("disabled", "disabled")
                    },
                    showDelete: function() {
                        return g.removeAttr("disabled")
                    },
                    hideDelete: function() {
                        return g.attr("disabled", "disabled")
                    }
                }, t = this.fields;
                for (p = 0, r = t.length; p < r; p++) j = t[p], i = a(j.element).clone().appendTo(k)[0], j.load(i, c, e)
            }
            return this.publish("load", [this.annotations]), this.show()
        }, e.prototype.addField = function(b) {
            var c;
            return c = a.extend({
                load: function() {}
            }, b), c.element = a("<div />")[0], this.fields.push(c), c.element, this
        }, e.prototype.onEditClick = function(a) {
            return this.onButtonClick(a, "edit")
        }, e.prototype.onDeleteClick = function(a) {
            return this.onButtonClick(a, "delete")
        }, e.prototype.onButtonClick = function(b, c) {
            var d;
            return d = a(b.target).parents(".annotator-annotation"), this.publish(c, [d.data("annotation")])
        }, e
    }(b.Widget), d = function() {
        function b(a) {
            this.data = a
        }
        return b.prototype.get = function(b, c) {
            var d, e, f, g, h, i, j, k, l;
            c == null && (c = {}), c = a.extend({}, c, {
                rel: b
            }), f = function() {
                var a;
                a = [];
                for (e in c) {
                    if (!G.call(c, e)) continue;
                    h = c[e], a.push(e)
                }
                return a
            }(), k = this.data, l = [];
            for (i = 0, j = k.length; i < j; i++) {
                d = k[i], g = f.reduce(function(a, b) {
                    return a && d[b] === c[b]
                }, !0);
                if (!g) continue;
                l.push(d)
            }
            return l
        }, b
    }(), b = b || {}, b.Notification = function(c) {
        function d(b) {
            this.hide = I(this.hide, this), this.show = I(this.show, this), d.__super__.constructor.call(this, a(this.options.html).appendTo(document.body)[0], b)
        }
        return H(d, c), d.prototype.events = {
            click: "hide"
        }, d.prototype.options = {
            html: "<div class='annotator-notice'></div>",
            classes: {
                show: "annotator-notice-show",
                info: "annotator-notice-info",
                success: "annotator-notice-success",
                error: "annotator-notice-error"
            }
        }, d.prototype.show = function(c, d) {
            return d == null && (d = b.Notification.INFO), a(this.element).addClass(this.options.classes.show).addClass(this.options.classes[d]).html(f.escape(c || "")), setTimeout(this.hide, 5e3), this
        }, d.prototype.hide = function() {
            return a(this.element).removeClass(this.options.classes.show), this
        }, d
    }(c), b.Notification.INFO = "show", b.Notification.SUCCESS = "success", b.Notification.ERROR = "error", a(function() {
        var a;
        return a = new b.Notification, b.showNotification = a.show, b.hideNotification = a.hide
    }), b.Plugin.Unsupported = function(c) {
        function d() {
            return B = d.__super__.constructor.apply(this, arguments), B
        }
        return H(d, c), d.prototype.options = {
            message: b._t("Sorry your current browser does not support the Annotator")
        }, d.prototype.pluginInit = function() {
            var c = this;
            if (!b.supported()) return a(function() {
                b.showNotification(c.options.message);
                if (window.XMLHttpRequest === void 0 && ActiveXObject !== void 0) return a("html").addClass("ie6")
            })
        }, d
    }(b.Plugin), i = function(a) {
        var b, c, d, e, f, g;
        return e = "([0-9]{4})(-([0-9]{2})(-([0-9]{2})(T([0-9]{2}):([0-9]{2})(:([0-9]{2})(\\.([0-9]+))?)?(Z|(([-+])([0-9]{2}):([0-9]{2})))?)?)?)?", b = a.match(new RegExp(e)), d = 0, c = new Date(b[1], 0, 1), b[3] && c.setMonth(b[3] - 1), b[5] && c.setDate(b[5]), b[7] && c.setHours(b[7]), b[8] && c.setMinutes(b[8]), b[10] && c.setSeconds(b[10]), b[12] && c.setMilliseconds(Number("0." + b[12]) * 1e3), b[14] && (d = Number(b[16]) * 60 + Number(b[17]), d *= (g = b[15] === "-") != null ? g : {
            1: -1
        }), d -= c.getTimezoneOffset(), f = Number(c) + d * 60 * 1e3, c.setTime(Number(f)), c
    }, g = function(a) {
        var b, c, d, e, f, g, h, i, j, k, l, m, n;
        if (typeof atob != "undefined" && atob !== null) return atob(a);
        c = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=", j = 0, b = 0, e = "", n = [];
        if (!a) return a;
        a += "";
        while (j < a.length) f = c.indexOf(a.charAt(j++)), g = c.indexOf(a.charAt(j++)), h = c.indexOf(a.charAt(j++)), i = c.indexOf(a.charAt(j++)), d = f << 18 | g << 12 | h << 6 | i, k = d >> 16 & 255, l = d >> 8 & 255, m = d & 255, h === 64 ? n[b++] = String.fromCharCode(k) : i === 64 ? n[b++] = String.fromCharCode(k, l) : n[b++] = String.fromCharCode(k, l, m);
        return n.join("")
    }, h = function(a) {
        var b, c, d, e;
        c = a.length % 4;
        if (c !== 0)
            for (b = d = 0, e = 4 - c; 0 <= e ? d < e : d > e; b = 0 <= e ? ++d : --d) a += "=";
        return a = a.replace(/-/g, "+"), a = a.replace(/_/g, "/"), g(a)
    }, q = function(a) {
        var b, c, d, e;
        return e = a.split("."), b = e[0], c = e[1], d = e[2], JSON.parse(h(c))
    }, b.Plugin.Auth = function(c) {
        function d(a, b) {
            d.__super__.constructor.apply(this, arguments), this.waitingForToken = [], this.options.token ? this.setToken(this.options.token) : this.requestToken()
        }
        return H(d, c), d.prototype.options = {
            token: null,
            tokenUrl: "/auth/token",
            autoFetch: !0
        }, d.prototype.requestToken = function() {
            var c = this;
            return this.requestInProgress = !0, a.ajax({
                url: this.options.tokenUrl,
                dataType: "text",
                xhrFields: {
                    withCredentials: !0
                }
            }).done(function(a, b, d) {
                alert(c.setToken(a));
                return c.setToken(a)
            }).fail(function(a, c, d) {
                var e;
                return e = b._t("Couldn't get auth token:"), console.error("" + e + " " + d, a), b.showNotification("" + e + " " + a.responseText, b.Notification.ERROR)
            }).always(function() {
                return c.requestInProgress = !1
            })
        }, d.prototype.setToken = function(a) {
            var c, d = this;
            this.token = a, this._unsafeToken = q(a);
            if (this.haveValidToken()) {
                this.options.autoFetch && (this.refreshTimeout = setTimeout(function() {
                    return d.requestToken()
                }, (this.timeToExpiry() - 2) * 1e3)), this.updateHeaders(), c = [];
                while (this.waitingForToken.length > 0) c.push(this.waitingForToken.pop()(this._unsafeToken));
                return c
            }
            console.warn(b._t("Didn't get a valid token."));
            if (this.options.autoFetch) return console.warn(b._t("Getting a new token in 10s.")), setTimeout(function() {
                return d.requestToken()
            }, 1e4)
        }, d.prototype.haveValidToken = function() {
            var a;
            return a = this._unsafeToken && this._unsafeToken.issuedAt && this._unsafeToken.ttl && this._unsafeToken.consumerKey, a && this.timeToExpiry() > 0 ? !0 : !1
        }, d.prototype.timeToExpiry = function() {
            var a, b, c, d;
            return c = (new Date).getTime() / 1e3, b = i(this._unsafeToken.issuedAt).getTime() / 1e3, a = b + this._unsafeToken.ttl, d = a - c, d > 0 ? d : 0
        }, d.prototype.updateHeaders = function() {
            var b;
            return b = this.element.data("annotator:headers"), this.element.data("annotator:headers", a.extend(b, {
                "x-annotator-auth-token": this.token
            }))
        }, d.prototype.withToken = function(a) {
            if (a == null) return;
            if (this.haveValidToken()) return a(this._unsafeToken);
            this.waitingForToken.push(a);
            if (!this.requestInProgress) return this.requestToken()
        }, d
    }(b.Plugin), b.Plugin.Store = function(c) {
        function d(a, b) {
            this._onError = I(this._onError, this), this._onLoadAnnotationsFromSearch = I(this._onLoadAnnotationsFromSearch, this), this._onLoadAnnotations = I(this._onLoadAnnotations, this), this._getAnnotations = I(this._getAnnotations, this), d.__super__.constructor.apply(this, arguments), this.annotations = []
        }
        return H(d, c), d.prototype.events = {
            annotationCreated: "annotationCreated",
            annotationDeleted: "annotationDeleted",
            annotationUpdated: "annotationUpdated"
        }, d.prototype.options = {
            annotationData: {},
            emulateHTTP: !1,
            loadFromSearch: !1,
            prefix: "/store",
            urls: {
                create: "/annotations",
                read: "/annotations/:id",
                update: "/annotations/:id",
                destroy: "/annotations/:id",
                search: "/search"
            }
        }, d.prototype.pluginInit = function() {
            if (!b.supported()) return;
            return this.annotator.plugins.Auth ? this.annotator.plugins.Auth.withToken(this._getAnnotations) : this._getAnnotations()
        }, d.prototype._getAnnotations = function() {
            return this.options.loadFromSearch ? this.loadAnnotationsFromSearch(this.options.loadFromSearch) : this.loadAnnotations()
        }, d.prototype.annotationCreated = function(a) {
            var c = this;
            return J.call(this.annotations, a) < 0 ? (this.registerAnnotation(a), this._apiRequest("create", a, function(d) {
                return d.id == null && console.warn(b._t("Warning: No ID returned from server for annotation "), a), c.updateAnnotation(a, d)
            })) : this.updateAnnotation(a, {})
        }, d.prototype.annotationUpdated = function(a) {
            var b = this;
            if (J.call(this.annotations, a) >= 0) return this._apiRequest("update", a, function(c) {
                return b.updateAnnotation(a, c)
            })
        }, d.prototype.annotationDeleted = function(a) {
            var b = this;
            if (J.call(this.annotations, a) >= 0) return this._apiRequest("destroy", a, function() {
                return b.unregisterAnnotation(a)
            })
        }, d.prototype.registerAnnotation = function(a) {
            return this.annotations.push(a)
        }, d.prototype.unregisterAnnotation = function(a) {
            return this.annotations.splice(this.annotations.indexOf(a), 1)
        }, d.prototype.updateAnnotation = function(c, d) {
            return J.call(this.annotations, c) < 0 ? console.error(b._t("Trying to update unregistered annotation!")) : a.extend(c, d), a(c.highlights).data("annotation", c)
        }, d.prototype.loadAnnotations = function() {
            return this._apiRequest("read", null, this._onLoadAnnotations)
        }, d.prototype._onLoadAnnotations = function(a) {
            var b, c, d, e, f, g, h, i, j;
            a == null && (a = []), d = {}, j = this.annotations;
            for (f = 0, h = j.length; f < h; f++) b = j[f], d[b.id] = b;
            e = [];
            for (g = 0, i = a.length; g < i; g++) b = a[g], d[b.id] ? (c = d[b.id], this.updateAnnotation(c, b)) : e.push(b);
            return this.annotations = this.annotations.concat(e), this.annotator.loadAnnotations(e.slice())
        }, d.prototype.loadAnnotationsFromSearch = function(a) {
            return this._apiRequest("search", a, this._onLoadAnnotationsFromSearch)
        }, d.prototype._onLoadAnnotationsFromSearch = function(a) {
            return a == null && (a = {}), this._onLoadAnnotations(a.rows || [])
        }, d.prototype.dumpAnnotations = function() {
            var a, b, c, d, e;
            d = this.annotations, e = [];
            for (b = 0, c = d.length; b < c; b++) a = d[b], e.push(JSON.parse(this._dataFor(a)));
            return e
        }, d.prototype._apiRequest = function(b, c, d) {
            var e, f, g, h;
            // variable e returns the current id of comment
            return e = c && c.id, h = this._urlFor(b, e), f = this._apiRequestOptions(b, c, d), g = a.ajax(h, f), g._id = e, g._action = b, g
        }, d.prototype._apiRequestOptions = function(b, c, d) {
            var e, f, g;
            return f = this._methodFor(b), g = {
                type: f,
                headers: this.element.data("annotator:headers"),
                dataType: "json",
                success: d || function() {},
                error: this._onError
            }, this.options.emulateHTTP && (f === "PUT" || f === "DELETE") && (g.headers = a.extend(g.headers, {
                "X-HTTP-Method-Override": f
            }), g.type = "POST"), b === "search" ? (g = a.extend(g, {
                data: c
            }), g) : (e = c && this._dataFor(c), this.options.emulateJSON ? (g.data = {
                json: e
            }, this.options.emulateHTTP && (g.data._method = f), g) : (g = a.extend(g, {
                data: e,
                contentType: "application/json; charset=utf-8"
            }), g))
        }, d.prototype._urlFor = function(a, b) {
            var c;
            //if(this.options.urls[a] == '/annotations') {
            //    
            //return c = this.options.prefix != null ? 'http://nvlid8.unimity.com/annotations/apis' : "", c += this.options.urls[a], c = c.replace(/\/:id/, b != null ? "/" + b : ""), c = c.replace(/:id/, b != null ? b : ""), c
            //}
            return c = this.options.prefix != null ? this.options.prefix : "", c += this.options.urls[a], c = c.replace(/\/:id/, b != null ? "/" + b : ""), c = c.replace(/:id/, b != null ? b : ""), c
        }, d.prototype._methodFor = function(a) {
            var b;
            return b = {
                create: "POST",
                read: "GET",
                update: "PUT",
                destroy: "DELETE",
                search: "GET"
            }, b[a]
        }, d.prototype._dataFor = function(b) {
            var c, d;
            return d = b.highlights, delete b.highlights, a.extend(b, this.options.annotationData), c = JSON.stringify(b), d && (b.highlights = d), c
        }, d.prototype._onError = function(a) {
            var c, d;
            c = a._action, d = b._t("Sorry we could not ") + c + b._t(" this annotation"), a._action === "search" ? d = b._t("Sorry we could not search the store for annotations") : a._action === "read" && !a._id && (d = b._t("Sorry we could not ") + c + b._t(" the annotations from the store"));
            switch (a.status) {
                case 401:
                    d = b._t("Sorry you are not allowed to ") + c + b._t(" this annotation");
                    break;
                case 404:
                    d = b._t("Sorry we could not connect to the annotations store");
                    break;
                case 500:
                    d = b._t("Sorry something went wrong with the annotation store")
            }
            return b.showNotification(d, b.Notification.ERROR), console.error(b._t("API request failed:") + (" '" + a.status + "'"))
        }, d
    }(b.Plugin), b.Plugin.Permissions = function(c) {
        function d(a, b) {
            this._setAuthFromToken = I(this._setAuthFromToken, this), this.updateViewer = I(this.updateViewer, this), this.updateAnnotationPermissions = I(this.updateAnnotationPermissions, this), this.updatePermissionsField = I(this.updatePermissionsField, this), this.addFieldsToAnnotation = I(this.addFieldsToAnnotation, this), d.__super__.constructor.apply(this, arguments), this.options.user && (this.setUser(this.options.user), delete this.options.user)
        }
        return H(d, c), d.prototype.events = {
            beforeAnnotationCreated: "addFieldsToAnnotation"
        }, d.prototype.options = {
            showViewPermissionsCheckbox: !0,
            showEditPermissionsCheckbox: !0,
            userId: function(a) {
                return a
            },
            userString: function(a) {
                return a
            },
            userAuthorize: function(a, b, c) {
                var d, e, f, g;
                if (b.permissions) {
                    e = b.permissions[a] || [];
                    if (e.length === 0) return !0;
                    for (f = 0, g = e.length; f < g; f++) {
                        d = e[f];
                        if (this.userId(c) === d) return !0
                    }
                    return !1
                }
                return b.user ? c ? this.userId(c) === this.userId(b.user) : !1 : !0
            },
            user: "",
            permissions: {
                read: [],
                update: [],
                "delete": [],
                admin: []
            }
        }, d.prototype.pluginInit = function() {
            var a, c, d = this;
            if (!b.supported()) return;
            c = this, a = function(a, b) {
                return function(d, e) {
                    return c[a].call(c, b, d, e)
                }
            }, !this.user && this.annotator.plugins.Auth && this.annotator.plugins.Auth.withToken(this._setAuthFromToken), this.options.showViewPermissionsCheckbox === !0 && this.annotator.editor.addField({
                type: "checkbox",
                label: b._t("Allow anyone to <strong>view</strong> this annotation"),
                load: a("updatePermissionsField", "read"),
                submit: a("updateAnnotationPermissions", "read")
            }), this.options.showEditPermissionsCheckbox === !0 && this.annotator.editor.addField({
                type: "checkbox",
                label: b._t("Allow anyone to <strong>edit</strong> this annotation"),
                load: a("updatePermissionsField", "update"),
                submit: a("updateAnnotationPermissions", "update")
            }), this.annotator.viewer.addField({
                load: this.updateViewer
            });
            if (this.annotator.plugins.Filter) return this.annotator.plugins.Filter.addFilter({
                label: b._t("User"),
                property: "user",
                isFiltered: function(a, b) {
                    var c, e, f, g;
                    b = d.options.userString(b);
                    if (!a || !b) return !1;
                    g = a.split(/\s*/);
                    for (e = 0, f = g.length; e < f; e++) {
                        c = g[e];
                        if (b.indexOf(c) === -1) return !1
                    }
                    return !0
                }
            })
        }, d.prototype.setUser = function(a) {
            return this.user = a
        }, d.prototype.addFieldsToAnnotation = function(a) {
            if (a) {
                a.permissions = this.options.permissions;
                if (this.user) return a.user = this.user
            }
        }, d.prototype.authorize = function(a, b, c) {
            return c === void 0 && (c = this.user), this.options.userAuthorize ? this.options.userAuthorize.call(this.options, a, b, c) : !0
        }, d.prototype.updatePermissionsField = function(b, c, d) {
            var e;
            return c = a(c).show(), e = c.find("input").removeAttr("disabled"), this.authorize("admin", d) || c.hide(), this.authorize(b, d || {}, null) ? e.attr("checked", "checked") : e.removeAttr("checked")
        }, d.prototype.updateAnnotationPermissions = function(b, c, d) {
            var e;
            return d.permissions || (d.permissions = this.options.permissions), e = b + "-permissions", a(c).find("input").is(":checked") ? d.permissions[b] = [] : d.permissions[b] = [this.user]
        }, d.prototype.updateViewer = function(c, d, e) {
            var f, g;
            c = a(c), g = this.options.userString(d.user), d.user && g && typeof g == "string" ? (f = b.Util.escape(this.options.userString(d.user)), c.html(f).addClass("annotator-user")) : c.remove();
            if (e) {
                this.authorize("update", d) || e.hideEdit();
                if (!this.authorize("delete", d)) return e.hideDelete()
            }
        }, d.prototype._setAuthFromToken = function(a) {
            return this.setUser(a.userId)
        }, d
    }(b.Plugin), b.Plugin.AnnotateItPermissions = function(b) {
        function c() {
            return this._setAuthFromToken = I(this._setAuthFromToken, this), this.updateAnnotationPermissions = I(this.updateAnnotationPermissions, this), this.updatePermissionsField = I(this.updatePermissionsField, this), this.addFieldsToAnnotation = I(this.addFieldsToAnnotation, this), C = c.__super__.constructor.apply(this, arguments), C
        }
        return H(c, b), c.prototype.options = {
            showViewPermissionsCheckbox: !0,
            showEditPermissionsCheckbox: !0,
            groups: {
                world: "group:__world__",
                authenticated: "group:__authenticated__",
                consumer: "group:__consumer__"
            },
            userId: function(a) {
                return a.userId
            },
            userString: function(a) {
                return a.userId
            },
            userAuthorize: function(a, b, c) {
                var d, e, f, g, h, i;
                return e = b.permissions || {}, d = e[a] || [], (f = this.groups.world, J.call(d, f) >= 0) ? !0 : c != null && c.userId != null && c.consumerKey != null ? c.userId === b.user && c.consumerKey === b.consumer ? !0 : (g = this.groups.authenticated, J.call(d, g) >= 0) ? !0 : c.consumerKey === b.consumer && (h = this.groups.consumer, J.call(d, h) >= 0) ? !0 : c.consumerKey === b.consumer && (i = c.userId, J.call(d, i) >= 0) ? !0 : c.consumerKey === b.consumer && c.admin ? !0 : !1 : !1
            },
            permissions: {
                read: ["group:__world__"],
                update: [],
                "delete": [],
                admin: []
            }
        }, c.prototype.addFieldsToAnnotation = function(a) {
            if (a) {
                a.permissions = this.options.permissions;
                if (this.user) return a.user = this.user.userId, a.consumer = this.user.consumerKey
            }
        }, c.prototype.updatePermissionsField = function(b, c, d) {
            var e;
            return c = a(c).show(), e = c.find("input").removeAttr("disabled"), this.authorize("admin", d) || c.hide(), this.user && this.authorize(b, d || {}, {
                userId: "__nonexistentuser__",
                consumerKey: this.user.consumerKey
            }) ? e.attr("checked", "checked") : e.removeAttr("checked")
        }, c.prototype.updateAnnotationPermissions = function(b, c, d) {
            var e;
            return d.permissions || (d.permissions = this.options.permissions), e = b + "-permissions", a(c).find("input").is(":checked") ? d.permissions[b] = [b === "read" ? this.options.groups.world : this.options.groups.consumer] : d.permissions[b] = []
        }, c.prototype._setAuthFromToken = function(a) {
            return this.setUser(a)
        }, c
    }(b.Plugin.Permissions), b.Plugin.Filter = function(c) {
        function d(b, c) {
            this._onPreviousClick = I(this._onPreviousClick, this), this._onNextClick = I(this._onNextClick, this), this._onFilterKeyup = I(this._onFilterKeyup, this), this._onFilterBlur = I(this._onFilterBlur, this), this._onFilterFocus = I(this._onFilterFocus, this), this.updateHighlights = I(this.updateHighlights, this);
            var e;
            b = a(this.html.element).appendTo((c != null ? c.appendTo : void 0) || this.options.appendTo), d.__super__.constructor.call(this, b, c), (e = this.options).filters || (e.filters = []), this.filter = a(this.html.filter), this.filters = [], this.current = 0
        }
        return H(d, c), d.prototype.events = {
            ".annotator-filter-property input focus": "_onFilterFocus",
            ".annotator-filter-property input blur": "_onFilterBlur",
            ".annotator-filter-property input keyup": "_onFilterKeyup",
            ".annotator-filter-previous click": "_onPreviousClick",
            ".annotator-filter-next click": "_onNextClick",
            ".annotator-filter-clear click": "_onClearClick"
        }, d.prototype.classes = {
            active: "annotator-filter-active",
            hl: {
                hide: "annotator-hl-filtered",
                active: "annotator-hl-active"
            }
        }, d.prototype.html = {
            element: '<div class="annotator-filter">\n  <strong>' + b._t("Navigate:") + '</strong>\n<span class="annotator-filter-navigation">\n  <button class="annotator-filter-previous">' + b._t("Previous") + '</button>\n<button class="annotator-filter-next">' + b._t("Next") + "</button>\n</span>\n<strong>" + b._t("Filter by:") + "</strong>\n</div>",
            filter: '<span class="annotator-filter-property">\n  <label></label>\n  <input/>\n  <button class="annotator-filter-clear">' + b._t("Clear") + "</button>\n</span>"
        }, d.prototype.options = {
            appendTo: "body",
            filters: [],
            addAnnotationFilter: !0,
            isFiltered: function(a, b) {
                var c, d, e, f;
                if (!a || !b) return !1;
                f = a.split(/\s*/);
                for (d = 0, e = f.length; d < e; d++) {
                    c = f[d];
                    if (b.indexOf(c) === -1) return !1
                }
                return !0
            }
        }, d.prototype.pluginInit = function() {
            var a, c, d, e;
            e = this.options.filters;
            for (c = 0, d = e.length; c < d; c++) a = e[c], this.addFilter(a);
            this.updateHighlights(), this._setupListeners()._insertSpacer();
            if (this.options.addAnnotationFilter === !0) return this.addFilter({
                label: b._t("Annotation"),
                property: "text"
            })
        }, d.prototype._insertSpacer = function() {
            var b, c;
            return c = a("html"), b = parseInt(c.css("padding-top"), 10) || 0, c.css("padding-top", b + this.element.outerHeight()), this
        }, d.prototype._setupListeners = function() {
            var a, b, c, d;
            b = ["annotationsLoaded", "annotationCreated", "annotationUpdated", "annotationDeleted"];
            for (c = 0, d = b.length; c < d; c++) a = b[c], this.annotator.subscribe(a, this.updateHighlights);
            return this
        }, d.prototype.addFilter = function(c) {
            var d, e;
            e = a.extend({
                label: "",
                property: "",
                isFiltered: this.options.isFiltered
            }, c);
            if (! function() {
                    var a, b, c, f;
                    c = this.filters, f = [];
                    for (a = 0, b = c.length; a < b; a++) d = c[a], d.property === e.property && f.push(d);
                    return f
                }.call(this).length) e.id = "annotator-filter-" + e.property, e.annotations = [], e.element = this.filter.clone().appendTo(this.element), e.element.find("label").html(e.label).attr("for", e.id), e.element.find("input").attr({
                id: e.id,
                placeholder: b._t("Filter by ") + e.label + "â€¦"
            }), e.element.find("button").hide(), e.element.data("filter", e), this.filters.push(e);
            return this
        }, d.prototype.updateFilter = function(b) {
            var c, d, e, f, g, h, i;
            b.annotations = [], this.updateHighlights(), this.resetHighlights(), e = a.trim(b.element.find("input").val());
            if (e) {
                d = this.highlights.map(function() {
                    return a(this).data("annotation")
                }), i = a.makeArray(d);
                for (g = 0, h = i.length; g < h; g++) c = i[g], f = c[b.property], b.isFiltered(e, f) && b.annotations.push(c);
                return this.filterHighlights()
            }
        }, d.prototype.updateHighlights = function() {
            return this.highlights = this.annotator.element.find(".annotator-hl:visible"), this.filtered = this.highlights.not(this.classes.hl.hide)
        }, d.prototype.filterHighlights = function() {
            var b, c, d, e, f, g, h, i, j, k;
            b = a.grep(this.filters, function(a) {
                return !!a.annotations.length
            }), e = ((k = b[0]) != null ? k.annotations : void 0) || [], b.length > 1 && (d = [], a.each(b, function() {
                return a.merge(d, this.annotations)
            }), h = [], e = [], a.each(d, function() {
                return a.inArray(this, h) === -1 ? h.push(this) : e.push(this)
            })), f = this.highlights;
            for (g = i = 0, j = e.length; i < j; g = ++i) c = e[g], f = f.not(c.highlights);
            return f.addClass(this.classes.hl.hide), this.filtered = this.highlights.not(this.classes.hl.hide), this
        }, d.prototype.resetHighlights = function() {
            return this.highlights.removeClass(this.classes.hl.hide), this.filtered = this.highlights, this
        }, d.prototype._onFilterFocus = function(b) {
            var c;
            return c = a(b.target), c.parent().addClass(this.classes.active), c.next("button").show()
        }, d.prototype._onFilterBlur = function(b) {
            var c;
            if (!b.target.value) return c = a(b.target), c.parent().removeClass(this.classes.active), c.next("button").hide()
        }, d.prototype._onFilterKeyup = function(b) {
            var c;
            c = a(b.target).parent().data("filter");
            if (c) return this.updateFilter(c)
        }, d.prototype._findNextHighlight = function(a) {
            var b, c, d, e, f, g, h, i;
            return this.highlights.length ? (g = a ? 0 : -1, i = a ? -1 : 0, h = a ? "lt" : "gt", b = this.highlights.not("." + this.classes.hl.hide), d = b.filter("." + this.classes.hl.active), d.length || (d = b.eq(g)), c = d.data("annotation"), e = b.index(d[0]), f = b.filter(":" + h + "(" + e + ")").not(c.highlights).eq(i), f.length || (f = b.eq(i)), this._scrollToHighlight(f.data("annotation").highlights)) : this
        }, d.prototype._onNextClick = function(a) {
            return this._findNextHighlight()
        }, d.prototype._onPreviousClick = function(a) {
            return this._findNextHighlight(!0)
        }, d.prototype._scrollToHighlight = function(b) {
            return b = a(b), this.highlights.removeClass(this.classes.hl.active), b.addClass(this.classes.hl.active), a("html, body").animate({
                scrollTop: b.offset().top - (this.element.height() + 20)
            }, 150)
        }, d.prototype._onClearClick = function(b) {
            return a(b.target).prev("input").val("").keyup().blur()
        }, d
    }(b.Plugin), b.Plugin.Markdown = function(c) {
        function d(a, c) {
            this.updateTextField = I(this.updateTextField, this), (typeof Showdown !== "undefined" && Showdown !== null ? Showdown.converter : void 0) != null ? (d.__super__.constructor.apply(this, arguments), this.converter = new Showdown.converter) : console.error(b._t("To use the Markdown plugin, you must include Showdown into the page first."))
        }
        return H(d, c), d.prototype.events = {
            annotationViewerTextField: "updateTextField"
        }, d.prototype.updateTextField = function(c, d) {
            var e;
            return e = b.Util.escape(d.text || ""), a(c).html(this.convert(e))
        }, d.prototype.convert = function(a) {
            return this.converter.makeHtml(a)
        }, d
    }(b.Plugin), b.Plugin.Tags = function(c) {
        function d() {
            return this.setAnnotationTags = I(this.setAnnotationTags, this), this.updateField = I(this.updateField, this), D = d.__super__.constructor.apply(this, arguments), D
        }
        return H(d, c), d.prototype.options = {
            parseTags: function(b) {
                var c;
                return b = a.trim(b), c = [], b && (c = b.split(/\s+/)), c
            },
            stringifyTags: function(a) {
                return a.join(" ")
            }
        }, d.prototype.field = null, d.prototype.input = null, d.prototype.pluginInit = function() {
            if (!b.supported()) return;
            return this.field = this.annotator.editor.addField({
                label: b._t("Add some tags here") + "â€¦",
                load: this.updateField,
                submit: this.setAnnotationTags
            }), this.annotator.viewer.addField({
                load: this.updateViewer
            }), this.annotator.plugins.Filter && this.annotator.plugins.Filter.addFilter({
                label: b._t("Tag"),
                property: "tags",
                isFiltered: b.Plugin.Tags.filterCallback
            }), this.input = a(this.field).find(":input")
        }, d.prototype.parseTags = function(a) {
            return this.options.parseTags(a)
        }, d.prototype.stringifyTags = function(a) {
            return this.options.stringifyTags(a)
        }, d.prototype.updateField = function(a, b) {
            var c;
            return c = "", b.tags && (c = this.stringifyTags(b.tags)), this.input.val(c)
        }, d.prototype.setAnnotationTags = function(a, b) {
            return b.tags = this.parseTags(this.input.val())
        }, d.prototype.updateViewer = function(c, d) {
            return c = a(c), d.tags && a.isArray(d.tags) && d.tags.length ? c.addClass("annotator-tags").html(function() {
                var c;
                return c = a.map(d.tags, function(a) {
                    return '<span class="annotator-tag">' + b.Util.escape(a) + "</span>"
                }).join(" ")
            }) : c.remove()
        }, d
    }(b.Plugin), b.Plugin.Tags.filterCallback = function(a, b) {
        var c, d, e, f, g, h, i, j;
        b == null && (b = []), e = 0, d = [];
        if (a) {
            d = a.split(/\s+/g);
            for (g = 0, i = d.length; g < i; g++) {
                c = d[g];
                if (b.length)
                    for (h = 0, j = b.length; h < j; h++) f = b[h], f.indexOf(c) !== -1 && (e += 1)
            }
        }
        return e === d.length
    }, b.prototype.setupPlugins = function(c, d) {
        var e, f, g, h, i, j, k, l, m;
        c == null && (c = {}), d == null && (d = {}), j = b.Util.getGlobal(), h = ["Unsupported", "Auth", "Tags", "Filter", "Store", "AnnotateItPermissions"], j.Showdown && h.push("Markdown"), i = j.location.href.split(/#|\?/).shift() || "", g = {
            Tags: {},
            Filter: {
                filters: [{
                    label: b._t("User"),
                    property: "user"
                }, {
                    label: b._t("Tags"),
                    property: "tags"
                }]
            },
            Auth: {
                tokenUrl: c.tokenUrl || "http://annotateit.org/api/token"
            },
            Store: {
                prefix: c.storeUrl || "http://annotateit.org/api",
                annotationData: {
                    uri: i
                },
                loadFromSearch: {
                    uri: i
                }
            }
        };
        for (e in d) {
            if (!G.call(d, e)) continue;
            f = d[e], J.call(h, e) < 0 && h.push(e)
        }
        a.extend(!0, g, d), m = [];
        for (k = 0, l = h.length; k < l; k++) e = h[k], e in g && !g[e] ? m.push(void 0) : m.push(this.addPlugin(e, g[e]));
        return m
    }
})).call(this);
})(jQuery);