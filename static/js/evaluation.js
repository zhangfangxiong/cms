/*
 * cms 评测模块 相关公共js
 * author cjj
 */
$(function () {

    // 弹出框 居中
    var divCenter = function (e) {
        $(this).find('.modal-dialog').css({
            'margin-top': function () {
                var modalHeight = $('#evaluationModal').find('.modal-dialog').height();
                return ($(window).height() / 2 - (modalHeight / 2)) - 200;
            }
        });
    };
    $('#myModal').on('show.bs.modal', divCenter);
    $('#fxsModal').on('show.bs.modal', divCenter);


    // 章节菜单 联动
    $("#iCatID").change(function () {
        if (chapterChild != '') {
            chapterChildObj = $.parseJSON(chapterChild);
            for (var key in chapterChildObj) {
                if (key == $(this).val()) {
                    $("#iSubCatID option").remove();
                    $("#iSubCatID").append("<option value='0'>请选择子章节</option>");
                    for (var childKey in chapterChildObj[key]) {
                        $("#iSubCatID").append("<option value='" + childKey + "'>" + chapterChildObj[key][childKey] + "</option>");
                    }
                    break;
                }
            }
        }
    });
})
function deleteUploadimg() {
    pDiv = $(this).parents(".eHxImgDiv");
    if ($(this).parent('div').siblings().length <= 0) {
        $(this).parent('div').parents(".eHxImgDiv").hide();
        $(this).parent('div').remove();
    } else {
        $(this).parent('div').remove();
    }
    // 删除隐藏域
    if ((pos = $(this).prev().attr('src').lastIndexOf("/")) != -1) {
        fileName = $(this).prev().attr('src').substr(pos + 1);
        $(this).parent().find("input[value='" + fileName + "']").remove();
        //pDiv.find("input[value='"+fileName+"']").remove();
    }
    if ($(this).prev().data('content-id')) {
        $("#myform").append('<input type="hidden" name="sHxImgDel[]" value="' + $(this).prev().data('content-id') + '">');
    }
}
// 图片未上传错误提示
function imgErrorTip(cdiv, pdiv) {
    if (parseInt(cdiv.children("div").length) <= 0) {
        uploadButton = pdiv.find('.plupload');
        uploadButton.next('span.validate_checktip').addClass('validate_wrong').html(uploadButton.data('error-msg'));
        return false;
    } else {
        pdiv.find('.plupload').next('span.validate_checktip').removeClass('validate_wrong').html('');
    }
    return true;
}

//选择提交提示
function pushTips() {
    if ($(".dpList").length <= 1 && $(".dpList").eq(0).is(":hidden")) {
        // $("#myform").find("select[id^='sHuXingID']").next("#checktips").addClass('validate_wrong').html("请选择户型编号");  
        $("#myform").find("select[id^='sHuXingID']").siblings(".validate_checktip").addClass('validate_wrong').html("请选择户型编号");
        return false;

    } else {
        $("#myform").find("select[id^='sHuXingID']").siblings(".validate_checktip").removeClass('validate_wrong').html("");
    }
    return true;
}
//图片未上传循环判断

function imgForeachtips() {
    var tflag = 0;
    //console.log($("#myform").find("div.dpList div.row").length);
    for (var i = 0; i < $("#myform").find("div.dpList div.row").length; i++) {

        if ($("#myform").find("div.dpList div.row").eq(i).children("div").length <= 0) {
            //console.log($("#myform").find("div.row").eq(i).children("div"));
            $("#myform").find("div.dpList div.row").eq(i).parent().siblings(".validate_checktip ").addClass('validate_wrong').html("请选择图片");
            tflag = 1;
            // return false;
        } else {
            $("#myform").children().find("div.dpList div.row").eq(i).parent().siblings(".validate_checktip ").removeClass('validate_wrong').html("");
            // tflag=0;
            //  return true;
            //   console.log($("#myform").find("div.row").eq(i).children("div"));
        }
    }
    if (tflag) {
        return false;
    } else {
        return true;
    }
}

