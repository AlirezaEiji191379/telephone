$(document).ready(function () {
    $("input").focus(function () {
        $(this).attr("placeholder","");
        let className=$(this).attr("class");
        $("#"+className).fadeIn();
    });
    $("input").blur(function () {
        let className=$(this).attr("class");
        $("#"+className).fadeOut();
        if(className==="username"){
            $(this).attr("placeholder","نام کاربری");
        }else if(className==="password"){
            $(this).attr("placeholder","رمز عبور");
        }else if(className==="confirm"){
            $(this).attr("placeholder","تایید");
        }else if(className==="email"){
            $(this).attr("placeholder","پست الکترونیکی");
        }else if(className==="firstName"){
            $(this).attr("placeholder","نام");
        }else if(className==="lastName"){
            $(this).attr("placeholder","نام خانوادگی");
        }else if(className==="phoneNumber"){
            $(this).attr("placeholder","تلفن همراه");
        }
    });
});
