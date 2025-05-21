<?php

namespace Models;

use Models\Database;
class MenseModel
{
    private $db;

    // table fields
    private string|null $nome;
    private string|null $indirizzo;
    private string|null $telefono;
    private string|null $maps_link;

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
        if (isset($data["indirizzo"])) {
            $this->indirizzo = $data["indirizzo"];
        }
        if (isset($data["telefono"])) {
            $this->telefono = $data["telefono"];
        }
        if (isset($data["maps_link"])) {
            $this->maps_link = $data["maps_link"];
        }
    }

    public function validate(): bool
    {
        return $this->nome != "" && $this->indirizzo != "";
    }

    public function refresh(): bool
    {
        if ($this->nome === null) {
            return false;
        }

        $data = self::findByName($this->nome);
        if ($data) {
            $this->indirizzo = $data->indirizzo;
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

    public function getIndirizzo(): ?string
    {
        return $this->indirizzo;
    }

    /** @param string $value */
    public function setIndirizzo($value): void
    {
        $this->indirizzo = $value;
    }

    public function getTelefono(): ?string
    {
        return $this->telefono;
    }

    /** @param string $value */
    public function setTelefono($value): void
    {
        $this->telefono = $value;
    }

    public function getMapsLink(): ?string
    {
        return $this->maps_link;
    }

    /** @param string $value */
    public function setMapsLink($value): void
    {
        $this->maps_link = $value;
    }

    //-----------------Relationals methods----------------

    /**
     * @return PiattoModel[]|null
     */
    public function getPiatti(): ?array
    {
        if ($this->nome === null) {
            return null;
        }

        $stmt = $this->db->prepare(
            "SELECT * FROM menu WHERE mensa = :mensa"
        );

        $stmt->execute([
            "mensa" => $this->nome,
        ]);

        $data = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $piatti = [];

        foreach ($data as $piattoData) {
            $piatto = PiattoModel::findByName($piattoData["piatto"]);
            if ($piatto !== null) {
                $piatti[] = $piatto;
            }
        }
        return $piatti;
    }

    /** 
     * @return array<string,string>
     * @return null
     */
    public function getMenseOrari(): ?array
    {
        if ($this->nome === null) {
            return null;
        }

        $stmt = $this->db->prepare(
            "SELECT CASE 
                WHEN giornoSettimana = 1 THEN 'Lunedì'
                WHEN giornoSettimana = 2 THEN 'Martedì'
                WHEN giornoSettimana = 3 THEN 'Mercoledì'
                WHEN giornoSettimana = 4 THEN 'Giovedì'
                WHEN giornoSettimana = 5 THEN 'Venerdì'
                WHEN giornoSettimana = 6 THEN 'Sabato'
                WHEN giornoSettimana = 7 THEN 'Domenica'
            END AS Giorno, orainizio, orafine, mensa
            FROM orarioapertura
            WHERE mensa = :mensa"
        );
        $stmt->execute([
            "mensa" => $this->nome,
        ]);
        $data = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        if (!empty($data)) {
            return $data;
        }
        return null;
    }

    //-----------------Database methods----------------

    public function saveToDB(): bool
    {
        if ($this->nome === null) {
            return false;
        }

        $exists = self::findByName($this->nome);
        if (!$exists) {
            $stmt = $this->db->prepare(
                "INSERT INTO mensa (nome, indirizzo) VALUES (:nome, :indirizzo)"
            );
            return $stmt->execute([
                "nome" => $this->nome,
                "indirizzo" => $this->indirizzo,
            ]);
        } else {
            $stmt = $this->db->prepare(
                "UPDATE mensa SET indirizzo = :indirizzo WHERE nome = :nome"
            );
            return $stmt->execute([
                "nome" => $this->nome,
                "indirizzo" => $this->indirizzo,
            ]);
        }
    }

    public function deleteFromDB(): bool
    {
        if ($this->nome === null) {
            return false;
        }

        $stmt = $this->db->prepare("DELETE FROM mensa WHERE nome = :nome");
        return $stmt->execute([
            "nome" => $this->nome,
        ]);
    }

    //-----------------Stateless methods----------------

    /**
    * @param string $name
    * @return MenseModel|null
     */
    public static function findByName($name): ?MenseModel
    {
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT * FROM mensa WHERE nome = :nome");
        $stmt->execute([
            "nome" => $name,
        ]);

        /** @var MenseModel $data */
        $data = $stmt->fetchAll(\PDO::FETCH_CLASS, MenseModel::class)[0];

        if (!empty($data)) {
            return $data;
        }
        return null;
    }

    /** @return MenseModel[] */
    public static function findAll(): array
    {
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT * FROM mensa");
        $stmt->execute();
        $mense = $stmt->fetchAll(\PDO::FETCH_CLASS, MenseModel::class);

        if (!empty($mense)) {
            return $mense;
        }

        return [];
    }
}
