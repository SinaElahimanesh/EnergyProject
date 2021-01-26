<?php
require_once ("../Controller/database.php");

class User {

    private $db = null;

    private $isAuthenticated;
    private $userId;
    private $isEnabled;

    public function __construct() {
        $this->db=new database();
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

    /**
     * @return bool
     */
    public function isAuthenticated()
    {
        return $this->isAuthenticated;
    }

    /**
     * @param bool $isAuthenticated
     */
    public function setIsAuthenticated($isAuthenticated)
    {
        $this->isAuthenticated = $isAuthenticated;
    }

    /**
     * @return null
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * @param null $userId
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;
    }

    /**
     * @return bool
     */
    public function isEnabled()
    {
        return $this->isEnabled;
    }

    /**
     * @param bool $isEnabled
     */
    public function setIsEnabled($isEnabled)
    {
        $this->isEnabled = $isEnabled;

    }





    public function findAll() {
        // find all users
        $statement = "SELECT * FROM USERS;";
        try {
            //$statement = $this->db->query($statement);
            $statement= $this->db->getConnection()->query($statement);
            $result = $statement->fetchAll(\PDO::FETCH_ASSOC);
            return $result;
        } catch (\PDOException $e) {
            exit($e->getMessage());
        }
    }

    public function find($id) {
        // find an specific id
        $statement = "SELECT * FROM USERS WHERE accountId=?";
        try {
            $statement = $this->db->getConnection()->prepare($statement);
            $statement->execute(array($id));
            $result = $statement->fetchAll(\PDO::FETCH_ASSOC);
            return $result;
        } catch (\PDOException $e) {
            exit($e->getMessage());
        }
    }



    public function insert(Array $input) {

        // insert a user to database
        $statement = "INSERT INTO USERS (user_name, phone, password, email, nationalCode, address, residence, schoolName)
                    VALUES (:user_name, :phone, :password, :email, :nationalCode, :address, :residence, :schoolName);";
        try {
            $input["password"]=password_hash($this->db->makeSafe($input["password"]),PASSWORD_DEFAULT);
            $statement = $this->db->getConnection()->prepare($statement);
            $statement->execute(array(
                'phone' => $input['phone'],
                'password' => $input['password'],
                'fullname' => $input['fullname']
            ));
            return $statement->rowCount();
        } catch (\PDOException $e) {
            exit($e->getMessage());
        }
    }

    public function update($id, Array $input) {
        // update user's data (for completing account information)
        $statement = "UPDATE USERS SET 
                     email= :email,
                     nationalCode= :nationalCode,
                     address= :address,
                     residence= :residence,
                     schoolName= :schoolName,
                     enabled= :enabled
                     ;";
        try {
            $statement = $this->db->getConnection()->prepare($statement);
            $statement->execute(array(
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

    public function delete($id) {
        // delete a user
        $statement = "
            DELETE FROM USERS
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


    public function getUserByPhoneNumber($phoneNumber){
        $statement = "SELECT `accountId,password` FROM users WHERE `phoneNum`=?";
        try {
            $statement = $this->db->getConnection()->prepare($statement);
            $statement->execute(array($phoneNumber));
            $result = $statement->fetchAll(\PDO::FETCH_ASSOC);
            return $result;
        }catch (\PDOException $e){
            exit($e->getMessage());
        }

    }

}