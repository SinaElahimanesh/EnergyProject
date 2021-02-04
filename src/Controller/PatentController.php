<?php
require_once ("../Model/User.php");
header("Content-Type: application/json; charset=UTF-8");

class PatentController {

    private $requestMethod;
    private $patentId;
    private $ownerId;
    private $currentUser;
    public function __construct($requestMethod, $patentId, $ownerId,$currentUser=null) {
        $this->requestMethod = $requestMethod;
        $this->patentId = $patentId;
        $this->ownerId = $ownerId;
        $this->currentUser=$currentUser;
    }

    public function processRequest() {
        switch ($this->requestMethod) {
            case 'GET':
                if($this->patentId) {
                    $response = $this->getPatent($this->patentId);
                } else if($this->ownerId) {
                    $response = $this->getAllPatentsOfAUser($this->ownerId);
                } else {
                    $response = $this->getAllPatents();
                }
                break;
            case 'POST':
                $response = $this->createPatentFromRequest();
                break;
            case 'PUT':
                $response = $this->updatePatentFromRequest($this->patentId);
                break;
            case 'DELETE':
                $response = $this->deletePatent($this->patentId);
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

    private function getPatent($id) {
        $result = $this->findPatent($id);
        if (! $result) {
            return $this->notFoundResponse();
        }
        if($this->currentUser->getType()=="Student" && $result["ownerId"]!=$this->currentUser->getUserId()){
            return $this->unprocessableEntityResponse();
        }
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = json_encode($result);
        return $response;
    }

    private function getAllPatentsOfAUser($id) {
        $result = $this->findAllPatentsOfAUser($id);
        if (! $result) {
            return $this->notFoundResponse();
        }
        if($this->currentUser->getType()=="Student" && $id!=$this->currentUser->getUserId()){
            return $this->unprocessableEntityResponse();
        }
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = json_encode($result);
        return $response;
    }

    private function getAllPatents() {
        if($this->currentUser->getType()=="Student"){
            return $this->unprocessableEntityResponse();
        }
        $result = $this->findAllPatents();
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = json_encode($result);
        return $response;
    }


    private function createPatentFromRequest() {
        $input = (array) json_decode(file_get_contents('php://input'), TRUE);
        if (! $this->validatePatent($input)) {
            return $this->unprocessableEntityResponse();
        }
        $this->insert($input);
        $response['status_code_header'] = 'HTTP/1.1 201 Created';
        $response['body'] = null;
        return $response;
    }

    private function updatePatentFromRequest($id) {
        $result = $this->findPatent($id);
        if (! $result) {
            return $this->notFoundResponse();
        }
        $input = (array) json_decode(file_get_contents('php://input'), TRUE);
        if (! $this->validatePatent($input)) {
            return $this->unprocessableEntityResponse();
        }
        if(array_key_exists ( 'expertId' ,  $input )) {
            $this->updateExpert($id, $input);
        } else if(array_key_exists ( 'extraResources' ,  $input )) {
            $this->updateExtraResources($id, $input);
        } else if(array_key_exists ( 'patentStatus' ,  $input )) {
            $this->updateStatus($id, $input);
        }
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = null;
        return $response;
    }

    private function deletePatent($id) {
        $result = $this->findPatent($id);
        if (! $result) {
            return $this->notFoundResponse();
        }
        if($this->currentUser->getType()=="Student" && $result["ownerId"]!=$this->currentUser->getUserId()){
            return $this->unprocessableEntityResponse();
        }
        $this->delete($id);
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = null;
        return $response;
    }

    private function validatePatent($input) {
        if (! isset($input['id'])) {
            return false;
        }
        return true;
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


    private function findAllPatents() {
        // find all patents of all students
        $statement = "SELECT * FROM PATENTS;";
        try {
            $db=new databaseController();
            $statement= $db->getConnection()->query($statement);
            $result = $statement->fetchAll(\PDO::FETCH_ASSOC);
            return $result;
        } catch (\PDOException $e) {
            exit($e->getMessage());
        }
    }

    private function findAllPatentsOfAUser($id) {
        // find all patents of a user with ID $id
        $statement = "SELECT * FROM PATENTS WHERE ownerId=?;";
        try {
            $db=new databaseController();
            $statement= $db->getConnection()->query($statement);
            $statement->execute(array($id));
            $result = $statement->fetchAll(\PDO::FETCH_ASSOC);
            return $result;
        } catch (\PDOException $e) {
            exit($e->getMessage());
        }
    }

    private function findPatent($id) {
        // find an specific patent of a user
        $statement = "SELECT * FROM PATENTS WHERE patentId=?;";
        try {
            $db=new databaseController();
            $statement= $db->getConnection()->query($statement);
            $statement->execute(array($id));
            $result = $statement->fetchAll(\PDO::FETCH_ASSOC);
            return $result;
        } catch (\PDOException $e) {
            exit($e->getMessage());
        }
    }

    private function insert(Array $input) {

        // insert a patent to databaseController
        $statement = "INSERT INTO PATENTS (patent_id,patent_name, ownerId, expertId, patentStatus, description, extraResources)
                    VALUES (:patent_id,:patent_name ,:ownerId, :expertId, :patentStatus, :description, :extraResources);";
        try {
            $db=new databaseController();
            $statement = $db->getConnection()->prepare($statement);
            $statement->execute(array(
                'patent_id' => $input['patent_id'],
                'patent_name'=>$input['patent_name'],
                'ownerId' => $input['ownerId'],
                'patentStatus' => 'START',
                'description' => $input['description'],
                'extraResources' => $input['extraResources'],
            ));
            return $statement->rowCount();
        } catch (\PDOException $e) {
            exit($e->getMessage());
        }
    }

    private function updateExpert($id, Array $input) {
        // update patent's data (EXPERT)
        $statement = "UPDATE PATENTS SET 
                     expertId= :expertId,
                     WHERE id = :id;";
        try {
            $db=new databaseController();
            $statement = $db->getConnection()->prepare($statement);
            $statement->execute(array(
                'id' => (int) $id,
                'expertId' => $input['expertId'],
            ));
            return $statement->rowCount();
        } catch (\PDOException $e) {
            exit($e->getMessage());
        }
    }

    private function updateExtraResources($id, Array $input) {
        // update patent's data (EXTRA_RESOURCES)
        $statement = "UPDATE PATENTS SET 
                     extraResources= :extraResources,
                     WHERE id = :id;";
        try {
            $db=new databaseController();
            $statement = $db->getConnection()->prepare($statement);
            $statement->execute(array(
                'id' => (int) $id,
                'extraResources' => $input['extraResources'],
            ));
            return $statement->rowCount();
        } catch (\PDOException $e) {
            exit($e->getMessage());
        }
    }

    private function updateStatus($id, Array $input) {
        // update patent's data (PATENT_STATUS)
        $statement = "UPDATE PATENTS SET 
                     patentStatus= :patentStatus,
                     WHERE id = :id;";
        try {
            $db=new databaseController();
            $statement = $db->getConnection()->prepare($statement);
            $statement->execute(array(
                'id' => (int) $id,
                'patentStatus' => $input['patentStatus'],
            ));
            return $statement->rowCount();
        } catch (\PDOException $e) {
            exit($e->getMessage());
        }
    }

    private function delete($id) {
        // delete a patent
        $statement = "
            DELETE FROM PATENTS
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

}