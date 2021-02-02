<?php


class Patent {
    private $patentId;
    private $ownerId;

    function __construct($patentId,$ownerId) {
        $this->patentId=$patentId;
        $this->ownerId=$ownerId;
    }

    public function getPatentId()
    {
        return $this->patentId;
    }

    public function setPatentId($patentId)
    {
        $this->patentId = $patentId;
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