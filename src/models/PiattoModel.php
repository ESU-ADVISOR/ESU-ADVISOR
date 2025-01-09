<?php

namespace Models;

use Models\RecensioneModel;
use Models\Database;

class PiattoModel
{
    private $db;

    // table fields
    private string|null $nome;
    private string|null $descrizione;

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
        if (isset($data["nome"])) {
            $this->nome = $data["nome"];
        }
        if (isset($data["descrizione"])) {
            $this->descrizione = $data["descrizione"];
        }
    }

    public function validate(): bool
    {
        return $this->nome != "" && $this->descrizione != "";
    }

    public function refresh(): bool
    {
        if ($this->nome === null) {
            return false;
        }

        $data = self::findByName($this->nome);
        if ($data) {
            $this->descrizione = $data->descrizione;
            return true;
        }
        return false;
    }

    public function getNome(): ?string
    {
        return $this->nome;
    }

    /** @param string $value */
    public function setNome($value): void
    {
        $this->nome = $value;
    }

    public function getDescrizione(): ?string
    {
        return $this->descrizione;
    }

    /** @param string $value */
    public function setDescrizione($value): void
    {
        $this->descrizione = $value;
    }

    //-----------------Relationals methods----------------

    public function getImage(): ?string
    {
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT * FROM piatto_foto WHERE piatto = :piatto");
        $stmt->execute([
            "piatto" => $this->nome,
        ]);

        $data = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        if (!empty($data[0])) {

            return $data[0]["foto"];
        }
        return null;
    }

    /** @return RecensioneModel[] */
    public function getRecensioni(): array
    {
        if ($this->nome == null) {
            return null;
        }

        $recensioni = RecensioneModel::findAll();

        $result = [];

        foreach ($recensioni as $recensione) {
            if ($recensione->getPiatto() == $this->nome) {
                $result[] = $recensione;
            }
        }

        return $result;
    }

    public function getAvgVote(): ?int
    {
        $recensioni = $this->getRecensioni();
        $avg = 0.0;
        foreach ($recensioni as $recensione) {
            $avg += $recensione->getVoto();
        }
        $avg = $avg / count($recensioni);
        return intval($avg);
    }

    //-----------------Database methods----------------

    public function saveToDB(): bool
    {
        if ($this->nome == null) {
            return false;
        }

        $exists = self::findByName($this->nome);
        if (!$exists) {
            $stmt = $this->db->prepare(
                "INSERT INTO piatto (nome, descrizione) VALUES (:nome, :descrizione)"
            );
            return $stmt->execute([
                "nome" => $this->nome,
                "descrizione" => $this->descrizione,
            ]);
        } else {
            $stmt = $this->db->prepare(
                "UPDATE piatto SET descrizione = :descrizione WHERE nome = :nome"
            );
            return $stmt->execute([
                "nome" => $this->nome,
                "descrizione" => $this->descrizione,
            ]);
        }
    }

    public function deleteFromDB(): bool
    {
        if ($this->nome == null || $this->descrizione == null) {
            return false;
        }

        $stmt = $this->db->prepare("DELETE FROM piatto WHERE nome = :nome");

        return $stmt->execute([
            "nome" => $this->nome,
        ]);
    }

    //-----------------Stateless methods----------------

    /** @param string $name */
    public static function findByName($name): ?PiattoModel
    {
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT * FROM piatto WHERE nome = :nome");
        $stmt->execute([
            "nome" => $name,
        ]);

        $data = $stmt->fetchAll(\PDO::FETCH_CLASS, PiattoModel::class)[0];

        if (!empty($data)) {
            return $data;
        }
        return null;
    }
    /** @return PiattoModel[] */
    public static function findAll(): array
    {
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT * FROM piatto");
        $stmt->execute();
        $piatti = $stmt->fetchAll(\PDO::FETCH_CLASS, PiattoModel::class);

        if (!empty($piatti)) {
            return $piatti;
        }

        return [];
    }
}
