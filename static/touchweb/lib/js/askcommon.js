define(["jquery"], function () {
    return askObj = {
        notBlank: function () {
            var analystId = askObj.trim($("#analystId").val());
            var comment = askObj.trim($("#comments_text").val());
            var name = askObj.trim($("#person_names").val());
            var phone = askObj.trim($("#person_phones").val());
            if (analystId == "" || comment == "" || name == "" || phone == "") {
                if (analystId == "") {
                    $("#tipsbox").html("分析师Id不能为空！");
                }
                if (phone == "") {
                    $("#tipsbox").html("手机号码不能为空！");
                    $("#person_phones").focus();
                } else {
                    var reg = /^1[3|4|5|7|8]\d{9}$/;
                    if (!reg.test(phone)) {
                        $("#tipsbox").html("手机号码不正确！");
                        $("#person_phones").focus();
                    }
                }
                if (name == "") {
                    $("#tipsbox").html("用户姓名不能为空！");
                    $("#person_names").focus();
                }
                if (comment == "") {
                    $("#tipsbox").html("咨询内容不能为空！");
                    $("#comments_text").focus();
                }
                $("#tipsbox").slideDown(100);
                return;
            } else {
                if (phone != "") {
                    var reg = /^1[3|4|5|7|8]\d{9}$/;
                    if (!reg.test(phone)) {
                        $("#tipsbox").html("手机号码不正确！");
                        $("#tipsbox").slideDown(100);
                        $("#person_phones").focus();
                    } else {
                        console.log(analystId);
                        //下面书写ajax提交函数,success返回成功！
                        $.ajax({
                            type: "get",
                            url: "/Ajax/Analyst/sendQuestion",
                            data: {
                                id : analystId,
                                content : comment,
                                username : name,
                                mobile : phone
                            },
                            dataType: "json",
                            success: function (data) {
                                if(data.status) {
                                    $("#tipsbox").html("咨询内容已提交！");
                                    $("#tipsbox").slideDown(100);
                                    setTimeout('$("#tipsbox").slideUp(100);', 2000)
                                }else {
                                    $("#tipsbox").html("咨询内容提交失败！");
                                    $("#tipsbox").slideDown(100);
                                    setTimeout('$("#tipsbox").slideUp(100);', 2000)
                                }                                
                            },
                            error: function (result) {
                                $("#tipsbox").html("咨询内容提交失败！");
                                $("#tipsbox").slideDown(100);
                                setTimeout('$("#tipsbox").slideUp(100);', 2000)
                            }
                        });
                    }
                }
            }
        },
        focusfn: function () {
            $("#tipsbox").slideUp(100);
        },
        trim: function (text) {
            if (typeof (text) == "string")
            {
                return text.replace(/^\s*|\s*$/g, "");
            }
            else
            {
                return text;
            }
        }

    }
});