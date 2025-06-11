<?php

namespace Models;

use DateTimeImmutable;
use Models\Database;
use Models\MenuModel;
use Models\UserModel;

class RecensioneModel
{
    private $db;

    private int|null $voto;
    private string|null $descrizione;
    private int|null $idUtente;
    private string|null $piatto;
    private string|null $mensa;
    private string|DateTimeImmutable|null $data;
    private bool|null $modificato = false;

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
        if (isset($data["idUtente"])) {
            $this->idUtente = $data["idUtente"];
        }
        if (isset($data["piatto"])) {
            $this->piatto = $data["piatto"];
        }
        if (isset($data["mensa"])) {
            $this->mensa = $data["mensa"];
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
        return $this->idUtente != null && MenuModel::exists($this->piatto, $this->mensa);
    }

    public function refresh(): bool
    {
        if ($this->idUtente === null || $this->piatto === null || $this->mensa === null) {
            return false;
        }

        $data = self::findByFields($this->idUtente, $this->piatto, $this->mensa);
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

    public function getIdUtente(): ?int
    {
        return $this->idUtente;
    }

    public function setIdUtente(int $idUtente): void
    {
        $this->idUtente = $idUtente;
    }
    public function getUsername(): ?string
    {
        if ($this->idUtente === null) {
            return null;
        }

        $user = UserModel::findById($this->idUtente);
        return $user ? $user->getUsername() : null;
    }



    public function getPiatto(): ?string
    {
        return $this->piatto;
    }

    public function getMensa(): ?string
    {

        return $this->mensa;
    }

    public function getMenu(): ?MenuModel
    {
        return MenuModel::findByFields($this->piatto, $this->mensa);
    }

    public function setMenu(MenuModel $menu): void
    {
        $this->piatto = $menu->getPiatto();
        $this->mensa = $menu->getMensa();
    }

    public function setPiattoMensa(string $piatto, string $mensa): void
    {
        $menu = MenuModel::findByFields($piatto, $mensa);
        if ($menu) {
            $this->setMenu($menu);
        } else {
            $this->piatto = $piatto;
            $this->mensa = $mensa;
        }
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

    //-----------------Database methods----------------

    public function saveToDB(): bool
    {
        if ($this->idUtente === null || $this->piatto === null || $this->mensa === null) {
            return false;
        }


        // Assicurati che il menu esista nel database
        if (!MenuModel::exists($this->piatto, $this->mensa)) {

            return false;
        }

        $exists = self::findByFields($this->idUtente, $this->piatto, $this->mensa);
        if ($exists === null) {
            $stmt = $this->db->prepare(
                "INSERT INTO recensione (voto, descrizione, idUtente, piatto, mensa, data, modificato) VALUES (:voto, :descrizione, :idUtente, :piatto, :mensa, :data, :modificato)"
            );
            return $stmt->execute([
                "voto" => $this->voto,
                "descrizione" => $this->descrizione,
                "idUtente" => $this->idUtente,
                "piatto" => $this->piatto,
                "mensa" => $this->mensa,
                "data" => $this->data->format("Y-m-d"),
                "modificato" => 0,
            ]);
        } else {
            $stmt = $this->db->prepare(
                "UPDATE recensione SET voto = :voto, descrizione = :descrizione, modificato = :modificato, data = :data WHERE idUtente = :idUtente AND piatto = :piatto AND mensa = :mensa"
            );
            return $stmt->execute([
                "voto" => $this->voto,
                "descrizione" => $this->descrizione,
                "idUtente" => $this->idUtente,
                "piatto" => $this->piatto,
                "mensa" => $this->mensa,
                "data" => $this->data->format("Y-m-d"),
                "modificato" => 1
            ]);
        }
    }

    public function deleteFromDB(): bool
    {
        if ($this->idUtente === null || $this->piatto === null || $this->mensa === null) {
            return false;
        }

        $stmt = $this->db->prepare(
            "DELETE FROM recensione WHERE idUtente = :idUtente AND piatto = :piatto AND mensa = :mensa"
        );

        return $stmt->execute([
            "idUtente" => $this->idUtente,
            "piatto" => $this->piatto,
            "mensa" => $this->mensa,
        ]);
    }

    //-----------------Stateless methods----------------

    /**
    @param int $idUtente
    @param string $piatto
    @param string $mensa
    @return RecensioneModel|null
     */
    public static function findByFields(
        int $idUtente,
        string $piatto,
        string $mensa
    ): ?RecensioneModel {
        $db = Database::getInstance();
        $stmt = $db->prepare(
            "SELECT voto, descrizione, idUtente, piatto, mensa, data, modificato FROM recensione WHERE idUtente = :idUtente AND piatto = :piatto AND mensa = :mensa"
        );
        $stmt->execute([
            "idUtente" => $idUtente,
            "piatto" => $piatto,
            "mensa" => $mensa,
        ]);

        $data = $stmt->fetchAll(\PDO::FETCH_CLASS, RecensioneModel::class);

        if (!empty($data)) {
            return $data[0];
        }
        return null;
    }

    /** @return RecensioneModel[] */
    public static function findAll(): array
    {
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT voto, descrizione, idUtente, piatto, mensa, data, modificato FROM recensione JOIN utente ON utente.id = recensione.idUtente");
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
