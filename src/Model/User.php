<?php
require_once("../Controller/databaseController.php");

class User {

    private $userId;
    private $authenticated;
    private $enabled;
    private $type;

    public function __construct($userId,$type,$enabled,$authenticated) {
        $this->userId=$userId;
        $this->type=$type;
        $this->enabled=$enabled;
        $this->authenticated=$authenticated;
    }

    public function getUserId()
    {
        return $this->userId;
    }

    public function getAuthenticated()
    {
        return $this->authenticated;
    }


    public function setAuthenticated($authenticated)
    {
        $this->authenticated = $authenticated;
    }


    public function getEnabled()
    {
        return $this->enabled;
    }


    public function setEnabled($enabled)
    {
        $this->enabled = $enabled;
    }


    public function getType()
    {
        return $this->type;
    }

    public function setType($type)
    {
        $this->type = $type;
    }

}