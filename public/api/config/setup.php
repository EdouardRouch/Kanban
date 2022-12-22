<?php
include_once "cors.php";

$env_file_path = '.env';
$sql_file_path = 'projet_kanban_structure.sql';

if (!file_exists($env_file_path)) {
    //récupérer les données de POST
    $data = json_decode(file_get_contents("php://input"));

    if (
        !empty($data->db_host) &&
        !empty($data->db_name) &&
        !empty($data->db_user) &&
        !empty($data->db_password)
    ) {
        $env_data =
            "HOST=\"{$data->db_host}\"\n" .
            "DB_NAME=\"{$data->db_name}\"\n" .
            "USERNAME=\"{$data->db_user}\"\n" .
            "PASSWORD=\"{$data->db_password}\"";

        try {
            file_put_contents($env_file_path, $env_data);

            include_once 'database.php';
            $db = new Database();
            $conn = $db->getConnection();

            $sql = file_get_contents($sql_file_path);
            $conn->exec($sql);
        } catch (Exception $e) {
            unlink($env_file_path);
            http_response_code(500);
            echo $e->getMessage();
            die;
        }

        echo 'Base de donnée installée avec succès.';
    } else {
        http_response_code(400);
        echo 'Les données sont incomplètes';
    }
} else {
    echo 'La configuration a déjà été effectuée. Pour changer la configuration, veuillez supprimer le fichier .env';
}
