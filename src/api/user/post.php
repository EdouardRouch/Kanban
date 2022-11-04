<?php
    include_once '../config/cors.php';
    include_once '../config/database.php';
    include_once '../objects/user.php';
    include_once '../config/response_body.php';

    $database = new Database();
    $db = $database->getConnection();
    
    $user = new User($db);
    
    // récupérer  données de POST
    $data = json_decode(file_get_contents("php://input"));
    
    // vérifier que  données ne sont pas vides
    if(
        !empty($data->username) &&
        !empty($data->password) &&
        !empty($data->password_verify)
    ){
        // vérifier la longueur du nom d'utilisateur
        if (strlen($data->username) > 20) {
            http_response_code(400);
            echo json_encode(new ResponseBody("Le nom d'utilisateur est trop long."));
            die;
        }
        // vérifier la concordance des mots de passe
        if ($data->password != $data->password_verify) {
            // fixer le code de réponse - 400 Bad Request
            http_response_code(400);
            echo json_encode(new ResponseBody("Les mots de passes ne correspondent pas."));
            die;
        }

        // fixer  propriétés de User
        $user->name = $data->username;
        $user->password = password_hash($data->password, PASSWORD_DEFAULT);

        // créer l'utilisateur dans la BDD
        try {
            $user->create();
            // paramètres de cookies de sessions requis par les navigateurs modernes.
            session_set_cookie_params(["SameSite" => "Strict"]);
            session_set_cookie_params(["Secure" => "true"]);
            session_set_cookie_params(["HttpOnly" => "true"]);
            if(session_start()) {
                $_SESSION["loggedin"] = true;
                $_SESSION["username"] = $data->username;
                http_response_code(200);
                echo json_encode(array("message" => "Utilisateur créé avec succès"));
            } else {
                http_response_code(500);
                echo json_encode(new ResponseBody("Erreur interne"));
            }
            die;
        } catch(PDOException $e) {
            switch ([$e->errorInfo[0], $e->errorInfo[1]]){
                case ["22001", "1406"]:
                    http_response_code(400);
                    echo json_encode(new ResponseBody("Le nom d'utilisateur est trop long"));
                    break;
                case ["23000", "1062"]:
                    http_response_code(400);
                    echo json_encode(new ResponseBody("L'utilisateur ".$user->name." existe déjà"));
                    break;
                default:
                    http_response_code(500);
                    echo json_encode(new ResponseBody("Erreur interne"));
            }
        }
    }
   // dire au client que  données sont incomplètes
    else{
        // fixer le code de réponse - 400 Bad Request
        http_response_code(400);
        echo json_encode(new ResponseBody("Création de l'utilisateur impossible : les données sont incomplètes"));
    } 
?>