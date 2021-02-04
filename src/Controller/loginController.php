<?php
require_once ("databaseController.php");
require_once ("UserController.php");
require_once ("../Model/User.php");
header("Content-Type: application/json; charset=UTF-8");

class loginController
{


    private $requestMethod;
    public function __construct($requestMethod)
    {
        $this->requestMethod=$requestMethod;
    }

    public function processRequest(){
        switch ($this->requestMethod) {

            case "POST":
                $response=$this->login();
                break;
            case "DELETE":
                $response=$this->logout();
                break;
            default:
                $response = $this->notFoundResponse();
                break;
        }
        header($response['status_code_header']);
        if($response['body']) {
            echo $response['body'];
        }
    }

    private function login(){ //// login normal az tarighe safhe adi!
        $input = (array) json_decode(file_get_contents('php://input'), TRUE);
        $phone=$input["phoneNum"];
        $password=$input["password"];
        $userController=new UserController(null,null);
        $result=$userController->getUserByPhoneNumber($phone);
        if(count($result)==0) {
          return  $this->notFoundResponse();
        }
        if(password_verify($password,$result["password"])==false){
          return $this->unprocessableEntityResponse();
        }
        $user=new User($result["accountId"],$result["type"],$result["enabled"],1);
        $accountId=$result["accountId"];
        //$current_time=time();
        $lifetime=604800;
        session_set_cookie_params($lifetime);
        $sessionId=$userController->saveUserObjectInSession($user);
        $db=new databaseController();
        $statement="INSERT INTO `users_sessions` (`sessionId`,`accountId`) VALUES (:sessionId,:accountId)";
        $statement=$db->getConnection()->prepare($statement);
        $statement->execute(array(
            'sessionId' => $sessionId,
            'accountId' => $accountId
        ));
        $response['status_code_header'] = 'HTTP/1.1 200 ok';
        $response['body'] = null;
        return $response;
    }

//    public function validateUser($expectedClientType){
//        $userController=new UserController();
//        $client=$userController->loadUserFromSession();
//        if($expectedClientType!=$client->type) {
//            return $this->unprocessableEntityResponse();
//        }
//        $result=$this->sessionBasedLogin();
//        if($result!=true){
//            return $result;
//        }
//        $response['status_code_header'] = 'HTTP/1.1 200 User is Valid';
//        $response['body'] = null;
//        return $response;
//    }




    private function logout(){
        if(session_status()==PHP_SESSION_NONE) {
            session_start();
        }
        $sessionId=session_id();
        $db=new databaseController();
        $statement="DELETE FROM `users_sessions` WHERE `sessionId`=$sessionId";
        $db->getConnection()->exec($statement);
        session_destroy();
        unset($_COOKIE[session_name()]);
        $response['status_code_header'] = 'HTTP/1.1 200 ok';
        $response['body'] = null;
        return $response;
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