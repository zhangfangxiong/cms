(function () {
	var system = {
		win: false,
		mac: false,
		xll: false
	};
	//检测平台
	var p = navigator.platform;
	system.win = p.indexOf("Win") == 0;
	system.mac = p.indexOf("Mac") == 0;
	system.x11 = (p == "X11") || (p.indexOf("Linux") == 0);
	//跳转语句
	var t = getQueryString("t");
	var cookiet = getCookie("t_ver");
	if (cookiet != "touch") {
		if (t != "touch") {
			if (!(system.win || system.mac || system.xll)) {
				window.location = "http://m.fangjiadp.com/selectversion.html?redirect_url=" + location.href;
			}
		} else {
			document.cookie = "t_ver=touch";
		}
	}


})();
//获取url参数值
function getQueryString(name) {
	var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)", "i");
	var u = decodeURI(window.location.search.substr(1));
	u = u.replace(/\s+/g, "");
	var r = u.match(reg);
	if (r != null) return decodeURI(r[2]); return null;
}
function getCookie(c_name) {
	if (document.cookie.length > 0) {
		c_start = document.cookie.indexOf(c_name + "=");
		if (c_start != -1) {
			c_start = c_start + c_name.length + 1;
			c_end = document.cookie.indexOf(";", c_start);
			if (c_end == -1) c_end = document.cookie.length;
			return unescape(document.cookie.substring(c_start, c_end));
		}
	}
	return "";
}