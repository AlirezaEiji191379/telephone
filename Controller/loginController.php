<?php
header("Content-Type: application/json; charset=UTF-8");
require_once ("../Model/User.php");
require_once ("authHandler.php");

class loginController
{
    private $requestMethod;
    function __construct($requestMethod)
    {
        $this->requestMethod=$requestMethod;
    }

    public function requestProcess(){
        $response=null;
        if($this->requestMethod=="POST"){
            $response=$this->login();
        }
        if($this->requestMethod=="DELETE"){
            $response=$this->logout();
        }

        header($response["header"]);
        echo json_encode($response["body"],JSON_UNESCAPED_UNICODE );
    }

    private  function login(){
        $input = (array) json_decode(file_get_contents('php://input'), TRUE);
        if($this->validateLoginInput($input)==false){
            return $this->createMessageToClient(403,"Forbidden","wrong username or password!");
        }
        $username=$input["username"];
        $password=$input["password"];
        $result=User::getUserByUsername($username);
        if(is_array($result)==false){
           return $this->createMessageToClient(404,"Not Found","نام کاربری یا رمز عبور اشتباه است");
        }
        if(password_verify($password,$result["password"])==false){
            return  $this->createMessageToClient(403,"Forbidden","نام کاربری یا رمز عبور اشتباه است");
        }
        if($result["status"]!=1){
            return  $this->createMessageToClient(403,"access denied!","حساب کاربری شما فعال نشده است لطفا از طریق ایمیل خود اقدام کنید!");
        }
        $user=new User($result["user_id"],$result["type"]);
        $token=authHandler::generateJwtAccessTokenForUser($user);
        $refresh=authHandler::generateJwtRefreshTokenForUser($user);
        setcookie("refreshToken",$refresh,time()+604800,null,null,false,true);/// needs to be changed!
        $arr["accessToken"]=$token;
        $arr["type"]=$user->getType();
        return  $this->createMessageToClient(201,"created",$arr);
    }

    private function logout(){
        $token=authHandler::getBearerToken();
        $decoded=authHandler::validateToken();
        if($decoded=="invalid token!" || $decoded=="expired token!") return $this->createMessageToClient("403","access denied!",$decoded);
        $refreshToken=$_COOKIE["refreshToken"];
        $db=new authDB();
        $sql="INSERT INTO `black_list` (`access_id`,`expires_at`) VALUES ('$token','$decoded->expire')";
        $db->getConnection()->query($sql);
        $sql="DELETE FROM `refreshtokens` WHERE `refresh_id`= '$refreshToken'";
        $db->getConnection()->query($sql);
        unset($_COOKIE["refreshToken"]);
        return $this->createMessageToClient("200","ok","ok");
    }


    private function validateLoginInput($input){
        if(!isset($input["username"]) || !isset($input["password"])){
            return false;
        }
        return true;
    }

    private function createMessageToClient($httpCode,$headerMessage,$body){
        $response["header"]="HTTP/1.1 ".$httpCode." ".$headerMessage;
        $response["body"]=$body;
        return $response;
    }






}