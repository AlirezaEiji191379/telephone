<?php

header("Content-Type: application/json; charset=UTF-8");
require_once ('../libs/PHPMailer-5.2.16/PHPMailerAutoload.php');
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
        echo json_encode($response["body"], JSON_UNESCAPED_UNICODE );
    }

    private function sendEmailForRegistration(){
        $key="92?VH2WMrx";
        $input = (array) json_decode(file_get_contents('php://input'), TRUE);
        $result=$this->checkValidation($input);
        if(is_array($result))return $result;
        $input["verificationCode"]=$this->createVerificationCodeForUser();
        User::createUser($input);
        $issued_at = time();
        $expiration_time = $issued_at + (900);
        $payload=array(
            "start"=>$issued_at,
            "expire"=>$expiration_time,
            "data"=>array(
                "verificationCode"=>$input["verificationCode"]
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
        $key="92?VH2WMrx";
        $url=\Firebase\JWT\JWT::decode($this->urlToRegister,$key,array("HS256"));
        if(time()>$url->expire){
            return $this->createMessageToClient(403,"access denied!","access denied!");
        }
        User::enableUser($url->data->verificationCode);
        header("Location: http://localhost/telephone_project/View/login/index.html?register=1");
        //return $this->createMessageToClient(200,"created!","successfully created!");
    }


    private function checkValidation($input){
        if(!isset($input["username"]) || !isset($input["password"]) || !isset($input["email"])||
            !isset($input["phoneNumber"]) || !isset($input["firstname"]) || !isset($input["lastname"])){
            return $this->createMessageToClient(403,"not allowed!","لطفا همه فیلد ها را کامل پر کنید.");
        }
        if(strlen($input["username"])<8 || strlen($input["username"])>20){
            return $this->createMessageToClient(403,"invalid","نام کاربری باید بین 8 تا 20 حرف باشد.");
        }

        if(strlen($input["password"])<8 || strlen($input["password"])>20){
            return $this->createMessageToClient(403,"invalid","رمز عبور باید بین 8 تا 20 حرف باشد!");
        }

        if(preg_match('/^[a-zA-Z0-9]{5,}$/', $input["username"]) == false) {
            return $this->createMessageToClient(403,"invalid","نام کاربری باید فقط شامل حروف الفبا و عدد باشد.");
        }
        if(preg_match('/^[a-zA-Z0-9]{5,}$/', $input["password"]) == false) {
            return $this->createMessageToClient(403,"invalid","رمز عبور باید فقط شامل حروف الفبا و عدد باشد.");
        }

        if(is_numeric($input["phoneNumber"]) ==false){
            return $this->createMessageToClient(403,"invalid","لطفا شماره تلفن همراه معتبر وارد کنید");
        }

        if (!filter_var($input["email"], FILTER_VALIDATE_EMAIL)) {
            return $this->createMessageToClient(403,"invalid","لطفا پست الکترونیکی معتبر وارد کنید");
        }

        if(User::hasUserWithUsername($input["username"])){
            return $this->createMessageToClient(403,"invalid!","این نام کاربری قبلا ثبت شده است");
        }
        if(User::hasUserWithPhoneNumber($input["phoneNumber"])){
            return $this->createMessageToClient(403,"invalid!","این شماره تلفن قبلا ثبت شده است");
        }
        if(User::hasUserWithEmail($input["email"])){
            return $this->createMessageToClient(403,"invalid!","این پست الکترونیکی قبلا ثبت شده است");
        }
        return true;
    }

    private function createVerificationCodeForUser(){
        $code=rand(10000,1000000);
        $result=User::hasUserWithVerificationCode($code);
        if($result==true){
            $this->createVerificationCodeForUser();
        }
        return $code;
    }

    private function createMessageToClient($httpCode,$headerMessage,$body){
        $response["header"]="HTTP/1.1 ".$httpCode." ".$headerMessage;
        $response["body"]=$body;
        return $response;
    }



}