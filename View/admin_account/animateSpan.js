$(document).ready(function () {
    $("input").focus(function () {
        $(this).attr("placeholder","");
        let className=$(this).attr("class");
        let str=(className).split(" ")
        $("#"+str[1]).fadeIn();
    });
    $("input").blur(function () {
        let className=$(this).attr("class");
        let str=(className).split(" ")
        $("#"+str[1]).fadeOut();
        if(className==="form-control fullname"){
            $(this).attr("placeholder","نام و نام خانوادگی");
        }else if(className==="form-control phone1"){
            $(this).attr("placeholder","تلفن همراه اول");
        }else if(className==="form-control phone2"){
            $(this).attr("placeholder","تلفن همراه دوم");
        }else if(className==="form-control email"){
            $(this).attr("placeholder","پست الکترونیکی");
        }else if(className==="form-control fax"){
            $(this).attr("placeholder","فکس");
        }else if(className==="form-control home1"){
            $(this).attr("placeholder","شماره تلفن ثابت");
        }else if(className==="form-control address"){
            $(this).attr("placeholder","آدرس");
        }
    });
});

