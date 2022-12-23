<?php
include_once "../config/cors.php";
include_once "../config/database.php";
include_once "../config/response_body.php";
include_once "../objects/task.php";

if (!session_start()) {
    http_response_code(500);
    echo json_encode(new ResponseBody("Erreur interne."));
}

// connexion à la BDD
$database = new Database();
$conn = $database->getConnection();

// création du Tache
$task = new Task($conn);

// si l'utilisateur est connecté à un compte
if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"]) {

    // si la demande concerne une tache en particulier
    if (isset($_GET["id"])) {
        try {
            $task->id = $_GET["id"];
        } catch (TypeError $e) {
            http_response_code(400);
            echo json_encode(new ResponseBody($e->getMessage()));
        }

        if ($task->can_access($_SESSION["username"])) {
            try {
                $records = $task->get_task_by_id();
                http_response_code(200);
                echo json_encode(new ResponseBody("", $records));
            } catch (PDOException $e) {
                http_response_code(500);
                echo json_encode(new ResponseBody("Erreur interne."));
            }
        } else {
            http_response_code(401);
            echo json_encode(new ResponseBody("Vous n'êtes pas autorisé à accéder à cette ressource."));
        }
    }
    // si la demande ne concerne pas une colonne en particulier
    else {
        $task->assigned_user = $_SESSION["username"];
        try {
            $records = $task->get_assigned_tasks();
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
    http_response_code(401);
    echo json_encode(new ResponseBody("Vous devez être connecté pour voir les taches."));
}
