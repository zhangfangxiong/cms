// JavaScript Document
requirejs.config({
    baseUrl: localPath + 'lib',
    //baseUrl: '../lib',
    paths: {
        'jquery': 'jquery/jquery.min',
        'swiper': 'plugs/swiper.min',
    },
    shim: {
        'swiper': {
            deps: ['jquery']
        }
    }
});


require(['jquery', 'swiper'], function () {
var swiper = new Swiper('.swiper-container', {
		slidesPerView : 1,
        paginationClickable: true,
        direction: 'vertical',
		nextButton:"#next_btn",
		onSlideChangeStart: function(swiper){
			//console.log(swiper.activeIndex);
			//点击跳过显示第五张图片 
			if(swiper.activeIndex == 6){
				$('.next').hide();	
			}else{
				$('.next').show();
			}
   	 }
    });
	
		
	//点击加入我们，出现弹框
	$('#join_us_btn').click(function(e) {
        $('#join_now').show();
    });
	
	//点击叉号关闭弹框
	$('#close_btn').click(function(e) {
        $('#join_now').hide();
    });
	   //获取验证码
        $("#getverifyCode").on("click", function () {
            var phone = $("#phoneNum").val();
            var url = localpath2 + '/h5/Ucenter/verifycode';

            if (phone != "") {
                $.get(url + "?mobile=" + phone + "&type=3", function (ret) {
                    ret = eval('(' + ret + ')');
                    console.log(ret.data);
                    if (ret.status) {
                        console.log(ret.status);
                    } else {
                        alert(ret.data);
                    }
                    return false;
                });
                //SendSms(phone);
            } else {
                alert('请填写手机号');
            }
        });
        $('#join_now_btn').on('click',function(){
			var a=localpath2;
            //zfx新加
            var url = localpath2+'/h5/index/lixiangjia';
            var locationUrl = localpath2+'/h5/index/lixiangjia';
            var phoneNum = $('#phoneNum').val();
            var verifyCode = $('#verifyCode').val();
            var name = $('#name').val();
            var param = {phoneNum: phoneNum, verifyCode: verifyCode, name: name};

            $.post(url, param, function (ret) {
                ret = eval('(' + ret + ')');
                if (ret.status) {
                    alert('加入成功');
                    //location.href=locationUrl;
                } else {
                    alert(ret.data);
                }
                return false;
            });
        })


});