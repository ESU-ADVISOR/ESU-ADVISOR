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
    private string|null $categoria;

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
        if (isset($data["categoria"])) {
            $this->categoria = $data["categoria"];
        }
    }

    public function validate(): bool
    {
        return $this->nome != "" && $this->descrizione != "" && $this->categoria != "";
    }

    public function refresh(): bool
    {
        if ($this->nome === null) {
            return false;
        }

        $data = self::findByName($this->nome);
        if ($data) {
            $this->descrizione = $data->descrizione;
            $this->categoria = $data->categoria;
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

    public function getCategoria(): ?string
    {
        return $this->categoria;
    }

    /** @param string $value */
    public function setCategoria($value): void
    {
        $this->categoria = $value;
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

    /**
     * @return string[]
     */
    public function getAllergeni(): array
    {

        $stmt = $this->db->prepare("SELECT allergene FROM piatto_allergeni WHERE piatto = :piatto");
        $stmt->execute([
            "piatto" => $this->nome,
        ]);

        $data = $stmt->fetchAll(\PDO::FETCH_COLUMN);

        if (!empty($data)) {
            return $data;
        }

        return [];
    }

    /**
     * @return string[]
     */
    public function getMense(): array
    {
        if ($this->nome === null) {
            return [];
        }

        $stmt = $this->db->prepare("SELECT mensa FROM menu WHERE piatto = :piatto");
        $stmt->execute([
            "piatto" => $this->nome,
        ]);

        $data = $stmt->fetchAll(\PDO::FETCH_COLUMN);

        if (!empty($data)) {
            return $data;
        }

        return [];
    }

    /**
     * @param string[] $allergeni
     * @return bool
     */
    public function containsAllergens(array $allergeni): bool
    {
        if (empty($allergeni)) {
            return false;
        }

        $piattoAllergeni = $this->getAllergeni();

        // Normalize both arrays for case-insensitive comparison
        $normalizedUserAllergeni = array_map('strtolower', $allergeni);
        $normalizedPiattoAllergeni = array_map('strtolower', $piattoAllergeni);

        return !empty(array_intersect($normalizedUserAllergeni, $normalizedPiattoAllergeni));
    }

    /** @return RecensioneModel[] */
    public function getRecensioni(): ?array
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
        if (count($recensioni) == 0) {
            return 0; // No reviews, no average
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
                "INSERT INTO piatto (nome, descrizione, categoria) VALUES (:nome, :descrizione, :categoria)"
            );
            return $stmt->execute([
                "nome" => $this->nome,
                "descrizione" => $this->descrizione,
                "categoria" => $this->categoria,
            ]);
        } else {
            $stmt = $this->db->prepare(
                "UPDATE piatto SET descrizione = :descrizione, categoria = :categoria WHERE nome = :nome"
            );
            return $stmt->execute([
                "nome" => $this->nome,
                "descrizione" => $this->descrizione,
                "categoria" => $this->categoria,
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

        $data = $stmt->fetchAll(\PDO::FETCH_CLASS, PiattoModel::class);

        if (!empty($data)) {
            return $data[0];
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
