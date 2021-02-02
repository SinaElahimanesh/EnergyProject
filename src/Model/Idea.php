<?php


class Idea {
    private $db;
    private $ideaId;
    private $ownerId;

    function __construct() {
        $db=new databaseController();
    }

}