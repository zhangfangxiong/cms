function pager(target, func, pageID, pageTotal, button) {
	if (button == null) button = 7;

	var html = "";
	if (pageTotal > 1) {
		html += "<div class='letter_page_list'><span>";
		var i = 0;
		var page = parseInt(pageID - (button - 1) / 2);
		if (page + button > pageTotal) {
			page = pageTotal - button + 1;
		}
		if (page < 1) {
			page = 1;
		}
		for (var i = 0; page <= pageTotal && i < button; i++, page++) {
			if (page == pageID) {
				html += "<a href='javascript:void(0);' onclick='return false;' page = " + page + " class='pager_go_link current'>" + page + "</a>";
			}
			else {
				html += "<a href='javascript:void(0);' onclick='return false;' page = " + page + " class='pager_go_link'>" + page + "</a>";
			}
		}
		html += "</span></div>";
		html += "<div class='letter_page_go'><span>向第<input class='pager_input' type='text' value='" + pageID + "'/>页 </span><a class='pager_go_button' href='javascript:void(0);'>跳转</a></div>";
	}
	$(target).attr("current_page_id", pageID);
	$(target).html(html);
	var children = $(".pager_go_link", $(target));
	children.click(function (args) {
		var button = args.srcElement || args.target;
		var page = parseInt($(button).attr("page"));
		if (page == $(target).attr("current_page_id")) {
			return false;
		}
		if (page > 0 && page <= pageTotal) {
			$(".letter_page_list span a", $(target)).removeClass("current");
			$(".letter_page_list span a[page=" + page + "]", $(target)).addClass("current");
			$("input[type=text]", target).val(page);
			$(target).attr("current_page_id", page);
			if (typeof (func) == "function") {
				func(page);
				return;
			}
			try {
				var f = eval("(" + func + ")");
				if (typeof (f) == "function") {
					f(page);
					return;
				}
			}
			catch (e) {
			}
			try {
				eval("(" + func.replace(/\$0/gi, page) + ")");
			}
			catch (e) {
			}
		}
	});
	$(".pager_go_button", $(target)).click(function () {
		var val = $("input[type=text]", target).val();
		val = val.replace(/[^0-9]+/gi, "");
		var page = parseInt(val, 10);
		if (page > 0 && page <= pageTotal) {
			$(".letter_page_list span a", $(target)).removeClass("current");
			$(".letter_page_list span a[page=" + page + "]", $(target)).addClass("current");
			//console.log(page);
			if (typeof (func) == "function") {
				func(page);
				return;
			}
			try {
				var f = eval("(" + func + ")");
				if (typeof (f) == "function") {
					f(page);
					return;
				}
			}
			catch (e) {
			}
			try {
				eval("(" + func.replace(/\$0/gi, page) + ")");
			}
			catch (e) {
			}
		}
		else {
			alert("请输入1-" + pageTotal + "之间的页码数");
		}
	});
	$(".pager_input", $(target)).keyup(function (args) {
		if (args.keyCode == 13) {
			var val = $("input[type=text]", target).val();
			val = val.replace(/[^0-9]+/gi, "");
			var page = parseInt(val, 10);
			if (page > 0 && page <= pageTotal) {
				$(".letter_page_list span a", $(target)).removeClass("current");
				$(".letter_page_list span a[page=" + page + "]", $(target)).addClass("current");
				if (typeof (func) == "function") {
					func(page);
					return;
				}
				try {
					var f = eval("(" + func + ")");
					if (typeof (f) == "function") {
						f(page);
						return;
					}
				}
				catch (e) {
				}
				try {
					eval("(" + func.replace(/\$0/gi, page) + ")");
				}
				catch (e) {
				}
			}
			else {
				alert("请输入1-" + pageTotal + "之间的页码数");
			}
		}
	});
}