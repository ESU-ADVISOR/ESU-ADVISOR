<?php

namespace Models;

use Models\Database;
use Models\UserModel;
use Models\MenseModel;
use PDO;
use Models\Enums\DimensioneTesto;
use Models\Enums\ModificaFont;
use Models\Enums\ModificaTema;

class PreferenzeUtenteModel
{
    private $db;

    private int|null $idUtente = null;
    private DimensioneTesto|null $dimensioneTesto = null;
    private ModificaFont|null $modificaFont = null;
    private ModificaTema|null $modificaTema = null;
    private string|null $mensaPreferita = null;

    /**
     * @param array<string, mixed> $data
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
     * @param array<string, mixed> $data
     */
    private function fill(array $data): void
    {
        if (isset($data["idUtente"])) {
            $this->idUtente = $data["idUtente"];
        }
        if (isset($data["dimensione_testo"])) {
            $this->dimensioneTesto = DimensioneTesto::tryFrom($data["dimensione_testo"]);
        }
        if (isset($data["modifica_font"])) {
            $this->modificaFont = ModificaFont::tryFrom($data["modifica_font"]);
        }
        if (isset($data["modifica_tema"])) {
            $this->modificaTema = ModificaTema::tryFrom($data["modifica_tema"]);
        }
        if (isset($data["mensa_preferita"])) {
            $this->mensaPreferita = $data["mensa_preferita"];
        }
    }

    //-------------Getters and Setters----------------

    public function getIdUtente(): string
    {
        return $this->idUtente;
    }

    public function setIdUtente(int $idUtente): void
    {
        $this->idUtente = $idUtente;
    }

    public function getDimensioneTesto(): ?DimensioneTesto
    {
        return $this->dimensioneTesto;
    }

    public function setDimensioneTesto(?DimensioneTesto $dimensioneTesto): void
    {
        $this->dimensioneTesto = $dimensioneTesto;
    }

    public function getModificaFont(): ?ModificaFont
    {
        return $this->modificaFont;
    }

    public function setModificaFont(?ModificaFont $modificaFont): void
    {
        $this->modificaFont = $modificaFont;
    }

    public function getTema(): ?ModificaTema
    {
        return $this->modificaTema;
    }

    public function setTema(?ModificaTema $modificaTema): void
    {
        $this->modificaTema = $modificaTema;
    }

    public function getMensaPreferita(): ?string
    {
        return $this->mensaPreferita;
    }

    public function setMensaPreferita(?string $mensaPreferita): void
    {
        $this->mensaPreferita = $mensaPreferita;
    }

    //-------------Database methods----------------

