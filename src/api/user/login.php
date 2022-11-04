<?php
    include_once '../config/cors.php';
    include_once '../config/database.php';
    include_once '../objects/user.php';
    include_once '../config/response_body.php';
    include_once '../config/session_settings.php';

    $database = new Database();
    $db = $database->getConnection();
    
    $user = new User($db);
    
    // récupérer  données de POST
    $data = json_decode(file_get_contents("php://input"));
    
    // vérifier que  données ne sont pas vides
    if(
        !empty($data->username) &&
        !empty($data->password)
    ){
        // fixer  propriétés de User
        $user->name = $data->username;

        // connecter l'utilisateur
        try {
            if ($user->login($data->password)) {
                // paramètres de cookies de sessions requis par les navigateurs modernes.
                set_up_session_settings();
                // démarrer la session
                if (session_start()){
                    $_SESSION["loggedin"] = true;
                    $_SESSION["username"] = $user->name;
                    http_response_code(200);
                    echo json_encode(new ResponseBody("Connexion réussie", array("user" => $_SESSION["username"])));
                } else {
                    // la session n'a pas pu être démarrée
                    http_response_code(500);
                    echo json_encode(new ResponseBody("Erreur interne"));
                }
            } else {
                // aucune correspondance
                http_response_code(400);
                echo json_encode(new ResponseBody("Nom d'utilisateur ou mot de passe invalide"));
            }
        } catch(PDOException $e) {
            // erreur de PDO
            http_response_code(500);
            echo json_encode(new ResponseBody("Erreur interne"));
        }
    }
   // dire au client que  données sont incomplètes
    else{
        // fixer le code de réponse - 400 Bad Request
        http_response_code(400);
        echo json_encode(new ResponseBody("Les données sont incomplètes"));
    } 
?>