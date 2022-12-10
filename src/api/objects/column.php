<?php
class Column {

    // database connection and table name
    private $conn;
    private $table_name = "Columns";

    // object properties
    public int $id;
    public string $title;
    public string $id_kanban;

    // constructor with $db as database connection
    public function __construct($db) {
        $this->conn = $db;
    }

    function fetch_columns($stmt) {
        $res = array();

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            extract($row);
            $column_item = array(
                "id" => $id,
                "title" => $title,
                "id_kanban" => $id_Kanban
            );

            array_push($res, $column_item);
        }

        return $res;
    }

    function get($query) {
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $this->fetch_columns($stmt);
    }

    function get_column_by_id() {
        $query = "SELECT id, 
                         title, 
                         id_Kanban
                FROM {$this->table_name}
                WHERE id = {$this->id}";

        return $this->get($query);
    }

    function get_columns_by_kanban($kanban_id) {
        $query = "SELECT id, 
                         title, 
                         id_Kanban
                FROM {$this->table_name}
                INNER JOIN Kanban ON Kanban.id = Columns.id_Kanban
                WHERE Kanban.id = {$kanban_id}
                ORDER BY deadline DESC";

        return $this->get($query);
    }

    function post() {
        $query = "INSERT INTO {$this->table_name} (title, id_Kanban)
                VALUES (:title,
                        :id_Kanban)";

        // préparer la requête
        $stmt = $this->conn->prepare($query);

        $this->title = htmlspecialchars(strip_tags($this->title));

        $stmt->bindParam(":title", $this->title);
        $stmt->bindParam(":id_Kanban", $this->id_kanban);

        // exécuter la requête
        $stmt->execute();
    }

    function put() {
        $query = "UPDATE {$this->table_name}
                SET
                    title = :title
                WHERE id = :id";

        $stmt = $this->conn->prepare($query);

        $this->title = htmlspecialchars(strip_tags($this->title));

        $stmt->bindParam(":title", $this->title);
        $stmt->bindParam(":id", $this->id);

        $stmt->execute();
    }

    function delete() {
        $query = "DELETE C, T 
                FROM {$this->table_name} C
                INNER JOIN Task T ON T.id_Columns = C.id
                WHERE C.id = {$this->id}";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();
    }

    function is_owner(string $username) {
        $query = "SELECT C.id
                FROM {$this->table_name} C
                INNER JOIN Kanban K ON K.id = C.id_Kanban
                WHERE C.id = {$this->id}
                AND   K.owner = {$username}";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return ($stmt->rowCount() > 0);
    }
}
