requirejs.config({
    baseUrl: localPath + 'lib',
    // baseUrl: '../lib',
    paths: {
        'jquery': 'jquery/jquery.min',
        'webtouchcommon': 'js/webtouchcommon',
        'listcommon': 'js/listcommon'
    }
});

requirejs(['jquery', 'listcommon'], function (doc) {

    $(anayListInit);
    function anayListInit() {
        webtouchObj.canvasload();
        $(window).scroll(listcomObj.scrollHandler);
        $('#backtop').on("click", webtouchObj.backtoTopdh);
    }

});

