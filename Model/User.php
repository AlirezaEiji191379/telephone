<?php


class User
{
    private $userId;
    function __construct($userId)
    {
        $this->userId=$userId;
    }


    public function getUserId()
    {
        return $this->userId;
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
        $query="SELECT `user_id`,`username`,`password` FROM `user` WHERE `username`=?";
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
        $query="CREATE TABLE $username (
        contact_id INT(20)  AUTO_INCREMENT PRIMARY KEY,
        fullname VARCHAR(80) NOT NULL,
        phone1 VARCHAR(11) UNIQUE NOT NULL,
        phone2 VARCHAR(11) UNIQUE NOT NULL,
        home1 VARCHAR(10)  NULL,
        home2 VARCHAR(10)  NULL,
        fax VARCHAR(20) NULL,
        email VARCHAR(60) NULL UNIQUE,
        address TEXT NULL,
        workPlace VARCHAR(80) NULL
        )";
        $db=new databaseController();
        $db->getConnection()->query($query);
        $query="INSERT INTO `user` (`username`,`email`,`password`,`firstname`,`lastname`,`phone_number`) VALUES (?,?,?,?,?,?)";
        $statement=$db->getConnection()->prepare($query);
        $statement->bind_param("ssssss",$username,$email,$password,$firstname,$lastname,$phoneNumber);
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






}