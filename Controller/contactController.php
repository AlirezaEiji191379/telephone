<?php

require_once ("../Model/Contact.php");
class contactController
{
    private $contact_id;
    private $requestMethod;

    function __construct($requestMethod,$contact_id=null){
        $this->contact_id=$contact_id;
        $this->requestMethod=$requestMethod;
    }

    public function requestProcess(){
        $response=null;
        if($this->requestMethod=="GET"){
            if(isset($this->contact_id)){
                $response=$this->getContactById($this->contact_id);
            }else{
                $response=$this->getAllContacts();
            }
        }
        elseif ($this->requestMethod=="POST"){
            $response=$this->addContact();
        }
        elseif ($this->requestMethod=="PUT"){
            $response=$this->updateContactById();

        }elseif ($this->requestMethod=="DELETE"){
            $response=$this->deleteContactById();
        }
        header($response["header"]);
        echo json_encode($response["body"],JSON_UNESCAPED_UNICODE);
    }

    private function getContactById($id){
        $decoded=authHandler::validateToken();
        if($decoded=="invalid token!" || $decoded=="expired token!") return $this->createMessageToClient("403","access denied!",$decoded);
        $result=Contact::getContactById($id);
        if($result==false) return $this->createMessageToClient(404,"not found!","not found!");
        return $this->createMessageToClient(200,"ok!",$result);
    }

    private function getAllContacts(){
        $decoded=authHandler::validateToken();
        if($decoded=="invalid token!" || $decoded=="expired token!") return $this->createMessageToClient("403","access denied!",$decoded);
        $result=Contact::getAllContacts();
        if(is_array($result)==false) return $this->createMessageToClient(404,"not found!","not found!");
        return $this->createMessageToClient(200,"ok!",$result);
    }

    private function addContact(){
        $authHandler=new authHandler("GET","Admin",null);
        $response=$authHandler->checkCorrectType();
        if($response["header"]!="HTTP/1.1 200 ok") return $response;
        $input = (array) json_decode(file_get_contents('php://input'), TRUE);
        $result=$this->validateContactForRegistration($input);
        if(is_array($result)){
            return $result;
        }
        Contact::addContact($input);
        return $this->createMessageToClient(201,"created!","ok");
    }

    private function updateContactById(){
        $authHandler=new authHandler("GET","Admin",null);
        $response=$authHandler->checkCorrectType();
        if($response["header"]!="HTTP/1.1 200 ok") return $response;
        $sql="SELECT * FROM `contacts` WHERE `contact_id`='$this->contact_id'";
        $db=new databaseController();
        $result=$db->getConnection()->query($sql);
        if($result->num_rows==0)return $this->createMessageToClient(404,"not found!","not found!");
        $input = (array) json_decode(file_get_contents('php://input'), TRUE);
        $result=$this->validateContactForUpdation($input);
        if(is_array($result)==true){
            return $result;
        }
        $result=Contact::updateContactById($this->contact_id,$input);
        if($result===true) return $this->createMessageToClient(200,"ok!","ok");
        else return $this->createMessageToClient(400,"bad command!","دستور نادرست!");
    }


    private function deleteContactById(){
        $authHandler=new authHandler("GET","Admin",null);
        $response=$authHandler->checkCorrectType();
        if($response["header"]!="HTTP/1.1 200 ok") return $response;
        $sql="SELECT * FROM `contacts` WHERE `contact_id`='$this->contact_id'";
        $db=new databaseController();
        $result=$db->getConnection()->query($sql);
        if($result->num_rows==0)return $this->createMessageToClient(404,"not found!","not found!");
        Contact::deleteContactById($this->contact_id);
        return $this->createMessageToClient(200,"ok!","ok");
    }

    private function validateContactForRegistration($input){
        if(!isset($input["fullname"]) || !isset($input["phone1"]) || !isset($input["home1"])||
            !isset($input["address"]) || !isset($input["email"])){
            return $this->createMessageToClient(403,"not allowed!","لطفا تمامی فیلد ها را پر کنید");
        }
        if(empty($input["fullname"]) || empty($input["phone1"]) || empty($input["home1"])||
            empty($input["address"]) || empty($input["email"])){
            return $this->createMessageToClient(403,"not allowed!","لطفا تمامی فیلد ها را پر کنید");
        }
        if(is_numeric($input["phone1"])==false){
            return $this->createMessageToClient(403,"not allowed!","لطفا تلفن معتبر وارد کنید");
        }
        if(is_numeric($input["home1"])==false){
            return $this->createMessageToClient(403,"not allowed!","لطفا تلفن معتبر وارد کنید");
        }
        if (!filter_var($input["email"], FILTER_VALIDATE_EMAIL)) {
            return $this->createMessageToClient(403,"invalid","لطفا پست الکترونیکی معتبر وارد کنید");
        }
        if(isset($input["phone2"])) {
            if (is_numeric($input["phone2"]) == false) {
                return $this->createMessageToClient(403, "not allowed!", "لطفا تلفن معتبر وارد کنید");
            }
        }
        if(isset($input["fax"])){
            if (is_numeric($input["fax"]) == false) {
                return $this->createMessageToClient(403, "not allowed!", "لطفا فکس معتبر وارد کنید");
            }
        }
        if(Contact::hasContactWithPhoneNumber($input["phone1"])){
            return $this->createMessageToClient(403,"invalid!","این شماره تلفن قبلا ثبت شده است");
        }
        if(Contact::hasContactWithEmail($input["email"])){
            return $this->createMessageToClient(403,"invalid!","این پست الکترونیکی قبلا ثبت شده است");
        }
        return true;
    }

    private function validateContactForUpdation($input){
        if(!isset($input["fullname"]) || !isset($input["phone1"]) || !isset($input["home1"])|| !isset($input["phone2"]) ||
            !isset($input["address"]) || !isset($input["email"])){
            return $this->createMessageToClient(403,"not allowed!","لطفا تمامی فیلد ها را پر کنید");
        }
        return true;
    }

    private function createMessageToClient($httpCode,$headerMessage,$body){
        $response["header"]="HTTP/1.1 ".$httpCode." ".$headerMessage;
        $response["body"]=$body;
        return $response;
    }
}