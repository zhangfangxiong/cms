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

	// ��������ҳ�涥����ˮƽ����
	var x = Math.max(x1, Math.max(x2, x3));
	// ��������ҳ�涥���Ĵ�ֱ����
	var y = Math.max(y1, Math.max(y2, y3));

	// �������� = Ŀǰ���� / �ٶ�, ��Ϊ����ԭ��ԽС, �ٶ��Ǵ��� 1 ����, ���Թ��������Խ��ԽС
	var speed = 2 + acceleration;
	window.scrollTo(Math.floor(x / speed), Math.floor(y / speed));

	// ������벻Ϊ��, �������õ���������
	if (x > 0 || y > 0) {
		var invokeFunction = "goTop(" + acceleration + ", " + time + ")";
		window.setTimeout(invokeFunction, time);
	};
}
window.onscroll = function () {
	var scrollY = document.documentElement.scrollTop || document.body.scrollTop //;//�����������chrome�»�ȡ������߶ȵļ���д��
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


