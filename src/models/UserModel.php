<?php

namespace Models;

use Models\Database;
use DateTimeImmutable;
use Models\Enums\DimensioneTesto;
use Models\Enums\ModificaFont;
use Models\Enums\ModificaTema;

class UserModel
{
    private $db;


    private int|null $id = null;
    private string|null $username = null;
    private string|null $password = null;
    private string|DateTimeImmutable|null $dataNascita = null;

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
        if (isset($data["id"])) {
            $this->id = (int)$data["id"];
        }
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
        return $this->username != "" && $this->id !== null;
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

    public function getId(): ?int
    {
        return $this->id;
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

    /** @return DateTimeImmutable|null */
    public function getDataNascita(): ?DateTimeImmutable
    {
        if (is_string($this->dataNascita)) {
            try {
                return new DateTimeImmutable($this->dataNascita);
            } catch (\Exception $e) {
                return null;
            }
        }
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
            "SELECT * FROM recensione WHERE idUtente = :idUtente"
        );
        $stmt->execute([
            "idUtente" => $this->id,
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

        if (is_string($this->dataNascita)) {
            try {
                $this->dataNascita = new DateTimeImmutable($this->dataNascita);
            } catch (\Exception) {
                return false; 
            }
        }

        $exists = $this->id !== null && $this->id > 0; //nel caso della registrazione l'id è null
        if (!$exists) {
            $stmt = $this->db->prepare(
                "INSERT INTO utente (username, password, dataNascita) VALUES (:username, :password, :dataNascita)"
            );
            $result = $stmt->execute([
                "username" => $this->username,
                "password" => $this->password,
                "dataNascita" => $this->dataNascita->format("Y-m-d"),
            ]);

            if ($result == true) {
                $preferenzeUtente = new PreferenzeUtenteModel([
                    "idUtente" => UserModel::findByUsername($this->username)->getId(),
                    "dimensione_testo" => DimensioneTesto::MEDIO->value,
                    "modifica_font" => ModificaFont::NORMALE->value,
                    "modifica_tema" => ModificaTema::SISTEMA->value,
                ]);

                return $preferenzeUtente->saveToDB();
            } else {
                return false;
            }
        } else {
            $stmt = $this->db->prepare(
                "UPDATE utente SET username = :username, password = :password, dataNascita = :dataNascita WHERE id = :id"
            );
            return $stmt->execute([
                "username" => $this->username,
                "password" => $this->password,
                "dataNascita" => $this->dataNascita->format("Y-m-d"),
                "id" => $this->id,
            ]);
        }
    }

    public function deleteFromDB(): bool
    {
        if ($this->username == null || $this->id == null) {
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
        $data = $stmt->fetchAll(\PDO::FETCH_CLASS, UserModel::class);

        if (!empty($data)) {
            return $data[0];
        }
        return null;
    }

    public static function findById($id): ?UserModel
    {
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT * FROM utente WHERE id = :id");
        $stmt->execute([
            "id" => $id,
        ]);
        $data = $stmt->fetchAll(\PDO::FETCH_CLASS, UserModel::class);

        if (!empty($data)) {
            return $data[0];
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
