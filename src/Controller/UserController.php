<?php
require_once ("databaseController.php");

header("Content-Type: application/json; charset=UTF-8");

class UserController {
    private $requestMethod;
    private $userId;
    public function __construct($requestMethod,$userId=null) {
        $this->requestMethod = $requestMethod;
        $this->userId=$userId;
    }

    public function processRequest() {
        switch ($this->requestMethod) {
            case 'GET':
                if($this->userId) {
                    $response = $this->getUser($this->userId);
                } else {
                    $response = $this->getAllUsers();
                }
                break;
            case 'POST':
                $response = $this->createUserFromRequest();
                break;
            case 'PUT':
                $response = $this->updateUserFromRequest($this->userId);
                break;
            case 'DELETE':
                $response = $this->deleteUser($this->userId);
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

    private function getAllUsers() {
        $result = $this->findAll();
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = json_encode($result);
        return $response;
    }


    public function getUserByPhoneNumber($phoneNumber){
        $statement = "SELECT `accountId`,`password`,`enabled`,`type` FROM `users` WHERE `phoneNum`=$phoneNumber";
        try {
            $db=new databaseController();
            $statement = $db->getConnection()->prepare($statement);
            $statement->execute();
            $result=$statement->fetch(PDO::FETCH_ASSOC);
            if(count($result)==0) return null;
            return ($result);
        }catch (\PDOException $e){
            exit($e->getMessage());
        }
    }


    private function getUser($id) {
        $result = $this->findUser($id);
        if (! $result) {
            return $this->notFoundResponse();
        }
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = json_encode($result);
        return $response;
    }

    private function findAll() {
        // find all users
        $statement = "SELECT * FROM USERS;";
        try {
            $db= new databaseController();
            $statement= $db->getConnection()->query($statement);
            $result = $statement->fetchAll(\PDO::FETCH_ASSOC);

            return $result;
        } catch (\PDOException $e) {
            exit($e->getMessage());
        }
    }

    private function createUserFromRequest() {
        $input = (array) json_decode(file_get_contents('php://input'), TRUE);
        if (! $this->validateUser($input)) {
            return $this->unprocessableEntityResponse();
        }
        $this->insert($input);
        $response['status_code_header'] = 'HTTP/1.1 201 Created';
        $response['body'] = null;
        return $response;
    }

    private function insert(Array $input) {

        // insert a user to databaseController
        $db=new databaseController();
        $statement = "INSERT INTO USERS (phoneNum, password,fullname)
                    VALUES (:phoneNum, :password,:fullname);";
        try {
            $input["password"]=password_hash($db->makeSafe($input["password"]),PASSWORD_DEFAULT);
            $statement = $db->getConnection()->prepare($statement);
            $statement->execute(array(
                'phoneNum' => $input['phoneNum'],
                'password' => $input['password'],
                'fullname' => $input['fullname']
            ));
            return $statement->rowCount();
        } catch (\PDOException $e) {
            exit($e->getMessage());
        }
    }


    private function updateUserFromRequest($id) {
        $result = $this->findUser($id);
        if (! $result) {
            return $this->notFoundResponse();
        }
        $input = (array) json_decode(file_get_contents('php://input'), TRUE);
        if (! $this->validateUser($input)) {
            return $this->unprocessableEntityResponse();
        }
        $this->update($id, $input);
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = null;
        $user=$this->loadUserFromSession();
        $user->setEnabled(true);
        $this->saveUserObjectInSession($user);
        return $response;
    }

    private function update($id, Array $input) {
        // update user's data (for completing account information)
        $statement = "UPDATE USERS SET 
                     email= :email,
                     nationalCode= :nationalCode,
                     address= :address,
                     residence= :residence,
                     schoolName= :schoolName,
                     enabled= :enabled
                     WHERE id = :id;";
        try {
            $db=new databaseController();
            $statement = $db->getConnection()->prepare($statement);
            $statement->execute(array(
                'id' => (int) $id,
                'email' => $input['email'],
                'nationalCode' => $input['nationalCode'],
                'address' => $input['address'],
                'residence' => $input['residence'],
                'schoolName' => $input['schoolName'],
                'fullname'   => $input["fullname"],
                'enabled' => 1
            ));
            return $statement->rowCount();
        } catch (\PDOException $e) {
            exit($e->getMessage());
        }
    }


    private function deleteUser($id) {
        $result = $this->findUser($id);
        if (! $result) {
            return $this->notFoundResponse();
        }
        $this->delete($id);
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = null;
        return $response;
    }

    private function delete($id) {
        // delete a user
        $statement = "
            DELETE FROM USERS
            WHERE id = :id;
        ";
        try {
            $db=new databaseController();
            $statement = $db->getConnection()->prepare($statement);
            $statement->execute(array('id' => $id));
            return $statement->rowCount();
        } catch (\PDOException $e) {
            exit($e->getMessage());
        }
    }

    private function findUser($id) {
        // find an specific id
        $statement = "SELECT * FROM USERS WHERE accountId=?";
        try {
            $db=new databaseController();
            $statement = $db->getConnection()->prepare($statement);
            $statement->execute(array($id));
            $result = $statement->fetchAll(\PDO::FETCH_ASSOC);
            return $result;
        } catch (\PDOException $e) {
            exit($e->getMessage());
        }
    }



    private function validateUser($input) {
        if (! isset($input['phoneNum'])) {
            return false;
        }
        if (! isset($input['password'])) {
            return false;
        }
        return true;
    }

    public function saveUserObjectInSession($userObj){ /// at the end of each page!!!!
        if(session_status()==PHP_SESSION_NONE) {
            session_start();
        }
        $_SESSION["userObj"]=serialize($userObj);
        return session_id();
    }

    public function loadUserFromSession(){
        if(session_status()==PHP_SESSION_NONE) {
            session_start();
        }
        $userObj=unserialize($_SESSION["userObj"]);
        return $userObj;
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

