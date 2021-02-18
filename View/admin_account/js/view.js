function Contact(fullname,phone1,phone2,home1,email,fax,address){
    this.fullname=fullname;
    this.phone1=phone1;
    this.phone2=phone2;
    this.home1=home1;
    this.fax=fax;
    this.email=email;
    this.address=address;
}

$("#btns").click(
    function () {
        let fullname=$("#fullnameInput").val();
        let phone1=$("#phone1Input").val();
        let phone2=$("#phone2Input").val();
        let home1=$("#home1Input").val();
        let email=$("#emailInput").val();
        let fax=$("#faxInput").val();
        let address=$("#addressInput").val();
        let newContact=new Contact(fullname,phone1,phone2,home1,email,fax,address);
        let  jsonData=JSON.stringify(newContact);
        data=sendAjaxRequest("POST","http://localhost/telephone_project/Controller/mainController.php/contact",jsonData)
        if(data=="ok")window.location.replace("index.html");
        else $("p").text(data);
    }
);