    public function saveToDB(): bool
    {
        $existing = self::findByIdUtente($this->idUtente);
        if ($existing != null) {
            $stmt = $this->db->prepare(
                "UPDATE preferenze_utente SET
                    dimensione_testo = :dimensione_testo,
                    modifica_font = :modifica_font,
                    modifica_tema = :modifica_tema,
                    mensa_preferita = :mensa_preferita
                WHERE idUtente = :idUtente"
            );

            return $stmt->execute([
                "dimensione_testo" => $this->dimensioneTesto->value,
                "modifica_font" => $this->modificaFont->value,
                "modifica_tema" => $this->modificaTema->value,
                "mensa_preferita" => $this->mensaPreferita,
                "idUtente" => $this->idUtente
            ]);
        } else {
            $stmt = $this->db->prepare(
                "INSERT INTO preferenze_utente (
                    idUtente, dimensione_testo,
                   modifica_font,
                    modifica_tema, mensa_preferita
                ) VALUES (
                    :idUtente, :dimensione_testo,
                     :modifica_font,
                    :modifica_tema, :mensa_preferita
                )"
            );

            return $stmt->execute([
                "idUtente" => $this->idUtente,
                "dimensione_testo" => $this->dimensioneTesto->value,
                "modifica_font" => $this->modificaFont->value,
                "modifica_tema" => $this->modificaTema->value,
                "mensa_preferita" => $this->mensaPreferita
            ]);
        }
    }

    public function deleteFromDB(): bool
    {
        if (empty($this->idUtente)) {
            return false;
        }

        $stmt = $this->db->prepare("DELETE FROM preferenze_utente WHERE idUtente = :idUtente");
        return $stmt->execute([
            "idUtente" => $this->idUtente,
        ]);
    }

    //-----------------Stateless methods----------------

    /**
     * @param int $idUtente
     * @return PreferenzeUtenteModel|null
     */
    public static function findByIdUtente(int $idUtente): ?PreferenzeUtenteModel
    {
        $db = Database::getInstance();
        $stmt = $db->prepare(
            "SELECT idUtente, dimensione_testo,
                    modifica_font, modifica_tema, mensa_preferita
             FROM preferenze_utente
             WHERE idUtente = :idUtente"
        );
        $stmt->execute([
            "idUtente" => $idUtente,
        ]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($data) {
            return new PreferenzeUtenteModel($data);
        }
        return null;
    }

    /**
     * @param string $username
     * @return PreferenzeUtenteModel|null
     */
    public static function findByUsername(string $username): ?PreferenzeUtenteModel
    {
        $db = Database::getInstance();
        $stmt = $db->prepare(
            "SELECT idUtente, dimensione_testo,
                    modifica_font, modifica_tema, mensa_preferita,
                    u.username as username
             FROM preferenze_utente pu
             JOIN utente u ON pu.idUtente = u.id
             WHERE u.username = :username"
        );
        $stmt->execute([
            "username" => $username,
        ]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($data) {
            return new PreferenzeUtenteModel($data);
        }
        return null;
    }

    /**
     * @return PreferenzeUtenteModel[]
     */
    public static function findAll(): array
    {
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT idUtente, dimensione_testo,
                                    modifica_font,
                                    modifica_tema, mensa_preferita,
                                    u.username as username
                            FROM preferenze_utente pu
                            JOIN utente u ON pu.idUtente = u.id");
        $stmt->execute();
        $data = $stmt->fetchAll(PDO::FETCH_CLASS, PreferenzeUtenteModel::class);

        $preferenze = [];
        foreach ($data as $row) {
            $preferenze[] = new PreferenzeUtenteModel($row);
        }

        return $preferenze;
    }

    //-----------------Relationals methods----------------

    /**
     * Get the associated UtenteModel
     *
     * @return UserModel|null
     */
    public function getUtente(): ?UserModel
    {
        if (!$this->idUtente) {
            return null;
        }

        return UserModel::findById($this->idUtente);
    }

    /**
     * Get the associated MensaModel
     *
     * @return MenseModel|null
     */
    public function getMensa(): ?MenseModel
    {
        if (!$this->mensaPreferita) {
            return null;
        }

        return MenseModel::findByName($this->mensaPreferita);
    }

    /**
     * @return string[]
     */
    public function getAllergeni(): array
    {
        if (!$this->idUtente) {
            return [];
        }

        $stmt = $this->db->prepare("SELECT allergene FROM allergeni_utente WHERE idUtente = :idUtente");
        $stmt->execute(["idUtente" => $this->idUtente]);

        return $stmt->fetchAll(\PDO::FETCH_COLUMN);
    }

    /**
     * @param string[] $allergeni
     * @return bool
     */
    public function saveAllergeni(array $allergeni): bool
    {
        if (!$this->idUtente) {
            return false;
        }

        $deleteStmt = $this->db->prepare("DELETE FROM allergeni_utente WHERE idUtente = :idUtente");
        $deleteStmt->execute(["idUtente" => $this->idUtente]);

        if (!empty($allergeni)) {
            $insertStmt = $this->db->prepare("INSERT INTO allergeni_utente (idUtente, allergene) VALUES (:idUtente, :allergene)");
            foreach ($allergeni as $allergene) {
                $insertStmt->execute([
                    "idUtente" => $this->idUtente,
                    "allergene" => $allergene
                ]);
            }
        }
        return true;
    }

    public function syncToSession(): void
    {
        if ($this->mensaPreferita) {
            $_SESSION["mensa_preferita"] = $this->mensaPreferita;
        }
        if ($this->modificaTema) {
            $_SESSION["tema"] = $this->modificaTema->value;
        }
        if ($this->dimensioneTesto) {
            $_SESSION["dimensione_testo"] = $this->dimensioneTesto->value;
        }
        if ($this->modificaFont) {
            $_SESSION["modifica_font"] = $this->modificaFont->value;
        }

        $allergeni = $this->getAllergeni();
        if (!empty($allergeni)) {
            $_SESSION["allergeni"] = $allergeni;
        }
    }
}
