define(["jquery","webtouchcommon"], function () {
    return listcomObj = {
        analystId : $('#analystId').val(),
        pagesize : 5,
        currentpage : parseInt($('#currentpage').val()),
        winH: $(window).height(),
        getData: function (pagenumber) {
            $.ajax({
                type: "get",
                url: "/Ajax/Analyst/commentList",
                data: {
                    id : listcomObj.analystId,
                    page:pagenumber,
                    row:  listcomObj.pagesize, 
                },
                dataType: "json",
                success: function (result) {
                    $(".loaddiv").hide();
                    if (result.data.length > 0) {
                        var jsonObj = result.data;
                        listcomObj.insertDiv(jsonObj);
                        webtouchObj.canvasload();
                    }else {
                        $("#pagenumlength").val("0");
                        // alert('暂无数据');
                    }
                },
                beforeSend: function () {
                    $(".loaddiv").show();
                },
                error: function () {
                    $(".loaddiv").hide();
                }
            });

        },
        insertDiv: function (json) {
            var $mainDiv = $("#anay_com_ulid");
            var html = '';
            for (var i = 0; i < json.length; i++) {
                var sImage = '';
                for(var x in json[i].images) {
                    sImage += '<canvas data-src="'+json[i].images[x]+'"></canvas>';
                }                
                html += '<div class="anay_com_list arc_bottom">' +
                        '<a href="/h5/xf/xfdetail?id='+json[i].iBuildingID+'">'+
                        '<div class="anay_com_list_title">' +
                        ' <span class="title_name">'+json[i].sBuildingTitle+'</span>' +
                        '<span class="title_star title_star'+
                        Math.ceil(2*json[i].sScore+1)+'">' +                                             
                        '</span>' +
                        ' <span class="title_star_score">'+json[i].sScore+'</span>' +
                        '</div>' +
                        '<div class="anay_com_list_des">' +
                        json[i].sComment +
                        '</div>' +
                        ' <div class="anay_com_list_img">' +
                        sImage +
                        ' </div>' +
                        '<div class="anay_com_list_time">'+json[i].sDate+'</div>' +
                        '</a></div>';
            }
            $mainDiv.append(html);
        },
        scrollHandler: function () {
            webtouchObj.backtoTop();
            var pageH = $(document).height()
            var scrollT = $(window).scrollTop(); //滚动条top   
            var aa = pageH - listcomObj.winH - scrollT;
            if (aa < 1) {
                if($("#pagenumlength").val()=="1"){
               listcomObj.currentpage++;
                listcomObj.getData(listcomObj.currentpage)
            }else{
                return
            }
            }
        }
    }
});