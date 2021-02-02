<?php


class Patent {
    private $db;
    private $patentId;
    private $ownerId;

    function __construct() {
        $this->db=new databaseController();
    }

}