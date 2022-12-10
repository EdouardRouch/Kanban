<?php
require __DIR__.'/../vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__.'/../');
$dotenv->load();

class Database{
    
    public $conn;
  
    // get the database connection
    public function getConnection(){
  
        $this->conn = null;
  
        try{
            $this->conn = new PDO("mysql:host=" . $_ENV["HOST"] . ";dbname=" . $_ENV["DB_NAME"], $_ENV["USERNAME"], $_ENV["PASSWORD"]);
            $this->conn->exec("set names utf8");
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }catch(PDOException $exception){
            echo "Connection error: " . $exception->getMessage();
        }
  
        return $this->conn;
    }
}

function verify_data_type(mixed $data, string $type){
    if (gettype($data) != $type) {
        http_response_code(400);
        echo json_encode(["message" => $data." doit être de type ".$type]);
    }
}
