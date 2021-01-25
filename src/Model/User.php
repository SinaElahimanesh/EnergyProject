<?php
require_once ("../Controller/database.php");

class User {

    private $db = null;

    private $isAuthenticated;
    private $userId;
    private $isEnabled;

    public function __construct($db) {
        $this->db = $db;
        $this->isAuthenticated=false;
        $this->isEnabled=false;
        $this->userId=null;
    }

    public function saveUserObjectInSession(){ /// at the end of each page!!!!
        if(session_status()==PHP_SESSION_NONE) {
            session_start();
        }
        $_SESSION["userObj"]=serialize($this);
    }

    public function loadUserFromSession(){
        if(session_status()==PHP_SESSION_NONE) {
            session_start();
        }
        $obj=unserialize($_SESSION["userObj"]);
        $this->userId=$obj->userId;
        $this->isEnabled=$obj->isEnabeled;
        $this->isAuthenticated=$obj->isAuthenticated;
    }


    public function findAll() {
        // find all users
        $statement = "SELECT * FROM USERS;";
        try {
            $statement = $this->db->query($statement);
            $result = $statement->fetchAll(\PDO::FETCH_ASSOC);
            return $result;
        } catch (\PDOException $e) {
            exit($e->getMessage());
        }
    }

    public function find($id) {
        // find an specific id
        $statement = "SELECT * FROM USERS WHERE id=?";
        try {
            $statement = $this->db->prepare($statement);
            $statement->execute(array($id));
            $result = $statement->fetchAll(\PDO::FETCH_ASSOC);
            return $result;
        } catch (\PDOException $e) {
            exit($e->getMessage());
        }
    }


    public function addUserToDataBase(َ$phoneNumber,$password,$fullName){
        $db=new database();
        $phoneNumber=$db->makeSafe($phoneNumber);
        $password=$db->makeSafe($password);
        $fullName=$db->makeSafe($fullName);
        /// not completed!

    }

//    public function insert(Array $input) {
//        // insert a user to database
//        $statement = "INSERT INTO USERS (user_name, phone, password, email, nationalCode, address, residence, schoolName)
//                    VALUES (:user_name, :phone, :password, :email, :nationalCode, :address, :residence, :schoolName);";
//        try {
//            $statement = $this->db->prepare($statement);
//            $statement->execute(array(
//                'user_name' => $input['user_name'],
//                'phone' => $input['phone'],
//                'password' => $input['password'],
//                'email' => $input['email'] ?? null,
//                'nationalCode' => $input['nationalCode'] ?? null,
//                'address' => $input['address'] ?? null,
//                'residence' => $input['residence'] ?? null,
//                'schoolName' => $input['schoolName'] ?? null
//            ));
//            return $statement->rowCount();
//        } catch (\PDOException $e) {
//            exit($e->getMessage());
//        }
//    }

    public function update($id, Array $input) {
        // update user's data (for completing account information)
        $statement = "UPDATE USERS SET 
                     user_name= :user_name,
                     phone= :phone,
                     password= :password,
                     email= :email,
                     nationalCode= :nationalCode,
                     address= :address,
                     residence= :residence
                     schoolName= :schoolName;";
        try {
            $statement = $this->db->prepare($statement);
            $statement->execute(array(
                'id' => (int) $id,
                'user_name' => $input['user_name'],
                'phone' => $input['phone'],
                'password' => $input['password'],
                'email' => $input['email'],
                'nationalCode' => $input['nationalCode'],
                'address' => $input['address'],
                'residence' => $input['residence'],
                'schoolName' => $input['schoolName']
            ));
            return $statement->rowCount();
        } catch (\PDOException $e) {
            exit($e->getMessage());
        }
    }

    public function delete($id) {
        // delete a user
        $statement = "
            DELETE FROM USERS
            WHERE id = :id;
        ";
        try {
            $statement = $this->db->prepare($statement);
            $statement->execute(array('id' => $id));
            return $statement->rowCount();
        } catch (\PDOException $e) {
            exit($e->getMessage());
        }
    }

}