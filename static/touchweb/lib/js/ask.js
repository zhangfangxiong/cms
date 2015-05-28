requirejs.config({
    baseUrl: localPath + 'lib',
    // baseUrl: '../lib',
    paths: {
        'jquery': 'jquery/jquery.min',
         'askcommon': 'js/askcommon'
    }
});

requirejs(['jquery','askcommon'], function (doc) {
    $(initAskpage);
    function initAskpage(){
        $("#askonelinebtn").on("click",askObj.notBlank); 
        $("#comments_text").on("keydown",askObj.focusfn);
        $("#person_phones").on("keydown",askObj.focusfn)
        $("#person_names").on("keydown",askObj.focusfn)
    }
});

