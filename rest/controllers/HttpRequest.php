<?php 
namespace Src\Controller;

function unprocessableEntityResponse($errorObj)
    {
        $response['status_code_header'] = 'HTTP/1.1 400 (Bad Request)';
        $response['body'] = json_encode($errorObj);
        return $response;
    }

function notFoundResponse()
    {
        $response['status_code_header'] = 'HTTP/1.1 404 Not Found';
        $response['body'] = null;
        return $response;
    }