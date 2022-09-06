<?php
namespace Src\TableGateways;

class UserGateway {

    private $db = null;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function findAll()
    {
        $statement = "
            SELECT 
                id, firstname, lastname, email, username
            FROM
                users;
        ";

        try {
            $statement = $this->db->query($statement);
            $result = $statement->fetchAll(\PDO::FETCH_ASSOC);
            return $result;
        } catch (\PDOException $e) {
            exit($e->getMessage());
        }
    }

    public function find($id)
    {
        $statement = "
            SELECT 
                id, firstname, lastname, email, username
            FROM
                users
            WHERE id = ?;
        ";

        try {
            $statement = $this->db->prepare($statement);
            $statement->execute(array($id));
            $result = $statement->fetchAll(\PDO::FETCH_ASSOC);
            return $result;
        } catch (\PDOException $e) {
            exit($e->getMessage());
        }    
    }
 public function findUserWithIdentifier($user_identifier)
    {
        $statement = "
            SELECT 
                password
            FROM
                users
            WHERE username = ?;
        ";

        try {
            $statement = $this->db->prepare($statement);
            $statement->execute(array($user_identifier));
            $result = $statement->fetchAll(\PDO::FETCH_ASSOC);
            return $result;
        } catch (\PDOException $e) {
            exit($e->getMessage());
        }    
    }
    public function insert(Array $input)
    {
        $statement = "
            INSERT INTO users 
                (firstname, lastname, email, username, password)
            VALUES
                (:firstname, :lastname, :email, :username, :password);
        ";

        try {
            $pw = password_hash("rasmuslerdorf", PASSWORD_BCRYPT);
           
            $statement = $this->db->prepare($statement);
            $statement->execute(array(
                'firstname' => $input['firstname'],
                'lastname'  => $input['lastname'],
                'email' => $input['email'] ?? null,
                'username' => $input['username'] ?? null,
                'password' => $input['password'] ? password_hash($input['password'], PASSWORD_BCRYPT) : null,
            ));
            return $statement->rowCount();
        } catch (\PDOException $e) {
            header("HTTP/1.1 500 Server Error");
            exit($e->getMessage());
        }    
    }

    public function update($id, Array $input)
    {
        $statement = "
            UPDATE users
            SET 
                firstname = :firstname,
                lastname  = :lastname,
                email = :email,
                username = :username
            WHERE id = :id;
        ";

        try {
            $statement = $this->db->prepare($statement);
            $statement->execute(array(
                'id' => (int) $id,
                'firstname' => $input['firstname'],
                'lastname'  => $input['lastname'],
                'email' => $input['email'] ?? null,
                'username' => $input['username'] ?? null,
            ));
            return $statement->rowCount();
        } catch (\PDOException $e) {
            exit($e->getMessage());
        }    
    }

    public function delete($id)
    {
        $statement = "
            DELETE FROM users
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

     public function isEmailUnique($email)
    {
        $statement = "
            SELECT * FROM users
            WHERE email = :email;
        ";

        try {
            $statement = $this->db->prepare($statement);
            $statement->execute(array('email' => $email));
            return $statement->rowCount();
        } catch (\PDOException $e) {
            exit($e->getMessage());
        }    
    }
     public function isUsernameUnique($username)
    {
        $statement = "
            SELECT * FROM users
            WHERE username = :username;
        ";

        try {
            $statement = $this->db->prepare($statement);
            $statement->execute(array('username' => $username));
            return $statement->rowCount();
        } catch (\PDOException $e) {
            exit($e->getMessage());
        }    
    }
    public function hasPermission($user_id, $pw){
        $user = $this->find($user_id);
        VAR_DUMP($user);
    }
}
?>
