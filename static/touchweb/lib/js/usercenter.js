requirejs.config({
    baseUrl: localPath + 'lib',
    paths: {
        'jquery': 'jquery/jquery.min',
        'webtouchcommon': 'js/webtouchcommon',
        'commonus': 'js/commonus'
    },
    shim: {
        'commonus': {
            deps: ['jquery']
        }
    }
});

requirejs(['jquery', 'webtouchcommon', 'commonus'], function (doc) {
    $(usercenterInitpage);
    function usercenterInitpage() {
        //canvas加载图片
        webtouchObj.canvasload();
        $('#edit a').click(function (e) {
           var cop = $('.finish a').html();
            if (cop == '编辑') {
                $('.finish a').html('完成');
                $('.finish a').css('color', '#e8382b');
                $('.finish_icon').show();
                $('.finish_icon').addClass("editit");
                $('.del').show();
                $('.fangurl').attr("href","javascript:void(0)");
                //编辑状态，不能翻页
                if (typeof(ifLoad)!="undefined") {
                    ifLoad = false;
                }
            } else {
                var url=$('.fangurl').data("url");
                $('#edit a').html('编辑');
                $('#edit a').css('color', '#333');
                $('.finish_icon').hide();
                $('.finish_icon').removeClass("editit");
                $('.del').hide();
                $('.fangurl').attr("href", url);
                if (typeof(ifLoad)!="undefined") {
                    ifLoad = true;
                }
            }

        });
        $('#mainsection').on('click', '.finish_icon', function () {
            if ($(this).hasClass("editit")) {
                $(this).removeClass("editit");
                $(this).addClass("selectits");
            }
            else {
                $(this).removeClass("selectits");
                $(this).addClass("editit");
            }
        });

        $(".cnt:last").removeClass("border_bottom");

        $('#select_delete').on('click', function () {
            deleteSelected();
        });
        $('#all_delete').on('click', function () {
            deleteAll();

        });
        function deleteSelected() {
            var localPath = '/h5/Ucenter/removefav';
            var selected = $('.newHosue_list .selectits');
            var ids = "";

            selected.each(function (index) {
                ids += $(this).data("id") + ",";
            });
            if (selected.length > 0) {
                ids = ids.substring(0, ids.length - 1);
            }

            if (ids.length != 0) {
                var targetID = "targetID=" + ids;
                try {
                    $.ajax({
                        url: localPath,
                        type: "GET",
                        data: targetID,
                        dataType: "json",
                        async: true,
                        success: function (ret) {
                            //console.dir(ret);
                            window.location.reload();
                            // ret = eval('(' + ret + ')');
                            /* console.log("relult:"+ ret);
                             console.log(ret.data);
                             if (ret.status) {
                             console.log(ret.status);
                             }*/
                            //return false;

                            //location.href="";//此处填写请求成功后的跳转地址
                        },
                        error: function (result) {
                            alert("请重新填写信息！");

                        }
                    });
                } catch (e) {

                    alert("请求失败，请稍后再试");

                }
            } else {
                //alert("请选中");	
            }
        }

        function deleteAll() {
            var localPath = '/h5/Ucenter/removeallfav';

            try {
                $.ajax({
                    url: localPath,
                    type: "GET",
                    data: null,
                    dataType: "json",
                    async: true,
                    success: function (ret) {
                        // ret = eval('(' + ret + ')');
                        //	console.log(ret.data);
                        if (ret.status) {
                            alert("删除成功！");
                            window.location.reload();
                        }
                        //return false;
                    },
                    error: function (result) {
                        alert("请重新填写信息！");

                    }
                });
            } catch (e) {

                alert("请求失败，请稍后再试");

            }
        }

        $('#select_deleteE').on('click', function () {
            deleteSelectedE();
        });

        $('#all_deleteE').on('click', function () {
            deleteAllE();
        });

        function deleteSelectedE() {
            var selected = $('.collect_evaluation_list .selectits');
            var ids = [];

            selected.each(function (index, item) {
                ids.push($(item).attr("unitId"));
            });

            if (ids.length != 0) {
                var targetID = "targetID=" + ids;
                try {
                    $.ajax({
                        url: "/h5/Ucenter/removeEvaluation",
                        type: "GET",
                        data: targetID,
                        dataType: "json",
                        async: true,
                        success: function (ret) {
                            if (!ret.status) {
                                alert(ret.data.msg);
                            }
                            else {
                                alert("删除成功！");
                                window.location.reload();
                            }
                        },
                        error: function (result) {
                            alert("删除失败！");
                        }
                    });
                } catch (e) {
                    alert("请求失败，请稍后再试");
                }
            } else {
                //alert("请选中");
            }
        }

        function deleteAllE() {
            try {
                $.ajax({
                    url: "/h5/Ucenter/removeallEvaluation",
                    type: "GET",
                    data: null,
                    dataType: "json",
                    async: true,
                    success: function (ret) {
                        window.location.reload();
                        // ret = eval('(' + ret + ')');
                        //	console.log(ret.data);
                        if (ret.status) {
                            //console.log(ret.status);
                        }
                        //return false;

                        location.href = "#";//此处填写请求成功后的跳转地址
                    },
                    error: function (result) {
                        alert("删除失败！");
                    }
                });
            } catch (e) {
                alert("请求失败，请稍后再试");
            }
        }















    }

});

