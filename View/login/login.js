$("#myform").click(function () {
    let username=$("#usernameInput").val()
    let password=$("#passwordInput").val()
    let obj={
        "username":username,
        "password":password
    };
    let jsonObj=JSON.stringify(obj);
    $.post(
        "http://localhost/telephone_project/Controller/mainController.php/login",
        jsonObj
    ).fail(function (xhr, status, error) {
        $("p").text(xhr.responseText)
    }).done(function (data, textStatus, jqXHR) {
        let x=JSON.parse(jqXHR.responseText);
        localStorage.setItem("accessToken",x["accessToken"])
        //alert("login successful!")
        if(x["type"]=="User"){
            window.location.replace("../user_account/index.html");
        }else{
            window.location.replace("../admin_account/index.html");
        }
    });
});