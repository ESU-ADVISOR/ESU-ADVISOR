<?php

namespace Models;

use Models\Database;
use DateTimeImmutable;

class UserModel
{
    private $db;


    // table fields
    private string|null $username;
    private string|null $email;
    private string|null $hashedPassword;
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
        if (isset($data["email"])) {
            $this->email = $data["email"];
        }
        if (isset($data["password"])) {
            $this->hashedPassword = password_hash(
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
        return $this->email != "";
    }

    public function refresh(): bool
    {
        if ($this->email === null) {
            return false;
        }

        $data = self::findByEmail($this->email);
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

    public function getEmail(): string
    {
        return $this->email;
    }

    /** @param string $value */
    public function setEmail($value): void
    {
        $this->email = $value;
    }

    /** @param string $password */
    public function setPassword($password): void
    {
        $this->hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    }

    /**
     * @param mixed $value @param string $password */
    public function setHashedPassword($value): void
    {
        $this->hashedPassword = $value;
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
            "utente" => $this->email,
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
        if ($this->email == null || $this->username == "") {
            return false;
        }
        $exists = self::findByEmail($this->email);
        if (!$exists) {
            $stmt = $this->db->prepare(
                "INSERT INTO utente (username, email, password, dataNascita) VALUES (:username, :email, :password, :dataNascita)"
            );
            return $stmt->execute([
                "username" => $this->username,
                "email" => $this->email,
                "password" => $this->hashedPassword,
                "dataNascita" => $this->dataNascita->format("Y-m-d"),
            ]);
        } else {
            $stmt = $this->db->prepare(
                "UPDATE utente SET username = :username, password = :password, dataNascita = :dataNascita WHERE email = :email"
            );
            return $stmt->execute([
                "username" => $this->username,
                "email" => $this->email,
                "password" => $this->hashedPassword,
                "dataNascita" => $this->dataNascita->format("Y-m-d"),
            ]);
        }
    }

    public function deleteFromDB(): bool
    {
        if ($this->email == null) {
            return false;
        }

        $stmt = $this->db->prepare("DELETE FROM utente WHERE email = :email");
        return $stmt->execute([
            "email" => $this->email,
        ]);
    }

    //-----------------Stateless methods----------------

    public static function isEmailTaken(string $email): bool
    {
        $db = Database::getInstance();
        $stmt = $db->prepare(
            "SELECT COUNT(*) FROM utente WHERE email = :email"
        );
        $stmt->execute(["email" => $email]);
        return $stmt->fetchColumn() > 0;
    }

    public static function isUserValid(string $email): bool
    {
        $db = Database::getInstance();
        $stmt = $db->prepare(
            "SELECT COUNT(*) FROM utente WHERE email = :email"
        );
        $stmt->execute(["email" => $email]);
        return $stmt->fetchColumn() > 0;
    }

    public static function authenticate(string $email, string $password): bool
    {
        $db = Database::getInstance();
        $stmt = $db->prepare(
            "SELECT password FROM utente WHERE email = :email"
        );
        $stmt->execute(["email" => $email]);
        $hashedPassword = $stmt->fetchColumn();
        if ($hashedPassword && password_verify($password, $hashedPassword)) {
            if (password_needs_rehash($hashedPassword, PASSWORD_DEFAULT)) {
                $newHash = password_hash($password, PASSWORD_DEFAULT);
                $updateSql =
                    "UPDATE utente SET password = :password WHERE email = :email";
                $updateStmt = $db->prepare($updateSql);
                $updateStmt->execute([
                    ":password" => $newHash,
                    ":email" => $email,
                ]);
            }
            return true;
        } else {
            return false;
        }
    }

    /**
     * @param mixed $email @param string $$email
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

    /**
     * @param mixed $email @param string $$email
     */
    public static function findByEmail($email): ?UserModel
    {
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT * FROM utente WHERE email = :email");
        $stmt->execute([
            "email" => $email,
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
