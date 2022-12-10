<?php
include_once "../config/cors.php";
include_once "../config/database.php";
include_once "../config/response_body.php";
include_once "../objects/task.php";
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
        !empty($data->description) &&
        !empty($data->deadline) &&
        !empty($data->assigned_user) &&
        !empty($data->column_id) &&
        !empty($data->id_kanban)
    ) {
        // connexion à la BDD
        $database = new Database();
        $conn = $database->getConnection();

        // Création du kanban
        $kanban = new Kanban($conn);
        try {
            $kanban->id = $data->id_kanban;
        } catch (TypeError $e) {
            http_response_code(400);
            echo json_encode(new ResponseBody($e->getMessage()));
        }

        if ($kanban->is_owner($_SESSION["username"])) {
            // création de la tâche
            $task = new Task($conn);
            try {
                $task->title = substr($data->title, 0, 20);
                $task->description = $data->decription;
                $task->creation_date = new DateTimeImmutable();
                $task->deadline = DateTimeImmutable::createFromFormat("d/m/Y", $data->deadline);
                $task->assigned_user = $data->assigned_user;
                $task->column_id = $data->column_id;
            } catch (TypeError $e) {
                http_response_code(400);
                echo json_encode(new ResponseBody($e->getMessage()));
            }

            try {
                $task->post();
                http_response_code(200);
                echo json_encode(array("message" => "Tâche créée avec succès."));
            } catch (PDOException $e) {
                if ($e->errorInfo[0] == "23000" && $e->errorInfo[1] == "1452") {
                    http_response_code(400);
                    echo json_encode(new ResponseBody("L'utilisateur ou la colonne n'existe pas."));
                } else {
                    http_response_code(500);
                    echo json_encode(new ResponseBody("Erreur interne."));
                }
            }
        } else {
            http_response_code(401);
            echo json_encode(new ResponseBody("Vous n'êtes pas autorisé à créer cette ressource."));
        }
    } else {
        http_response_code(400);
        echo json_encode(new ResponseBody("Les données sont inclomplètes."));
    }
}
// si l'utilisateur n'est pas connecté, il ne peut pas créer de Kanban
else {
    http_response_code(401);
    echo json_encode(new ResponseBody("Vous devez être connecté pour créer une tâche."));
}
