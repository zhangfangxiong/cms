requirejs.config({
    baseUrl: localPath + 'lib',
     //baseUrl: '../lib',
    paths: {
        'jquery': 'jquery/jquery.min',
        'swiper': 'plugs/swiper.min',
        'webtouchcommon': 'js/webtouchcommon'
    },
    shim: {
        'swiper': {
            deps: ['jquery']
        }
    }
});

requirejs(['jquery', 'swiper', 'webtouchcommon'], function (doc) {

    $(hotsellInit);
    function hotsellInit() {
        //canvas加载
        webtouchObj.canvasload();

        //左右滑动效果    
        var swiper = new Swiper('.swiper-container', {
            slidesPerView: 2.5,
            paginationClickable: true,
            spaceBetween: 12,
            freeMode: true,
            freeModeMomentum: false
        });
        //滚动条下拉翻页请参考listcommon
		
		html +='<a href="'+ 跳转地址 +'" class="item pl12">' + 
				   '<div class="cnt p12 border_bottom">' +
					   '<div class="pic mr12">' +
						   '<canvas data-src="' + 图片地址 + '">' + '</canvas>' +
						   '<s class="' + 楼盘状态是推荐购买还是尽快入手还是 + '">' + '</s>' +
					   '</div>' +
					   '<div class="wrap">' +
						   '<div class="house_name">' +
							   '<span class="name">' + 楼盘名字 + '</span>' +
							   '<span class="grade ml5">' + 评分 + '</span>' +
						   '</div>' +
						   '<div class="house_txt">' + 浦东-川沙 + '</div>' +
						   '<div class="house_txt house_txt1">' +
							   '<span class="mr5">' + 品牌开发商 + '</span>' +
							   '<span>户型紧凑实用</span>' +
						   '</div>' +
						   '<div class="house_txt house_txt2">' +
							   '<span class="key_txt">' + 价格 + '</span>' +
							   '元/平' +
						   '</div>' +
					   '</div>' +
				   '</div>' +
               '</a>' 

    }

});
