<?php
include_once "./bootstrap.php";
include_once "./controllers/LoginController.php";
include_once "./controllers/UserController.php";
include_once "./utils/SecurityToken.php";
include_once 'database/user/UserGateway.php';
include_once './controllers/HttpRequest.php';

use Src\Controller\UserController as UserControls;
use Src\Controller\LoginController as LoginControls;

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: OPTIONS,GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
$requestUri = $_SERVER['REQUEST_URI'];
$uri = parse_url($requestUri, PHP_URL_PATH);
$uri = explode( '/api/', $uri );

$requestMethod = $_SERVER["REQUEST_METHOD"];
$token = getBearerToken();
if(empty($token) && isValidApiEndpoint($requestUri, $requestMethod)){
  header("HTTP/1.1 403 Forbidden");
  exit();
}
// login
if( !empty($uri) && $uri[1] === 'login'){

  $controller = new LoginControls($dbConnection, $requestMethod);
  $controller->processRequest();
}


// user
if( $uri[1] === 'user'){
  // the user id is, of course, optional and must be a number:
  $userId = null;
  if (isset($uri[2])) {
      $userId = (int) $uri[2];
  }
  // pass the request method and user ID to the PersonController and process the HTTP request:
  $controller = new UserControls($dbConnection, $requestMethod, $userId);
  $controller->processRequest();
}

// if($uri[1] === 'video_content'){
//  echo 'gimme some video content';
// }

?>