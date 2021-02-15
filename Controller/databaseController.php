<?php


class databaseController
{
    private $username="alirezaeiji151379";
    private $password="alirezaeiji";
    private $dbname="telephone";
    private $servername="localHost";

    private $connection=null;
    function __construct()
    {
        $this->connection=new mysqli($this->servername,$this->username,$this->password,$this->dbname);
        if ($this->connection->connect_error) {
            die("Connection failed: " . $this->connection->connect_error);
        }
    }

   public static function makeSafe($input){
        return stripcslashes(htmlspecialchars(trim($input)));
    }

    public function closeConnection(){
        $this->connection->close();
    }

    function __destruct()
    {
        // TODO: Implement __destruct() method.
        //$this->connection->close();
    }

    public function getConnection(){
        return $this->connection;
    }

}