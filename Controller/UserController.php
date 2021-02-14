<?php

header("Content-Type: application/json; charset=UTF-8");
require '../libs/PHPMailer-5.2.16/PHPMailerAutoload.php';
require_once ("../Model/User.php");
require_once ("databaseController.php");
require_once ('../libs/php-jwt-master/src/BeforeValidException.php');
require_once ('../libs/php-jwt-master/src/ExpiredException.php');
require_once ('../libs/php-jwt-master/src/SignatureInvalidException.php');
require_once ('../libs/php-jwt-master/src/JWT.php');

class UserController
{

    private $requestMethod;
    private $urlToRegister;
    function __construct($requestMethod,$urlToRegister=null)
    {
        $this->requestMethod=$requestMethod;
        $this->urlToRegister=$urlToRegister;
    }


    public function requestProcess(){
        $response=null;
        if($this->requestMethod=="POST")
            $response=$this->sendEmailForRegistration();
        if($this->urlToRegister!=null && $this->requestMethod=="GET"){
            $response=$this->registerUser();
        }
        header($response["header"]);
        echo json_encode($response["body"]);
    }

    private function sendEmailForRegistration(){
        $key="92?VH2WMrx";
        $input = (array) json_decode(file_get_contents('php://input'), TRUE);
        $result=$this->checkValidation($input);
        if(is_array($result))return $result;

        $issued_at = time();
        $expiration_time = $issued_at + (900);
        $payload=array(
            "start"=>$issued_at,
            "expire"=>$expiration_time,
            "data"=>array(
                "username"=>$input["username"],
                "password"=>$input["password"],
                "email"=>$input["email"],
                "phone_number"=>$input["phoneNumber"],
                "firstname"=>$input["firstname"],
                "lastname"=>$input["lastname"]
            )
        );
        $key="92?VH2WMrx";
        $queryString=\Firebase\JWT\JWT::encode($payload,$key);
        $url="http://localhost/telephone_project/Controller/mainController.php/User?user=".$queryString;
        $email=$input["email"];
        $mail = new PHPMailer;
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'alirezaeiji191379@gmail.com';
        $mail->Password = 'aqvootqbfziukjce';
        $mail->SMTPSecure = 'tls';
        $mail->From = 'alirezaeiji191379@gmail.com';
        $mail->FromName = 'sazaman tablighat';
        $mail->addAddress($email, 'Alireza Eiji');
        $mail->WordWrap = 50;
        $mail->isHTML(true);
        $mail->Port = 587;
        $mail->Subject = 'ایمیل فعال سازی';
        $mail->Body    =" 
        <!DOCTYPE html>
        <html lang='en'>
        <head>
            <meta charset='UTF-8'>
            <title>Title</title>
        </head>
        <body>
            <div>برای فعال سازی حساب کاربری خود بر روی لینک زیر کلیک کنید!</div>
            <a href=$url>لینک فعال سازی</a>
         ";
        if(!$mail->send()) {
            echo 'Message could not be sent.';
            echo 'Mailer Error: ' . $mail->ErrorInfo;
            exit;
        }
        return $this->createMessageToClient(200,"emailSent!","email sent successfully!");
    }

    private function registerUser(){
        $key='92?VH2WMrx';
        $url=\Firebase\JWT\JWT::decode($this->urlToRegister,$key,array("HS256"));
        if(time()>$url->expire){
            return $this->createMessageToClient(403,"access denied!","access denied!");
        }
        $input["username"]=$url->data->username;
        $input["password"]=$url->data->password;
        $input["email"]=$url->data->email;
        $input["phone_number"]=$url->data->phone_number;
        $input["firstname"]=$url->data->firstname;
        $input["lastname"]=$url->data->lastname;
        User::createUser($input);
        return $this->createMessageToClient(200,"created!","successfully created!");
    }


    private function checkValidation($input){
        if(!isset($input["username"]) || !isset($input["password"]) || !isset($input["email"])||
            !isset($input["phoneNumber"]) || !isset($input["firstname"]) || !isset($input["lastname"])){
            return $this->createMessageToClient(403,"not allowed!","please complete inputs!");
        }
        if(strlen($input["username"])<8 || strlen($input["username"])>20){
            return $this->createMessageToClient(403,"invalid","invalid username!");
        }

        if(strlen($input["password"])<8 || strlen($input["password"])>20){
            return $this->createMessageToClient(403,"invalid","invalid password!");
        }

        if(preg_match('/^[a-zA-Z0-9]{5,}$/', $input["username"]) == false) {
            return $this->createMessageToClient(403,"invalid","the username must have only alphanumeric characters!");
        }
        if(preg_match('/^[a-zA-Z0-9]{5,}$/', $input["password"]) == false) {
            return $this->createMessageToClient(403,"invalid","the password must have only alphanumeric characters!");
        }

        if(is_numeric($input["phoneNumber"]) ==false){
            return $this->createMessageToClient(403,"invalid","please enter valid phone number!");
        }

        if (!filter_var($input["email"], FILTER_VALIDATE_EMAIL)) {
            return $this->createMessageToClient(403,"invalid","please enter valid email!");
        }

        if(User::hasUserWithUsername($input["username"])){
            return $this->createMessageToClient(403,"invalid!","this username was registered in the system!");
        }
        if(User::hasUserWithPhoneNumber($input["phoneNumber"])){
            return $this->createMessageToClient(403,"invalid!","this phoneNumber was registered in the system!");
        }
        if(User::hasUserWithEmail($input["email"])){
            return $this->createMessageToClient(403,"invalid!","this email was registered in the system!");
        }
        return true;
    }

    private function createMessageToClient($httpCode,$headerMessage,$body){
        $response["header"]="HTTP/1.1 ".$httpCode." ".$headerMessage;
        $response["body"]=$body;
        return $response;
    }



}