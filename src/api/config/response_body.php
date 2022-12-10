<?php
class ResponseBody {
    // Propriétés
    public $message;
    public $records;

    // Constructeur
    public function __construct($message, $records = array()) {
        $this->message = $message;
        $this->records = $records;
    }
}
