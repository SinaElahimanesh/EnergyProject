<?php


class loginController
{

    private $db;

    public function __construct()
    {
        $db=new databaseController();
    }

    public function login(){
        $input = (array) json_decode(file_get_contents('php://input'), TRUE);
        $phone=$input["phone"];
        $password=$input["password"];
        $user=new User();



    }

    private function registerLoginUserSession($accountId,$sessionId){

    }


    public function sessionBasedLogin(){
    }


}