<?php
namespace Models;

use PDO;
use PDOException;

abstract class BaseModel
{
    protected $db;

    public function __construct()
    {
        $this->initConnection();
    }

    protected function initConnection(): void
    {
        if ($this->db === null) {
            try {
                $host = DB_HOST;
                $dbname = DB_NAME;
                $username = DB_USER;
                $password = DB_PASS;

                $dsn = "mysql:host=$host;dbname=$dbname;charset=utf8mb4";
                $options = [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ];

                $this->db = new PDO($dsn, $username, $password, $options);
            } catch (PDOException $e) {
                die("Database Connection Failed: " . $e->getMessage());
            }
        }
    }
}
?>
