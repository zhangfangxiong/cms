var DataHelper = {};

(function (helper) {
	var defaultTimeOut = 60000;
	var request = null;
    //helper.Url = "http://localhost:7831/fangjia/mobile/api/";
    //helper.Url = "http://172.28.29.61:8086/fangjia/mobile/api/";
	//helper.Url = "http://172.28.29.99:8083/fangjia/mobile/api/";
    helper.Url="http://api.fangjiadp.com/app_1_0/fangjia/mobile/api/";

	//获取数据，actionCode：对应的action.parameter:参数列表，successFun:成功的回调函数
	helper.GetJson = function (actionCode, prameter, successFuc, errorFun,finalFun,async) {
   
		var parameter = JSON.stringify(prameter);
		request = $.ajax({
			type: 'post',
			url: this.Url+ actionCode,
			data: { parameterStr: parameter },
			dataType: 'json',
			timeout: defaultTimeOut,
			async:async,
			success: successFuc,
			error: function (xhr, textStatus, errorThrown) {
				if (typeof errorFun == "function") {
					errorFun();
				}
				if (textStatus == "timeout") {
						alert("请求超时！请检查网络后重试！");
				}
			},
			complete:function() {
				if (finalFun) {
					finalFun();
				}
			}
		});
	};
})(DataHelper);