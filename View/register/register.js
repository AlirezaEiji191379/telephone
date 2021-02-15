function User(firstname,lastname,username,password,email,phoneNumber){
    this.firstname=firstname;
    this.lastname=lastname;
    this.username=username;
    this.password=password;
    this.email=email;
    this.phoneNumber=phoneNumber;
}

$('#myform').click(function () {
    let firstname=$("#firstNameInput").val();
    let lastname=$("#lastNameInput").val();
    let email=$("#emailInput").val();
    let username=$("#usernameInput").val();
    let password=$("#passwordInput").val();
    let phoneNumber=$("#phoneNumberInput").val();
    let confirm=$("#confirmInput").val();

    if(firstname=="" || lastname=="" || email=="" || username=="" || password=="" || confirm=="" || phoneNumber==""){
        $("p").text("لطفا همه را پر کنید!")
        return
    }
     if(confirm!=password){
         $("p").text("رمز عبور های وارد شده یکسان نیستند!");
         return;
    }
    let inputUser=new User(firstname,lastname,username,password,email,phoneNumber);
    let jsonData=JSON.stringify(inputUser);
    $.post("http://localhost/telephone_project/Controller/mainController.php/User",
        jsonData
    ).fail(function (xhr, status, error) {
        $("p").text(xhr.responseText)
    }).done(function () {
        window.location.replace("../login/index.html?registered=0");
    });

});








