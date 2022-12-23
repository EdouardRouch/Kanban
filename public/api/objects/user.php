<?php
class User{
  
    // database connection and table name
    private $conn;
    private $table_name = "User";
  
    // object properties
    public $name;
    public $password;
  
    // constructor with $db as database connection
    public function __construct($db){
        $this->conn = $db;
    }

    // read products
    function read(){
        // select all query
        $query = "SELECT name
                FROM
                    " . $this->table_name . "
                ORDER BY
                    name";
    
        // prepare query statement
        $stmt = $this->conn->prepare($query);
    
        // execute query
        $stmt->execute();
        return $stmt;
    }

    function create(){
        // requête pour insérer l'utilisateur
        $query = "INSERT INTO
                    " . $this->table_name . "
                VALUES
                    (:name, :password)";
    
        // préparer la requête
        $stmt = $this->conn->prepare($query);
    
        // sanitize
        $this->name = htmlspecialchars(strip_tags($this->name));
    
        // lier les paramètres
        $stmt->bindParam(':name', $this->name);
        $stmt->bindParam(':password', $this->password);
    
        // exécuter la requête
        $stmt->execute();
    }

    function login($password){
        $query = "SELECT password
                FROM ". $this->table_name ."
                WHERE name=:name";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":name", $this->name);
        $stmt->execute();
        
        if ($stmt->rowCount() > 0) {
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
                return password_verify($password, $row["password"]);
            }
        } else {
            return false;
        }
    }
}
