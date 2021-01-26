<?php


class PatentController {

    private $db;
    private $requestMethod;
    private $patentId;
    private $ownerId;

    private $patentGateway;

    public function __construct($db, $requestMethod, $patentId, $ownerId) {
        $this->db = $db;
        $this->requestMethod = $requestMethod;
        $this->patentId = $patentId;
        $this->ownerId = $ownerId;

        $this->patentGateway = new Patent($db);
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
        $result = $this->patentGateway->findPatent($id);
        if (! $result) {
            return $this->notFoundResponse();
        }
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = json_encode($result);
        return $response;
    }

    private function getAllPatentsOfAUser($id) {
        $result = $this->patentGateway->findAllPatentsOfAUser($id);
        if (! $result) {
            return $this->notFoundResponse();
        }
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = json_encode($result);
        return $response;
    }

    private function getAllPatents() {
        $result = $this->patentGateway->findAllPatents();
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = json_encode($result);
        return $response;
    }


    private function createPatentFromRequest() {
        $input = (array) json_decode(file_get_contents('php://input'), TRUE);
        if (! $this->validatePatent($input)) {
            return $this->unprocessableEntityResponse();
        }
        $this->patentGateway->insert($input);
        $response['status_code_header'] = 'HTTP/1.1 201 Created';
        $response['body'] = null;
        return $response;
    }

    private function updatePatentFromRequest($id) {
        $result = $this->patentGateway->findPatent($id);
        if (! $result) {
            return $this->notFoundResponse();
        }
        $input = (array) json_decode(file_get_contents('php://input'), TRUE);
        if (! $this->validatePatent($input)) {
            return $this->unprocessableEntityResponse();
        }
        if(array_key_exists ( 'expertId' ,  $input )) {
            $this->patentGateway->updateExpert($id, $input);
        } else if(array_key_exists ( 'extraResources' ,  $input )) {
            $this->patentGateway->updateExtraResources($id, $input);
        } else if(array_key_exists ( 'patentStatus' ,  $input )) {
            $this->patentGateway->updateStatus($id, $input);
        }
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = null;
        return $response;
    }

    private function deletePatent($id) {
        $result = $this->patentGateway->findPatent($id);
        if (! $result) {
            return $this->notFoundResponse();
        }
        $this->patentGateway->delete($id);
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

}