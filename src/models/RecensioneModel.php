<?php

namespace Models;

use DateTimeImmutable;
use Models\Database;

class RecensioneModel
{
    private $db;

    private int|null $voto;
    private string|null $descrizione;
    private string|null $utente;
    private int|null $idutente;
    private string|null $piatto;
    private string|DateTimeImmutable|null $data;
    private bool|null $modificato;

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
        if (isset($data["voto"])) {
            $this->voto = $data["voto"];
        }
        if (isset($data["descrizione"])) {
            $this->descrizione = $data["descrizione"];
        }
        if (isset($data["utente"])) {
            $this->utente = $data["utente"];
        }
        if (isset($data["idutente"])) {
            $this->idutente = (int)$data["idutente"];
        }
        if (isset($data["piatto"])) {
            $this->piatto = $data["piatto"];
        }
        if (isset($data["data"])) {
            $this->data = new DateTimeImmutable($data["data"]);
        }
        if (isset($data["modificato"])) {
            $this->modificato = (bool)$data["modificato"];
        }
    }

    public function validate(): bool
    {
        return $this->idutente != null && $this->piatto != "";
    }

    public function refresh(): bool
    {
        if ($this->utente === null || $this->piatto === null) {
            return false;
        }

        $data = self::findByFields($this->idutente, $this->piatto);
        if ($data) {
            $this->descrizione = $data->descrizione;
            $this->voto = $data->voto;
            return true;
        }
        return false;
    }

    public function getVoto(): ?int
    {
        return $this->voto;
    }

    public function setVoto(int $voto): void
    {
        $this->voto = $voto;
    }

    public function getDescrizione(): ?string
    {
        return $this->descrizione;
    }

    public function setDescrizione(string $descrizione): void
    {
        $this->descrizione = $descrizione;
    }

    public function getUtente(): ?string
    {
        return $this->utente;
    }

    public function setUtente(string $utente): void
    {
        $this->utente = $utente;
    }

    public function getPiatto(): ?string
    {
        return $this->piatto;
    }

    public function setPiatto(string $piatto): void
    {
        $this->piatto = $piatto;
    }

    public function getData(): ?DateTimeImmutable
    {
        if (is_string($this->data)) {
            try {
                $this->data = new DateTimeImmutable($this->data);
            } catch (\Exception $e) {
                $this->data = null;
            }
        }

        return $this->data;
    }

    /** @param string $value */
    public function setData($value): void
    {
        $this->data = new DateTimeImmutable($value);
    }

    public function isEdited(): ?bool
    {
        return $this->modificato;
    }

    /** @param bool $value */
    public function setEdited($value): void
    {
        $this->modificato = (bool)$value;
    }
    //-----------------Relationals methods----------------

    //-----------------Database methods----------------

    public function saveToDB(): bool
    {
        if ($this->utente === null || $this->piatto === null) {
            return false;
        }

        $exists = self::findByFields($this->utente, $this->piatto);
        if ($exists === null) {
            $stmt = $this->db->prepare(
                "INSERT INTO recensione (voto, descrizione, utente, piatto, data, modificato) VALUES (:voto, :descrizione, :utente, :piatto, :data, :modificato)"
            );
            return $stmt->execute([
                "voto" => $this->voto,
                "descrizione" => $this->descrizione,
                "utente" => $this->idutente,
                "piatto" => $this->piatto,
                "data" => $this->data->format("Y-m-d"),
                "modificato" => false,
            ]);
        } else {
            $stmt = $this->db->prepare(
                "UPDATE recensione SET voto = :voto, descrizione = :descrizione, modificato = :modificato, data = :data WHERE utente = :utente AND piatto = :piatto"
            );
            return $stmt->execute([
                "voto" => $this->voto,
                "descrizione" => $this->descrizione,
                "utente" => $this->idutente,
                "piatto" => $this->piatto,
                "data" => $this->data->format("Y-m-d"),
                "modificato" => true
            ]);
        }
    }

    public function deleteFromDB(): bool
    {
        if ($this->utente === null || $this->piatto === null) {
            return false;
        }

        $stmt = $this->db->prepare(
            "DELETE FROM recensione WHERE utente = :utente AND piatto = :piatto"
        );

        return $stmt->execute([
            "utente" => $this->utente,
            "piatto" => $this->piatto,
        ]);
    }

    //-----------------Stateless methods----------------

    /**
    @param string $utente
    @param string $piatto
    @return RecensioneModel|null
     */
    public static function findByFields(
        string $utente,
        string $piatto
    ): ?RecensioneModel {
        $db = Database::getInstance();
        $stmt = $db->prepare(
            "SELECT voto, descrizione, username as utente, utente as idutente, piatto, data, modificato FROM recensione JOIN utente ON utente.id = recensione.utente WHERE username = :utente AND piatto = :piatto"
        );
        $stmt->execute([
            "utente" => $utente,
            "piatto" => $piatto,
        ]);

        $data = $stmt->fetchAll(\PDO::FETCH_CLASS, RecensioneModel::class);

        if (!empty($data) && count($data) == 1) {
            return $data[0];
        }
        return null;
    }

    /** @return RecensioneModel[] */
    public static function findAll(): array
    {
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT voto, descrizione, username as utente, utente as idutente, piatto, data, modificato FROM recensione JOIN utente ON utente.id = recensione.utente");
        $stmt->execute();

        $recensioni = $stmt->fetchAll(
            \PDO::FETCH_CLASS,
            RecensioneModel::class
        );

        if (!empty($recensioni)) {
            return $recensioni;
        }

        return [];
    }
}
