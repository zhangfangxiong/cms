requirejs.config({
    baseUrl: localPath + 'lib',
    // tbaseUrl: '../lib',
    paths: {
        'jquery': 'jquery/jquery.min'
    }
});

requirejs(["jquery"], function (doc) {

    //canvas加载图片
    var canvaslength = $(".main_section").find("canvas").length;
    if (canvaslength > 0) {
        $(".main_section").find("canvas").each(function () {
            var imgSrc = $(this).data("src");
            var imageObj = new Image();
            imageObj.canvs = $(this)[0];
            var cvs = imageObj.canvs.getContext('2d');
            if (cvs) {
                imageObj.onload = function () {
                    imageObj.canvs.width = this.width;
                    imageObj.canvs.height = this.height;
                    cvs.drawImage(this, 0, 0);
                }
                imageObj.src = imgSrc;
            }
        })
    }

});

