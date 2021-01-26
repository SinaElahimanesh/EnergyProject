<?php


class Patent {
    private $db;

    function __construct() {
        $this->db=new database();
    }


    public function findAllPatents($id) {
        // find all patents of all students
        $statement = "SELECT * FROM PATENTS;";
        try {
            $statement= $this->db->getConnection()->query($statement);
            $result = $statement->fetchAll(\PDO::FETCH_ASSOC);
            return $result;
        } catch (\PDOException $e) {
            exit($e->getMessage());
        }
    }

    public function findAllPatentsOfAUser($id) {
        // find all patents of a user with ID $id
        $statement = "SELECT * FROM PATENTS WHERE ownerId=?;";
        try {
            $statement= $this->db->getConnection()->query($statement);
            $result = $statement->fetchAll(\PDO::FETCH_ASSOC);
            return $result;
        } catch (\PDOException $e) {
            exit($e->getMessage());
        }
    }

    public function findPatent($id) {
        // find an specific patent of a user
        $statement = "SELECT * FROM PATENTS WHERE patentId=?;";
        try {
            $statement= $this->db->getConnection()->query($statement);
            $result = $statement->fetchAll(\PDO::FETCH_ASSOC);
            return $result;
        } catch (\PDOException $e) {
            exit($e->getMessage());
        }
    }

    public function insert(Array $input) {

        // insert a patent to database
        $statement = "INSERT INTO PATENTS (patent_name, ownerId, expertId, patentStatus, description, extraResources)
                    VALUES (:patent_name, :ownerId, :expertId, :patentStatus, :description, :extraResources);";
        try {
            $statement = $this->db->getConnection()->prepare($statement);
            $statement->execute(array(
                'patent_name' => $input['patent_name'],
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

    public function updateExpert($id, Array $input) {
        // update patent's data (EXPERT)
        $statement = "UPDATE PATENTS SET 
                     expertId= :expertId,
                     ;";
        try {
            $statement = $this->db->getConnection()->prepare($statement);
            $statement->execute(array(
                'expertId' => $input['expertId'],
            ));
            return $statement->rowCount();
        } catch (\PDOException $e) {
            exit($e->getMessage());
        }
    }

    public function updateExtraResources($id, Array $input) {
        // update patent's data (EXTRA_RESOURCES)
        $statement = "UPDATE PATENTS SET 
                     extraResources= :extraResources,
                     ;";
        try {
            $statement = $this->db->getConnection()->prepare($statement);
            $statement->execute(array(
                'extraResources' => $input['extraResources'],
            ));
            return $statement->rowCount();
        } catch (\PDOException $e) {
            exit($e->getMessage());
        }
    }

    public function updateStatus($id, Array $input) {
        // update patent's data (PATENT_STATUS)
        $statement = "UPDATE PATENTS SET 
                     patentStatus= :patentStatus,
                     ;";
        try {
            $statement = $this->db->getConnection()->prepare($statement);
            $statement->execute(array(
                'patentStatus' => $input['patentStatus'],
            ));
            return $statement->rowCount();
        } catch (\PDOException $e) {
            exit($e->getMessage());
        }
    }

    public function delete($id) {
        // delete a patent
        $statement = "
            DELETE FROM PATENTS
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