<?php

namespace Src\TableGateways;

class UserGateway
{

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

    public function findUserWithAuthToken($token)
    {
        $statement = "
            SELECT 
                *
            FROM
                authenticated_users
            WHERE auth_token = ?;
        ";

        try {
            $statement = $this->db->prepare($statement);
            $statement->execute(array($token));
            $result = $statement->fetchAll(\PDO::FETCH_ASSOC);
            return $result;
        } catch (\PDOException $e) {
            exit($e->getMessage());
        }
    }


    public function  findUserWithIdentifier($user_identifier)
    {
        $statement = "
            SELECT 
                password,
                id
            FROM
                users
            WHERE username = ?;
        ";

        try {
            $statement = $this->db->prepare($statement);
            $statement->execute(array($user_identifier));
            $result = $statement->fetchAll(\PDO::FETCH_ASSOC);
            if (count($result) > 0) {
                return $result[0];
            }
        } catch (\PDOException $e) {
            exit($e->getMessage());
        }
    }
    public function insert(array $input)
    {
        $statement = "
            INSERT INTO users 
                (firstname, lastname, email, username, password)
            VALUES
                (:firstname, :lastname, :email, :username, :password);
        ";

        try {
            $pepper = $_ENV['PEPPER'];
            $pwd_peppered = hash_hmac("sha256", $input['password'], $pepper);
            $pwd_hashed = password_hash($pwd_peppered, PASSWORD_BCRYPT);
            $statement = $this->db->prepare($statement);
            $statement->execute(array(
                'firstname' => $input['firstname'],
                'lastname'  => $input['lastname'],
                'email' => $input['email'] ?? null,
                'username' => $input['username'] ?? null,
                'password' => $input['password'] ? $pwd_hashed : null,
            ));
            return $statement->rowCount();
        } catch (\PDOException $e) {
            header("HTTP/1.1 500 Server Error");
            exit($e->getMessage());
        }
    }

    public function update($id, array $input)
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
    public function doesAuthSessionExist(Int $userId)
    {
        $statement = "
            SELECT * FROM authenticated_users
            WHERE user_id = :user_id;
        ";

        try {
            $statement = $this->db->prepare($statement);
            $statement->execute(array('user_id' => $userId));
            return $statement->rowCount() > 0;
        } catch (\PDOException $e) {
            exit($e->getMessage());
        }
    }
    public function hasPermission($user_id, $pw)
    {
        $user = $this->find($user_id);
    }

    public function insertToken(Int $userId, $token)
    {
        $statement = "
            INSERT INTO authenticated_users 
                (user_id, auth_token)
            VALUES
                (:user_id, :auth_token);
        ";

        try {
            $statement = $this->db->prepare($statement);
            $statement->execute(array(
                'user_id' => $userId,
                'auth_token'  => $token,
            ));
            return $token;
        } catch (\PDOException $e) {
            header("HTTP/1.1 500 Server Error");
            exit($e->getMessage());
        }
    }
    public function getAuthToken($userId)
    {
        $token = $this->random_str(64);
        if ($this->doesAuthSessionExist($userId)) {
            $this->updateToken($userId, $token);
        } else {
            $this->insertToken($userId, $token);
        }
        return $token;
    }
    public function updateToken(Int $userId, $token)
    {
        $statement = "
            UPDATE authenticated_users
            SET auth_token = :auth_token
            WHERE user_id = :user_id;
        ";

        try {
            $statement = $this->db->prepare($statement);
            $statement->execute(array(
                'user_id' => $userId,
                'auth_token'  => $token,
            ));
            return $token;
        } catch (\PDOException $e) {
            header("HTTP/1.1 500 Server Error");
            exit($e->getMessage());
        }
    }

    public function random_str(int $length = 64, string $keyspace = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'): string
    {
        if ($length < 1) {
            throw new \RangeException("Length must be a positive integer");
        }
        $pieces = [];
        $max = mb_strlen($keyspace, '8bit') - 1;
        for ($i = 0; $i < $length; ++$i) {
            $pieces[] = $keyspace[random_int(0, $max)];
        }
        return implode('', $pieces);
    }
}