//判断必须有一个不为空

function oneNotempty() {
    var xiaoguo = $("#myform").find("div.eHxImgDiv div.row").eq(0).children("div").length;
    var shijing = $("#myform").find("div.eHxImgDiv div.row").eq(1).children("div").length;
    if (xiaoguo <= 0 && shijing <= 0) {
        $("#myform").find("div.eHxImgDiv").siblings(".validate_checktip ").addClass('validate_wrong').html("请选择图片");
        return false;
    } else {
        return true;
    }
}

//判断全都不能为空
function allNotempty() {
    var tflag = 0;
    for (var i = 0; i < $("#myform").find("div.eHxImgDiv div.row").length; i++) {
        if ($("#myform").find("div.eHxImgDiv div.row").eq(i).children("div").length <= 0) {
            $("#myform").find("div.eHxImgDiv").eq(i).siblings(".validate_checktip ").addClass('validate_wrong').html("请选择图片");
            tflag = 1;
        } else {
            $("#myform").find("div.eHxImgDiv").eq(i).siblings(".validate_checktip ").removeClass('validate_wrong').html("");
        }
    }
    if (tflag) {
        return false;
    } else {
        return true;
    }
}

//非必填notEmpty加了*判断
function Starempty() {
    var tflag = 0;
    for (var i = 0; i < $("#myform").find("div.eHxImgDiv div.row").length; i++) {
        if ($("#myform").find("div.eHxImgDiv div.row").eq(i).children("div").length <= 0 && $("#myform").find("div.eHxImgDiv").eq(i).parent().prev().html().indexOf("*") != -1) {
            $("#myform").find("div.eHxImgDiv").eq(i).siblings(".validate_checktip ").addClass('validate_wrong').html("请选择图片");
            tflag = 1;
        } else {
            $("#myform").find("div.eHxImgDiv").eq(i).siblings(".validate_checktip ").removeClass('validate_wrong').html("");
        }
    }
    if (tflag) {
        return false;
    } else {
        return true;
    }
}

function clearUploadtips(file, ele) {
    /*
     if($(thebtn).parent().siblings().hasClass("validate_wrong")){
     $(thebtn).parent().parent().find(".validate_wrong").removeClass('validate_wrong').html("");
     }*/
}


//重置表单
function initForm(url) {
    $("#myform").validate({
        submitHandler: function (form) {
            if (index == 1) {
                if (!imageNamenotBlank()) {
                  return false
                }
                $.post(url, $(form).serialize(), function (ret) {
                    alert(ret.data);
                    location.reload();
                }, 'json');

            }
            index++;
            return false;
        }
    });
}

