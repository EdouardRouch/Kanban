<?php
include_once "../config/cors.php";
include_once "../config/response_body.php";

if (!session_start()) {
    http_response_code(500);
    echo json_encode(new ResponseBody("Erreur interne"));
}


http_response_code(200);
if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"]) {
    echo json_encode(new ResponseBody("", array("user" => $_SESSION["username"])));
} else {
    echo json_encode(new ResponseBody("", array("user" => "")));
}
