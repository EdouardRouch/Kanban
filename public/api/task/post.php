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
        isset($data->title) &&
        isset($data->description) &&
        !empty($data->deadline) &&
        isset($data->assigned_user) &&
        !empty($data->column_id) &&
        !empty($data->kanban_id)
    ) {
        // connexion à la BDD
        $database = new Database();
        $conn = $database->getConnection();

        // Création du kanban
        $kanban = new Kanban($conn);
        try {
            $kanban->id = $data->kanban_id;
        } catch (TypeError $e) {
            http_response_code(400);
            echo json_encode(new ResponseBody($e->getMessage()));
        }

        if ($kanban->is_owner($_SESSION["username"])) {
            // création de la tâche
            $task = new Task($conn);
            try {
                $task->title = substr($data->title, 0, 20);
                $task->description = $data->description;
                $task->creation_date = new DateTimeImmutable();
                $task->deadline = DateTimeImmutable::createFromFormat("Y-m-d", $data->deadline);
                $task->assigned_user = $data->assigned_user;
                $task->column_id = $data->column_id;
            } catch (TypeError $e) {
                http_response_code(400);
                echo json_encode(new ResponseBody($e->getMessage()));
            }

            try {
                $task->post();
                $task->id = $conn->lastInsertId();
                $record = $task->get_task_by_id();
                http_response_code(200);
                echo json_encode(new ResponseBody("Tâche créée avec succès.", array("task" => $record[0])));
            } catch (PDOException $e) {
                if ($e->errorInfo[0] == "23000" && $e->errorInfo[1] == "1452") {
                    http_response_code(400);
                    echo json_encode(new ResponseBody($e->getMessage()));
                } else {
                    http_response_code(500);
                    echo json_encode(new ResponseBody($e->getMessage()));
                }
            } catch (Error $e) {
                http_response_code(500);
                echo json_encode(new ResponseBody($e->__toString()));
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