//公共资源重置表单
function initPublickForm(url) {
    $("#myform").validate({
        submitHandler: function (form) {
            if (index == 1) {
                if (!imgErrorTip($("#sImageShow"), $(".form-group"))) {
                    return false;
                }
                $.post(url, $(form).serialize(), function (ret) {
                    alert(ret.data);
                    location.reload();
                }, 'json');

            }
            index++;
            return false;
        }
    });
}
//判断是否选择函数
function checkitChecked() {
    var isblank;
    if ($(this).is(':checked') == true) {
        isblank = 1;
        $.data($('#myform')[0], 'validator', '');
        $("#sComment").attr('validate', $("#sComment").attr('validate').replace('!', ''));
        $("span.validate_checktip").removeClass('validate_wrong').html('');
        initForm(theformUrl);
    } else {
        isblank = 0;
        $.data($('#myform')[0], 'validator', '');
        $("#sComment").attr('validate', '!' + $("#sComment").attr('validate'));
        $("span.validate_checktip").removeClass('validate_wrong').html('');
        initForm(theformUrl);
    }
    //$.get(thepostUrl, {isblank:isblank},'json');
}
function exist(id) {
    var s = document.getElementById(id);
    if (s) {
        return true
    }
    else {
        return false
    }
}
/*图片展示*/
function showImg(file, ele) {
    var hxId = "";
    var placeholdertitle = $(ele).data('placeholderName');
    var placeholderdes = $(ele).data('placeholderDec');
    if (exist("sHuXingID[1]") && $(ele).data('target') != "sHxOtherDpImg") {
        hxId = '[' + $(ele).parents(".dpList").find(".showtitle").attr('data-value') + ']';
    }
    if ($(ele).data('outside') == '1') {
        hxId = '[' + $(ele).parents(".onebadlist").find(".badTagname").attr('data-id') + ']';
    }

    if ($(ele).data('dec') == '1') {
        // 当 target 名称为数组形式时 特别处理下
        var patt1 = new RegExp(/\[[0-9]+\]/);
        var result = patt1.test($(ele).data('target'));
        var titleName = '';
        var descName = '';
        if (result) {
            titleName = $(ele).data('target').replace(/(\w+)/, "$1Title");
            descName = $(ele).data('target').replace(/(\w+)/, "$1Desc");
        } else {
            titleName = $(ele).data('target') + 'Title';
            descName = $(ele).data('target') + 'Desc';
        }
        var html = '<div class="image_list"><img  src="' + getDFSViewURL(file) + '"  class="datahovers" ><a href="javascript:void(0);" title="删除" class="action  icon-trash"></a>'
                + '<div class="top-group">'
                + '  <div class="col-sm-12 duiq">'
                + '  <input type="text" class="form-control" maxlength="50" name="' + titleName + hxId + '[]"  placeholder="' + placeholdertitle + '">'
                + '  </div>'
                + '</div>'
                + '<div class="top-group">'
                + '  <div class="col-sm-12 duiq">'
                + '  <textarea class="form-control" rows="2" maxlength="300" name="' + descName + hxId + '[]" placeholder="' + placeholderdes + '"></textarea>'
                + '  </div>'
                + '</div>'
                + '</div>';
        $($(ele).data('img')).append(html);
        $($(ele).data('img')).children(".image_list:last").append('<input type="hidden" name="' + $(ele).data('target') + hxId + '[]" value="' + file + '">');
        $($(ele).data('img')).parent().slideDown('normal');

    } else {
        $($(ele).data('img')).append('<div class="image_list"><img   class="datahovers" src="' + getDFSViewURL(file) + '"><a href="javascript:void(0);" title="删除" class="action  icon-trash"></a></div>');
        //$($(ele).data('img')).parent().append('<input type="hidden" name="' + $(ele).data('target') + hxId + '[]" value="' + file + '">');
        $($(ele).data('img')).children(".image_list:last").append('<input type="hidden" name="' + $(ele).data('target') + hxId + '[]" value="' + file + '">');
        $($(ele).data('img')).parent().slideDown('normal');
    }

}

