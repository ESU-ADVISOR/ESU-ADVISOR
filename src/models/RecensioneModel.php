<?php
namespace Models;

use Models\Database;

class RecensioneModel
{
    private $db;

    private int|null $voto;
    private string|null $descrizione;
    private string|null $utente;
    private string|null $piatto;

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
        if (isset($data["piatto"])) {
            $this->piatto = $data["piatto"];
        }
    }

    public function validate(): bool
    {
        return $this->utente != "" && $this->piatto != "";
    }

    public function refresh(): bool
    {
        if ($this->utente === null || $this->piatto === null) {
            return false;
        }

        $data = self::findByFields($this->utente, $this->piatto);
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

    public function setDescrizione(int $descrizione): void
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
    //-----------------Relationals methods----------------

    //-----------------Database methods----------------

    public function saveToDB(): bool
    {
        if ($this->utente === null || $this->piatto === null) {
            return false;
        }

        $exists = self::findByFields($this->utente, $this->piatto);
        if (!$exists) {
            $stmt = $this->db->prepare(
                "INSERT INTO recensione (voto, descrizione, utente, piatto) VALUES (:voto, :descrizione, :utente, :piatto)"
            );
            return $stmt->execute([
                "voto" => $this->voto,
                "descrizione" => $this->descrizione,
                "utente" => $this->utente,
                "piatto" => $this->piatto,
            ]);
        } else {
            $stmt = $this->db->prepare(
                "UPDATE recensione SET voto = :voto AND descrizione = :descrizione WHERE utente = :utente AND piatto = :piatto"
            );
            return $stmt->execute([
                "voto" => $this->voto,
                "descrizione" => $this->descrizione,
                "utente" => $this->utente,
                "piatto" => $this->piatto,
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
    @param string string
    @return RecensioneModel|null
    */
    public static function findByFields(
        string $utente,
        string $piatto
    ): ?RecensioneModel {
        $db = Database::getInstance();
        $stmt = $db->prepare(
            "SELECT * FROM recensione WHERE utente = :utente AND piatto = :piatto"
        );
        $stmt->execute([
            "utente" => $utente,
            "piatto" => $piatto,
        ]);

        $data = $stmt->fetchAll(\PDO::FETCH_CLASS, RecensioneModel::class)[0];

        if (!empty($data)) {
            return $data;
        }
        return null;
    }

    /** @return RecensioneModel[] */
    public static function findAll(): array
    {
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT * FROM recensione");
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
