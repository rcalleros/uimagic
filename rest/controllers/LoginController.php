<?php
namespace Src\Controller;
use Src\TableGateways\UserGateway as userGateway;
use Src\Controller\HttpRequestController;
use Dotenv\Dotenv;
class LoginController {

    private $db;
    private $requestMethod;
    private $userId;
    private $userGateway;
    private $user;
    private $user_identifier;
    private $pepper;

    public function __construct($db, $requestMethod)
    {
        $this->db = $db;
        $this->requestMethod = $requestMethod;
        $this->pepper = $_ENV['PEPPER'];
        $this->user = new userGateway($db);
    }

    public function loginUser(){
        $input = (array) json_decode(file_get_contents('php://input'), TRUE);
        $user = $this->user->findUserWithIdentifier($input['identifier']);
        $pwd = $input['password'];
        $pwd_peppered = hash_hmac("sha256", $pwd, $this->pepper);
        if (!is_null($user) && password_verify($pwd_peppered, $user['password'])) {
            
            $token = $this->user->getAuthToken($user['id']);
             $response = array(
            "body"=>json_encode(array('success'=>true, 'token'=>$token)),
            'status_code_header' => 'HTTP/1.1 200 OK'
        );
        }else {
             $response = array(
            "body"=>json_encode(array('success'=>false)),
            'status_code_header' => 'HTTP/1.1 400 BAD REQUEST'
        );
        }
       
        return $response;
    }

    public function processRequest()
    {
        switch ($this->requestMethod) {
          
            case 'POST':
                $response = $this->loginUser();
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
  
}