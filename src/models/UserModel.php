<?php

namespace Models;

use Models\Database;
use DateTimeImmutable;

class UserModel
{
    private $db;


    // table fields
    private string|null $username;
    private string|null $password;
    private string|DateTimeImmutable|null $dataNascita;

    /**
     * @param array<int,string> $data
     */
    public function __construct(array $data = [])
    {
        $this->db = Database::getInstance();
        if (!empty($data)) {
            $this->fill($data);
        }
    }

    //-------------Stateful methods----------------

    /**
     * @param array<int,string> $data
     */
    private function fill(array $data): void
    {
        if (isset($data["username"])) {
            $this->username = $data["username"];
        }
        if (isset($data["password"])) {
            $this->password = password_hash(
                $data["password"],
                PASSWORD_DEFAULT
            );
        }
        if (isset($data["dataNascita"])) {
            $this->dataNascita = new DateTimeImmutable($data["dataNascita"]);
        }
    }

    public function validate(): bool
    {
        return $this->username != "";
    }

    public function refresh(): bool
    {
        if ($this->username === null) {
            return false;
        }

        $data = self::findByUsername($this->username);
        if ($data) {
            self::__construct($data);
            return true;
        }
        return false;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    /** @param string $value */
    public function setUsername($value): void
    {
        $this->username = $value;
    }



    /** @param string $password */
    public function setClearPassword($password): void
    {
        $this->password = password_hash($password, PASSWORD_DEFAULT);
    }

    /**
     * @param mixed $value @param string $password */
    public function setPassword($value): void
    {
        $this->password = $value;
    }

    /** @return DateTimeImmutable */
    public function getDataNascita(): ?DateTimeImmutable
    {
        return $this->dataNascita;
    }

    /** @param string $value */
    public function setDataNascita($value): void
    {
        $this->dataNascita = new DateTimeImmutable($value);
    }

    //-----------------Relationals methods----------------

    /** @return RecensioneModel[] */
    public function getRecensioni(): array
    {
        $stmt = $this->db->prepare(
            "SELECT * FROM recensione WHERE utente = :utente"
        );
        $stmt->execute([
            "utente" => $this->username,
        ]);
        $data = $stmt->fetchAll(\PDO::FETCH_CLASS, RecensioneModel::class);
        if (!empty($data)) {
            return $data;
        }
        return [];
    }

    //-----------------Database methods----------------
    public function saveToDB(): bool
    {
        if ($this->username == "") {
            return false;
        }
        $exists = self::findByUsername($this->username);
        if (!$exists) {
            $stmt = $this->db->prepare(
                "INSERT INTO utente (username, password, dataNascita) VALUES (:username, :password, :dataNascita)"
            );
            return $stmt->execute([
                "username" => $this->username,
                "password" => $this->password,
                "dataNascita" => $this->dataNascita->format("Y-m-d"),
            ]);
        } else {
            $stmt = $this->db->prepare(
                "UPDATE utente SET username = :username, password = :password, dataNascita = :dataNascita WHERE username = :username"
            );
            return $stmt->execute([
                "username" => $this->username,
                "password" => $this->password,
                "dataNascita" => $this->dataNascita->format("Y-m-d"),
            ]);
        }
    }

    public function deleteFromDB(): bool
    {
        if ($this->username == null) {
            return false;
        }

        $stmt = $this->db->prepare("DELETE FROM utente WHERE username = :username");
        return $stmt->execute([
            "username" => $this->username,
        ]);
    }

    //-----------------Stateless methods----------------

    public static function isUsernameTaken(string $username): bool
    {
        $db = Database::getInstance();
        $stmt = $db->prepare(
            "SELECT COUNT(*) FROM utente WHERE username = :username"
        );
        $stmt->execute(["username" => $username]);
        return $stmt->fetchColumn() > 0;
    }

    public static function isUserValid(string $username): bool
    {
        $db = Database::getInstance();
        $stmt = $db->prepare(
            "SELECT COUNT(*) FROM utente WHERE username = :username"
        );
        $stmt->execute(["username" => $username]);
        return $stmt->fetchColumn() > 0;
    }

    public static function authenticate(string $username, string $clearPassword): bool
    {
        $db = Database::getInstance();
        $stmt = $db->prepare(
            "SELECT password FROM utente WHERE username = :username"
        );
        $stmt->execute(["username" => $username]);
        $password = $stmt->fetchColumn();
        if ($password && password_verify($clearPassword, $password)) {
            if (password_needs_rehash($password, PASSWORD_DEFAULT)) {
                $newHash = password_hash($password, PASSWORD_DEFAULT);
                $updateSql =
                    "UPDATE utente SET password = :password WHERE username = :username";
                $updateStmt = $db->prepare($updateSql);
                $updateStmt->execute([
                    ":password" => $newHash,
                    ":username" => $username,
                ]);
            }
            return true;
        } else {
            return false;
        }
    }

    /**
     * @param mixed $username @param string $$username
     */
    public static function findByUsername($username): ?UserModel
    {
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT * FROM utente WHERE username = :username");
        $stmt->execute([
            "username" => $username,
        ]);
        $data = $stmt->fetchAll(\PDO::FETCH_CLASS, UserModel::class)[0];

        if (!empty($data)) {
            return $data;
        }
        return null;
    }

    /** @return UserModel[] */
    public static function findAll(): array
    {
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT * FROM utente");
        $stmt->execute();
        /** @var MenuModel[] */
        $users = $stmt->fetchAll(
            \PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE,
            UserModel::class
        );
        return $users;
    }
}
