<?php
include_once "../config/cors.php";
include_once "../config/database.php";
include_once "../config/response_body.php";
include_once "../objects/kanban.php";

if (!session_start()) {
    http_response_code(500);
    echo json_encode(new ResponseBody("Erreur interne."));
}

// connexion à la BDD
$database = new Database();
$conn = $database->getConnection();

// création du Kanban
$kanban = new Kanban($conn);

// si l'utilisateur est connecté à un compte
if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] == true) {
    // fixer le propriétaire du Kanban
    $kanban->owner = $_SESSION["username"];

    // si la demande concerne un kanban en particulier
    if (isset($_GET['id'])) {
        $kanban->id = $_GET["id"];

        try {
            if (
                $kanban->is_public() ||
                $kanban->is_owner($_SESSION['username']) ||
                $kanban->can_access($_SESSION['username'])
            ) {
                $records = $kanban->get_details();
                http_response_code(200);
                echo json_encode(new ResponseBody("", $records));
            } else {
                http_response_code(401);
                echo json_encode(new ResponseBody("Vous n'êtes pas autorisé à accéder à cette ressource."));
            }
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(new ResponseBody($e->__toString()));
        }
    }
    // si la demande ne concerne pas un kanban particulier
    else {
        try {
            // récupérer tous les kanbans accessibles à l'utilisateur
            $public = $kanban->get_public_kanbans();
            $private = $kanban->get_private_kanbans();
            $shared = $kanban->get_shared_kanbans($_SESSION["username"]);
            $records = array("public" => $public, "private" => $private, "shared" => $shared);

            http_response_code(200);
            echo json_encode(new ResponseBody("", $records));
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(new ResponseBody($e->getMessage()));
        }
    }
}
// si l'utilisateur n'est pas connecté à un compte
else {
    // Si la requête concerne un kanban en particulier
    if (isset($_GET["id"])) {
        $kanban->id = $_GET["id"];
        if ($kanban->is_public()) {
            try {
                $records = $kanban->get_details();
                http_response_code(200);
                echo json_encode(new ResponseBody("", $records));
            } catch (PDOException $e) {
                http_response_code(500);
                echo json_encode(new ResponseBody("Erreur interne"));
            }
        } else {
            http_response_code(401);
            echo json_encode(new ResponseBody("Vous n'êtes pas autorisé à accéder à cette ressource."));
        }
    }
    // Sinon
    else {
        try {
            $public = $kanban->get_public_kanbans();
            http_response_code(200);
            echo json_encode(new ResponseBody("", array("public" => $public)));
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(new ResponseBody("Erreur interne"));
        }
    }
}
