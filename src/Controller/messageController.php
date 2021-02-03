<?php


class messageController{

    private $senderId;
    private $receiverId;
    private $requestMethod;


    public function __construct($senderId, $receiverId, $requestMethod)
    {
        $this->senderId = $senderId;
        $this->receiverId = $receiverId;
        $this->requestMethod = $requestMethod;
    }


    private function getMessageBySenderId(){



    }
    private function getAllMessageForUser(){

    }

    private function sendMessageToUser(){

    }



}