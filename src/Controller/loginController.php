<?php
header("Content-Type: application/json; charset=UTF-8");

class loginController
{


    private $requestMethod;
    private $expectedClientType;
    public function __construct($requestMethod,$expectedClientType=null)
    {
        $this->requestMethod=$requestMethod;
        $this->expectedClientType=$expectedClientType;
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
        $phone=$input["phone"];
        $password=$input["password"];
        $userController=new UserController();
        $result=$userController->getUserByPhoneNumber($phone);
        if(count($result)==0) {
          return  $this->notFoundResponse();
        }
        if(password_verify($password,$result["password"])==false){
          return $this->unprocessableEntityResponse();
        }
        $user=new User($result["accountId"],$result["type"],$result["enabled"],1);
        $accountId=$result["accountId"];
        $current_time=time();
        $lifetime=604800;
        session_set_cookie_params($lifetime);
        $sessionId=$userController->saveUserObjectInSession($user);
        $db=new databaseController();
        $db->getConnection()->query("REPLACE INTO `users_sessions` (`sessionId`,`accountId`,`loginTime`) VALUES
        ($sessionId,$accountId,$current_time)");
        $response['status_code_header'] = 'HTTP/1.1 200 logged In';
        $response['body'] = null;
        return $response;
    }

    public function validateUser($expectedClientType){
        $userController=new UserController();
        $client=$userController->loadUserFromSession();
        if($expectedClientType!=$client->type) {
            return $this->unprocessableEntityResponse();
        }
        $result=$this->sessionBasedLogin();
        if($result!=true){
            return $result;
        }
        $response['status_code_header'] = 'HTTP/1.1 200 User is Valid';
        $response['body'] = null;
        return $response;
    }

    ///// tu har pagi bayas estefade she!
    private function sessionBasedLogin(){ /// inja bayad bad az ye hafte user ru part kone biron va redirect kone safhe login!
           if(isset($_COOKIE[session_name()])==false){
               return false;
           }
        $sessionId=$_COOKIE[session_name()];
        $db=new databaseController();
        $statement=$db->getConnection()->prepare("SELECT `loginTime` FROM `users_sessions` WHERE `sessionId`=$sessionId");
        $statement->execute();
        $result = $statement->setFetchMode(PDO::FETCH_ASSOC);
            if(time()-$result["loginTime"]>604800){
                $statement="DELETE FROM `users_sessions` WHERE `sessionId`=$sessionId";
                $db->getConnection()->exec($statement);
                return false;
            }
        $userController=new UserController();
        $user=$userController->loadUserFromSession();
        $user->setAuthenticated(1);
        $userController->saveUserObjectInSession($user);
        return true;
    }

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
        $response['status_code_header'] = 'HTTP/1.1 200 logged Out';
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