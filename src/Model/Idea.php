<?php


class Idea {
    private $db;

    function __construct() {
        $db=new databaseController();
    }


    public function findAllIdeas() {
        // find all ideas of all students
        $statement = "SELECT * FROM IDEAS;";
        try {
            $statement= $this->db->getConnection()->query($statement);
            $result = $statement->fetchAll(\PDO::FETCH_ASSOC);
            return $result;
        } catch (\PDOException $e) {
            exit($e->getMessage());
        }
    }

    public function findAllIdeasOfAUser($id) {
        // find all ideas of a user with ID $id
        $statement = "SELECT * FROM IDEAS WHERE ownerId=?;";
        try {
            $statement= $this->db->getConnection()->query($statement);
            $statement->execute(array($id));
            $result = $statement->fetchAll(\PDO::FETCH_ASSOC);
            return $result;
        } catch (\PDOException $e) {
            exit($e->getMessage());
        }
    }

    public function findIdea($id) {
        // find an specific idea of a user
        $statement = "SELECT * FROM IDEAS WHERE ideaId=?;";
        try {
            $statement= $this->db->getConnection()->query($statement);
            $statement->execute(array($id));
            $result = $statement->fetchAll(\PDO::FETCH_ASSOC);
            return $result;
        } catch (\PDOException $e) {
            exit($e->getMessage());
        }
    }

    public function insert(Array $input) {

        // insert an idea to databaseController
        $statement = "INSERT INTO PATENTS (idea_name, ownerId, expertId, ideaStatus, description, extraResources)
                    VALUES (:idea_name, :ownerId, :expertId, :ideaStatus, :description, :extraResources);";
        try {
            $statement = $this->db->getConnection()->prepare($statement);
            $statement->execute(array(
                'idea_name' => $input['idea_name'],
                'ownerId' => $input['ownerId'],
                'ideaStatus' => 'START',
                'description' => $input['description'],
                'extraResources' => $input['extraResources'],
            ));
            return $statement->rowCount();
        } catch (\PDOException $e) {
            exit($e->getMessage());
        }
    }

    public function updateExpert($id, Array $input) {
        // update idea's data (EXPERT)
        $statement = "UPDATE IDEAS SET 
                     expertId= :expertId,
                      WHERE id = :id;";
        try {
            $statement = $this->db->getConnection()->prepare($statement);
            $statement->execute(array(
                'id' => (int) $id,
                'expertId' => $input['expertId'],
            ));
            return $statement->rowCount();
        } catch (\PDOException $e) {
            exit($e->getMessage());
        }
    }

    public function updateExtraResources($id, Array $input) {
        // update idea's data (EXTRA_RESOURCES)
        $statement = "UPDATE IDEAS SET 
                     extraResources= :extraResources,
                     WHERE id = :id;";
        try {
            $statement = $this->db->getConnection()->prepare($statement);
            $statement->execute(array(
                'id' => (int) $id,
                'extraResources' => $input['extraResources'],
            ));
            return $statement->rowCount();
        } catch (\PDOException $e) {
            exit($e->getMessage());
        }
    }

    public function updateStatus($id, Array $input) {
        // update idea's data (IDEA_STATUS)
        $statement = "UPDATE IDEAS SET 
                     ideaStatus= :ideaStatus,
                      WHERE id = :id;";
        try {
            $statement = $this->db->getConnection()->prepare($statement);
            $statement->execute(array(
                'id' => (int) $id,
                'ideaStatus' => $input['ideaStatus'],
            ));
            return $statement->rowCount();
        } catch (\PDOException $e) {
            exit($e->getMessage());
        }
    }

    public function delete($id) {
        // delete a idea
        $statement = "
            DELETE FROM IDEAS
            WHERE id = :id;
        ";
        try {
            $statement = $this->db->getConnection()->prepare($statement);
            $statement->execute(array('id' => $id));
            return $statement->rowCount();
        } catch (\PDOException $e) {
            exit($e->getMessage());
        }
    }
}