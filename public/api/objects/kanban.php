<?php
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
    public string $owner = '';
    public DateTimeImmutable $creation_date;
    public array $authorized_users;
    public array $unauthorized_users;

    // constructeur
    public function __construct($db) {
        $this->conn = $db;
    }

    // méthodes
    public function getId(): int {
        return $this->id;
    }

    function fetch_kanbans($stmt) {
        $res = array();

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            extract($row);
            $kanban_item = array(
                "id" => $id,
                "title" => $title,
                "public" => (bool) $public,
                "owner" => $owner,
                "creation_date" => $creation_date
            );

            array_push($res, $kanban_item);
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
                FROM  {$this->table_name} K
                WHERE PUBLIC = true
                AND   K.owner != '{$this->owner}'
                ORDER BY creation_date DESC";

        return $this->get($query);
    }

    function get_private_kanbans() {
        $query = "SELECT *
                FROM {$this->table_name} T
                WHERE T.owner = '{$this->owner}'
                ORDER BY creation_date DESC";

        return $this->get($query);
    }

    function get_shared_kanbans(string $username) {
        $query = "SELECT T.*
                FROM {$this->table_name} T, Can_Access
                WHERE Can_Access.id = T.id
                AND   Can_Access.user = '{$username}'
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
                    public = :public,
                    title = :title
                WHERE id = :id";

        $stmt = $this->conn->prepare($query);

        $this->title = htmlspecialchars(strip_tags($this->title));

        $stmt->bindParam(":public", $this->public, PDO::PARAM_BOOL);
        $stmt->bindParam(":title", $this->title);
        $stmt->bindParam(":id", $this->id);

        $stmt->execute();

        if (!empty($this->authorized_users)) {
            $this->delete_unauthorized_users();
            $this->put_authorized_users();
        }
    }

    function put_authorized_users() {
        $query = "INSERT IGNORE INTO Can_ACCESS (id, user) VALUES (:id, :user)";

        $stmt = $this->conn->prepare($query);
        try {
            $this->conn->beginTransaction();
            foreach ($this->authorized_users as $user) {
                $user = htmlspecialchars(strip_tags($user));
                $stmt->execute([":id" => $this->id, ":user" => $user]);
            }
            $this->conn->commit();
        } catch (PDOException $e) {
            $this->conn->rollback();
            throw $e;
        }
    }

    function delete_unauthorized_users() {
        $query = "DELETE FROM Can_Access WHERE id = :id AND user = :user";

        $stmt = $this->conn->prepare($query);
        try {
            $this->conn->beginTransaction();
            foreach ($this->unauthorized_users as $user) {
                $user = htmlspecialchars(strip_tags($user));
                $stmt->execute([":id" => $this->id, ":user" => $user]);
            }
            $this->conn->commit();
        } catch (PDOException $e) {
            $this->conn->rollback();
            throw $e;
        }
    }

    function can_access(string $username): bool {
        $query = "SELECT id 
                FROM Can_Access
                WHERE id = {$this->id}
                AND  Can_Access.user = '{$username}'";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return ($stmt->rowCount() > 0);
    }

    function is_owner($username): bool {
        $query = "SELECT id 
                FROM {$this->table_name}
                WHERE owner = '{$username}'";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return ($stmt->rowCount() > 0);
    }

    function is_public(): bool {
        $query = "SELECT id
                FROM {$this->table_name} 
                WHERE public IS TRUE";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return ($stmt->rowCount() > 0);
    }

    function get_details(): array {
        $kanban = $this->get_kanban_by_id()[0];
        $columns = $this->get_all_columns();
        $auth_users = $this->get_authorized_users();

        foreach ($columns as &$column) {
            $tasks = $this->get_tasks_by_column($column["id"]);
            $column["tasks"] = $tasks;
        }

        return array("kanban" => $kanban, "columns" => $columns, "authorized_users" => $auth_users);
    }

    function get_all_columns(): array {
        include_once "column.php";
        $column = new Column($this->conn);
        return $column->get_columns_by_kanban($this->id);
    }

    function get_tasks_by_column($column_id): array {
        include_once "task.php";
        $task = new Task($this->conn);
        $task->column_id = (int) $column_id;
        return $task->get_tasks_by_column();
    }

    function get_authorized_users(): array {
        $query = "SELECT user
                FROM Can_Access
                WHERE id = {$this->id}";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_COLUMN);
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
