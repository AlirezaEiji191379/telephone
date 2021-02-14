<?php


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
        echo json_encode($response["body"]);
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
        if($result==false) return $this->createMessageToClient(404,"not found!","not found!");
        return $this->createMessageToClient(200,"ok!",$result);
    }

    private function addContact(){
        $authHandler=new authHandler("GET","Admin",null);
        $response=$authHandler->checkCorrectType();
        if($response["header"]!=200) return $response;
        $input = (array) json_decode(file_get_contents('php://input'), TRUE);
        $result=$this->validateContact();
        if($result!=true){
            return $result;
        }
        Contact::addContact($input);
        return $this->createMessageToClient(201,"created!","ok");
    }

    private function updateContactById(){
        $authHandler=new authHandler("GET","Admin",null);
        $response=$authHandler->checkCorrectType();
        if($response["header"]!=200) return $response;
        $sql="SELECT * FROM `contacts` WHERE `contact_id`='$this->contact_id'";
        $db=new databaseController();
        $result=$db->getConnection()->query($sql);
        if($result->num_rows==0)return $this->createMessageToClient(404,"not found!","not found!");
        $input = (array) json_decode(file_get_contents('php://input'), TRUE);
        $result=$this->validateContact();
        if($result!=true){
            return $result;
        }
        Contact::updateContactById($this->contact_id,$input);
        return $this->createMessageToClient(200,"ok!","ok");
    }


    private function deleteContactById(){
        $authHandler=new authHandler("GET","Admin",null);
        $response=$authHandler->checkCorrectType();
        if($response["header"]!=200) return $response;
        $sql="SELECT * FROM `contacts` WHERE `contact_id`='$this->contact_id'";
        $db=new databaseController();
        $result=$db->getConnection()->query($sql);
        if($result->num_rows==0)return $this->createMessageToClient(404,"not found!","not found!");
        Contact::deleteContactById($this->contact_id);
        return $this->createMessageToClient(200,"ok!","ok");
    }

    private function validateContact(){
        if(!isset($input["fullname"]) || !isset($input["phone1"]) || !isset($input["home1"])||
            !isset($input["address"]) || !isset($input["email"])){
            return $this->createMessageToClient(403,"not allowed!","please complete inputs!");
        }
        return true;
    }

    private function createMessageToClient($httpCode,$headerMessage,$body){
        $response["header"]="HTTP/1.1 ".$httpCode." ".$headerMessage;
        $response["body"]=$body;
        return $response;
    }
}