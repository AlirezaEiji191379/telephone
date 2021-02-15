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
        }
    });
});
