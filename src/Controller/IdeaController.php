<?php


class IdeaController {

    private $db;
    private $requestMethod;
    private $ideaId;
    private $ownerId;

    private $ideaGateway;

    public function __construct($db, $requestMethod, $ideaId, $ownerId) {
        $this->db = $db;
        $this->requestMethod = $requestMethod;
        $this->ideaId = $ideaId;
        $this->ownerId = $ownerId;

        $this->ideaGateway = new Idea($db);
    }

    public function processRequest() {
        switch ($this->requestMethod) {
            case 'GET':
                if($this->ideaId) {
                    $response = $this->getIdea($this->ideaId);
                } else if($this->ownerId) {
                    $response = $this->getAllIdeasOfAUser($this->ownerId);
                } else {
                    $response = $this->getAllIdeas();
                }
                break;
            case 'POST':
                $response = $this->createIdeaFromRequest();
                break;
            case 'PUT':
                $response = $this->updateIdeaFromRequest($this->ideaId);
                break;
            case 'DELETE':
                $response = $this->deleteIdea($this->ideaId);
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

    private function getIdea($id) {
        $result = $this->ideaGateway->findIdea($id);
        if (! $result) {
            return $this->notFoundResponse();
        }
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = json_encode($result);
        return $response;
    }

    private function getAllIdeasOfAUser($id) {
        $result = $this->ideaGateway->findAllIdeasOfAUser($id);
        if (! $result) {
            return $this->notFoundResponse();
        }
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = json_encode($result);
        return $response;
    }

    private function getAllIdeas() {
        $result = $this->ideaGateway->findAllIdeas();
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = json_encode($result);
        return $response;
    }


    private function createIdeaFromRequest() {
        $input = (array) json_decode(file_get_contents('php://input'), TRUE);
        if (! $this->validateIdea($input)) {
            return $this->unprocessableEntityResponse();
        }
        $this->ideaGateway->insert($input);
        $response['status_code_header'] = 'HTTP/1.1 201 Created';
        $response['body'] = null;
        return $response;
    }

    private function updateIdeaFromRequest($id) {
        $result = $this->ideaGateway->findIdea($id);
        if (! $result) {
            return $this->notFoundResponse();
        }
        $input = (array) json_decode(file_get_contents('php://input'), TRUE);
        if (! $this->validateIdea($input)) {
            return $this->unprocessableEntityResponse();
        }
        if(array_key_exists ( 'expertId' ,  $input )) {
            $this->ideaGateway->updateExpert($id, $input);
        } else if(array_key_exists ( 'extraResources' ,  $input )) {
            $this->ideaGateway->updateExtraResources($id, $input);
        } else if(array_key_exists ( 'ideaStatus' ,  $input )) {
            $this->ideaGateway->updateStatus($id, $input);
        }
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = null;
        return $response;
    }

    private function deleteIdea($id) {
        $result = $this->ideaGateway->findIdea($id);
        if (! $result) {
            return $this->notFoundResponse();
        }
        $this->ideaGateway->delete($id);
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = null;
        return $response;
    }

    private function validateIdea($input) {
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