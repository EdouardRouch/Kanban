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
        isset($data->public) &&
        !empty($data->owner) &&
        !empty($data->id)
    ) {
        // connexion à la BDD
        $database = new Database();
        $conn = $database->getConnection();

        // création du Kanban
        $kanban = new Kanban($conn);
        try {
            $kanban->id = $data->kanban_id;
        } catch (Error $e) {
            http_response_code(400);
            echo json_encode(new ResponseBody($e->getMessage()));
        }
        if ($kanban->is_owner($_SESSION["username"])) {
            try {
                $kanban->title = substr($data->title, 0, 40);
                $kanban->public = $data->public;
                $kanban->owner = $data->owner;
                $kanban->id = $data->id;

                if (
                    !empty($data->authorized_users) &&
                    isset($data->unauthorized_users)
                ) {
                    $kanban->authorized_users = $data->authorized_users;
                    $kanban->unauthorized_users = $data->unauthorized_users;
                }
            } catch (TypeError $e) {
                http_response_code(400);
                echo json_encode(new ResponseBody($e->__toString()));
            }

            try {
                $kanban->put();
                http_response_code(200);
                echo json_encode(new ResponseBody("Kanban modifié avec succès."));
            } catch (PDOException $e) {
                http_response_code(500);
                echo json_encode(new ResponseBody($e->__toString()));
            }
        } else {
            http_response_code(401);
            echo json_encode(new ResponseBody("Vous n'êtes pas autorisé à modifier cette ressource."));
        }
    } else {
        http_response_code(400);
        echo json_encode(new ResponseBody("Les données sont incomplètes."));
    }
}
// si l'utilisateur n'est pas connecté, il ne peut pas modifier de Kanban
else {
    http_response_code(401);
    echo json_encode(new ResponseBody("Vous devez être connecté pour modifier un Kanban."));
}
