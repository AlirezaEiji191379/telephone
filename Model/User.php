<?php


class User
{
    private $userId;
    private $type;
    function __construct($userId,$type)
    {
        $this->userId=$userId;
        $this->type=$type;
    }


    public function getUserId()
    {
        return $this->userId;
    }


    public function getType()
    {
        return $this->type;
    }

    public static function getUserById($id){
        $query="SELECT * FROM `user` WHERE `user_id`=?";
        $db=new databaseController();
        $statement=$db->getConnection()->prepare($query);
        $statement->bind_param("i",$id);
        $statement->execute();
        $result=$statement->get_result();
        if($result->num_rows>0){
            return $result->fetch_assoc();
        }else{
            return "not Found!";
        }
    }

    public static function getUserByUsername($username){
        $query="SELECT `user_id`,`username`,`password`,`status`,`type` FROM `user` WHERE `username`=?";
        $db=new databaseController();
        $statement=$db->getConnection()->prepare($query);
        $statement->bind_param("s",$username);
        $statement->execute();
        $result=$statement->get_result();
        if($result->num_rows>0){
            return $result->fetch_assoc();
        }else{
            return "not Found!";
        }
    }

    public static function createUser($input){ /// input is array
        $username=databaseController::makeSafe($input["username"]);
        $email=databaseController::makeSafe($input["email"]);
        $password=password_hash(databaseController::makeSafe($input["password"]),PASSWORD_DEFAULT);
        $firstname=databaseController::makeSafe($input["firstname"]);
        $lastname=databaseController::makeSafe($input["lastname"]);
        $phoneNumber=databaseController::makeSafe($input["phoneNumber"]);
        $verificationCode=$input["verificationCode"];
        $query="INSERT INTO `user` (`username`,`email`,`password`,`firstname`,`lastname`,`phone_number`,`verificationCode`) VALUES (?,?,?,?,?,?,?)";
        $db=new databaseController();
        $db->getConnection()->query($query);
        $statement=$db->getConnection()->prepare($query);
        $statement->bind_param("sssssss",$username,$email,$password,$firstname,$lastname,$phoneNumber,$verificationCode);
        $statement->execute();
    }


    public static function hasUserWithUsername($username){
        $query="SELECT * FROM `user` WHERE `username`=?";
        $db=new databaseController();
        $statement=$db->getConnection()->prepare($query);
        $statement->bind_param("s",$username);
        $statement->execute();
        $result=$statement->get_result();
        if($result->num_rows>0){
            return true;
        }else{
            return false;
        }
    }

    public static function hasUserWithEmail($email){
        $query="SELECT * FROM `user` WHERE `email`=?";
        $db=new databaseController();
        $statement=$db->getConnection()->prepare($query);
        $statement->bind_param("s",$email);
        $statement->execute();
        $result=$statement->get_result();
        if($result->num_rows>0){
            return true;
        }else{
            return false;
        }
    }

    public static function hasUserWithPhoneNumber($phoneNumber){
        $query="SELECT * FROM `user` WHERE `phone_number`=?";
        $db=new databaseController();
        $statement=$db->getConnection()->prepare($query);
        $statement->bind_param("s",$phoneNumber);
        $statement->execute();
        $result=$statement->get_result();
        if($result->num_rows>0){
            return true;
        }else{
            return false;
        }
    }

    public static function hasUserWithVerificationCode($verification){
        $query="SELECT * FROM `user` WHERE `status`=0 AND `verificationCode`=?";
        $db=new databaseController();
        $statement=$db->getConnection()->prepare($query);
        $statement->bind_param("s",$phoneNumber);
        $statement->execute();
        $result=$statement->get_result();
        if($result->num_rows>0){
            return true;
        }else{
            return false;
        }
    }

    public static function enableUser($verification){
        $query="UPDATE `user` SET `status`='1' WHERE `status`='0' AND `verificationCode`=?";
        $db=new databaseController();
        $statement=$db->getConnection()->prepare($query);
        $statement->bind_param("s",$verification);
        $statement->execute();
        $result=$statement->get_result();
        if($result===true){
            return true;
        }
        return false;
    }






}