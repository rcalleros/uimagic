<?php

namespace Src\Controller;

use Src\TableGateways\UserGateway as userGateway;

class UserController
{

    private $db;
    private $requestMethod;
    private $userId;
    private $userGateway;

    public function __construct($db, $requestMethod, $userId)
    {
        $this->db = $db;
        $this->requestMethod = $requestMethod;
        $this->userId = $userId;

        $this->userGateway = new userGateway($db);
    }

    public function processRequest()
    {
        switch ($this->requestMethod) {
            case 'GET':
                if ($this->userId) {
                    $response = $this->getUser($this->userId);
                } else {
                    $response = $this->getAllUsers();
                };
                break;
            case 'POST':
                $response = $this->createUserFromRequest();
                break;
            case 'PUT':
                $response = $this->updateUserFromRequest($this->userId);
                break;
            case 'DELETE':
                $response = $this->deleteUser($this->userId);
                break;
            default:
                $response = notFoundResponse();
                break;
        }
        header($response['status_code_header']);
        if ($response['body']) {
            echo $response['body'];
        }
    }

    private function getAllUsers()
    {
        $result = $this->userGateway->findAll();
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = json_encode($result);
        return $response;
    }

    private function getUser($id)
    {
        $result = $this->userGateway->find($id);
        if (!$result) {
            return notFoundResponse();
        }
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = json_encode($result);
        return $response;
    }
    private function isEmailUnique()
    {
    }
    private function createUserFromRequest()
    {
        $input = (array) json_decode(file_get_contents('php://input'), TRUE);
        $errorObject = $this->validatePerson($input);
        if (count($errorObject['data']) > 0) {
            return unprocessableEntityResponse($errorObject);
        }
        $this->userGateway->insert($input);
        $response['status_code_header'] = 'HTTP/1.1 201 Created';
        $response['body'] = null;
        return $response;
    }

    private function updateUserFromRequest($id)
    {
        $result = $this->userGateway->find($id);
        if (!$result) {
            return notFoundResponse();
        }
        $input = (array) json_decode(file_get_contents('php://input'), TRUE);
        $errorList = $this->validatePerson($input);
        if ($this->validatePerson($input)) {
            return unprocessableEntityResponse($errorList);
        }
        $this->userGateway->update($id, $input);
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = null;
        return $response;
    }

    private function deleteUser($id)
    {
        $result = $this->userGateway->find($id);
        if (!$result) {
            return notFoundResponse();
        }
        $this->userGateway->delete($id);
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = null;
        return $response;
    }

    private function validatePerson($input)
    {
        $matchingEmailRecordsCount = $this->userGateway->isEmailUnique($input['email']);
        $matchingUsernameRecordsCount = $this->userGateway->isUsernameUnique($input['username']);
        $responseObject = [
            'success' => true,
            'data' => [],
            'error' => null
        ];
        $errorName = null;
        foreach ($input as $field => $value) {
            // CHECK IF VALUE EMPTY
            if (!isset($value) || empty($value)) {

                $responseObject['data'][$field] = "$field cannot be empty.";
                $errorName = "INVALID_USER_FORM";
            }

            if ($field === 'email') {
                // CHECK EMAIL FORMAT
                if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    $responseObject['data'][$field] = "Your email is incorrectly formatted.";
                    $errorName = "INVALID_EMAIL";
                }
            }

            // CHECK PASSWORD COMPLEXITY
            if ($field === 'password') {
                $is_special = preg_match('/[+#!?\$@]/', $value);
                $is_numeric = preg_match('/[0-9]/', $value);
                $is_cap = preg_match('/[A-Z]/', $value);
                $is_char = preg_match('/[a-z]/', $value);
                // ADD UNIQUE ERROR MESSAGE
                if (!$is_special) {
                    $responseObject['data'][$field] = "Your password does not contain a special character.";
                    $errorName = "INVALID_PASSWORD_SPECIAL";
                }
                if (!$is_numeric) {
                    $responseObject['data'][$field] = "Your password does not contain at least one number.";
                    $errorName = "INVALID_PASSWORD_NUMBER";
                }
                if (!$is_cap) {
                    $responseObject['data'][$field] = "Your password does not contain a capital letter.";
                    $errorName = "INVALID_PASSWORD_UPPERCASE";
                }
                if (!$is_char) {
                    $responseObject['data'][$field] = "Your password does not contain a lower case letter.";
                    $errorName = "INVALID_PASSWORD_LOWERCASE";
                }
                // CHECK PW LENGTH
                if (strlen($value) < 8) {
                    $responseObject['data'][$field] = "Your password does not contain 8 or more characters.";
                    $errorName = "INVALID_PASSWORD_TOO_SHORT";
                }
            }
        }

        if ($errorName === "INVALID_USER_FORM") {
            $responseObject['success'] = false;
            return $responseObject;
        }

        if ($input['password'] !== $input['confirmpassword']) {
            $responseObject['success'] = false;
            $responseObject['data'] = array('password' => 'The password and confirmpassword do not match');
            $responseObject['error'] = "PASSWORD_MISMATCH_ERROR";
            return $responseObject;
        }
        if ($matchingEmailRecordsCount > 0) {
            $responseObject['success'] = false;
            $responseObject['data'] = array('email' => 'An account already exists with this email.');
            $responseObject['error'] = "EMAIL_ALREADY_EXISTS";
            return $responseObject;
        }
        if ($matchingUsernameRecordsCount > 0) {
            $responseObject['success'] = false;
            $responseObject['data'] = array('email' => 'An account already exists with this username.');
            $responseObject['error'] = "USERNAME_ALREADY_EXISTS";
            return $responseObject;
        }

        return $responseObject;
    }
}
