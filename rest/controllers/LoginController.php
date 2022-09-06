<?php
require 'database/user/UserGateway.php';
namespace Src\Controller;

use Src\TableGateways\UserGateway;


class LoginController {

    private $db;
    private $requestMethod;
    private $userId;
    private $userGateway;
    private $user;
    private $user_identifier;

    public function __construct($db, $requestMethod)
    {
        $this->db = $db;
        $this->requestMethod = $requestMethod;

        $this->user = new UserGateway($db);
    }

    public function loginUser(){
        $input = (array) json_decode(file_get_contents('php://input'), TRUE);
        $result = $this->user->findUserWithIdentifier($input['identifier']);
        VAR_DUMP($result);
    }

  
}