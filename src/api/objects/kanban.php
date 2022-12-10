<?php

include_once "./task.php";
include_once "./column.php";

class Kanban {
    // connexion à la BDD et nom de la table
    private $conn;
    private $table_name = "Kanban";
    private $php_date_format = "Y-m-d";
    private $sql_date_format = "%Y-%m-%d";

    // propriétés du Kanban
    public int $id;
    public string $title;
    public bool $public;
    public string $owner;
    public DateTimeImmutable $creation_date;

    private array $columns;

    // constructeur
    public function __construct($db) {
        $this->conn = $db;
    }

    // méthodes
    function fetch_kanbans($stmt) {
        $res = array();

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            extract($row);
            $user_item = array(
                "id" => $id,
                "title" => $title,
                "public" => $public,
                "owner" => $owner,
                "creation_date" => $creation_date
            );

            array_push($res, $user_item);
        }

        return $res;
    }

    // Récupère 
    private function get($query) {
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $this->fetch_kanbans($stmt);
    }

    function get_public_kanbans() {
        $query = "SELECT * 
                FROM  {$this->table_name}
                WHERE PUBLIC = true
                ORDER BY creation_date DESC";

        return $this->get($query);
    }

    function get_private_kanbans() {
        $query = "SELECT *
                FROM {$this->table_name}
                WHERE owner = {$this->owner}
                ORDER BY creation_date DESC";

        return $this->get($query);
    }

    function get_shared_kanbans(string $username) {
        $query = "SELECT T.*
                FROM {$this->table_name} T, CAN_ACCESS
                WHERE CAN_ACCESS.id = T.id
                AND   CAN_ACCESS.user_name = {$username}
                ORDER BY creation_date DESC";

        return $this->get($query);
    }

    function get_kanban_by_id() {
        $query = "SELECT *
                FROM {$this->table_name}
                WHERE id = {$this->id}";

        return $this->get($query);
    }

    function post() {
        $query = "INSERT INTO {$this->table_name} (public, title, creation_date, owner)
                VALUES (:public, 
                        :title, 
                        STR_TO_DATE(:creation_date, '{$this->sql_date_format}'), 
                        :owner)";

        // préparer la requête
        $stmt = $this->conn->prepare($query);

        $creation_date_string = $this->creation_date->format($this->php_date_format);
        $this->title = htmlspecialchars(strip_tags($this->title));
        $this->owner = htmlspecialchars(strip_tags($this->owner));

        $stmt->bindParam(":public", $this->public, PDO::PARAM_BOOL);
        $stmt->bindParam(":title", $this->title);
        $stmt->bindParam(":creation_date", $creation_date_string);
        $stmt->bindParam(":owner", $this->owner);

        // exécuter la requête
        $stmt->execute();
    }

    function put() {
        $query = "UPDATE {$this->table_name}
                SET
                    public = :public
                    title = :title
                WHERE id = :id";

        $stmt = $this->conn->prepare($query);

        $this->title = htmlspecialchars(strip_tags($this->title));

        $stmt->bindParam(":public", $this->public, PDO::PARAM_BOOL);
        $stmt->bindParam(":title", $this->title);
        $stmt->bindParam(":id", $this->id);

        $stmt->execute();
    }

    function can_access(string $username): bool {
        $query = "SELECT K.id 
                FROM {$this->table_name} K
                INNER JOIN Can_Access ON Can_Access.id = K.id 
                WHERE K.id = {$this->id}
                AND  (K.owner = {$username} OR Can_Access.user = {$username})";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return ($stmt->rowCount() > 0);
    }

    function is_owner($username): bool {
        $query = "SELECT K.id 
                FROM {$this->table_name} K
                WHERE K.owner = {$username}";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return ($stmt->rowCount() > 0);
    }

    function is_public(): bool {
        $query = "SELECT K.id
                FROM {$this->table_name} K
                WHERE K.public IS TRUE";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return ($stmt->rowCount() > 0);
    }

    function get_details(): array {
        $kanban = $this->get_kanban_by_id();
        $columns = $this->get_all_columns();

        foreach ($columns as $column) {
            $tasks = $this->get_all_tasks($column["column_id"]);
            array_push($column, $tasks);
        }

        return array("kanban" => $kanban, "columns" => $columns);
    }

    function get_all_columns(): array {
        $column = new Column($this->conn);
        return $column->get_columns_by_kanban($this->id);
    }

    function get_all_tasks($column_id): array {
        $task = new Task($this->conn);
        return $task->get_tasks_by_kanban($this->id);
    }

    function delete() {
        $query = "DELETE K, C, T, A
                FROM {$this->table_name} K
                INNER JOIN Columns    C ON C.id_Kanban = K.id
                INNER JOIN Task       T ON T.id_Columns = C.id
                INNER JOIN Can_Access A ON A.id = K.id
                WHERE K.id = {$this->id}";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();
    }
}
