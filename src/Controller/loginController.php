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
        $userController=new UserController();
        $result=$userController->getUserByPhoneNumber($phone);



    }

    private function registerLoginUserSession($accountId,$sessionId){

    }


    public function sessionBasedLogin(){
    }

    private function unprocessableEntityResponse()
    {
        $response['status_code_header'] = 'HTTP/1.1 422 Unprocessable Entity';
        $response['body'] = json_encode([
            'error' => 'Invalid input'
        ]);
        return $response;
    }

    private function notFoundResponse() {
        $response['status_code_header'] = 'HTTP/1.1 404 Not Found';
        $response['body'] = null;
        return $response;
    }


}