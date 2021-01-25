<?php


class database
{
    private $connection;

    function __construct()
    {
       $this->connection = new mysqli("localHost","alirezaeiji151379","alirezaeiji","energy_Project");
    }

    function __destruct()
    {
        // TODO: Implement __destruct() method.
        $this->connection->close();
    }

    public function query($queryStr){
        if(is_null($this->connection)){
            return;
        }
        else{
            return $this->connection->query($queryStr);
        }
    }

    public function makeSafe($input){
        return stripcslashes(htmlspecialchars(trim($input)));
    }


}