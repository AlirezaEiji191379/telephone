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
        let query=getUrlVars()["id"];
        if(query==null) {
            data = sendAjaxRequest("POST", "http://localhost/telephone_project/Controller/mainController.php/contact", jsonData)
        }else{
            data=sendAjaxRequest("PUT","http://localhost/telephone_project/Controller/mainController.php/contact/"+query,jsonData)
        }
        if(data=="ok")window.location.replace("index.html");
        else $("p").text(data);
    }
);

function getUrlVars()
{
    var vars = [], hash;
    var hashes = window.location.href.slice(window.location.href.indexOf('?') + 1).split('&');
    for(var i = 0; i < hashes.length; i++)
    {
        hash = hashes[i].split('=');
        vars.push(hash[0]);
        vars[hash[0]] = hash[1];
    }
    return vars;
}





