<?php
require '../vendor/autoload.php';
require 'database/DatabaseConnector.php';
use Dotenv\Dotenv;

use Src\System\DatabaseConnector;
(Dotenv::createImmutable(__DIR__))->load();
$dbConnection = (new DatabaseConnector())->getConnection();

?>