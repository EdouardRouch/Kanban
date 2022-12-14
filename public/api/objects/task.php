<?php
class Task {
    private $conn;
    private $table_name = "Task";
    private $php_date_format = "Y-m-d";
    private $sql_date_format = "%Y-%m-%d";


    public int $id;
    public string $title;
    public string $description;
    public DateTimeImmutable $creation_date;
    public DateTimeImmutable $deadline;
    public string $assigned_user;
    public int $column_id;
    public int $kanban_id;


    public function __construct($db) {
        $this->conn = $db;
    }

    function fetch_tasks($stmt) {
        $res = array();

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            extract($row);
            $task_item = array(
                "id" => $id,
                "title" => $title,
                "description" => $description,
                "creation_date" => $creation_date,
                "deadline" => $deadline,
                "assigned_user" => $assigned_user,
                "column_id" => $column_id,
                "kanban_id" => $kanban_id,
            );

            array_push($res, $task_item);
        }

        return $res;
    }

    function get($query) {
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $this->fetch_tasks($stmt);
    }

    function get_assigned_tasks() {
        $query = "SELECT T.id, 
                         T.title, 
                         T.description,
                         T.creation_date,
                         T.deadline,
                         T.assigned_user,
                         id_Columns column_id,
                         Kanban.id kanban_id
                FROM {$this->table_name} T
                INNER JOIN Columns ON Columns.id = T.id_Columns
                INNER JOIN Kanban ON Kanban.id = Columns.id_Kanban
                WHERE assigned_user = '{$this->assigned_user}'
                ORDER BY deadline DESC";

        return $this->get($query);
    }

    function get_task_by_id() {
        $query = "SELECT T.id, 
                         T.title, 
                         T.description,
                         T.creation_date,
                         T.deadline,
                         T.assigned_user,
                         id_Columns column_id,
                         Kanban.id kanban_id
                FROM {$this->table_name} T
                INNER JOIN Columns ON Columns.id = T.id_Columns
                INNER JOIN Kanban ON Kanban.id = Columns.id_Kanban
                WHERE T.id = {$this->id}
                ORDER BY deadline DESC";

        return $this->get($query);
    }

    function get_tasks_by_column() {
        $query = "SELECT T.id, 
                         T.title, 
                         T.description,
                         T.creation_date,
                         T.deadline,
                         T.assigned_user,
                         T.id_Columns column_id,
                         C.id_Kanban kanban_id
                FROM {$this->table_name} T
                INNER JOIN Columns C ON T.id_Columns = C.id
                WHERE id_Columns = {$this->column_id}";

        return $this->get($query);
    }

    function get_tasks_by_kanban() {
        $query = "SELECT T.id, 
                         T.title, 
                         T.description,
                         creation_date,
                         T.deadline,
                         T.assigned_user,
                         T.id_Columns column_id,
                         Kanban.id kanban_id
                FROM {$this->table_name} T
                INNER JOIN Columns ON Columns.id = Task.id_Columns
                INNER JOIN Kanban ON Kanban.id = Columns.id_Kanban
                WHERE Kanban.id = {$this->kanban_id}
                ORDER BY deadline DESC";

        return $this->get($query);
    }

    function post() {
        $query = "INSERT INTO {$this->table_name} (title, description, creation_date, deadline, assigned_user, id_Columns)
                VALUES (NULLIF(:title, ''),
                        NULLIF(:description, ''),
                        STR_TO_DATE(:creation_date, '{$this->sql_date_format}'), 
                        STR_TO_DATE(:deadline, '{$this->sql_date_format}'),
                        NULLIF(:assigned_user, ''),
                        :id_Columns)";

        // pr??parer la requ??te
        $stmt = $this->conn->prepare($query);

        $creation_date_string = $this->creation_date->format($this->php_date_format);
        $deadline_string = $this->deadline->format($this->php_date_format);
        $this->title = htmlspecialchars(strip_tags($this->title));
        $this->assigned_user = htmlspecialchars(strip_tags($this->assigned_user));

        $stmt->bindParam(":title", $this->title);
        $stmt->bindParam(":description", $this->description);
        $stmt->bindParam(":creation_date", $creation_date_string);
        $stmt->bindParam(":deadline", $deadline_string);
        $stmt->bindParam("id_Columns", $this->column_id);
        $stmt->bindParam(":assigned_user", $this->assigned_user);

        // ex??cuter la requ??te
        $stmt->execute();
    }

    function put() {
        $query = "UPDATE {$this->table_name}
                SET
                    title = NULLIF(:title, ''),
                    description = NULLIF(:description, ''),
                    deadline = :deadline,
                    assigned_user = NULLIF(:assigned_user, ''),
                    id_Columns = :column_id
                WHERE id = :id";

        $stmt = $this->conn->prepare($query);

        $this->title = htmlspecialchars(strip_tags($this->title));
        $deadline_string = $this->deadline->format($this->php_date_format);
        $this->assigned_user = htmlspecialchars(strip_tags($this->assigned_user));

        $stmt->bindParam(":title", $this->title);
        $stmt->bindParam(":description", $this->description);
        $stmt->bindParam(":deadline", $deadline_string);
        $stmt->bindParam(":assigned_user", $this->assigned_user);
        $stmt->bindParam(":id", $this->id);
        $stmt->bindParam("column_id", $this->column_id);

        $stmt->execute();
    }

    function delete() {
        $query = "DELETE FROM {$this->table_name}
                WHERE id = {$this->id}";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();
    }

    function can_access(string $username): bool {
        $query = "SELECT T.id 
                FROM {$this->table_name} T
                INNER JOIN Columns C ON C.id = T.id_Columns
                INNER JOIN Kanban K ON K.id = C.id_Kanban
                INNER JOIN Can_Access CA ON CA.id = K.id
                WHERE T.id = {$this->id}
                AND  (K.owner = {$username} OR Can_Access.user = {$username})";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return ($stmt->rowCount() > 0);
    }

    function assign() {
        $query = "UPDATE {$this->table_name}
                SET
                    assigned_user = NULLIF(:assigned_user, '')
                WHERE id = :id";

        $stmt = $this->conn->prepare($query);

        $this->assigned_user = htmlspecialchars(strip_tags($this->assigned_user));
        $stmt->bindParam(":id", $this->id);
        $stmt->bindParam(":assigned_user", $this->assigned_user);

        $stmt->execute();
    }
}
