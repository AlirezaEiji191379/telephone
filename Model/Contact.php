<?php


class Contact
{

    public static function getContactById($id){
        $sql="SELECT * FROM `contacts` WHERE `contact_id`='$id'";
        $db=new databaseController();
        $result=$db->getConnection()->query($sql);
        if($result->num_rows==0){
            return false;
        }
        return json_encode($result->fetch_assoc());
    }

    public static function getAllContacts(){
        $sql="SELECT * FROM `contacts`";
        $db=new databaseController();
        $result=$db->getConnection()->query($sql);
        if($result->num_rows==0){
            return false;
        }
        $json="";
        $i=0;
        while ($row=$result->fetch_assoc()){
            $json[$i]=json_encode($row);
            $i++;
        }
        return $json;
    }

    public static function addContact($input){
        $fullname=databaseController::makeSafe($input["fullname"]);
        $phone1=databaseController::makeSafe($input["phone1"]);
        $phone2=databaseController::makeSafe($input["phone2"]);
        $home1=databaseController::makeSafe($input["home1"]);
        $fax=databaseController::makeSafe($input["fax"]);
        $email=databaseController::makeSafe($input["email"]);
        $address=databaseController::makeSafe($input["address"]);
        $sql="INSERT INTO `contacts` (`fullname`,`phone1`,`phone2`,`home1`,`fax`,`email`,`address`) VALUES
        (?,?,?,?,?,?,?)";
        $db=new databaseController();
        $statement=$db->getConnection()->prepare($sql);
        $statement->bind_param("sssssss",$fullname,$phone1,$phone2,$home1,$fax,$email,$address);
        $statement->execute();
    }


    public static function deleteContactById($id){
        $sql="DELETE FROM `contacts` WHERE `contact_id`='$id'";
        $db=new databaseController();
        return $db->getConnection()->query($sql);
    }

    public static function updateContactById($id,$input){
        $fullname=databaseController::makeSafe($input["fullname"]);
        $phone1=databaseController::makeSafe($input["phone1"]);
        $phone2=databaseController::makeSafe($input["phone2"]);
        $home1=databaseController::makeSafe($input["home1"]);
        $fax=databaseController::makeSafe($input["fax"]);
        $email=databaseController::makeSafe($input["email"]);
        $address=databaseController::makeSafe($input["address"]);
        $sql="UPDATE `contacts` SET (`fullname`,`phone1`,`phone2`,`home1`,`fax`,`email`,`address`) VALUES
        (?,?,?,?,?,?,?)";
        $db=new databaseController();
        $statement=$db->getConnection()->prepare($sql);
        $statement->bind_param("sssssss",$fullname,$phone1,$phone2,$home1,$fax,$email,$address);
        $statement->execute();
    }



}