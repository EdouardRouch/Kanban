<?php
include_once "../config/cors.php";
include_once "../config/database.php";
include_once "../config/response_body.php";
include_once "../objects/kanban.php";

if (!session_start()) {
    http_response_code(500);
    echo json_encode(new ResponseBody("Erreur interne"));
}

if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"]) {
    // récupérer les données de POST
    $data = json_decode(file_get_contents("php://input"));

    if (
        !empty($data->title) &&
        !empty($data->owner) &&
        isset($data->public)
    ) {
        // connexion à la BDD
        $database = new Database();
        $conn = $database->getConnection();

        // création du Kanban
        $kanban = new Kanban($conn);
        try {
            $kanban->title = substr($data->title, 0, 40);
            $kanban->public = $data->public;
            $kanban->owner = $data->owner;
            $kanban->creation_date = new DateTimeImmutable();
        } catch (TypeError $e) {
            http_response_code(400);
            echo json_encode(new ResponseBody($e->getMessage()));
        }

        try {
            $kanban->post();
            http_response_code(200);
            echo json_encode(array("message" => "Kanban créé avec succès."));
        } catch (PDOException $e) {
            if ($e->errorInfo[0] == "23000" && $e->errorInfo[1] == "1452") {
                http_response_code(400);
                echo json_encode(new ResponseBody("L'utilisateur n'existe pas."));
            } else {
                http_response_code(500);
                echo json_encode(new ResponseBody("Erreur interne."));
            }
        }
    } else {
        http_response_code(400);
        echo json_encode(new ResponseBody("Les données sont inclomplètes."));
    }
}
// si l'utilisateur n'est pas connecté, il ne peut pas créer de Kanban
else {
    http_response_code(401);
    echo json_encode(new ResponseBody("Vous devez être connecté pour créer un Kanban."));
}
