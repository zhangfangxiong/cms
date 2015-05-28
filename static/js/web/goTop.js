function goTop(acceleration, time) {
	acceleration = acceleration || 0.1;
	time = time || 6;

	var x1 = 0, y1 = 0, x2 = 0, y2 = 0, x3 = 0, y3 = 0;

	if (document.documentElement) {
		x1 = document.documentElement.scrollLeft || 0;
		y1 = document.documentElement.scrollTop || 0;
	}
	if (document.body) {
		x2 = document.body.scrollLeft || 0;
		y2 = document.body.scrollTop || 0;
	}
	var x3 = window.scrollX || 0;
	var y3 = window.scrollY || 0;

	// 滚动条到页面顶部的水平距离
	var x = Math.max(x1, Math.max(x2, x3));
	// 滚动条到页面顶部的垂直距离
	var y = Math.max(y1, Math.max(y2, y3));

	// 滚动距离 = 目前距离 / 速度, 因为距离原来越小, 速度是大于 1 的数, 所以滚动距离会越来越小
	var speed = 2 + acceleration;
	window.scrollTo(Math.floor(x / speed), Math.floor(y / speed));

	// 如果距离不为零, 继续调用迭代本函数
	if (x > 0 || y > 0) {
		var invokeFunction = "goTop(" + acceleration + ", " + time + ")";
		window.setTimeout(invokeFunction, time);
	};
}
window.onscroll = function () {
	var scrollY = document.documentElement.scrollTop || document.body.scrollTop //;//其他浏览器与chrome下获取滚动轴高度的兼容写法
	var funTop = document.getElementById("funTop");
	if (funTop != null) {
		if (scrollY == 0) {
			document.getElementById("funTop").style.display = "none";
		} else {
			document.getElementById("funTop").style.display = "block";
		}
	}

};

~function ie6Fixed(elem) {
	var isIE6 = ! -[1, ] && !window.XMLHttpRequest;
	if (isIE6 && document.body.currentStyle.backgroundAttachment !== 'fixed') {
		var css = elem.style; var dom = '(document.documentElement)',
		html = document.getElementsByTagName('html')[0];
		html.style.backgroundImage = 'url(about:blank)';
		html.style.backgroundAttachment = 'fixed';
		l = elem.offsetLeft - document.documentElement.scrollLeft,
			t = elem.offsetTop - document.documentElement.scrollTop;
		css.position = 'absolute';
		css.setExpression('left', 'eval(' + dom + '.scrollLeft + ' + l + ') + "px"');
		css.setExpression('top', 'eval(' + dom + '.scrollTop + ' + t + ') + "px"');
	}
} (document.getElementById("funTop"));