/*图片展示-已建待建*/
function showImgBuild(file, ele) {
    // 当 target 名称为数组形式时 特别处理下
    var patt1 = new RegExp(/\[[0-9]+\]/);
    var result = patt1.test($(ele).data('target'));
    var titleName = '';
    var descName = '';
    if (result) {
        titleName = $(ele).data('target').replace(/(\w+)/, "$1Title");
        descName = $(ele).data('target').replace(/(\w+)/, "$1Desc");
    } else {
        titleName = $(ele).data('target') + 'Title';
        descName = $(ele).data('target') + 'Desc';
    }
    // 取最后一个publicImageTitleCheck元素
    if ($("input[name^=publicImageTitleCheck]").length > 0) {
        num = parseInt($("input[name^=publicImageTitleCheck]:last").attr('name').replace("publicImageTitleCheck", '')) + 1;
        checkName = "publicImageTitleCheck" + num;
    } else {
        checkName = "publicImageTitleCheck0";
    }
    var alength = $($(ele).data('img')).children().length;
    var html = '<div class="image_list"><img  src="' + getDFSViewURL(file) + '" class="datahovers" ><a href="javascript:void(0);" title="删除" class="action  icon-trash"></a>'
            +' <div class="col-sm-12"> <input type="text" name="publicImageName[]" value="" class="form-control" placeholder="图片名称"></div>'
            + '<input type="hidden" name="' + titleName + '[]">'
            + '<div validate="!radio:' + checkName + ':建设情况" >'
            + '<div class="top-group mb0">'
            + ' <div class="col-sm-12">'
            + '  <span class="fl">*建设情况</span><div class="radio tzrd"><label><input type="radio"  onclick="setPublicImageTitleValue(this)" name="' + checkName + '" data-title="已建成"> 已建成</label></div>'
            + ' </div>'
            + '</div>'
            + '<div class="top-group mb0">'
            + ' <div class="col-sm-offset-3 col-sm-8 mltz" style="margin-top:-8px;">'
            + ' <div class="radio"><label><input type="radio"  name="' + checkName + '" onclick="setPublicImageTitleValue(this)" data-title="建设中/待建"> 建设中/待建</label></div>'
            + '</div>'
            + '</div>'
            + '</div>';
    +'</div>';
    $($(ele).data('img')).append(html);
    $($(ele).data('img')).children(".image_list:last").append('<span class="validate_checktip col-sm-8 mltz"></span><input type="hidden" name="' + $(ele).data('target') + '[]" value="' + file + '">');
    $($(ele).data('img')).parent().slideDown('normal');
    $.data($('#myform')[0], 'validator', '');
    initPublickForm(publickformUrl);
}

function setPublicImageTitleValue(obj) {
    //console.log(obj);
    $(obj).parents(".image_list").find("input[name^=publicImageTitle]").val($(obj).data('title'));
}

//评测修改-图片名称必填
function imageNamenotBlank() {
    var tflag = 0;
    for (var i = 0; i < $("#myform").find("div.eHxImgDiv div.row").length; i++) {
        $("#myform").find("div.eHxImgDiv div.row").eq(i).find("input").each(function () {
            if ($(this).val() == "") {
                $(this).addClass("b_red");
                tflag = 1;
                $(this).focus();
            }
        })
    }
    if (tflag) {
        return false;
    } else {
        return true;
    }
}

//去边框
function borderdisplay() {
    if ($(this).hasClass("b_red") && $(this).val() != "") {
        $(this).removeClass('b_red');
    } else {
        if ($(this).val() == "") {
            $(this).addClass('b_red');
        }
    }
}
//鼠标移上显示大图-模拟hover
function hoverImgFn(event) {
    if (event.type == "mouseover") {
        var src = $(this).attr("src");
        var appentHtml = '<div class="showBigimg"><img src="' + src + '"></div>';
        var obj=this;
         t=setTimeout(function(){ $(obj).parent().append(appentHtml)},100)
    } else if (event.type == "mouseout") {
         clearTimeout(t);
        if ($(this).parent().find(".showBigimg").length > 0) {
            var obj=this;
            setTimeout(function(){ 
                   if(!$(obj).parent().find(".showBigimg").eq(0).hasClass('bigimghover_flag')){
                                $(obj).parent().find(".showBigimg").eq(0).remove()
                   }
              },100)
        }
    }
}

function bigimghover(event) {
    if (event.type == "mouseover") {
        $(this).addClass("bigimghover_flag");
    } else if (event.type == "mouseout") {
       $(this).removeClass("bigimghover_flag")
       $(this).remove();
    }
}

//编辑开关
function evaluationSwitch(event){
    if(event == 1){
      $("input[type='checkbox']").attr("disabled",'disabled');
      $("input[type='text']").attr("readonly",'readonly');
      $("input[type='radio']").attr("disabled",'disabled');
      $("input[type='button']").attr("disabled",'disabled');
      $("textarea").attr("readonly",'readonly');
      $("select").attr("disabled",'disabled');
    }
}

// 同步cricin子章节数据
function syncCricin(event) {
    $("#sync").html('正在同步...')
    $.get("/evaluation/Syncricin/index/", {c:event.data.c,a:event.data.a,unitid:event.data.unitid}, function(ret){
        alert('数据已同步');
        location.reload();
    });
    return false;
}
