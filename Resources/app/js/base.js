/**
 * 基础js，封装常用工具
 * 基于JQ
 */
var D_B = function (msg) {
    try {
        console.log(msg);
    } catch (e) {
    }
};

$.BJLang = $.lang.BASEJS;

$.extend({
    "alert": function (msg, param) {
        msg = msg ? '<div>' + msg + '</div>' : '';
        var defParam = {
            "content": msg,
            "lock": true,
            "title": $.BJLang.msessage,
            "opacity": 0.4,
            "width": 250,
            "button": [
                {
                    "name": $.BJLang.close,
                    "focus": false,
                    "callback": function () {
                        return true;
                    }
                }
            ]
        };

        if (typeof param === 'object') {
            defParam = $.extend(defParam, param);
        }

        var artIndex;
        try {
            artIndex = art.dialog(defParam);
        } catch (e) {
            alert("dialog widget error");
        }
        return artIndex;
    },
    "toast": function (msg, time, callback) {
        var icon = "/Resources/app/plugins/artDialog/4.1.7/images/tips-icon.png";
        time = time ? time : 3000;
        try {
            art.dialog({
                "lock": true,
                "content": '<img src="' + icon + '" style="margin-right:10px" />' + msg,
                "tips": true,
                "time": time
            });
            if (typeof callback === 'function') {
                setTimeout(function () {
                    callback();
                }, time);
            }
        } catch (e) {
            alert("dialog widget error");
        }
    },
    "loading": function (msg) {
        var icon = "/Resources/app/plugins/artDialog/4.1.7/images/loading.gif";
        msg = msg ? msg : $.BJLang.loading;
        var artLoading;
        try {
            artLoading = art.dialog({
                "lock": true,
                "content": '<img src="' + icon + '" style="margin-right:10px" />' + msg,
                "tips": true
            });
        } catch (e) {
            alert("dialog widget error");
        }
        return artLoading;
    },
    "sAjax": function (param) {
        var def = {
            "timeout": 80000,
            "type": "post",
            "async": true,
            "loading": true,
            "title": void(0)
        };
        param = $.extend(def, param);

        if (param.loading) {
            var index = $.loading();
            var complete = function () {
                try {
                    param.complete();
                } catch (e) {
                }
                try {
                    index.close();
                } catch (e) {
                }
            };
            param.complete = complete;
        }

        var error = param.error;
        if (typeof error !== "function") {
            param.error = function () {
                $.alert($.BJLang.networkErr);
                return false;
            };
        }
        return $.ajax(param);
    },
    /**
     * 为url加装用户信息
     * @param {type} url
     * @param {type} param
     * @returns {unresolved}
     */
    "sUrl": function (url, param) {
        var defParam = {};

        if (typeof param === 'object') {
            defParam = $.extend(defParam, param);
        }

        if (url.indexOf("?") === -1) {
            url += "?";
        } else {
            url += "&";
        }
        url += $.param(defParam);
        return url;
    }
});
