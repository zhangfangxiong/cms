var Hxanaylse = {
    AddHxid: function () {
        var fj_name = $(this).parent().find("option:selected").text();
        var fj_value = $(this).parent().find("select").val();
        if (fj_value == "") {
            alert("选择户型编号");
            return;
        } else {
            if ($(this).parent().parent().next().is(":hidden")) {
                valueId.push(fj_value);
                $(this).parent().parent().next().find(".showtitle").html(fj_name);
                $(this).parent().parent().next().find(".showtitle").attr("data-value", fj_value);
                $(this).parent().parent().next().show();
                $("body,html").animate({scrollTop: 120}, 500);
            } else {
                ifhas = valueId.join(",");
                valueId.push(fj_value);
                if (ifhas.indexOf(fj_value) != -1) {
                    alert("相同户型编号不能添加两次！");
                } else {
                    count = $(".dpList").length + 1;
                    $(".dpList:last").after("<div class=\"dpList\">" +
                            $(this).parents("#myform").find(".dpList:last").html().
                            replace(/hxImg/g, 'hxImg' + count).
                            replace(/sImageShow/g, 'sImageShow' + count).
                            replace(/validate_wrong/g, '').
                            replace(/hxshipaiID/g, 'hxshipaiID' + count)
                            + "</div>");
                    $("#myform").find(".dpList:last").find(".eHxImgDiv").children().children().remove();
                    // $("#myform").find(".dpList:last").find(".eHxImgDiv").eq(1).children().remove();
                    $("#myform").find(".dpList:last").find(".eHxImgDiv").hide();
                    //$("#myform").find(".dpList:last").find(".eHxImgDiv").eq(1).hide();
                    $("#myform").find(".dpList:last").find(".eHxImgDiv").next().remove();
                    //$("#myform").find(".dpList:last").find(".eHxImgDiv").eq(1).next().remove();
                    $("#myform").find(".dpList:last").find(".eHxImgDiv").find("input[type='hidden']").remove();
                    //$("#myform").find(".dpList:last").find(".eHxImgDiv").eq(1).find("input[type='hidden']").remove();
                    $("#myform").find(".dpList:last").find(".validate_checktip ").html("");
                    $(".showtitle:last").attr("data-value", fj_value);
                    $(".showtitle:last").html(fj_name);
                    var ccuploader = initUpload($(".hxupload:last")[0]);
                    var ccuploader2 = initUpload($(".hxupload2:last")[0]);
                    ccuploader.init();
                    ccuploader2.init();
                    tops = tops + $(".dpList:first").height();
                    $("body,html").animate({scrollTop: tops}, 500);
                }

            }
        }
    },
    deleteone: function () {
        if ($(".dpList").length > 1) {
            $(this).parents(".dpList").remove();
            var valueIdnum = $(this).prev().data("value");
            for (var i = 0; i < valueId.length; i++) {
                if (valueId[i] == valueIdnum) {
                    valueId.remove(valueId[i]);
                    i = 0;
                }
            }
            ifhas = valueId.join(",");
            // console.dir(ifhas);
        } else {
            $(this).parents(".dpList").hide();
            var valueIdnum = $(this).prev().data("value");
            valueId = [];
            ifhas = "";
            // console.dir(ifhas);
        }
    },
    changeSelectbtn: function () {
        if ($(this).val() != "") {
            $(this).next().click();
            $("#myform").find("select[id^='sHuXingID']").siblings(".validate_checktip").removeClass('validate_wrong').html("");
        } else {
            $("#myform").find("select[id^='sHuXingID']").siblings(".validate_checktip").addClass('validate_wrong').html("请选择户型编号");
        }
    },
    initArray: function () {
        if ($(".dpList").length > 0) {
            for (var i = 0; i < $(".dpList").length; i++) {
                var a = $(".dpList").eq([i]).children().find(".showtitle").data("value");
                valueId.push(a);
            }
        }
    },
    addIpDivImg: function () {
        count = $(".dpDivImg").length + 1;
        $(".dpDivImg:last").after("<div class=\"form-group dpDivImg\">" +
                $(this).parents(".form-group").html().
                replace("其他点评：", '').
                replace(/sHxOtherDpImg/g, 'sHxOtherDpImg' + count).
                replace(/sHxOtherImgShow/g, 'sHxOtherImgShow' + count).
                replace('<a href="" id="addIpDivImg">+添加图片</a>', '<a href="" class="delIpDivImg">-</a>')
                + "</div>");
        $(".dpDivImg:last").find("input[name^='sHxOtherDp']").val('');
        $(".dpDivImg:last").find("input[name^='upImageId']").remove();
        $(".dpDivImg:last").find("input[name^='sDesc']").val('');
        $(".dpDivImg:last").find(".eUploadImg").attr('src', '');
        $(".dpDivImg:last").find(".delIpDivImg").next("div").remove();
        var ccuploader = initUpload($(".plupload:last")[0]);
        ccuploader.init();
        return false;
    },
    delDpimg: function () {
        $(this).parents(".form-group").remove();
        if ($(this).data('content-id')) {
            $("#myform").append('<input type="hidden" name="sHxImgDel[]" value="' + $(this).data('content-id') + '">');
        }
        return false;
    }

}

Array.prototype.remove = function (el) {
    return this.splice(this.indexOf(el), 1);
}

function HxinitPage() {
    // 添加 点评图片
    Hxanaylse.initArray();
    $("#addIpDivImg").on("click", Hxanaylse.addIpDivImg)
    // 删除 点评图片
    $("#pcContent").on("click", ".delIpDivImg", Hxanaylse.delDpimg);
    //增加户型编号选项         
    $("#AddNewbtn").on("click", Hxanaylse.AddHxid);
    //删除一行
    $("#myform").on("click", '.deletebtn ', Hxanaylse.deleteone);
    //选择框切换
    $("#myform").on("change", 'select[name^="sHuXingID"]', Hxanaylse.changeSelectbtn);
    // 删除图片
    $("#myform").on('click', '.action', deleteUploadimg);
    // 移除边框
    $("#myform").on('blur', '.eHxImgDiv input', borderdisplay);
    //鼠标移上显示大图
    $("#myform").on("mouseover mouseout","img.datahovers",hoverImgFn)
     $("#myform").on("mouseover mouseout",".showBigimg",bigimghover)
    // 提交整体评价表单
    $("#myform").validate({submitHandler: function (form) {
            // 判断是否上传了图片
            flag = 0;
            if (!Starempty()) {
                flag = 1;
            }
            if (!pushTips()) {
                flag = 1;
            }
            if (!imageNamenotBlank()) {
                flag = 1;
            }
            if (flag) {
                return false;
            }
            $.post("/evaluation/Hxanalyse/addHxanalyse", $(form).serialize(), function (ret) {
                alert(ret.data);
                 location.reload();
            }, 'json');
            return false;
        }});
}