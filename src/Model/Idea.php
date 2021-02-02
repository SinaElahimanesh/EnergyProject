<?php


class Idea {


    private $ideaId;
    private $ownerId;

    function __construct($ideaId,$ownerId) {
        $this->ideaId=$ideaId;
        $this->ownerId=$ownerId;
    }


    public function getIdeaId()
    {
        return $this->ideaId;
    }


    public function setIdeaId($ideaId)
    {
        $this->ideaId = $ideaId;
    }


    public function getOwnerId()
    {
        return $this->ownerId;
    }

    public function setOwnerId($ownerId)
    {
        $this->ownerId = $ownerId;
    }

}