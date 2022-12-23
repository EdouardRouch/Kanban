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

// si l'utilisateur est connecté à un compte
if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"]) {
    // si la demande concerne les utilisateur correspondant au préfixe
    if (isset($_GET["startswith"])) {
        try {
            $query = "SELECT name
                FROM USER
                WHERE name LIKE :pattern
                AND   name != :owner
                LIMIT 10";

            $stmt = $conn->prepare($query);
            $username = htmlspecialchars(strip_tags($_GET["startswith"]));
            $pattern = $username . '%';
            $stmt->execute([":pattern" => $pattern, ":owner" => $_SESSION["username"]]);

            $res = $stmt->fetchAll(PDO::FETCH_COLUMN);
            http_response_code(200);
            echo json_encode(new ResponseBody("", $res));
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(new ResponseBody($e->__toString()));
        }
    }
}
// si l'utilisateur n'est pas connecté à un compte
else {
    http_response_code(401);
    echo json_encode(new ResponseBody("Vous n'êtes pas autorisé à accéder à cette ressource."));
}
