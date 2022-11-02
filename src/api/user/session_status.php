<?php
    include_once '../config/cors.php';
    include_once '../config/response_body.php';

    if (!session_start()) {
        http_response_code(500);
        echo json_encode(new ResponseBody("Erreur interne"));
    }

    if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"]) {
        http_response_code(200);
        echo json_encode(new ResponseBody(records: array("user" => $_SESSION["username"])));
    } else {
        http_response_code(200);
        echo json_encode(new ResponseBody(records: array("user" => "")));
    }
?>