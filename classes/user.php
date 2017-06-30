<?php

include('password.php');
require_once ($_SERVER['DOCUMENT_ROOT'].'/encapsulate/classes/randomlib/random.php');

class User extends Password {

    private $_db;

    function __construct($db) {
        parent::__construct();

        $this->_db = $db;
    }

    private function get_user_hash($username) {

        try {
            $stmt = $this->_db->prepare('SELECT userPassword FROM user_master WHERE userUName = :username AND isActive=1 ');
            $stmt->execute(array(':username' => $username));

            $row = $stmt->fetch();
            return $row['userPassword'];
        } catch (PDOException $e) {
            echo '<p class="bg-danger">' . $e->getMessage() . '</p>';
        }
    }

    private function update_user_token() {
        try {
            $bytes = random_bytes(127);
            $token = bin2hex($bytes);
            $stmt = $this->_db->prepare('INSERT INTO access_master (accessIP, accessTime, accessInterface, userID, accessToken) VALUES (:access, :last, :Int, :userID, :token)');
            $stmt->execute(array(':access' => $_SERVER['REMOTE_ADDR'],
                ':Int' => $_SERVER['HTTP_USER_AGENT'],
                ':token' => $token,
                ':last' => date('Y-m-d H:i:s'),
                ':userID' => $_SESSION['ID']
            ));
            $id = $this->_db->lastInsertId('accessID');
            $stmt2 = $this->_db->prepare('UPDATE user_master SET userLAID = :last WHERE userID = :userID');
            $stmt2->execute(array(
                ':last' => $id,
                ':userID' => $_SESSION['ID']
            ));
            $_SESSION['token'] = $token;
            return true;
        } catch (Exception $ex) {
            $_SESSION["success"] = ERROR_DATABASE;
            $_SESSION["message"] = ERROR_DATABASE_MESSAGE . $e->getMessage();
            return false;
        }
    }

    public function verify_token($token) {
        $stmt = $this->_db->prepare('SELECT accessToken, userID from access_master WHERE accessID = :ID');
        $stmt->execute(array(':ID' => $_SESSION['LAID']));

        $row = $stmt->fetch();
        if ($row['accessToken'] == $token && $_SESSION['ID'] == $row['userID']) {
            if ($this->update_user_token()) {
                return true;
            } else {
                return false;
            }
        } else {
            $this->update_user_token();
            $_SESSION["success"] = ERROR_TOKEN;
            $_SESSION['message'] = "Invalid access token!";
            return false;
        }
    }

    public function login($username, $password) {

        $hashed = $this->get_user_hash($username);

        if ($this->password_verify($password, $hashed) == 1) {

            $stmt1 = $this->_db->prepare('SELECT userID, userLAID FROM user_master WHERE userUName = :username AND isActive=1');
            $stmt1->execute(array('username' => $username));

            $row = $stmt1->fetch();
            $_SESSION['ID'] = $row['userID'];
            //$_SESSION['LAID'] = $row['userLAID'];
            if ($this->update_user_token()) {
                return true;
            } else {
                return false;
            }
        } else {
            $_SESSION["success"] = ERROR_PASSWORD;
            $_SESSION["message"] = ERROR_PASSWORD_MESSAGE;
            return false;
        }
    }

    public function logout() {
        session_destroy();
    }

