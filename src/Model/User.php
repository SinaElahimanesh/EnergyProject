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
}