    public function is_logged_in() {
        if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == true) {
            return true;
        }
    }

    public function is_uname_unique($uname) {
        try {
            $stmt = $this->_db->prepare('SELECT userUName FROM user_master WHERE userUName = :username');
            $stmt->execute(array(':username' => $uname));
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!empty($row['userUName'])) {
                return false;
            } else {
                return true;
            }
        } catch (Exception $e) {
            
            return $e->getMessage();
        }
    }

    public function is_uemail_unique($uemail) {
        try {
            $stmt = $this->_db->prepare('SELECT userEmail FROM user_master WHERE userEmail = :email');
            $stmt->execute(array(':email' => $uemail));
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!empty($row['userEmail'])) {
                return false;
            } else {
                return true;
            }
        } catch (Exception $e) {
            $_SESSION["success"] = ERROR_DATABASE;
            $_SESSION["message"] = ERROR_DATABASE_MESSAGE . $e->getMessage();
            return false;
        }
    }

    public function add_key($data, $info, $name) {
        try {
            $stmt = $this->_db->prepare('INSERT INTO key_master (userID, keyInfo, keyCDate, keyData, keyName, isAssociated) VALUES (:id, :info, :date, :data, :name, :assoc)');
            $stmt->execute(array(
                ':id' => $_SESSION['ID'],
                ':info' => $info,
                ':date' => date('Y-m-d H:i:s'),
                ':data' => $data,
                ':name' => $name,
                ':assoc' => 1
            ));
            return true;
        } catch (Exception $ex) {
            $_SESSION["success"] = ERROR_DATABASE;
            $_SESSION["message"] = ERROR_DATABASE_MESSAGE . $ex->getMessage();
            return false;
        }
    }

    public function del_key($kid) {
        try {
            $stmt = $this->_db->prepare('UPDATE key_master SET isAssociated=:assoc AND keyCDate=:date WHERE keyID=:id AND isAssociated=1');
            $stmt->execute(array(
                ':id' => $kid,
                ':date' => date('Y-m-d H:i:s'),
                ':assoc' => 2
            ));
            return true;
        } catch (Exception $ex) {
            $_SESSION["success"] = ERROR_DATABASE;
            $_SESSION["message"] = ERROR_DATABASE_MESSAGE . $ex->getMessage();
            return false;
        }
    }

    public function get_laid() {
        $stmt = $this->_db->prepare('SELECT userLAID FROM user_master WHERE userID = :id AND isActive=1 ');
        $stmt->execute(array(':id' => $_SESSION['ID']));
        $row = $stmt->fetch();
        return $row['userLAID'];
    }
    
    public function add_secret($encap_data, $name, $info, $add, $keyID, $encapID){
        try {
            $stmt = $this->_db->prepare('INSERT INTO secret_master (userID, secretInfo, secretAdd, secretCDate, secretData, secretName, keyID, encapID, isAssociated) VALUES (:id, :info, :add, :date, :data, :name, :key, :encap, :assoc)');
            $stmt->execute(array(
                ':id' => $_SESSION['ID'],
                ':info' => $info,
                ':date' => date('Y-m-d H:i:s'),
                ':data' => $encap_data,
                ':add' => $add,
                ':key' => $keyID,
                ':encap' => $encapID,
                ':name' => $name,
                ':assoc' => 1
            ));
            return true;
        } catch (Exception $ex) {
            $_SESSION["success"] = ERROR_DATABASE;
            $_SESSION["message"] = ERROR_DATABASE_MESSAGE . $ex->getMessage();
            return false;
        }
    }

    public function del_secret($sid) {
        try {
            $stmt = $this->_db->prepare('UPDATE secret_master SET isAssociated=:assoc AND secretCDate=:date WHERE secretID=:id AND isAssociated=1');
            $stmt->execute(array(
                ':id' => $sid,
                ':date' => date('Y-m-d H:i:s'),
                ':assoc' => 0
            ));
            return true;
        } catch (Exception $ex) {
            $_SESSION["success"] = ERROR_DATABASE;
            $_SESSION["message"] = ERROR_DATABASE_MESSAGE . $ex->getMessage();
            return false;
        }
    }
    
    public function get_key($kid){
        $stmt = $this->_db->prepare('SELECT * FROM key_master WHERE userID = :id AND keyID=:key AND isAssociated = 1');
        $stmt->execute(array(
            ':id' => $_SESSION['ID'],
            ':key' => $kid
                ));
        $row = $stmt->fetch();
        return $row;
    }
    
    private function get_encap($enid){
        $stmt = $this->_db->prepare('SELECT encapName FROM encap_master WHERE userID = :id AND encapID=:encap AND isAssociated = 1');
        $stmt->execute(array(
            ':id' => $_SESSION['ID'],
            ':encap' => $enid
                ));
        $row = $stmt->fetch();
        return $row['encapName'];
    }
    
    public function get_secret($sid){
        $stmt = $this->_db->prepare('SELECT keyID, secretData, encapID FROM secret_master WHERE userID = :id AND secretID=:secret AND isAssociated = 1');
        $stmt->execute(array(
            ':id' => $_SESSION['ID'],
            ':secret' => $sid
                ));
        $row = $stmt->fetch();
        return $row;
    }
    
    public function encap_data ($encap_id, $key_id, $data){
        $keydata = $this->get_key($key_id)['keyData'];
       /* $encap_type = $this->get_encap($encap_id);
        switch ($encap_type){
            case "AES":
                break;
        }*/
        return $data . $keydata;
    }
    
    public function get_all_keys(){
        $stmt = $this->_db->prepare('SELECT keyID, keyName, keyInfo, keyCDate FROM key_master WHERE userID = :id AND isAssociated = 1');
        $stmt->execute(array(':id' => $_SESSION['ID']));
        $row = $stmt->fetchAll();
        return $row;
    }
    
    public function get_all_secrets(){
        $stmt = $this->_db->prepare('SELECT secretID, secretName, secretData, secretAdd, secretInfo, secretCDate FROM secret_master WHERE userID = :id AND isAssociated = 1');
        $stmt->execute(array(':id' => $_SESSION['ID']));
        $row = $stmt->fetchAll();
        return $row;
    }
    
    private function get_username(){
        $stmt = $this->_db->prepare('SELECT userUName FROM user_master WHERE userID = :id AND isActive=1');
        $stmt->execute(array(':id' => $_SESSION['ID']));
        $row = $stmt->fetch();
        return $row['userUName'];
    }
    
    public function verify_pwd($password) {
        $username = $this->get_username();
        $hashed = $this->get_user_hash($username);

        if (($this->password_verify($password, $hashed) == 1)) {
            return true;
        } else {
            $_SESSION["success"] = ERROR_PASSWORD;
            $_SESSION["message"] = ERROR_PASSWORD_MESSAGE;
            return false;
        }
    }

}

?>